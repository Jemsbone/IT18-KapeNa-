<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Mail\OrderSummary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class CheckoutController extends Controller
{
    /**
     * Process checkout and create a new order
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkout(Request $request)
    {
        try {
            // Validate incoming request
            $validator = Validator::make($request->all(), [
                'cart_data' => 'required|array',
                'cart_data.*.id' => 'required|integer|exists:products,id',
                'cart_data.*.quantity' => 'required|integer|min:1',
                'order_summary' => 'required|array',
                'order_summary.subtotal' => 'required|numeric|min:0',
                'order_summary.tax' => 'required|numeric|min:0',
                'order_summary.service_fee' => 'required|numeric|min:0',
                'order_summary.total' => 'required|numeric|min:0',
                'payment_method' => 'required|string|in:cash,gcash,bank',
                'payment_details' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $user = $request->user();
            
            // Begin transaction
            DB::beginTransaction();

            // Verify product availability and prices
            $validatedCartData = [];
            foreach ($request->cart_data as $cartItem) {
                $product = Product::find($cartItem['id']);
                
                if (!$product) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Product not found: ' . $cartItem['id'],
                    ], 404);
                }

                // Check stock availability if applicable
                if (isset($product->stock) && $product->stock < $cartItem['quantity']) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Insufficient stock for product: ' . $product->product_name,
                    ], 400);
                }

                $validatedCartData[] = [
                    'id' => $product->id,
                    'product_name' => $product->product_name,
                    'product_price' => $product->product_price,
                    'quantity' => $cartItem['quantity'],
                    'subtotal' => $product->product_price * $cartItem['quantity'],
                    'product_image' => $product->product_image,
                    'product_category' => $product->product_category,
                ];

                // Update stock if applicable
                if (isset($product->stock)) {
                    $product->decrement('stock', $cartItem['quantity']);
                }
            }

            // Generate unique order number
            $orderNumber = Order::generateOrderNumber();

            // Create the order
            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => $orderNumber,
                'cart_data' => $validatedCartData,
                'order_summary' => $request->order_summary,
                'payment_method' => $request->payment_method,
                'payment_details' => $request->payment_details,
                'status' => 'pending',
                'payment_status' => $request->payment_method === 'cash' ? 'pending' : 'pending_verification',
                'total_amount' => $request->order_summary['total'],
            ]);

            // Commit transaction
            DB::commit();

            // Send order confirmation email (async - don't block response)
            try {
                Mail::to($user->email)->send(
                    new OrderSummary(
                        $validatedCartData,
                        $request->order_summary,
                        $user,
                        $order
                    )
                );
            } catch (\Exception $e) {
                // Log email error but don't fail the order
                Log::error('Failed to send order confirmation email: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully!',
                'order' => $order->load('user'),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Checkout error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to process order',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all orders for the authenticated user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserOrders(Request $request)
    {
        try {
            $user = $request->user();
            
            $orders = Order::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'orders' => $orders,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Get user orders error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch orders',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all orders (Admin only)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllOrders(Request $request)
    {
        try {
            // Add pagination support
            $perPage = $request->input('per_page', 100);
            $status = $request->input('status');
            $orderBy = $request->input('order_by', 'created_at');
            $orderDir = $request->input('order_dir', 'desc');

            $query = Order::with('user');

            // Filter by status if provided
            if ($status && $status !== 'all') {
                $query->where('status', $status);
            }

            // Search functionality
            if ($search = $request->input('search')) {
                $query->where(function ($q) use ($search) {
                    $q->where('order_number', 'like', "%{$search}%")
                      ->orWhereHas('user', function ($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                      });
                });
            }

            $orders = $query->orderBy($orderBy, $orderDir)->get();

            // Format orders for frontend
            $formattedOrders = $orders->map(function ($order) {
                // Format products list
                $products = '';
                if (is_array($order->cart_data)) {
                    $productsList = [];
                    foreach ($order->cart_data as $item) {
                        $productName = $item['product_name'] ?? $item['name'] ?? 'Unknown Product';
                        $quantity = $item['quantity'] ?? 1;
                        $productsList[] = "{$productName} ({$quantity})";
                    }
                    $products = implode(', ', $productsList);
                }

                // Get payment details
                $paymentDetails = $order->payment_details ?? [];
                $phone = '';
                $address = '';

                if (is_array($paymentDetails)) {
                    $phone = $paymentDetails['phone_number'] ?? '';
                    $address = $paymentDetails['bank_account'] ?? $paymentDetails['address'] ?? '';
                }

                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'date' => $order->created_at ? $order->created_at->format('Y-m-d') : null,
                    'created_at' => $order->created_at,
                    'name' => $order->user->name ?? 'Unknown',
                    'email' => $order->user->email ?? 'N/A',
                    'phone' => $phone ?: 'N/A',
                    'address' => $address ?: 'N/A',
                    'products' => $products,
                    'total_price' => $order->total_amount,
                    'payment_type' => strtoupper($order->payment_method),
                    'payment_status' => $order->payment_status ?? 'pending',
                    'status' => $order->status,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedOrders,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Get all orders error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch orders',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update order status (Admin only)
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateOrderStatus(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'status' => 'required|string|in:pending,confirmed,processing,ready_for_pickup,completed,cancelled',
                'payment_status' => 'nullable|string|in:pending,pending_verification,paid,failed',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $order = Order::with('user')->findOrFail($id);

            $oldStatus = $order->status;
            $order->status = $request->status;
            
            if ($request->has('payment_status')) {
                $order->payment_status = $request->payment_status;
            }
            
            $order->save();

            // Send notification email if order is ready for pickup
            if ($request->status === 'ready_for_pickup' && $oldStatus !== 'ready_for_pickup') {
                try {
                    // Check if OrderReadyToPickup mail class exists
                    if (class_exists('App\Mail\OrderReadyToPickup')) {
                        Mail::to($order->user->email)->send(
                            new \App\Mail\OrderReadyToPickup($order)
                        );
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to send order ready notification: ' . $e->getMessage());
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Order status updated successfully',
                'order' => $order,
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found',
            ], 404);
        } catch (\Exception $e) {
            Log::error('Update order status error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update order status',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete an order (Admin only)
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteOrder($id)
    {
        try {
            $order = Order::findOrFail($id);

            // Restore stock if applicable
            DB::beginTransaction();
            
            foreach ($order->cart_data as $item) {
                $product = Product::find($item['id']);
                if ($product && isset($product->stock)) {
                    $product->increment('stock', $item['quantity']);
                }
            }

            $order->delete();
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order deleted successfully',
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found',
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Delete order error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete order',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

