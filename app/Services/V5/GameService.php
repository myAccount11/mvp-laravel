<?php

namespace App\Services\V5;

use App\Models\V5\Game;
use App\Models\V5\User;
use App\Models\V5\TeamTournament;
use App\Models\V5\GameDraft;
use App\Models\V5\ClubVenue;
use App\Models\V5\VenueSeasonSport;
use App\Models\V5\Conflict;
use App\Repositories\V5\GameRepository;
use Carbon\Carbon;

class GameService
{
    protected GameRepository $gameRepository;
    protected ?TournamentService $tournamentService = null;
    protected ?GameDraftService $gameDraftService = null;
    protected ?CourtService $courtService = null;
    protected ?TimeSlotService $timeSlotService = null;
    protected ?TournamentGroupService $tournamentGroupService = null;
    protected ?ClubService $clubService = null;
    protected ?ConflictService $conflictService = null;
    protected ?TournamentConfigsService $tournamentConfigService = null;
    protected ?TeamService $teamsService = null;
    protected ?ReservationService $reservationsService = null;
    protected ?BlockedPeriodsService $blockedPeriodsService = null;
    protected ?SuggestionService $suggestionsService = null;
    protected ?MessageService $messageService = null;
    protected ?TournamentProgramItemsService $tournamentProgramItemsService = null;

    public function __construct(GameRepository $gameRepository)
    {
        $this->gameRepository = $gameRepository;
    }

    // Lazy loading methods for services
    protected function getTournamentService(): TournamentService
    {
        return $this->tournamentService ??= app(TournamentService::class);
    }

    protected function getGameDraftService(): GameDraftService
    {
        return $this->gameDraftService ??= app(GameDraftService::class);
    }

    protected function getCourtService(): CourtService
    {
        return $this->courtService ??= app(CourtService::class);
    }

    protected function getTimeSlotService(): TimeSlotService
    {
        return $this->timeSlotService ??= app(TimeSlotService::class);
    }

    protected function getTournamentGroupService(): TournamentGroupService
    {
        return $this->tournamentGroupService ??= app(TournamentGroupService::class);
    }

    protected function getClubService(): ClubService
    {
        return $this->clubService ??= app(ClubService::class);
    }

    protected function getConflictService(): ConflictService
    {
        return $this->conflictService ??= app(ConflictService::class);
    }

    protected function getTournamentConfigService(): TournamentConfigsService
    {
        return $this->tournamentConfigService ??= app(TournamentConfigsService::class);
    }

    protected function getTeamsService(): TeamService
    {
        return $this->teamsService ??= app(TeamService::class);
    }

    protected function getReservationsService(): ReservationService
    {
        return $this->reservationsService ??= app(ReservationService::class);
    }

    protected function getBlockedPeriodsService(): BlockedPeriodsService
    {
        return $this->blockedPeriodsService ??= app(BlockedPeriodsService::class);
    }

    protected function getSuggestionsService(): SuggestionService
    {
        return $this->suggestionsService ??= app(SuggestionService::class);
    }

    protected function getMessageService(): MessageService
    {
        return $this->messageService ??= app(MessageService::class);
    }

    protected function getTournamentProgramItemsService(): TournamentProgramItemsService
    {
        return $this->tournamentProgramItemsService ??= app(TournamentProgramItemsService::class);
    }

    public function findOne(array $condition): ?Game
    {
        // Extract where conditions and include relations
        $whereConditions = $condition['where'] ?? [];
        $includeRelations = $condition['include'] ?? [];

        // If whereConditions is empty but condition has direct keys (for backward compatibility)
        if (empty($whereConditions) && !isset($condition['where']) && !isset($condition['include'])) {
            $whereConditions = $condition;
        }

        $game = $this->gameRepository->findOneBy($whereConditions, ['*'], $includeRelations);

        // Load relations if game found and relations were requested
        if ($game && !empty($includeRelations)) {
            $game->load($includeRelations);
        }

        return $game;
    }

    public function findAll(array $condition = []): \Illuminate\Database\Eloquent\Collection
    {
        return $this->gameRepository->findBy($condition);
    }

    public function findAndCountAll(array $options): array
    {
        return $this->gameRepository->findAndCountAll($options);
    }

    public function create(array $data): Game
    {
        return $this->gameRepository->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->gameRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->gameRepository->delete($id);
    }

    protected function isAdmin(User $user): bool
    {
        $roles = $user->roles()->get();
        return $roles->contains(function ($role) {
            return in_array($role->description, ['Super Admin', 'Association Admin']);
        });
    }

    public function createAllTournamentGames(int $tournamentId, ?int $seasonSportId): array
    {
        $this->getGameDraftService()->deleteByCondition(['tournament_id' => $tournamentId]);

        $tournament = $this->getTournamentService()->findOne([
            'where'   => [
                'id'      => $tournamentId,
                'deleted' => false,
            ],
            'include' => ['teamTournaments', 'rounds', 'pools'],
        ]);

        if (!$tournament) {
            throw new \Exception('Tournament not found');
        }

        $tournament->load(['pools' => function ($query) {
            $query->orderBy('name', 'asc');
        }, 'rounds'                => function ($query) {
            $query->orderBy('id', 'asc');
        }]);

        foreach ($tournament->pools as $pool) {
            $poolTeamCount = $pool->teams_count;
            $evenTeams = $poolTeamCount % 2 == 0;

            $maxRounds = $pool->games_between * ($evenTeams ? $poolTeamCount - 1 : $poolTeamCount);

            $poolTeams = $tournament->teamTournaments
                ->filter(function ($team) use ($pool) {
                    return $team->pool_id === $pool->id;
                })
                ->shuffle();

            $poolKey = 0;
            foreach ($poolTeams as $team) {
                $poolKey++;
                $team->pool_key = $poolKey;
                $team->save();
            }

            // Match Node.js logic: only proceed if actual team count matches expected count
            if ($poolKey === $poolTeamCount) {
                $roundsArray = [];
                $teamRoundPositions = range(1, $poolTeamCount);

                if ($tournament->rounds->count()) {
                    // Match Node.js loop condition: stop when roundsArray.length >= maxRounds (unless roundType == 1)
                    for ($i = 0; $i < $tournament->rounds->count() && (count($roundsArray) < $maxRounds || $tournament->round_type == 1); $i++) {
                        $round = $tournament->rounds[$i];

                        if (!$round->force_cross && !$round->deleted) {
                            $gamesSameMasterRound = $tournament->round_type === 1 ? $maxRounds : 1;

                            for ($roundSubIndex = 1; $roundSubIndex <= $gamesSameMasterRound; $roundSubIndex++) {
                                // Check if we've exceeded maxRounds (unless roundType == 1)
                                if ($tournament->round_type != 1 && count($roundsArray) >= $maxRounds) {
                                    break 2; // Break out of both inner and outer loop
                                }

                                $termDate = Carbon::parse($round->to_date)->format('Y-m-d');

                                if ($round->type >= 1) {
                                    $termDate = Carbon::parse($round->from_date)->addDays(2)->format('Y-m-d');
                                }

                                $roundsArray[] = [
                                    'id'           => $round->id,
                                    'matrix'       => $teamRoundPositions,
                                    'tournamentId' => $tournamentId,
                                    'poolId'       => $pool->id,
                                    'termDate'     => $termDate,
                                    'roundNumber'  => $round->number,
                                ];

                                $teamRoundPositions = $this->generateNewTeamRoundPositions($teamRoundPositions, $evenTeams);

                                if ($round->type == 2) {
                                    // Check again before adding second entry for type 2 rounds
                                    if ($tournament->round_type != 1 && count($roundsArray) >= $maxRounds) {
                                        break 2; // Break out of both inner and outer loop
                                    }

                                    $roundsArray[] = [
                                        'id'           => $round->id,
                                        'matrix'       => [...$teamRoundPositions],
                                        'tournamentId' => $tournamentId,
                                        'poolId'       => $pool->id,
                                        'termDate'     => $round->to_date,
                                        'roundNumber'  => $round->number + 1,
                                    ];
                                    $teamRoundPositions = $this->generateNewTeamRoundPositions($teamRoundPositions, $evenTeams);
                                }
                            }
                        } elseif (!$round->deleted && $round->force_cross) {
                            // Check if we've exceeded maxRounds (unless roundType == 1)
                            // Note: force_cross rounds don't count towards maxRounds limit in Node.js
                            // But we still check to avoid infinite loops
                            if ($tournament->round_type != 1 && count($roundsArray) >= $maxRounds * 2) {
                                break;
                            }

                            $termDate = Carbon::parse($round->to_date)->format('Y-m-d');

                            if ($round->type >= 1) {
                                $termDate = Carbon::parse($round->from_date)->addDays(2)->format('Y-m-d');
                            }

                            $roundsArray[] = [
                                'id'           => $round->id,
                                'matrix'       => [...$teamRoundPositions],
                                'tournamentId' => $tournamentId,
                                'poolId'       => null,
                                'termDate'     => $termDate,
                                'roundNumber'  => $round->number,
                            ];
                        }
                    }
                    $switchHomeAway = false;
                    $switchHomeAwayCross = false;
                    $switchHomeAwayIndex = 0;
                    $switchFirstKey = false;
                    $otherPoolKeys = [1, 2, 3, 4, 5, 6, 7, 8, 9];

                    $gameDraftData = [];

                    // Get tournament program items if tournament has a program
                    $programItems = [];
                    if ($tournament->tournament_program_id) {
                        $items = $this->getTournamentProgramItemsService()->findAll([
                            'where' => ['tournament_program_id' => $tournament->tournament_program_id],
                        ]);
                        // Convert to array for easier access
                        $programItems = $items->map(function ($item) {
                            return [
                                'round_number'          => $item->round_number,
                                'home_key'              => $item->home_key,
                                'away_key'              => $item->away_key,
                                'tournament_program_id' => $item->tournament_program_id,
                            ];
                        })->toArray();
                    }

                    foreach ($roundsArray as $round) {
                        if (!$round['poolId']) {
                            $gameDraftParameters = [
                                'round_id'             => $round['id'],
                                'pool_id'              => null,
                                'tournament_id'        => $round['tournamentId'],
                                'term_date'            => $round['termDate'],
                                'round_number'         => $round['roundNumber'],
                                'pool_id_cross_master' => $switchHomeAwayCross ? 430 : $pool->id,
                                'pool_id_cross_slave'  => $switchHomeAwayCross ? $pool->id : 430,
                                'home_key'             => 0,
                                'away_key'             => 0,
                            ];

                            for ($masterPoolKey = 1; $masterPoolKey <= 5; $masterPoolKey++) {
                                if ($switchHomeAwayCross) {
                                    $gameDraftParameters['home_key'] = $otherPoolKeys[$masterPoolKey];
                                    $gameDraftParameters['away_key'] = $masterPoolKey;
                                } else {
                                    $gameDraftParameters['home_key'] = $masterPoolKey;
                                    $gameDraftParameters['away_key'] = $otherPoolKeys[$masterPoolKey];
                                }

                                $gameDraftData[] = [...$gameDraftParameters];
                            }

                            $otherPoolKeys = array_merge(
                                [$otherPoolKeys[3]],
                                array_slice($otherPoolKeys, 0, 3),
                                array_slice($otherPoolKeys, 4)
                            );

                            $switchHomeAwayCross = !$switchHomeAwayCross;
                        } else {
                            $gameDraftParameters = [
                                'round_id'      => $round['id'],
                                'pool_id'       => $round['poolId'],
                                'tournament_id' => $round['tournamentId'],
                                'term_date'     => $round['termDate'],
                                'round_number'  => $round['roundNumber'],
                                'home_key'      => 0,
                                'away_key'      => 0,
                            ];

                            // Use tournament program items if available, otherwise use default logic
                            if (!empty($programItems)) {
                                // Filter program items for this round
                                $roundProgramItems = array_filter($programItems, function ($item) use ($round) {
                                    return $item['round_number'] == $round['roundNumber'];
                                });

                                if (!empty($roundProgramItems)) {
                                    // Use program items to set home_key and away_key
                                    foreach ($roundProgramItems as $item) {
                                        $gameDraftParameters['home_key'] = $item['home_key'];
                                        $gameDraftParameters['away_key'] = $item['away_key'];
                                        $gameDraftData[] = [...$gameDraftParameters];
                                    }
                                } else {
                                    // Fallback to default logic if no program items for this round
                                    $switchHomeAwayIndex++;

                                    if (
                                        ($switchHomeAwayIndex > $poolTeamCount - 1 && $evenTeams) ||
                                        ($switchHomeAwayIndex > $poolTeamCount && !$evenTeams)
                                    ) {
                                        $switchHomeAway = !$switchHomeAway;
                                        $switchFirstKey = false;
                                        $switchHomeAwayIndex = 1;
                                    }

                                    $arrayMatrix = $round['matrix'];
                                    $homeIndex = 0;
                                    $awayIndex = count($arrayMatrix) - 1;

                                    while ($homeIndex < $awayIndex) {
                                        $homeKey = $arrayMatrix[$homeIndex];
                                        $awayKey = $arrayMatrix[$awayIndex];

                                        if (($homeKey === 1 || $awayKey === 1) && $evenTeams) {
                                            if ($switchFirstKey) {
                                                $temp = $awayKey;
                                                $awayKey = $homeKey;
                                                $homeKey = $temp;
                                            }
                                            $switchFirstKey = !$switchFirstKey;
                                        }

                                        $gameDraftParameters['home_key'] = $switchHomeAway ? $awayKey : $homeKey;
                                        $gameDraftParameters['away_key'] = $switchHomeAway ? $homeKey : $awayKey;

                                        $gameDraftData[] = [...$gameDraftParameters];
                                        $homeIndex++;
                                        $awayIndex--;
                                    }
                                }
                            } else {
                                // Default logic when no program items
                                $switchHomeAwayIndex++;

                                if (
                                    ($switchHomeAwayIndex > $poolTeamCount - 1 && $evenTeams) ||
                                    ($switchHomeAwayIndex > $poolTeamCount && !$evenTeams)
                                ) {
                                    $switchHomeAway = !$switchHomeAway;
                                    $switchFirstKey = false;
                                    $switchHomeAwayIndex = 1;
                                }

                                $arrayMatrix = $round['matrix'];
                                $homeIndex = 0;
                                $awayIndex = count($arrayMatrix) - 1;

                                while ($homeIndex < $awayIndex) {
                                    $homeKey = $arrayMatrix[$homeIndex];
                                    $awayKey = $arrayMatrix[$awayIndex];

                                    if (($homeKey === 1 || $awayKey === 1) && $evenTeams) {
                                        if ($switchFirstKey) {
                                            $temp = $awayKey;
                                            $awayKey = $homeKey;
                                            $homeKey = $temp;
                                        }
                                        $switchFirstKey = !$switchFirstKey;
                                    }

                                    $gameDraftParameters['home_key'] = $switchHomeAway ? $awayKey : $homeKey;
                                    $gameDraftParameters['away_key'] = $switchHomeAway ? $homeKey : $awayKey;

                                    $gameDraftData[] = [...$gameDraftParameters];
                                    $homeIndex++;
                                    $awayIndex--;
                                }
                            }
                        }
                    }

                    $this->getGameDraftService()->createMany($gameDraftData);
                }
            } else {
                throw new \Exception(
                    "Team Count Error: The pool contains ({$poolKey}) teams, which doesn't match the ({$poolTeamCount}) teams listed for the tournament's pool. Please review the details and try again."
                );
            }
        }

        return $this->createGamesFromDraft($tournamentId, $seasonSportId);
    }

    protected function generateNewTeamRoundPositions(array $teamRoundPositions, bool $evenTeams): array
    {
        if ($evenTeams) {
            return array_merge(
                [$teamRoundPositions[0]],
                [$teamRoundPositions[count($teamRoundPositions) - 1]],
                array_slice($teamRoundPositions, 1, -1)
            );
        } else {
            return array_merge(
                [$teamRoundPositions[count($teamRoundPositions) - 1]],
                array_slice($teamRoundPositions, 0, -1)
            );
        }
    }

    protected function createGamesFromDraft(int $tournamentId, ?int $seasonSportId): array
    {
        $gameDrafts = GameDraft::where('tournament_id', $tournamentId)
            ->orderBy('id', 'asc')
            ->with([
                'homeTeamTournament'  => function ($query) use ($tournamentId) {
                    $query->where('tournament_id', $tournamentId);
                },
                'guestTeamTournament' => function ($query) use ($tournamentId) {
                    $query->where('tournament_id', $tournamentId);
                },
            ])
            ->get();

        $gameData = [];
        $lastGame = Game::orderBy('id', 'desc')->first();
        $gameNumber = $lastGame ? $lastGame->number : 0;
        $lastGameId = $lastGame ? $lastGame->id : 0;

        Game::where('tournament_id', $tournamentId)->update(['is_deleted' => true]);

        foreach ($gameDrafts as $gameDraft) {
            $homeTeamTournament = TeamTournament::where('tournament_id', $tournamentId)
                ->where('pool_id', $gameDraft->pool_id)
                ->where('pool_key', $gameDraft->home_key)
                ->first();

            $guestTeamTournament = TeamTournament::where('tournament_id', $tournamentId)
                ->where('pool_id', $gameDraft->pool_id)
                ->where('pool_key', $gameDraft->away_key)
                ->first();

            if ($homeTeamTournament && $guestTeamTournament) {
                $gameData[] = [
                    'round_id'           => $gameDraft->round_id,
                    'pool_id'            => $gameDraft->pool_id,
                    'tournament_id'      => $gameDraft->tournament_id,
                    'status_id'          => 1,
                    'number'             => ++$gameNumber,
                    'draft_id'           => $gameDraft->id,
                    'date'               => $gameDraft->term_date,
                    'original_term_date' => $gameDraft->term_date,
                    'home_key'           => $gameDraft->home_key,
                    'away_key'           => $gameDraft->away_key,
                    'season_sport_id'    => $seasonSportId,
                    'team_id_home'       => $homeTeamTournament->team_id,
                    'team_id_away'       => $guestTeamTournament->team_id,
                ];
            }
        }

        if (empty($gameData)) {
            return [];
        }

        // Insert games
        Game::insert($gameData);

        // Query and return the newly created games
        // We identify them by tournament_id and IDs greater than the last game ID before insertion
        $createdGames = Game::where('tournament_id', $tournamentId)
            ->where('id', '>', $lastGameId)
            ->orderBy('id', 'asc')
            ->get()
            ->toArray();

        return $createdGames;
    }

    public function deleteTournamentGames(int $tournamentId): bool
    {
        return Game::where('tournament_id', $tournamentId)->delete();
    }

    public function changeHomeAway(int $id, User $user): Game
    {
        $game = $this->findOne(['where' => ['id' => $id]]);
        if ($game) {
            $homeId = $game->team_id_home;
            $game->team_id_home = $game->team_id_away;
            $game->team_id_away = $homeId;
            $game->save();

            $this->getMessageService()->create([
                'user_id'     => $user->id,
                'type_id'     => 5,
                'to_id'       => $game->id,
                'restriction' => 2,
                'html'        => "Match {$game->number} has switched home grounds.",
            ]);
        }
        return $game;
    }

    public function getGames(array $queryParams, User $user): array
    {
        $orderBy = $queryParams['order_by'] ?? 'date';
        $orderDirection = $queryParams['order_direction'] ?? 'ASC';
        $page = $queryParams['page'] ?? 1;
        $limit = $queryParams['limit'] ?? 20;
        $searchTerm = $queryParams['search_term'] ?? null;
        $tournamentGroupId = $queryParams['tournament_group_id'] ?? null;
        $clubId = $queryParams['club_id'] ?? null;
        $teamId = $queryParams['team_id'] ?? null;
        $venueId = $queryParams['venue_id'] ?? null;
        $type = $queryParams['type'] ?? null;
        $courtId = $queryParams['court_id'] ?? null;
        $period = $queryParams['period'] ?? null;
        $mine = $queryParams['mine'] ?? null;
        $userId = $queryParams['user_id'] ?? null;
        $seasonSportId = $queryParams['season_sport_id'] ?? null;

        $query = Game::query();

        $query->where('season_sport_id', $seasonSportId);

        if ($tournamentGroupId) {
            $tournamentGroup = $this->getTournamentGroupService()->findOne([
                'where'   => ['id' => $tournamentGroupId],
                'include' => ['games', 'tournaments'],
            ]);

            if ($tournamentGroup) {
                $tournamentIds = $tournamentGroup->tournaments->pluck('id')->toArray();
                $query->where(function ($q) use ($tournamentGroupId, $tournamentIds) {
                    $q->where('group_id', $tournamentGroupId)
                        ->orWhereIn('tournament_id', $tournamentIds);
                });
            }
        }

        if ($clubId) {
            $club = $this->getClubService()->findOne([
                'where'   => ['id' => $clubId],
                'include' => ['teams'],
            ]);

            if ($club) {
                $teamIds = $club->teams->pluck('id')->toArray();
                $query->where(function ($q) use ($teamIds) {
                    $q->whereIn('team_id_home', $teamIds)
                        ->orWhereIn('team_id_away', $teamIds);
                });
            }
        }

        if ($teamId) {
            $query->where(function ($q) use ($teamId) {
                $q->where('team_id_home', $teamId)
                    ->orWhere('team_id_away', $teamId);
            });
        }

        if ($venueId) {
            $courts = $this->getCourtService()->findAll(['where' => ['venue_id' => $venueId]]);
            $courtIds = $courts->pluck('id')->toArray();
            $query->whereIn('court_id', $courtIds);
        }

        $isAdmin = $this->isAdmin($user);

        if ($type === 'conflicts' || $type === 'conflictsExcludeTimes') {
            if ($isAdmin) {
                $conflicts = $this->getConflictService()->findAll([
                    'where' => [
                        'ignore_associations' => false,
                        function ($q) {
                            $q->whereNotNull('start_time')
                                ->orWhereNotNull('blocked_association');
                        },
                    ],
                ]);

                $gameIds = $conflicts->pluck('game_id')->toArray();
                $query->whereIn('id', $gameIds);
            } else {
                $userClubs = $user->userRoles()->whereNotNull('club_id')->pluck('club_id')->toArray();
                $teams = $this->getTeamsService()->findAll(['where' => ['club_id' => ['in' => $userClubs]]]);
                $userTeamIds = $teams->pluck('id')->toArray();

                $conflictsHome = $this->getConflictService()->findAll([
                    'where' => ['ignore_home' => false],
                ]);

                $conflictsAway = $this->getConflictService()->findAll([
                    'where' => ['ignore_away' => false],
                ]);

                $conflictGameIds = array_merge(
                    $conflictsHome->pluck('game_id')->toArray(),
                    $conflictsAway->pluck('game_id')->toArray()
                );

                $query->where(function ($q) use ($conflictGameIds, $userTeamIds) {
                    $q->where(function ($subQ) use ($conflictGameIds, $userTeamIds) {
                        $subQ->whereIn('id', $conflictGameIds)
                            ->whereIn('team_id_home', $userTeamIds);
                    })->orWhere(function ($subQ) use ($conflictGameIds, $userTeamIds) {
                        $subQ->whereIn('id', $conflictGameIds)
                            ->whereIn('team_id_away', $userTeamIds);
                    });
                });
            }
        } elseif ($type === 'attention') {
            $query->where(function ($q) {
                $q->whereNull('court_id')
                    ->orWhereNull('time')
                    ->orWhereBetween('status_id', [1, 4]);
            });

            if (!$isAdmin) {
                $userClubs = $user->userRoles()->whereNotNull('club_id')->pluck('club_id')->toArray();
                $teams = $this->getTeamsService()->findAll(['where' => ['club_id' => ['in' => $userClubs]]]);
                $teamIds = $teams->pluck('id')->toArray();

                $query->where(function ($q) use ($teamIds) {
                    $q->whereIn('team_id_home', $teamIds)
                        ->orWhereIn('team_id_away', $teamIds);
                });
            }
        }

        if ($mine) {
            $clubIds = $user->userRoles()
                ->where('role_id', 1)
                ->whereNotNull('club_id')
                ->pluck('club_id')
                ->toArray();

            $teams = collect();
            if ($clubIds) {
                $teams = $this->getTeamsService()->findAll(['where' => ['club_id' => ['in' => $clubIds]]]);
            }

            $userTeams = $user->userRoles()
                ->whereNotNull('team_id')
                ->pluck('team_id')
                ->toArray();

            $userTeamIds = $teams->pluck('id')->toArray();

            if ($userTeams) {
                $additionalTeams = $this->getTeamsService()->findAll(['where' => ['id' => ['in' => $userTeams]]]);
                $userTeamIds = array_merge($userTeamIds, $additionalTeams->pluck('id')->toArray());
            }

            $query->where(function ($q) use ($userTeamIds) {
                $q->whereIn('team_id_home', $userTeamIds)
                    ->orWhereIn('team_id_away', $userTeamIds);
            });
        }

        if ($courtId) {
            $query->where('court_id', $courtId);
        }

        if ($period && is_array($period) && count($period) === 2) {
            $query->whereBetween('date', [$period[0], $period[1]]);
        }

        if ($userId) {
            $userRoles = UserRole::where('user_id', $userId)
                ->whereIn('role_id', [5, 6, 7, 8, 9])
                ->where('season_sport_id', $seasonSportId)
                ->where('user_role_approved_by_user_id', '>', 0)
                ->get();

            $userTeamIds = $userRoles->pluck('team_id')->toArray();

            $query->where(function ($q) use ($userTeamIds) {
                $q->whereIn('team_id_home', $userTeamIds)
                    ->orWhereIn('team_id_away', $userTeamIds);
            });
        }

        if ($searchTerm) {
            $query->where('number', $searchTerm);
        }

        // Handle ordering
        if ($orderBy === 'court') {
            $query->with(['court' => function ($q) use ($orderDirection) {
                $q->orderBy('name', $orderDirection);
            }]);
        } elseif ($orderBy === 'homeTeam') {
            $query->with(['homeTeam' => function ($q) use ($orderDirection) {
                $q->orderBy('tournament_name', $orderDirection);
            }]);
        } elseif ($orderBy === 'guestTeam') {
            $query->with(['guestTeam' => function ($q) use ($orderDirection) {
                $q->orderBy('tournament_name', $orderDirection);
            }]);
        } elseif ($orderBy === 'tournament') {
            $query->with(['tournament' => function ($q) use ($orderDirection) {
                $q->orderBy('short_name', $orderDirection);
            }]);
        } else {
            $query->orderBy($orderBy, $orderDirection);
        }

        $query->orderBy('time', 'ASC');

        $query->with([
            'tournament',
            'homeTeam',
            'guestTeam',
            'court.venue',
            'conflict',
        ]);

        $count = $query->count();
        $rows = $query->skip(($page - 1) * $limit)->take($limit)->get();

        return [
            'rows'  => $rows,
            'count' => $count,
        ];
    }

    public function getGamesCount(array $queryParams): int
    {
        try {
            $type = $queryParams['type'] ?? null;
            $isDeleted = isset($queryParams['isDeleted']) ? filter_var($queryParams['isDeleted'], FILTER_VALIDATE_BOOLEAN) : false;
            $statusId = $queryParams['statusId'] ?? null;
            $seasonSportId = $queryParams['seasonSportId'] ?? null;

            $query = Game::query();

            // Apply isDeleted filter
            if ($isDeleted === false) {
                $query->where('is_deleted', false);
            } elseif ($isDeleted === true) {
                $query->where('is_deleted', true);
            }

            // Apply statusId filter
            if ($statusId !== null && $statusId !== '' && $statusId !== '0') {
                $query->where('status_id', (int)$statusId);
            }

            // Apply seasonSportId filter (only if not 0 or empty)
            if ($seasonSportId !== null && $seasonSportId !== '' && $seasonSportId != '0' && $seasonSportId != 0) {
                $query->where('season_sport_id', (int)$seasonSportId);
            }

            if ($type === 'conflicts' || $type === 'conflictsExcludeTimes') {
                $conflictQuery = Conflict::query()
                    ->where('ignore_associations', false)
                    ->where(function ($q) {
                        $q->whereNotNull('start_time')
                            ->orWhereNotNull('blocked_association');
                    });

                $gameIds = $conflictQuery->pluck('game_id')->filter()->toArray();

                if (!empty($gameIds)) {
                    $query->whereIn('id', $gameIds);
                } else {
                    // No conflicts found, return 0
                    return 0;
                }
            } elseif ($type === 'attention') {
                $query->where(function ($q) {
                    $q->whereNull('court_id')
                        ->orWhereNull('time')
                        ->orWhereBetween('status_id', [1, 4]);
                });
            }

            return $query->count();
        } catch (\Exception $e) {
            \Log::error('Error in getGamesCount', [
                'message'     => $e->getMessage(),
                'trace'       => $e->getTraceAsString(),
                'queryParams' => $queryParams
            ]);
            throw $e;
        }
    }

    public function deleteGame(int $id, User $user): bool
    {
        $game = $this->findOne(['where' => ['id' => $id]]);
        if ($game) {
            $game->is_deleted = true;
            $game->save();

            $this->getMessageService()->create([
                'user_id'     => $user->id,
                'type_id'     => 13,
                'to_id'       => $id,
                'restriction' => 1,
                'html'        => "Match {$game->number} has been <strong>CANCELLED</strong> by " . ($user->name ?? $user->email),
            ]);

            return true;
        }
        return false;
    }

    public function postponeMatch(int $gameId, User $user, ?string $message): bool
    {
        $game = $this->findOne(['where' => ['id' => $gameId], 'include' => ['tournament']]);
        if ($game) {
            $game->status_id = 3;
            $game->save();

            if ($message) {
                $cancelMessage = "The match has been POSTPONED by " . ($user->name ?? $user->email) . " due to current circumstances. The association will decide on the status of the match in the near future";
                $this->getMessageService()->create([
                    'user_id'     => $user->id,
                    'type_id'     => 8,
                    'to_id'       => $gameId,
                    'restriction' => 1,
                    'html'        => $cancelMessage,
                ]);

                $this->getMessageService()->create([
                    'user_id'     => $user->id,
                    'type_id'     => 12,
                    'to_id'       => $gameId,
                    'restriction' => 1,
                    'html'        => $cancelMessage,
                ]);

                $this->getMessageService()->create([
                    'user_id'     => 1473, // MVP Admin id
                    'type_id'     => 1,
                    'to_id'       => 1874, // Lars account id
                    'restriction' => 1,
                    'html'        => ($user->name ?? $user->email) . " has cancelled match {$game->number} ({$game->tournament->short_name}). The has been given the status POSTPONED. You should check the correspondence of the match to determine the result",
                ]);

                $this->getMessageService()->create([
                    'user_id'     => $user->id,
                    'type_id'     => 5,
                    'to_id'       => $gameId,
                    'restriction' => 1,
                    'html'        => $message,
                ]);
            } else {
                $this->getMessageService()->create([
                    'user_id'     => $user->id,
                    'type_id'     => 10,
                    'to_id'       => $gameId,
                    'restriction' => 1,
                    'html'        => "Match {$game->number} has been given the status <strong>STOPPED</strong> by " . ($user->name ?? $user->email) . " <br /><br />This means that the planned date (see below) is no longer valid. The parties to the match will set a new time as soon as possible.<br /><br />Remember that you can follow the teams from the MVP App which can be downloaded from the App Store and Google Play",
                ]);

                $this->getMessageService()->create([
                    'user_id'     => $user->id,
                    'type_id'     => 12,
                    'to_id'       => $gameId,
                    'restriction' => 1,
                    'html'        => "The match has been POSTPONED by " . ($user->name ?? $user->email),
                ]);

                $this->getMessageService()->create([
                    'user_id'     => $user->id,
                    'type_id'     => 1,
                    'to_id'       => 1874, // Lars account id
                    'restriction' => 1,
                    'html'        => ($user->name ?? $user->email) . " has reported the request for POSTPONEMENT of match {$game->number} ({$game->tournament->short_name}). The match has so far been given the status POSTPONED. You should check the correspondence of the match to determine the result",
                ]);
            }

            return true;
        }
        return false;
    }

    public function setOrganizerClub(int $id, array $data, User $user): bool
    {
        $game = $this->findOne(['where' => ['id' => $id]]);
        if ($game) {
            $club = $this->getClubService()->findOne(['where' => ['id' => $data['clubId']]]);
            if ($club) {
                $game->organizer_club_id = $club->id;
                $game->save();

                if (isset($data['forWholeGroup']) && $data['forWholeGroup'] && $game->pool_id && $game->round_id) {
                    Game::where('pool_id', $game->pool_id)
                        ->where('round_id', $game->round_id)
                        ->update(['organizer_club_id' => $club->id]);
                }

                $this->getMessageService()->create([
                    'user_id'     => $user->id,
                    'type_id'     => 5,
                    'to_id'       => $game->id,
                    'restriction' => 2,
                    'html'        => "{$club->name} has been assigned the role of match organizer for the match. At the Grand Prix, this applies to all matches at the event.",
                ]);

                return true;
            }
        }
        return false;
    }

    // Additional complex methods (checkGame, checkForConflicts, saveDateTimeAndCourt, getCourts, updateReservation, getGamesWithRefs, movedGames, cancelledGames)
    // These are very large methods - implementing key parts below

    public function getCourts(int $id): array
    {
        $game = $this->findOne(['where' => ['id' => $id], 'include' => ['homeTeam', 'guestTeam']]);

        $organizerClubId = $game->organizer_club_id ?? $game->homeTeam->club_id;

        $clubVenuesForHomeTeam = ClubVenue::where('club_id', $organizerClubId)->get();
        $clubVenuesForGuestTeam = ClubVenue::where('club_id', $game->guestTeam->club_id)->get();

        $venueSeasonSports = VenueSeasonSport::where('season_sport_id', $game->season_sport_id)->get();

        $additionalCourts = $this->getCourtService()->findAll([
            'where'   => [
                'venue_id' => ['in' => $venueSeasonSports->pluck('venue_id')->toArray()],
            ],
            'include' => ['venue'],
        ]);

        if ($clubVenuesForHomeTeam->count() || $clubVenuesForGuestTeam->count()) {
            $courtsHomeTeam = $this->getCourtService()->findAll([
                'where'   => [
                    'venue_id' => ['in' => $clubVenuesForHomeTeam->pluck('venue_id')->toArray()],
                ],
                'include' => ['venue'],
            ]);

            $courtsGuestTeam = $this->getCourtService()->findAll([
                'where'   => [
                    'venue_id' => ['in' => $clubVenuesForGuestTeam->pluck('venue_id')->toArray()],
                ],
                'include' => ['venue'],
            ]);

            return [
                'home_team_courts'  => $courtsHomeTeam,
                'guest_team_courts' => $courtsGuestTeam,
                'additional_courts' => $additionalCourts,
            ];
        }

        return [
            'home_team_courts'  => collect([]),
            'guest_team_courts' => collect([]),
            'additional_courts' => $additionalCourts,
        ];
    }

    public function movedGames(array $queryParams): array
    {
        $orderBy = $queryParams['order_by'] ?? 'date';
        $orderDirection = $queryParams['order_direction'] ?? 'ASC';
        $page = $queryParams['page'] ?? 1;
        $limit = $queryParams['limit'] ?? 20;
        $searchTerm = $queryParams['search_term'] ?? null;
        $tournamentGroupId = $queryParams['tournament_group_id'] ?? null;
        $clubId = $queryParams['club_id'] ?? null;
        $penaltyStatusId = $queryParams['penalty_status_id'] ?? null;

        $query = Game::query();

        if ($penaltyStatusId !== null) {
            if ($penaltyStatusId > 0) {
                $query->where('penalty_status_id', '>=', $penaltyStatusId);
            } else {
                $query->where('penalty_status_id', $penaltyStatusId);
            }
        }

        $searchConditions = $this->generateSearchConditions([], $clubId, $tournamentGroupId, $searchTerm);
        foreach ($searchConditions as $condition) {
            $query->where($condition[0], $condition[1], $condition[2] ?? null);
        }

        if ($orderBy === 'homeTeam') {
            $query->with(['homeTeam' => function ($q) use ($orderDirection) {
                $q->orderBy('tournament_name', $orderDirection);
            }]);
        } else {
            $query->orderBy($orderBy, $orderDirection);
        }

        $query->orderBy('time', 'ASC');

        $query->with([
            'tournament',
            'homeTeam',
            'guestTeam',
            'penalties',
            'suggestions' => function ($q) {
                $q->where('accepted_by', '>', 0);
            },
        ]);

        $count = $query->count();
        $rows = $query->skip(($page - 1) * $limit)->take($limit)->get();

        return [
            'rows'  => $rows,
            'count' => $count,
        ];
    }

    public function cancelledGames(array $queryParams): array
    {
        $orderBy = $queryParams['orderBy'] ?? 'date';
        $orderDirection = $queryParams['orderDirection'] ?? 'ASC';
        $page = $queryParams['page'] ?? 1;
        $limit = $queryParams['limit'] ?? 20;
        $searchTerm = $queryParams['searchTerm'] ?? null;
        $tournamentGroupId = $queryParams['tournamentGroupId'] ?? null;
        $clubId = $queryParams['clubId'] ?? null;
        $penaltyStatusId = $queryParams['penaltyStatusId'] ?? null;

        $query = Game::query();

        if ($penaltyStatusId !== null) {
            if ($penaltyStatusId === 2) {
                $query->where('penalty_status_id', '>=', $penaltyStatusId);
            } else {
                $query->where('penalty_status_id', '<=', $penaltyStatusId);
            }
        }

        $searchConditions = $this->generateSearchConditions([], $clubId, $tournamentGroupId, $searchTerm);
        foreach ($searchConditions as $condition) {
            $query->where($condition[0], $condition[1], $condition[2] ?? null);
        }

        $query->where(function ($q) {
            $q->whereNull('points_home')
                ->orWhere('points_home', 0)
                ->orWhereNull('points_away')
                ->orWhere('points_away', 0);
        })
            ->where('status_id', 10)
            ->whereHas('tournament', function ($q) {
                $q->where('short_name', 'not like', '%GP%');
            });

        if ($orderBy === 'homeTeam') {
            $query->with(['homeTeam' => function ($q) use ($orderDirection) {
                $q->orderBy('tournament_name', $orderDirection);
            }]);
        } else {
            $query->orderBy($orderBy, $orderDirection);
        }

        $query->orderBy('time', 'ASC');

        $query->with([
            'tournament',
            'homeTeam',
            'guestTeam',
            'gamePenalties',
        ]);

        $count = $query->count();
        $rows = $query->skip(($page - 1) * $limit)->take($limit)->get();

        return [
            'rows'  => $rows,
            'count' => $count,
        ];
    }

    protected function generateSearchConditions(
        array   $searchConditions,
        ?int    $clubId,
        ?int    $tournamentGroupId,
        ?string $searchTerm
    ): array
    {
        $conditions = [];

        if ($clubId) {
            $teamIds = $this->getTeamsByClub($clubId);
            $conditions[] = [
                function ($q) use ($teamIds) {
                    $q->whereIn('team_id_home', $teamIds)
                        ->orWhereIn('team_id_away', $teamIds);
                },
            ];
        }

        if ($searchTerm) {
            $conditions[] = ['number', '=', $searchTerm];
        }

        if ($tournamentGroupId) {
            $tournamentGroup = $this->getTournamentGroupService()->findOne([
                'where'   => ['id' => $tournamentGroupId],
                'include' => ['games', 'tournaments'],
            ]);

            if ($tournamentGroup) {
                $tournamentIds = $tournamentGroup->tournaments->pluck('id')->toArray();
                $conditions[] = [
                    function ($q) use ($tournamentGroupId, $tournamentIds) {
                        $q->where('group_id', $tournamentGroupId)
                            ->orWhereIn('tournament_id', $tournamentIds);
                    },
                ];
            }
        }

        return $conditions;
    }

    protected function getTeamsByClub(int $clubId): array
    {
        $club = $this->getClubService()->findOne([
            'where'   => ['id' => $clubId],
            'include' => ['teams'],
        ]);

        if ($club) {
            return $club->teams->pluck('id')->toArray();
        }

        return [];
    }

    public function checkGame(int $id, array $checkGameDto): array
    {
        $game = $this->findOne([
            'where'   => ['id' => $id],
            'include' => ['court', 'homeTeam', 'guestTeam', 'tournament'],
        ]);

        if (!$game) {
            return [];
        }

        $errors = [];
        $time = $checkGameDto['time'] ?? null;
        $dateInput = $checkGameDto['date'] ?? null;
        $courtId = $checkGameDto['court_id'] ?? null;
        $suggestionId = $checkGameDto['suggestion_id'] ?? null;

        // Parse date if it's a full datetime string
        $date = null;
        if ($dateInput) {
            try {
                $parsedDate = Carbon::parse($dateInput);
                $date = $parsedDate->format('Y-m-d');
            } catch (\Exception $e) {
                // If parsing fails, use as is
                $date = $dateInput;
            }
        }

        if ($suggestionId) {
            $suggestion = $this->getSuggestionsService()->findOne(['id' => $suggestionId]);
            if ($suggestion) {
                $time = $suggestion->time;
                $date = $suggestion->date ? Carbon::parse($suggestion->date)->format('Y-m-d') : null;
                $courtId = $suggestion->court_id;
            }
        }

        if ($time && ($time <= '07:59' || $time >= '22:01')) {
            $errors[] = [
                'message' => Carbon::parse($time)->format('H:i') . ' is incorrect.',
                'header'  => 'Incorrect time:',
                'type'    => 'time',
                'status'  => 'warning',
            ];
        }

        $courtIds = [];
        if ($courtId) {
            $court = $this->getCourtService()->findOne(['id' => $courtId]);
            if ($court) {
                // Find courts where parent_id = courtId OR id in [courtId, parent_id] (matching NestJS logic)
                $courtBlocks = $this->getCourtService()->findAll([
                    'where' => function ($q) use ($courtId, $court) {
                        $q->where('parent_id', $courtId)
                            ->orWhere(function ($subQ) use ($courtId, $court) {
                                if ($court->parent_id ?? null) {
                                    $subQ->whereIn('id', [$court->id, $court->parent_id]);
                                } else {
                                    $subQ->where('id', $courtId);
                                }
                            });
                    },
                ]);

                if ($courtBlocks && $courtBlocks->count() > 0) {
                    $courtIds = $courtBlocks->pluck('id')->toArray();
                }
            }
        }

        if ($date) {
            $gameDate = Carbon::parse($date);

            $blockedPeriod = $this->getBlockedPeriodsService()->findOne([
                'where'   => [
                    function ($q) use ($game) {
                        $q->where('block_all', true)
                            ->orWhereHas('tournamentGroups', function ($subQ) use ($game) {
                                $subQ->where('tournament_groups.id', $game->tournament->tournament_group_id);
                            });
                    },
                    ['start_date', '<=', $gameDate->format('Y-m-d')],
                    ['end_date', '>=', $gameDate->format('Y-m-d')],
                    ['season_sport_id', '=', $game->season_sport_id],
                ],
                'include' => ['tournamentGroups'],
            ]);

            if ($blockedPeriod) {
                $errors[] = [
                    'type'    => 'blockedPeriod',
                    'header'  => 'Blocked Periods',
                    'status'  => 'danger',
                    'message' => $gameDate->format('Y-m-d') . " is blocked by the association: {$blockedPeriod->title}",
                ];
            }

            $minusDayDate = $gameDate->copy()->subDay();
            $plusDayDate = $gameDate->copy()->addDay();

            $otherGames = $this->gameRepository->query()
                ->where('status_id', '>', 3)
                ->where('is_deleted', false)
                ->where('id', '!=', $game->id)
                ->where(function ($q) use ($game) {
                    $q->whereIn('team_id_home', [$game->team_id_home, $game->team_id_away])
                        ->orWhereIn('team_id_away', [$game->team_id_home, $game->team_id_away]);
                })
                ->whereBetween('date', [$minusDayDate->format('Y-m-d'), $plusDayDate->format('Y-m-d')])
                ->with(['court.venue', 'homeTeam', 'guestTeam'])
                ->get();

            foreach ($otherGames as $otherGame) {
                $errors[] = [
                    'type'    => 'conflict',
                    'header'  => 'Conflict match:',
                    'status'  => 'danger',
                    'message' => "#{$otherGame->number} {$otherGame->homeTeam->tournament_name} - {$otherGame->guestTeam->tournament_name} {$otherGame->date} " . ($otherGame->time ? Carbon::parse($otherGame->time)->format('H:i') : '') . ' ' . ($otherGame->court?->venue?->name ?? ''),
                ];
            }

            if (!empty($courtIds)) {
                $otherGamesCourt = $this->gameRepository->query()
                    ->where('status_id', '>', 3)
                    ->where('is_deleted', false)
                    ->where('id', '!=', $game->id)
                    ->where('date', $date)
                    ->whereIn('court_id', $courtIds)
                    ->where('time', '>=', '07:59:00')
                    ->where('time', '<=', '22:00:00')
                    ->with(['homeTeam', 'guestTeam', 'court.venue', 'tournament'])
                    ->orderBy('time', 'asc')
                    ->get();

                foreach ($otherGamesCourt as $otherGameCourt) {
                    $errors[] = [
                        'type'    => 'court',
                        'header'  => 'Other matches on the same court/day:',
                        'status'  => 'info',
                        'message' => Carbon::parse($otherGameCourt->time)->format('H:i') . " #{$otherGameCourt->number} {$otherGameCourt->tournament->short_name} {$otherGameCourt->homeTeam->tournament_name} - {$otherGameCourt->guestTeam->tournament_name}",
                    ];
                }
            }
        }

        if ($time && $courtId) {
            $endTime = Carbon::parse($time)->addMinutes(90)->format('H:i');

            // Match NestJS logic: find reservation with nested TimeSlot where clause
            $reservation = $this->getReservationsService()->findOne([
                'where'   => [
                    'is_deleted' => false,
                    function ($q) use ($game) {
                        $q->where('game_id', '!=', $game->id)
                            ->orWhereNull('game_id');
                    },
                    function ($q) use ($time, $endTime) {
                        $q->where(function ($subQ) use ($time, $endTime) {
                            $subQ->where('start_time', '>=', $time)
                                ->where('start_time', '<=', $endTime);
                        })
                            ->orWhere(function ($subQ) use ($time, $endTime) {
                                $subQ->where('end_time', '>=', $time)
                                    ->where('end_time', '<=', $endTime);
                            })
                            ->orWhere(function ($subQ) use ($time, $endTime) {
                                $subQ->where('end_time', '>', $endTime)
                                    ->where('start_time', '<', $time);
                            });
                    },
                ],
                'include' => [
                    'timeSlot' => function ($q) use ($courtId, $date) {
                        $q->where('court_id', $courtId)
                            ->where('date', $date)
                            ->where('expiration', '>=', Carbon::now()->format('Y-m-d'));
                    },
                    'type',
                ],
            ]);

            if ($reservation) {
                $errors[] = [
                    'type'    => 'reservation',
                    'header'  => 'The court is reserved:',
                    'status'  => 'danger',
                    'message' => Carbon::parse($reservation->start_time)->format('H:i') . ' to ' . Carbon::parse($reservation->end_time)->format('H:i') . ' ' . ($reservation->text ?: ($reservation->type->text ?? '')),
                ];
            }
        }

        $userRoleRepo = app(\App\Repositories\V5\UserRoleRepository::class);
        $teamCoaches = $userRoleRepo->query()
            ->whereIn('role_id', [5, 6, 7])
            ->whereIn('team_id', [$game->team_id_away, $game->team_id_home])
            ->where('user_role_approved_by_user_id', '>', 0)
            ->with('user')
            ->get();

        foreach ($teamCoaches as $coach) {
            $coachOtherTeams = $userRoleRepo->query()
                ->whereIn('role_id', [5, 6, 7])
                ->where('user_id', $coach->user_id)
                ->where('team_id', '!=', $coach->team_id)
                ->where('team_id', '>', 0)
                ->where('user_role_approved_by_user_id', '>', 0)
                ->get();

            $coachOtherTeamIds = $coachOtherTeams->pluck('team_id')->toArray();

            if (count($coachOtherTeamIds) && $date) {
                $otherCoachGames = $this->gameRepository->query()
                    ->where('status_id', '>', 3)
                    ->where('is_deleted', false)
                    ->where('id', '!=', $game->id)
                    ->where('date', $date)
                    ->where(function ($q) use ($coachOtherTeamIds) {
                        $q->whereIn('team_id_home', $coachOtherTeamIds)
                            ->orWhereIn('team_id_away', $coachOtherTeamIds);
                    })
                    ->with(['homeTeam', 'guestTeam', 'court.venue', 'tournament'])
                    ->orderBy('time', 'asc')
                    ->get();

                foreach ($otherCoachGames as $otherGame) {
                    $errors[] = [
                        'type'    => 'coach',
                        'header'  => 'Conflicts regarding coaches:',
                        'status'  => 'warning',
                        'message' => ($coach->user->name ?? '') . " have other match on the same day: " . ($otherGame->time ? Carbon::parse($otherGame->time)->format('H:i') : '') . " #{$otherGame->number} {$otherGame->homeTeam->tournament_name} - {$otherGame->guestTeam->tournament_name}",
                    ];
                }
            }
        }

        if ($courtId && $date) {
            $courtForVenue = $this->getCourtService()->findOne(['id' => $courtId]);
            $venueId = $courtForVenue?->venue_id;

            $checkHome = \App\Models\V5\ClubVenue::query()
                ->where('venue_id', $venueId)
                ->where('club_id', $game->homeTeam->club_id)
                ->first();

            $checkAway = \App\Models\V5\ClubVenue::query()
                ->where('venue_id', $venueId)
                ->where('club_id', $game->guestTeam->club_id)
                ->first();

            $team = 'Home Team';
            $checkClubId = $game->homeTeam->club_id;

            if (!$checkHome && $checkAway) {
                $team = 'The guest team';
                $checkClubId = $game->guestTeam->club_id;
            }

            $times = $this->getTimeSlotService()->findAll([
                'where' => [
                    'is_deleted' => false,
                    'club_id'    => $checkClubId,
                    'date'       => $date,
                    'court_id'   => $courtId,
                    ['expiration', '>=', Carbon::now()->format('Y-m-d')],
                ],
            ]);

            if ($times->count()) {
                if (empty($errors)) {
                    $errors[] = [
                        'type'    => 'success',
                        'header'  => 'All checks OK!',
                        'status'  => 'success',
                        'message' => '',
                    ];
                }
                $errors[] = [
                    'type'    => 'team',
                    'header'  => "Times' information:",
                    'status'  => 'info',
                    'message' => "{$team} has the court " . Carbon::parse($date)->format('d-M-Y') . ' from ' . Carbon::parse($times->first()->start_time)->format('H:i') . ' to ' . Carbon::parse($times->first()->end_time)->format('H:i'),
                ];
            } else {
                $errors[] = [
                    'type'    => 'team',
                    'header'  => "Times' information:",
                    'status'  => 'warning',
                    'message' => "{$team} don't have time on " . Carbon::parse($date)->format('d-M-Y'),
                ];
            }
        }

        return $errors;
    }

    public function saveDateTimeAndCourt(int $id, array $data, User $user): bool
    {
        $game = $this->findOne([
            'where'   => ['id' => $id],
            'include' => ['tournament', 'homeTeam', 'guestTeam']
        ]);

        if (!$game) {
            throw new \Exception('Game not found');
        }

        $statusId = 7;
        $time = $data['time'] ?? $game->time;
        $date = $data['date'] ?? $game->date;
        $courtId = $data['court_id'] ?? $game->court_id;

        // Handle suggestion acceptance/rejection
        if (isset($data['suggestion_id'])) {
            $suggestion = $this->getSuggestionsService()->findOne(['id' => $data['suggestion_id']]);

            if ($suggestion) {
                $court = $suggestion->court_id ? $this->getCourtService()->findOne(['id' => $suggestion->court_id], ['venue']) : null;
                $courtName = $court ? ($court->venue ? $court->venue->name . ' ' : '') . $court->name : '';

                if (isset($data['is_accepted_suggestion']) && $data['is_accepted_suggestion']) {
                    $time = $suggestion->time;
                    $date = $suggestion->date;
                    $courtId = $suggestion->court_id;

                    $suggestion->accepted_by = $user->id;
                    $suggestion->accepted_date = Carbon::now();
                    $suggestion->save();
                } else {
                    $this->getMessageService()->create([
                        'user_id'     => $user->id,
                        'type_id'     => 5,
                        'to_id'       => $id,
                        'restriction' => 1,
                        'html'        => "Moving match {$game->number} to " . Carbon::parse($date)->format('Y-m-d') . " " . ($time ? Carbon::parse($time)->format('H:i') : '') . " {$courtName} is DENIED!",
                    ]);

                    $suggestion->rejected_by = $user->id;
                    $suggestion->rejected_date = Carbon::now();
                    $suggestion->save();

                    if (!$game->time) {
                        $statusId = 1;
                    } elseif (!$game->court_id) {
                        $statusId = 2;
                    }
                }
            }
        } else {
            // Check if user is admin
            $isAdmin = $user->roles->contains(function ($role) {
                return in_array($role->description, ['SUPER_ADMIN', 'ASSOCIATION_ADMIN']);
            });

            if (!$isAdmin) {
                $gameDate = Carbon::parse($game->date);
                $newDate = Carbon::parse($date);
                $gameTime = $game->time ? Carbon::parse($game->time)->format('H:i') : null;
                $newTime = $time ? Carbon::parse($time)->format('H:i') : null;

                // If date or time changed, create a suggestion
                if ($gameDate->format('Y-m-d') !== $newDate->format('Y-m-d') ||
                    $gameTime !== $newTime) {
                    $statusId = 4;

                    // Reject existing suggestions
                    $this->getSuggestionsService()->updateByCondition(
                        [
                            'where' => [
                                'game_id'     => $game->id,
                                'accepted_by' => null,
                                'rejected_by' => null,
                            ]
                        ],
                        [
                            'rejected_by'   => $user->id,
                            'rejected_date' => Carbon::now(),
                        ]
                    );

                    $court = $courtId ? $this->getCourtService()->findOne(['id' => $courtId], ['venue']) : null;
                    $courtName = $court ? ($court->venue ? $court->venue->name . ' ' : '') . $court->name : '';

                    $suggestion = $this->getSuggestionsService()->create([
                        'date'           => $date,
                        'time'           => $time,
                        'court_id'       => $courtId,
                        'game_id'        => $game->id,
                        'requested_by'   => $user->id,
                        'requested_date' => Carbon::now(),
                    ]);

                    $this->getMessageService()->create([
                        'user_id'     => $user->id,
                        'type_id'     => 6,
                        'to_id'       => $suggestion->id,
                        'restriction' => 1,
                        'html'        => "Match {$game->number} {$game->tournament->short_name} {$game->homeTeam->tournament_name} - {$game->guestTeam->tournament_name} would like to be moved to " . Carbon::parse($date)->format('Y-m-d') . " {$time} {$courtName}",
                    ]);

                    $this->getMessageService()->create([
                        'user_id'     => $user->id,
                        'type_id'     => 7,
                        'to_id'       => $game->id,
                        'restriction' => 1,
                        'html'        => "The match would like to be moved to " . Carbon::parse($date)->format('Y-m-d') . " {$time} {$courtName}",
                    ]);
                } elseif ($courtId != $game->court_id) {
                    // Court changed but date/time didn't - just notify (todo: implement notification)
                }
            }
        }

        // Check if user is admin (needed for message creation)
        $isAdmin = $user->roles->contains(function ($role) {
            return in_array($role->description, ['SUPER_ADMIN', 'ASSOCIATION_ADMIN']);
        });

        $response = false;
        if ($statusId === 7) {
            $response = $this->update($id, [
                'time'      => $time,
                'date'      => $date,
                'court_id'  => $courtId,
                'status_id' => $statusId,
            ]);

            if (!isset($data['suggestion_id']) || (isset($data['is_accepted_suggestion']) && $data['is_accepted_suggestion'])) {
                $court = $courtId ? $this->getCourtService()->findOne(['id' => $courtId], ['venue']) : null;
                $courtName = $court ? ($court->venue ? $court->venue->name . ' ' : '') . $court->name : '';

                $message = "Match {$game->number} has moved to " . Carbon::parse($date)->format('Y-m-d') . " {$time} {$courtName}";
                $this->getMessageService()->create([
                    'user_id'     => $user->id,
                    'type_id'     => 5,
                    'to_id'       => $id,
                    'restriction' => 0,
                    'html'        => $message,
                ]);

                if (!$isAdmin) {
                    $this->getMessageService()->create([
                        'user_id'     => $user->id,
                        'type_id'     => 1,
                        'to_id'       => $id,
                        'restriction' => 0,
                        'html'        => $message,
                    ]);
                    $this->getMessageService()->create([
                        'user_id'     => $user->id,
                        'type_id'     => 1,
                        'to_id'       => 1874, // Lars account id
                        'restriction' => 0,
                        'html'        => $message,
                    ]);
                }
            }
        } else {
            $response = $this->update($id, [
                'status_id' => $statusId,
            ]);
        }

        $this->updateReservation($id);
        if ($time && $date && $courtId) {
            $this->checkForConflicts($id);
        }

        return $response;
    }

    protected function updateReservation(int $gameId): void
    {
        // Mark existing reservations as deleted
        $this->getReservationsService()->updateByCondition(
            ['where' => ['game_id' => $gameId]],
            ['is_deleted' => true]
        );

        $game = $this->findOne([
            'where'   => ['id' => $gameId],
            'include' => ['tournament.tournamentGroup', 'homeTeam', 'guestTeam']
        ]);

        if ($game && $game->time && $game->court_id) {
            $tournamentConfig = $this->getTournamentConfigService()->findOne([
                'id' => $game->tournament->tournamentGroup->tournament_configs_id
            ]);

            if ($tournamentConfig) {
                $timeStart = Carbon::parse($game->time)->subMinutes($tournamentConfig->minimum_warmup_minutes)->format('H:i');
                $timeEnd = Carbon::parse($game->time)->addMinutes($tournamentConfig->expected_duration_minutes)->format('H:i');

                // Check if reservation already exists
                $existingReservations = $this->getReservationsService()->findAll([
                    'where'   => [
                        'is_deleted' => false,
                        'game_id'    => $gameId,
                    ],
                    'include' => ['timeSlot']
                ]);

                $existingReservation = $existingReservations->first(function ($reservation) use ($game) {
                    return $reservation->timeSlot &&
                        $reservation->timeSlot->court_id == $game->court_id &&
                        $reservation->timeSlot->date == $game->date;
                });

                if ($existingReservation) {
                    return;
                }

                // Find matching time slots
                $timeSlots = $this->getTimeSlotService()->findAll([
                    'where' => [
                        'court_id' => $game->court_id,
                        'date'     => $game->date,
                    ]
                ]);

                // Filter time slots that overlap with game time and are not expired
                $matchingTimeSlots = $timeSlots->filter(function ($timeSlot) use ($timeStart, $timeEnd) {
                    $notExpired = !$timeSlot->expiration || Carbon::parse($timeSlot->expiration)->gte(Carbon::now());
                    $overlaps = ($timeSlot->start_time <= $timeStart && $timeSlot->end_time > $timeStart) ||
                        ($timeSlot->start_time < $timeEnd && $timeSlot->end_time >= $timeEnd);
                    return $notExpired && $overlaps;
                });

                foreach ($matchingTimeSlots as $timeSlot) {
                    $this->getReservationsService()->create([
                        'game_id'      => $gameId,
                        'time_slot_id' => $timeSlot->id,
                        'start_time'   => $timeStart,
                        'end_time'     => $timeEnd,
                    ]);
                }
            }
        }
    }

    public function checkForConflicts(int $gameId): ?Conflict
    {
        // This method should check for conflicts and update the conflict record
        // For now, we'll return the existing conflict if it exists
        $game = $this->findOne(['id' => $gameId]);
        if ($game) {
            return $game->conflict;
        }
        return null;
    }
}
