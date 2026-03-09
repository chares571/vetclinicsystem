<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $roles): Response
    {
        $allowedRoles = array_filter(array_map('trim', explode('|', $roles)));
        $user = $request->user();

        if (! $user instanceof User || ! $user->hasRole(...$allowedRoles)) {
            abort(403);
        }

        return $next($request);
    }
}
