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
     * Usage example: middleware('role:Admin') or middleware('role:Admin,Super Admin')
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(403);
        }

        $allowedRoles = [];
        foreach ($roles as $role) {
            foreach (explode('|', $role) as $r) {
                foreach (explode(',', $r) as $subRole) {
                    $trimmed = trim($subRole);
                    if ($trimmed !== '') {
                        $allowedRoles[] = $trimmed;
                    }
                }
            }
        }

        if ($user->hasRole('Super Admin') || $user->hasRole($allowedRoles)) {
            return $next($request);
        }

        abort(403);
    }
}
