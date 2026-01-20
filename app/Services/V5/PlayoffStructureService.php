<?php

namespace App\Services\V5;

use App\Models\V5\Tournament;
use App\Models\V5\TournamentConfig;
use App\Models\V5\Game;
use Illuminate\Database\Eloquent\Collection as DatabaseCollection;
use Illuminate\Support\Facades\DB;

class PlayoffStructureService implements TournamentStructureGeneratorInterface
{
    /**
     * Generate playoff bracket structure
     * Teams are randomly paired, winners advance, losers are eliminated
     *
     * Each matchup can have multiple games (games_between setting).
     * For example, with games_between=2:
     *   - Game 1: Team A (home) vs Team B (away)
     *   - Game 2: Team B (home) vs Team A (away)
     *
     * Only first round has teams assigned. Other rounds have null teams
     * until previous round winners are determined.
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

            // Delete existing tournament matches
            $tournament->tournamentMatches()->delete();

            // Save tournament config
            TournamentConfig::updateOrCreate(
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
            $roundsCount = (int)log($teamsCount, 2) * $gamesBetween - ($gamesBetween - $finalGamesBetween);

            $roundNames = $this->getRoundNames($teamsCount);

            $rounds = $tournament->rounds;

            // First round: pair up the shuffled teams
            $teamIds = $teams->pluck('team_id')->toArray();
            $gamePosition = 1;
            $games = collect();

            for ($roundNum = 1; $roundNum <= $roundsCount; $roundNum++) {
                $isFinal = ($roundNum > ((int)log($teamsCount, 2) - 1) * $gamesBetween);
                $gamesForRound = $isFinal ? $finalGamesBetween : $gamesBetween;
                $round = $rounds[$roundNum - 1];

                $previousGames = $games->where('round_number', $roundNum - 1)
                    ->where('is_final', $isFinal)->values();

                // check should create from previous round or not
                $shouldCreateFromPrevious = !$isFinal ? ($roundNum - 1) % ($gamesForRound) !== 0 : ((($roundNum - ((int)log($teamsCount, 2) - 1)) * $gamesBetween) > 1);

                if ($shouldCreateFromPrevious && $previousGames->count()) {
                    $previousGames->each(function ($game) use (&$games, $round, $roundNum) {
                        $games->push([
                            ...$game,
                            'team_id_home' => $game['team_id_away'],
                            'team_id_away' => $game['team_id_home'],
                            'round_number' => $roundNum,
                            'round_id'     => $round->id,
                            'date'         => $round->from_date,
                        ]);
                    });
                } else {
                    $currentRoundTeamsCount = $teamsCount;
                    $actualRoundNumber = (int)(($roundNum - 1) / $gamesBetween);
                    if ($actualRoundNumber) {
                        $currentRoundTeamsCount = $teamsCount / (($isFinal ? ($actualRoundNumber + 1) : $actualRoundNumber) * 2);
                    }

                    for ($gameNumber = 1; $gameNumber <= $currentRoundTeamsCount / 2; $gameNumber++) {

                        $homeTeam = $roundNum === 1 ? $teamIds[$gameNumber - 1] : null;
                        $awayTeam = $roundNum === 1 ? $teamIds[$currentRoundTeamsCount - $gameNumber] : null;

                        $games->push([
                            'tournament_id' => $tournament->id,
                            'round_id'      => $round->id,
                            'round_number'  => $roundNum,
                            'round_name'    => $roundNames[$actualRoundNumber] ?? "Round {$roundNum}",
                            'match_number'  => $games->count() + 1,
                            'position'      => $gamePosition++,
                            'side'          => $isFinal ? 'center' : ($gameNumber > $currentRoundTeamsCount / 4 ? 'right' : 'left'),
                            'team_id_home'  => $homeTeam,
                            'team_id_away'  => $awayTeam,
                            'date'          => $round->from_date,
                            'status_id'     => null,
                            'games_between' => $gamesForRound,
                            'home_wins'     => 0,
                            'away_wins'     => 0,
                            'is_final'      => $isFinal,
                            'is_deleted'    => false,
                            'created_at'    => now(),
                            'updated_at'    => now(),
                        ]);
                    }
                }
            }

            Game::query()->insert($games->toArray());
            $games = $tournament->games()->get()->groupBy('round_name');

            // Link parent matches (first game of each matchup links to first game of parent matchups)
            $this->linkParentMatches($games, $this->getRoundNames($teamsCount));
        });
    }

    /**
     * Link parent matches to create bracket structure
     * Links first game of each matchup to first games of the two parent matchups
     */
    private function linkParentMatches(DatabaseCollection $matchesByRound, array $roundNames): void
    {
        $roundNames = array_reverse($roundNames);
        foreach ($roundNames as $roundIndex => $roundName) {

            if ($roundIndex === count($roundNames) - 1) {
                // Last round has no parents
                continue;
            }

            $parentRoundGames = $matchesByRound[$roundNames[$roundIndex + 1]];
            $currentRoundGames = $matchesByRound[$roundName];

            $parentRoundGamesBetween = $parentRoundGames->first()->games_between;
            $parentGames = $parentRoundGames->sortByDesc('id')->take($parentRoundGames->count() / $parentRoundGamesBetween);
            if ($currentRoundGames->first()->is_final) {
                $finalGame = $currentRoundGames->first();
                $parent1 = $parentGames->pop();
                $parent2 = $parentGames->pop();
                $finalGame->parent_match_2_id = $parent1->id;
                $finalGame->parent_match_1_id = $parent2->id;
                $finalGame->save();
                continue;
            }

            $currentGames = $currentRoundGames->sortBy('id')->take($currentRoundGames->count() / $currentRoundGames->first()->games_between);

            foreach ($currentGames as $currentGame) {
                $parent1 = $parentGames->pop();
                $parent2 = $parentGames->pop();
                $currentGame->parent_match_1_id = $parent1->id;
                $currentGame->parent_match_2_id = $parent2->id;
                $currentGame->save();
            }
        }
    }

    /**
     * Get round names based on team count
     */
    private function getRoundNames(int $teamsCount): array
    {
        $roundNames = [];
        $roundsCount = (int)log($teamsCount, 2);

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
            $teamsCount = (int)$settings['teams_count'];
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
            'valid'  => empty($errors),
            'errors' => $errors,
        ];
    }
}

