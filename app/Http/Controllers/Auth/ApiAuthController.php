<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\SendOtpNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Auth\Events\Registered;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class ApiAuthController extends Controller
{
    /**
     * Handle API registration request.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:15',
            'password' => 'required|min:8|confirmed',
            'address' => 'required|string|max:255',
        ]);

        try {
            // Create user account (email not verified yet)
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => $validated['password'], // Will be automatically hashed by the model's cast
                'address' => $validated['address'],
            ]);

            // Fire registered event
            event(new Registered($user));
            
            // Generate OTP code
            $otpCode = $user->generateOtp();

            // Send OTP via email
            try {
                $user->notify(new SendOtpNotification($otpCode));
            } catch (\Exception $e) {
                \Log::error('Failed to send OTP email: ' . $e->getMessage());
                // Continue even if email fails - user can request resend
            }

            // Do NOT log the user in - they need to verify OTP first
            // Return user info for verification page
            return response()->json([
                'message' => 'Registration successful! Please check your email for the verification code.',
                'user_id' => $user->id,
                'email' => $user->email,
                'requires_verification' => true,
            ], 201);
        } catch (\Exception $e) {
            \Log::error('Registration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Registration failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle API login request.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return response()->json([
                'message' => 'The provided credentials do not match our records.',
            ], 422);
        }

        $user = Auth::user();

        // Check if email is verified (skip for Google OAuth users)
        // For now, we'll allow login without email verification
        // If you want to enforce email verification, uncomment the code below
        /*
        if (!$user->hasVerifiedEmail() && !isset($user->google_id)) {
            Auth::logout();
            return response()->json([
                'message' => 'Please verify your email first.',
                'requires_verification' => true,
            ], 403);
        }
        */

        $request->session()->regenerate();

        // Generate Sanctum token for API access
        $token = $user->createToken('login-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful! Welcome back!',
            'user' => $user,
            'token' => $token,
        ]);
    }

    /**
     * Handle API logout request.
     */
    public function logout(Request $request)
    {
        // Revoke all tokens for this user (if any exist)
        if ($request->user()) {
            $request->user()->tokens()->delete();
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'You have been logged out successfully.',
        ]);
    }

    /**
     * Redirect to Google OAuth (for web routes, not API)
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google OAuth callback (for web routes, not API)
     */
    public function handleGoogleCallback()
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

                $message = 'Account created successfully! Welcome to Kape Na!';
            }

            // Generate Sanctum token for API access
            $token = $user->createToken('google-oauth-token')->plainTextToken;

            return response()->json([
                'message' => $message,
                'user' => $user,
                'token' => $token,
            ]);
        } catch (Exception $e) {
            // Log the error for debugging
            \Log::error('Google OAuth Error: ' . $e->getMessage());

            return response()->json([
                'message' => 'Unable to login with Google. Please try again or contact support.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Verify OTP code (API endpoint)
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp_code' => 'required|string|size:6',
            'user_id' => 'required|integer|exists:users,id',
        ]);

        $user = User::find($request->user_id);
        if (!$user) {
            return response()->json([
                'message' => 'User not found.',
            ], 404);
        }

        // Check if already verified
        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email already verified! Please login.',
            ], 422);
        }

        // Verify the OTP
        if ($user->verifyOtpCode($request->otp_code)) {
            // OTP is valid - log the user in
            Auth::login($user);

            // Generate Sanctum token
            $token = $user->createToken('verification-token')->plainTextToken;

            return response()->json([
                'message' => 'Email verified successfully! Welcome to Kape Na!',
                'user' => $user,
                'token' => $token,
            ]);
        }

        // OTP is invalid or expired
        return response()->json([
            'message' => 'Invalid or expired OTP code. Please try again or request a new code.',
        ], 422);
    }

    /**
     * Resend OTP code (API endpoint)
     */
    public function resendOtp(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
        ]);

        $user = User::find($request->user_id);
        if (!$user) {
            return response()->json([
                'message' => 'User not found.',
            ], 404);
        }

        // Check if already verified
        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email already verified! Please login.',
            ], 422);
        }

        // Generate new OTP code
        $otpCode = $user->generateOtp();

        // Send new OTP via email
        try {
            $user->notify(new SendOtpNotification($otpCode));
            
            return response()->json([
                'message' => 'A new OTP code has been sent to your email!',
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to resend OTP email: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Failed to send OTP email. Please try again later.',
            ], 500);
        }
    }
}


