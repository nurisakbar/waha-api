<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ReferralSetting;
use App\Models\User;
use App\Models\UserQuota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Check if user exists by email
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // User exists, log them in
                // Check if user has google_id, if not update it
                if (!$user->google_id) {
                    $user->google_id = $googleUser->getId();
                    if (!$user->avatar && $googleUser->getAvatar()) {
                        $user->avatar = $googleUser->getAvatar();
                    }
                    $user->save();
                }

                Auth::login($user, true);
                $user->update(['last_login_at' => now()]);

                return redirect()->intended('/home');
            } else {
                // New user, create account
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'password' => Hash::make(Str::random(32)), // Random password since they use Google
                    'email_verified_at' => now(), // Google email is already verified
                ]);

                Auth::login($user, true);
                $user->update(['last_login_at' => now()]);

                // Check if phone is required (user doesn't have phone)
                if (!$user->phone) {
                    // Store user in session temporarily to ask for phone number
                    session(['google_new_user_requires_phone' => true]);
                    // Redirect to phone input page
                    return redirect()->route('auth.google.phone')->with('info', 'Silakan lengkapi nomor HP Anda untuk melanjutkan.');
                }

                return redirect()->intended('/home');
            }
        } catch (\Exception $e) {
            Log::error('Google OAuth error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Terjadi kesalahan saat login dengan Google. Silakan coba lagi.');
        }
    }

    /**
     * Show phone number input form for new Google users
     */
    public function showPhoneInput()
    {
        if (!session('google_new_user_requires_phone') || !Auth::check()) {
            return redirect()->route('home');
        }

        return view('auth.google-phone');
    }

    /**
     * Save phone number for new Google user
     */
    public function savePhone(Request $request)
    {
        if (!session('google_new_user_requires_phone') || !Auth::check()) {
            return redirect()->route('home');
        }

        $request->validate([
            'phone' => ['required', 'string', 'max:20', 'unique:users,phone'],
        ], [
            'phone.required' => 'Nomor HP harus diisi.',
            'phone.unique' => 'Nomor HP sudah terdaftar.',
        ]);

        $user = Auth::user();
        $user->phone = $request->phone;
        $user->save();

        // Clear session flags
        session()->forget(['google_new_user_id', 'google_new_user_requires_phone']);

        return redirect()->route('home')->with('success', 'Nomor HP berhasil ditambahkan.');
    }
}

