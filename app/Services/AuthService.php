<?php

namespace App\Services;

use App\Services\V5\UserService;
use App\Services\MailService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tymon\JWTAuth\Facades\JWTAuth;
use Exception;

class AuthService
{
    protected $userService;
    protected $mailService;

    public function __construct(UserService $userService, MailService $mailService)
    {
        $this->userService = $userService;
        $this->mailService = $mailService;
    }

    public function validateUser($email, $password)
    {
        $user = $this->userService->findOne(['email' => strtolower($email)]);
        
        if ($user && Hash::check($password, $user->password)) {
            return $user;
        }
        
        return null;
    }

    public function login($email, $password)
    {
        $user = $this->validateUser($email, $password);
        
        if ($user) {
            // Load relations
            $user->load(['seasonSports', 'roles', 'userRoles']);
            
            $customClaims = ['exp' => now()->addYear()->timestamp];
        $token = JWTAuth::customClaims($customClaims)->fromUser($user);
            
            $userData = $user->toArray();
            unset($userData['password']);
            
            return [
                'accessToken' => $token,
                ...$userData,
                'userRoles' => $user->userRoles,
            ];
        }
        
        return null;
    }

    public function registration(array $data)
    {
        // Handle old user registration if in production
        if (config('app.env') === 'prod') {
            // Old user service logic would go here
        }
        
        $user = $this->userService->createUser($data);
        
        if ($user && $user->password) {
            unset($user->password);
        }
        
        $customClaims = ['exp' => now()->addMinutes(30)->timestamp];
        $token = JWTAuth::customClaims($customClaims)->fromUser($user);
        $this->mailService->sendMailVerifyEmail($user->email, $user->name, $token);
        
        return $user;
    }

    public function googleSignIn($accessToken)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$accessToken}",
            ])->get('https://www.googleapis.com/oauth2/v3/userinfo');
            
            if ($response->successful() && $response->json()) {
                $googleData = $response->json();
                
                $user = $this->userService->findOne(['email' => $googleData['email']]);
                
                if (!$user) {
                    // Handle old user registration if in production
                    if (config('app.env') === 'prod') {
                        // Old user service logic would go here
                    }
                    
                    $user = $this->userService->userRepository->create([
                        'email' => $googleData['email'],
                        'name' => $googleData['name'],
                        'google_account_id' => $googleData['sub'],
                        'is_verified' => true,
                    ]);
                    
                    $user->load('roles');
                }
                
                if ($user) {
                    // Load relations
                    $user->load(['seasonSports', 'roles', 'userRoles']);
                    
                    $userData = $user->toArray();
                    unset($userData['password']);
                    
                    return [
                        'accessToken' => JWTAuth::fromUser($user),
                        ...$userData,
                        'userRoles' => $user->userRoles,
                    ];
                }
            }
        } catch (Exception $e) {
            throw new Exception('Google sign in failed: ' . $e->getMessage());
        }
    }

    public function validateEmail(array $data)
    {
        $user = $this->userService->findOne(['email' => $data['email']]);
        
        if ($user) {
            if ($user->disable_emails) {
                return 'Your account is disabled. Please contact Support.';
            } elseif (!$user->password && !$user->google_account_id) {
                $customClaims = ['exp' => now()->addMinutes(30)->timestamp];
        $token = JWTAuth::customClaims($customClaims)->fromUser($user);
                $this->mailService->sendMailCreatePassword($user->email, $user->name, $token);
                return 'Sent email.';
            } elseif ($user->google_account_id) {
                throw new Exception('Try signing in with your Google account.');
            } elseif (!$user->is_verified) {
                $customClaims = ['exp' => now()->addMinutes(30)->timestamp];
        $token = JWTAuth::customClaims($customClaims)->fromUser($user);
                $this->mailService->sendMailVerifyEmail($user->email, $user->name, $token);
                return 'A new token has been sent because the previous one was expired.';
            }
            
            return ['user' => $user];
        }
        
        return ['user' => null];
    }

    public function forgetPassword(array $data)
    {
        $response = $this->validateEmail($data);
        
        if (is_array($response) && isset($response['user']) && $response['user']) {
            $user = $response['user'];
            $customClaims = ['exp' => now()->addMinutes(30)->timestamp];
        $token = JWTAuth::customClaims($customClaims)->fromUser($user);
            $this->mailService->sendMailResetPassword($user->email, $user->name, $token);
            return 'Sent mail';
        }
        
        return $response;
    }

    public function resetPassword(array $data)
    {
        $hashedPassword = Hash::make($data['new_password']);
        
        return $this->userService->updateUser(
            ['email' => $data['email']],
            ['password' => $hashedPassword, 'is_verified' => true]
        );
    }

    public function changePassword(array $data)
    {
        $user = $this->userService->findOne(['email' => $data['email']]);
        
        if (!$user) {
            return 'User not found';
        }
        
        if (!Hash::check($data['current_password'], $user->password)) {
            return 'Incorrect current password';
        }
        
        $hashedPassword = Hash::make($data['new_password']);
        
        return $this->userService->updateUser(
            ['email' => $data['email']],
            ['password' => $hashedPassword]
        );
    }
}

