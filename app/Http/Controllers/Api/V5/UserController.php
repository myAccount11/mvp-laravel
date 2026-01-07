<?php

namespace App\Http\Controllers\Api\V5;

use App\Http\Controllers\Controller;
use App\Http\Requests\V5\CreateUserRequest;
use App\Http\Requests\V5\UpdateUserRequest;
use App\Services\V5\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function create(CreateUserRequest $request): JsonResponse
    {
        $user = $this->userService->createUser($request->validated());
        return response()->json($user, 201);
    }

    public function createByAdmin(CreateUserRequest $request): JsonResponse
    {
        $user = $this->userService->createUserByAdmin($request->validated());
        return response()->json($user, 201);
    }

    public function index(Request $request): JsonResponse
    {
        $clubId = $request->query('club_id');
        $roleId = $request->query('role_id');

        if ($clubId && $roleId) {
            $result = $this->userService->clubManagerNames($roleId, $clubId);
            return response()->json($result);
        }

        $result = $this->userService->findAndCountAll($request->all());
        return response()->json($result);
    }

    public function show($id): JsonResponse
    {
        $user = $this->userService->findOne(['id' => $id]);
        
        if ($user) {
            $userData = $user->toArray();
            unset($userData['password']);
            return response()->json($userData);
        }

        return response()->json(['error' => 'User not found'], 404);
    }

    public function getTeamUser($id, Request $request): JsonResponse
    {
        $user = $this->userService->findTeamUser($id, $request->all());
        
        if ($user) {
            return response()->json($user);
        }

        return response()->json(['error' => 'User not found'], 404);
    }

    public function update($id, UpdateUserRequest $request): JsonResponse
    {
        if ($request->hasFile('picture')) {
            $file = $request->file('picture');
            
            // Validate file type
            $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            if (!in_array($file->getMimeType(), $allowedMimeTypes)) {
                return response()->json(['error' => 'Only .png, .jpeg and .jpg format allowed!'], 400);
            }

            // Validate file size (2MB)
            if ($file->getSize() > 2 * 1024 * 1024) {
                return response()->json(['error' => 'File size must be less than 2MB'], 400);
            }

            $random = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz0123456789'), 0, 10);
            $extension = $file->getClientOriginalExtension();
            $fileName = "user-{$id}-{$random}.{$extension}";
            
            $file->storeAs('uploads', $fileName, 'public');
            $request->merge(['picture' => $fileName]);
        }

        $result = $this->userService->updateUser(['id' => $id], $request->validated());
        return response()->json($result);
    }

    public function destroy($id): JsonResponse
    {
        $result = $this->userService->deleteUser($id);
        return response()->json($result);
    }

    public function deleteRole($id, Request $request): JsonResponse
    {
        $deleted = DB::table('user_roles')
            ->where('user_id', $id)
            ->where($request->all())
            ->delete();

        return response()->json(['deleted' => $deleted]);
    }
}

