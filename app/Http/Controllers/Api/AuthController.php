<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuthService;
use App\Services\V5\UserService;
use App\Services\MailService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;
use Exception;

class AuthController extends Controller
{
    protected $authService;
    protected $mailService;
    protected $userService;

    public function __construct(
        AuthService $authService,
        MailService $mailService,
        UserService $userService
    ) {
        $this->authService = $authService;
        $this->mailService = $mailService;
        $this->userService = $userService;
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login(
            $request->input('email'),
            $request->input('password')
        );

        if ($result) {
            return response()->json($result);
        }

        return response()->json(['error' => 'Invalid credentials'], 401);
    }

    public function validateEmail(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        try {
            $result = $this->authService->validateEmail($request->all());
            return response()->json($result);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    public function verifyEmail(Request $request): JsonResponse
    {
        try {
            $token = $request->query('token');
            $payload = JWTAuth::setToken($token)->getPayload();
            $userId = $payload->get('sub');
            
            $this->userService->verifyUser($userId);
            return response()->json(['message' => 'Email verified successfully']);
        } catch (Exception $e) {
            return response()->json(['error' => 'Invalid token'], 400);
        }
    }

    public function googleSignIn(Request $request): JsonResponse
    {
        $request->validate([
            'access_token' => 'required|string',
        ]);

        try {
            $result = $this->authService->googleSignIn($request->input('access_token'));
            return response()->json($result);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function register(Request $request): JsonResponse
    {
        // Validation will be handled by CreateUserRequest if needed
        $user = $this->authService->registration($request->all());
        return response()->json($user, 201);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        
        // Load relations
        $user->load(['seasonSports', 'roles', 'userRoles']);
        
        return response()->json($user);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'new_password' => 'required|string|min:8',
        ]);

        $result = $this->authService->resetPassword($request->all());
        return response()->json($result);
    }

    public function forgetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $result = $this->authService->forgetPassword($request->all());
        return response()->json(['message' => $result]);
    }

    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8',
        ]);

        $result = $this->authService->changePassword($request->all());
        
        if (is_string($result)) {
            return response()->json(['error' => $result], 400);
        }

        return response()->json($result);
    }
}

