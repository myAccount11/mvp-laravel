<?php

namespace App\Http\Controllers\Api\V5;

use App\Http\Controllers\Controller;
use App\Http\Requests\V5\CreatePlayerRequest;
use App\Http\Requests\V5\UpdatePlayerJerseyNumberRequest;
use App\Services\V5\PlayerService;
use Illuminate\Http\JsonResponse;

class PlayerController extends Controller
{
    protected PlayerService $playerService;

    public function __construct(PlayerService $playerService)
    {
        $this->playerService = $playerService;
    }

    public function create(CreatePlayerRequest $request): JsonResponse
    {
        $player = $this->playerService->create($request->validated());
        return response()->json($player, 201);
    }

    public function updateJerseyNumber(int $id, UpdatePlayerJerseyNumberRequest $request): JsonResponse
    {
        $result = $this->playerService->updateJerseyNumber($id, $request->validated()['jersey_number']);
        return response()->json(['success' => $result]);
    }

    public function getAllPlayers(): JsonResponse
    {
        $orderBy = request('orderBy', 'id');
        $orderDirection = request('orderDirection', 'asc');
        $players = $this->playerService->findAll($orderBy, $orderDirection);
        return response()->json($players);
    }
}

