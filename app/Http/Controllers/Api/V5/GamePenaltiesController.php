<?php

namespace App\Http\Controllers\Api\V5;

use App\Http\Controllers\Controller;
use App\Services\V5\GamePenaltiesService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GamePenaltiesController extends Controller
{
    protected GamePenaltiesService $gamePenaltiesService;

    public function __construct(GamePenaltiesService $gamePenaltiesService)
    {
        $this->gamePenaltiesService = $gamePenaltiesService;
    }

    public function updateOrCreate(Request $request): JsonResponse
    {
        $data = $request->all();
        $penalty = $this->gamePenaltiesService->findOne([
            'where' => [
                'game_id' => $data['gameId'],
                'side' => $data['side'],
                'number' => $data['number'],
            ],
        ]);

        if ($penalty) {
            $penalty->value = $data['value'];
            $penalty->save();
            $penalty->refresh();
            return response()->json($penalty);
        }

        $penalty = $this->gamePenaltiesService->create($data);
        return response()->json($penalty, 201);
    }
}

