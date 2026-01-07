<?php

namespace App\Http\Controllers\Api\V5;

use App\Http\Controllers\Controller;
use App\Http\Requests\V5\CreateSuggestionRequest;
use App\Http\Requests\V5\UpdateSuggestionRequest;
use App\Services\V5\SuggestionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SuggestionController extends Controller
{
    protected SuggestionService $suggestionService;

    public function __construct(SuggestionService $suggestionService)
    {
        $this->suggestionService = $suggestionService;
    }

    public function create(CreateSuggestionRequest $request): JsonResponse
    {
        $suggestion = $this->suggestionService->create($request->validated());
        return response()->json($suggestion, 201);
    }

    public function getAll(Request $request): JsonResponse
    {
        $orderBy = $request->query('orderBy', 'id');
        $orderDirection = $request->query('orderDirection', 'ASC');
        $query = $request->except(['orderBy', 'orderDirection']);

        $conditions = [
            'where' => $query,
            'order' => [[$orderBy, $orderDirection]],
        ];

        $suggestions = $this->suggestionService->findAll($conditions);
        return response()->json($suggestions);
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->suggestionService->delete($id);
        if (!$deleted) {
            return response()->json(['message' => 'Suggestion not found'], 404);
        }
        return response()->json(['message' => 'Suggestion deleted successfully']);
    }

    public function getById(int $id): JsonResponse
    {
        $suggestion = $this->suggestionService->findOne(['id' => $id]);
        if (!$suggestion) {
            return response()->json(['message' => 'Suggestion not found'], 404);
        }
        return response()->json($suggestion);
    }

    public function update(int $id, UpdateSuggestionRequest $request): JsonResponse
    {
        $updated = $this->suggestionService->update($id, $request->validated());
        if (!$updated) {
            return response()->json(['message' => 'Suggestion not found or no changes made'], 404);
        }
        return response()->json(['message' => 'Suggestion updated successfully']);
    }
}

