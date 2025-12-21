@extends('layouts.app')

@section('title', 'Pengeluaran')
@section('page-title', 'Pengeluaran')
@section('icon', 'fas fa-chart-pie')

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
            <div class="card border-start border-danger border-4 shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-danger text-uppercase mb-1">
                                Total Pengeluaran
                            </div>
                            <div class="h5 mb-0 fw-bold">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-receipt fa-2x text-danger"></i>
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
                            <div class="h5 mb-0 fw-bold">{{ $pengeluaran->total() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-info"></i>
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
                            <div class="h5 mb-0 fw-bold">Rp {{ number_format($pengeluaran->avg('jumlah') ?? 0, 0, ',', '.') }}</div>
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
                <span class="badge bg-primary fs-6">{{ $pengeluaran->total() }} Transaksi</span>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('laporan.pengeluaran') }}" method="GET" class="row g-3">
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
                <div class="col-md-4">
                    <label for="keterangan" class="form-label text-muted">Keterangan</label>
                    <input type="text" class="form-control" id="keterangan" name="keterangan" 
                           value="{{ request('keterangan') }}" placeholder="Cari keterangan...">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <div class="d-flex gap-2 w-100">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="fas fa-filter me-2"></i>Filter
                        </button>
                        <a href="{{ route('laporan.pengeluaran') }}" class="btn btn-outline-secondary">
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
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#tambahPengeluaranModal">
                <i class="fas fa-plus me-2"></i>Tambah Pengeluaran
            </button>
            @if($pengeluaran->count() > 0)
            <a href="#" class="btn btn-outline-primary">
                <i class="fas fa-file-pdf me-2"></i>Export PDF
            </a>
            <a href="#" class="btn btn-outline-success">
                <i class="fas fa-file-excel me-2"></i>Export Excel
            </a>
            @endif
        </div>
        <div class="text-muted">
            Menampilkan {{ $pengeluaran->firstItem() ?? 0 }} - {{ $pengeluaran->lastItem() ?? 0 }} dari {{ $pengeluaran->total() }} data
        </div>
    </div>

    {{-- Chart --}}
    @if(isset($chartData) && $chartData->count() > 0)
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 fw-bold text-dark"><i class="fas fa-chart-line me-2"></i>Grafik Pengeluaran per Bulan</h6>
        </div>
        <div class="card-body">
            <canvas id="pengeluaranChart" height="100"></canvas>
        </div>
    </div>
    @endif

    {{-- Table --}}
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3">No</th>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Keterangan</th>
                            <th class="px-4 py-3">Jumlah</th>
                            <th class="px-4 py-3">Bukti</th>
                            <th class="px-4 py-3">Dibuat Oleh</th>
                            <th class="px-4 py-3" style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pengeluaran as $item)
                        <tr>
                            <td class="px-4 py-3">{{ ($pengeluaran->currentPage() - 1) * $pengeluaran->perPage() + $loop->iteration }}</td>
                            <td class="px-4 py-3">
                                <i class="fas fa-calendar me-2 text-primary"></i>
                                {{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="badge bg-danger px-3 py-2">{{ $item->keterangan }}</span>
                            </td>
                            <td class="px-4 py-3 fw-bold text-danger">
                                Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3">
                                @if($item->bukti)
                                <a href="{{ Storage::url($item->bukti) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-file-invoice me-1"></i>Lihat
                                </a>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($item->user)
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 32px; height: 32px; font-size: 0.9rem; margin-right: 8px;">
                                        {{ substr($item->user->name, 0, 1) }}
                                    </div>
                                    <span>{{ $item->user->name }}</span>
                                </div>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="d-flex gap-2">
                                    <button class="btn btn-warning btn-sm" 
                                            onclick="editPengeluaran({{ $item->id }})"
                                            title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('laporan.pengeluaran.destroy', $item->id) }}" 
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
                                    <h5 class="mb-2">Tidak ada data pengeluaran</h5>
                                    <p>Silakan tambah data pengeluaran terlebih dahulu</p>
                                    <button class="btn btn-success mt-2" data-bs-toggle="modal" data-bs-target="#tambahPengeluaranModal">
                                        <i class="fas fa-plus me-2"></i>Tambah Pengeluaran
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
    @if($pengeluaran->hasPages())
    <div class="d-flex justify-content-between align-items-center mt-4">
        <div class="text-muted">
            Halaman {{ $pengeluaran->currentPage() }} dari {{ $pengeluaran->lastPage() }}
        </div>
        <div>
            {{ $pengeluaran->links() }}
        </div>
    </div>
    @endif
</div>

{{-- Modal Tambah --}}
<div class="modal fade" id="tambahPengeluaranModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Tambah Pengeluaran</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('laporan.pengeluaran.store') }}" method="POST" enctype="multipart/form-data" id="tambahForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tanggal</label>
                        <input type="date" class="form-control" name="tanggal" 
                               value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <input type="text" class="form-control" name="keterangan" 
                               placeholder="Contoh: Gaji Perawat, Obat-obatan, dll" required>
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
                        <label class="form-label">Bukti (Opsional)</label>
                        <input type="file" class="form-control" name="bukti" accept="image/*,.pdf">
                        <small class="text-muted">Format: JPG, PNG, PDF (Max: 2MB)</small>
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
<div class="modal fade" id="editPengeluaranModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Pengeluaran</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_id" name="id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tanggal</label>
                        <input type="date" class="form-control" id="edit_tanggal" name="tanggal" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <input type="text" class="form-control" id="edit_keterangan" name="keterangan" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jumlah (Rp)</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" class="form-control" id="edit_jumlah" name="jumlah" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bukti (Opsional)</label>
                        <input type="file" class="form-control" id="edit_bukti" name="bukti" accept="image/*,.pdf">
                        <small class="text-muted">Biarkan kosong jika tidak ingin mengubah</small>
                        <div id="current_bukti" class="mt-2"></div>
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
    
    .badge {
        font-weight: 500;
        white-space: normal;
        word-break: break-word;
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
        
        .badge {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Edit function
    function editPengeluaran(id) {
        fetch(`/laporan/pengeluaran/${id}/edit`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    document.getElementById('edit_id').value = data.pengeluaran.id;
                    document.getElementById('edit_tanggal').value = data.pengeluaran.tanggal;
                    document.getElementById('edit_keterangan').value = data.pengeluaran.keterangan;
                    document.getElementById('edit_jumlah').value = data.pengeluaran.jumlah.toLocaleString('id-ID');
                    
                    // Show current bukti
                    const buktiContainer = document.getElementById('current_bukti');
                    if (data.pengeluaran.bukti) {
                        buktiContainer.innerHTML = `
                            <div class="alert alert-info p-2 mb-0">
                                <i class="fas fa-file me-2"></i> 
                                <small>Bukti saat ini: </small>
                                <a href="${data.pengeluaran.bukti_url}" target="_blank" class="text-decoration-underline">
                                    Lihat Bukti
                                </a>
                            </div>
                        `;
                    } else {
                        buktiContainer.innerHTML = '<div class="alert alert-warning p-2 mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Tidak ada bukti</div>';
                    }
                    
                    document.getElementById('editForm').action = `/laporan/pengeluaran/${id}`;
                    
                    const modal = new bootstrap.Modal(document.getElementById('editPengeluaranModal'));
                    modal.show();
                } else {
                    alert(data.message || 'Data tidak ditemukan');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat mengambil data');
            });
    }

    // Format currency input
    document.addEventListener('DOMContentLoaded', function() {
        // Format input jumlah
        function formatCurrency(input) {
            if (input) {
                input.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/[^\d]/g, '');
                    if (value) {
                        e.target.value = parseInt(value).toLocaleString('id-ID');
                    }
                });
            }
        }

        // Apply to inputs
        formatCurrency(document.querySelector('input[name="jumlah"]'));
        formatCurrency(document.getElementById('edit_jumlah'));

        // Convert to number before submit
        function prepareFormForSubmit(formId) {
            const form = document.getElementById(formId);
            if (form) {
                const jumlahInput = form.querySelector('input[name="jumlah"]');
                if (jumlahInput && jumlahInput.value) {
                    jumlahInput.value = jumlahInput.value.replace(/\./g, '');
                }
            }
        }

        document.getElementById('tambahForm')?.addEventListener('submit', function(e) {
            prepareFormForSubmit('tambahForm');
        });

        document.getElementById('editForm')?.addEventListener('submit', function(e) {
            prepareFormForSubmit('editForm');
        });

        // Auto-hide alerts
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => {
                new bootstrap.Alert(alert).close();
            });
        }, 5000);

        // Chart.js
        @if(isset($chartData) && $chartData->count() > 0)
        const ctx = document.getElementById('pengeluaranChart').getContext('2d');
        const labels = {!! json_encode($chartData->map(function($item) {
            return \Carbon\Carbon::createFromFormat('Y-m', $item->bulan_tahun)->format('M Y');
        })) !!};
        
        const data = {!! json_encode($chartData->pluck('total')) !!};

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Pengeluaran',
                    data: data,
                    borderColor: '#e53935',
                    backgroundColor: 'rgba(229, 57, 53, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    }
                }
            }
        });
        @endif

        // Reset edit modal
        document.getElementById('editPengeluaranModal')?.addEventListener('hidden.bs.modal', function() {
            document.getElementById('current_bukti').innerHTML = '';
        });
    });
</script>
@endpush
@endsection