<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ApiAuthController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\SocialiteController;

// Test endpoint to verify API is accessible
Route::get('/test', function () {
    return response()->json([
        'message' => 'API is working!',
        'timestamp' => now(),
    ]);
});

// Authentication (JSON) for Nuxt frontend
Route::post('/register', [ApiAuthController::class, 'register']);
Route::post('/login', [ApiAuthController::class, 'login']);
Route::post('/logout', [ApiAuthController::class, 'logout'])->middleware('auth:sanctum');

// OTP Verification endpoints
Route::post('/verify-otp', [ApiAuthController::class, 'verifyOtp']);
Route::post('/resend-otp', [ApiAuthController::class, 'resendOtp']);

// Google Authentication endpoints
Route::get('/auth/google', [SocialiteController::class, 'googleLogin'])->name('auth.google');
Route::get('/auth/google/callback', [SocialiteController::class, 'googleCallback'])->name('auth.google.callback');

// Admin Authentication endpoints
use App\Http\Controllers\adminAuth\AdminAuthController;
Route::post('/admin/register', [AdminAuthController::class, 'apiRegister']);
Route::post('/admin/login', [AdminAuthController::class, 'apiLogin']);
Route::post('/admin/logout', [AdminAuthController::class, 'apiLogout'])->middleware('auth:sanctum');
Route::post('/admin/verify-otp', [AdminAuthController::class, 'apiVerifyOtp']);
Route::post('/admin/resend-otp', [AdminAuthController::class, 'apiResendOtp']);

// Current authenticated user
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Products endpoints
// Public endpoints - anyone can view products
Route::get('/products', [ProductsController::class, 'getAllproducts'])->name('api.products.index');
Route::get('/products/{id}', [ProductsController::class, 'getProduct'])->name('api.products.show');

// Protected endpoints - require authentication for create/update/delete
// Note: Using optional auth - will work with or without authentication
Route::post('/products', [ProductsController::class, 'createProduct'])->name('api.products.store');
Route::put('/products/{id}', [ProductsController::class, 'updateProduct'])->name('api.products.update');
Route::delete('/products/{id}', [ProductsController::class, 'deleteProduct'])->name('api.products.destroy');

// Debug endpoint to test image access
Route::get('/products-debug/images', function () {
    $products = \App\Models\Product::select('id', 'product_name', 'product_image')->get();
    $baseUrl = config('app.url', 'http://localhost:8000');
    
    return response()->json([
        'base_url' => $baseUrl,
        'storage_path' => storage_path('app/public'),
        'public_storage_path' => public_path('storage'),
        'storage_link_exists' => is_link(public_path('storage')) || is_dir(public_path('storage')),
        'products' => $products->map(function ($product) use ($baseUrl) {
            $imagePath = $product->product_image ? ltrim($product->product_image, '/') : null;
            $imageUrl = $imagePath ? rtrim($baseUrl, '/') . '/storage/' . $imagePath : null;
            $fileExists = $imagePath ? \Illuminate\Support\Facades\Storage::disk('public')->exists($imagePath) : false;
            $publicFileExists = $imagePath ? file_exists(public_path('storage/' . $imagePath)) : false;
            
            return [
                'id' => $product->id,
                'name' => $product->product_name,
                'image_path_db' => $product->product_image,
                'image_path_normalized' => $imagePath,
                'image_url' => $imageUrl,
                'file_exists_storage' => $fileExists,
                'file_exists_public' => $publicFileExists,
            ];
        }),
    ]);
});

// Database verification endpoint
Route::get('/products-debug/database', function () {
    try {
        $dbConnection = \DB::connection()->getDatabaseName();
        $tableExists = \Schema::hasTable('products');
        $productCount = \App\Models\Product::count();
        $sampleProducts = \App\Models\Product::select('id', 'product_name', 'product_price', 'product_category', 'created_at')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        return response()->json([
            'success' => true,
            'database' => [
                'connection' => $dbConnection,
                'type' => \DB::connection()->getDriverName(),
                'products_table_exists' => $tableExists,
                'total_products' => $productCount,
            ],
            'recent_products' => $sampleProducts,
            'message' => 'Database connection successful. Products are being saved to the database.',
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'message' => 'Database connection failed. Please run migrations: php artisan migrate',
        ], 500);
    }
});

// Message endpoints
use App\Http\Controllers\MessageController;
Route::post('/message/store', [MessageController::class, 'store']);
Route::get('/message', [MessageController::class, 'index']);
Route::put('/message/{id}/read', [MessageController::class, 'markRead']);
Route::delete('/message/{id}', [MessageController::class, 'destroy']);

// Checkout and Orders endpoints
use App\Http\Controllers\CheckoutController;
Route::post('/checkout', [CheckoutController::class, 'checkout'])->middleware('auth:sanctum');
Route::get('/orders', [CheckoutController::class, 'getUserOrders'])->middleware('auth:sanctum');

// Admin Orders endpoints
Route::get('/admin/orders', [CheckoutController::class, 'getAllOrders'])->name('api.admin.orders.index');
Route::put('/admin/orders/{id}/status', [CheckoutController::class, 'updateOrderStatus'])->name('api.admin.orders.updateStatus');
Route::delete('/admin/orders/{id}', [CheckoutController::class, 'deleteOrder'])->name('api.admin.orders.destroy');

// Admin Users endpoints
use App\Http\Controllers\UserController;
Route::get('/admin/users', [UserController::class, 'getAllUsers'])->name('api.admin.users.index');
Route::delete('/admin/users/{id}', [UserController::class, 'deleteUser'])->name('api.admin.users.destroy');

// Admin Admins endpoints
Route::get('/admin/admins', [AdminAuthController::class, 'getAllAdmins'])->name('api.admin.admins.index');
Route::delete('/admin/admins/{id}', [AdminAuthController::class, 'deleteAdmin'])->name('api.admin.admins.destroy');

// Admin Dashboard endpoints
Route::get('/admin/dashboard/stats', [AdminAuthController::class, 'getDashboardStats'])->name('api.admin.dashboard.stats');
Route::get('/admin/user', [AdminAuthController::class, 'getAdminUser'])->name('api.admin.user');

// Admin Employees endpoints
use App\Http\Controllers\EmployeeController;
Route::get('/admin/employees', [EmployeeController::class, 'getAllEmployees'])->name('api.admin.employees.index');
Route::post('/admin/employees', [EmployeeController::class, 'createEmployee'])->name('api.admin.employees.store');
Route::delete('/admin/employees/{id}', [EmployeeController::class, 'deleteEmployee'])->name('api.admin.employees.destroy');

// Employee Authentication endpoints
use App\Http\Controllers\EmployeeAuth\EmployeeAuthController;
Route::post('/employee/login', [EmployeeAuthController::class, 'login']);
Route::post('/employee/logout', [EmployeeAuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/employee/dashboard', [EmployeeAuthController::class, 'dashboard'])->middleware('auth:sanctum');
Route::get('/employee/user', [EmployeeAuthController::class, 'getEmployeeUser'])->middleware('auth:sanctum');

