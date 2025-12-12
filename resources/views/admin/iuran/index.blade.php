@extends('layouts.app')

@section('title', 'Kelola Iuran Bulanan')
@section('page-title', 'Iuran Bulanan')
@section('icon', 'fas fa-money-bill-wave')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-2" style="color: var(--dark-brown);">Kelola Iuran Bulanan</h2>
            <p class="text-muted">Kelola iuran bulanan lansia dan keluarga</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.iuran.create') }}" class="btn btn-primary" style="background-color: var(--primary-color);">
                <i class="fas fa-plus-circle me-2"></i>Tambah Iuran
            </a>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#generateModal">
                <i class="fas fa-bolt me-2"></i>Generate Iuran
            </button>
            <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#bulkModal">
                <i class="fas fa-layer-group me-2"></i>Bulk Generate
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="dashboard-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="card-title">Total Iuran</p>
                        <h3 class="card-value">{{ $stats['total'] }}</h3>
                    </div>
                    <div class="card-icon primary">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dashboard-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="card-title">Lunas</p>
                        <h3 class="card-value" style="color: var(--success-color);">{{ $stats['lunas'] }}</h3>
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
                        <p class="card-title">Belum Bayar</p>
                        <h3 class="card-value" style="color: var(--warning-color);">{{ $stats['pending'] }}</h3>
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
                        <p class="card-title">Total Nominal</p>
                        <h3 class="card-value">Rp {{ number_format($stats['total_nominal'], 0, ',', '.') }}</h3>
                    </div>
                    <div class="card-icon info">
                        <i class="fas fa-coins"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="filter-card mb-4">
        <form action="{{ route('admin.iuran.index') }}" method="GET">
            <div class="row g-3">
                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Belum Bayar</option>
                        <option value="lunas" {{ request('status') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                        <option value="terlambat" {{ request('status') == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                        <option value="dibatalkan" {{ request('status') == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="periode" class="form-label">Periode (YYYY-MM)</label>
                    <input type="month" class="form-control" name="periode" 
                           value="{{ request('periode') }}" placeholder="YYYY-MM">
                </div>
                <div class="col-md-3">
                    <label for="search" class="form-label">Pencarian</label>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Cari nama iuran/lansia..." value="{{ request('search') }}">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <div class="d-flex gap-2 w-100">
                        <button type="submit" class="btn btn-primary flex-fill" style="background-color: var(--primary-color);">
                            <i class="fas fa-filter me-2"></i>Filter
                        </button>
                        <a href="{{ route('admin.iuran.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Tabs -->
    <div class="mb-4">
        <ul class="nav nav-tabs" id="iuranTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button">
                    Semua Iuran
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button">
                    Belum Bayar
                    @if($stats['pending'] > 0)
                    <span class="badge bg-danger ms-1">{{ $stats['pending'] }}</span>
                    @endif
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="late-tab" data-bs-toggle="tab" data-bs-target="#late" type="button">
                    Terlambat
                    @if($stats['terlambat'] > 0)
                    <span class="badge bg-warning ms-1">{{ $stats['terlambat'] }}</span>
                    @endif
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="recurring-tab" data-bs-toggle="tab" data-bs-target="#recurring" type="button">
                    Iuran Otomatis
                </button>
            </li>
        </ul>
    </div>

    <!-- Tab Content -->
    <div class="tab-content" id="iuranTabContent">
        <!-- Tab 1: Semua Iuran -->
        <div class="tab-pane fade show active" id="all" role="tabpanel">
            <div class="activity-card">
                <div class="card-header">
                    <h3><i class="fas fa-list me-2"></i>Daftar Iuran</h3>
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted">{{ $iurans->total() }} iuran</span>
                        <button class="btn btn-sm btn-outline-primary" onclick="exportToExcel()">
                            <i class="fas fa-download me-1"></i>Export
                        </button>
                    </div>
                </div>
                
                @if($iurans->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x mb-3" style="color: var(--accent-color);"></i>
                    <h4 class="mb-2">Belum ada iuran</h4>
                    <p class="text-muted mb-4">Mulai dengan membuat iuran baru</p>
                    <a href="{{ route('admin.iuran.create') }}" class="btn btn-primary" style="background-color: var(--primary-color);">
                        <i class="fas fa-plus-circle me-2"></i>Tambah Iuran
                    </a>
                </div>
                @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead style="background-color: var(--light-bg);">
                            <tr>
                                <th width="50">#</th>
                                <th>Kode Iuran</th>
                                <th>Nama Iuran</th>
                                <th>Lansia/Keluarga</th>
                                <th>Jumlah</th>
                                <th>Periode</th>
                                <th>Jatuh Tempo</th>
                                <th>Status</th>
                                <th width="150">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($iurans as $iuran)
                            <tr>
                                <td>{{ $loop->iteration + ($iurans->perPage() * ($iurans->currentPage() - 1)) }}</td>
                                <td>
                                    <code>{{ $iuran->kode_iuran }}</code>
                                    @if($iuran->is_otomatis)
                                    <span class="badge bg-info ms-1">Auto</span>
                                    @endif
                                </td>
                                <td>
                                    <strong class="d-block">{{ $iuran->nama_iuran }}</strong>
                                    @if($iuran->deskripsi)
                                    <small class="text-muted">{{ Str::limit($iuran->deskripsi, 50) }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($iuran->datalansia)
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" 
                                             style="width: 30px; height: 30px; font-size: 12px;">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div class="ms-2">
                                            <strong class="d-block">{{ $iuran->datalansia->nama_lansia }}</strong>
                                            <small class="text-muted">{{ $iuran->user->name ?? 'Tidak ada keluarga' }}</small>
                                        </div>
                                    </div>
                                    @elseif($iuran->user)
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center" 
                                             style="width: 30px; height: 30px; font-size: 12px;">
                                            <i class="fas fa-users"></i>
                                        </div>
                                        <div class="ms-2">
                                            <strong class="d-block">{{ $iuran->user->name }}</strong>
                                            <small class="text-muted">Keluarga</small>
                                        </div>
                                    </div>
                                    @else
                                    <span class="text-muted">Tidak ada</span>
                                    @endif
                                </td>
                                <td>
                                    <strong>Rp {{ number_format($iuran->jumlah, 0, ',', '.') }}</strong>
                                    @if($iuran->is_terlambat)
                                    <br>
                                    <small class="text-danger">
                                        + Denda: Rp {{ number_format($iuran->denda, 0, ',', '.') }}
                                    </small>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ \Carbon\Carbon::parse($iuran->periode . '-01')->format('M Y') }}</strong>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span>{{ $iuran->tanggal_jatuh_tempo->format('d M Y') }}</span>
                                        @if($iuran->is_terlambat)
                                        <span class="badge bg-danger mt-1">
                                            Terlambat {{ \Carbon\Carbon::now()->diffInDays($iuran->tanggal_jatuh_tempo) }} hari
                                        </span>
                                        @elseif($iuran->status == 'lunas')
                                        <small class="text-success">
                                            Dibayar: {{ $iuran->tanggal_bayar ? $iuran->tanggal_bayar->format('d M Y') : '' }}
                                        </small>
                                        @else
                                        <small class="text-muted">
                                            {{ \Carbon\Carbon::now()->diffInDays($iuran->tanggal_jatuh_tempo, false) }} hari lagi
                                        </small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $statusColors = [
                                            'pending' => 'warning',
                                            'lunas' => 'success',
                                            'terlambat' => 'danger',
                                            'dibatalkan' => 'secondary'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $statusColors[$iuran->status] }}">
                                        {{ ucfirst($iuran->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-info" title="Detail"
                                                onclick="showDetail({{ $iuran->id }})">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        
                                        @if($iuran->status == 'pending')
                                        <button class="btn btn-sm btn-success" title="Tandai Lunas"
                                                onclick="markAsPaid({{ $iuran->id }})">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        @endif
                                        
                                        <button class="btn btn-sm btn-danger" title="Hapus"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteModal{{ $iuran->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Modal Hapus -->
                            <div class="modal fade" id="deleteModal{{ $iuran->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Konfirmasi Hapus</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Apakah Anda yakin ingin menghapus iuran:</p>
                                            <p><strong>{{ $iuran->nama_iuran }}</strong></p>
                                            <p class="text-muted">Kode: {{ $iuran->kode_iuran }}</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <form action="{{ route('admin.iuran.destroy', $iuran) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">Hapus</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        <p class="mb-0 text-muted">
                            Menampilkan {{ $iurans->firstItem() }} - {{ $iurans->lastItem() }} dari {{ $iurans->total() }} iuran
                        </p>
                    </div>
                    <div>
                        {{ $iurans->links() }}
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Tab 2: Belum Bayar -->
        <div class="tab-pane fade" id="pending" role="tabpanel">
            <div class="activity-card">
                <div class="card-header">
                    <h3><i class="fas fa-clock me-2"></i>Iuran Belum Dibayar</h3>
                </div>
                <div class="p-3">
                    @php
                        $pendingIurans = \App\Models\IuranBulanan::whereIn('status', ['pending', 'terlambat'])
                            ->with(['user', 'datalansia'])
                            ->orderBy('tanggal_jatuh_tempo', 'asc')
                            ->get();
                    @endphp
                    
                    @if($pendingIurans->isEmpty())
                    <div class="text-center py-5">
                        <i class="fas fa-check-circle fa-3x mb-3" style="color: var(--success-color);"></i>
                        <h4 class="mb-2">Tidak ada iuran yang belum dibayar</h4>
                        <p class="text-muted">Semua iuran sudah lunas</p>
                    </div>
                    @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Nama Iuran</th>
                                    <th>Lansia/Keluarga</th>
                                    <th>Jatuh Tempo</th>
                                    <th>Status</th>
                                    <th>Total</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingIurans as $iuran)
                                <tr class="{{ $iuran->is_terlambat ? 'table-danger' : 'table-warning' }}">
                                    <td><code>{{ $iuran->kode_iuran }}</code></td>
                                    <td>{{ $iuran->nama_iuran }}</td>
                                    <td>
                                        @if($iuran->datalansia)
                                        {{ $iuran->datalansia->nama_lansia }}
                                        @elseif($iuran->user)
                                        {{ $iuran->user->name }}
                                        @endif
                                    </td>
                                    <td class="text-black">
                                        {{ $iuran->tanggal_jatuh_tempo->format('d M Y') }}
                                        @if($iuran->is_terlambat)
                                        <br><small class="text-black">Terlambat {{ \Carbon\Carbon::now()->diffInDays($iuran->tanggal_jatuh_tempo) }} hari</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $iuran->is_terlambat ? 'danger' : 'warning' }}">
                                            {{ $iuran->is_terlambat ? 'Terlambat' : 'Belum Bayar' }}
                                        </span>
                                    </td>
                                    <td>
                                        <strong>Rp {{ number_format($iuran->total_bayar, 0, ',', '.') }}</strong>
                                        @if($iuran->denda > 0)
                                        <br><small class="text-danger">+ denda Rp {{ number_format($iuran->denda, 0, ',', '.') }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-success" onclick="markAsPaid({{ $iuran->id }})">
                                            <i class="fas fa-check me-1"></i>Tandai Lunas
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Tab 3: Terlambat -->
        <div class="tab-pane fade" id="late" role="tabpanel">
            <div class="activity-card">
                <div class="card-header">
                    <h3><i class="fas fa-exclamation-triangle me-2"></i>Iuran Terlambat</h3>
                </div>
                <div class="p-3">
                    @php
                        $lateIurans = \App\Models\IuranBulanan::where('status', 'terlambat')
                            ->orWhere(function($query) {
                                $query->where('status', 'pending')
                                      ->whereDate('tanggal_jatuh_tempo', '<', now());
                            })
                            ->with(['user', 'datalansia'])
                            ->get();
                    @endphp
                    
                    @if($lateIurans->isEmpty())
                    <div class="text-center py-5">
                        <i class="fas fa-check-circle fa-3x mb-3" style="color: var(--success-color);"></i>
                        <h4 class="mb-2">Tidak ada iuran terlambat</h4>
                        <p class="text-muted">Semua iuran sudah dibayar tepat waktu</p>
                    </div>
                    @else
                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle me-2"></i>
                        Terdapat {{ $lateIurans->count() }} iuran yang terlambat dibayar
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Nama Iuran</th>
                                    <th>Lansia/Keluarga</th>
                                    <th>Jatuh Tempo</th>
                                    <th>Terlambat</th>
                                    <th>Denda</th>
                                    <th>Total</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lateIurans as $iuran)
                                <tr>
                                    <td><code>{{ $iuran->kode_iuran }}</code></td>
                                    <td>{{ $iuran->nama_iuran }}</td>
                                    <td>
                                        @if($iuran->datalansia)
                                        {{ $iuran->datalansia->nama_lansia }}
                                        @elseif($iuran->user)
                                        {{ $iuran->user->name }}
                                        @endif
                                    </td>
                                    <td>{{ $iuran->tanggal_jatuh_tempo->format('d M Y') }}</td>
                                    <td>
                                        <span class="badge bg-danger">
                                            {{ \Carbon\Carbon::now()->diffInDays($iuran->tanggal_jatuh_tempo) }} hari
                                        </span>
                                    </td>
                                    <td class="text-danger">
                                        Rp {{ number_format($iuran->denda, 0, ',', '.') }}
                                    </td>
                                    <td>
                                        <strong class="text-danger">Rp {{ number_format($iuran->total_bayar, 0, ',', '.') }}</strong>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-success" onclick="markAsPaid({{ $iuran->id }})">
                                            <i class="fas fa-check me-1"></i>Lunas
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Tab 4: Iuran Otomatis -->
        <div class="tab-pane fade" id="recurring" role="tabpanel">
            <div class="activity-card">
                <div class="card-header">
                    <h3><i class="fas fa-sync-alt me-2"></i>Iuran Otomatis</h3>
                </div>
                <div class="p-3">
                    @php
                        $recurringIurans = \App\Models\IuranBulanan::where('is_otomatis', true)
                            ->with(['user', 'datalansia'])
                            ->latest()
                            ->get();
                    @endphp
                    
                    @if($recurringIurans->isEmpty())
                    <div class="text-center py-5">
                        <i class="fas fa-cogs fa-3x mb-3" style="color: var(--accent-color);"></i>
                        <h4 class="mb-2">Belum ada iuran otomatis</h4>
                        <p class="text-muted">Iuran otomatis akan dibuat dari template</p>
                    </div>
                    @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Iuran otomatis akan dibuat setiap bulan sesuai pengaturan
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Nama Iuran</th>
                                    <th>Lansia/Keluarga</th>
                                    <th>Jumlah</th>
                                    <th>Interval</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recurringIurans as $iuran)
                                <tr>
                                    <td><code>{{ $iuran->kode_iuran }}</code></td>
                                    <td>{{ $iuran->nama_iuran }}</td>
                                    <td>
                                        @if($iuran->datalansia)
                                        {{ $iuran->datalansia->nama_lansia }}
                                        @elseif($iuran->user)
                                        {{ $iuran->user->name }}
                                        @endif
                                    </td>
                                    <td>Rp {{ number_format($iuran->jumlah, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="badge bg-info">Setiap {{ $iuran->interval_bulan }} bulan</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $iuran->status == 'lunas' ? 'success' : 'warning' }}">
                                            {{ ucfirst($iuran->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-sm btn-info" onclick="showDetail({{ $iuran->id }})">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            @if($iuran->status == 'pending')
                                            <button class="btn btn-sm btn-success" onclick="markAsPaid({{ $iuran->id }})">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Generate Iuran -->
<div class="modal fade" id="generateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.iuran.generate') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Generate Iuran dari Template</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="template_id" class="form-label">Pilih Template</label>
                        <select class="form-select" id="template_id" name="template_id" required>
                            <option value="">Pilih Template...</option>
                            @foreach($templates as $template)
                            <option value="{{ $template->id }}">
                                {{ $template->nama_template }} (Rp {{ number_format($template->jumlah, 0, ',', '.') }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="periode" class="form-label">Periode (YYYY-MM)</label>
                        <input type="month" class="form-control" name="periode" 
                               value="{{ date('Y-m') }}" required>
                        <small class="text-muted">Iuran akan dibuat untuk periode ini</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Generate Iuran</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Bulk Generate -->
<div class="modal fade" id="bulkModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.iuran.bulk-generate') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Bulk Generate Iuran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="bulk_template_id" class="form-label">Pilih Template</label>
                        <select class="form-select" id="bulk_template_id" name="template_id" required>
                            <option value="">Pilih Template...</option>
                            @foreach($templates as $template)
                            <option value="{{ $template->id }}">
                                {{ $template->nama_template }} (Rp {{ number_format($template->jumlah, 0, ',', '.') }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="bulk_month_start" class="form-label">Mulai Bulan</label>
                            <input type="month" class="form-control" name="bulan_mulai" 
                                   value="{{ date('Y-m') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="bulk_month_end" class="form-label">Sampai Bulan</label>
                            <input type="month" class="form-control" name="bulan_selesai" 
                                   value="{{ date('Y-m') }}" required>
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Iuran akan digenerate untuk setiap bulan dalam rentang waktu yang dipilih
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Bulk Generate</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Detail Iuran -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Iuran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailContent">
                <!-- Detail akan diisi via JavaScript -->
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function showDetail(iuranId) {
        fetch(`/admin/iuran/${iuranId}/detail`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('detailContent').innerHTML = data.html;
                new bootstrap.Modal(document.getElementById('detailModal')).show();
            });
    }
    
    function markAsPaid(iuranId) {
        if (confirm('Tandai iuran sebagai lunas?')) {
            fetch(`/admin/iuran/${iuranId}/mark-paid`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Iuran telah ditandai sebagai lunas');
                    location.reload();
                }
            });
        }
    }
    
    function exportToExcel() {
        // Implement export to Excel
        window.location.href = '{{ route('admin.iuran.export') }}?{{ request()->getQueryString() }}';
    }
    
    // Inisialisasi tabs
    document.addEventListener('DOMContentLoaded', function() {
        // Aktifkan tab berdasarkan URL hash
        const hash = window.location.hash;
        if (hash) {
            const tab = new bootstrap.Tab(document.querySelector(hash + '-tab'));
            tab.show();
        }
        
        // Auto-update status terlambat
        setInterval(() => {
            fetch('{{ route('admin.iuran.check-late') }}');
        }, 60000); // Check setiap menit
    });
</script>
@endpush
@endsection