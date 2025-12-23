<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{
    /**
     * Store a new message from customer contact form (API endpoint)
     */
    public function store(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'msg' => 'required|string|max:500',
            'userId' => 'nullable|integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Create the message
            $message = Message::create([
                'user_id' => $request->userId ?? (Auth::check() ? Auth::id() : null),
                'name' => $request->name,
                'email' => $request->email,
                'message' => $request->msg,
                'is_read' => false,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Your message has been sent successfully! We will respond to you soon.',
                'data' => [
                    'id' => $message->id,
                    'name' => $message->name,
                    'email' => $message->email,
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send message. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Display all messages for admin (API endpoint)
     */
    public function index()
    {
        try {
            // Get all messages ordered by newest first
            $messages = Message::with('user')
                ->orderBy('is_read', 'asc')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($message) {
                    return [
                        'id' => $message->id,
                        'name' => $message->name,
                        'email' => $message->email,
                        'message' => $message->message,
                        'user_id' => $message->user_id,
                        'is_read' => $message->is_read,
                        'created_at' => $message->created_at->toISOString(),
                        'updated_at' => $message->updated_at->toISOString(),
                    ];
                });

            return response()->json($messages, 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch messages.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Mark a message as read (API endpoint)
     */
    public function markRead($id)
    {
        try {
            $message = Message::findOrFail($id);
            $message->is_read = true;
            $message->save();

            return response()->json([
                'success' => true,
                'message' => 'Message marked as read.'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Message not found or failed to update.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 404);
        }
    }

    /**
     * Delete a message (API endpoint)
     */
    public function destroy($id)
    {
        try {
            $message = Message::findOrFail($id);
            $message->delete();

            return response()->json([
                'success' => true,
                'message' => 'Message deleted successfully.'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Message not found or failed to delete.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 404);
        }
    }

    /**
     * Get unread message count (API endpoint)
     */
    public function getUnreadCount()
    {
        try {
            $count = Message::where('is_read', false)->count();

            return response()->json([
                'success' => true,
                'count' => $count
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get unread count.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
