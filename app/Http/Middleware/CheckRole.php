<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (! auth()->check()) {
            abort(403, 'Unauthorized action.');
        }

        $userRole = auth()->user()->role;

        if (! in_array($userRole, $roles)) {
            \App\Models\AuditLog::logAction(
                'authorization_failed',
                'Middleware',
                auth()->id(),
                'Role check failed for route',
                ['required_roles' => $roles, 'user_role' => $userRole, 'path' => $request->path()],
                null
            );
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['message' => 'This action is unauthorized.'], 403);
            }
            abort(403, 'This action is unauthorized.');
        }

        return $next($request);
    }
}
