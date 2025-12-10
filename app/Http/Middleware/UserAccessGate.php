<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserAccessGate
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();
        $routeName = $request->route()?->getName();

        if ($user && $user->status === 'deactivated') {
            $assigned = $user->assigned_app;

            if ($assigned && $routeName) {
                $allowedRoute = 'apps.'.$assigned;
                if ($routeName === $allowedRoute) {
                    return $next($request);
                }
            }

            abort(403, 'Your account is deactivated. Access limited to assigned app.');
        }

        return $next($request);
    }
}
