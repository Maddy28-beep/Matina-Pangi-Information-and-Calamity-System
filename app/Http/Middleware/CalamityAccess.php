<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CalamityAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        $routeName = $request->route()?->getName();

        if (! $user) {
            \App\Models\AuditLog::logAction('authorization_failed', 'Middleware', null, 'Unauthenticated access to Calamity subsystem', ['route' => $request->path()]);
            abort(403, 'Calamity Management access required.');
        }

        $isCalamityHead = method_exists($user, 'isCalamityHead') && $user->isCalamityHead();
        $isSecretary = method_exists($user, 'isSecretary') && $user->isSecretary();
        $secretaryEnabled = (bool) config('app.calamity_secretary_enabled');

        // Calamity Dashboard is exclusive to Calamity Head
        if ($routeName === 'calamities.dashboard') {
            if (! $isCalamityHead) {
                $message = 'Access Denied: This dashboard is restricted to Calamity Head accounts';
                \App\Models\AuditLog::logAction('authorization_failed', 'Middleware', $user->id, $message, ['route' => $request->path(), 'method' => $request->method()]);
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['message' => $message], 403);
                }
                abort(403, $message);
            }
        } else {
            // Allow Calamity Head and (if enabled) Secretary to access all other Calamity subsystem routes
            if (! ($isCalamityHead || ($isSecretary && $secretaryEnabled))) {
                $message = 'Calamity Management access required.';
                \App\Models\AuditLog::logAction('authorization_failed', 'Middleware', $user->id, $message, ['route' => $request->path(), 'method' => $request->method()]);
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['message' => $message], 403);
                }
                abort(403, $message);
            }
        }

        \App\Models\AuditLog::logAction('calamity_access_granted', 'Middleware', $user->id, 'Calamity access granted', ['path' => $request->path(), 'route' => $routeName]);

        return $next($request);
    }
}
