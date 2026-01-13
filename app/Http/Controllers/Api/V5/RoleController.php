<?php

namespace App\Http\Controllers\Api\V5;

use App\Http\Controllers\Controller;
use App\Http\Requests\V5\CreateRoleRequest;
use App\Http\Requests\V5\AssignUserRoleRequest;
use App\Services\V5\RoleService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RoleController extends Controller
{
    protected $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    public function create(CreateRoleRequest $request): JsonResponse
    {
        $role = $this->roleService->createRole($request->validated());
        return response()->json($role, 201);
    }

    public function getByValue($value): JsonResponse
    {
        $role = $this->roleService->getRoleByValue($value);
        return response()->json($role);
    }

    public function assignUserRole($roleId, $userId, AssignUserRoleRequest $request): JsonResponse
    {
        $user = auth()->user();
        $this->roleService->addUserRole(
            $user,
            $userId,
            $roleId,
            $request->input('season_sport_id'),
            $user->id,
            $request->input('team_id'),
            $request->input('club_id')
        );
        return response()->json(['message' => 'Role assigned']);
    }

    public function detachUserRoles($userId, Request $request): JsonResponse
    {
        $user = $request->user();
        $userRoles = $request->input('user_roles');
        if (!is_array($userRoles)) {
            $userRoles = [$userRoles];
        }
        $this->roleService->detachUserRole($user, $userId, $userRoles);
        return response()->json(['message' => 'Roles detached']);
    }

    public function approveUserRoles($userId, Request $request): JsonResponse
    {
        $user = $request->user();
        $userRoles = $request->input('user_roles');
        if (!is_array($userRoles)) {
            $userRoles = [$userRoles];
        }
        $this->roleService->approveUserRole($user, $userId, $userRoles);
        return response()->json(['message' => 'Roles approved']);
    }

    public function getAll(Request $request): JsonResponse
    {
        $roles = $this->roleService->findAll($request->all());
        return response()->json($roles);
    }
}

