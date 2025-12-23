<?php

namespace App\Http\Controllers\EmployeeAuth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Employee;

class EmployeeAuthController extends Controller
{
    /**
     * Handle employee login request (API)
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Find the employee by email
        $employee = Employee::where('email', $credentials['email'])->first();

        // Check if employee exists and password is correct
        if (!$employee || !Hash::check($credentials['password'], $employee->password)) {
            return response()->json([
                'success' => false,
                'message' => 'The provided credentials do not match our records.',
            ], 401);
        }

        // Check if employee account is active
        if (isset($employee->is_active) && !$employee->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Your account is inactive. Please contact administrator.',
            ], 403);
        }

        // Generate Sanctum token for API access
        $token = $employee->createToken('employee-login-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful! Welcome back!',
            'employee' => [
                'id' => $employee->id,
                'name' => $employee->name,
                'email' => $employee->email,
                'phone' => $employee->phone,
                'address' => $employee->address,
                'sex' => $employee->sex,
                'birthday' => $employee->birthday,
            ],
            'token' => $token,
        ]);
    }

    /**
     * Handle employee logout request (API)
     */
    public function logout(Request $request)
    {
        // Revoke all tokens for this employee (if any exist)
        if ($request->user()) {
            $request->user()->tokens()->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'You have been logged out successfully.',
        ]);
    }

    /**
     * Get employee dashboard data (API)
     */
    public function dashboard(Request $request)
    {
        try {
            // Get the authenticated employee
            $employee = $request->user();

            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 401);
            }

            // Get statistics
            $stats = [
                'pendingOrders' => \App\Models\Order::where('status', 'pending')->count(),
                'completedToday' => \App\Models\Order::where('status', 'completed')
                    ->whereDate('updated_at', today())
                    ->count(),
                'totalProducts' => \App\Models\Product::count(),
                'todayRevenue' => \App\Models\Order::where('status', 'completed')
                    ->whereDate('updated_at', today())
                    ->sum('total_amount') ?? 0,
            ];

            // Get recent orders
            $recentOrders = \App\Models\Order::with('user:id,name')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($order) {
                    return [
                        'id' => $order->id,
                        'customer_name' => $order->user ? $order->user->name : 'Guest',
                        'items_count' => $order->items ? count(json_decode($order->items, true)) : 0,
                        'total' => $order->total_amount,
                        'status' => $order->status,
                        'created_at' => $order->created_at,
                    ];
                });

            return response()->json([
                'success' => true,
                'stats' => $stats,
                'recent_orders' => $recentOrders,
            ]);

        } catch (\Exception $e) {
            \Log::error('Employee dashboard error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch dashboard data.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get current authenticated employee info
     */
    public function getEmployeeUser(Request $request)
    {
        try {
            $employee = $request->user();
            
            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee not authenticated'
                ], 401);
            }

            return response()->json([
                'success' => true,
                'employee' => [
                    'id' => $employee->id,
                    'name' => $employee->name,
                    'email' => $employee->email,
                    'phone' => $employee->phone,
                    'address' => $employee->address,
                    'sex' => $employee->sex,
                    'birthday' => $employee->birthday,
                ],
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Get employee user error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch employee data.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}

