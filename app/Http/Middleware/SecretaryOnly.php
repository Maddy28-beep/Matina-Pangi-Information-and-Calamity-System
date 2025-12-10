<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecretaryOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check() || ! auth()->user()->isSecretary()) {
            if (auth()->check() && auth()->user()->isStaff()) {
                $routeName = $request->route()?->getName();
                $params = $request->route()?->parameters() ?? [];
                if ($routeName === 'resident-transfers.index') {
                    return redirect()->route('staff.resident-transfers.index');
                }
                if ($routeName === 'resident-transfers.create') {
                    return redirect()->route('staff.resident-transfers.create');
                }
                if ($routeName === 'resident-transfers.show') {
                    $modelOrId = $params['residentTransfer'] ?? $params['resident_transfer'] ?? null;
                    if ($modelOrId) {
                        return redirect()->route('staff.resident-transfers.show', $modelOrId);
                    }
                }
            }
            $routeName = $request->route()?->getName();
            $message = $routeName === 'dashboard'
                ? 'Access Denied: This dashboard is restricted to Secretary accounts'
                : 'This action requires Secretary privileges.';

            AuditLog::logAction(
                'authorization_failed',
                'Middleware',
                null,
                $message,
                ['route' => $request->path(), 'method' => $request->method()],
                null
            );

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['message' => $message], 403);
            }

            abort(403, $message);
        }

        return $next($request);
    }
}
