<?php

namespace App\Http\Controllers\Api\V5;

use App\Http\Controllers\Controller;
use App\Services\V5\GameNotesService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GameNotesController extends Controller
{
    protected GameNotesService $gameNotesService;

    public function __construct(GameNotesService $gameNotesService)
    {
        $this->gameNotesService = $gameNotesService;
    }

    public function create(Request $request): JsonResponse
    {
        $user = Auth::user();
        $data = $request->all();
        
        $note = $this->gameNotesService->findOne([
            'where' => [
                'game_id' => $data['gameId'],
                'user_id' => $user->id,
            ],
        ]);

        if ($note) {
            $note->text = $data['text'];
            $note->save();
            $note->refresh();
            return response()->json($note);
        }

        $data['userId'] = $user->id;
        $note = $this->gameNotesService->create($data);
        return response()->json($note, 201);
    }

    public function delete(int $id): JsonResponse
    {
        $result = $this->gameNotesService->delete($id);
        return response()->json($result);
    }
}

