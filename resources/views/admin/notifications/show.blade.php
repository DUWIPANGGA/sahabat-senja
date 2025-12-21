@extends('layouts.app')

@section('title', 'Detail Notifikasi')
@section('page-title', 'Detail Notifikasi')
@section('icon', 'fas fa-bell')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="activity-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0"><i class="fas fa-eye me-2"></i>Detail Notifikasi</h3>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.notifications.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Kembali
                        </a>
                        
                        @if(!$notification->is_read)
                        <form action="{{ route('admin.notifications.mark-as-read', $notification) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success">
                                <i class="fas fa-check me-1"></i>Tandai Dibaca
                            </button>
                        </form>
                        @endif
                        
                        @if(!$notification->is_action_taken)
                        <form action="{{ route('admin.notifications.mark-as-action-taken', $notification) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-warning">
                                <i class="fas fa-check-double me-1"></i>Tandai Ditindak
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Status Badge -->
                    <div class="mb-4">
                        @php
                            $typeColors = [
                                'emergency' => 'danger',
                                'warning' => 'warning',
                                'info' => 'info',
                                'system' => 'primary'
                            ];
                            
                            $typeIcons = [
                                'emergency' => 'exclamation-triangle',
                                'warning' => 'exclamation-circle',
                                'info' => 'info-circle',
                                'system' => 'cog'
                            ];
                            
                            $urgencyColors = [
                                'critical' => 'danger',
                                'high' => 'warning',
                                'medium' => 'info',
                                'low' => 'success'
                            ];
                            
                            $urgencyIcons = [
                                'critical' => 'fire',
                                'high' => 'exclamation',
                                'medium' => 'info',
                                'low' => 'check'
                            ];
                            
                            $categoryIcons = [
                                'kesehatan' => 'heartbeat',
                                'iuran' => 'money-bill-wave',
                                'pengobatan' => 'pills',
                                'administrasi' => 'file-alt',
                                'sistem' => 'cogs'
                            ];
                        @endphp
                        
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <span class="badge bg-{{ $typeColors[$notification->type] ?? 'secondary' }}">
                                <i class="fas fa-{{ $typeIcons[$notification->type] ?? 'bell' }} me-1"></i>
                                {{ ucfirst($notification->type) }}
                            </span>
                            
                            <span class="badge bg-{{ $urgencyColors[$notification->urgency_level] ?? 'secondary' }}">
                                <i class="fas fa-{{ $urgencyIcons[$notification->urgency_level] ?? 'info' }} me-1"></i>
                                {{ ucfirst($notification->urgency_level) }}
                            </span>
                            
                            <span class="badge bg-secondary">
                                <i class="fas fa-{{ $categoryIcons[$notification->category] ?? 'tag' }} me-1"></i>
                                {{ ucfirst($notification->category) }}
                            </span>
                            
                            @if($notification->is_read)
                            <span class="badge bg-success">
                                <i class="fas fa-check me-1"></i>Sudah Dibaca
                            </span>
                            @else
                            <span class="badge bg-warning">
                                <i class="fas fa-clock me-1"></i>Belum Dibaca
                            </span>
                            @endif
                            
                            @if($notification->is_action_taken)
                            <span class="badge bg-success">
                                <i class="fas fa-check-double me-1"></i>Sudah Ditindak
                            </span>
                            @endif
                            
                            @if($notification->is_archived)
                            <span class="badge bg-secondary">
                                <i class="fas fa-archive me-1"></i>Diarsipkan
                            </span>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Informasi Utama -->
                    <div class="row mb-4">
                        <div class="col-lg-8">
                            <div class="card h-100">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">
                                        <i class="fas fa-comment me-2"></i>Konten Notifikasi
                                    </h5>
                                    <small class="text-muted">ID: {{ $notification->id }}</small>
                                </div>
                                <div class="card-body">
                                    <h4 class="card-title mb-4">{{ $notification->title }}</h4>
                                    <div class="border rounded p-4 bg-light mb-4">
                                        <div class="message-content" style="white-space: pre-wrap; line-height: 1.6;">
                                            {{ $notification->message }}
                                        </div>
                                    </div>
                                    
                                    @if($notification->action_url && $notification->action_text)
                                    <div class="mt-4">
                                        <a href="{{ url($notification->action_url) }}" 
                                           class="btn btn-primary" 
                                           target="_blank"
                                           onclick="trackActionClick('{{ $notification->id }}')">
                                            <i class="fas fa-external-link-alt me-1"></i>
                                            {{ $notification->action_text }}
                                        </a>
                                        <small class="text-muted ms-2">
                                            <i class="fas fa-link me-1"></i>{{ $notification->action_url }}
                                        </small>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-4 mt-3 mt-lg-0">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">
                                        <i class="fas fa-info-circle me-2"></i>Informasi
                                    </h5>
                                </div>
                                <div class="card-body p-0">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                            <span class="text-muted">
                                                <i class="fas fa-user me-2"></i>Dibuat Oleh:
                                            </span>
                                            <span class="fw-bold">
                                                {{ $notification->sender->name ?? ($notification->data['sender_name'] ?? 'Sistem') }}
                                            </span>
                                        </li>
                                        
                                        <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                            <span class="text-muted">
                                                <i class="fas fa-calendar me-2"></i>Dibuat:
                                            </span>
                                            <span>
                                                {{ $notification->created_at->translatedFormat('d F Y') }}
                                                <br>
                                                <small class="text-muted">{{ $notification->created_at->translatedFormat('H:i:s') }}</small>
                                            </span>
                                        </li>
                                        
                                        @if($notification->is_read && $notification->read_at)
                                        <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                            <span class="text-muted">
                                                <i class="fas fa-check me-2"></i>Dibaca:
                                            </span>
                                            <span>
                                                {{ $notification->read_at->translatedFormat('d F Y') }}
                                                <br>
                                                <small class="text-muted">{{ $notification->read_at->translatedFormat('H:i:s') }}</small>
                                            </span>
                                        </li>
                                        @endif
                                        
                                        @if($notification->scheduled_at)
                                        <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                            <span class="text-muted">
                                                <i class="fas fa-clock me-2"></i>Dijadwalkan:
                                            </span>
                                            <span>
                                                {{ $notification->scheduled_at->translatedFormat('d F Y H:i') }}
                                            </span>
                                        </li>
                                        @endif
                                        
                                        @if($notification->expires_at)
                                        <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                            <span class="text-muted">
                                                <i class="fas fa-hourglass-end me-2"></i>Kadaluarsa:
                                            </span>
                                            <span class="{{ $notification->expires_at->isPast() ? 'text-danger' : '' }}">
                                                {{ $notification->expires_at->translatedFormat('d F Y H:i') }}
                                                @if($notification->expires_at->isPast())
                                                <br>
                                                <small class="text-danger">(Sudah kadaluarsa)</small>
                                                @endif
                                            </span>
                                        </li>
                                        @endif
                                        
                                        <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                            <span class="text-muted">
                                                <i class="fas fa-bullhorn me-2"></i>Tipe Pengiriman:
                                            </span>
                                            <span class="fw-bold">
                                                @if($notification->user_id)
                                                User Tertentu
                                                @elseif($notification->data['broadcast_type'] ?? false)
                                                {{ ucfirst(str_replace('_', ' ', $notification->data['broadcast_type'])) }}
                                                @else
                                                User Tertentu
                                                @endif
                                            </span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Informasi Penerima -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">
                                        <i class="fas fa-user me-2"></i>Penerima
                                    </h5>
                                    <span class="badge bg-info">
                                        @if($notification->user_id)
                                        1 User
                                        @elseif($notification->data['broadcast_type'] ?? false)
                                        Semua {{ ucfirst(str_replace('_', ' ', $notification->data['broadcast_type'])) }}
                                        @else
                                        Tidak diketahui
                                        @endif
                                    </span>
                                </div>
                                <div class="card-body">
                                    @if($notification->user)
                                    <div class="d-flex align-items-start mb-4">
                                        <div class="avatar me-3">
                                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" 
                                                 style="width: 60px; height: 60px; font-size: 24px;">
                                                {{ strtoupper(substr($notification->user->name, 0, 1)) }}
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="mb-1">{{ $notification->user->name }}</h5>
                                            <p class="text-muted mb-1">
                                                <i class="fas fa-envelope me-1"></i>{{ $notification->user->email }}
                                            </p>
                                            <div class="d-flex align-items-center">
                                                <span class="badge bg-info me-2">{{ ucfirst($notification->user->role) }}</span>
                                                @if($notification->user->phone)
                                                <span class="text-muted">
                                                    <i class="fas fa-phone me-1"></i>{{ $notification->user->phone }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @elseif($notification->data['broadcast_type'] ?? false)
                                    <div class="alert alert-info">
                                        <i class="fas fa-broadcast-tower me-2"></i>
                                        Notifikasi ini dikirim ke semua 
                                        <strong>{{ str_replace('_', ' ', $notification->data['broadcast_type']) }}</strong>
                                        @if($notification->data['sent_count'] ?? false)
                                        <br>
                                        <small class="mt-1 d-block">
                                            <i class="fas fa-paper-plane me-1"></i>
                                            Terkirim ke {{ $notification->data['sent_count'] }} user
                                        </small>
                                        @endif
                                    </div>
                                    @else
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        Tidak ada informasi penerima
                                    </div>
                                    @endif
                                    
                                    @if($notification->data && !empty($notification->data))
                                    <div class="mt-4">
                                        <h6 class="text-muted mb-2">
                                            <i class="fas fa-database me-1"></i>Data Tambahan:
                                        </h6>
                                        <div class="border rounded p-3 bg-light">
                                            <pre class="mb-0 small" style="max-height: 200px; overflow-y: auto;">{!! htmlspecialchars(json_encode(json_decode($notification->data), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) !!}</pre>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- Informasi Lansia Terkait -->
                        @if($notification->datalansia)
                        <div class="col-md-6 mt-3 mt-md-0">
                            <div class="card h-100">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">
                                        <i class="fas fa-user-friends me-2"></i>Lansia Terkait
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex align-items-start">
                                        <div class="avatar me-3">
                                            <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center" 
                                                 style="width: 60px; height: 60px; font-size: 24px;">
                                                <i class="fas fa-user-friends"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="mb-1">{{ $notification->datalansia->nama_lansia }}</h5>
                                            <p class="text-muted mb-2">
                                                <i class="fas fa-birthday-cake me-1"></i>{{ $notification->datalansia->umur_lansia }} tahun
                                                <span class="mx-2">•</span>
                                                <i class="fas fa-venus-mars me-1"></i>{{ $notification->datalansia->jenis_kelamin }}
                                            </p>
                                            
                                            @if($notification->datalansia->alamat)
                                            <p class="mb-2">
                                                <i class="fas fa-map-marker-alt me-1"></i>
                                                <small class="text-muted">{{ Str::limit($notification->datalansia->alamat, 80) }}</small>
                                            </p>
                                            @endif
                                            
                                            <div class="mt-3">
                                                <a href="{{ route('admin.datalansia.show', $notification->datalansia) }}" 
                                                   class="btn btn-outline-primary btn-sm me-2">
                                                    <i class="fas fa-eye me-1"></i>Lihat Profil
                                                </a>
                                                <a href="{{ route('admin.datalansia.edit', $notification->datalansia) }}" 
                                                   class="btn btn-outline-secondary btn-sm">
                                                    <i class="fas fa-edit me-1"></i>Edit
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    @if($notification->type == 'emergency' && isset($notification->data['emergency_details']))
                                    <div class="mt-4 pt-3 border-top">
                                        <h6 class="text-danger">
                                            <i class="fas fa-exclamation-triangle me-1"></i>Detail Darurat
                                        </h6>
                                        <div class="alert alert-danger small mb-0">
                                            @if(isset($notification->data['emergency_details']['description']))
                                            <p class="mb-2"><strong>Deskripsi:</strong> {{ $notification->data['emergency_details']['description'] }}</p>
                                            @endif
                                            @if(isset($notification->data['emergency_details']['severity']))
                                            <p class="mb-2"><strong>Tingkat Keparahan:</strong> {{ $notification->data['emergency_details']['severity'] }}</p>
                                            @endif
                                            @if(isset($notification->data['emergency_details']['location']))
                                            <p class="mb-0"><strong>Lokasi:</strong> {{ $notification->data['emergency_details']['location'] }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    <!-- Statistik -->
                    @if($notification->data && isset($notification->data['stats']))
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">
                                        <i class="fas fa-chart-bar me-2"></i>Statistik Pengiriman
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h3 class="text-primary">{{ $notification->data['stats']['sent_count'] ?? 0 }}</h3>
                                                <p class="text-muted mb-0">Total Terkirim</p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h3 class="text-success">{{ $notification->data['stats']['read_count'] ?? 0 }}</h3>
                                                <p class="text-muted mb-0">Sudah Dibaca</p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h3 class="text-warning">{{ $notification->data['stats']['clicked_count'] ?? 0 }}</h3>
                                                <p class="text-muted mb-0">Diklik</p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h3 class="text-info">{{ $notification->data['stats']['action_taken_count'] ?? 0 }}</h3>
                                                <p class="text-muted mb-0">Ditindak</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Tombol Aksi -->
                    <div class="border-top pt-4 mt-4">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                            <div class="mb-3 mb-md-0">
                                <form action="{{ route('admin.notifications.destroy', $notification) }}" method="POST" 
                                      class="d-inline delete-form"
                                      data-confirm="Hapus notifikasi ini?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash me-2"></i>Hapus Notifikasi
                                    </button>
                                </form>
                                
                                @if(!$notification->is_archived)
                                <form action="{{ route('admin.notifications.mark-as-archived', $notification) }}" method="POST" class="d-inline ms-2">
                                    @csrf
                                    <button type="submit" class="btn btn-secondary">
                                        <i class="fas fa-archive me-2"></i>Arsipkan
                                    </button>
                                </form>
                                @endif
                            </div>
                            
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('admin.notifications.create', $notification) }}" class="btn btn-outline-primary">
                                    <i class="fas fa-edit me-2"></i>Edit Notifikasi
                                </a>
                                
                                <button type="button" class="btn btn-outline-info" onclick="copyToClipboard('{{ route('admin.notifications.show', $notification) }}')">
                                    <i class="fas fa-link me-2"></i>Salin Link
                                </button>
                                
                                <a href="{{ route('admin.notifications.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-list me-2"></i>Kembali ke Daftar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .activity-card {
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 15px rgba(0,0,0,0.1);
    }
    
    .card-header {
        background-color: var(--light-bg);
        padding: 15px 20px;
        border-bottom: 1px solid #dee2e6;
    }
    
    .card-body {
        padding: 25px;
    }
    
    .avatar {
        flex-shrink: 0;
    }
    
    .list-group-item {
        border: none;
        border-bottom: 1px solid rgba(0,0,0,.125);
    }
    
    .list-group-item:last-child {
        border-bottom: none;
    }
    
    .message-content {
        font-size: 1.1rem;
        line-height: 1.8;
    }
    
    pre {
        font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
        background: transparent;
        border: none;
        font-size: 0.85rem;
        margin: 0;
        padding: 0;
        white-space: pre-wrap;
        word-wrap: break-word;
    }
    
    @media (max-width: 768px) {
        .card-body {
            padding: 15px;
        }
        
        .message-content {
            font-size: 1rem;
        }
        
        .d-flex.flex-wrap.gap-2 {
            gap: 0.5rem !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Track action button click
    function trackActionClick(notificationId) {
        fetch(`/admin/notifications/${notificationId}/track-action-click`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'button_click'
            })
        }).catch(error => {
            console.error('Error tracking click:', error);
        });
    }
    
    // Copy link to clipboard
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            // Show success message
            showToast('success', 'Link berhasil disalin ke clipboard');
        }).catch(err => {
            console.error('Failed to copy:', err);
            showToast('error', 'Gagal menyalin link');
        });
    }
    
    // Delete confirmation
    document.addEventListener('DOMContentLoaded', function() {
        const deleteForms = document.querySelectorAll('.delete-form');
        deleteForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const confirmMessage = this.getAttribute('data-confirm') || 'Apakah Anda yakin?';
                
                if (confirm(confirmMessage)) {
                    this.submit();
                }
            });
        });
    });
    
    // Toast notification
    function showToast(type, message) {
        const toastContainer = document.querySelector('.toast-container') || createToastContainer();
        
        const toastId = 'toast-' + Date.now();
        const bgColor = type === 'success' ? 'bg-success' : 'bg-danger';
        const icon = type === 'success' ? 'check-circle' : 'exclamation-circle';
        
        const toastHTML = `
            <div id="${toastId}" class="toast align-items-center text-white ${bgColor} border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas fa-${icon} me-2"></i>${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;
        
        toastContainer.insertAdjacentHTML('beforeend', toastHTML);
        
        const toastEl = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastEl, {
            autohide: true,
            delay: 3000
        });
        toast.show();
        
        toastEl.addEventListener('hidden.bs.toast', function() {
            this.remove();
        });
    }
    
    function createToastContainer() {
        const container = document.createElement('div');
        container.className = 'toast-container position-fixed top-0 end-0 p-3';
        document.body.appendChild(container);
        return container;
    }
</script>
@endpush
@endsection