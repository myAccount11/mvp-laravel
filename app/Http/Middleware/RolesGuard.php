<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RolesGuard
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        if (empty($roles)) {
            return $next($request);
        }

        $userRoles = $user->roles->pluck('description')->toArray();
        
        $hasRole = false;
        foreach ($roles as $role) {
            if (in_array($role, $userRoles)) {
                $hasRole = true;
                break;
            }
        }

        if (!$hasRole) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        return $next($request);
    }
}

