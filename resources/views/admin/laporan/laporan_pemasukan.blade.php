@extends('layouts.app')

@section('title', 'Pemasukan')
@section('page-title', 'Pemasukan')
@section('icon', 'fas fa-chart-pie')

@section('content')
<script>
    
    // Edit Pemasukan
        function editPemasukan(id) {
            fetch(`/laporan/pemasukan/${id}/edit`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        document.getElementById('edit_tanggal').value = data.pemasukan.tanggal;
                        document.getElementById('edit_sumber').value = data.pemasukan.sumber;
                        document.getElementById('edit_keterangan').value = data.pemasukan.keterangan || '';
                        
                        // Format jumlah dengan pemisah ribuan
                        const jumlah = data.pemasukan.jumlah;
                        if (jumlah) {
                            document.getElementById('edit_jumlah').value = 
                                parseInt(jumlah).toLocaleString('id-ID');
                        }
                        
                        // Set form action
                        const form = document.getElementById('editPemasukanForm');
                        form.action = `/laporan/pemasukan/${id}`;
                        
                        // Trigger Select2 update
                        $('#edit_sumber').val(data.pemasukan.sumber).trigger('change');
                        
                        // Show modal
                        const editModal = new bootstrap.Modal(document.getElementById('editPemasukanModal'));
                        editModal.show();
                    } else {
                        alert(data.message || 'Terjadi kesalahan saat mengambil data');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat mengambil data. Silakan coba lagi.');
                });
        }
        // Reset form edit pemasukan saat modal ditutup
        document.getElementById('editPemasukanModal')?.addEventListener('hidden.bs.modal', function () {
            const form = document.getElementById('editPemasukanForm');
            if (form) {
                form.reset();
                $('#edit_sumber').val(null).trigger('change');
            }
        });
</script>
    <div class="content-container">
        {{-- Alert sukses --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Alert error --}}
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Summary Cards --}}
        <div class="row mb-5 dashboard-card">
            <div class="col-md-4 mb-3 mb-md-0">
                <div class="summary-card income p-4">
                    <i class="fas fa-money-bill-wave mb-3"></i>
                    <h3 class="mb-2">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</h3>
                    <p class="mb-0">Total Pemasukan</p>
                </div>
            </div>
            <div class="col-md-4 mb-3 mb-md-0">
                <div class="summary-card expense p-4">
                    <i class="fas fa-receipt mb-3"></i>
                    <h3 class="mb-2">{{ $pemasukan->total() }}</h3>
                    <p class="mb-0">Jumlah Transaksi</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="summary-card net p-4">
                    <i class="fas fa-chart-line mb-3"></i>
                    <h3 class="mb-2">Rp {{ number_format(($pemasukan->average('jumlah') ?? 0), 0, ',', '.') }}</h3>
                    <p class="mb-0">Rata-rata per Transaksi</p>
                </div>
            </div>
        </div>

        {{-- Filter --}}
        <div class="filter-container mb-5">
            <h5 class="mb-4"><i class="fas fa-filter me-2"></i>Filter Data</h5>
            <form action="{{ route('laporan.pemasukan') }}" method="GET" class="row g-4">
                <div class="col-md-3">
                    <label for="dari_tanggal" class="form-label mb-2">Dari Tanggal</label>
                    <input type="date" class="form-control py-2" id="dari_tanggal" name="dari_tanggal" value="{{ request('dari_tanggal') }}">
                </div>
                <div class="col-md-3">
                    <label for="sampai_tanggal" class="form-label mb-2">Sampai Tanggal</label>
                    <input type="date" class="form-control py-2" id="sampai_tanggal" name="sampai_tanggal" value="{{ request('sampai_tanggal') }}">
                </div>
                <div class="col-md-3">
                    <label for="sumber" class="form-label mb-2">Sumber Pemasukan</label>
                    <select class="form-select py-2" id="sumber" name="sumber">
                        <option value="">Semua Sumber</option>
                        @foreach($pemasukan->pluck('sumber')->unique() as $sumber)
                            <option value="{{ $sumber }}" {{ request('sumber') == $sumber ? 'selected' : '' }}>{{ $sumber }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <div class="d-flex gap-3 w-100">
                        <button type="submit" class="btn btn-primary flex-grow-1 py-2">
                            <i class="fas fa-filter me-2"></i>Filter
                        </button>
                        <a href="{{ route('laporan.pemasukan') }}" class="btn btn-outline-secondary py-2 px-3">
                            <i class="fas fa-refresh"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>

        {{-- Chart --}}
        @if($chartData->count() > 0)
        <div class="chart-container mb-5 p-4 bg-white rounded shadow-sm">
            <h5 class="mb-4"><i class="fas fa-chart-line me-2"></i>Grafik Pemasukan per Bulan</h5>
            <canvas id="pemasukanChart" height="120"></canvas>
        </div>
        @endif

        {{-- Action Buttons --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex gap-2">
                <button class="btn btn-success px-4 py-2" data-bs-toggle="modal" data-bs-target="#tambahPemasukanModal">
                    <i class="fas fa-plus me-2"></i>Tambah Pemasukan
                </button>
                <button class="btn btn-outline-primary px-3 py-2">
                    <i class="fas fa-file-pdf me-2"></i>Export PDF
                </button>
                <button class="btn btn-outline-success px-3 py-2">
                    <i class="fas fa-file-excel me-2"></i>Export Excel
                </button>
            </div>
            <div>
                <span class="badge bg-primary px-3 py-2 fs-6">{{ $pemasukan->total() }} Transaksi</span>
            </div>
        </div>

        {{-- Tabel Data --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="py-3 px-4">No</th>
                                <th class="py-3 px-4">Tanggal</th>
                                <th class="py-3 px-4">Sumber</th>
                                <th class="py-3 px-4">Jumlah</th>
                                <th class="py-3 px-4">Keterangan</th>
                                <th class="py-3 px-4">Dibuat Oleh</th>
                                <th class="py-3 px-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pemasukan as $item)
                                <tr>
                                    <td class="py-3 px-4">
                                        {{ ($pemasukan->currentPage() - 1) * $pemasukan->perPage() + $loop->iteration }}
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="text-dark">
                                            <i class="fas fa-calendar me-2 text-primary"></i>
                                            {{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="badge bg-primary px-3 py-2">{{ $item->sumber }}</span>
                                    </td>
                                    <td class="py-3 px-4 fw-bold text-success">
                                        <i class="fas fa-money-bill-wave me-2"></i>
                                        Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3 px-4">{{ $item->keterangan ?? '-' }}</td>
                                    <td class="py-3 px-4">
                                        @if($item->user)
                                            <span class="badge bg-light text-dark px-3 py-2">{{ $item->user->name }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-warning btn-sm px-3 py-2" title="Edit" onclick="editPemasukan({{ $item->id }})">
                                                <i class="fas fa-edit me-1"></i>Edit
                                            </button>
                                            <form action="{{ route('laporan.pemasukan.destroy', $item->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm px-3 py-2" title="Hapus" onclick="return confirm('Yakin ingin menghapus data ini?')">
                                                    <i class="fas fa-trash me-1"></i>Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="empty-state">
                                            <i class="fas fa-inbox fa-3x mb-4 text-muted"></i>
                                            <h5 class="text-muted mb-2">Tidak ada data pemasukan</h5>
                                            <p class="text-muted mb-4">Silakan tambah data pemasukan terlebih dahulu</p>
                                            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#tambahPemasukanModal">
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
        <div class="d-flex justify-content-between align-items-center mt-5 pt-3 border-top">
            <div class="text-muted">
                Menampilkan {{ $pemasukan->firstItem() }} - {{ $pemasukan->lastItem() }} dari {{ $pemasukan->total() }} data
            </div>
            <div>
                {{ $pemasukan->links() }}
            </div>
        </div>
        @endif
    </div>

    <!-- Modal Tambah Pemasukan -->
    <div class="modal fade" id="tambahPemasukanModal" tabindex="-1" aria-labelledby="tambahPemasukanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="tambahPemasukanModalLabel">
                        <i class="fas fa-plus me-2"></i>Tambah Pemasukan Baru
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('laporan.pemasukan.store') }}" method="POST" id="tambahPemasukanForm">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label for="tanggal" class="form-label mb-2">Tanggal</label>
                                <input type="date" class="form-control py-2 px-3" id="tanggal" name="tanggal" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="sumber" class="form-label mb-2">Sumber Pemasukan</label>
                                <select class="form-control py-2 px-3" id="sumber" name="sumber" required>
                                    <option value="">Pilih Sumber</option>
                                    <option value="Iuran Bulanan">Iuran Bulanan</option>
                                    <option value="Donasi">Donasi</option>
                                    <option value="Bantuan Pemerintah">Bantuan Pemerintah</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="jumlah" class="form-label mb-2">Jumlah (Rp)</label>
                                <div class="input-group">
                                    <span class="input-group-text py-2 px-3 bg-light">Rp</span>
                                    <input type="text" class="form-control py-2 px-3" id="jumlah" name="jumlah" placeholder="0" required>
                                </div>
                                <div class="form-text mt-2">
                                    <i class="fas fa-info-circle me-1"></i>Contoh: 1.000.000
                                </div>
                            </div>
                            <div class="col-12">
                                <label for="keterangan" class="form-label mb-2">Keterangan (Opsional)</label>
                                <textarea class="form-control py-2 px-3" id="keterangan" name="keterangan" rows="4" placeholder="Masukkan keterangan tambahan jika diperlukan"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer p-4 pt-0">
                        <button type="button" class="btn btn-outline-secondary px-4 py-2" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success px-4 py-2">
                            <i class="fas fa-save me-2"></i>Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Pemasukan -->
    <div class="modal fade" id="editPemasukanModal" tabindex="-1" aria-labelledby="editPemasukanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title" id="editPemasukanModalLabel">
                        <i class="fas fa-edit me-2"></i>Edit Pemasukan
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editPemasukanForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label for="edit_tanggal" class="form-label mb-2">Tanggal</label>
                                <input type="date" class="form-control py-2 px-3" id="edit_tanggal" name="tanggal" required>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_sumber" class="form-label mb-2">Sumber Pemasukan</label>
                                <select class="form-control py-2 px-3" id="edit_sumber" name="sumber" required>
                                    <option value="">Pilih Sumber</option>
                                    <option value="Iuran Bulanan">Iuran Bulanan</option>
                                    <option value="Donasi">Donasi</option>
                                    <option value="Bantuan Pemerintah">Bantuan Pemerintah</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="edit_jumlah" class="form-label mb-2">Jumlah (Rp)</label>
                                <div class="input-group">
                                    <span class="input-group-text py-2 px-3 bg-light">Rp</span>
                                    <input type="text" class="form-control py-2 px-3" id="edit_jumlah" name="jumlah" placeholder="0" required>
                                </div>
                                <div class="form-text mt-2">
                                    <i class="fas fa-info-circle me-1"></i>Contoh: 1.000.000
                                </div>
                            </div>
                            <div class="col-12">
                                <label for="edit_keterangan" class="form-label mb-2">Keterangan (Opsional)</label>
                                <textarea class="form-control py-2 px-3" id="edit_keterangan" name="keterangan" rows="4" placeholder="Masukkan keterangan tambahan jika diperlukan"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer p-4 pt-0">
                        <button type="button" class="btn btn-outline-secondary px-4 py-2" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success px-4 py-2">
                            <i class="fas fa-save me-2"></i>Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        // Toggle Sidebar
        document.getElementById('toggleSidebar').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');

            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
        });

        // Mobile Menu Toggle
        document.getElementById('mobileMenuBtn').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('show');
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');

            if (window.innerWidth <= 768 &&
                !sidebar.contains(event.target) &&
                !mobileMenuBtn.contains(event.target) &&
                sidebar.classList.contains('show')) {
                sidebar.classList.remove('show');
            }
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            const sidebar = document.getElementById('sidebar');
            if (window.innerWidth > 768) {
                sidebar.classList.remove('show');
            }
        });

        // Initialize Select2
        $(document).ready(function() {
            $('#sumber').select2({
                placeholder: 'Pilih sumber pemasukan',
                allowClear: true,
                width: '100%'
            });
            
            $('#edit_sumber').select2({
                placeholder: 'Pilih sumber pemasukan',
                allowClear: true,
                width: '100%'
            });
        });

        // Format currency untuk input
        function formatCurrency(input) {
            // Hapus semua karakter non-digit
            let value = input.value.replace(/[^\d]/g, '');
            
            // Format dengan titik sebagai pemisah ribuan
            if (value) {
                const numericValue = parseInt(value);
                if (!isNaN(numericValue)) {
                    input.value = numericValue.toLocaleString('id-ID');
                }
            }
        }

        // Format currency untuk input tambah
        document.getElementById('jumlah')?.addEventListener('input', function(e) {
            formatCurrency(e.target);
        });

        // Format currency untuk input edit
        document.getElementById('edit_jumlah')?.addEventListener('input', function(e) {
            formatCurrency(e.target);
        });

        // Fungsi untuk mengubah format currency ke angka sebelum submit
        function formatCurrencyToNumber(value) {
            return value.toString().replace(/\./g, '');
        }

        // Tambahkan event listener untuk form submission
        document.addEventListener('DOMContentLoaded', function() {
            // Form tambah pemasukan
            const formTambah = document.getElementById('tambahPemasukanForm');
            if (formTambah) {
                formTambah.addEventListener('submit', function(e) {
                    const jumlahInput = document.getElementById('jumlah');
                    if (jumlahInput) {
                        // Konversi format currency ke angka murni
                        const rawValue = formatCurrencyToNumber(jumlahInput.value);
                        jumlahInput.value = rawValue;
                    }
                });
            }

            // Form edit pemasukan
            const formEdit = document.getElementById('editPemasukanForm');
            if (formEdit) {
                formEdit.addEventListener('submit', function(e) {
                    const jumlahInput = document.getElementById('edit_jumlah');
                    if (jumlahInput) {
                        // Konversi format currency ke angka murni
                        const rawValue = formatCurrencyToNumber(jumlahInput.value);
                        jumlahInput.value = rawValue;
                    }
                });
            }
        });

        // Chart.js
        @if($chartData->count() > 0)
        const ctx = document.getElementById('pemasukanChart').getContext('2d');
        const labels = {!! json_encode($chartData->map(function($item) {
            return \Carbon\Carbon::createFromFormat('Y-m', $item->bulan_tahun)->format('M Y');
        })) !!};
        const data = {!! json_encode($chartData->pluck('total')) !!};

        const pemasukanChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Pemasukan',
                    data: data,
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#28a745',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: {
                                size: 14
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleFont: {
                            size: 14
                        },
                        bodyFont: {
                            size: 14
                        },
                        padding: 12,
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
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            font: {
                                size: 12
                            },
                            padding: 10,
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        },
                        title: {
                            display: true,
                            text: 'Jumlah (Rp)',
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            font: {
                                size: 12
                            },
                            padding: 10
                        },
                        title: {
                            display: true,
                            text: 'Bulan',
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        }
                    }
                }
            }
        });
        @endif

        

        // Validasi input hanya angka
        function validateNumberInput(event) {
            const key = event.key;
            // Izinkan: angka, backspace, delete, tab, escape, enter
            const allowedKeys = ['Backspace', 'Delete', 'Tab', 'Escape', 'Enter'];
            // Izinkan: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
            const ctrlKeys = ['a', 'c', 'v', 'x'];
            
            if (allowedKeys.includes(key) ||
                (event.ctrlKey && ctrlKeys.includes(key.toLowerCase()))) {
                return;
            }
            
            // Izinkan hanya angka
            if (!/^\d$/.test(key)) {
                event.preventDefault();
            }
        }

        // Tambahkan event listener untuk validasi angka
        document.getElementById('jumlah')?.addEventListener('keydown', validateNumberInput);
        document.getElementById('edit_jumlah')?.addEventListener('keydown', validateNumberInput);

        // Reset form tambah pemasukan saat modal ditutup
        document.getElementById('tambahPemasukanModal')?.addEventListener('hidden.bs.modal', function () {
            const form = document.getElementById('tambahPemasukanForm');
            if (form) {
                form.reset();
                document.getElementById('tanggal').value = new Date().toISOString().split('T')[0];
                $('#sumber').val(null).trigger('change');
            }
        });

        
    </script>
@endpush