<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    /**
     * Get all chat conversations for current user
     */
    public function conversations(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Ambil semua user yang pernah chatting dengan current user
            $conversations = ChatMessage::select('sender_id', 'receiver_id')
                ->where(function($q) use ($user) {
                    $q->where('sender_id', $user->id)
                      ->orWhere('receiver_id', $user->id);
                })
                ->with(['sender', 'receiver'])
                ->get()
                ->map(function($message) use ($user) {
                    // Tentukan lawan bicara
                    $otherUser = $message->sender_id == $user->id 
                        ? $message->receiver 
                        : $message->sender;
                    
                    // Ambil last message
                    $lastMessage = ChatMessage::betweenUsers($user->id, $otherUser->id)
                        ->latest()
                        ->first();
                    
                    // Hitung unread messages
                    $unreadCount = ChatMessage::where('sender_id', $otherUser->id)
                        ->where('receiver_id', $user->id)
                        ->where('is_read', false)
                        ->count();
                    
                    return [
                        'user' => [
                            'id' => $otherUser->id,
                            'name' => $otherUser->name,
                            'email' => $otherUser->email,
                            'role' => $otherUser->role,
                            'avatar' => $otherUser->avatar_url ?? null,
                        ],
                        'last_message' => $lastMessage ? [
                            'message' => $lastMessage->message,
                            'time' => $lastMessage->created_at->format('H:i'),
                            'date' => $lastMessage->created_at->format('d/m/Y'),
                        ] : null,
                        'unread_count' => $unreadCount,
                        'last_message_time' => $lastMessage ? $lastMessage->created_at : null,
                    ];
                })
                ->unique('user.id')
                ->sortByDesc('last_message_time')
                ->values();
            
            // Untuk perawat, tambahkan semua keluarga
            if ($user->role === 'perawat') {
                $allFamilies = User::where('role', 'keluarga')->get();
                
                foreach ($allFamilies as $family) {
                    $exists = $conversations->contains(function($conv) use ($family) {
                        return $conv['user']['id'] == $family->id;
                    });
                    
                    if (!$exists) {
                        $conversations->push([
                            'user' => [
                                'id' => $family->id,
                                'name' => $family->name,
                                'email' => $family->email,
                                'role' => $family->role,
                                'avatar' => $family->avatar_url ?? null,
                            ],
                            'last_message' => null,
                            'unread_count' => 0,
                            'last_message_time' => null,
                        ]);
                    }
                }
            }
            
            // Untuk keluarga, tambahkan semua perawat
            if ($user->role === 'keluarga') {
                $allNurses = User::where('role', 'perawat')->get();
                
                foreach ($allNurses as $nurse) {
                    $exists = $conversations->contains(function($conv) use ($nurse) {
                        return $conv['user']['id'] == $nurse->id;
                    });
                    
                    if (!$exists) {
                        $conversations->push([
                            'user' => [
                                'id' => $nurse->id,
                                'name' => $nurse->name,
                                'email' => $nurse->email,
                                'role' => $nurse->role,
                                'avatar' => $nurse->avatar_url ?? null,
                            ],
                            'last_message' => null,
                            'unread_count' => 0,
                            'last_message_time' => null,
                        ]);
                    }
                }
            }
            
            return response()->json([
                'status' => 'success',
                'message' => 'Conversations retrieved successfully',
                'data' => $conversations
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve conversations',
                'error' => env('APP_DEBUG') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get messages between current user and another user
     */
    public function messages($userId)
    {
        try {
            $currentUser = Auth::user();
            
            // Validasi user
            $otherUser = User::find($userId);
            if (!$otherUser) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found'
                ], 404);
            }
            
            // Validasi role (hanya keluarga ↔ perawat)
            $validRoles = ['keluarga', 'perawat'];
            if (!in_array($currentUser->role, $validRoles) || !in_array($otherUser->role, $validRoles)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Chat hanya tersedia antara keluarga dan perawat'
                ], 400);
            }
            
            // Ambil pesan
            $messages = ChatMessage::betweenUsers($currentUser->id, $userId)
                ->with(['sender', 'receiver'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);
            
            // Tandai pesan sebagai dibaca
            ChatMessage::where('sender_id', $userId)
                ->where('receiver_id', $currentUser->id)
                ->where('is_read', false)
                ->update(['is_read' => true, 'read_at' => now()]);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Messages retrieved successfully',
                'data' => [
                    'messages' => $messages->items(),
                    'other_user' => [
                        'id' => $otherUser->id,
                        'name' => $otherUser->name,
                        'role' => $otherUser->role,
                        'avatar' => $otherUser->avatar_url ?? null,
                    ],
                    'pagination' => [
                        'current_page' => $messages->currentPage(),
                        'total_pages' => $messages->lastPage(),
                        'total_messages' => $messages->total(),
                        'has_more' => $messages->hasMorePages(),
                    ]
                ]
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve messages',
                'error' => env('APP_DEBUG') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Send new message
     */
    public function sendMessage(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'receiver_id' => 'required|exists:users,id',
                'message' => 'required_if:type,text|string|max:2000',
                'type' => 'required|in:text,image,file',
                'file' => 'required_if:type,image,file|file|max:5120', // 5MB max
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $sender = Auth::user();
            $receiver = User::find($request->receiver_id);
            
            // Validasi role
            $validRoles = ['keluarga', 'perawat'];
            if (!in_array($sender->role, $validRoles) || !in_array($receiver->role, $validRoles)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Chat hanya tersedia antara keluarga dan perawat'
                ], 400);
            }
            
            $data = [
                'sender_id' => $sender->id,
                'receiver_id' => $receiver->id,
                'type' => $request->type,
                'message' => $request->message ?? '',
            ];
            
            // Handle file upload jika ada
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = Str::random(20) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('chat_files', $fileName, 'public');
                
                $data['file_path'] = $path;
                $data['file_name'] = $file->getClientOriginalName();
                
                // Untuk image, tambahkan URL
                if ($request->type === 'image') {
                    $data['file_url'] = asset('storage/' . $path);
                }
            }
            
            $message = ChatMessage::create($data);
            $message->load(['sender', 'receiver']);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Message sent successfully',
                'data' => $message
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send message',
                'error' => env('APP_DEBUG') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Mark messages as read
     */
    public function markAsRead(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'sender_id' => 'required|exists:users,id',
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $receiver = Auth::user();
            $senderId = $request->sender_id;
            
            $updated = ChatMessage::where('sender_id', $senderId)
                ->where('receiver_id', $receiver->id)
                ->where('is_read', false)
                ->update([
                    'is_read' => true,
                    'read_at' => now(),
                ]);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Messages marked as read',
                'data' => [
                    'updated_count' => $updated
                ]
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to mark messages as read',
                'error' => env('APP_DEBUG') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get unread messages count
     */
    public function unreadCount()
    {
        try {
            $user = Auth::user();
            
            $count = ChatMessage::where('receiver_id', $user->id)
                ->where('is_read', false)
                ->count();
            
            // Count per conversation
            $conversations = ChatMessage::where('receiver_id', $user->id)
                ->where('is_read', false)
                ->selectRaw('sender_id, COUNT(*) as count')
                ->groupBy('sender_id')
                ->get()
                ->mapWithKeys(function($item) {
                    return [$item->sender_id => $item->count];
                });
            
            return response()->json([
                'status' => 'success',
                'message' => 'Unread count retrieved',
                'data' => [
                    'total_unread' => $count,
                    'conversations' => $conversations
                ]
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get unread count',
                'error' => env('APP_DEBUG') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Search users for chat
     */
    public function searchUsers(Request $request)
    {
        try {
            $user = Auth::user();
            $search = $request->get('search', '');
            
            // Tentukan role yang dicari
            $targetRole = $user->role === 'perawat' ? 'keluarga' : 'perawat';
            
            $users = User::where('role', $targetRole)
                ->where('id', '!=', $user->id)
                ->where(function($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                })
                ->limit(20)
                ->get()
                ->map(function($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                        'avatar' => $user->avatar_url ?? null,
                    ];
                });
            
            return response()->json([
                'status' => 'success',
                'message' => 'Users found',
                'data' => $users
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to search users',
                'error' => env('APP_DEBUG') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Delete a message
     */
    public function deleteMessage($messageId)
    {
        try {
            $user = Auth::user();
            
            $message = ChatMessage::find($messageId);
            
            if (!$message) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Message not found'
                ], 404);
            }
            
            // Hanya pengirim yang bisa menghapus
            if ($message->sender_id != $user->id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You can only delete your own messages'
                ], 403);
            }
            
            $message->delete();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Message deleted successfully'
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete message',
                'error' => env('APP_DEBUG') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Clear conversation with a user
     */
    public function clearConversation($userId)
    {
        try {
            $currentUser = Auth::user();
            
            // Hapus pesan dari kedua sisi
            ChatMessage::betweenUsers($currentUser->id, $userId)->delete();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Conversation cleared successfully'
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to clear conversation',
                'error' => env('APP_DEBUG') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get chat statistics
     */
    public function statistics()
    {
        try {
            $user = Auth::user();
            
            $today = now()->startOfDay();
            $weekStart = now()->startOfWeek();
            
            $stats = [
                'total_conversations' => ChatMessage::select('sender_id', 'receiver_id')
                    ->where(function($q) use ($user) {
                        $q->where('sender_id', $user->id)
                          ->orWhere('receiver_id', $user->id);
                    })
                    ->distinct()
                    ->count(),
                
                'messages_today' => ChatMessage::where('sender_id', $user->id)
                    ->whereDate('created_at', today())
                    ->count(),
                
                'messages_this_week' => ChatMessage::where('sender_id', $user->id)
                    ->where('created_at', '>=', $weekStart)
                    ->count(),
                
                'total_sent' => ChatMessage::where('sender_id', $user->id)->count(),
                'total_received' => ChatMessage::where('receiver_id', $user->id)->count(),
                
                'unread_messages' => ChatMessage::where('receiver_id', $user->id)
                    ->where('is_read', false)
                    ->count(),
            ];
            
            return response()->json([
                'status' => 'success',
                'message' => 'Chat statistics retrieved',
                'data' => $stats
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get statistics',
                'error' => env('APP_DEBUG') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}