@extends('layouts.app')

@section('title', 'Kelola Kampanye Donasi')
@section('page-title', 'Kampanye Donasi')
@section('icon', 'fas fa-hand-holding-heart')

@section('content')
<div class="container-fluid">
    <!-- Header dengan Tombol Tambah -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-2" style="color: var(--dark-brown);">Kelola Kampanye Donasi</h2>
            <p class="text-muted">Kelola semua kampanye donasi untuk membantu lansia</p>
        </div>
        <a href="{{ route('admin.kampanye.create') }}" class="btn btn-primary" style="background-color: var(--primary-color); border-color: var(--primary-color);">
            <i class="fas fa-plus-circle me-2"></i>Tambah Kampanye
        </a>
    </div>

    <!-- Filter dan Pencarian -->
    <div class="filter-card mb-4">
        <form action="{{ route('admin.kampanye.index') }}" method="GET">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="ditutup" {{ request('status') == 'ditutup' ? 'selected' : '' }}>Ditutup</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="kategori" class="form-label">Kategori</label>
                    <select name="kategori" id="kategori" class="form-select">
                        <option value="">Semua Kategori</option>
                        <option value="lansia" {{ request('kategori') == 'lansia' ? 'selected' : '' }}>Lansia</option>
                        <option value="kesehatan" {{ request('kategori') == 'kesehatan' ? 'selected' : '' }}>Kesehatan</option>
                        <option value="pendidikan" {{ request('kategori') == 'pendidikan' ? 'selected' : '' }}>Pendidikan</option>
                        <option value="bencana" {{ request('kategori') == 'bencana' ? 'selected' : '' }}>Bencana</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="search" class="form-label">Pencarian</label>
                    <div class="input-group">
                        <input type="text" name="search" id="search" class="form-control" 
                               placeholder="Cari judul kampanye..." value="{{ request('search') }}">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100" style="background-color: var(--primary-color);">
                        <i class="fas fa-filter me-2"></i>Filter
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="dashboard-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="card-title">Total Kampanye</p>
                        <h3 class="card-value">{{ $totalKampanye }}</h3>
                    </div>
                    <div class="card-icon primary">
                        <i class="fas fa-campground"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dashboard-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="card-title">Kampanye Aktif</p>
                        <h3 class="card-value" style="color: var(--success-color);">{{ $kampanyeAktif }}</h3>
                    </div>
                    <div class="card-icon success">
                        <i class="fas fa-play-circle"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dashboard-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="card-title">Total Dana Terkumpul</p>
                        <h3 class="card-value">Rp {{ number_format($totalDana, 0, ',', '.') }}</h3>
                    </div>
                    <div class="card-icon warning">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dashboard-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="card-title">Total Donatur</p>
                        <h3 class="card-value" style="color: var(--info-color);">{{ $totalDonatur }}</h3>
                    </div>
                    <div class="card-icon info">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Kampanye -->
    <div class="activity-card">
        <div class="card-header">
            <h3><i class="fas fa-list me-2"></i>Daftar Kampanye</h3>
            <span class="badge bg-primary">{{ $kampanyes->total() }} Kampanye</span>
        </div>
        
        @if($kampanyes->isEmpty())
        <div class="text-center py-5">
            <i class="fas fa-inbox fa-3x mb-3" style="color: var(--accent-color);"></i>
            <h4 class="mb-2">Belum ada kampanye</h4>
            <p class="text-muted mb-4">Mulai dengan membuat kampanye donasi pertama Anda</p>
            <a href="{{ route('admin.kampanye.create') }}" class="btn btn-primary" style="background-color: var(--primary-color);">
                <i class="fas fa-plus-circle me-2"></i>Tambah Kampanye
            </a>
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover">
                <thead style="background-color: var(--light-bg);">
                    <tr>
                        <th width="50">#</th>
                        <th>Judul Kampanye</th>
                        <th>Target Dana</th>
                        <th>Terkumpul</th>
                        <th>Progress</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($kampanyes as $kampanye)
                    <tr>
                        <td>{{ $loop->iteration + ($kampanyes->perPage() * ($kampanyes->currentPage() - 1)) }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                @if($kampanye->thumbnail)
                                <img src="{{ asset('storage/' . $kampanye->thumbnail) }}" 
                                     alt="{{ $kampanye->judul }}" 
                                     class="rounded me-3" 
                                     width="50" height="50" style="object-fit: cover;">
                                @else
                                <div class="rounded me-3 d-flex align-items-center justify-content-center" 
                                     style="width: 50px; height: 50px; background-color: var(--accent-color);">
                                    <i class="fas fa-hand-holding-heart" style="color: var(--primary-color);"></i>
                                </div>
                                @endif
                                <div>
                                    <strong class="d-block">{{ Str::limit($kampanye->judul, 50) }}</strong>
                                    <small class="text-muted">{{ $kampanye->kategori }}</small>
                                    @if($kampanye->is_featured)
                                    <span class="badge bg-warning ms-2">Featured</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>Rp {{ number_format($kampanye->target_dana, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($kampanye->dana_terkumpul, 0, ',', '.') }}</td>
                        <td>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar" role="progressbar" 
                                     style="width: {{ $kampanye->progress }}%; background-color: var(--primary-color);" 
                                     aria-valuenow="{{ $kampanye->progress }}" 
                                     aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>
                            <small class="text-muted">{{ number_format($kampanye->progress, 1) }}%</small>
                        </td>
                        <td>
                            @php
                                $statusColors = [
                                    'draft' => 'secondary',
                                    'aktif' => 'success',
                                    'selesai' => 'primary',
                                    'ditutup' => 'danger'
                                ];
                            @endphp
                            <span class="badge bg-{{ $statusColors[$kampanye->status] ?? 'secondary' }}">
                                {{ ucfirst($kampanye->status) }}
                            </span>
                        </td>
                        <td>
                            <small class="d-block">{{ $kampanye->tanggal_mulai->format('d M Y') }}</small>
                            <small class="text-muted">s/d</small>
                            <small class="d-block">{{ $kampanye->tanggal_selesai->format('d M Y') }}</small>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.kampanye.show', $kampanye->id) }}" 
                                   class="btn btn-sm btn-info" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.kampanye.edit', $kampanye) }}" 
                                   class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-danger" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#deleteModal{{ $kampanye->id }}"
                                        title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>

                            <!-- Modal Hapus -->
                            <div class="modal fade" id="deleteModal{{ $kampanye->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Konfirmasi Hapus</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Apakah Anda yakin ingin menghapus kampanye:</p>
                                            <p><strong>"{{ $kampanye->judul }}"</strong></p>
                                            <p class="text-danger">Data yang dihapus tidak dapat dikembalikan!</p>
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
                    Menampilkan {{ $kampanyes->firstItem() }} - {{ $kampanyes->lastItem() }} dari {{ $kampanyes->total() }} kampanye
                </p>
            </div>
            <div>
                {{ $kampanyes->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Update status kampanye
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
                        showToast('Status berhasil diperbarui', 'success');
                        location.reload();
                    }
                });
            });
        });

        function showToast(message, type) {
            const toast = document.createElement('div');
            toast.className = `toast align-items-center text-white bg-${type} border-0 position-fixed bottom-0 end-0 m-3`;
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            `;
            document.body.appendChild(toast);
            new bootstrap.Toast(toast).show();
        }
    });
</script>
@endpush
@endsection