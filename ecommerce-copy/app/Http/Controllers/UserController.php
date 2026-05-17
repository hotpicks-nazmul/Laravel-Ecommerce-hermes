<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Address;
use App\Models\Setting;
use App\Models\UserNotificationPreference;
use App\Models\ActivityLog;
use Laravel\Socialite\Facades\Socialite;
use App\Helpers\ImageHelper;

class UserController extends Controller
{
    /**
     * Show login form.
     */
    public function showLogin()
    {
        return view('themes.general.auth.login');
    }

    /**
     * Handle login.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            // Log login activity
            ActivityLog::customerLog(
                'User logged in',
                Auth::user(),
                Auth::user(),
                ['login_method' => 'email']
            );
            
            // Redirect based on user role
            if (Auth::user()->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'));
            }
            
            return redirect()->intended(route('home'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    /**
     * Show registration form.
     */
    public function showRegister()
    {
        return view('themes.general.auth.register');
    }

    /**
     * Handle registration.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
        ]);

        Auth::login($user);
        
        // Log registration activity
        ActivityLog::customerLog(
            'User registered',
            $user,
            $user,
            ['email' => $user->email]
        );

        return redirect()->route('home');
    }

    /**
     * Handle logout.
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        
        // Log logout activity before logging out
        if ($user) {
            ActivityLog::customerLog(
                'User logged out',
                $user,
                $user,
                ['email' => $user->email]
            );
        }
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }

    /**
     * Show forgot password form.
     */
    public function showForgotPassword()
    {
        return view('themes.general.auth.forgot-password');
    }

    /**
     * Send password reset link.
     */
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    /**
     * Show reset password form.
     */
    public function showResetPassword($token)
    {
        return view('themes.general.auth.reset-password', ['token' => $token]);
    }

    /**
     * Reset password.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }

    /**
     * Redirect to social provider.
     */
    public function redirectToProvider($provider)
    {
        // Check if provider is enabled
        $settings = Setting::where('group', 'social_login')->pluck('value', 'key');
        $enabledKey = "{$provider}_enabled";
        
        if (($settings[$enabledKey] ?? '0') !== '1') {
            return redirect()->route('login')->with('error', ucfirst($provider) . ' login is not enabled.');
        }
        
        // Configure the provider with credentials from settings
        config([
            "services.{$provider}.client_id" => $settings["{$provider}_client_id"] ?? '',
            "services.{$provider}.client_secret" => $settings["{$provider}_client_secret"] ?? '',
            "services.{$provider}.redirect" => url("/auth/{$provider}/callback"),
        ]);
        
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Handle social provider callback.
     */
    public function handleProviderCallback($provider)
    {
        // Check if provider is enabled
        $settings = Setting::where('group', 'social_login')->pluck('value', 'key');
        $enabledKey = "{$provider}_enabled";
        
        if (($settings[$enabledKey] ?? '0') !== '1') {
            return redirect()->route('login')->with('error', ucfirst($provider) . ' login is not enabled.');
        }
        
        // Configure the provider with credentials from settings
        config([
            "services.{$provider}.client_id" => $settings["{$provider}_client_id"] ?? '',
            "services.{$provider}.client_secret" => $settings["{$provider}_client_secret"] ?? '',
            "services.{$provider}.redirect" => url("/login/{$provider}/callback"),
        ]);
        
        try {
            $socialUser = Socialite::driver($provider)->user();
            
            // Check if user already exists with this provider
            $user = User::where('provider', $provider)
                ->where('provider_id', $socialUser->getId())
                ->first();
            
            if (!$user) {
                // Check if user exists with this email
                $user = User::where('email', $socialUser->getEmail())->first();
                
                if ($user) {
                    // Update existing user with provider info
                    $user->update([
                        'provider' => $provider,
                        'provider_id' => $socialUser->getId(),
                        'avatar' => $socialUser->getAvatar() ?? $user->avatar,
                    ]);
                } else {
                    // Create new user
                    $user = User::create([
                        'name' => $socialUser->getName() ?? explode('@', $socialUser->getEmail())[0],
                        'email' => $socialUser->getEmail(),
                        'password' => Hash::make(Str::random(16)),
                        'provider' => $provider,
                        'provider_id' => $socialUser->getId(),
                        'avatar' => $socialUser->getAvatar(),
                        'email_verified_at' => now(), // Social login users are pre-verified
                    ]);
                }
            }
            
            Auth::login($user);
            
            return redirect()->intended(route('home'));
        } catch (\Exception $e) {
            \Log::error("Social login error ({$provider}): " . $e->getMessage());
            return redirect()->route('login')->with('error', 'Authentication failed. Please try again.');
        }
    }

    /**
     * Display user dashboard.
     */
    public function dashboard()
    {
        $user = auth()->user();
        $recentOrders = $user->orders()->latest()->take(5)->get();
        
        $totalOrders = $user->orders()->count();
        $totalSpent = $user->orders()->where('payment_status', 'paid')->sum('total');
        $wishlistCount = $user->wishlist()->count();
        
        // Get cart count
        $cart = \App\Models\Cart::where('session_id', session()->getId())->first();
        $cartCount = $cart ? $cart->getItemCount() : 0;
        
        return view('themes.general.dashboard.index', compact(
            'user',
            'recentOrders',
            'totalOrders',
            'totalSpent',
            'wishlistCount',
            'cartCount'
        ));
    }

    /**
     * Display user profile.
     */
    public function profile()
    {
        $user = auth()->user();
        return view('themes.general.dashboard.profile', compact('user'));
    }

    /**
     * Update user profile.
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|max:2048',
            'current_password' => 'nullable|required_with:password',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $data = $request->only(['name', 'email', 'phone']);
        
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar) {
                ImageHelper::deleteImage($user->avatar);
            }
            // Upload avatar with WebP conversion
            if (ImageHelper::isValidImage($request->file('avatar'))) {
                $imageResult = ImageHelper::processImage(
                    $request->file('avatar'),
                    'avatars',        // directory
                    300,              // max width (user avatar)
                    100,              // thumbnail width
                    90                // quality (higher for avatars)
                );
                $data['avatar'] = $imageResult['path'];
            }
        }

        // Handle password change
        if ($request->filled('password')) {
            if (!\Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.']);
            }
            $data['password'] = \Hash::make($request->password);
        }

        $user->update($data);

        return back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Update user password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Password updated successfully.');
    }

    /**
     * Display user addresses.
     */
    public function addresses()
    {
        $addresses = auth()->user()->addresses;
        return view('themes.general.dashboard.addresses', compact('addresses'));
    }

    /**
     * Store new address.
     */
    public function storeAddress(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postcode' => 'required|string|max:20',
            'is_default' => 'boolean',
        ]);

        $address = auth()->user()->addresses()->create($request->all());

        if ($request->is_default) {
            auth()->user()->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
        }

        return back()->with('success', 'Address added successfully.');
    }

    /**
     * Update address.
     */
    public function updateAddress(Request $request, Address $address)
    {
        if ($address->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postcode' => 'required|string|max:20',
            'is_default' => 'boolean',
        ]);

        $address->update($request->all());

        if ($request->is_default) {
            auth()->user()->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
        }

        return back()->with('success', 'Address updated successfully.');
    }

    /**
     * Delete address.
     */
    public function deleteAddress(Address $address)
    {
        if ($address->user_id !== auth()->id()) {
            abort(403);
        }

        $address->delete();

        return back()->with('success', 'Address deleted successfully.');
    }

    /**
     * Display user notification settings.
     */
    public function notifications()
    {
        $user = auth()->user();
        $defaultKeys = UserNotificationPreference::getDefaultKeys();
        
        // Get user's preferences
        $preferences = [];
        foreach ($defaultKeys as $type => $keys) {
            foreach ($keys as $key => $label) {
                $pref = UserNotificationPreference::getPreference($user->id, $type, $key);
                $preferences[$type][$key] = $pref->enabled;
            }
        }
        
        // Get global notification settings (to check what's enabled by admin)
        $globalSettings = Setting::where('group', 'notifications')->pluck('value', 'key');
        
        return view('themes.general.dashboard.notifications', compact('user', 'preferences', 'globalSettings', 'defaultKeys'));
    }

    /**
     * Update user notification settings.
     */
    public function updateNotifications(Request $request)
    {
        $user = auth()->user();
        $defaultKeys = UserNotificationPreference::getDefaultKeys();
        
        // Update email notifications
        if (isset($defaultKeys['email'])) {
            foreach ($defaultKeys['email'] as $key => $label) {
                $enabled = $request->has("email_{$key}") ? true : false;
                UserNotificationPreference::updatePreference($user->id, 'email', $key, $enabled);
            }
        }
        
        // Update SMS notifications
        if (isset($defaultKeys['sms'])) {
            foreach ($defaultKeys['sms'] as $key => $label) {
                $enabled = $request->has("sms_{$key}") ? true : false;
                UserNotificationPreference::updatePreference($user->id, 'sms', $key, $enabled);
            }
        }
        
        // Update push notifications
        if (isset($defaultKeys['push'])) {
            foreach ($defaultKeys['push'] as $key => $label) {
                $enabled = $request->has("push_{$key}") ? true : false;
                UserNotificationPreference::updatePreference($user->id, 'push', $key, $enabled);
            }
        }

        return back()->with('success', 'Notification preferences updated successfully.');
    }

    /**
     * Display my data export page.
     */
    public function myData()
    {
        $user = auth()->user();
        return view('themes.general.dashboard.my-data', compact('user'));
    }

    /**
     * Export user data.
     */
    public function exportMyData(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'export_type' => 'required|in:orders,wishlist,addresses,all',
        ]);
        
        $exportType = $request->export_type;
        
        // Generate filename
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "{$user->name}_data_{$timestamp}.json";
        
        $data = [];
        
        // Export user profile
        $data['profile'] = [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'created_at' => $user->created_at,
        ];
        
        // Export based on type
        if ($exportType === 'orders' || $exportType === 'all') {
            $data['orders'] = $user->orders()->get()->toArray();
        }
        
        if ($exportType === 'wishlist' || $exportType === 'all') {
            $data['wishlist'] = $user->wishlist()->with('product')->get()->toArray();
        }
        
        if ($exportType === 'addresses' || $exportType === 'all') {
            $data['addresses'] = $user->addresses()->get()->toArray();
        }
        
        if ($exportType === 'all') {
            $data['notifications'] = UserNotificationPreference::where('user_id', $user->id)->get()->toArray();
        }
        
        $data['exported_at'] = now()->toIso8601String();
        $data['export_type'] = $exportType;
        
        return response()->streamDownload(function () use ($data) {
            echo json_encode($data, JSON_PRETTY_PRINT);
        }, $filename, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
