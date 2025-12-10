<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLogin()
    {
        if (Auth::check()) {
            $user = auth()->user();
            if ($user && $user->role === 'calamity_head') {
                return redirect()->route('calamities.dashboard');
            }

            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Handle login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            AuditLog::logAction(
                'login',
                'User',
                auth()->id(),
                auth()->user()->name.' logged in'
            );

            $user = auth()->user();
            if (($user->status ?? 'active') === 'deactivated') {
                $app = $user->assigned_app;
                if ($app && \Illuminate\Support\Facades\Route::has('apps.'.$app)) {
                    return redirect()->route('apps.'.$app);
                }
                Auth::logout();

                return redirect()->route('login')->withErrors([
                    'email' => 'Your account is deactivated. Contact secretary for access.',
                ]);
            }

            if ($user->role === 'calamity_head') {
                // Avoid redirecting to unauthorized intended URLs (e.g., /dashboard)
                $intended = session()->get('url.intended');
                if ($intended) {
                    session()->forget('url.intended');
                }

                return redirect()->route('calamities.dashboard');
            }
            if ($user->role === 'staff' || $user->role === 'secretary') {
                return redirect()->intended(route('dashboard'));
            }

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        AuditLog::logAction(
            'logout',
            'User',
            auth()->id(),
            auth()->user()->name.' logged out'
        );

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landing');
    }
}
