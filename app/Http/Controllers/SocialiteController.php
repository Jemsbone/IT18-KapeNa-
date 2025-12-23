<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class SocialiteController extends Controller
{

    /**
     * Redirect to Google OAuth
     * This function is used to redirect the user to the Google OAuth page
     * @param NA
     * @return \Illuminate\Http\RedirectResponse
     */
    public function googleLogin()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google OAuth callback
     * This function handles the callback from Google OAuth and creates/updates user
     * @param NA
     * @return \Illuminate\Http\RedirectResponse
     */
    public function googleCallback()
    {
        try {
            // Get user information from Google
            $googleUser = Socialite::driver('google')->user();

            // Check if user already exists by email or Google ID
            $user = User::where('email', $googleUser->getEmail())
                        ->orWhere('google_id', $googleUser->getId())
                        ->first();

            if ($user) {
                // User exists - update their Google ID and avatar if not set
                if (!$user->google_id) {
                    $user->google_id = $googleUser->getId();
                }

                // Update avatar from Google (if column exists)
                if ($googleUser->getAvatar() && Schema::hasColumn('users', 'avatar')) {
                    $user->avatar = $googleUser->getAvatar();
                }

                // Mark email as verified since Google verified it
                if (!$user->email_verified_at) {
                    $user->email_verified_at = now();
                }

                $user->save();

                // Log the user in
                Auth::login($user);

                $message = 'Welcome back! Successfully logged in with Google.';
            } else {
                // Create new user account automatically
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => Schema::hasColumn('users', 'avatar') ? $googleUser->getAvatar() : null,
                    'email_verified_at' => now(), // Google emails are already verified
                    'phone' => null,
                    'address' => null,
                    'password' => bcrypt(uniqid()), // Random password (not used for Google login)
                ]);

                // Log the new user in
                Auth::login($user);

                $message = 'Account created successfully! Welcome!';
            }

            // Generate Sanctum token for API access (stored in session)
            $token = $user->createToken('google-oauth-token')->plainTextToken;
            
            // Store token in session for the frontend to retrieve
            session(['auth_token' => $token]);

            // Redirect to frontend with success message
            // The frontend URL should be configured in your .env file
            $frontendUrl = config('app.frontend_url', 'http://localhost:3000');
            
            return redirect()->to(
                $frontendUrl . '/auth/google/callback?success=1&message=' . urlencode($message)
            );
        } catch (Exception $e) {
            // Log the error for debugging
            \Log::error('Google OAuth Error: ' . $e->getMessage());

            // Redirect to frontend with error message
            $frontendUrl = config('app.frontend_url', 'http://localhost:3000');
            
            return redirect()->to(
                $frontendUrl . '/auth/google/callback?error=' . urlencode('Unable to login with Google. Please try again.')
            );
        }
    }
}
