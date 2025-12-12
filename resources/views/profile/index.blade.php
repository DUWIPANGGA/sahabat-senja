@extends('layouts.app')

@section('title', 'Profile')
@section('page-title', 'Profile')
@section('icon', 'fas fa-user')

@section('content')
    <div class="content-container">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-4">
                <!-- Profile Card -->
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-body p-4 text-center">
                        <!-- Profile Photo -->
                        <div class="profile-photo-container mb-4">
                            @if($user->foto_profil)
                                <img src="{{ Storage::url($user->foto_profil) }}" 
                                     alt="Profile Photo" 
                                     class="profile-photo rounded-circle shadow">
                            @else
                                <div class="profile-default rounded-circle d-flex align-items-center justify-content-center mx-auto shadow">
                                    <i class="fas fa-user fa-3x text-white"></i>
                                </div>
                            @endif
                            
                            <!-- Upload Button -->
                            <div class="mt-4">
                                <button class="btn btn-outline-primary btn-sm me-2" onclick="document.getElementById('uploadPhoto').click()">
                                    <i class="fas fa-upload me-1"></i>Upload Foto
                                </button>
                                @if($user->foto_profil)
                                    <button class="btn btn-outline-danger btn-sm" onclick="deletePhoto()">
                                        <i class="fas fa-trash me-1"></i>Hapus Foto
                                    </button>
                                @endif
                            </div>
                            
                            <!-- Hidden File Input -->
                            <form action="{{ route('profile.upload-photo') }}" method="POST" enctype="multipart/form-data" id="uploadForm" style="display: none;">
                                @csrf
                                <input type="file" name="foto_profil" id="uploadPhoto" accept="image/*" onchange="uploadPhoto()">
                            </form>
                        </div>

                        <!-- User Info -->
                        <h4 class="mb-2">{{ $user->name }}</h4>
                        <div class="badge bg-primary mb-3 px-3 py-2">
                            <i class="fas fa-user-tag me-1"></i>{{ ucfirst($user->role) }}
                        </div>
                        
                        <!-- Stats -->
                        <div class="row g-3 mt-4">
                            <div class="col-6">
                                <div class="stat-card p-3 rounded bg-light">
                                    <div class="stat-icon mb-2">
                                        <i class="fas fa-calendar-check text-primary"></i>
                                    </div>
                                    <div class="stat-number fw-bold">{{ $user->created_at->format('d M Y') }}</div>
                                    <div class="stat-label text-muted small">Bergabung</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-card p-3 rounded bg-light">
                                    <div class="stat-icon mb-2">
                                        <i class="fas fa-user-edit text-success"></i>
                                    </div>
                                    <div class="stat-number fw-bold">{{ $user->updated_at->format('d M Y') }}</div>
                                    <div class="stat-label text-muted small">Terakhir Update</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h6 class="mb-3"><i class="fas fa-bolt me-2"></i>Quick Actions</h6>
                        <div class="list-group list-group-flush">
                            <a href="{{ route('profile.edit') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                                <i class="fas fa-edit text-primary me-3"></i>
                                <div>
                                    <div class="fw-medium">Edit Profile</div>
                                    <small class="text-muted">Update informasi pribadi</small>
                                </div>
                            </a>
                            <a href="{{ route('profile.edit-password') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                                <i class="fas fa-key text-warning me-3"></i>
                                <div>
                                    <div class="fw-medium">Ganti Password</div>
                                    <small class="text-muted">Update kata sandi</small>
                                </div>
                            </a>
                            @if($user->isPerawat())
                            <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                                <i class="fas fa-heartbeat text-danger me-3"></i>
                                <div>
                                    <div class="fw-medium">Data Lansia</div>
                                    <small class="text-muted">Lihat data lansia yang dihandle</small>
                                </div>
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <!-- Profile Details -->
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Profile</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <!-- Personal Info -->
                            <div class="col-md-6">
                                <h6 class="mb-3 text-primary"><i class="fas fa-user me-2"></i>Informasi Pribadi</h6>
                                <div class="profile-detail mb-3">
                                    <div class="detail-label">Nama Lengkap</div>
                                    <div class="detail-value">{{ $user->name }}</div>
                                </div>
                                <div class="profile-detail mb-3">
                                    <div class="detail-label">Email</div>
                                    <div class="detail-value">{{ $user->email }}</div>
                                </div>
                                <div class="profile-detail mb-3">
                                    <div class="detail-label">Role</div>
                                    <div class="detail-value">
                                        <span class="badge bg-primary">{{ ucfirst($user->role) }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Info -->
                            <div class="col-md-6">
                                <h6 class="mb-3 text-primary"><i class="fas fa-address-book me-2"></i>Kontak & Alamat</h6>
                                <div class="profile-detail mb-3">
                                    <div class="detail-label">Nomor Telepon</div>
                                    <div class="detail-value">
                                        {{ $user->no_telepon ?? '<span class="text-muted">Belum diisi</span>' }}
                                    </div>
                                </div>
                                <div class="profile-detail mb-3">
                                    <div class="detail-label">Alamat</div>
                                    <div class="detail-value">
                                        {{ $user->alamat ?? '<span class="text-muted">Belum diisi</span>' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex gap-2 mt-4 pt-3 border-top">
                            <a href="{{ route('profile.edit') }}" class="btn btn-primary px-4 py-2">
                                <i class="fas fa-edit me-2"></i>Edit Profile
                            </a>
                            <a href="{{ route('profile.edit-password') }}" class="btn btn-outline-warning px-4 py-2">
                                <i class="fas fa-key me-2"></i>Ganti Password
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity (jika ada) -->
                @if($user->isPerawat() || $user->isAdmin())
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0"><i class="fas fa-history me-2"></i>Aktivitas Terakhir</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="timeline">
                            @php
                                $activities = [];
                                if($user->isPerawat()) {
                                    $activities = $user->kondisiInput()->latest()->take(5)->get();
                                } elseif($user->isAdmin()) {
                                    // Admin activities
                                }
                            @endphp

                            @forelse($activities as $activity)
                            <div class="timeline-item mb-3">
                                <div class="timeline-marker">
                                    <i class="fas fa-circle text-primary"></i>
                                </div>
                                <div class="timeline-content">
                                    <div class="d-flex justify-content-between">
                                        <h6 class="mb-1">{{ $activity->datalansia->nama ?? 'Data Lansia' }}</h6>
                                        <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-1 text-muted">Memperbarui data kondisi lansia</p>
                                    <span class="badge bg-light text-dark">{{ $activity->kondisi }}</span>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-4">
                                <i class="fas fa-history fa-2x mb-3 text-muted"></i>
                                <p class="text-muted mb-0">Belum ada aktivitas</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Delete Photo Modal -->
    <div class="modal fade" id="deletePhotoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fas fa-trash me-2"></i>Hapus Foto Profil</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('profile.delete-photo') }}" method="POST" id="deletePhotoForm">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <p>Apakah Anda yakin ingin menghapus foto profil ini?</p>
                        <p class="text-muted small">Foto yang dihapus tidak dapat dikembalikan.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .profile-photo-container {
        position: relative;
    }
    
    .profile-photo {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border: 5px solid #fff;
    }
    
    .profile-default {
        width: 150px;
        height: 150px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .stat-card {
        transition: transform 0.3s;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
    }
    
    .stat-icon {
        font-size: 1.5rem;
    }
    
    .stat-number {
        font-size: 1.1rem;
    }
    
    .stat-label {
        font-size: 0.85rem;
    }
    
    .profile-detail {
        padding: 0.75rem;
        border-radius: 8px;
        background: #f8f9fa;
    }
    
    .detail-label {
        font-size: 0.85rem;
        color: #6c757d;
        margin-bottom: 0.25rem;
    }
    
    .detail-value {
        font-weight: 500;
        color: #212529;
    }
    
    .list-group-item {
        border: none;
        padding: 1rem 0;
    }
    
    .list-group-item:not(:last-child) {
        border-bottom: 1px solid #f0f0f0 !important;
    }
    
    .timeline {
        position: relative;
        padding-left: 2rem;
    }
    
    .timeline::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e9ecef;
    }
    
    .timeline-item {
        position: relative;
        padding-left: 1.5rem;
    }
    
    .timeline-marker {
        position: absolute;
        left: -0.5rem;
        top: 0;
        background: white;
        padding: 2px;
    }
    
    .timeline-content {
        background: white;
        border-radius: 8px;
        padding: 1rem;
        border: 1px solid #f0f0f0;
    }
</style>
@endpush

@push('scripts')
<script>
    function deletePhoto() {
        const modal = new bootstrap.Modal(document.getElementById('deletePhotoModal'));
        modal.show();
    }
    
    function uploadPhoto() {
        document.getElementById('uploadForm').submit();
    }
    
    // Preview photo before upload
    document.getElementById('uploadPhoto')?.addEventListener('change', function(e) {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                // Create preview or show confirmation modal
                if (confirm('Upload foto profil ini?')) {
                    document.getElementById('uploadForm').submit();
                }
            }
            reader.readAsDataURL(this.files[0]);
        }
    });
    
    // Auto submit delete form
    document.getElementById('deletePhotoForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        this.submit();
    });
</script>
@endpush