<?php

namespace App\Http\Controllers\adminAuth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\coffee_shop_admin;

class AdminAuthController extends Controller
{
    /**
     * Show admin registration form
     */
    public function showRegistrationForm()
    {
        // Redirect if already logged in as admin
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('auth.admin_register');
    }

    /**
     * Handle admin registration
     */
    public function register(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|unique:coffee_shop_admin,admin_email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Create the admin account
        $admin = coffee_shop_admin::create([
            'admin_name' => $request->admin_name,
            'admin_email' => $request->admin_email,
            'admin_password' => Hash::make($request->password),
        ]);

        // Log the admin in (but not verified yet)
        Auth::guard('admin')->login($admin);
        $request->session()->regenerate();

        // Generate and send OTP for registration verification
        try {
            \Log::info('Generating OTP for new admin registration', [
                'admin_id' => $admin->admin_id,
                'admin_email' => $admin->admin_email
            ]);
            
            // Generate OTP code
            $otpCode = $admin->generateOtpCode();
            
            \Log::info('OTP code generated for registration', [
                'admin_id' => $admin->admin_id,
                'otp_code' => $otpCode,
                'expires_at' => $admin->otp_expires_at
            ]);
            
            // Send OTP email
            $admin->notify(new \App\Notifications\AdminOtpVerificationNotification($otpCode));
            
            \Log::info('Admin registration OTP sent successfully', [
                'admin_id' => $admin->admin_id,
                'admin_email' => $admin->admin_email
            ]);
            
            // Redirect to verification page
            return redirect()->route('admin.verification.notice')
                ->with('success', 'Registration successful! Please check your email for the verification code.');
        } catch (\Exception $e) {
            \Log::error('Failed to send admin registration OTP', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'admin_email' => $admin->admin_email,
                'admin_id' => $admin->admin_id
            ]);
            
            return redirect()->route('admin.verification.notice')
                ->with('warning', 'Registration successful, but we had trouble sending the verification email. Please try resending the code.');
        }
    }

    /**
     * Show admin login form
     */
    public function showLoginForm()
    {
        // Redirect if already logged in as admin
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('auth.admin_login');
    }

    /**
     * Handle admin login request
     */
    public function login(Request $request)
    {
        // Validate the incoming request
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Find the admin by email
        $admin = coffee_shop_admin::where('admin_email', $credentials['email'])->first();

        // Check if admin exists and password is correct
        if (!$admin || !Hash::check($credentials['password'], $admin->admin_password)) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }

        // Log the admin in
        Auth::guard('admin')->login($admin, $request->remember);
        $request->session()->regenerate();

        // Redirect directly to admin dashboard (OTP verification removed)
        return redirect()->route('admin.dashboard')
            ->with('success', 'Welcome back to Kape Na! Admin Dashboard.');
    }

    /**
     * Show admin OTP verification notice
     */
    public function verificationNotice()
    {
        // Get the authenticated admin
        $admin = Auth::guard('admin')->user();

        // If not authenticated, redirect to login
        if (!$admin) {
            return redirect()->route('admin.login');
        }

        // If already verified, redirect to dashboard
        if ($admin->hasVerifiedEmail()) {
            return redirect()->route('admin.dashboard');
        }

        return view('auth.admin_verify_otp');
    }

    /**
     * Handle admin OTP verification
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp_code' => 'required|string|size:6',
        ]);

        $admin = Auth::guard('admin')->user();

        // Check if admin is authenticated
        if (!$admin) {
            return redirect()->route('admin.login')
                ->with('error', 'Session expired. Please log in again.');
        }

        // Check if already verified
        if ($admin->hasVerifiedEmail()) {
            return redirect()->route('admin.dashboard')
                ->with('info', 'Your email is already verified!');
        }

        // Verify the OTP code
        if ($admin->verifyOtpCode($request->otp_code)) {
            return redirect()->route('admin.dashboard')
                ->with('success', 'Your email has been verified successfully! Welcome to Kape Na! Admin Dashboard.');
        }

        return back()->withErrors([
            'otp_code' => 'Invalid or expired verification code. Please try again or request a new code.',
        ]);
    }

    /**
     * Resend OTP verification code
     */
    public function resendVerification(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        if (!$admin) {
            return redirect()->route('admin.login');
        }

        if ($admin->hasVerifiedEmail()) {
            return redirect()->route('admin.dashboard');
        }

        // Generate and resend OTP
        try {
            \Log::info('Resending admin OTP', [
                'admin_id' => $admin->admin_id,
                'admin_email' => $admin->admin_email
            ]);
            
            // Generate OTP code first
            $otpCode = $admin->generateOtpCode();
            
            \Log::info('New OTP code generated for resend', [
                'admin_id' => $admin->admin_id,
                'otp_code' => $otpCode,
                'expires_at' => $admin->otp_expires_at
            ]);
            
            // Send email immediately using direct notification
            $admin->notify(new \App\Notifications\AdminOtpVerificationNotification($otpCode));
            
            \Log::info('Admin OTP resent successfully', [
                'admin_id' => $admin->admin_id,
                'admin_email' => $admin->admin_email,
                'otp_code' => $otpCode,
                'message' => 'Email delivered to mail server'
            ]);
            
            return back()->with('success', 'A new verification code has been sent to your email address!');
        } catch (\Exception $e) {
            \Log::error('Failed to resend admin OTP email', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'admin_email' => $admin->admin_email,
                'admin_id' => $admin->admin_id
            ]);
            
            return back()->with('error', 'Failed to send verification code. Please check the logs or contact support.');
        }
    }

    /**
     * Handle admin logout
     */
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'You have been logged out successfully.');
    }

    /**
     * Handle API admin registration request (JSON response)
     */
    public function apiRegister(Request $request)
    {
        $validated = $request->validate([
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|unique:coffee_shop_admin,admin_email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            // Create the admin account
            $admin = coffee_shop_admin::create([
                'admin_name' => $validated['admin_name'],
                'admin_email' => $validated['admin_email'],
                'admin_password' => $validated['password'], // Will be automatically hashed by the model's cast
            ]);

            // Generate OTP code
            $otpCode = $admin->generateOtpCode();

            // Send OTP via email
            try {
                $admin->notify(new \App\Notifications\AdminOtpVerificationNotification($otpCode));
            } catch (\Exception $e) {
                \Log::error('Failed to send admin OTP email: ' . $e->getMessage());
                // Continue even if email fails - admin can request resend
            }

            // Do NOT log the admin in - they need to verify OTP first
            // Return admin info for verification page
            return response()->json([
                'message' => 'Registration successful! Please check your email for the verification code.',
                'user_id' => $admin->admin_id,
                'email' => $admin->admin_email,
                'requires_verification' => true,
            ], 201);
        } catch (\Exception $e) {
            \Log::error('Admin registration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Registration failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle API admin login request (JSON response)
     */
    public function apiLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Find the admin by email
        $admin = coffee_shop_admin::where('admin_email', $credentials['email'])->first();

        // Check if admin exists and password is correct
        if (!$admin || !Hash::check($credentials['password'], $admin->admin_password)) {
            return response()->json([
                'message' => 'The provided credentials do not match our records.',
            ], 422);
        }

        // Log the admin in
        Auth::guard('admin')->login($admin, $request->boolean('remember'));
        $request->session()->regenerate();

        // Generate Sanctum token for API access
        $token = $admin->createToken('admin-login-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful! Welcome back!',
            'admin' => $admin,
            'token' => $token,
        ]);
    }

    /**
     * Handle API admin OTP verification (JSON response)
     */
    public function apiVerifyOtp(Request $request)
    {
        $request->validate([
            'otp_code' => 'required|string|size:6',
            'user_id' => 'required|integer|exists:coffee_shop_admin,admin_id',
        ]);

        $admin = coffee_shop_admin::find($request->user_id);
        if (!$admin) {
            return response()->json([
                'message' => 'Admin not found.',
            ], 404);
        }

        // Check if already verified
        if ($admin->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email already verified! Please login.',
            ], 422);
        }

        // Verify the OTP
        if ($admin->verifyOtpCode($request->otp_code)) {
            // OTP is valid - log the admin in
            Auth::guard('admin')->login($admin);

            // Generate Sanctum token
            $token = $admin->createToken('admin-verification-token')->plainTextToken;

            return response()->json([
                'message' => 'Email verified successfully! Welcome to Kape Na! Admin Dashboard.',
                'admin' => $admin,
                'token' => $token,
            ]);
        }

        // OTP is invalid or expired
        return response()->json([
            'message' => 'Invalid or expired OTP code. Please try again or request a new code.',
        ], 422);
    }

    /**
     * Handle API admin resend OTP (JSON response)
     */
    public function apiResendOtp(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:coffee_shop_admin,admin_id',
        ]);

        $admin = coffee_shop_admin::find($request->user_id);
        if (!$admin) {
            return response()->json([
                'message' => 'Admin not found.',
            ], 404);
        }

        // Check if already verified
        if ($admin->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email already verified! Please login.',
            ], 422);
        }

        // Generate new OTP code
        $otpCode = $admin->generateOtpCode();

        // Send new OTP via email
        try {
            $admin->notify(new \App\Notifications\AdminOtpVerificationNotification($otpCode));
            
            return response()->json([
                'message' => 'A new OTP code has been sent to your email!',
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to resend admin OTP email: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Failed to send OTP email. Please try again later.',
            ], 500);
        }
    }

    /**
     * Handle API admin logout (JSON response)
     */
    public function apiLogout(Request $request)
    {
        // Revoke all tokens for this admin (if any exist)
        if ($request->user()) {
            $request->user()->tokens()->delete();
        }

        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'You have been logged out successfully.',
        ]);
    }

    /**
     * Get all admins for admin management
     */
    public function getAllAdmins()
    {
        try {
            $admins = coffee_shop_admin::select('admin_id', 'admin_name', 'admin_email', 'email_verified_at', 'created_at', 'updated_at')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($admin) {
                    return [
                        'admin_id' => $admin->admin_id,
                        'admin_name' => $admin->admin_name,
                        'admin_email' => $admin->admin_email,
                        'email_verified_at' => $admin->email_verified_at ? true : false,
                        'created_at' => $admin->created_at ? $admin->created_at->format('M d, Y') : 'N/A',
                        'updated_at' => $admin->updated_at,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $admins
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Get all admins error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch admins.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Delete an admin
     */
    public function deleteAdmin($id)
    {
        try {
            $admin = coffee_shop_admin::findOrFail($id);
            $admin->delete();

            return response()->json([
                'success' => true,
                'message' => 'Admin deleted successfully!'
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Delete admin error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete admin.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get dashboard statistics
     */
    public function getDashboardStats()
    {
        try {
            // Order statistics
            $totalOrders = \App\Models\Order::count();
            $totalOrdersAmount = \App\Models\Order::sum('total_amount') ?? 0;
            
            $pendingOrders = \App\Models\Order::where('status', 'pending')->get();
            $totalPending = $pendingOrders->count();
            $totalPendingAmount = $pendingOrders->sum('total_amount') ?? 0;
            
            $completedOrders = \App\Models\Order::where('status', 'completed')->get();
            $totalCompleted = $completedOrders->count();
            $totalCompletedAmount = $completedOrders->sum('total_amount') ?? 0;
            
            // Product statistics
            $totalProducts = \App\Models\Product::count();
            
            // User statistics
            $totalUsers = \App\Models\User::count();
            
            // Admin statistics
            $totalAdmins = coffee_shop_admin::count();
            
            // Employee statistics
            $totalEmployees = \App\Models\Employee::count();
            
            // Message statistics
            $newMessages = \App\Models\Message::where('is_read', false)->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'totalPendingAmount' => (float) $totalPendingAmount,
                    'totalPending' => $totalPending,
                    'totalCompletedAmount' => (float) $totalCompletedAmount,
                    'totalCompleted' => $totalCompleted,
                    'totalOrdersAmount' => (float) $totalOrdersAmount,
                    'totalOrders' => $totalOrders,
                    'totalProducts' => $totalProducts,
                    'totalUsers' => $totalUsers,
                    'totalAdmins' => $totalAdmins,
                    'totalEmployees' => $totalEmployees,
                    'newMessages' => $newMessages,
                ]
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Get dashboard stats error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch dashboard statistics.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get current admin user info
     */
    public function getAdminUser()
    {
        try {
            // Try to get admin from Sanctum token first
            $admin = null;
            $token = request()->bearerToken();
            
            if ($token) {
                $personalAccessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
                if ($personalAccessToken && $personalAccessToken->tokenable instanceof coffee_shop_admin) {
                    $admin = $personalAccessToken->tokenable;
                }
            }
            
            // Fallback to session-based auth
            if (!$admin) {
                $admin = auth()->guard('admin')->user();
            }
            
            if (!$admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Admin not authenticated'
                ], 401);
            }

            return response()->json([
                'success' => true,
                'admin_name' => $admin->admin_name,
                'admin_email' => $admin->admin_email,
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Get admin user error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch admin data.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
