@extends('layouts.app')

@section('title', 'Kelola Notifikasi')
@section('page-title', 'Notifikasi')
@section('icon', 'fas fa-bell')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-2" style="color: var(--dark-brown);">Kelola Notifikasi</h2>
            <p class="text-muted">Kelola semua notifikasi sistem dan darurat</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.notifications.create') }}" class="btn btn-primary" style="background-color: var(--primary-color);">
                <i class="fas fa-plus-circle me-2"></i>Buat Notifikasi
            </a>
            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#emergencyModal">
                <i class="fas fa-exclamation-triangle me-2"></i>Notifikasi Darurat
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="dashboard-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="card-title">Total Notifikasi</p>
                        <h3 class="card-value">{{ $stats['total'] ?? 0 }}</h3>
                    </div>
                    <div class="card-icon primary">
                        <i class="fas fa-bell"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dashboard-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="card-title">Belum Dibaca</p>
                        <h3 class="card-value" style="color: var(--warning-color);">{{ $stats['unread'] ?? 0 }}</h3>
                    </div>
                    <div class="card-icon warning">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dashboard-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="card-title">Darurat Hari Ini</p>
                        <h3 class="card-value" style="color: var(--danger);">{{ $stats['emergency'] ?? 0 }}</h3>
                    </div>
                    <div class="card-icon" style="background-color: rgba(244, 67, 54, 0.1); color: #f44336;">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dashboard-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="card-title">Kritis</p>
                        <h3 class="card-value">{{ $stats['critical'] ?? 0 }}</h3>
                    </div>
                    <div class="card-icon info">
                        <i class="fas fa-stethoscope"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="filter-card mb-4">
        <form action="{{ route('admin.notifications.index') }}" method="GET">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="type" class="form-label">Tipe</label>
                    <select name="type" id="type" class="form-select">
                        <option value="">Semua Tipe</option>
                        <option value="emergency" {{ request('type') == 'emergency' ? 'selected' : '' }}>Darurat</option>
                        <option value="warning" {{ request('type') == 'warning' ? 'selected' : '' }}>Peringatan</option>
                        <option value="info" {{ request('type') == 'info' ? 'selected' : '' }}>Informasi</option>
                        <option value="system" {{ request('type') == 'system' ? 'selected' : '' }}>Sistem</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="category" class="form-label">Kategori</label>
                    <select name="category" id="category" class="form-select">
                        <option value="">Semua Kategori</option>
                        <option value="kesehatan" {{ request('category') == 'kesehatan' ? 'selected' : '' }}>Kesehatan</option>
                        <option value="iuran" {{ request('category') == 'iuran' ? 'selected' : '' }}>Iuran</option>
                        <option value="pengobatan" {{ request('category') == 'pengobatan' ? 'selected' : '' }}>Pengobatan</option>
                        <option value="administrasi" {{ request('category') == 'administrasi' ? 'selected' : '' }}>Administrasi</option>
                        <option value="sistem" {{ request('category') == 'sistem' ? 'selected' : '' }}>Sistem</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="urgency_level" class="form-label">Urgensi</label>
                    <select name="urgency_level" id="urgency_level" class="form-select">
                        <option value="">Semua Level</option>
                        <option value="low" {{ request('urgency_level') == 'low' ? 'selected' : '' }}>Rendah</option>
                        <option value="medium" {{ request('urgency_level') == 'medium' ? 'selected' : '' }}>Sedang</option>
                        <option value="high" {{ request('urgency_level') == 'high' ? 'selected' : '' }}>Tinggi</option>
                        <option value="critical" {{ request('urgency_level') == 'critical' ? 'selected' : '' }}>Kritis</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="unread" {{ request('status') == 'unread' ? 'selected' : '' }}>Belum Dibaca</option>
                        <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>Sudah Dibaca</option>
                        <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Diarsipkan</option>
                        <option value="action_taken" {{ request('status') == 'action_taken' ? 'selected' : '' }}>Sudah Ditindak</option>
                    </select>
                </div>
                <div class="col-md-8">
                    <label for="search" class="form-label">Pencarian</label>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Cari judul, pesan, atau penerima..." value="{{ request('search') }}">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <div class="d-flex gap-2 w-100">
                        <button type="submit" class="btn btn-primary flex-fill" style="background-color: var(--primary-color);">
                            <i class="fas fa-filter me-2"></i>Filter
                        </button>
                        <a href="{{ route('admin.notifications.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Quick Actions -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <button class="btn btn-sm btn-outline-primary me-2" onclick="markAllAsRead()">
                <i class="fas fa-check-double me-1"></i>Tandai Semua Dibaca
            </button>
            <button class="btn btn-sm btn-outline-warning me-2" data-bs-toggle="modal" data-bs-target="#testModal">
                <i class="fas fa-vial me-1"></i>Test Notifikasi
            </button>
        </div>
        <div>
            <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#bulkDeleteModal" id="bulkDeleteBtn" disabled>
                <i class="fas fa-trash me-1"></i>Hapus Massal
            </button>
        </div>
    </div>

    <!-- Tabel Notifikasi -->
    <div class="activity-card">
        <div class="card-header">
            <h3><i class="fas fa-list me-2"></i>Daftar Notifikasi</h3>
            <span class="badge bg-primary">{{ $notifications->total() }} notifikasi</span>
        </div>
        
        @if($notifications->isEmpty())
        <div class="text-center py-5">
            <i class="fas fa-inbox fa-3x mb-3" style="color: var(--accent-color);"></i>
            <h4 class="mb-2">Belum ada notifikasi</h4>
            <p class="text-muted">Mulai dengan membuat notifikasi baru</p>
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover">
                <thead style="background-color: var(--light-bg);">
                    <tr>
                        <th width="50">
                            <input type="checkbox" id="selectAll" class="form-check-input">
                        </th>
                        <th>Judul</th>
                        <th>Penerima</th>
                        <th>Tipe</th>
                        <th>Urgensi</th>
                        <th>Status</th>
                        <th>Waktu</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($notifications as $notification)
                    <tr class="{{ $notification->is_read ? '' : 'unread-row' }}">
                        <td>
                            <input type="checkbox" name="notification_ids[]" 
                                   value="{{ $notification->id }}" class="form-check-input notification-checkbox">
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <strong>{{ $notification->title }}</strong>
                                <small class="text-muted">{{ Str::limit($notification->message, 50) }}</small>
                                @if($notification->datalansia)
                                <small>
                                    <i class="fas fa-user me-1"></i>{{ $notification->datalansia->nama_lansia }}
                                </small>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($notification->user)
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" 
                                     style="width: 30px; height: 30px; font-size: 12px;">
                                    {{ substr($notification->user->name, 0, 1) }}
                                </div>
                                <div class="ms-2">
                                    <strong class="d-block">{{ $notification->user->name }}</strong>
                                    <small class="text-muted">{{ $notification->user->role }}</small>
                                </div>
                            </div>
                            @elseif($notification->broadcast_type == 'all')
                            <span class="badge bg-info">
                                <i class="fas fa-broadcast-tower me-1"></i>Semua Pengguna
                            </span>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $typeColors = [
                                    'emergency' => 'danger',
                                    'warning' => 'warning',
                                    'info' => 'info',
                                    'system' => 'secondary'
                                ];
                                $typeIcons = [
                                    'emergency' => 'exclamation-triangle',
                                    'warning' => 'exclamation-circle',
                                    'info' => 'info-circle',
                                    'system' => 'cog'
                                ];
                            @endphp
                            <span class="badge bg-{{ $typeColors[$notification->type] ?? 'secondary' }}">
                                <i class="fas fa-{{ $typeIcons[$notification->type] ?? 'bell' }} me-1"></i>
                                {{ ucfirst($notification->type) }}
                            </span>
                        </td>
                        <td>
                            @php
                                $urgencyColors = [
                                    'low' => 'success',
                                    'medium' => 'info',
                                    'high' => 'warning',
                                    'critical' => 'danger'
                                ];
                            @endphp
                            <span class="badge bg-{{ $urgencyColors[$notification->urgency_level] ?? 'info' }}">
                                {{ ucfirst($notification->urgency_level) }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex flex-column gap-1">
                                @if(!$notification->is_read)
                                <span class="badge bg-warning">Belum Dibaca</span>
                                @endif
                                @if($notification->is_archived)
                                <span class="badge bg-secondary">Diarsipkan</span>
                                @endif
                                @if($notification->is_action_taken)
                                <span class="badge bg-success">Sudah Ditindak</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <small class="d-block">{{ $notification->created_at->format('d M Y') }}</small>
                            <small class="text-muted">{{ $notification->created_at->format('H:i') }}</small>
                            @if($notification->read_at)
                            <br>
                            <small class="text-success">Dibaca: {{ $notification->read_at->format('H:i') }}</small>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.notifications.show', $notification) }}" 
                                   class="btn btn-sm btn-info" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                @if(!$notification->is_read)
                                <button class="btn btn-sm btn-success" title="Tandai Dibaca"
                                        onclick="markAsRead('{{ $notification->id }}')">
                                    <i class="fas fa-check"></i>
                                </button>
                                @endif
                                
                                @if(!$notification->is_archived)
                                <button class="btn btn-sm btn-warning" title="Arsipkan"
                                        onclick="markAsArchived('{{ $notification->id }}')">
                                    <i class="fas fa-archive"></i>
                                </button>
                                @endif
                                
                                <button class="btn btn-sm btn-danger" title="Hapus"
                                        onclick="deleteNotification('{{ $notification->id }}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div>
                <p class="mb-0 text-muted">
                    Menampilkan {{ $notifications->firstItem() ?? 0 }} - {{ $notifications->lastItem() ?? 0 }} dari {{ $notifications->total() }} notifikasi
                </p>
            </div>
            <div>
                {{ $notifications->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Modal Notifikasi Darurat -->
<div class="modal fade" id="emergencyModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.notifications.send-emergency') }}" method="POST" id="emergencyForm">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle me-2"></i>Kirim Notifikasi Darurat
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>PERHATIAN:</strong> Notifikasi darurat akan dikirim ke semua penerima yang dipilih dengan prioritas tertinggi.
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="emergency_datalansia_id" class="form-label">Lansia <span class="text-danger">*</span></label>
                            <select class="form-select" id="emergency_datalansia_id" name="datalansia_id" required>
                                <option value="">Pilih Lansia...</option>
                                @foreach($lansias ?? [] as $lansia)
                                <option value="{{ $lansia->id }}">{{ $lansia->nama_lansia }} ({{ $lansia->umur_lansia }} tahun)</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="emergency_type" class="form-label">Jenis Darurat <span class="text-danger">*</span></label>
                            <select class="form-select" id="emergency_type" name="emergency_type" required>
                                <option value="medical_emergency">Darurat Medis</option>
                                <option value="hospitalization">Dirujuk ke Rumah Sakit</option>
                                <option value="critical_condition">Kondisi Kritis</option>
                                <option value="accident">Kecelakaan</option>
                                <option value="missing_person">Lansia Hilang</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="severity_level" class="form-label">Tingkat Keparahan <span class="text-danger">*</span></label>
                        <select class="form-select" id="severity_level" name="severity_level" required>
                            <option value="level_1">Level 1 - Ringan (Observasi)</option>
                            <option value="level_2">Level 2 - Sedang (Perhatian)</option>
                            <option value="level_3" selected>Level 3 - Serius (Tindakan Cepat)</option>
                            <option value="level_4">Level 4 - Kritis (Tindakan Segera)</option>
                            <option value="level_5">Level 5 - Darurat (Ancaman Jiwa)</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi Keadaan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="description" name="description" rows="3" 
                                  placeholder="Jelaskan kondisi darurat secara detail..." required></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="location" class="form-label">Lokasi Kejadian</label>
                            <input type="text" class="form-control" id="location" name="location" 
                                   placeholder="Contoh: Ruang 101, Lantai 1">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="hospital_name" class="form-label">Nama Rumah Sakit</label>
                            <input type="text" class="form-control" id="hospital_name" name="hospital_name" 
                                   placeholder="Jika dirujuk ke RS">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="contact_person" class="form-label">Nama Kontak Darurat</label>
                            <input type="text" class="form-control" id="contact_person" name="contact_person">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="contact_number" class="form-label">Nomor Kontak Darurat</label>
                            <input type="text" class="form-control" id="contact_number" name="contact_number">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Kirim Ke <span class="text-danger">*</span></label>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="send_to[]" 
                                           value="family" id="send_family" checked>
                                    <label class="form-check-label" for="send_family">
                                        <i class="fas fa-users me-1"></i> Keluarga Lansia
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="send_to[]" 
                                           value="emergency_contacts" id="send_contacts" checked>
                                    <label class="form-check-label" for="send_contacts">
                                        <i class="fas fa-address-book me-1"></i> Kontak Darurat
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="send_to[]" 
                                           value="nurses" id="send_nurses" checked>
                                    <label class="form-check-label" for="send_nurses">
                                        <i class="fas fa-user-md me-1"></i> Semua Perawat
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="send_to[]" 
                                           value="admins" id="send_admins" checked>
                                    <label class="form-check-label" for="send_admins">
                                        <i class="fas fa-user-shield me-1"></i> Semua Admin
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger" id="emergencySubmitBtn">
                        <i class="fas fa-paper-plane me-2"></i>Kirim Notifikasi Darurat
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Test Notifikasi -->
<div class="modal fade" id="testModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.notifications.send-test') }}" method="POST" id="testForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-vial me-2"></i>Test Notifikasi
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="test_user_id" class="form-label">Penerima Test <span class="text-danger">*</span></label>
                        <select class="form-select" id="test_user_id" name="user_id" required>
                            <option value="">Pilih User...</option>
                            @foreach($users ?? [] as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->role }})</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="test_type" class="form-label">Tipe Notifikasi <span class="text-danger">*</span></label>
                        <select class="form-select" id="test_type" name="type" required>
                            <option value="emergency">Darurat</option>
                            <option value="warning">Peringatan</option>
                            <option value="info">Informasi</option>
                            <option value="system">Sistem</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="test_title" class="form-label">Judul <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="test_title" name="title" 
                               value="Ini adalah notifikasi test" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="test_message" class="form-label">Pesan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="test_message" name="message" rows="3" required>Ini adalah pesan notifikasi test dari sistem. Harap abaikan jika Anda menerima pesan ini.</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="testSubmitBtn">
                        <i class="fas fa-paper-plane me-2"></i>Kirim Test
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Bulk Delete -->
<div class="modal fade" id="bulkDeleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.notifications.bulk-delete') }}" method="POST" id="bulkDeleteForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Hapus Notifikasi Massal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus notifikasi yang dipilih?</p>
                    <p class="text-danger">Notifikasi yang dihapus tidak dapat dikembalikan!</p>
                    <div id="selectedCount" class="fw-bold">0 notifikasi dipilih</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Hapus yang Dipilih</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    .unread-row {
        background-color: rgba(255, 193, 7, 0.1) !important;
        border-left: 3px solid var(--warning-color);
    }
    
    .dashboard-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }
    
    .dashboard-card:hover {
        transform: translateY(-5px);
    }
    
    .card-icon {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }
    
    .card-icon.primary {
        background-color: rgba(var(--primary-rgb), 0.1);
        color: var(--primary-color);
    }
    
    .card-icon.warning {
        background-color: rgba(255, 193, 7, 0.1);
        color: var(--warning-color);
    }
    
    .card-icon.info {
        background-color: rgba(23, 162, 184, 0.1);
        color: #17a2b8;
    }
    
    .filter-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .activity-card {
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .activity-card .card-header {
        background-color: var(--light-bg);
        padding: 15px 20px;
        border-bottom: 1px solid #dee2e6;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize
        updateSelectedCount();
        
        // Select all checkboxes
        const selectAll = document.getElementById('selectAll');
        if (selectAll) {
            selectAll.addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.notification-checkbox');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateSelectedCount();
            });
        }
        
        // Emergency form submission
        const emergencyForm = document.getElementById('emergencyForm');
        if (emergencyForm) {
            emergencyForm.addEventListener('submit', function(e) {
                const submitBtn = document.getElementById('emergencySubmitBtn');
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mengirim...';
                submitBtn.disabled = true;
            });
        }
        
        // Test form submission
        const testForm = document.getElementById('testForm');
        if (testForm) {
            testForm.addEventListener('submit', function(e) {
                const submitBtn = document.getElementById('testSubmitBtn');
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mengirim...';
                submitBtn.disabled = true;
            });
        }
        
        // Event listener for individual checkboxes
        document.querySelectorAll('.notification-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectedCount);
        });
    });
    
    // Update selected count
    function updateSelectedCount() {
        const selected = document.querySelectorAll('.notification-checkbox:checked').length;
        const countElement = document.getElementById('selectedCount');
        const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
        
        if (countElement) {
            countElement.textContent = selected + ' notifikasi dipilih';
        }
        
        if (bulkDeleteBtn) {
            bulkDeleteBtn.disabled = selected === 0;
        }
    }
    
    // Mark as read
    async function markAsRead(notificationId) {
        if (confirm('Tandai notifikasi ini sebagai sudah dibaca?')) {
            try {
                const response = await fetch(`/admin/notifications/${notificationId}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showToast('success', 'Notifikasi ditandai sebagai sudah dibaca');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    throw new Error(data.message || 'Terjadi kesalahan');
                }
            } catch (error) {
                showToast('error', error.message || 'Gagal memperbarui status');
                console.error('Error:', error);
            }
        }
    }
    
    // Mark all as read
    async function markAllAsRead() {
        const selectedIds = Array.from(document.querySelectorAll('.notification-checkbox:checked'))
            .map(checkbox => checkbox.value);
            
        if (selectedIds.length === 0) {
            alert('Pilih notifikasi terlebih dahulu!');
            return;
        }
        
        if (confirm(`Tandai ${selectedIds.length} notifikasi sebagai sudah dibaca?`)) {
            try {
                const response = await fetch('/admin/notifications/bulk-read', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ notification_ids: selectedIds })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showToast('success', `${selectedIds.length} notifikasi ditandai sebagai sudah dibaca`);
                    setTimeout(() => location.reload(), 1000);
                } else {
                    throw new Error(data.message || 'Terjadi kesalahan');
                }
            } catch (error) {
                showToast('error', error.message || 'Gagal memperbarui status');
                console.error('Error:', error);
            }
        }
    }
    
    // Mark as archived
    async function markAsArchived(notificationId) {
        if (confirm('Arsipkan notifikasi ini?')) {
            try {
                const response = await fetch(`/admin/notifications/${notificationId}/archive`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showToast('success', 'Notifikasi diarsipkan');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    throw new Error(data.message || 'Terjadi kesalahan');
                }
            } catch (error) {
                showToast('error', error.message || 'Gagal mengarsipkan');
                console.error('Error:', error);
            }
        }
    }
    
    // Delete notification
    async function deleteNotification(notificationId) {
        if (confirm('Apakah Anda yakin ingin menghapus notifikasi ini?')) {
            try {
                const response = await fetch(`/admin/notifications/${notificationId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showToast('success', 'Notifikasi berhasil dihapus');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    throw new Error(data.message || 'Terjadi kesalahan');
                }
            } catch (error) {
                showToast('error', error.message || 'Gagal menghapus notifikasi');
                console.error('Error:', error);
            }
        }
    }
    
    // Bulk delete
    document.getElementById('bulkDeleteForm')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const selectedIds = Array.from(document.querySelectorAll('.notification-checkbox:checked'))
            .map(checkbox => checkbox.value);
        
        if (selectedIds.length === 0) return;
        
        const formData = new FormData(this);
        formData.append('notification_ids', selectedIds.join(','));
        
        try {
            const response = await fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                showToast('success', `${selectedIds.length} notifikasi berhasil dihapus`);
                setTimeout(() => location.reload(), 1000);
            } else {
                throw new Error(data.message || 'Terjadi kesalahan');
            }
        } catch (error) {
            showToast('error', error.message || 'Gagal menghapus notifikasi');
            console.error('Error:', error);
        }
    });
    
    // Toast notification function
    function showToast(type, message) {
        // Create toast container if not exists
        let toastContainer = document.querySelector('.toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
            document.body.appendChild(toastContainer);
        }
        
        const toastId = 'toast-' + Date.now();
        const bgColor = type === 'success' ? 'bg-success' : type === 'error' ? 'bg-danger' : 'bg-info';
        const icon = type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle';
        
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
</script>
@endpush
@endsection