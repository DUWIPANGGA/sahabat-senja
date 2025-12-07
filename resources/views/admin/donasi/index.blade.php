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
        <div class="d-flex gap-2">
            <button class="btn btn-primary" style="background-color: var(--primary-color);" 
                    onclick="exportToExcel()">
                <i class="fas fa-download me-2"></i>Export Excel
            </button>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#filterModal">
                <i class="fas fa-filter me-2"></i>Filter Lanjutan
            </button>
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

    <!-- Filter Sederhana -->
    <div class="filter-card mb-4">
        <form action="{{ route('admin.donasi.index') }}" method="GET">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>Success</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="metode_pembayaran" class="form-label">Metode</label>
                    <select name="metode_pembayaran" id="metode_pembayaran" class="form-select">
                        <option value="">Semua Metode</option>
                        <option value="transfer_bank" {{ request('metode_pembayaran') == 'transfer_bank' ? 'selected' : '' }}>Transfer Bank</option>
                        <option value="ewallet" {{ request('metode_pembayaran') == 'ewallet' ? 'selected' : '' }}>E-Wallet</option>
                        <option value="qris" {{ request('metode_pembayaran') == 'qris' ? 'selected' : '' }}>QRIS</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="search" class="form-label">Pencarian</label>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Cari nama donatur/kode donasi..." 
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
        <div class="card-header">
            <h3><i class="fas fa-list me-2"></i>Daftar Donasi</h3>
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted">{{ $donasis->total() }} donasi</span>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" 
                            data-bs-toggle="dropdown">
                        <i class="fas fa-cog me-1"></i> Aksi
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" onclick="exportToExcel()">
                            <i class="fas fa-file-excel me-2"></i>Export Excel
                        </a></li>
                        <li><a class="dropdown-item" href="#" onclick="exportToPDF()">
                            <i class="fas fa-file-pdf me-2"></i>Export PDF
                        </a></li>
                    </ul>
                </div>
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

<!-- Modal Filter Lanjutan -->
<div class="modal fade" id="filterModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.donasi.index') }}" method="GET">
                <div class="modal-header">
                    <h5 class="modal-title">Filter Lanjutan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="start_date" class="form-label">Dari Tanggal</label>
                            <input type="date" class="form-control" name="start_date" 
                                   value="{{ request('start_date') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="end_date" class="form-label">Sampai Tanggal</label>
                            <input type="date" class="form-control" name="end_date" 
                                   value="{{ request('end_date') }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="amount_min" class="form-label">Jumlah Minimum</label>
                        <input type="number" class="form-control" name="amount_min" 
                               placeholder="Rp" value="{{ request('amount_min') }}">
                    </div>
                    <div class="mb-3">
                        <label for="amount_max" class="form-label">Jumlah Maksimum</label>
                        <input type="number" class="form-control" name="amount_max" 
                               placeholder="Rp" value="{{ request('amount_max') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tampilkan Kolom</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="columns[]" 
                                   value="doa" id="show_doa" checked>
                            <label class="form-check-label" for="show_doa">Doa/Harapan</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="columns[]" 
                                   value="keterangan" id="show_keterangan">
                            <label class="form-check-label" for="show_keterangan">Keterangan</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Terapkan Filter</button>
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
                @method('POST')
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
                @method('POST')
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
        document.getElementById('verifyForm').action = `/admin/donasi/${donationId}/status`;
        document.getElementById('verifyForm').innerHTML += `
            <input type="hidden" name="status" value="success">
        `;
        new bootstrap.Modal(document.getElementById('verifyModal')).show();
    }
    
    function rejectDonation(donationId) {
        document.getElementById('rejectForm').action = `/admin/donasi/${donationId}/status`;
        document.getElementById('rejectForm').innerHTML += `
            <input type="hidden" name="status" value="failed">
        `;
        new bootstrap.Modal(document.getElementById('rejectModal')).show();
    }
    
    function exportToExcel() {
        const params = new URLSearchParams(window.location.search);
        params.append('export', 'excel');
        window.location.href = `{{ route('admin.donasi.export') }}?${params.toString()}`;
    }
    
    function exportToPDF() {
        const params = new URLSearchParams(window.location.search);
        params.append('export', 'pdf');
        window.location.href = `{{ route('admin.donasi.export') }}?${params.toString()}`;
    }
    
    // Auto refresh untuk donasi pending
    @if(request('status') == 'pending' || !request('status'))
    setInterval(() => {
        fetch('{{ route('admin.donasi.check-pending') }}')
            .then(response => response.json())
            .then(data => {
                if (data.has_new) {
                    location.reload();
                }
            });
    }, 30000); // Check setiap 30 detik
    @endif
</script>
@endpush
@endsection