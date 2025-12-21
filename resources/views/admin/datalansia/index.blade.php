@extends('layouts.app')

@section('title', 'Data Lansia')
@section('page-title', 'Data Lansia')
@section('icon', 'fas fa-user-friends')

@section('content')
<div class="content-container fade-in">
    {{-- Alert sukses --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Search and Filter --}}
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('admin.datalansia.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0" 
                               placeholder="Cari berdasarkan nama lansia atau nama anak..." 
                               value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="jenis_kelamin" class="form-select">
                        <option value="">Semua Jenis Kelamin</option>
                        <option value="Laki-laki" {{ request('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>
                            Laki-laki
                        </option>
                        <option value="Perempuan" {{ request('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>
                            Perempuan
                        </option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="sort" class="form-select">
                        <option value="">Urutkan</option>
                        <option value="nama_asc" {{ request('sort') == 'nama_asc' ? 'selected' : '' }}>
                            Nama A-Z
                        </option>
                        <option value="nama_desc" {{ request('sort') == 'nama_desc' ? 'selected' : '' }}>
                            Nama Z-A
                        </option>
                        <option value="umur_asc" {{ request('sort') == 'umur_asc' ? 'selected' : '' }}>
                            Umur Muda-Tua
                        </option>
                        <option value="umur_desc" {{ request('sort') == 'umur_desc' ? 'selected' : '' }}>
                            Umur Tua-Muda
                        </option>
                        <option value="terbaru" {{ request('sort') == 'terbaru' ? 'selected' : '' }}>
                            Terbaru
                        </option>
                    </select>
                </div>
                <div class="col-md-2">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="fas fa-filter me-2"></i>Filter
                        </button>
                        <a href="{{ route('admin.datalansia.index') }}" class="btn btn-outline-secondary" title="Reset">
                            <i class="fas fa-refresh"></i>
                        </a>
                    </div>
                </div>
            </form>
            
            {{-- Export Button --}}
            @if($datalansia->count() > 0)
            <div class="mt-3 d-flex justify-content-between align-items-center">
                <div>
                    <span class="badge bg-light text-dark">
                        <i class="fas fa-info-circle me-1"></i>
                        Total Data: {{ $datalansia->total() }}
                    </span>
                    @if(request()->has('search') || request()->has('jenis_kelamin'))
                        <span class="badge bg-info ms-2">
                            <i class="fas fa-filter me-1"></i>
                            Hasil Filter
                        </span>
                    @endif
                </div>
                <div>
                    <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#exportModal">
                        <i class="fas fa-file-export me-1"></i>Export
                    </button>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-primary shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Lansia</h6>
                            <h3 class="mb-0">{{ $datalansia->total() }}</h3>
                            @if(request()->has('search') || request()->has('jenis_kelamin'))
                                <small class="text-muted">Hasil Filter</small>
                            @endif
                        </div>
                        <div class="bg-primary rounded-circle p-3 shadow">
                            <i class="fas fa-users text-white fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-info shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Laki-laki</h6>
                            <h3 class="mb-0">{{ $lakiLaki ?? '0' }}</h3>
                            <small class="text-muted">
                                {{ $datalansia->total() > 0 ? round(($lakiLaki / $datalansia->total()) * 100, 1) : 0 }}%
                            </small>
                        </div>
                        <div class="bg-info rounded-circle p-3 shadow">
                            <i class="fas fa-mars text-white fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-secondary shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Perempuan</h6>
                            <h3 class="mb-0">{{ $perempuan ?? '0' }}</h3>
                            <small class="text-muted">
                                {{ $datalansia->total() > 0 ? round(($perempuan / $datalansia->total()) * 100, 1) : 0 }}%
                            </small>
                        </div>
                        <div class="bg-secondary rounded-circle p-3 shadow">
                            <i class="fas fa-venus text-white fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-success shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Rata-rata Umur</h6>
                            <h3 class="mb-0">{{ $rataUmur ?? '0' }} <small>Thn</small></h3>
                            <small class="text-muted">
                                @php
                                    $kategori = '';
                                    if($rataUmur >= 60) $kategori = 'Lansia Senior';
                                    elseif($rataUmur >= 45) $kategori = 'Lansia Muda';
                                    else $kategori = 'Pra-Lansia';
                                @endphp
                                {{ $kategori }}
                            </small>
                        </div>
                        <div class="bg-success rounded-circle p-3 shadow">
                            <i class="fas fa-birthday-cake text-white fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Table --}}
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3 border-bottom">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>Daftar Lansia
            </h5>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.datalansia.create') }}" class="btn btn-success">
                    <i class="fas fa-plus me-2"></i>Tambah Lansia
                </a>
                @if($datalansia->count() > 0)
                <div class="dropdown">
                    <button class="btn btn-outline-primary dropdown-toggle" type="button" 
                            data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-cog"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#exportModal">
                                <i class="fas fa-file-export me-2"></i>Export Data
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.datalansia.index') }}">
                                <i class="fas fa-refresh me-2"></i>Refresh Data
                            </a>
                        </li>
                    </ul>
                </div>
                @endif
            </div>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="50" class="text-center">#</th>
                            <th>Nama Lansia</th>
                            <th>Umur</th>
                            <th>Jenis Kelamin</th>
                            <th>Kontak Keluarga</th>
                            <th>Status</th>
                            <th width="150" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($datalansia as $index => $lansia)
                            <tr class="{{ $loop->even ? 'table-light' : '' }}">
                                <td class="text-center text-muted">{{ ($datalansia->currentPage()-1) * $datalansia->perPage() + $loop->iteration }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle me-3">
                                            <div class="avatar-initial {{ $lansia->jenis_kelamin_lansia == 'Laki-laki' ? 'bg-primary' : 'bg-secondary' }} text-white">
                                                {{ substr($lansia->nama_lansia, 0, 1) }}
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $lansia->nama_lansia }}</h6>
                                            <small class="text-muted">
                                                <i class="fas fa-calendar-alt me-1"></i>
                                                Ditambahkan: {{ $lansia->created_at->format('d/m/Y') }}
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($lansia->umur_lansia >= 60)
                                            <span class="badge bg-warning me-2">{{ $lansia->umur_lansia }} Thn</span>
                                        @elseif($lansia->umur_lansia >= 45)
                                            <span class="badge bg-info me-2">{{ $lansia->umur_lansia }} Thn</span>
                                        @else
                                            <span class="badge bg-light text-dark me-2">{{ $lansia->umur_lansia }} Thn</span>
                                        @endif
                                        @if($lansia->tanggal_lahir_lansia)
                                            <small class="text-muted">
                                                <i class="fas fa-birthday-cake me-1"></i>
                                                {{ \Carbon\Carbon::parse($lansia->tanggal_lahir_lansia)->format('d/m/Y') }}
                                            </small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($lansia->jenis_kelamin_lansia == 'Laki-laki')
                                        <span class="badge bg-primary p-2">
                                            <i class="fas fa-mars me-1"></i>Laki-laki
                                        </span>
                                    @else
                                        <span class="badge bg-secondary p-2">
                                            <i class="fas fa-venus me-1"></i>Perempuan
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div>
                                        <div class="fw-medium">
                                            <i class="fas fa-user-friends me-1 text-success"></i>
                                            {{ $lansia->nama_anak ?? '-' }}
                                        </div>
                                        @if($lansia->no_hp_anak)
                                            <small class="text-muted">
                                                <i class="fas fa-phone me-1"></i>{{ $lansia->no_hp_anak }}
                                            </small>
                                        @endif
                                        @if($lansia->email_anak)
                                            <br>
                                            <small class="text-muted">
                                                <i class="fas fa-envelope me-1"></i>{{ $lansia->email_anak }}
                                            </small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $statusClass = 'bg-success';
                                        $statusText = 'Aktif';
                                        if($lansia->umur_lansia >= 75) {
                                            $statusClass = 'bg-danger';
                                            $statusText = 'Senior';
                                        } elseif($lansia->umur_lansia >= 60) {
                                            $statusClass = 'bg-warning';
                                            $statusText = 'Lansia';
                                        } elseif($lansia->umur_lansia >= 45) {
                                            $statusClass = 'bg-info';
                                            $statusText = 'Pra-Lansia';
                                        }
                                    @endphp
                                    <span class="badge {{ $statusClass }} p-2">
                                        {{ $statusText }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1 justify-content-center">
                                        <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" 
                                                data-bs-target="#detailModal{{ $lansia->id }}" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <a href="{{ route('admin.datalansia.edit', $lansia->id) }}" 
                                           class="btn btn-warning btn-sm" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.datalansia.destroy', $lansia->id) }}" 
                                              method="POST" class="d-inline" 
                                              onsubmit="return confirmDelete(this)">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="fas fa-inbox fa-4x text-muted mb-4"></i>
                                        <h5>Tidak ada data ditemukan</h5>
                                        <p class="text-muted mb-4">
                                            @if(request()->has('search') || request()->has('jenis_kelamin') || request()->has('sort'))
                                                Data tidak ditemukan dengan filter yang diterapkan.
                                            @else
                                                Silakan tambah data lansia terlebih dahulu
                                            @endif
                                        </p>
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="{{ route('admin.datalansia.create') }}" class="btn btn-primary">
                                                <i class="fas fa-plus me-2"></i>Tambah Lansia
                                            </a>
                                            @if(request()->has('search') || request()->has('jenis_kelamin') || request()->has('sort'))
                                                <a href="{{ route('admin.datalansia.index') }}" class="btn btn-outline-secondary">
                                                    <i class="fas fa-refresh me-2"></i>Reset Filter
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        {{-- Pagination --}}
        @if($datalansia->hasPages())
            <div class="card-footer bg-white border-top">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div class="text-muted mb-2 mb-md-0">
                        Menampilkan <strong>{{ $datalansia->firstItem() ?? 0 }}</strong> - 
                        <strong>{{ $datalansia->lastItem() ?? 0 }}</strong> dari 
                        <strong>{{ $datalansia->total() }}</strong> data
                        @if(request()->has('search') || request()->has('jenis_kelamin') || request()->has('sort'))
                            <span class="badge bg-light text-dark ms-2">Hasil Filter</span>
                        @endif
                    </div>
                    <div>
                        {{ $datalansia->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

{{-- Export Modal --}}
@if($datalansia->count() > 0)
<div class="modal fade" id="exportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-file-export me-2"></i>Export Data
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Data akan diexport berdasarkan filter yang sedang aktif.
                </div>
                <div class="mb-3">
                    <label class="form-label">Pilih Format Export:</label>
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="form-check card border p-3 h-100">
                                <input class="form-check-input" type="radio" name="exportFormat" id="formatExcel" value="excel" checked>
                                <label class="form-check-label d-flex align-items-center" for="formatExcel">
                                    <i class="fas fa-file-excel text-success fs-4 me-2"></i>
                                    <div>
                                        <strong>Excel (.xlsx)</strong>
                                        <div class="text-muted small">Format spreadsheet</div>
                                    </div>
                                </label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-check card border p-3 h-100">
                                <input class="form-check-input" type="radio" name="exportFormat" id="formatPDF" value="pdf">
                                <label class="form-check-label d-flex align-items-center" for="formatPDF">
                                    <i class="fas fa-file-pdf text-danger fs-4 me-2"></i>
                                    <div>
                                        <strong>PDF (.pdf)</strong>
                                        <div class="text-muted small">Format dokumen</div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Pilih Kolom:</label>
                    <div class="border rounded p-3 max-h-200 overflow-auto">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="checkAllColumns" checked>
                            <label class="form-check-label" for="checkAllColumns">
                                <strong>Pilih Semua</strong>
                            </label>
                        </div>
                        <hr class="my-2">
                        @foreach([
                            'Nama Lansia',
                            'Umur',
                            'Jenis Kelamin', 
                            'Tanggal Lahir',
                            'Nama Anak',
                            'No. HP Anak',
                            'Email Anak',
                            'Alamat',
                            'Riwayat Penyakit',
                            'Alergi',
                            'Obat Rutin'
                        ] as $column)
                            <div class="form-check mb-1">
                                <input class="form-check-input column-check" type="checkbox" 
                                       id="col{{ $loop->index }}" value="{{ $column }}" checked>
                                <label class="form-check-label" for="col{{ $loop->index }}">
                                    {{ $column }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" onclick="exportData()">
                    <i class="fas fa-download me-2"></i>Export Data
                </button>
            </div>
        </div>
    </div>
</div>
@endif

@foreach($datalansia as $lansia)
<!-- Detail Modal -->
<div class="modal fade" id="detailModal{{ $lansia->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-user-circle me-2"></i>Detail Data Lansia
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Foto Profil & Info Utama -->
                    <div class="col-md-12 mb-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar-circle me-3" style="width: 80px; height: 80px;">
                                <div class="avatar-initial {{ $lansia->jenis_kelamin_lansia == 'Laki-laki' ? 'bg-primary' : 'bg-secondary' }} text-white fs-2">
                                    {{ substr($lansia->nama_lansia, 0, 1) }}
                                </div>
                            </div>
                            <div>
                                <h4 class="mb-1">{{ $lansia->nama_lansia }}</h4>
                                <div class="d-flex flex-wrap gap-2">
                                    <span class="badge bg-primary">
                                        <i class="fas fa-id-card me-1"></i>ID: {{ $lansia->id }}
                                    </span>
                                    <span class="badge bg-{{ $lansia->jenis_kelamin_lansia == 'Laki-laki' ? 'info' : 'secondary' }}">
                                        <i class="fas fa-{{ $lansia->jenis_kelamin_lansia == 'Laki-laki' ? 'mars' : 'venus' }} me-1"></i>
                                        {{ $lansia->jenis_kelamin_lansia }}
                                    </span>
                                    <span class="badge bg-{{ $lansia->umur_lansia >= 60 ? 'warning' : 'success' }}">
                                        <i class="fas fa-birthday-cake me-1"></i>{{ $lansia->umur_lansia }} Tahun
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Informasi Pribadi -->
                    <div class="col-md-6 mb-4">
                        <div class="card h-100 border-primary">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-user me-2"></i>Informasi Pribadi
                                </h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm">
                                    <tr>
                                        <td width="40%"><small class="text-muted">Nama Lengkap</small></td>
                                        <td><strong>{{ $lansia->nama_lansia }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td><small class="text-muted">Umur</small></td>
                                        <td><strong>{{ $lansia->umur_lansia }} Tahun</strong></td>
                                    </tr>
                                    <tr>
                                        <td><small class="text-muted">Jenis Kelamin</small></td>
                                        <td><strong>{{ $lansia->jenis_kelamin_lansia }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td><small class="text-muted">Tempat/Tgl Lahir</small></td>
                                        <td>
                                            <strong>
                                                {{ $lansia->tempat_lahir_lansia ?? '-' }} / 
                                                @if($lansia->tanggal_lahir_lansia)
                                                    {{ \Carbon\Carbon::parse($lansia->tanggal_lahir_lansia)->format('d F Y') }}
                                                @else
                                                    -
                                                @endif
                                            </strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><small class="text-muted">Golongan Darah</small></td>
                                        <td>
                                            @if($lansia->gol_darah_lansia)
                                                <span class="badge bg-danger">{{ $lansia->gol_darah_lansia }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Kontak Keluarga -->
                    <div class="col-md-6 mb-4">
                        <div class="card h-100 border-info">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-users me-2"></i>Kontak Keluarga
                                </h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm">
                                    <tr>
                                        <td width="40%"><small class="text-muted">Nama Anak</small></td>
                                        <td><strong>{{ $lansia->nama_anak ?? '-' }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td><small class="text-muted">No. HP Anak</small></td>
                                        <td>
                                            <strong>
                                                @if($lansia->no_hp_anak)
                                                    <a href="tel:{{ $lansia->no_hp_anak }}" class="text-decoration-none">
                                                        <i class="fas fa-phone me-1 text-success"></i>{{ $lansia->no_hp_anak }}
                                                    </a>
                                                @else
                                                    -
                                                @endif
                                            </strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><small class="text-muted">Email Anak</small></td>
                                        <td>
                                            <strong>
                                                @if($lansia->email_anak)
                                                    <a href="mailto:{{ $lansia->email_anak }}" class="text-decoration-none">
                                                        <i class="fas fa-envelope me-1 text-primary"></i>{{ $lansia->email_anak }}
                                                    </a>
                                                @else
                                                    -
                                                @endif
                                            </strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><small class="text-muted">Alamat Lengkap</small></td>
                                        <td><strong>{{ $lansia->alamat_lengkap ?? '-' }}</strong></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Informasi Kesehatan -->
                    <div class="col-12">
                        <div class="card border-warning">
                            <div class="card-header bg-warning text-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-heartbeat me-2"></i>Informasi Kesehatan
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <div class="card border-danger h-100">
                                            <div class="card-body">
                                                <h6 class="card-title text-danger">
                                                    <i class="fas fa-disease me-1"></i> Riwayat Penyakit
                                                </h6>
                                                <p class="card-text">{{ $lansia->riwayat_penyakit_lansia ?: '-' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="card border-warning h-100">
                                            <div class="card-body">
                                                <h6 class="card-title text-warning">
                                                    <i class="fas fa-allergies me-1"></i> Alergi
                                                </h6>
                                                <p class="card-text">{{ $lansia->alergi_lansia ?: '-' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="card border-success h-100">
                                            <div class="card-body">
                                                <h6 class="card-title text-success">
                                                    <i class="fas fa-pills me-1"></i> Obat Rutin
                                                </h6>
                                                <p class="card-text">{{ $lansia->obat_rutin_lansia ?: '-' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <a href="{{ route('admin.datalansia.edit', $lansia->id) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-1"></i>Edit Data
                </a>
                <a href="#" class="btn btn-info">
                    <i class="fas fa-print me-1"></i>Cetak
                </a>
            </div>
        </div>
    </div>
</div>
@endforeach

<style>
    .content-container {
        animation: fadeIn 0.5s ease-out;
    }
    
    .avatar-circle {
        width: 40px;
        height: 40px;
        flex-shrink: 0;
    }
    
    .avatar-initial {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 1.2rem;
    }
    
    .empty-state {
        padding: 3rem 1rem;
    }
    
    .empty-state i {
        opacity: 0.3;
    }
    
    .card.border-primary {
        border-left: 4px solid var(--primary-color) !important;
    }
    
    .card.border-info {
        border-left: 4px solid var(--info-color) !important;
    }
    
    .card.border-secondary {
        border-left: 4px solid var(--secondary-color) !important;
    }
    
    .card.border-success {
        border-left: 4px solid var(--success-color) !important;
    }
    
    .max-h-200 {
        max-height: 200px;
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.05);
    }
    
    .badge {
        font-weight: 500;
    }
    
    @media (max-width: 768px) {
        .modal-dialog {
            margin: 0.5rem;
        }
        
        .d-flex.gap-1 .btn {
            padding: 0.25rem 0.5rem;
        }
        
        .card-header .d-flex {
            flex-direction: column;
            gap: 1rem;
        }
        
        .card-header .btn {
            width: 100%;
        }
    }
</style>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
        
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            var alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                var bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
        
        // Check all columns functionality
        const checkAll = document.getElementById('checkAllColumns');
        const columnChecks = document.querySelectorAll('.column-check');
        
        if(checkAll) {
            checkAll.addEventListener('change', function() {
                columnChecks.forEach(check => {
                    check.checked = this.checked;
                });
            });
            
            columnChecks.forEach(check => {
                check.addEventListener('change', function() {
                    const allChecked = [...columnChecks].every(c => c.checked);
                    const someChecked = [...columnChecks].some(c => c.checked);
                    checkAll.checked = allChecked;
                    checkAll.indeterminate = !allChecked && someChecked;
                });
            });
        }
    });
    
    function confirmDelete(form) {
        const lansiaName = form.closest('tr').querySelector('h6').textContent;
        return confirm(`Apakah Anda yakin ingin menghapus data lansia "${lansiaName}"?\n\nData yang dihapus tidak dapat dikembalikan.`);
    }
    
    function exportData() {
    const format = document.querySelector('input[name="exportFormat"]:checked').value;
    const selectedColumns = Array.from(document.querySelectorAll('.column-check:checked'))
        .map(check => check.value);
    
    // Build export URL dengan query parameters
    const baseUrl = '{{ route("admin.datalansia.export") }}';
    const url = new URL(baseUrl);
    
    // Tambahkan format
    url.searchParams.set('format', format);
    
    // Tambahkan kolom jika ada
    if (selectedColumns.length > 0) {
        url.searchParams.set('columns', selectedColumns.join(','));
    }
    
    // Tambahkan filter yang aktif
    const currentParams = new URLSearchParams(window.location.search);
    ['search', 'jenis_kelamin', 'sort'].forEach(param => {
        if (currentParams.has(param)) {
            url.searchParams.set(param, currentParams.get(param));
        }
    });
    
    // Show loading state
    const exportBtn = document.querySelector('.modal-footer .btn-success');
    const originalHtml = exportBtn.innerHTML;
    exportBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memproses...';
    exportBtn.disabled = true;
    
    // Tutup modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('exportModal'));
    if (modal) modal.hide();
    
    // Redirect ke URL export
    window.location.href = url.toString();
    
    // Reset button setelah 3 detik
    setTimeout(() => {
        exportBtn.innerHTML = originalHtml;
        exportBtn.disabled = false;
    }, 3000);
}
</script>
@endpush
@endsection