@extends('layouts.app')

@section('title', 'Kelola Kampanye Donasi')
@section('page-title', 'Kampanye Donasi')
@section('icon', 'fas fa-hand-holding-heart')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-2">Kelola Kampanye Donasi</h1>
            <p class="text-muted mb-0">Kelola semua kampanye donasi untuk membantu lansia</p>
        </div>
        <a href="{{ route('admin.kampanye.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle me-2"></i>Tambah Kampanye
        </a>
    </div>

    <!-- Filter Section -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('admin.kampanye.index') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="status" class="form-label text-muted">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="ditutup" {{ request('status') == 'ditutup' ? 'selected' : '' }}>Ditutup</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="kategori" class="form-label text-muted">Kategori</label>
                        <select name="kategori" id="kategori" class="form-select">
                            <option value="">Semua Kategori</option>
                            <option value="lansia" {{ request('kategori') == 'lansia' ? 'selected' : '' }}>Lansia</option>
                            <option value="kesehatan" {{ request('kategori') == 'kesehatan' ? 'selected' : '' }}>Kesehatan</option>
                            <option value="pendidikan" {{ request('kategori') == 'pendidikan' ? 'selected' : '' }}>Pendidikan</option>
                            <option value="bencana" {{ request('kategori') == 'bencana' ? 'selected' : '' }}>Bencana</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="search" class="form-label text-muted">Pencarian</label>
                        <div class="input-group">
                            <input type="text" name="search" id="search" class="form-control" 
                                   placeholder="Cari judul kampanye..." value="{{ request('search') }}">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter me-2"></i>Filter
                        </button>
                    </div>
                </div>
            </form>
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
                                Total Kampanye
                            </div>
                            <div class="h5 mb-0 fw-bold">{{ $totalKampanye }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-campground fa-2x text-primary"></i>
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
                                Kampanye Aktif
                            </div>
                            <div class="h5 mb-0 fw-bold">{{ $kampanyeAktif }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-play-circle fa-2x text-success"></i>
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
                                Total Dana Terkumpul
                            </div>
                            <div class="h5 mb-0 fw-bold">Rp {{ number_format($totalDana, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-warning"></i>
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
                                Total Donatur
                            </div>
                            <div class="h5 mb-0 fw-bold">{{ $totalDonatur }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 fw-bold text-dark">
                <i class="fas fa-list me-2"></i>Daftar Kampanye
            </h6>
            <span class="badge bg-primary fs-6">{{ $kampanyes->total() }} Kampanye</span>
        </div>
        
        <div class="card-body p-0">
            @if($kampanyes->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x mb-3 text-muted opacity-50"></i>
                <h5 class="text-muted mb-2">Belum ada kampanye</h5>
                <p class="text-muted mb-4">Mulai dengan membuat kampanye donasi pertama Anda</p>
                <a href="{{ route('admin.kampanye.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle me-2"></i>Tambah Kampanye
                </a>
            </div>
            @else
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3">#</th>
                            <th class="px-4 py-3">Judul Kampanye</th>
                            <th class="px-4 py-3">Target Dana</th>
                            <th class="px-4 py-3">Terkumpul</th>
                            <th class="px-4 py-3">Progress</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3" style="width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kampanyes as $kampanye)
                        <tr>
                            <td class="px-4 py-3">{{ ($kampanyes->currentPage() - 1) * $kampanyes->perPage() + $loop->iteration }}</td>
                            <td class="px-4 py-3">
                                <div class="d-flex align-items-center">
                                    @if($kampanye->thumbnail)
                                    <img src="{{ asset('storage/' . $kampanye->thumbnail) }}" 
                                         alt="{{ $kampanye->judul }}" 
                                         class="rounded me-3" 
                                         width="50" height="50" style="object-fit: cover;">
                                    @else
                                    <div class="rounded me-3 d-flex align-items-center justify-content-center" 
                                         style="width: 50px; height: 50px; background-color: #f8f9fa;">
                                        <i class="fas fa-hand-holding-heart text-primary"></i>
                                    </div>
                                    @endif
                                    <div>
                                        <strong class="d-block mb-1">{{ Str::limit($kampanye->judul, 40) }}</strong>
                                        <small class="text-muted">{{ $kampanye->kategori }}</small>
                                        @if($kampanye->is_featured)
                                        <span class="badge bg-warning ms-2">Featured</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="fw-bold">Rp {{ number_format($kampanye->target_dana, 0, ',', '.') }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="fw-bold">Rp {{ number_format($kampanye->dana_terkumpul, 0, ',', '.') }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="progress mb-2" style="height: 6px;">
                                    <div class="progress-bar" role="progressbar" 
                                         style="width: {{ $kampanye->progress }}%;" 
                                         aria-valuenow="{{ $kampanye->progress }}" 
                                         aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                                <small class="text-muted">{{ number_format($kampanye->progress, 1) }}%</small>
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $statusColors = [
                                        'draft' => 'secondary',
                                        'aktif' => 'success',
                                        'selesai' => 'primary',
                                        'ditutup' => 'danger'
                                    ];
                                    $statusIcons = [
                                        'draft' => 'fas fa-file',
                                        'aktif' => 'fas fa-play',
                                        'selesai' => 'fas fa-check',
                                        'ditutup' => 'fas fa-times'
                                    ];
                                @endphp
                                <span class="badge bg-{{ $statusColors[$kampanye->status] ?? 'secondary' }} d-flex align-items-center" 
                                      style="width: fit-content;">
                                    <i class="{{ $statusIcons[$kampanye->status] ?? 'fas fa-question' }} me-1"></i>
                                    {{ ucfirst($kampanye->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-muted small">
                                    <div class="mb-1">{{ $kampanye->tanggal_mulai->format('d M Y') }}</div>
                                    <div>s/d</div>
                                    <div>{{ $kampanye->tanggal_selesai->format('d M Y') }}</div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.kampanye.show', $kampanye->id) }}" 
                                       class="btn btn-info btn-sm" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.kampanye.edit', $kampanye) }}" 
                                       class="btn btn-warning btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger btn-sm" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#deleteModal{{ $kampanye->id }}"
                                            title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Delete Modal -->
                        <div class="modal fade" id="deleteModal{{ $kampanye->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Konfirmasi Hapus</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Apakah Anda yakin ingin menghapus kampanye:</p>
                                        <p class="fw-bold">"{{ $kampanye->judul }}"</p>
                                        <p class="text-danger">
                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                            Data yang dihapus tidak dapat dikembalikan!
                                        </p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <form action="{{ route('admin.kampanye.destroy', $kampanye) }}" method="POST">
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

        @if(!$kampanyes->isEmpty())
        <div class="card-footer bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted">
                    Menampilkan {{ $kampanyes->firstItem() }} - {{ $kampanyes->lastItem() }} dari {{ $kampanyes->total() }} kampanye
                </div>
                <div>
                    {{ $kampanyes->links() }}
                </div>
            </div>
        </div>
        @endif
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
    
    .progress-bar {
        background-color: var(--primary-color);
    }
    
    .btn-sm {
        padding: 0.375rem 0.75rem;
    }
    
    @media (max-width: 768px) {
        .btn-group {
            width: 100%;
        }
        
        .btn-group .btn {
            flex: 1;
        }
        
        .table-responsive {
            font-size: 0.875rem;
        }
        
        .d-flex.gap-2 .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-hide alerts after 5 seconds
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => {
                new bootstrap.Alert(alert).close();
            });
        }, 5000);

        // Update status function (if needed)
        document.querySelectorAll('.status-update').forEach(select => {
            select.addEventListener('change', function() {
                const kampanyeId = this.dataset.id;
                const status = this.value;
                
                fetch(`/admin/kampanye/${kampanyeId}/status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ status: status })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            });
        });
    });
</script>
@endpush
@endsection