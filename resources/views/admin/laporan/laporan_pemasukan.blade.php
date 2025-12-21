@extends('layouts.app')

@section('title', 'Pemasukan')
@section('page-title', 'Pemasukan')
@section('icon', 'fas fa-chart-line')

@section('content')
<div class="container-fluid py-3">
    {{-- Alerts --}}
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

    {{-- Summary Cards --}}
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-success border-4 shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-success text-uppercase mb-1">
                                Total Pemasukan
                            </div>
                            <div class="h5 mb-0 fw-bold">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-success"></i>
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
                                Jumlah Transaksi
                            </div>
                            <div class="h5 mb-0 fw-bold">{{ $pemasukan->total() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-receipt fa-2x text-info"></i>
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
                                Rata-rata per Transaksi
                            </div>
                            <div class="h5 mb-0 fw-bold">Rp {{ number_format($pemasukan->avg('jumlah') ?? 0, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-primary border-4 shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-primary text-uppercase mb-1">
                                Periode
                            </div>
                            <div class="h6 mb-0 fw-bold">
                                @if(request('dari_tanggal') && request('sampai_tanggal'))
                                {{ date('d M Y', strtotime(request('dari_tanggal'))) }} - {{ date('d M Y', strtotime(request('sampai_tanggal'))) }}
                                @else
                                Semua Waktu
                                @endif
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Section --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white py-3 d-flex flex-column flex-md-row justify-content-between align-items-center">
            <h6 class="m-0 fw-bold text-dark"><i class="fas fa-filter me-2"></i>Filter Data</h6>
            <div class="mt-2 mt-md-0">
                <span class="badge bg-primary fs-6">{{ $pemasukan->total() }} Transaksi</span>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('laporan.pemasukan') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="dari_tanggal" class="form-label text-muted">Dari Tanggal</label>
                    <input type="date" class="form-control" id="dari_tanggal" name="dari_tanggal" 
                           value="{{ request('dari_tanggal') }}">
                </div>
                <div class="col-md-3">
                    <label for="sampai_tanggal" class="form-label text-muted">Sampai Tanggal</label>
                    <input type="date" class="form-control" id="sampai_tanggal" name="sampai_tanggal" 
                           value="{{ request('sampai_tanggal') }}">
                </div>
                <div class="col-md-3">
                    <label for="sumber" class="form-label text-muted">Sumber Pemasukan</label>
                    <select class="form-select" id="sumber" name="sumber">
                        <option value="">Semua Sumber</option>
                        <option value="Iuran Bulanan" {{ request('sumber') == 'Iuran Bulanan' ? 'selected' : '' }}>Iuran Bulanan</option>
                        <option value="Donasi" {{ request('sumber') == 'Donasi' ? 'selected' : '' }}>Donasi</option>
                        <option value="Bantuan Pemerintah" {{ request('sumber') == 'Bantuan Pemerintah' ? 'selected' : '' }}>Bantuan Pemerintah</option>
                        <option value="Lainnya" {{ request('sumber') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <div class="d-flex gap-2 w-100">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="fas fa-filter me-2"></i>Filter
                        </button>
                        <a href="{{ route('laporan.pemasukan') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-refresh"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex gap-2 flex-wrap">
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#tambahPemasukanModal">
                <i class="fas fa-plus me-2"></i>Tambah Pemasukan
            </button>
            @if($pemasukan->count() > 0)
            <a href="#" class="btn btn-outline-primary">
                <i class="fas fa-file-pdf me-2"></i>Export PDF
            </a>
            <a href="#" class="btn btn-outline-success">
                <i class="fas fa-file-excel me-2"></i>Export Excel
            </a>
            @endif
        </div>
        <div class="text-muted">
            Menampilkan {{ $pemasukan->firstItem() ?? 0 }} - {{ $pemasukan->lastItem() ?? 0 }} dari {{ $pemasukan->total() }} data
        </div>
    </div>

    {{-- Table --}}
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3">No</th>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Sumber</th>
                            <th class="px-4 py-3">Jumlah</th>
                            <th class="px-4 py-3">Keterangan</th>
                            <th class="px-4 py-3">Dibuat Oleh</th>
                            <th class="px-4 py-3" style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pemasukan as $item)
                        <tr>
                            <td class="px-4 py-3">{{ ($pemasukan->currentPage() - 1) * $pemasukan->perPage() + $loop->iteration }}</td>
                            <td class="px-4 py-3">
                                <i class="fas fa-calendar me-2 text-primary"></i>
                                {{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="badge bg-primary px-3 py-2">{{ $item->sumber }}</span>
                            </td>
                            <td class="px-4 py-3 fw-bold text-success">
                                Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3">{{ $item->keterangan ?: '-' }}</td>
                            <td class="px-4 py-3">
                                @if($item->user)
                                <span class="badge bg-light text-dark">{{ $item->user->name }}</span>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="d-flex gap-2">
                                    <button class="btn btn-warning btn-sm" 
                                            onclick="editPemasukan({{ $item->id }})"
                                            title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('laporan.pemasukan.destroy', $item->id) }}" 
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('Yakin ingin menghapus data ini?')">
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
                                <div class="text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3 opacity-50"></i>
                                    <h5 class="mb-2">Tidak ada data pemasukan</h5>
                                    <p>Silakan tambah data pemasukan terlebih dahulu</p>
                                    <button class="btn btn-success mt-2" data-bs-toggle="modal" data-bs-target="#tambahPemasukanModal">
                                        <i class="fas fa-plus me-2"></i>Tambah Pemasukan
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Pagination --}}
    @if($pemasukan->hasPages())
    <div class="d-flex justify-content-between align-items-center mt-4">
        <div class="text-muted">
            Halaman {{ $pemasukan->currentPage() }} dari {{ $pemasukan->lastPage() }}
        </div>
        <div>
            {{ $pemasukan->links() }}
        </div>
    </div>
    @endif
</div>

{{-- Modal Tambah --}}
<div class="modal fade" id="tambahPemasukanModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Tambah Pemasukan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('laporan.pemasukan.store') }}" method="POST" id="tambahForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tanggal</label>
                        <input type="date" class="form-control" name="tanggal" 
                               value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sumber Pemasukan</label>
                        <select class="form-select" name="sumber" required>
                            <option value="">Pilih Sumber</option>
                            <option value="Iuran Bulanan">Iuran Bulanan</option>
                            <option value="Donasi">Donasi</option>
                            <option value="Bantuan Pemerintah">Bantuan Pemerintah</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jumlah (Rp)</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" class="form-control" name="jumlah" 
                                   placeholder="0" required>
                        </div>
                        <small class="text-muted">Contoh: 1000000 atau 1.000.000</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea class="form-control" name="keterangan" rows="3" 
                                  placeholder="Opsional"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Edit --}}
<div class="modal fade" id="editPemasukanModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Pemasukan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_id" name="id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tanggal</label>
                        <input type="date" class="form-control" id="edit_tanggal" name="tanggal" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sumber Pemasukan</label>
                        <select class="form-select" id="edit_sumber" name="sumber" required>
                            <option value="">Pilih Sumber</option>
                            <option value="Iuran Bulanan">Iuran Bulanan</option>
                            <option value="Donasi">Donasi</option>
                            <option value="Bantuan Pemerintah">Bantuan Pemerintah</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jumlah (Rp)</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" class="form-control" id="edit_jumlah" name="jumlah" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea class="form-control" id="edit_keterangan" name="keterangan" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Update</button>
                </div>
            </form>
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
    
    .btn-sm {
        padding: 0.375rem 0.75rem;
    }
    
    .badge {
        font-weight: 500;
    }
    
    .modal-header {
        border-radius: 10px 10px 0 0;
    }
    
    .modal-content {
        border-radius: 10px;
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
    }
</style>
@endpush

@push('scripts')
<script>
    // Edit function
    function editPemasukan(id) {
        fetch(`/laporan/pemasukan/${id}/edit`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('edit_id').value = data.pemasukan.id;
                    document.getElementById('edit_tanggal').value = data.pemasukan.tanggal;
                    document.getElementById('edit_sumber').value = data.pemasukan.sumber;
                    document.getElementById('edit_jumlah').value = data.pemasukan.jumlah.toLocaleString('id-ID');
                    document.getElementById('edit_keterangan').value = data.pemasukan.keterangan || '';
                    
                    document.getElementById('editForm').action = `/laporan/pemasukan/${id}`;
                    
                    const modal = new bootstrap.Modal(document.getElementById('editPemasukanModal'));
                    modal.show();
                } else {
                    alert('Data tidak ditemukan');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan');
            });
    }

    // Format currency input
    document.addEventListener('DOMContentLoaded', function() {
        // Format input jumlah
        function formatCurrency(input) {
            input.addEventListener('input', function(e) {
                let value = e.target.value.replace(/[^\d]/g, '');
                if (value) {
                    e.target.value = parseInt(value).toLocaleString('id-ID');
                }
            });
        }

        // Apply to inputs
        formatCurrency(document.querySelector('input[name="jumlah"]'));
        formatCurrency(document.getElementById('edit_jumlah'));

        // Convert to number before submit
        document.getElementById('tambahForm')?.addEventListener('submit', function(e) {
            const input = this.querySelector('input[name="jumlah"]');
            if (input) {
                input.value = input.value.replace(/\./g, '');
            }
        });

        document.getElementById('editForm')?.addEventListener('submit', function(e) {
            const input = this.querySelector('input[name="jumlah"]');
            if (input) {
                input.value = input.value.replace(/\./g, '');
            }
        });

        // Auto-hide alerts
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => {
                new bootstrap.Alert(alert).close();
            });
        }, 5000);
    });
</script>
@endpush
@endsection