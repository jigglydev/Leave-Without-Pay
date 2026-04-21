<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    // ─── Login ────────────────────────────────────────────────────────────────

    public function showLogin()
    {
        return redirect('/');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            // Block employees whose account has not been confirmed by the admin yet
            if ($user->isEmployee() && $user->isPending()) {
                Auth::logout();
                $request->session()->invalidate();
                return back()->withErrors([
                    'email' => 'Your account is pending admin approval. Please wait for confirmation before logging in.',
                ], 'login')->onlyInput('email');
            }

            // Block employees whose account has been rejected
            if ($user->isEmployee() && $user->isRejected()) {
                Auth::logout();
                $request->session()->invalidate();
                return back()->withErrors([
                    'email' => 'Your account registration has been rejected. Please contact the administrator.',
                ], 'login')->onlyInput('email');
            }

            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'These credentials do not match our records.',
        ], 'login')->onlyInput('email');
    }

    // ─── Register ─────────────────────────────────────────────────────────────

    public function showRegister()
    {
        return redirect('/');
    }

    public function register(Request $request)
    {
        $validated = $request->validateWithBag('register', [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = User::create([
            'name'         => $validated['name'],
            'email'        => $validated['email'],
            'password'     => Hash::make($validated['password']),
            'role'         => 'employee',
            'is_confirmed' => null,  // pending admin approval
        ]);

        \App\Models\ActivityLog::create([
            'user_id'     => null,
            'action'      => 'user_registered',
            'description' => "New employee account registered: {$validated['name']} ({$validated['email']})",
            'meta'        => ['name' => $validated['name'], 'email' => $validated['email'], 'new_user_id' => $user->id],
        ]);

        return redirect('/')
            ->with('register_success', 'Account created! Awaiting admin approval before you can log in.');
    }

    // ─── Logout ───────────────────────────────────────────────────────────────

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
