<?php

namespace App\Http\Controllers\Api\V5;

use App\Http\Controllers\Controller;
use App\Http\Requests\V5\CreateRefereeRequest;
use App\Services\V5\RefereeService;
use Illuminate\Http\JsonResponse;

class RefereeController extends Controller
{
    protected RefereeService $refereeService;

    public function __construct(RefereeService $refereeService)
    {
        $this->refereeService = $refereeService;
    }

    public function create(CreateRefereeRequest $request): JsonResponse
    {
        $referee = $this->refereeService->createRef($request->validated());
        return response()->json($referee, 201);
    }

    public function getAllReferees(): JsonResponse
    {
        $result = $this->refereeService->findAllReferees(request()->all());
        return response()->json($result);
    }

    public function getRefereeById(int $id): JsonResponse
    {
        $referee = $this->refereeService->findOne(['id' => $id]);
        if (!$referee) {
            return response()->json(['error' => 'Referee not found'], 404);
        }
        return response()->json($referee);
    }

    public function destroy(int $id): JsonResponse
    {
        $result = $this->refereeService->delete($id);
        return response()->json(['success' => $result]);
    }
}

