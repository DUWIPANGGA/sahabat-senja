@extends('layouts.app')

@section('title', 'Detail Donasi: ' . $donasi->kode_donasi)
@section('page-title', 'Detail Donasi')
@section('icon', 'fas fa-info-circle')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-2" style="color: var(--dark-brown);">Detail Donasi</h2>
            <div class="d-flex align-items-center gap-2">
                <code class="bg-light p-2 rounded">{{ $donasi->kode_donasi }}</code>
                @if($donasi->anonim)
                <span class="badge bg-secondary">Anonim</span>
                @endif
                <span class="text-muted ms-2">
                    <i class="far fa-calendar me-1"></i>{{ $donasi->created_at->format('d M Y H:i') }}
                </span>
            </div>
        </div>
        <div>
            <a href="{{ route('admin.donasi.index') }}" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
            
            @if($donasi->status == 'pending' && $donasi->bukti_pembayaran)
            <button class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#verifyModal">
                <i class="fas fa-check me-2"></i>Verifikasi
            </button>
            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                <i class="fas fa-times me-2"></i>Tolak
            </button>
            @endif
        </div>
    </div>

    <div class="row">
        <!-- Kolom Kiri - Informasi Donasi -->
        <div class="col-md-8">
            <!-- Informasi Utama -->
            <div class="activity-card mb-4">
                <div class="card-header">
                    <h3><i class="fas fa-info-circle me-2"></i>Informasi Donasi</h3>
                    <div class="text-end">
                        <h3 class="mb-0" style="color: var(--primary-color);">
                            Rp {{ number_format($donasi->jumlah, 0, ',', '.') }}
                        </h3>
                        <span class="badge bg-{{ $donasi->status == 'success' ? 'success' : ($donasi->status == 'pending' ? 'warning' : 'danger') }}">
                            {{ strtoupper($donasi->status) }}
                        </span>
                    </div>
                </div>
                <div class="p-3">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Metode Pembayaran</h6>
                            <p class="mb-0">
                                <span class="badge bg-info">
                                    {{ ucwords(str_replace('_', ' ', $donasi->metode_pembayaran)) }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Tanggal</h6>
                            <p class="mb-0">
                                <i class="far fa-calendar me-1"></i>{{ $donasi->created_at->format('d F Y') }}
                                <br>
                                <i class="far fa-clock me-1"></i>{{ $donasi->created_at->format('H:i') }}
                            </p>
                        </div>
                    </div>
                    
                    @if($donasi->doa_harapan)
                    <div class="mb-3">
                        <h6 class="text-muted mb-2">
                            <i class="fas fa-pray me-1"></i>Doa & Harapan
                        </h6>
                        <div class="bg-light p-3 rounded">
                            <i class="fas fa-quote-left me-2 text-primary"></i>
                            {{ $donasi->doa_harapan }}
                        </div>
                    </div>
                    @endif
                    
                    @if($donasi->keterangan)
                    <div class="mb-3">
                        <h6 class="text-muted mb-2">
                            <i class="fas fa-sticky-note me-1"></i>Keterangan
                        </h6>
                        <div class="bg-light p-3 rounded">
                            {{ $donasi->keterangan }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Informasi Donatur -->
            <div class="activity-card mb-4">
                <div class="card-header">
                    <h3><i class="fas fa-user me-2"></i>Informasi Donatur</h3>
                </div>
                <div class="p-3">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted mb-2">Nama Donatur</h6>
                            <p class="mb-0 fw-bold">{{ $donasi->nama_donatur }}</p>
                            @if($donasi->anonim)
                            <small class="text-muted">(Donasi Anonim)</small>
                            @endif
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted mb-2">Email</h6>
                            <p class="mb-0">{{ $donasi->email }}</p>
                        </div>
                    </div>
                    
                    @if($donasi->telepon)
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted mb-2">Telepon</h6>
                            <p class="mb-0">{{ $donasi->telepon }}</p>
                        </div>
                    </div>
                    @endif
                    
                    @if($donasi->user)
                    <div class="alert alert-info">
                        <h6><i class="fas fa-link me-2"></i>Terhubung dengan Akun</h6>
                        <p class="mb-0">Donatur ini terdaftar sebagai: {{ $donasi->user->name }}</p>
                        <small>Email: {{ $donasi->user->email }}</small>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Informasi Kampanye -->
            @if($donasi->kampanye)
            <div class="activity-card mb-4">
                <div class="card-header">
                    <h3><i class="fas fa-hand-holding-heart me-2"></i>Informasi Kampanye</h3>
                </div>
                <div class="p-3">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <h5 class="mb-2">{{ $donasi->kampanye->judul }}</h5>
                            <p class="text-muted mb-3">{{ $donasi->kampanye->deskripsi_singkat }}</p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center p-3 rounded" style="background-color: var(--light-bg);">
                                <h6 class="mb-1">Target Dana</h6>
                                <p class="mb-0 fw-bold">Rp {{ number_format($donasi->kampanye->target_dana, 0, ',', '.') }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3 rounded" style="background-color: var(--light-bg);">
                                <h6 class="mb-1">Terkumpul</h6>
                                <p class="mb-0 fw-bold">Rp {{ number_format($donasi->kampanye->dana_terkumpul, 0, ',', '.') }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3 rounded" style="background-color: var(--light-bg);">
                                <h6 class="mb-1">Progress</h6>
                                <p class="mb-0 fw-bold">{{ number_format($donasi->kampanye->progress, 1) }}%</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <a href="{{ route('admin.kampanye.show', $donasi->kampanye->id) }}" 
                           class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-external-link-alt me-1"></i>Lihat Kampanye
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Kolom Kanan - Aksi dan Bukti -->
        <div class="col-md-4">
            <!-- Status dan Aksi -->
            <div class="activity-card mb-4">
                <div class="card-header">
                    <h3><i class="fas fa-cog me-2"></i>Status & Aksi</h3>
                </div>
                <div class="p-3">
                    <div class="mb-3">
                        <h6 class="text-muted mb-2">Status Saat Ini</h6>
                        <div class="d-flex align-items-center">
                            <span class="badge bg-{{ $donasi->status == 'success' ? 'success' : ($donasi->status == 'pending' ? 'warning' : 'danger') }} p-2">
                                {{ strtoupper($donasi->status) }}
                            </span>
                            
                            @if($donasi->status == 'pending')
                            <span class="badge bg-warning ms-2">Menunggu Verifikasi</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <h6 class="text-muted mb-2">Ubah Status</h6>
                        <form action="{{ route('admin.donasi.updateStatus', $donasi) }}" method="POST">
                            @csrf
                            <div class="input-group mb-2">
                                <select name="status" class="form-select" required>
                                    <option value="pending" {{ $donasi->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="success" {{ $donasi->status == 'success' ? 'selected' : '' }}>Success</option>
                                    <option value="failed" {{ $donasi->status == 'failed' ? 'selected' : '' }}>Failed</option>
                                    <option value="expired" {{ $donasi->status == 'expired' ? 'selected' : '' }}>Expired</option>
                                </select>
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                            <small class="text-muted">Perubahan status akan mempengaruhi dana kampanye</small>
                        </form>
                    </div>
                    
                    <div class="d-grid gap-2">
                        @if($donasi->bukti_pembayaran)
                        <button class="btn btn-info" 
                                onclick="showPaymentProof('{{ asset('storage/' . $donasi->bukti_pembayaran) }}')">
                            <i class="fas fa-receipt me-2"></i>Lihat Bukti Pembayaran
                        </button>
                        @endif
                        
                        <a href="mailto:{{ $donasi->email }}" class="btn btn-outline-primary">
                            <i class="fas fa-envelope me-2"></i>Email Donatur
                        </a>
                        
                        @if($donasi->telepon)
                        <a href="https://wa.me/{{ $donasi->telepon }}" 
                           target="_blank" class="btn btn-outline-success">
                            <i class="fab fa-whatsapp me-2"></i>WhatsApp Donatur
                        </a>
                        @endif
                        
                        <button class="btn btn-outline-secondary" onclick="copyDonationLink()">
                            <i class="fas fa-copy me-2"></i>Copy Kode Donasi
                        </button>
                    </div>
                </div>
            </div>

            <!-- Bukti Pembayaran -->
            @if($donasi->bukti_pembayaran)
            <div class="activity-card mb-4">
                <div class="card-header">
                    <h3><i class="fas fa-receipt me-2"></i>Bukti Pembayaran</h3>
                </div>
                <div class="p-3 text-center">
                    <img src="{{ asset('storage/' . $donasi->bukti_pembayaran) }}" 
                         alt="Bukti Pembayaran" 
                         class="img-fluid rounded mb-3"
                         style="max-height: 200px; cursor: pointer;"
                         onclick="showPaymentProof('{{ asset('storage/' . $donasi->bukti_pembayaran) }}')">
                    
                    <div class="d-grid gap-2">
                        <a href="{{ asset('storage/' . $donasi->bukti_pembayaran) }}" 
                           target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-external-link-alt me-1"></i>Buka di Tab Baru
                        </a>
                        <a href="{{ asset('storage/' . $donasi->bukti_pembayaran) }}" 
                           download class="btn btn-sm btn-outline-success">
                            <i class="fas fa-download me-1"></i>Download
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <!-- Timeline Donasi -->
            <div class="activity-card">
                <div class="card-header">
                    <h3><i class="fas fa-history me-2"></i>Timeline</h3>
                </div>
                <div class="p-3">
                    <div class="timeline">
                        <div class="timeline-item {{ $donasi->status == 'success' ? 'active' : '' }}">
                            <div class="timeline-icon">
                                <i class="fas fa-plus"></i>
                            </div>
                            <div class="timeline-content">
                                <h6>Donasi Dibuat</h6>
                                <p class="text-muted mb-0">{{ $donasi->created_at->format('d M Y H:i') }}</p>
                            </div>
                        </div>
                        
                        @if($donasi->status == 'success')
                        <div class="timeline-item active">
                            <div class="timeline-icon">
                                <i class="fas fa-check"></i>
                            </div>
                            <div class="timeline-content">
                                <h6>Donasi Diverifikasi</h6>
                                <p class="text-muted mb-0">{{ $donasi->updated_at->format('d M Y H:i') }}</p>
                            </div>
                        </div>
                        @endif
                        
                        @if($donasi->status == 'failed' || $donasi->status == 'expired')
                        <div class="timeline-item">
                            <div class="timeline-icon">
                                <i class="fas fa-times"></i>
                            </div>
                            <div class="timeline-content">
                                <h6>Donasi {{ ucfirst($donasi->status) }}</h6>
                                <p class="text-muted mb-0">{{ $donasi->updated_at->format('d M Y H:i') }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Verifikasi -->
<div class="modal fade" id="verifyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.donasi.updateStatus', $donasi) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Verifikasi Donasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin memverifikasi donasi ini?</p>
                    <p class="text-success">Status akan berubah menjadi <strong>SUKSES</strong> dan dana akan ditambahkan ke kampanye.</p>
                    <div class="mb-3">
                        <label for="verification_note" class="form-label">Catatan (Opsional)</label>
                        <textarea class="form-control" id="verification_note" 
                                  name="note" rows="2" placeholder="Catatan verifikasi..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success" name="status" value="success">Verifikasi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Tolak -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.donasi.updateStatus', $donasi) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tolak Donasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menolak donasi ini?</p>
                    <p class="text-danger">Status akan berubah menjadi <strong>GAGAL</strong> dan dana tidak akan ditambahkan ke kampanye.</p>
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">Alasan Penolakan</label>
                        <textarea class="form-control" id="rejection_reason" 
                                  name="reason" rows="3" required placeholder="Alasan penolakan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger" name="status" value="failed">Tolak Donasi</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function showPaymentProof(imageSrc) {
        const modal = new bootstrap.Modal(document.getElementById('proofModal'));
        document.getElementById('proofImage').src = imageSrc;
        document.getElementById('downloadProof').href = imageSrc;
        modal.show();
    }
    
    function copyDonationLink() {
        const code = '{{ $donasi->kode_donasi }}';
        navigator.clipboard.writeText(code).then(() => {
            alert('Kode donasi berhasil disalin: ' + code);
        });
    }
    
    // Inisialisasi
    document.addEventListener('DOMContentLoaded', function() {
        // Update progress bar jika ada kampanye
        const progress = {{ $donasi->kampanye->progress ?? 0 }};
        if (progress > 0) {
            const progressBar = document.querySelector('.progress-bar');
            if (progressBar) {
                progressBar.style.width = `${progress}%`;
            }
        }
    });
</script>

<style>
    /* Timeline Styles */
    .timeline {
        position: relative;
        padding-left: 20px;
    }
    
    .timeline::before {
        content: '';
        position: absolute;
        left: 10px;
        top: 0;
        bottom: 0;
        width: 2px;
        background-color: var(--accent-color);
    }
    
    .timeline-item {
        position: relative;
        margin-bottom: 20px;
    }
    
    .timeline-item:last-child {
        margin-bottom: 0;
    }
    
    .timeline-icon {
        position: absolute;
        left: -22px;
        top: 0;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background-color: var(--accent-color);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 10px;
    }
    
    .timeline-item.active .timeline-icon {
        background-color: var(--primary-color);
    }
    
    .timeline-content {
        padding-left: 10px;
    }
    
    .timeline-content h6 {
        margin-bottom: 5px;
        font-weight: 600;
    }
</style>
@endpush
@endsection