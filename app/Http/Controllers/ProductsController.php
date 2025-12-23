<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductsController extends Controller
{
    /**
     * Get all products (API endpoint)
     */
    public function getAllproducts()
    {
        try {
            $baseUrl = config('app.url', 'http://localhost:8000');
            
            $products = Product::orderBy('created_at', 'desc')->get()->map(function ($product) use ($baseUrl) {
                // Get image URL
                $imageUrl = null;
                $imagePath = null;
                
                if ($product->product_image) {
                    // Normalize the path - remove any leading slashes
                    $imagePath = ltrim($product->product_image, '/');
                    
                    // Check if file exists
                    $fileExists = Storage::disk('public')->exists($imagePath);
                    
                    // Generate full URL - always construct manually to ensure it's correct
                    $imageUrl = rtrim($baseUrl, '/') . '/storage/' . $imagePath;
                    
                    // Log for debugging
                    \Log::info('Product image URL generated:', [
                        'product_id' => $product->id,
                        'product_name' => $product->product_name,
                        'image_path_db' => $product->product_image,
                        'image_path_normalized' => $imagePath,
                        'file_exists' => $fileExists,
                        'full_url' => $imageUrl
                    ]);
                }
                
                // Return product with all fields (product_name, product_price, etc.)
                return [
                    'id' => $product->id,
                    'name' => $product->product_name,
                    'product_name' => $product->product_name,
                    'price' => $product->product_price,
                    'product_price' => $product->product_price,
                    'stock' => $product->product_stock,
                    'product_stock' => $product->product_stock,
                    'category' => $product->product_category,
                    'product_category' => $product->product_category,
                    'image' => $imageUrl ?: $product->product_image, // Use full URL if available, fallback to path
                    'product_image' => $imageUrl ?: $product->product_image,
                    'admin_id' => $product->admin_id,
                    'created_at' => $product->created_at,
                    'updated_at' => $product->updated_at,
                    // Debug info
                    '_debug' => [
                        'image_path_db' => $product->product_image,
                        'image_path_normalized' => $imagePath,
                        'image_url' => $imageUrl,
                        'file_exists' => $imagePath ? Storage::disk('public')->exists($imagePath) : false,
                    ],
                ];
            });

            return response()->json($products, 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch products.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get a single product by ID (API endpoint)
     */
    public function getProduct($id)
    {
        try {
            $product = Product::findOrFail($id);
            
            // Get image URL
            $imageUrl = null;
            if ($product->product_image) {
                $imagePath = ltrim($product->product_image, '/');
                $baseUrl = config('app.url', 'http://localhost:8000');
                $imageUrl = rtrim($baseUrl, '/') . '/storage/' . $imagePath;
            }
            
            // Return product with both field name formats for compatibility
            return response()->json([
                'id' => $product->id,
                'name' => $product->product_name,
                'product_name' => $product->product_name,
                'price' => $product->product_price,
                'product_price' => $product->product_price,
                'stock' => $product->product_stock,
                'product_stock' => $product->product_stock,
                'category' => $product->product_category,
                'product_category' => $product->product_category,
                'image' => $imageUrl ?: $product->product_image,
                'product_image' => $imageUrl ?: $product->product_image,
                'admin_id' => $product->admin_id,
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 404);
        }
    }

    /**
     * Create a new product (API endpoint)
     */
    public function createProduct(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category' => 'required|in:coffee,main-dish,drinks,desserts',
            'image' => 'required|image|max:10240', // Accept all image types, max 10MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Handle image upload - store in category-specific folder
            $imagePath = null;
            if ($request->hasFile('image')) {
                // Create folder name based on category
                $categoryFolder = 'products/' . $request->category;
                $imagePath = $request->file('image')->store($categoryFolder, 'public');
                
                // Log the stored path for debugging
                \Log::info('Product image stored:', [
                    'path' => $imagePath,
                    'full_url' => Storage::disk('public')->url($imagePath),
                    'category' => $request->category
                ]);
            }

            // Get admin ID from authenticated user (try Sanctum token first, then session guards)
            $adminId = null;
            
            // Try to get authenticated user via Sanctum token
            if ($request->user()) {
                $adminId = $request->user()->admin_id ?? $request->user()->id ?? null;
            }
            
            // If no token auth, try session-based admin guard
            if (!$adminId && auth()->guard('admin')->check()) {
                $adminId = auth()->guard('admin')->id();
            }
            
            // If still no admin ID, try default guard
            if (!$adminId && auth()->check()) {
                $adminId = auth()->id();
            }

            // Create the product and save to database
            $product = Product::create([
                'admin_id' => $adminId,
                'product_name' => $request->name,
                'product_price' => $request->price,
                'product_stock' => $request->stock,
                'product_category' => $request->category,
                'product_image' => $imagePath,
            ]);

            // Log successful database save
            \Log::info('Product saved to database:', [
                'product_id' => $product->id,
                'product_name' => $product->product_name,
                'admin_id' => $product->admin_id,
            ]);

            // Get image URL
            $imageUrl = null;
            if ($product->product_image) {
                $imagePath = ltrim($product->product_image, '/');
                $baseUrl = config('app.url', 'http://localhost:8000');
                $imageUrl = rtrim($baseUrl, '/') . '/storage/' . $imagePath;
            }
            
            // Return product with both field name formats for compatibility
            return response()->json([
                'success' => true,
                'message' => 'Product created successfully!',
                'data' => [
                    'id' => $product->id,
                    'name' => $product->product_name,
                    'product_name' => $product->product_name,
                    'price' => $product->product_price,
                    'product_price' => $product->product_price,
                    'stock' => $product->product_stock,
                    'product_stock' => $product->product_stock,
                    'category' => $product->product_category,
                    'product_category' => $product->product_category,
                    'image' => $imageUrl ?: $product->product_image,
                    'product_image' => $imageUrl ?: $product->product_image,
                    'admin_id' => $product->admin_id,
                    'created_at' => $product->created_at,
                    'updated_at' => $product->updated_at,
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create product. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Update a product (API endpoint)
     */
    public function updateProduct(Request $request, $id)
    {
        try {
            $product = Product::findOrFail($id);

            // Validate the request
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:50',
                'price' => 'sometimes|required|numeric|min:0',
                'stock' => 'sometimes|required|integer|min:0',
                'category' => 'sometimes|required|in:coffee,main-dish,drinks,desserts',
                'image' => 'sometimes|image|max:10240', // Accept all image types, max 10MB
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = [
                'product_name' => $request->name ?? $product->product_name,
                'product_price' => $request->price ?? $product->product_price,
                'product_stock' => $request->stock ?? $product->product_stock,
                'product_category' => $request->category ?? $product->product_category,
            ];

            // Handle image upload if new image is provided
            if ($request->hasFile('image')) {
                // Delete old image
                if ($product->product_image && Storage::disk('public')->exists($product->product_image)) {
                    Storage::disk('public')->delete($product->product_image);
                }

                // Store in category-specific folder
                $categoryFolder = 'products/' . ($request->category ?? $product->product_category);
                $data['product_image'] = $request->file('image')->store($categoryFolder, 'public');
            }

            $product->update($data);
            $product->refresh();

            // Get image URL
            $imageUrl = null;
            if ($product->product_image) {
                $imagePath = ltrim($product->product_image, '/');
                $baseUrl = config('app.url', 'http://localhost:8000');
                $imageUrl = rtrim($baseUrl, '/') . '/storage/' . $imagePath;
            }

            // Return product with both field name formats for compatibility
            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully!',
                'data' => [
                    'id' => $product->id,
                    'name' => $product->product_name,
                    'product_name' => $product->product_name,
                    'price' => $product->product_price,
                    'product_price' => $product->product_price,
                    'stock' => $product->product_stock,
                    'product_stock' => $product->product_stock,
                    'category' => $product->product_category,
                    'product_category' => $product->product_category,
                    'image' => $imageUrl ?: $product->product_image,
                    'product_image' => $imageUrl ?: $product->product_image,
                    'admin_id' => $product->admin_id,
                    'created_at' => $product->created_at,
                    'updated_at' => $product->updated_at,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update product. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Delete a product (API endpoint)
     */
    public function deleteProduct($id)
    {
        try {
            $product = Product::findOrFail($id);

            // Delete image from storage
            if ($product->product_image && Storage::disk('public')->exists($product->product_image)) {
                Storage::disk('public')->delete($product->product_image);
            }

            $product->delete();

            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully!'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found or failed to delete.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 404);
        }
    }
}
