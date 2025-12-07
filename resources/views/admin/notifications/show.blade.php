@extends('layouts.app')

@section('title', 'Detail Notifikasi')
@section('page-title', 'Detail Notifikasi')
@section('icon', 'fas fa-bell')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="activity-card">
                <div class="card-header">
                    <h3><i class="fas fa-eye me-2"></i>Detail Notifikasi</h3>
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
                            
                            $urgencyColors = [
                                'critical' => 'danger',
                                'high' => 'warning',
                                'medium' => 'info',
                                'low' => 'success'
                            ];
                        @endphp
                        
                        <div class="d-flex gap-2 mb-2">
                            <span class="badge bg-{{ $typeColors[$notification->type] ?? 'secondary' }}">
                                <i class="fas 
                                    @if($notification->type == 'emergency') fa-exclamation-triangle
                                    @elseif($notification->type == 'warning') fa-exclamation-circle
                                    @elseif($notification->type == 'info') fa-info-circle
                                    @else fa-cog
                                    @endif
                                me-1"></i>
                                {{ ucfirst($notification->type) }}
                            </span>
                            
                            <span class="badge bg-{{ $urgencyColors[$notification->urgency_level] ?? 'secondary' }}">
                                <i class="fas 
                                    @if($notification->urgency_level == 'critical') fa-fire
                                    @elseif($notification->urgency_level == 'high') fa-exclamation
                                    @elseif($notification->urgency_level == 'medium') fa-info
                                    @else fa-check
                                    @endif
                                me-1"></i>
                                {{ ucfirst($notification->urgency_level) }}
                            </span>
                            
                            <span class="badge bg-secondary">
                                <i class="fas 
                                    @if($notification->category == 'kesehatan') fa-heartbeat
                                    @elseif($notification->category == 'iuran') fa-money-bill-wave
                                    @elseif($notification->category == 'pengobatan') fa-pills
                                    @elseif($notification->category == 'administrasi') fa-file-alt
                                    @else fa-cogs
                                    @endif
                                me-1"></i>
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
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">
                                        <i class="fas fa-comment me-2"></i>Konten Notifikasi
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <h4 class="card-title">{{ $notification->title }}</h4>
                                    <div class="border rounded p-3 bg-light mb-3">
                                        <p class="mb-0" style="white-space: pre-wrap;">{{ $notification->message }}</p>
                                    </div>
                                    
                                    @if($notification->action_url && $notification->action_text)
                                    <div class="mt-3">
                                        <a href="{{ $notification->action_url }}" class="btn btn-primary" target="_blank">
                                            <i class="fas fa-external-link-alt me-1"></i>{{ $notification->action_text }}
                                        </a>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">
                                        <i class="fas fa-info-circle me-2"></i>Informasi
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <span><i class="fas fa-user me-2"></i>Dibuat Oleh:</span>
                                            <span class="fw-bold">{{ $notification->sender->name ?? 'Sistem' }}</span>
                                        </li>
                                        
                                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <span><i class="fas fa-calendar me-2"></i>Dibuat:</span>
                                            <span>{{ $notification->created_at->translatedFormat('d F Y H:i') }}</span>
                                        </li>
                                        
                                        @if($notification->is_read && $notification->read_at)
                                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <span><i class="fas fa-check me-2"></i>Dibaca:</span>
                                            <span>{{ $notification->read_at->translatedFormat('d F Y H:i') }}</span>
                                        </li>
                                        @endif
                                        
                                        @if($notification->scheduled_at)
                                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <span><i class="fas fa-clock me-2"></i>Dijadwalkan:</span>
                                            <span>{{ $notification->scheduled_at->translatedFormat('d F Y H:i') }}</span>
                                        </li>
                                        @endif
                                        
                                        @if($notification->expires_at)
                                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <span><i class="fas fa-hourglass-end me-2"></i>Kadaluarsa:</span>
                                            <span>{{ $notification->expires_at->translatedFormat('d F Y H:i') }}</span>
                                        </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Informasi Penerima -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">
                                        <i class="fas fa-user me-2"></i>Penerima
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @if($notification->user)
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="avatar me-3">
                                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                <i class="fas fa-user"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $notification->user->name }}</h6>
                                            <p class="text-muted mb-0">{{ $notification->user->email }}</p>
                                            <span class="badge bg-info">{{ $notification->user->role }}</span>
                                        </div>
                                    </div>
                                    @else
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        Tidak ada data penerima
                                    </div>
                                    @endif
                                    
                                    @if($notification->data)
                                    <div class="mt-3">
                                        <small class="text-muted">Data Tambahan:</small>
                                        <div class="border rounded p-2 bg-light small">
                                            <pre class="mb-0">{{ json_encode(json_decode($notification->data), JSON_PRETTY_PRINT) }}</pre>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- Informasi Lansia Terkait -->
                        @if($notification->datalansia)
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">
                                        <i class="fas fa-user-friends me-2"></i>Lansia Terkait
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar me-3">
                                            <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                <i class="fas fa-user-friends"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $notification->datalansia->nama_lansia }}</h6>
                                            <p class="text-muted mb-0">{{ $notification->datalansia->umur_lansia }} tahun</p>
                                            <a href="{{ route('admin.datalansia.show', $notification->datalansia) }}" class="btn btn-sm btn-outline-primary mt-2">
                                                <i class="fas fa-eye me-1"></i>Lihat Profil
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    <!-- Tombol Aksi -->
                    <div class="d-flex justify-content-between align-items-center border-top pt-3">
                        <div>
                            <form action="{{ route('admin.notifications.destroy', $notification) }}" method="POST" class="d-inline" 
                                  onsubmit="return confirm('Hapus notifikasi ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash me-2"></i>Hapus
                                </button>
                            </form>
                            
                            @if(!$notification->is_archived)
                            <form action="{{ route('admin.notifications.mark-as-archived', $notification) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-secondary ms-2">
                                    <i class="fas fa-archive me-2"></i>Arsipkan
                                </button>
                            </form>
                            @endif
                        </div>
                        
                        <a href="{{ route('admin.notifications.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-list me-2"></i>Kembali ke Daftar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .avatar {
        flex-shrink: 0;
    }
    
    .list-group-item {
        border: none;
        padding: 0.75rem 0;
    }
    
    pre {
        background: transparent;
        border: none;
        font-size: 0.85rem;
        margin: 0;
        padding: 0;
        white-space: pre-wrap;
        word-wrap: break-word;
    }
</style>
@endpush
@endsection