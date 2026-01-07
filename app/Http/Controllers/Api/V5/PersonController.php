<?php

namespace App\Http\Controllers\Api\V5;

use App\Http\Controllers\Controller;
use App\Http\Requests\V5\CreatePersonRequest;
use App\Http\Requests\V5\UpdatePersonRequest;
use App\Http\Requests\V5\PlayerLicenseRequest;
use App\Services\V5\PersonService;
use Illuminate\Http\JsonResponse;

class PersonController extends Controller
{
    protected PersonService $personService;

    public function __construct(PersonService $personService)
    {
        $this->personService = $personService;
    }

    public function create(CreatePersonRequest $request): JsonResponse
    {
        $person = $this->personService->create($request->validated());
        return response()->json($person, 201);
    }

    public function getAllPersons(): JsonResponse
    {
        $orderBy = request('orderBy', 'id');
        $orderDirection = request('orderDirection', 'asc');
        $persons = $this->personService->findAll($orderBy, $orderDirection);
        return response()->json($persons);
    }

    public function updatePlayerLicense(PlayerLicenseRequest $request): JsonResponse
    {
        $result = $this->personService->savePlayerLicense($request->validated());
        return response()->json(['success' => $result]);
    }

    public function update(int $id, UpdatePersonRequest $request): JsonResponse
    {
        $person = $this->personService->updatePersonAndUserName($id, $request->validated());
        return response()->json($person);
    }
}

