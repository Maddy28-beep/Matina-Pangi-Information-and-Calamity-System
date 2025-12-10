<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;

class SecurityAuditController extends Controller
{
    public function index()
    {
        if (! auth()->user()->isSecretary()) {
            abort(403, 'Only Secretary can view security audits.');
        }

        $calamityHeadsWithoutMfa = User::where('role', 'calamity_head')->where(function ($q) {
            $q->whereNull('mfa_enabled')->orWhere('mfa_enabled', false);
        })->get(['id', 'name', 'email']);

        $recentAccessLogs = AuditLog::whereIn('action', [
            'mfa_required', 'mfa_expired', 'mfa_failed', 'mfa_passed', 'calamity_access_granted',
        ])->latest()->limit(50)->get();

        return view('security.audit', compact('calamityHeadsWithoutMfa', 'recentAccessLogs'));
    }
}
