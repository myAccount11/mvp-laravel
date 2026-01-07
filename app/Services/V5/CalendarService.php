<?php

namespace App\Services\V5;

use App\Models\V5\Game;
use App\Models\V5\Tournament;
use App\Models\V5\TournamentGroup;
use App\Models\V5\TournamentConfig;
use App\Models\V5\Team;
use App\Models\V5\Club;
use App\Models\V5\Court;
use App\Models\V5\ReservationType;
use App\Models\V5\TimeSlot;
use App\Models\V5\Suggestion;
use App\Services\V5\CourtService;
use App\Services\V5\GameService;
use App\Services\V5\TimeSlotService;
use App\Services\V5\ReservationService;
use App\Services\V5\SuggestionService;
use App\Services\V5\TeamService;
use App\Services\V5\UserService;
use Carbon\Carbon;

class CalendarService
{
    protected CourtService $courtService;
    protected GameService $gameService;
    protected TimeSlotService $timeSlotService;
    protected ReservationService $reservationService;
    protected SuggestionService $suggestionService;
    protected TeamService $teamService;
    protected UserService $userService;

    public function __construct(
        CourtService $courtService,
        GameService $gameService,
        TimeSlotService $timeSlotService,
        ReservationService $reservationService,
        SuggestionService $suggestionService,
        TeamService $teamService,
        UserService $userService
    ) {
        $this->courtService = $courtService;
        $this->gameService = $gameService;
        $this->timeSlotService = $timeSlotService;
        $this->reservationService = $reservationService;
        $this->suggestionService = $suggestionService;
        $this->teamService = $teamService;
        $this->userService = $userService;
    }

    public function getData(array $query, $user): array
    {
        $courtId = $query['courtId'] ?? null;
        $seasonSportId = $query['seasonSportId'] ?? null;
        $type = $query['type'] ?? null;
        $coachId = $query['coachId'] ?? null;

        $eventData = [];
        $searchConditions = [];
        $courtIds = [];

        if ($type === 'mine' || $type === 'coach') {
            $teams = [];

            if ($type === 'coach' && $coachId) {
                $user = $this->userService->findOne(['where' => ['id' => $coachId]]);
            }

            $userTeams = $user->userRoles()
                ->whereIn('role_id', [5, 6, 7, 8, 9])
                ->where('season_sport_id', $seasonSportId)
                ->where('user_role_approved_by_user_id', '>', 0)
                ->whereNotNull('team_id')
                ->pluck('team_id')
                ->toArray();

            $userTeamIds = [];

            if (!empty($userTeams)) {
                $teams = $this->teamService->findAll(['where' => ['id' => ['in' => $userTeams]]]);
                $userTeamIds = $teams->pluck('id')->toArray();
            }

            if (empty($userTeams)) {
                $clubIds = $user->userRoles()
                    ->where('role_id', 1)
                    ->where('season_sport_id', $seasonSportId)
                    ->where('user_role_approved_by_user_id', '>', 0)
                    ->whereNotNull('club_id')
                    ->pluck('club_id')
                    ->toArray();

                if (!empty($clubIds)) {
                    $teams = $this->teamService->findAll(['where' => ['club_id' => ['in' => $clubIds]]]);
                    $userTeamIds = $teams->pluck('id')->toArray();
                }
            }

            if (!empty($userTeamIds)) {
                $searchConditions[] = function ($query) use ($userTeamIds) {
                    $query->whereIn('team_id_home', $userTeamIds)
                        ->orWhereIn('team_id_away', $userTeamIds);
                };
            }
        }

        if ($type === 'court' && $courtId) {
            $courts = $this->courtService->findAll([
                'where' => [
                    ['id', '=', $courtId],
                    ['parent_id', '=', $courtId],
                ],
            ]);

            $courtIds = $courts->pluck('id')->toArray();

            if (!empty($courtIds)) {
                $searchConditions[] = ['court_id', 'in', $courtIds];
            }
        }

        $games = Game::where('season_sport_id', $seasonSportId)
            ->where('status_id', '>=', 3)
            ->whereNotNull('time')
            ->where('is_deleted', false)
            ->where(function ($query) use ($searchConditions) {
                foreach ($searchConditions as $condition) {
                    if (is_callable($condition)) {
                        $condition($query);
                    } else {
                        $query->where($condition[0], $condition[1], $condition[2]);
                    }
                }
            })
            ->with([
                'tournament.tournamentGroup.tournamentConfig',
                'homeTeam',
                'guestTeam',
            ])
            ->orderBy('date', 'ASC')
            ->orderBy('time', 'asc')
            ->get();

        foreach ($games as $game) {
            $warmupMinutes = $game->tournament->tournamentGroup->tournamentConfig->minimum_warmup_minutes ?? 0;
            $expectedDuration = $game->tournament->tournamentGroup->tournamentConfig->expected_duration_minutes ?? 90;

            // Game start is time minus warmup (subtract negative = add)
            $gameStart = Carbon::parse($game->time)->subMinutes(-$warmupMinutes)->format('H:i');
            // Game end is time plus expected duration
            $gameEnd = Carbon::parse($game->time)->addMinutes($expectedDuration)->format('H:i');

            $eventData[] = [
                'gameId' => $game->id,
                'title' => "#{$game->number} {$game->tournament->short_name}: {$game->homeTeam->tournament_name} - {$game->guestTeam->tournament_name}",
                'start' => "{$game->date}T{$gameStart}",
                'end' => "{$game->date}T{$gameEnd}",
                'backgroundColor' => $this->getColorFromStatus($game->status_id),
            ];
        }

        if (!empty($courtIds)) {
            $times = TimeSlot::where('season_sport_id', $seasonSportId)
                ->where('is_deleted', false)
                ->whereIn('court_id', $courtIds)
                ->with(['club', 'court'])
                ->orderBy('date', 'ASC')
                ->get();

            foreach ($times as $time) {
                $eventData[] = [
                    'title' => "{$time->court->name} halt time " . ($time->club ? $time->club->name : 'federation'),
                    'start' => "{$time->date}T{$time->start_time}",
                    'end' => "{$time->date}T{$time->end_time}",
                    'backgroundColor' => 'green',
                ];
            }

            $reservations = $this->reservationService->findAll([
                'where' => [
                    'is_deleted' => false,
                    ['game_id', 'is', null],
                ],
                'include' => [
                    'reservationType',
                    'timeSlot' => function ($query) use ($seasonSportId, $courtIds) {
                        $query->where('season_sport_id', $seasonSportId)
                            ->whereIn('court_id', $courtIds);
                    },
                ],
            ]);

            foreach ($reservations as $reservation) {
                if ($reservation->timeSlot) {
                    $eventData[] = [
                        'title' => "Reservation {$reservation->reservationType->text}",
                        'start' => "{$reservation->timeSlot->date}T{$reservation->timeSlot->start_time}",
                        'end' => "{$reservation->timeSlot->date}T{$reservation->timeSlot->end_time}",
                        'backgroundColor' => 'orange',
                    ];
                }
            }

            $suggestions = Suggestion::whereNull('accepted_by')
                ->whereNull('rejected_by')
                ->whereIn('court_id', $courtIds)
                ->with([
                    'game' => function ($query) use ($seasonSportId) {
                        $query->where('season_sport_id', $seasonSportId)
                            ->where('status_id', '>', 0)
                            ->where('is_deleted', false)
                            ->with([
                                'tournament.tournamentGroup.tournamentConfig',
                                'homeTeam',
                                'guestTeam',
                            ]);
                    },
                ])
                ->orderBy('date', 'ASC')
                ->orderBy('time', 'asc')
                ->get();

            foreach ($suggestions as $suggestion) {
                if ($suggestion->game) {
                    $warmupMinutes = $suggestion->game->tournament->tournamentGroup->tournamentConfig->minimum_warmup_minutes ?? 0;
                    $expectedDuration = $suggestion->game->tournament->tournamentGroup->tournamentConfig->expected_duration_minutes ?? 90;

                    // Game start is time minus warmup (subtract negative = add)
                    $gameStart = Carbon::parse($suggestion->time)->subMinutes(-$warmupMinutes)->format('H:i');
                    // Game end is time plus expected duration
                    $gameEnd = Carbon::parse($suggestion->time)->addMinutes($expectedDuration)->format('H:i');

                    $eventData[] = [
                        'gameId' => $suggestion->game->id,
                        'title' => "Moving in progress: Fight #{$suggestion->game->number} {$suggestion->game->tournament->short_name}: {$suggestion->game->homeTeam->tournament_name} - {$suggestion->game->guestTeam->tournament_name}",
                        'start' => "{$suggestion->date}T{$gameStart}",
                        'end' => "{$suggestion->date}T{$gameEnd}",
                        'backgroundColor' => 'orange',
                    ];
                }
            }
        }

        return $eventData;
    }

    protected function getColorFromStatus(int $statusId): string
    {
        return match ($statusId) {
            0, 1, 2 => 'FF0000',
            3, 4 => 'orange',
            default => 'green',
        };
    }
}

