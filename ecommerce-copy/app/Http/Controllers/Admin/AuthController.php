<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\ActivityLog;

class AuthController extends Controller
{
    /**
     * Show admin login form.
     */
    public function showLogin()
    {
        if (Auth::check() && in_array(Auth::user()->role, ['admin', 'staff'])) {
            return redirect()->route(Auth::user()->getFirstAllowedRoute());
        }
        
        return view('admin.auth.login');
    }

    /**
     * Handle admin login.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            // Only allow admin role (not super_admin or staff)
            if (!in_array($user->role, ['admin', 'super_admin'])) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'You do not have admin access.',
                ])->onlyInput('email');
            }

            // Check if admin user is active
            if (in_array($user->role, ['admin']) && $user->status !== 'active') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Your account is not active. Please contact administrator.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();
            
            // Log admin/staff login activity
            ActivityLog::adminLog(
                'Admin user logged in',
                $user,
                $user,
                ['email' => $user->email, 'role' => $user->role]
            );
            
            return redirect()->intended(route(Auth::user()->getFirstAllowedRoute()));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Handle admin logout.
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        
        // Log admin logout activity
        if ($user) {
            ActivityLog::adminLog(
                'Admin user logged out',
                $user,
                $user,
                ['email' => $user->email, 'role' => $user->role]
            );
        }
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('admin.login');
    }
}
