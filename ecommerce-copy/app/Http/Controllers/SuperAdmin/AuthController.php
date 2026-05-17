<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\ActivityLog;

class AuthController extends Controller
{
    /**
     * Show super admin login form.
     */
    public function showLogin()
    {
        if (Auth::check() && Auth::user()->role === 'super_admin') {
            return redirect()->route('super-admin.dashboard');
        }
        
        return view('super-admin.auth.login');
    }

    /**
     * Handle super admin login.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            // Only allow super_admin role
            if ($user->role !== 'super_admin') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'You do not have super admin access.',
                ])->onlyInput('email');
            }

            // Check if user is active
            if ($user->status !== 'active') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Your account is not active. Please contact administrator.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();
            
            // Log super admin login activity
            ActivityLog::adminLog(
                'Super Admin logged in',
                $user,
                $user,
                ['email' => $user->email, 'role' => $user->role]
            );
            
            return redirect()->route('super-admin.dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Handle super admin logout.
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        
        // Log super admin logout activity
        if ($user) {
            ActivityLog::adminLog(
                'Super Admin logged out',
                $user,
                $user,
                ['email' => $user->email, 'role' => $user->role]
            );
        }
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('super-admin.login');
    }
}
