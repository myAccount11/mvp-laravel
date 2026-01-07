<?php

namespace App\Http\Controllers\Api\V5;

use App\Http\Controllers\Controller;
use App\Http\Requests\V5\CreateClubRequest;
use App\Http\Requests\V5\UpdateClubRequest;
use App\Http\Requests\V5\AddUserRequest;
use App\Services\V5\ClubService;
use App\Services\V5\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ClubController extends Controller
{
    protected $clubService;
    protected $userService;

    public function __construct(ClubService $clubService, UserService $userService)
    {
        $this->clubService = $clubService;
        $this->userService = $userService;
    }

    public function create(CreateClubRequest $request): JsonResponse
    {
        $seasonSportId = $request->query('season_sport_id');
        $club = $this->clubService->createClub($request->validated(), $seasonSportId);
        return response()->json($club, 201);
    }

    public function index(Request $request): JsonResponse
    {
        $result = $this->clubService->findAndCountAll($request->all());
        return response()->json($result);
    }

    public function getAll(Request $request): JsonResponse
    {
        $clubs = $this->clubService->findAll($request->all());
        return response()->json($clubs);
    }

    public function show($id): JsonResponse
    {
        $club = $this->clubService->findOne(['id' => $id]);
        if (!$club) {
            return response()->json(['error' => 'Club not found'], 404);
        }
        return response()->json($club);
    }

    public function getClubUsers($id, Request $request): JsonResponse
    {
        $result = $this->userService->findAndCountAllClubOrTeamUsers(array_merge(
            $request->all(),
            ['club_id' => $id]
        ));
        return response()->json($result);
    }

    public function getAllClubUsers($id, Request $request): JsonResponse
    {
        $users = $this->userService->findAllClubUsers([
            'club_id' => $id,
            'season_sport_id' => $request->input('season_sport_id'),
        ]);
        return response()->json($users);
    }

    public function getByIdCourtsPriority($id): JsonResponse
    {
        $club = $this->clubService->getClubWithVenuesAndCourts($id);
        if (!$club) {
            return response()->json(['error' => 'Club not found'], 404);
        }
        return response()->json($club);
    }

    public function update($id, UpdateClubRequest $request): JsonResponse
    {
        $result = $this->clubService->update($id, $request->validated());
        return response()->json($result);
    }

    public function destroy($id): JsonResponse
    {
        $result = $this->clubService->delete($id);
        return response()->json($result);
    }

    public function addClubUser($id, AddUserRequest $request): JsonResponse
    {
        $user = $request->user();
        $club = $this->clubService->addClubUser(
            $request->input('email'),
            $id,
            $request->input('season_sport_id'),
            $user
        );
        return response()->json($club);
    }
}

