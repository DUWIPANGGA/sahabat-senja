@extends('layouts.app')

@section('title', 'Kelola Donasi')
@section('page-title', 'Kelola Donasi')
@section('icon', 'fas fa-donate')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-2" style="color: var(--dark-brown);">Kelola Donasi</h2>
            <p class="text-muted">Kelola semua donasi yang masuk dari kampanye</p>
        </div>
        <div class="btn-group">
            <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-file-excel me-2"></i>Export Excel
            </button>
            <ul class="dropdown-menu">
                <li>
                    <a class="dropdown-item" href="{{ route('admin.donasi.export') }}">
                        <i class="fas fa-download me-2"></i>Export Semua Data
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#exportFilterModal">
                        <i class="fas fa-filter me-2"></i>Export dengan Filter
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('admin.donasi.export-summary') }}">
                        <i class="fas fa-chart-pie me-2"></i>Export Ringkasan
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="dashboard-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="card-title">Total Donasi</p>
                        <h3 class="card-value">{{ $stats['total'] ?? 0 }}</h3>
                    </div>
                    <div class="card-icon primary">
                        <i class="fas fa-hand-holding-usd"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dashboard-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="card-title">Donasi Sukses</p>
                        <h3 class="card-value" style="color: var(--success-color);">{{ $stats['success'] ?? 0 }}</h3>
                    </div>
                    <div class="card-icon success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dashboard-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="card-title">Menunggu</p>
                        <h3 class="card-value" style="color: var(--warning-color);">{{ $stats['pending'] ?? 0 }}</h3>
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
                        <p class="card-title">Total Dana</p>
                        <h3 class="card-value">Rp {{ number_format($stats['total_amount'] ?? 0, 0, ',', '.') }}</h3>
                    </div>
                    <div class="card-icon info">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="dashboard-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="card-title">Donasi Gagal</p>
                        <h3 class="card-value" style="color: var(--danger-color);">{{ $stats['failed'] ?? 0 }}</h3>
                    </div>
                    <div class="card-icon danger">
                        <i class="fas fa-times-circle"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dashboard-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="card-title">Kadaluarsa</p>
                        <h3 class="card-value" style="color: var(--secondary-color);">{{ $stats['expired'] ?? 0 }}</h3>
                    </div>
                    <div class="card-icon secondary">
                        <i class="fas fa-hourglass-end"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dashboard-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="card-title">Hari Ini</p>
                        <h3 class="card-value">Rp {{ number_format($stats['today_success'] ?? 0, 0, ',', '.') }}</h3>
                    </div>
                    <div class="card-icon info">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dashboard-card">
                <div class="d-flex justifycontent-between align-items-center">
                    <div>
                        <p class="card-title">Bulan Ini</p>
                        <h3 class="card-value">Rp {{ number_format($stats['this_month_success'] ?? 0, 0, ',', '.') }}</h3>
                    </div>
                    <div class="card-icon primary">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Sederhana -->
    <div class="filter-card mb-4">
        <form action="{{ route('admin.donasi.index') }}" method="GET">
            <div class="row g-3">
                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>Success</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="metode_pembayaran" class="form-label">Metode</label>
                    <select name="metode_pembayaran" id="metode_pembayaran" class="form-select">
                        <option value="">Semua Metode</option>
                        <option value="transfer_bank" {{ request('metode_pembayaran') == 'transfer_bank' ? 'selected' : '' }}>Transfer Bank</option>
                        <option value="ewallet" {{ request('metode_pembayaran') == 'ewallet' ? 'selected' : '' }}>E-Wallet</option>
                        <option value="qris" {{ request('metode_pembayaran') == 'qris' ? 'selected' : '' }}>QRIS</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="sort" class="form-label">Urutkan</label>
                    <select name="sort" id="sort" class="form-select">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Terbaru</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                        <option value="amount_asc" {{ request('sort') == 'amount_asc' ? 'selected' : '' }}>Jumlah (Kecil→Besar)</option>
                        <option value="amount_desc" {{ request('sort') == 'amount_desc' ? 'selected' : '' }}>Jumlah (Besar→Kecil)</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="search" class="form-label">Pencarian</label>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Cari nama donatur, kode donasi, atau email..." 
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <div class="d-flex gap-2 w-100">
                        <button type="submit" class="btn btn-primary flex-fill" style="background-color: var(--primary-color);">
                            <i class="fas fa-filter me-2"></i>Filter
                        </button>
                        <a href="{{ route('admin.donasi.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Tabel Donasi -->
    <div class="activity-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3><i class="fas fa-list me-2"></i>Daftar Donasi</h3>
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted">{{ $donasis->total() }} donasi</span>
            </div>
        </div>
        
        @if($donasis->isEmpty())
        <div class="text-center py-5">
            <i class="fas fa-inbox fa-3x mb-3" style="color: var(--accent-color);"></i>
            <h4 class="mb-2">Belum ada donasi</h4>
            <p class="text-muted">Belum ada donasi yang masuk</p>
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover">
                <thead style="background-color: var(--light-bg);">
                    <tr>
                        <th width="50">#</th>
                        <th>Kode Donasi</th>
                        <th>Donatur</th>
                        <th>Kampanye</th>
                        <th>Jumlah</th>
                        <th>Metode</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($donasis as $donasi)
                    <tr>
                        <td>{{ $loop->iteration + ($donasis->perPage() * ($donasis->currentPage() - 1)) }}</td>
                        <td>
                            <code>{{ $donasi->kode_donasi }}</code>
                            @if($donasi->anonim)
                            <span class="badge bg-secondary ms-1">Anonim</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <strong>{{ $donasi->nama_donatur }}</strong>
                                <small class="text-muted">{{ $donasi->email }}</small>
                                @if($donasi->telepon)
                                <small class="text-muted">{{ $donasi->telepon }}</small>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($donasi->kampanye)
                            <div class="d-flex flex-column">
                                <strong>{{ Str::limit($donasi->kampanye->judul, 30) }}</strong>
                                <small class="text-muted">Target: Rp {{ number_format($donasi->kampanye->target_dana, 0, ',', '.') }}</small>
                            </div>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <strong>Rp {{ number_format($donasi->jumlah, 0, ',', '.') }}</strong>
                        </td>
                        <td>
                            <span class="badge bg-info">
                                {{ ucwords(str_replace('_', ' ', $donasi->metode_pembayaran)) }}
                            </span>
                        </td>
                        <td>
                            @php
                                $statusColors = [
                                    'pending' => 'warning',
                                    'success' => 'success',
                                    'failed' => 'danger',
                                    'expired' => 'secondary'
                                ];
                                $statusLabels = [
                                    'pending' => 'Menunggu',
                                    'success' => 'Sukses',
                                    'failed' => 'Gagal',
                                    'expired' => 'Kadaluarsa'
                                ];
                            @endphp
                            <span class="badge bg-{{ $statusColors[$donasi->status] ?? 'secondary' }}">
                                {{ $statusLabels[$donasi->status] ?? $donasi->status }}
                            </span>
                        </td>
                        <td>
                            <small class="d-block">{{ $donasi->created_at->format('d M Y') }}</small>
                            <small class="text-muted">{{ $donasi->created_at->format('H:i') }}</small>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.donasi.show', $donasi) }}" 
                                   class="btn btn-sm btn-info" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                @if($donasi->status == 'pending' && $donasi->bukti_pembayaran)
                                <button class="btn btn-sm btn-success" title="Verifikasi"
                                        onclick="verifyDonation({{ $donasi->id }})">
                                    <i class="fas fa-check"></i>
                                </button>
                                @endif
                                
                                @if($donasi->status == 'pending')
                                <button class="btn btn-sm btn-danger" title="Tolak"
                                        onclick="rejectDonation({{ $donasi->id }})">
                                    <i class="fas fa-times"></i>
                                </button>
                                @endif
                                
                                @if($donasi->bukti_pembayaran)
                                <button class="btn btn-sm btn-warning" title="Lihat Bukti"
                                        onclick="showPaymentProof('{{ asset('storage/' . $donasi->bukti_pembayaran) }}')">
                                    <i class="fas fa-receipt"></i>
                                </button>
                                @endif
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
                    Menampilkan {{ $donasis->firstItem() }} - {{ $donasis->lastItem() }} dari {{ $donasis->total() }} donasi
                </p>
            </div>
            <div>
                {{ $donasis->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Modal Export dengan Filter -->
<div class="modal fade" id="exportFilterModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.donasi.export-filtered') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-filter me-2"></i>Export Excel dengan Filter
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="export_type" class="form-label">Tipe Data</label>
                        <select class="form-select" id="export_type" name="export_type" required>
                            <option value="all">Semua Data</option>
                            <option value="success">Sukses Saja</option>
                            <option value="pending">Pending Saja</option>
                            <option value="failed">Gagal Saja</option>
                            <option value="expired">Kadaluarsa Saja</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="export_start_date" class="form-label">Dari Tanggal</label>
                            <input type="date" class="form-control" id="export_start_date" name="start_date">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="export_end_date" class="form-label">Sampai Tanggal</label>
                            <input type="date" class="form-control" id="export_end_date" name="end_date">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-download me-2"></i>Export Excel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Lihat Bukti Pembayaran -->
<div class="modal fade" id="proofModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bukti Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="proofImage" src="" alt="Bukti Pembayaran" class="img-fluid rounded">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <a href="#" id="downloadProof" class="btn btn-primary" download>
                    <i class="fas fa-download me-2"></i>Download
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Modal Verifikasi Donasi -->
<div class="modal fade" id="verifyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="verifyForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Verifikasi Donasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin memverifikasi donasi ini?</p>
                    <p class="text-success">Status akan berubah menjadi <strong>SUKSES</strong></p>
                    <div class="mb-3">
                        <label for="verification_note" class="form-label">Catatan (Opsional)</label>
                        <textarea class="form-control" id="verification_note" 
                                  name="note" rows="2" placeholder="Catatan verifikasi..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Verifikasi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Tolak Donasi -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tolak Donasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menolak donasi ini?</p>
                    <p class="text-danger">Status akan berubah menjadi <strong>GAGAL</strong></p>
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">Alasan Penolakan</label>
                        <textarea class="form-control" id="rejection_reason" 
                                  name="reason" rows="3" required placeholder="Alasan penolakan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak Donasi</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function showPaymentProof(imageSrc) {
        document.getElementById('proofImage').src = imageSrc;
        document.getElementById('downloadProof').href = imageSrc;
        new bootstrap.Modal(document.getElementById('proofModal')).show();
    }
    
    function verifyDonation(donationId) {
    // Update form action dengan cara yang benar
    const form = document.getElementById('verifyForm');
    form.action = `/admin/donasi/${donationId}/status`;
    
    // Pastikan ada input hidden untuk status
    let statusInput = form.querySelector('input[name="status"]');
    if (!statusInput) {
        statusInput = document.createElement('input');
        statusInput.type = 'hidden';
        statusInput.name = 'status';
        form.appendChild(statusInput);
    }
    statusInput.value = 'success';
    
    // Tampilkan modal
    new bootstrap.Modal(document.getElementById('verifyModal')).show();
}

function rejectDonation(donationId) {
    // Update form action dengan cara yang benar
    const form = document.getElementById('rejectForm');
    form.action = `/admin/donasi/${donasi}/status`;
    
    // Pastikan ada input hidden untuk status
    let statusInput = form.querySelector('input[name="status"]');
    if (!statusInput) {
        statusInput = document.createElement('input');
        statusInput.type = 'hidden';
        statusInput.name = 'status';
        form.appendChild(statusInput);
    }
    statusInput.value = 'failed';
    
    // Reset textarea reason
    const reasonTextarea = document.getElementById('rejection_reason');
    if (reasonTextarea) {
        reasonTextarea.value = '';
    }
    
    // Tampilkan modal
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
}
    
    // Export langsung dengan filter yang aktif
    function exportWithCurrentFilter() {
        const params = new URLSearchParams(window.location.search);
        window.location.href = `{{ route('admin.donasi.export') }}?${params.toString()}`;
    }
    
    // Auto refresh untuk donasi pending
    @if(request('status') == 'pending' || !request('status'))
    setInterval(() => {
        fetch('{{ route('admin.donasi.check-pending') }}')
            .then(response => response.json())
            .then(data => {
                if (data.has_new) {
                    const notification = new bootstrap.Toast(document.createElement('div'));
                    notification._element.innerHTML = `
                        <div class="toast align-items-center text-bg-warning border-0" role="alert" aria-live="assertive" aria-atomic="true">
                            <div class="d-flex">
                                <div class="toast-body">
                                    <i class="fas fa-bell me-2"></i>
                                    Ada ${data.count} donasi baru yang menunggu!
                                </div>
                                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                            </div>
                        </div>
                    `;
                    notification.show();
                }
            });
    }, 60000); // Check setiap 1 menit
    @endif
    
    // Validasi form sebelum submit
    document.addEventListener('DOMContentLoaded', function() {
        // Validasi form verifikasi
        const verifyForm = document.getElementById('verifyForm');
        if (verifyForm) {
            verifyForm.addEventListener('submit', function(e) {
                if (!confirm('Apakah Anda yakin ingin memverifikasi donasi ini?')) {
                    e.preventDefault();
                }
            });
        }
        
        // Validasi form tolak
        const rejectForm = document.getElementById('rejectForm');
        if (rejectForm) {
            rejectForm.addEventListener('submit', function(e) {
                const reason = document.getElementById('rejection_reason').value;
                if (!reason.trim()) {
                    e.preventDefault();
                    alert('Harap isi alasan penolakan!');
                    return;
                }
                if (!confirm('Apakah Anda yakin ingin menolak donasi ini?')) {
                    e.preventDefault();
                }
            });
        }
    });
</script>
@endpush

<style>
    .dashboard-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        transition: transform 0.3s;
    }
    
    .dashboard-card:hover {
        transform: translateY(-5px);
    }
    
    .card-title {
        font-size: 14px;
        color: #6c757d;
        margin-bottom: 5px;
    }
    
    .card-value {
        font-size: 24px;
        font-weight: 700;
        color: #212529;
    }
    
    .card-icon {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }
    
    .card-icon.primary {
        background: rgba(77, 171, 247, 0.1);
        color: #4dabf7;
    }
    
    .card-icon.success {
        background: rgba(40, 167, 69, 0.1);
        color: #28a745;
    }
    
    .card-icon.warning {
        background: rgba(255, 193, 7, 0.1);
        color: #ffc107;
    }
    
    .card-icon.danger {
        background: rgba(220, 53, 69, 0.1);
        color: #dc3545;
    }
    
    .card-icon.info {
        background: rgba(23, 162, 184, 0.1);
        color: #17a2b8;
    }
    
    .card-icon.secondary {
        background: rgba(108, 117, 125, 0.1);
        color: #6c757d;
    }
    
    .filter-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    
    .activity-card {
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    
    .activity-card .card-header {
        background: #f8f9fa;
        padding: 15px 20px;
        border-bottom: 1px solid #dee2e6;
    }
    
    .btn-group .btn {
        padding: 6px 12px;
    }
</style>
@endsection