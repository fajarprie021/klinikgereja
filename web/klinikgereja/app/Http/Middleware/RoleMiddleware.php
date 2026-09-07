<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Usage example: middleware('role:Admin')
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(403);
        }

        // Ambil relasi role dari users.role_id
        $userRoleName = $user->role?->name;

        if ($userRoleName !== $role) {
            abort(403);
        }

        return $next($request);
    }
}
