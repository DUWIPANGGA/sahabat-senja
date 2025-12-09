<?php

namespace App\Http\Controllers\API;

use App\Models\Notification;
use App\Models\User;
use App\Models\Datalansia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    /**
     * Get all notifications for authenticated user
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Build query
            $query = Notification::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->active();
$notifications = $query->get();

Log::info('Notification Data: ' . $notifications->toJson(JSON_PRETTY_PRINT));
            // Filter by type
            if ($request->has('type')) {
                $query->where('type', $request->type);
            }

            // Filter by category
            if ($request->has('category')) {
                $query->where('category', $request->category);
            }

            // Filter by read status
            if ($request->has('is_read')) {
                $query->where('is_read', filter_var($request->is_read, FILTER_VALIDATE_BOOLEAN));
            }

            // Filter by archived status
            if ($request->has('is_archived')) {
                $query->where('is_archived', filter_var($request->is_archived, FILTER_VALIDATE_BOOLEAN));
            }

            // Filter by urgency level
            if ($request->has('urgency_level')) {
                $query->where('urgency_level', $request->urgency_level);
            }

            // Search in title or message
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('message', 'like', "%{$search}%");
                });
            }

            // Pagination
            $perPage = $request->get('per_page', 20);
            $notifications = $query->paginate($perPage);

            // Get counts
            $counts = [
                'total' => Notification::where('user_id', $user->id)->count(),
                'unread' => Notification::where('user_id', $user->id)->unread()->count(),
                'urgent' => Notification::where('user_id', $user->id)
                    ->whereIn('urgency_level', ['high', 'critical'])
                    ->unread()
                    ->count(),
            ];

            return response()->json([
                'status' => 'success',
                'data' => $notifications,
                'counts' => $counts,
                'message' => 'Notifikasi berhasil diambil'
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching notifications: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil notifikasi'
            ], 500);
        }
    }

    /**
     * Get unread notifications count
     */
    public function getUnreadCount()
    {
        try {
            $user = Auth::user();
            
            $count = Notification::where('user_id', $user->id)
                ->unread()
                ->active()
                ->count();

            return response()->json([
                'status' => 'success',
                'count' => $count,
                'message' => 'Jumlah notifikasi belum dibaca berhasil diambil'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil jumlah notifikasi'
            ], 500);
        }
    }

    /**
     * Get urgent notifications
     */
    public function getUrgentNotifications()
    {
        try {
            $user = Auth::user();
            
            $notifications = Notification::where('user_id', $user->id)
                ->whereIn('urgency_level', ['high', 'critical'])
                ->unread()
                ->active()
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $notifications,
                'message' => 'Notifikasi darurat berhasil diambil'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil notifikasi darurat'
            ], 500);
        }
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        try {
            $user = Auth::user();
            
            $notification = Notification::where('user_id', $user->id)
                ->where('id', $id)
                ->firstOrFail();

            $notification->markAsRead();

            return response()->json([
                'status' => 'success',
                'data' => $notification,
                'message' => 'Notifikasi telah ditandai sebagai dibaca'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Notifikasi tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        try {
            $user = Auth::user();
            
            $count = Notification::where('user_id', $user->id)
                ->unread()
                ->update([
                    'is_read' => true,
                    'read_at' => now()
                ]);

            return response()->json([
                'status' => 'success',
                'count' => $count,
                'message' => "{$count} notifikasi telah ditandai sebagai dibaca"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menandai notifikasi'
            ], 500);
        }
    }

    /**
     * Mark notification as archived
     */
    public function archive($id)
    {
        try {
            $user = Auth::user();
            
            $notification = Notification::where('user_id', $user->id)
                ->where('id', $id)
                ->firstOrFail();

            $notification->update(['is_archived' => true]);

            return response()->json([
                'status' => 'success',
                'data' => $notification,
                'message' => 'Notifikasi telah diarsipkan'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Notifikasi tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Mark notification as action taken
     */
    public function markAsActionTaken($id)
    {
        try {
            $user = Auth::user();
            
            $notification = Notification::where('user_id', $user->id)
                ->where('id', $id)
                ->firstOrFail();

            $notification->markAsActionTaken();

            return response()->json([
                'status' => 'success',
                'data' => $notification,
                'message' => 'Notifikasi telah ditandai sebagai tindakan diambil'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Notifikasi tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Delete notification
     */
    public function destroy($id)
    {
        try {
            $user = Auth::user();
            
            $notification = Notification::where('user_id', $user->id)
                ->where('id', $id)
                ->firstOrFail();

            $notification->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Notifikasi berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Notifikasi tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Clear all read notifications
     */
    public function clearRead()
    {
        try {
            $user = Auth::user();
            
            $count = Notification::where('user_id', $user->id)
                ->where('is_read', true)
                ->delete();

            return response()->json([
                'status' => 'success',
                'count' => $count,
                'message' => "{$count} notifikasi yang sudah dibaca berhasil dihapus"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menghapus notifikasi'
            ], 500);
        }
    }

    /**
     * Get notification statistics
     */
    public function getStatistics()
    {
        try {
            $user = Auth::user();
            
            $statistics = [
                'total' => Notification::where('user_id', $user->id)->count(),
                'unread' => Notification::where('user_id', $user->id)->unread()->count(),
                'archived' => Notification::where('user_id', $user->id)->where('is_archived', true)->count(),
                'urgent' => Notification::where('user_id', $user->id)
                    ->whereIn('urgency_level', ['high', 'critical'])
                    ->count(),
                'by_type' => Notification::where('user_id', $user->id)
                    ->selectRaw('type, count(*) as count')
                    ->groupBy('type')
                    ->pluck('count', 'type'),
                'by_category' => Notification::where('user_id', $user->id)
                    ->selectRaw('category, count(*) as count')
                    ->groupBy('category')
                    ->pluck('count', 'category'),
            ];

            return response()->json([
                'status' => 'success',
                'data' => $statistics,
                'message' => 'Statistik notifikasi berhasil diambil'
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching notification statistics: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil statistik notifikasi'
            ], 500);
        }
    }

    /**
     * Create a new notification (for admin/perawat to send notifications)
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'type' => 'required|in:info,warning,emergency,reminder,success',
                'category' => 'required|in:kesehatan,iuran,jadwal,pengumuman,system',
                'title' => 'required|string|max:200',
                'message' => 'required|string',
                'urgency_level' => 'nullable|in:low,medium,high,critical',
                'datalansia_id' => 'nullable|exists:datalansia,id',
                'action_url' => 'nullable|url',
                'action_text' => 'nullable|string|max:50',
                'data' => 'nullable|array',
                'metadata' => 'nullable|array',
                'scheduled_at' => 'nullable|date',
                'expires_at' => 'nullable|date|after:now',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => $validator->errors(),
                    'message' => 'Validasi gagal'
                ], 422);
            }

            // Check if user has permission to send notification
            $sender = Auth::user();
            if (!$sender->isAdmin() && !$sender->isPerawat()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki izin untuk mengirim notifikasi'
                ], 403);
            }

            $notificationData = $request->all();
            $notificationData['sender_id'] = $sender->id;

            // If scheduled for future, don't send immediately
            if ($request->has('scheduled_at') && $request->scheduled_at > now()) {
                $notificationData['is_read'] = false;
            }

            $notification = Notification::create($notificationData);

            // If not scheduled for future, trigger real-time notification
            if (!$request->has('scheduled_at') || $request->scheduled_at <= now()) {
                $this->broadcastNotification($notification);
            }

            return response()->json([
                'status' => 'success',
                'data' => $notification,
                'message' => 'Notifikasi berhasil dikirim'
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error creating notification: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengirim notifikasi'
            ], 500);
        }
    }

    /**
     * Broadcast notification via WebSocket/Pusher (optional)
     */
    private function broadcastNotification(Notification $notification)
    {
        // Implementasi WebSocket/Pusher di sini
        // Contoh dengan Pusher:
        /*
        $pusher = new Pusher(
            config('broadcasting.connections.pusher.key'),
            config('broadcasting.connections.pusher.secret'),
            config('broadcasting.connections.pusher.app_id'),
            config('broadcasting.connections.pusher.options', [])
        );
        
        $pusher->trigger('user.' . $notification->user_id, 'notification.received', [
            'notification' => $notification,
            'type' => 'new_notification'
        ]);
        */
    }

    /**
     * Send batch notifications to multiple users
     */
    public function sendBatch(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_ids' => 'required|array',
                'user_ids.*' => 'exists:users,id',
                'type' => 'required|in:info,warning,emergency,reminder,success',
                'category' => 'required|in:kesehatan,iuran,jadwal,pengumuman,system',
                'title' => 'required|string|max:200',
                'message' => 'required|string',
                'urgency_level' => 'nullable|in:low,medium,high,critical',
                'datalansia_id' => 'nullable|exists:datalansia,id',
                'action_url' => 'nullable|url',
                'action_text' => 'nullable|string|max:50',
                'data' => 'nullable|array',
                'metadata' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => $validator->errors(),
                    'message' => 'Validasi gagal'
                ], 422);
            }

            $sender = Auth::user();
            if (!$sender->isAdmin() && !$sender->isPerawat()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki izin untuk mengirim notifikasi'
                ], 403);
            }

            $notifications = [];
            foreach ($request->user_ids as $userId) {
                $notificationData = $request->except('user_ids');
                $notificationData['user_id'] = $userId;
                $notificationData['sender_id'] = $sender->id;

                $notification = Notification::create($notificationData);
                $notifications[] = $notification;

                // Broadcast real-time notification
                $this->broadcastNotification($notification);
            }

            return response()->json([
                'status' => 'success',
                'count' => count($notifications),
                'data' => $notifications,
                'message' => count($notifications) . ' notifikasi berhasil dikirim'
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error sending batch notifications: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengirim notifikasi batch'
            ], 500);
        }
    }

    /**
     * Get notification by ID
     */
    public function show($id)
    {
        try {
            $user = Auth::user();
            
            $notification = Notification::where('user_id', $user->id)
                ->with(['sender:id,name,email,role', 'datalansia:id,nama_lansia,umur_lansia'])
                ->findOrFail($id);

            // Mark as read when viewing
            if (!$notification->is_read) {
                $notification->markAsRead();
            }

            return response()->json([
                'status' => 'success',
                'data' => $notification,
                'message' => 'Detail notifikasi berhasil diambil'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Notifikasi tidak ditemukan'
            ], 404);
        }
    }
}