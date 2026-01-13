<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuthService;
use App\Services\V5\UserService;
use App\Services\MailService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Laravel\Sanctum\PersonalAccessToken;
use Exception;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService,
        protected MailService $mailService,
        protected UserService $userService
    )
    {
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

            if (!$token) {
                return response()->json(['error' => 'Token is required'], 400);
            }

            // URL-decode the token in case it was URL-encoded
            $token = urldecode($token);

            // Find the token in Sanctum
            $accessToken = PersonalAccessToken::findToken($token);

            if (!$accessToken) {
                return response()->json(['error' => 'Invalid token'], 400);
            }

            // Check if token has expired
            if ($accessToken->expires_at && $accessToken->expires_at->isPast()) {
                $accessToken->delete();
                return response()->json(['error' => 'Token has expired. Please request a new verification email.'], 400);
            }

            // Check if token has the verify-email ability
            if (!in_array('verify-email', $accessToken->abilities ?? [])) {
                return response()->json(['error' => 'Invalid token type'], 400);
            }

            // Get the user from the token
            $user = $accessToken->tokenable;

            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }

            $this->userService->verifyUser($user->id);

            // Delete the verification token after use
            $accessToken->delete();

            return response()->json(['message' => 'Email verified successfully']);
        } catch (Exception $e) {
            return response()->json(['error' => 'Invalid token: ' . $e->getMessage()], 400);
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

        // Load relations including nested relations for user roles
        $user->load([
            'seasonSports.sport',
            'seasonSports.season',
            'roles',
            'userRoles.role',
        ]);

        return response()->json($user);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email'        => 'required|email',
            'new_password' => 'required|string|min:8',
            'token'        => 'nullable|string',
        ]);

        // If token is provided, verify it
        if ($request->has('token') && $request->input('token')) {
            try {
                $token = $request->input('token');
                // URL-decode the token in case it was URL-encoded
                $token = urldecode($token);
                
                $accessToken = PersonalAccessToken::findToken($token);

                if (!$accessToken) {
                    return response()->json(['error' => 'Invalid token'], 400);
                }

                // Check if token has expired
                if ($accessToken->expires_at && $accessToken->expires_at->isPast()) {
                    $accessToken->delete();
                    return response()->json(['error' => 'Token has expired. Please request a new password reset.'], 400);
                }

                // Check if token has the reset-password or create-password ability
                $validAbilities = ['reset-password', 'create-password'];
                $hasValidAbility = !empty(array_intersect($validAbilities, $accessToken->abilities ?? []));
                if (!$hasValidAbility) {
                    return response()->json(['error' => 'Invalid token type'], 400);
                }

                // Verify the email matches the token's user
                $user = $accessToken->tokenable;
                if (!$user || $user->email !== $request->input('email')) {
                    return response()->json(['error' => 'Token does not match email'], 400);
                }

                // Delete the token after use
                $accessToken->delete();
            } catch (Exception $e) {
                return response()->json(['error' => 'Invalid token: ' . $e->getMessage()], 400);
            }
        }

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
            'email'            => 'required|email',
            'current_password' => 'required|string',
            'new_password'     => 'required|string|min:8',
        ]);

        $result = $this->authService->changePassword($request->all());

        if (is_string($result)) {
            return response()->json(['error' => $result], 400);
        }

        return response()->json($result);
    }
}

