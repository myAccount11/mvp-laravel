<?php

namespace App\Services\V5;

use App\Models\V5\Tournament;
use App\Models\V5\TournamentConfig;
use App\Models\V5\TournamentGroup;
use App\Models\V5\Game;
use App\Models\V5\Round;
use App\Models\V5\TeamTournament;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class GroupStageAndPlayoffStructureService implements TournamentStructureGeneratorInterface
{
    /**
     * Generate group stage + playoff structure
     * Teams are divided into groups, play round-robin, top teams advance to playoff
     */
    public function generateStructure(Tournament $tournament, array $settings): void
    {
        DB::transaction(function () use ($tournament, $settings) {
            // Validate settings
            $validation = $this->validateSettings($settings);
            if (!$validation['valid']) {
                throw new \InvalidArgumentException('Invalid settings: ' . implode(', ', $validation['errors']));
            }
            $tournament->tournamentMatches()->delete();

            $groupsCount = $settings['groups_count'];
            $teamsPerGroup = $settings['teams_per_group'];
            $playoffTeamsCount = $settings['playoff_teams_count'];
            $gamesBetweenInGroup = $settings['games_between_in_group_stage'] ?? 1;
            $gamesBetweenInPlayoff = $settings['games_between_in_playoff_stage'] ?? 1;
            $gamesBetweenInFinal = $settings['games_between_in_final'] ?? 1;

            // Save tournament config
            $config = TournamentConfig::updateOrCreate(
                ['tournament_id' => $tournament->id],
                ['settings' => $settings]
            );

            // Get teams and shuffle for random distribution
            $teams = $tournament->teamTournaments()->where('is_deleted', false)->get();
            $totalTeamsNeeded = $groupsCount * $teamsPerGroup;

            if ($teams->count() !== $totalTeamsNeeded) {
                throw new \InvalidArgumentException("Tournament must have exactly {$totalTeamsNeeded} teams. Current: {$teams->count()}");
            }

            // Shuffle teams for random distribution
            $teams = $teams->shuffle();

            // Create groups and assign teams
            $groups = [];
            $teamIndex = 0;

            for ($groupNum = 1; $groupNum <= $groupsCount; $groupNum++) {
                $group = TournamentGroup::create([
                    'tournament_id' => $tournament->id,
                    'name' => 'Group ' . chr(64 + $groupNum), // A, B, C, etc.
                    'group_number' => $groupNum,
                    'teams_count' => $teamsPerGroup,
                    'games_between' => $gamesBetweenInGroup,
                    'advancing_teams_count' => 2, // Top 2 advance
                    'is_deleted' => false,
                ]);

                // Assign teams to group
                for ($i = 0; $i < $teamsPerGroup; $i++) {
                    if ($teamIndex < $teams->count()) {
                        $team = $teams[$teamIndex++];

                        // Create pivot record for team in group
                        DB::table('team_tournament_groups')->insert([
                            'group_id' => $group->id,
                            'team_id' => $team->team_id,
                            'position' => 0,
                            'points' => 0,
                            'wins' => 0,
                            'losses' => 0,
                            'draws' => 0,
                            'goals_for' => 0,
                            'goals_against' => 0,
                            'goal_difference' => 0,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }

                $groups[] = $group;
            }

            // Generate group stage matches
            $this->generateGroupStageMatches($tournament, $groups, $gamesBetweenInGroup);

            // Generate playoff bracket
            $this->generatePlayoffBracket($tournament, $playoffTeamsCount, $groups, $gamesBetweenInPlayoff, $gamesBetweenInFinal);
        });
    }

    /**
     * Generate group stage matches (round-robin within each group)
     */
    private function generateGroupStageMatches(
        Tournament $tournament,
        array $groups,
        int $gamesBetween
    ): void {
        // Calculate date range for group stage (first half of tournament)
        $startDate = $tournament->start_date;
        $endDate = $tournament->end_date;
        $totalDays = $startDate->diffInDays($endDate);
        $groupStageDays = floor($totalDays / 2);

        $matchNumber = 1;

        foreach ($groups as $group) {
            // Get team IDs from the group
            $teamIds = DB::table('team_tournament_groups')
                ->where('group_id', $group->id)
                ->pluck('team_id')
                ->toArray();

            if (count($teamIds) < 2) continue;

            // Generate round-robin matches within the group
            $matches = [];
            $teamsCount = count($teamIds);

            // For each pair of teams
            for ($i = 0; $i < $teamsCount; $i++) {
                for ($j = $i + 1; $j < $teamsCount; $j++) {
                    // Create matches based on games_between setting
                    for ($game = 1; $game <= $gamesBetween; $game++) {
                        // Alternate home/away for multiple games
                        $homeTeam = ($game % 2 === 1) ? $teamIds[$i] : $teamIds[$j];
                        $awayTeam = ($game % 2 === 1) ? $teamIds[$j] : $teamIds[$i];

                        $matches[] = [
                            'home_id' => $homeTeam,
                            'away_id' => $awayTeam,
                        ];
                    }
                }
            }

            // Calculate days per match day in group stage
            $totalGroupMatches = count($matches);
            $daysPerMatch = max(1, floor($groupStageDays / max(1, ceil($totalGroupMatches / count($groups)))));

            // Create game records for group stage matches
            foreach ($matches as $idx => $match) {
                $matchDate = $startDate->copy()->addDays($idx * $daysPerMatch);
                if ($matchDate->gt($startDate->copy()->addDays($groupStageDays))) {
                    $matchDate = $startDate->copy()->addDays($groupStageDays);
                }

                Game::create([
                    'tournament_id' => $tournament->id,
                    'group_id' => $group->id,
                    'match_number' => $matchNumber++,
                    'team_id_home' => $match['home_id'],
                    'team_id_away' => $match['away_id'],
                    'date' => $matchDate,
                    'games_between' => 1, // Each record is one game
                    'home_wins' => 0,
                    'away_wins' => 0,
                    'is_final' => false,
                    'is_deleted' => false,
                ]);
            }
        }
    }

    /**
     * Generate playoff bracket for advancing teams
     */
    private function generatePlayoffBracket(
        Tournament $tournament,
        int $playoffTeamsCount,
        array $groups,
        int $gamesBetweenInPlayoff,
        int $gamesBetweenInFinal
    ): void {
        // Validate playoff teams count is power of 2
        if (($playoffTeamsCount & ($playoffTeamsCount - 1)) !== 0) {
            throw new \InvalidArgumentException("playoff_teams_count must be a power of 2");
        }

        // Calculate rounds needed
        $roundsCount = (int) log($playoffTeamsCount, 2);
        $roundNames = $this->getRoundNames($playoffTeamsCount);

        // Calculate date range for playoff rounds (use second half of tournament duration)
        $startDate = $tournament->start_date;
        $endDate = $tournament->end_date;
        $totalDays = $startDate->diffInDays($endDate);

        // Group stage takes first half, playoffs take second half
        $playoffStartDate = $startDate->copy()->addDays(floor($totalDays / 2));
        $playoffDays = ceil($totalDays / 2);
        $daysPerRound = max(1, floor($playoffDays / $roundsCount));

        // Create rounds for playoff
        $rounds = [];
        for ($i = 1; $i <= $roundsCount; $i++) {
            $roundStartDate = $playoffStartDate->copy()->addDays(($i - 1) * $daysPerRound);
            $roundEndDate = ($i === $roundsCount) ? $endDate : $roundStartDate->copy()->addDays($daysPerRound - 1);

            $round = Round::create([
                'tournament_id' => $tournament->id,
                'number' => $i,
                'from_date' => $roundStartDate,
                'to_date' => $roundEndDate,
                'type' => 0,
                'force_cross' => false,
                'deleted' => false,
            ]);
            $rounds[$i] = $round;
        }

        // Generate matches for first round of playoff
        // Teams will be seeded based on group positions
        // First place of Group A plays Second place of Group B, etc.
        $matchesByRound = [];
        $matchPosition = 1;

        // First round: pair groups
        // Group A 1st vs Group B 2nd, Group B 1st vs Group A 2nd
        // Group C 1st vs Group D 2nd, Group D 1st vs Group C 2nd, etc.
        $firstRoundMatches = [];
        $groupsCount = count($groups);

        for ($i = 0; $i < $groupsCount; $i += 2) {
            $group1 = $groups[$i];
            $group2 = isset($groups[$i + 1]) ? $groups[$i + 1] : null;

            if ($group2) {
                // Match 1: Group1 1st vs Group2 2nd
                $match1 = Game::create([
                    'tournament_id' => $tournament->id,
                    'round_id' => $rounds[1]->id,
                    'round_number' => 1,
                    'round_name' => $roundNames[0] ?? 'Round 1',
                    'match_number' => count($firstRoundMatches) + 1,
                    'position' => $matchPosition++,
                    'team_id_home' => null, // Will be set when group stage completes
                    'team_id_away' => null,
                    'date' => $rounds[1]->from_date,
                    'status_id' => null, // Will be set when match starts
                    'games_between' => $gamesBetweenInPlayoff,
                    'home_wins' => 0,
                    'away_wins' => 0,
                    'group_id' => $group1->id,
                    'group_position' => 1, // 1st place
                    'is_final' => false,
                    'is_deleted' => false,
                ]);
                $match1->parent_match_1_id = null; // Will link to group2 2nd place
                $match1->save();
                $firstRoundMatches[] = $match1;

                // Match 2: Group2 1st vs Group1 2nd
                $match2 = Game::create([
                    'tournament_id' => $tournament->id,
                    'round_id' => $rounds[1]->id,
                    'round_number' => 1,
                    'round_name' => $roundNames[0] ?? 'Round 1',
                    'match_number' => count($firstRoundMatches) + 1,
                    'position' => $matchPosition++,
                    'team_id_home' => null,
                    'team_id_away' => null,
                    'date' => $rounds[1]->from_date,
                    'status_id' => null, // Will be set when match starts
                    'games_between' => $gamesBetweenInPlayoff,
                    'home_wins' => 0,
                    'away_wins' => 0,
                    'group_id' => $group2->id,
                    'group_position' => 1, // 1st place
                    'is_final' => false,
                    'is_deleted' => false,
                ]);
                $match2->save();
                $firstRoundMatches[] = $match2;
            }
        }

        $matchesByRound[1] = $firstRoundMatches;

        // Generate subsequent rounds
        $currentRoundMatches = $firstRoundMatches;
        for ($roundNum = 2; $roundNum <= $roundsCount; $roundNum++) {
            $isFinal = ($roundNum === $roundsCount);
            $gamesBetweenForRound = $isFinal ? $gamesBetweenInFinal : $gamesBetweenInPlayoff;
            $matchesInRound = count($currentRoundMatches) / 2;
            $nextRoundMatches = [];

            for ($matchNum = 1; $matchNum <= $matchesInRound; $matchNum++) {
                $parent1 = $currentRoundMatches[($matchNum - 1) * 2];
                $parent2 = $currentRoundMatches[($matchNum - 1) * 2 + 1];

                $match = Game::create([
                    'tournament_id' => $tournament->id,
                    'round_id' => $rounds[$roundNum]->id,
                    'round_number' => $roundNum,
                    'round_name' => $roundNames[$roundNum - 1] ?? "Round {$roundNum}",
                    'match_number' => $matchNum,
                    'position' => $matchPosition++,
                    'team_id_home' => null, // Will be set when parent matches complete
                    'team_id_away' => null,
                    'date' => $rounds[$roundNum]->from_date,
                    'status_id' => null, // Will be set when match starts
                    'games_between' => $gamesBetweenForRound,
                    'home_wins' => 0,
                    'away_wins' => 0,
                    'parent_match_1_id' => $parent1->id,
                    'parent_match_2_id' => $parent2->id,
                    'is_final' => $isFinal,
                    'is_deleted' => false,
                ]);

                $nextRoundMatches[] = $match;
            }

            $matchesByRound[$roundNum] = $nextRoundMatches;
            $currentRoundMatches = $nextRoundMatches;
        }
    }

    /**
     * Get round names based on team count
     */
    private function getRoundNames(int $teamsCount): array
    {
        $roundsCount = (int) log($teamsCount, 2);

        $names = [
            1 => ['Final'],
            2 => ['Semi-Final', 'Final'],
            3 => ['Quarter-Final', 'Semi-Final', 'Final'],
            4 => ['Round of 16', 'Quarter-Final', 'Semi-Final', 'Final'],
        ];

        if (isset($names[$roundsCount])) {
            return $names[$roundsCount];
        }

        // Generate generic names
        $roundNames = [];
        for ($i = $roundsCount; $i >= 1; $i--) {
            if ($i === 1) {
                $roundNames[] = 'Final';
            } elseif ($i === 2) {
                $roundNames[] = 'Semi-Final';
            } else {
                $roundNames[] = "Round " . (2 ** ($roundsCount - $i + 1));
            }
        }

        return array_reverse($roundNames);
    }

    public function validateSettings(array $settings): array
    {
        $errors = [];

        if (!isset($settings['groups_count']) || !is_numeric($settings['groups_count']) || $settings['groups_count'] < 2) {
            $errors[] = 'groups_count must be at least 2';
        }

        if (!isset($settings['teams_per_group']) || !is_numeric($settings['teams_per_group']) || $settings['teams_per_group'] < 2) {
            $errors[] = 'teams_per_group must be at least 2';
        }

        if (!isset($settings['playoff_teams_count']) || !is_numeric($settings['playoff_teams_count'])) {
            $errors[] = 'playoff_teams_count is required';
        } else {
            $playoffTeamsCount = (int) $settings['playoff_teams_count'];
            // Check if playoff_teams_count is a power of 2
            if ($playoffTeamsCount < 2 || ($playoffTeamsCount & ($playoffTeamsCount - 1)) !== 0) {
                $errors[] = 'playoff_teams_count must be a power of 2 (2, 4, 8, 16, 32, etc.)';
            }
        }

        // Validate total teams
        $totalTeams = $settings['groups_count'] * $settings['teams_per_group'];
        if ($totalTeams < $settings['playoff_teams_count']) {
            $errors[] = 'Total teams in groups must be at least equal to playoff_teams_count';
        }

        if (!isset($settings['games_between_in_group_stage']) || !is_numeric($settings['games_between_in_group_stage']) || $settings['games_between_in_group_stage'] < 1) {
            $errors[] = 'games_between_in_group_stage must be at least 1';
        }

        if (!isset($settings['games_between_in_playoff_stage']) || !is_numeric($settings['games_between_in_playoff_stage']) || $settings['games_between_in_playoff_stage'] < 1) {
            $errors[] = 'games_between_in_playoff_stage must be at least 1';
        }

        if (isset($settings['games_between_in_final']) && (!is_numeric($settings['games_between_in_final']) || $settings['games_between_in_final'] < 1)) {
            $errors[] = 'games_between_in_final must be at least 1';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }
}

