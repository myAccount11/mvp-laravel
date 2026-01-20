<?php

namespace App\Http\Controllers\Api\V5;

use App\Http\Controllers\Controller;
use App\Http\Requests\V5\CheckGameRequest;
use App\Services\V5\GameService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GamesController extends Controller
{
    protected GameService $gameService;

    public function __construct(GameService $gameService)
    {
        $this->gameService = $gameService;
    }

    public function create(Request $request): JsonResponse
    {
        $game = $this->gameService->create($request->all());
        return response()->json($game, 201);
    }

    public function getGames(Request $request): JsonResponse
    {
        $user = Auth::user();
        $result = $this->gameService->getGames($request->all(), $user);
        return response()->json($result);
    }

    public function getGamesCount(Request $request): JsonResponse
    {
        try {
            $count = $this->gameService->getGamesCount($request->all());
            return response()->json($count);
        } catch (\Exception $e) {
            \Log::error('Error in GamesController::getGamesCount', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            return response()->json([
                'error' => 'Failed to get games count',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getGamesWithRefs(Request $request): JsonResponse
    {
        $result = $this->gameService->getGamesWithRefs($request->all());
        return response()->json($result);
    }

    public function createAllTournamentGames(Request $request, int $tournamentId): JsonResponse
    {
        try {
            DB::beginTransaction();
            $seasonSportId = $request->query('seasonSportId');
            $result = $this->gameService->createAllTournamentGames($tournamentId, $seasonSportId);
            DB::commit();
            return response()->json($result);
        } catch (\Exception $e) {
            DB::rollBack();
            // Check if it's a team count validation error
            if (strpos($e->getMessage(), 'Team Count Error') !== false) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'error' => 'Validation failed'
                ], 422);
            }
            // Re-throw other exceptions
            throw $e;
        }
    }

    public function deleteTournamentGames(int $tournamentId): JsonResponse
    {
        $result = $this->gameService->deleteTournamentGames($tournamentId);
        return response()->json($result);
    }

    public function destroy(int $id): JsonResponse
    {
        $user = Auth::user();
        $result = $this->gameService->deleteGame($id, $user);
        return response()->json($result);
    }

    public function getMovedGames(Request $request): JsonResponse
    {
        $result = $this->gameService->movedGames($request->all());
        return response()->json($result);
    }

    public function getCancelledGames(Request $request): JsonResponse
    {
        $result = $this->gameService->cancelledGames($request->all());
        return response()->json($result);
    }

    public function getById(int $id, Request $request): JsonResponse
    {
        $user = Auth::user();
        $game = $this->gameService->findOne([
            'where' => ['id' => $id],
            'include' => [
                'tournament',
                'homeTeam.club',
                'guestTeam',
                'court.venue',
                'suggestion' => function ($query) {
                    $query->whereNull('accepted_by')
                        ->whereNull('rejected_by')
                        ->with(['requestedByUser', 'court.venue']);
                },
                'messages' => function ($query) {
                    $query->whereIn('type_id', [5, 7, 12, 13])
                        ->with(['writer.userRoles', 'attachments']);
                },
                'gameNotes',
            ],
        ]);

        if (!$game) {
            return response()->json(['message' => 'Game not found'], 404);
        }

        // Get conflict based on user roles
        $roles = $user->roles;
        $gameData = $game->toArray();

        $isAdmin = $roles->contains(function ($role) {
            return in_array($role->description, ['SUPER_ADMIN', 'ASSOCIATION_ADMIN']);
        });

        if ($isAdmin) {
            $gameData['conflict'] = $game->conflict()->where('ignore_associations', false)->first();
        } else {
            $clubRoles = $roles->filter(function ($role) {
                return in_array($role->description, ['CLUB_MANAGER', 'HEAD_COACH', 'ASSISTANT_COACH', 'TEAM_MANAGER']);
            });

            $homeTeamRole = $clubRoles->first(function ($role) use ($game) {
                return ($role->pivot->club_id ?? null) === $game->homeTeam->club_id ||
                    ($role->pivot->team_id ?? null) === $game->homeTeam->id;
            });

            if ($homeTeamRole) {
                $gameData['conflict'] = $game->conflict()->where('ignore_home', false)->first();
            } else {
                $guestTeamRole = $clubRoles->first(function ($role) use ($game) {
                    return ($role->pivot->club_id ?? null) === $game->guestTeam->club_id ||
                        ($role->pivot->team_id ?? null) === $game->guestTeam->id;
                });

                if ($guestTeamRole) {
                    $gameData['conflict'] = $game->conflict()->where('ignore_away', false)->first();
                }
            }
        }

        return response()->json($gameData);
    }

    public function changeHomeAway(int $id): JsonResponse
    {
        $user = Auth::user();
        $game = $this->gameService->changeHomeAway($id, $user);
        return response()->json($game);
    }

    public function update(int $id, Request $request): JsonResponse
    {
        $result = $this->gameService->update($id, $request->all());
        return response()->json($result);
    }

    public function checkGame(int $id, CheckGameRequest $request): JsonResponse
    {
        $errors = $this->gameService->checkGame($id, $request->validated());
        return response()->json($errors);
    }

    public function getCourtsForGame(int $id): JsonResponse
    {
        $courts = $this->gameService->getCourts($id);
        return response()->json($courts);
    }

    public function saveTimeAndCourt(int $id, Request $request): JsonResponse
    {
        $user = Auth::user();
        $result = $this->gameService->saveDateTimeAndCourt($id, $request->all(), $user);
        return response()->json($result);
    }

    public function setOrganizerClub(int $id, Request $request): JsonResponse
    {
        $user = Auth::user();
        $result = $this->gameService->setOrganizerClub($id, $request->all(), $user);
        return response()->json($result);
    }

    public function checkForGameConflict(int $gameId): JsonResponse
    {
        $conflict = $this->gameService->checkForConflicts($gameId);
        return response()->json($conflict);
    }

    public function postponeMatch(int $gameId, Request $request): JsonResponse
    {
        $user = Auth::user();
        $message = $request->input('message');
        $result = $this->gameService->postponeMatch($gameId, $user, $message);
        return response()->json($result);
    }
}

