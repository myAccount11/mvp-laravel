<?php

namespace App\Services\V5;

use App\Models\V5\Game;
use App\Models\V5\TimeSlot;
use App\Models\V5\Suggestion;
use Carbon\Carbon;

class CalendarService
{
    protected ?CourtService $courtService = null;
    protected ?GameService $gameService = null;
    protected ?TimeSlotService $timeSlotService = null;
    protected ?ReservationService $reservationService = null;
    protected ?SuggestionService $suggestionService = null;
    protected ?TeamService $teamService = null;
    protected ?UserService $userService = null;

    public function __construct()
    {
    }

    // Lazy loading methods for services
    protected function getCourtService(): CourtService
    {
        return $this->courtService ??= app(CourtService::class);
    }

    protected function getGameService(): GameService
    {
        return $this->gameService ??= app(GameService::class);
    }

    protected function getTimeSlotService(): TimeSlotService
    {
        return $this->timeSlotService ??= app(TimeSlotService::class);
    }

    protected function getReservationService(): ReservationService
    {
        return $this->reservationService ??= app(ReservationService::class);
    }

    protected function getSuggestionService(): SuggestionService
    {
        return $this->suggestionService ??= app(SuggestionService::class);
    }

    protected function getTeamService(): TeamService
    {
        return $this->teamService ??= app(TeamService::class);
    }

    protected function getUserService(): UserService
    {
        return $this->userService ??= app(UserService::class);
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
                $user = $this->getUserService()->findOne(['where' => ['id' => $coachId]]);
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
                $teams = $this->getTeamService()->findAll(['where' => ['id' => ['in' => $userTeams]]]);
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
                    $teams = $this->getTeamService()->findAll(['where' => ['club_id' => ['in' => $clubIds]]]);
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
            // Find courts where id = courtId OR parent_id = courtId
            $courts = \App\Models\V5\Court::where('id', $courtId)
                ->orWhere('parent_id', $courtId)
                ->get();

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
                'tournament.tournamentGroup',
                'homeTeam',
                'guestTeam',
            ])
            ->orderBy('date', 'ASC')
            ->orderBy('time', 'asc')
            ->get();

        foreach ($games as $game) {
            $warmupMinutes = $game->tournament->tournamentGroup->minimum_warmup_minutes ?? 0;
            $expectedDuration = $game->tournament->tournamentGroup->expected_duration_minutes ?? 90;

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

            $reservations = $this->getReservationService()->findAll([
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
                                'tournament.tournamentGroup',
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
                    $warmupMinutes = $suggestion->game->tournament->tournamentGroup->minimum_warmup_minutes ?? 0;
                    $expectedDuration = $suggestion->game->tournament->tournamentGroup->expected_duration_minutes ?? 90;

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

