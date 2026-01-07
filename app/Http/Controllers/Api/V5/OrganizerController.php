<?php

namespace App\Http\Controllers\Api\V5;

use App\Http\Controllers\Controller;
use App\Http\Requests\V5\CreateOrganizerRequest;
use App\Http\Requests\V5\UpdateOrganizerRequest;
use App\Services\V5\OrganizerService;
use Illuminate\Http\JsonResponse;

class OrganizerController extends Controller
{
    protected OrganizerService $organizerService;

    public function __construct(OrganizerService $organizerService)
    {
        $this->organizerService = $organizerService;
    }

    public function create(CreateOrganizerRequest $request): JsonResponse
    {
        $organizer = $this->organizerService->create($request->validated());
        return response()->json($organizer, 201);
    }

    public function getAll(): JsonResponse
    {
        $orderBy = request('orderBy', 'id');
        $orderDirection = request('orderDirection', 'asc');
        $organizers = $this->organizerService->findAll($orderBy, $orderDirection);
        return response()->json($organizers);
    }

    public function getById(int $id): JsonResponse
    {
        $organizer = $this->organizerService->findOne(['id' => $id]);
        if (!$organizer) {
            return response()->json(['error' => 'Organizer not found'], 404);
        }
        return response()->json($organizer);
    }

    public function update(int $id, UpdateOrganizerRequest $request): JsonResponse
    {
        $result = $this->organizerService->update($id, $request->validated());
        return response()->json(['success' => $result]);
    }

    public function destroy(int $id): JsonResponse
    {
        $result = $this->organizerService->delete($id);
        return response()->json(['success' => $result]);
    }
}

