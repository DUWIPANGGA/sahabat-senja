<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use App\Models\Datalansia;
use App\Models\NotificationTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    /**
     * Display a listing of notifications.
     */
     public function index(Request $request)
    {
        $query = Notification::with(['user', 'sender', 'datalansia'])
            ->latest();
        
        // Apply filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        
        if ($request->filled('urgency_level')) {
            $query->where('urgency_level', $request->urgency_level);
        }
        
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'unread':
                    $query->where('is_read', false);
                    break;
                case 'read':
                    $query->where('is_read', true);
                    break;
                case 'archived':
                    $query->where('is_archived', true);
                    break;
                case 'action_taken':
                    $query->where('is_action_taken', true);
                    break;
            }
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('message', 'like', '%' . $search . '%')
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('datalansia', function($q) use ($search) {
                      $q->where('nama_lansia', 'like', '%' . $search . '%');
                  });
            });
        }
        
        $notifications = $query->paginate(20);
        
        // Statistics
        $stats = [
            'total' => Notification::count(),
            'unread' => Notification::where('is_read', false)->count(),
            'emergency' => Notification::where('type', 'emergency')->count(),
            'critical' => Notification::where('urgency_level', 'critical')->count(),
            'today' => Notification::whereDate('created_at', today())->count(),
        ];
        
        // Get data for modals (untuk modal darurat dan test)
        $lansias = Datalansia::all(); // Tambahkan ini
        $users = User::whereIn('role', ['keluarga', 'perawat', 'admin'])->get(); // Tambahkan ini
        
        return view('admin.notifications.index', compact(
            'notifications', 
            'stats', 
            'lansias', // Tambahkan ini
            'users'     // Tambahkan ini
        ));
    }
    
    /**
     * Show the form for creating a new notification.
     */
    public function create()
    {
        $users = User::whereIn('role', ['keluarga', 'perawat'])->get();
        $lansias = Datalansia::all();
        $templates = NotificationTemplate::where('is_active', true)->get();
        
        return view('admin.notifications.create', compact('users', 'lansias', 'templates'));
    }
    
    /**
     * Store a newly created notification in storage.
     */
    public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'title' => 'required|string|max:255',
        'message' => 'required|string|max:1000',
        'type' => 'required|in:emergency,warning,info,system',
        'category' => 'required|in:kesehatan,iuran,pengobatan,administrasi,sistem',
        'urgency_level' => 'required|in:low,medium,high,critical',
        'user_id' => 'nullable|exists:users,id',
        'datalansia_id' => 'nullable|exists:datalansia,id',
        'target_type' => 'required|in:specific_user,all_family,all_nurses,all_admins,all_users',
        'action_url' => 'nullable|url',
        'action_text' => 'nullable|string|max:50',
        'scheduled_at' => 'nullable|date|after:now',
        'expires_at' => 'nullable|date|after:scheduled_at',
    ]);
    
    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }
    
    $validated = $validator->validated();
    
    // Handle different target types
    $notifications = [];
    $usersToNotify = [];
    
    // Tentukan user berdasarkan target type
    switch ($validated['target_type']) {
        case 'specific_user':
            if ($validated['user_id']) {
                $usersToNotify = [User::find($validated['user_id'])];
            }
            break;
            
        case 'all_family':
            $usersToNotify = User::where('role', 'keluarga')->get();
            break;
            
        case 'all_nurses':
            $usersToNotify = User::where('role', 'perawat')->get();
            break;
            
        case 'all_admins':
            $usersToNotify = User::where('role', 'admin')->get();
            break;
            
        case 'all_users':
            $usersToNotify = User::whereIn('role', ['keluarga', 'perawat', 'admin'])->get();
            break;
    }
    
    // Buat notifikasi untuk setiap user
    foreach ($usersToNotify as $user) {
        $notification = Notification::create([
            'id' => Str::uuid(),
            'user_id' => $user->id,
            'sender_id' => auth()->id(),
            'datalansia_id' => $validated['datalansia_id'] ?? null,
            'type' => $validated['type'],
            'category' => $validated['category'],
            'title' => $validated['title'],
            'message' => $validated['message'],
            'urgency_level' => $validated['urgency_level'],
            'action_url' => $validated['action_url'] ?? null,
            'action_text' => $validated['action_text'] ?? null,
            'scheduled_at' => $validated['scheduled_at'] ?? null,
            'expires_at' => $validated['expires_at'] ?? null,
            'data' => json_encode([
                'created_by' => auth()->user()->name,
                'created_by_email' => auth()->user()->email,
                'target_type' => $validated['target_type'],
                'timestamp' => now()->toISOString(),
            ]),
        ]);
        
        $notifications[] = $notification;
        
        // Kirim notifikasi real-time jika tidak dijadwalkan
        if (empty($validated['scheduled_at'])) {
            $this->sendRealTimeNotification($notification);
        }
    }
    
    $count = count($notifications);
    
    return redirect()->route('admin.notifications.index')
        ->with('success', "{$count} notifikasi berhasil dibuat.");
}
    
    /**
     * Create a single notification
     */
    private function createNotification(array $data)
{
    // Generate UUID untuk id
    $notification = Notification::create([
        'id' => Str::uuid(), // TAMBAHKAN INI
        'user_id' => $data['user_id'] ?? null,
        'sender_id' => $data['sender_id'] ?? auth()->id(),
        'datalansia_id' => $data['datalansia_id'] ?? null,
        'type' => $data['type'],
        'category' => $data['category'],
        'title' => $data['title'],
        'message' => $data['message'],
        'urgency_level' => $data['urgency_level'],
        'action_url' => $data['action_url'] ?? null,
        'action_text' => $data['action_text'] ?? null,
        'scheduled_at' => $data['scheduled_at'] ?? null,
        'expires_at' => $data['expires_at'] ?? null,
        'data' => $this->prepareNotificationData($data),
    ]);
    
    return $notification;
}
    /**
     * Prepare notification data
     */
    private function prepareNotificationData(array $data)
    {
        return json_encode([
            'created_by' => auth()->user()->name,
            'created_by_email' => auth()->user()->email,
            'target_type' => $data['target_type'],
            'timestamp' => now()->toISOString(),
        ]);
    }
    
    /**
     * Send real-time notification
     */
    private function sendRealTimeNotification(Notification $notification)
    {
        // Implement real-time notification sending
        // This could be via Pusher, Firebase, WebSocket, etc.
        
        // Example with broadcast event (if using Laravel Echo)
        // broadcast(new NewNotification($notification));
        
        // For now, we'll just log it
        \Log::info('Notification sent', [
            'id' => $notification->id,
            'title' => $notification->title,
            'user_id' => $notification->user_id,
        ]);
    }
    
    /**
     * Show the specified notification.
     */
    public function show(Notification $notification)
    {
        $notification->load(['user', 'sender', 'datalansia']);
        
        return view('admin.notifications.show', compact('notification'));
    }
    
    /**
     * Mark notification as read.
     */
    public function markAsRead(Notification $notification)
    {
        $notification->markAsRead();
        
        return back()->with('success', 'Notifikasi ditandai sebagai sudah dibaca.');
    }
    
    /**
     * Mark notification as archived.
     */
    public function markAsArchived(Notification $notification)
    {
        $notification->update(['is_archived' => true]);
        
        return back()->with('success', 'Notifikasi diarsipkan.');
    }
    
    /**
     * Mark notification as action taken.
     */
    public function markAsActionTaken(Notification $notification)
    {
        $notification->markAsActionTaken();
        
        return back()->with('success', 'Notifikasi ditandai sebagai sudah ditindaklanjuti.');
    }
    
    /**
     * Send emergency notification for lansia.
     */
    public function sendEmergency(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'datalansia_id' => 'required|exists:datalansia,id',
            'emergency_type' => 'required|in:medical_emergency,hospitalization,critical_condition,accident,missing_person',
            'description' => 'required|string',
            'location' => 'nullable|string',
            'hospital_name' => 'nullable|string',
            'severity_level' => 'required|in:level_1,level_2,level_3,level_4,level_5',
            'contact_person' => 'nullable|string',
            'contact_number' => 'nullable|string',
            'send_to' => 'required|array',
            'send_to.*' => 'in:family,emergency_contacts,nurses,admins',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Validasi gagal.');
        }
        
        $validated = $validator->validated();
        $lansia = Datalansia::findOrFail($validated['datalansia_id']);
        
        // Prepare notification data
        $notificationData = [
            'type' => 'emergency',
            'category' => 'kesehatan',
            'urgency_level' => $validated['severity_level'] == 'level_5' ? 'critical' : 'high',
            'datalansia_id' => $lansia->id,
            'sender_id' => auth()->id(),
            'title' => $this->getEmergencyTitle($validated['emergency_type']),
            'message' => $this->getEmergencyMessage($lansia, $validated),
            'action_url' => route('admin.datalansia.show', $lansia->id),
            'action_text' => 'Lihat Detail Lansia',
            'data' => json_encode([
                'emergency_type' => $validated['emergency_type'],
                'location' => $validated['location'],
                'hospital_name' => $validated['hospital_name'],
                'contact_person' => $validated['contact_person'],
                'contact_number' => $validated['contact_number'],
                'reported_by' => auth()->user()->name,
                'timestamp' => now()->toISOString(),
            ]),
        ];
        
        // Send to selected recipients
        $sentCount = 0;
        
        if (in_array('family', $validated['send_to']) && $lansia->user_id) {
            $notification = Notification::create(array_merge($notificationData, [
                'id' => Str::uuid(),
                'user_id' => $lansia->user_id,
            ]));
            $this->sendRealTimeNotification($notification);
            $sentCount++;
        }
        
        if (in_array('emergency_contacts', $validated['send_to'])) {
            // Get emergency contacts from lansia (you need to create this relationship)
            $contacts = $lansia->emergencyContacts ?? collect();
            foreach ($contacts as $contact) {
                // Here you would need to find the user associated with the contact
                // For now, we'll just log it
                \Log::info('Emergency contact notified', [
                    'lansia' => $lansia->nama_lansia,
                    'contact' => $contact->name,
                    'phone' => $contact->phone_number,
                ]);
                $sentCount++;
            }
        }
        
        if (in_array('nurses', $validated['send_to'])) {
            $nurses = User::where('role', 'perawat')->get();
            foreach ($nurses as $nurse) {
                $notification = Notification::create(array_merge($notificationData, [
                    'id' => Str::uuid(),
                    'user_id' => $nurse->id,
                ]));
                $this->sendRealTimeNotification($notification);
                $sentCount++;
            }
        }
        
        if (in_array('admins', $validated['send_to'])) {
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                $notification = Notification::create(array_merge($notificationData, [
                    'id' => Str::uuid(),
                    'user_id' => $admin->id,
                ]));
                $this->sendRealTimeNotification($notification);
                $sentCount++;
            }
        }
        
        // Log the emergency
        \Log::emergency('Emergency alert sent', [
            'lansia' => $lansia->nama_lansia,
            'type' => $validated['emergency_type'],
            'severity' => $validated['severity_level'],
            'sent_to' => $sentCount . ' recipients',
            'reported_by' => auth()->user()->name,
        ]);
        
        return redirect()->route('admin.notifications.index')
            ->with('success', "Notifikasi darurat berhasil dikirim ke {$sentCount} penerima.");
    }
    
    /**
     * Get emergency title based on type.
     */
    private function getEmergencyTitle($type)
    {
        $titles = [
            'medical_emergency' => '🚨 DARURAT MEDIS: Lansia Mengalami Kondisi Kritis',
            'hospitalization' => '🏥 LANSIA DIRUJUK KE RUMAH SAKIT',
            'critical_condition' => '⚠️ KONDISI KRITIS: Lansia Membutuhkan Perhatian Segera',
            'accident' => '🆘 KECELAKAAN: Lansia Mengalami Kecelakaan',
            'missing_person' => '🔍 LANSIA HILANG: Perlu Pencarian Segera',
        ];
        
        return $titles[$type] ?? 'Notifikasi Darurat';
    }
    
    /**
     * Get emergency message.
     */
    private function getEmergencyMessage($lansia, $data)
    {
        $message = "Lansia: {$lansia->nama_lansia} ({$lansia->umur_lansia} tahun)\n";
        $message .= "Kondisi: {$data['description']}\n";
        
        if ($data['location']) {
            $message .= "Lokasi: {$data['location']}\n";
        }
        
        if ($data['hospital_name']) {
            $message .= "Rumah Sakit: {$data['hospital_name']}\n";
        }
        
        if ($data['contact_person'] && $data['contact_number']) {
            $message .= "Kontak Darurat: {$data['contact_person']} - {$data['contact_number']}\n";
        }
        
        $message .= "\nDilaporkan oleh: " . auth()->user()->name . "\n";
        $message .= "Waktu: " . now()->format('d/m/Y H:i:s');
        
        return $message;
    }
    
    /**
     * Delete the specified notification.
     */
    public function destroy(Notification $notification)
    {
        $notification->delete();
        
        return back()->with('success', 'Notifikasi berhasil dihapus.');
    }
    
    /**
     * Bulk delete notifications.
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'notification_ids' => 'required|array',
            'notification_ids.*' => 'exists:notifications,id',
        ]);
        
        $count = Notification::whereIn('id', $request->notification_ids)->delete();
        
        return back()->with('success', "{$count} notifikasi berhasil dihapus.");
    }
    
    /**
     * Send test notification.
     */
    public function sendTest(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:emergency,warning,info,system',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
        ]);
        
        $user = User::findOrFail($request->user_id);
        
        $notification = Notification::create([
            'id' => Str::uuid(),
            'user_id' => $user->id,
            'sender_id' => auth()->id(),
            'type' => $request->type,
            'category' => 'sistem',
            'title' => '[TEST] ' . $request->title,
            'message' => $request->message,
            'urgency_level' => 'medium',
            'data' => json_encode([
                'is_test' => true,
                'sent_by' => auth()->user()->name,
            ]),
        ]);
        
        $this->sendRealTimeNotification($notification);
        
        return back()->with('success', 'Notifikasi test berhasil dikirim ke ' . $user->name);
    }
    
    /**
     * Get notification statistics for dashboard.
     */
    public function statistics()
    {
        $today = now()->startOfDay();
        
        $stats = [
            'today_total' => Notification::whereDate('created_at', $today)->count(),
            'today_emergency' => Notification::whereDate('created_at', $today)
                ->where('type', 'emergency')->count(),
            'today_critical' => Notification::whereDate('created_at', $today)
                ->where('urgency_level', 'critical')->count(),
            'unread_count' => Notification::where('is_read', false)->count(),
            'recent_emergencies' => Notification::where('type', 'emergency')
                ->where('created_at', '>=', now()->subHours(24))
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get(),
        ];
        
        return response()->json($stats);
    }
}