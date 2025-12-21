@extends('layouts.app')

@section('title', 'Kelola Iuran Bulanan')
@section('page-title', 'Iuran Bulanan')
@section('icon', 'fas fa-money-bill-wave')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-2">Kelola Iuran Bulanan</h1>
            <p class="text-muted mb-0">Kelola iuran bulanan lansia dan keluarga</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.iuran.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-2"></i>Tambah Iuran
            </a>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#generateModal">
                <i class="fas fa-bolt me-2"></i>Generate Iuran
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-primary border-4 shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-primary text-uppercase mb-1">
                                Total Iuran
                            </div>
                            <div class="h5 mb-0 fw-bold">{{ $stats['total'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-invoice-dollar fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-success border-4 shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-success text-uppercase mb-1">
                                Lunas
                            </div>
                            <div class="h5 mb-0 fw-bold">{{ $stats['lunas'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-warning border-4 shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-warning text-uppercase mb-1">
                                Belum Bayar
                            </div>
                            <div class="h5 mb-0 fw-bold">{{ $stats['pending'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-info border-4 shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-info text-uppercase mb-1">
                                Total Nominal
                            </div>
                            <div class="h5 mb-0 fw-bold">Rp {{ number_format($stats['total_nominal'] ?? 0, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-coins fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('admin.iuran.index') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="status" class="form-label text-muted">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Belum Bayar</option>
                            <option value="lunas" {{ request('status') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                            <option value="terlambat" {{ request('status') == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                            <option value="dibatalkan" {{ request('status') == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="periode" class="form-label text-muted">Periode (YYYY-MM)</label>
                        <input type="month" class="form-control" name="periode" 
                               value="{{ request('periode') }}">
                    </div>
                    <div class="col-md-4">
                        <label for="search" class="form-label text-muted">Pencarian</label>
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" 
                                   placeholder="Cari nama iuran/lansia..." value="{{ request('search') }}">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <div class="d-flex gap-2 w-100">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="fas fa-filter me-2"></i>Filter
                            </button>
                            <a href="{{ route('admin.iuran.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-redo"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabs Section -->
    <ul class="nav nav-tabs mb-4" id="iuranTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button">
                Semua Iuran
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button">
                Belum Bayar
                @if(($stats['pending'] ?? 0) > 0)
                <span class="badge bg-danger ms-1">{{ $stats['pending'] }}</span>
                @endif
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="terlambat-tab" data-bs-toggle="tab" data-bs-target="#terlambat" type="button">
                Terlambat
                @if(($stats['terlambat'] ?? 0) > 0)
                <span class="badge bg-warning ms-1">{{ $stats['terlambat'] }}</span>
                @endif
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="otomatis-tab" data-bs-toggle="tab" data-bs-target="#otomatis" type="button">
                Iuran Otomatis
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="iuranTabContent">
        <!-- Tab 1: Semua Iuran -->
        <div class="tab-pane fade show active" id="all" role="tabpanel">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-dark">
                        <i class="fas fa-list me-2"></i>Daftar Iuran
                    </h6>
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted">{{ $iurans->total() }} iuran</span>
                        <button class="btn btn-sm btn-outline-primary" onclick="exportToExcel()">
                            <i class="fas fa-download me-1"></i>Export
                        </button>
                    </div>
                </div>
                
                <div class="card-body p-0">
                    @if($iurans->isEmpty())
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-3x mb-3 text-muted opacity-50"></i>
                        <h5 class="text-muted mb-2">Belum ada iuran</h5>
                        <p class="text-muted mb-4">Mulai dengan membuat iuran baru</p>
                        <a href="{{ route('admin.iuran.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus-circle me-2"></i>Tambah Iuran
                        </a>
                    </div>
                    @else
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-4 py-3">#</th>
                                    <th class="px-4 py-3">Kode Iuran</th>
                                    <th class="px-4 py-3">Nama Iuran</th>
                                    <th class="px-4 py-3">Lansia/Keluarga</th>
                                    <th class="px-4 py-3">Jumlah</th>
                                    <th class="px-4 py-3">Periode</th>
                                    <th class="px-4 py-3">Jatuh Tempo</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3" style="width: 140px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($iurans as $iuran)
                                <tr>
                                    <td class="px-4 py-3">{{ ($iurans->currentPage() - 1) * $iurans->perPage() + $loop->iteration }}</td>
                                    <td class="px-4 py-3">
                                        <code>{{ $iuran->kode_iuran }}</code>
                                        @if($iuran->is_otomatis)
                                        <span class="badge bg-info ms-1">Auto</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <strong class="d-block">{{ $iuran->nama_iuran }}</strong>
                                        @if($iuran->deskripsi)
                                        <small class="text-muted">{{ Str::limit($iuran->deskripsi, 40) }}</small>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($iuran->datalansia)
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                                 style="width: 32px; height: 32px; font-size: 0.9rem; margin-right: 8px;">
                                                <i class="fas fa-user"></i>
                                            </div>
                                            <div>
                                                <strong class="d-block">{{ $iuran->datalansia->nama_lansia }}</strong>
                                                <small class="text-muted">{{ $iuran->user->name ?? '-' }}</small>
                                            </div>
                                        </div>
                                        @elseif($iuran->user)
                                        <div class="d-flex align-items-center">
                                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" 
                                                 style="width: 32px; height: 32px; font-size: 0.9rem; margin-right: 8px;">
                                                <i class="fas fa-users"></i>
                                            </div>
                                            <div>
                                                <strong class="d-block">{{ $iuran->user->name }}</strong>
                                                <small class="text-muted">Keluarga</small>
                                            </div>
                                        </div>
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <strong>Rp {{ number_format($iuran->jumlah, 0, ',', '.') }}</strong>
                                        @if($iuran->is_terlambat && $iuran->denda > 0)
                                        <br>
                                        <small class="text-danger">
                                            + Denda: Rp {{ number_format($iuran->denda, 0, ',', '.') }}
                                        </small>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <strong>{{ \Carbon\Carbon::parse($iuran->periode . '-01')->format('M Y') }}</strong>
                                    </td>
                                    <td class="px-4 py-3">
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
                                    <td class="px-4 py-3">
                                        @php
                                            $statusColors = [
                                                'pending' => 'warning',
                                                'lunas' => 'success',
                                                'terlambat' => 'danger',
                                                'dibatalkan' => 'secondary'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$iuran->status] ?? 'secondary' }}">
                                            {{ ucfirst($iuran->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-info btn-sm" title="Detail"
                                                    onclick="showDetail({{ $iuran->id }})">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            
                                            @if($iuran->status == 'pending')
                                            <button class="btn btn-success btn-sm" title="Tandai Lunas"
                                                    onclick="markAsPaid({{ $iuran->id }})">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            @endif
                                            
                                            <button class="btn btn-danger btn-sm" title="Hapus"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#deleteModal{{ $iuran->id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Delete Modal -->
                                <div class="modal fade" id="deleteModal{{ $iuran->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Konfirmasi Hapus</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Apakah Anda yakin ingin menghapus iuran:</p>
                                                <p class="fw-bold">{{ $iuran->nama_iuran }}</p>
                                                <p class="text-muted">Kode: {{ $iuran->kode_iuran }}</p>
                                                <p class="text-danger">
                                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                                    Data yang dihapus tidak dapat dikembalikan
                                                </p>
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
                    @endif
                </div>

                @if(!$iurans->isEmpty())
                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            Menampilkan {{ $iurans->firstItem() }} - {{ $iurans->lastItem() }} dari {{ $iurans->total() }} iuran
                        </div>
                        <div>
                            {{ $iurans->links() }}
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Tab 2: Belum Bayar -->
        <div class="tab-pane fade" id="pending" role="tabpanel">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-dark">
                        <i class="fas fa-clock me-2"></i>Iuran Belum Dibayar
                    </h6>
                </div>
                <div class="card-body">
                    @php
                        $pendingIurans = \App\Models\IuranBulanan::whereIn('status', ['pending'])
                            ->with(['user', 'datalansia'])
                            ->orderBy('tanggal_jatuh_tempo', 'asc')
                            ->get();
                    @endphp
                    
                    @if($pendingIurans->isEmpty())
                    <div class="text-center py-5">
                        <i class="fas fa-check-circle fa-3x mb-3 text-success opacity-50"></i>
                        <h5 class="text-muted mb-2">Tidak ada iuran yang belum dibayar</h5>
                        <p class="text-muted">Semua iuran sudah lunas</p>
                    </div>
                    @else
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Kode</th>
                                    <th>Nama Iuran</th>
                                    <th>Lansia/Keluarga</th>
                                    <th>Jatuh Tempo</th>
                                    <th>Total</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingIurans as $iuran)
                                <tr>
                                    <td><code>{{ $iuran->kode_iuran }}</code></td>
                                    <td>{{ $iuran->nama_iuran }}</td>
                                    <td>
                                        @if($iuran->datalansia)
                                        {{ $iuran->datalansia->nama_lansia }}
                                        @elseif($iuran->user)
                                        {{ $iuran->user->name }}
                                        @else
                                        -
                                        @endif
                                    </td>
                                    <td>
                                        {{ $iuran->tanggal_jatuh_tempo->format('d M Y') }}
                                        @if($iuran->is_terlambat)
                                        <br><small class="text-danger">Terlambat</small>
                                        @endif
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
        <div class="tab-pane fade" id="terlambat" role="tabpanel">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-dark">
                        <i class="fas fa-exclamation-triangle me-2"></i>Iuran Terlambat
                    </h6>
                </div>
                <div class="card-body">
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
                        <i class="fas fa-check-circle fa-3x mb-3 text-success opacity-50"></i>
                        <h5 class="text-muted mb-2">Tidak ada iuran terlambat</h5>
                        <p class="text-muted">Semua iuran sudah dibayar tepat waktu</p>
                    </div>
                    @else
                    <div class="alert alert-warning mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        Terdapat {{ $lateIurans->count() }} iuran yang terlambat dibayar
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Kode</th>
                                    <th>Nama Iuran</th>
                                    <th>Lansia/Keluarga</th>
                                    <th>Jatuh Tempo</th>
                                    <th>Terlambat</th>
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
                                        @else
                                        -
                                        @endif
                                    </td>
                                    <td>{{ $iuran->tanggal_jatuh_tempo->format('d M Y') }}</td>
                                    <td>
                                        <span class="badge bg-danger">
                                            {{ \Carbon\Carbon::now()->diffInDays($iuran->tanggal_jatuh_tempo) }} hari
                                        </span>
                                    </td>
                                    <td>
                                        <strong class="text-danger">Rp {{ number_format($iuran->total_bayar, 0, ',', '.') }}</strong>
                                        @if($iuran->denda > 0)
                                        <small class="d-block text-danger">Termasuk denda: Rp {{ number_format($iuran->denda, 0, ',', '.') }}</small>
                                        @endif
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
        <div class="tab-pane fade" id="otomatis" role="tabpanel">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-dark">
                        <i class="fas fa-sync-alt me-2"></i>Iuran Otomatis
                    </h6>
                </div>
                <div class="card-body">
                    @php
                        $recurringIurans = \App\Models\IuranBulanan::where('is_otomatis', true)
                            ->with(['user', 'datalansia'])
                            ->latest()
                            ->get();
                    @endphp
                    
                    @if($recurringIurans->isEmpty())
                    <div class="text-center py-5">
                        <i class="fas fa-cogs fa-3x mb-3 text-muted opacity-50"></i>
                        <h5 class="text-muted mb-2">Belum ada iuran otomatis</h5>
                        <p class="text-muted">Iuran otomatis akan dibuat dari template</p>
                    </div>
                    @else
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        Iuran otomatis akan dibuat setiap bulan sesuai pengaturan
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Kode</th>
                                    <th>Nama Iuran</th>
                                    <th>Lansia/Keluarga</th>
                                    <th>Jumlah</th>
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
                                        @else
                                        -
                                        @endif
                                    </td>
                                    <td>Rp {{ number_format($iuran->jumlah, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $iuran->status == 'lunas' ? 'success' : 'warning' }}">
                                            {{ ucfirst($iuran->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-info btn-sm" onclick="showDetail({{ $iuran->id }})">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            @if($iuran->status == 'pending')
                                            <button class="btn btn-success btn-sm" onclick="markAsPaid({{ $iuran->id }})">
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
                            @foreach($templates ?? [] as $template)
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
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .card {
        border: none;
        border-radius: 10px;
    }
    
    .table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
    }
    
    .badge {
        font-weight: 500;
    }
    
    .nav-tabs .nav-link {
        color: var(--text-light);
        font-weight: 500;
    }
    
    .nav-tabs .nav-link.active {
        color: var(--primary-color);
        border-bottom-color: var(--primary-color);
        font-weight: 600;
    }
    
    @media (max-width: 768px) {
        .d-flex.flex-wrap {
            gap: 0.5rem !important;
        }
        
        .btn {
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
        }
        
        .table-responsive {
            font-size: 0.875rem;
        }
        
        .nav-tabs .nav-link {
            font-size: 0.875rem;
            padding: 0.5rem 0.75rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Show iuran detail
    function showDetail(iuranId) {
        fetch(`/admin/iuran/${iuranId}/detail`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('detailContent').innerHTML = data.html;
                new bootstrap.Modal(document.getElementById('detailModal')).show();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Gagal mengambil detail iuran');
            });
    }
    
    // Mark iuran as paid
    function markAsPaid(iuranId) {
        if (confirm('Apakah Anda yakin ingin menandai iuran sebagai lunas?')) {
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
                } else {
                    alert(data.message || 'Gagal memperbarui status iuran');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat memperbarui status iuran');
            });
        }
    }
    
    // Export to Excel
    function exportToExcel() {
        window.location.href = '{{ route('admin.iuran.export') }}?{{ request()->getQueryString() }}';
    }
    
    // Initialize on DOM loaded
    document.addEventListener('DOMContentLoaded', function() {
        // Activate tab from URL hash
        const hash = window.location.hash;
        if (hash) {
            const tab = new bootstrap.Tab(document.querySelector(`${hash}-tab`));
            tab.show();
        }
        
        // Auto-hide alerts after 5 seconds
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => {
                new bootstrap.Alert(alert).close();
            });
        }, 5000);
    });
</script>
@endpush
@endsection