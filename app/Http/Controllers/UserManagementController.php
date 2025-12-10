<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    public function index()
    {
        if (! auth()->user()->isSecretary()) {
            abort(403, 'Only Secretary can manage user accounts.');
        }
        $users = User::orderBy('role')->orderBy('name')->get();

        return view('settings.users', compact('users'));
    }

    public function store(Request $request)
    {
        if (! auth()->user()->isSecretary()) {
            abort(403, 'Only Secretary can create accounts.');
        }
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'role' => 'required|in:staff,calamity_head,secretary',
            'password' => ['required', 'string', 'min:10', 'confirmed', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).+$/'],
            'assigned_app' => 'nullable|in:profiling_only',
            'status' => 'nullable|in:active,deactivated',
        ]);

        // Default assigned app for staff
        $assigned = $validated['assigned_app'] ?? null;
        if ($validated['role'] === 'staff' && ! $assigned) {
            $assigned = 'profiling_only';
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => $validated['password'],
            'assigned_app' => $assigned,
            'status' => $validated['status'] ?? 'active',
        ]);

        return redirect()->route('settings.users.index')->with('success', 'User account created: '.$user->name);
    }

    public function updateStatus(Request $request, User $user)
    {
        if (! auth()->user()->isSecretary()) {
            abort(403, 'Only Secretary can update user status.');
        }
        $validated = $request->validate([
            'status' => 'required|in:active,deactivated',
        ]);
        $user->update(['status' => $validated['status']]);

        return redirect()->route('settings.users.index')->with('success', 'User status updated.');
    }

    public function updateAssignment(Request $request, User $user)
    {
        if (! auth()->user()->isSecretary()) {
            abort(403, 'Only Secretary can update user assignment.');
        }
        $validated = $request->validate([
            'assigned_app' => 'nullable|in:profiling_only',
        ]);
        $user->update(['assigned_app' => $validated['assigned_app'] ?? null]);

        return redirect()->route('settings.users.index')->with('success', 'User assignment updated.');
    }

    // MFA disabled
}
