<?php

namespace App\Services\V5;

use App\Models\V5\Tournament;
use App\Models\V5\TournamentConfig;
use App\Models\V5\Game;
use App\Models\V5\Round;
use Illuminate\Support\Facades\DB;

class RegularLeagueStructureService implements TournamentStructureGeneratorInterface
{
    /**
     * Generate regular league structure
     * All teams play with each other a specified number of times
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
            $teamsCount = $settings['teams_count'];
            $gamesBetween = $settings['games_between'] ?? 1;

            // Save tournament config
            TournamentConfig::updateOrCreate(
                ['tournament_id' => $tournament->id],
                ['settings' => $settings]
            );

            $teams = $tournament->teamTournaments()->where('is_deleted', false)->get();

            if ($teams->count() !== $teamsCount) {
                throw new \InvalidArgumentException("Tournament must have exactly {$teamsCount} teams. Current: {$teams->count()}");
            }

            // Generate round-robin matches
            $this->generateRoundRobinMatches($tournament, $teams, $gamesBetween);
        });
    }

    /**
     * Generate round-robin matches for all teams
     */
    private function generateRoundRobinMatches(Tournament $tournament, $teams, int $gamesBetween): void
    {
        $teamIds = $teams->pluck('team_id')->toArray();
        $teamsCount = count($teamIds);

        // Calculate date range
        $startDate = $tournament->start_date;
        $endDate = $tournament->end_date;
        $totalDays = $startDate->diffInDays($endDate);

        // Calculate total number of rounds in a round-robin
        // Each team plays every other team once per "games_between" iteration
        $matchupsPerIteration = ($teamsCount * ($teamsCount - 1)) / 2;
        $totalMatchups = $matchupsPerIteration * $gamesBetween;

        // Calculate rounds needed (simplified: one round per match day)
        $roundsCount = $teamsCount - 1; // Standard round-robin rounds
        if ($teamsCount % 2 !== 0) {
            $roundsCount = $teamsCount;
        }
        $totalRounds = $roundsCount * $gamesBetween;

        $daysPerRound = max(1, floor($totalDays / $totalRounds));

        // Create rounds
        $rounds = [];
        for ($i = 1; $i <= $totalRounds; $i++) {
            $roundStartDate = $startDate->copy()->addDays(($i - 1) * $daysPerRound);
            $roundEndDate = ($i === $totalRounds) ? $endDate : $roundStartDate->copy()->addDays($daysPerRound - 1);

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

        // Generate matches using round-robin algorithm
        $matchNumber = 1;
        $roundNumber = 1;

        // For each "games_between" cycle
        for ($cycle = 0; $cycle < $gamesBetween; $cycle++) {
            // Generate round-robin schedule
            $schedule = $this->generateRoundRobinSchedule($teamIds);

            foreach ($schedule as $roundMatches) {
                $round = $rounds[$roundNumber] ?? $rounds[count($rounds)];

                foreach ($roundMatches as $match) {
                    // Swap home/away on alternate cycles for fairness
                    $homeId = ($cycle % 2 === 0) ? $match[0] : $match[1];
                    $awayId = ($cycle % 2 === 0) ? $match[1] : $match[0];

                    // Skip byes (null teams)
                    if ($homeId === null || $awayId === null) continue;

                    Game::create([
                        'tournament_id' => $tournament->id,
                        'round_id' => $round->id,
                        'round_number' => $roundNumber,
                        'match_number' => $matchNumber++,
                        'team_id_home' => $homeId,
                        'team_id_away' => $awayId,
                        'date' => $round->from_date,
                        'games_between' => 1,
                        'home_wins' => 0,
                        'away_wins' => 0,
                        'is_final' => false,
                        'is_deleted' => false,
                    ]);
                }

                $roundNumber++;
            }
        }
    }

    /**
     * Generate a round-robin schedule using the circle method
     */
    private function generateRoundRobinSchedule(array $teamIds): array
    {
        $teams = $teamIds;
        $n = count($teams);

        // If odd number of teams, add a "bye"
        if ($n % 2 !== 0) {
            $teams[] = null;
            $n++;
        }

        $schedule = [];
        $rounds = $n - 1;

        // Fix the first team and rotate the rest
        $fixed = array_shift($teams);

        for ($round = 0; $round < $rounds; $round++) {
            $roundMatches = [];

            // First team plays against the team at the top of the rotation
            $roundMatches[] = [$fixed, $teams[0]];

            // Pair up the remaining teams
            for ($i = 1; $i < $n / 2; $i++) {
                $team1 = $teams[$i];
                $team2 = $teams[$n - 1 - $i];
                $roundMatches[] = [$team1, $team2];
            }

            $schedule[] = $roundMatches;

            // Rotate the array (except the first fixed element)
            $last = array_pop($teams);
            array_unshift($teams, $last);
        }

        return $schedule;
    }

    public function validateSettings(array $settings): array
    {
        $errors = [];

        if (!isset($settings['teams_count']) || !is_numeric($settings['teams_count']) || $settings['teams_count'] < 2) {
            $errors[] = 'teams_count must be at least 2';
        }

        if (!isset($settings['games_between']) || !is_numeric($settings['games_between']) || $settings['games_between'] < 1) {
            $errors[] = 'games_between must be at least 1';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }
}

