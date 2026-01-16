<?php

namespace App\Services\V5;

use App\Models\V5\Tournament;
use App\Models\V5\TournamentConfig;
use App\Models\V5\Game;
use App\Models\V5\Round;
use App\Models\V5\TeamTournament;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class PlayoffStructureService implements TournamentStructureGeneratorInterface
{
    /**
     * Generate playoff bracket structure
     * Teams are randomly paired, winners advance, losers are eliminated
     */
    public function generateStructure(Tournament $tournament, array $settings): void
    {
        DB::transaction(function () use ($tournament, $settings) {
            // Validate settings
            $validation = $this->validateSettings($settings);
            if (!$validation['valid']) {
                throw new \InvalidArgumentException('Invalid settings: ' . implode(', ', $validation['errors']));
            }

            $teamsCount = $settings['teams_count'];
            $gamesBetween = $settings['games_between'] ?? 1;
            $finalGamesBetween = $settings['final_games_between'] ?? 1;

            $tournament->tournamentMatches()->delete();
            // Save tournament config
            $config = TournamentConfig::updateOrCreate(
                ['tournament_id' => $tournament->id],
                ['settings' => $settings]
            );

            // Get teams and shuffle for random pairing
            $teams = $tournament->teamTournaments()->where('is_deleted', false)->get();

            if ($teams->count() !== $teamsCount) {
                throw new \InvalidArgumentException("Tournament must have exactly {$teamsCount} teams. Current: {$teams->count()}");
            }

            // Shuffle teams for random seeding
            $teams = $teams->shuffle();

            // Calculate number of rounds needed
            $roundsCount = (int) log($teamsCount, 2);
            $roundNames = $this->getRoundNames($teamsCount);

            // Calculate date range for each round
            $startDate = $tournament->start_date;
            $endDate = $tournament->end_date;
            $totalDays = $startDate->diffInDays($endDate);
            $daysPerRound = max(1, floor($totalDays / $roundsCount));

            // Create rounds with calculated dates
            $rounds = [];
            for ($i = 1; $i <= $roundsCount; $i++) {
                $roundStartDate = $startDate->copy()->addDays(($i - 1) * $daysPerRound);
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

            // Generate matches for each round
            $matchesByRound = [];
            $currentRoundTeams = $teams->pluck('team_id')->toArray();
            $matchPosition = 1;

            for ($roundNum = 1; $roundNum <= $roundsCount; $roundNum++) {
                $roundMatches = [];
                $isFinal = ($roundNum === $roundsCount);
                $gamesBetweenForRound = $isFinal ? $finalGamesBetween : $gamesBetween;
                $matchesInRound = count($currentRoundTeams) / 2;

                for ($matchNum = 1; $matchNum <= $matchesInRound; $matchNum++) {
                    $team1Id = array_shift($currentRoundTeams);
                    $team2Id = array_shift($currentRoundTeams);
                    $match = Game::create([
                        'tournament_id' => $tournament->id,
                        'round_id' => $rounds[$roundNum]->id,
                        'round_number' => $roundNum,
                        'round_name' => $roundNames[$roundNum - 1] ?? "Round {$roundNum}",
                        'match_number' => $matchNum,
                        'position' => $matchPosition++,
                        'team_id_home' => $team1Id,
                        'team_id_away' => $team2Id,
                        'date' => $rounds[$roundNum]->from_date,
                        'status_id' => null, // Will be set when match starts
                        'games_between' => $gamesBetweenForRound,
                        'home_wins' => 0,
                        'away_wins' => 0,
                        'is_final' => $isFinal,
                        'is_deleted' => false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $roundMatches[] = $match;
                }

                $matchesByRound[$roundNum] = $roundMatches;

                if ($roundNum < $roundsCount) {
                    $nextRoundMatchesCount = $matchesInRound / 2;
                    $currentRoundTeams = [];
                    for ($i = 0; $i < $nextRoundMatchesCount; $i++) {
                        $currentRoundTeams[] = null;
                    }
                }
            }
            $this->linkParentMatches($matchesByRound, $roundsCount);
        });
    }

    /**
     * Link parent matches to create bracket structure
     */
    private function linkParentMatches(array $matchesByRound, int $roundsCount): void
    {
        for ($roundNum = 2; $roundNum <= $roundsCount; $roundNum++) {
            $currentRoundMatches = $matchesByRound[$roundNum];
            $previousRoundMatches = $matchesByRound[$roundNum - 1];

            $matchIndex = 0;
            for ($i = 0; $i < count($previousRoundMatches); $i += 2) {
                if ($matchIndex < count($currentRoundMatches)) {
                    $currentMatch = $currentRoundMatches[$matchIndex];
                    $currentMatch->parent_match_1_id = $previousRoundMatches[$i]->id;
                    if (isset($previousRoundMatches[$i + 1])) {
                        $currentMatch->parent_match_2_id = $previousRoundMatches[$i + 1]->id;
                    }
                    $currentMatch->save();
                    $matchIndex++;
                }
            }
        }

    }

    /**
     * Get round names based on team count
     */
    private function getRoundNames(int $teamsCount): array
    {
        $roundNames = [];
        $roundsCount = (int) log($teamsCount, 2);

        $names = [
            1 => 'Final',
            2 => ['Semi-Final', 'Final'],
            3 => ['Quarter-Final', 'Semi-Final', 'Final'],
            4 => ['Round of 16', 'Quarter-Final', 'Semi-Final', 'Final'],
            5 => ['Round of 32', 'Round of 16', 'Quarter-Final', 'Semi-Final', 'Final'],
        ];

        if (isset($names[$roundsCount])) {
            return is_array($names[$roundsCount]) ? $names[$roundsCount] : [$names[$roundsCount]];
        }

        // Generate generic names
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

        if (!isset($settings['teams_count']) || !is_numeric($settings['teams_count'])) {
            $errors[] = 'teams_count is required';
        } else {
            $teamsCount = (int) $settings['teams_count'];
            // Check if teams_count is a power of 2
            if ($teamsCount < 2 || ($teamsCount & ($teamsCount - 1)) !== 0) {
                $errors[] = 'teams_count must be a power of 2 (2, 4, 8, 16, 32, etc.)';
            }
        }

        if (!isset($settings['games_between']) || !is_numeric($settings['games_between']) || $settings['games_between'] < 1) {
            $errors[] = 'games_between must be at least 1';
        }

        if (isset($settings['final_games_between']) && (!is_numeric($settings['final_games_between']) || $settings['final_games_between'] < 1)) {
            $errors[] = 'final_games_between must be at least 1';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }
}

