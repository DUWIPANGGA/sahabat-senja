@extends('layouts.app')

@section('title', 'Pemasukan')
@section('page-title', 'Pemasukan')
@section('icon', 'fas fa-chart-pie')

@section('content')

        <div class="content-container">
            {{-- Alert sukses --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Alert error --}}
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Summary Cards --}}
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="summary-card income">
                        <i class="fas fa-money-bill-wave"></i>
                        <h3>Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</h3>
                        <p>Total Pemasukan</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="summary-card expense">
                        <i class="fas fa-receipt"></i>
                        <h3>{{ $pemasukan->total() }}</h3>
                        <p>Jumlah Transaksi</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="summary-card net">
                        <i class="fas fa-chart-line"></i>
                        <h3>Rp {{ number_format(($pemasukan->average('jumlah') ?? 0), 0, ',', '.') }}</h3>
                        <p>Rata-rata per Transaksi</p>
                    </div>
                </div>
            </div>

            {{-- Filter --}}
            <div class="filter-container">
                <form action="{{ route('laporan.pemasukan') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label for="dari_tanggal" class="form-label">Dari Tanggal</label>
                        <input type="date" class="form-control" id="dari_tanggal" name="dari_tanggal" value="{{ request('dari_tanggal') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="sampai_tanggal" class="form-label">Sampai Tanggal</label>
                        <input type="date" class="form-control" id="sampai_tanggal" name="sampai_tanggal" value="{{ request('sampai_tanggal') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="sumber" class="form-label">Sumber Pemasukan</label>
                        <select class="form-select" id="sumber" name="sumber">
                            <option value="">Semua Sumber</option>
                            @foreach($pemasukan->pluck('sumber')->unique() as $sumber)
                                <option value="{{ $sumber }}" {{ request('sumber') == $sumber ? 'selected' : '' }}>{{ $sumber }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <div class="d-flex gap-2 w-100">
                            <button type="submit" class="btn btn-primary flex-grow-1">
                                <i class="fas fa-filter me-1"></i>Filter
                            </button>
                            <a href="{{ route('laporan.pemasukan') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-refresh"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Chart --}}
            @if($chartData->count() > 0)
            <div class="chart-container">
                <h5 class="mb-3"><i class="fas fa-chart-line me-2"></i>Grafik Pemasukan per Bulan</h5>
                <canvas id="pemasukanChart" height="100"></canvas>
            </div>
            @endif

            {{-- Action Buttons --}}
            <div class="d-flex justify-content-between mb-3">
                <div>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#tambahPemasukanModal">
                        <i class="fas fa-plus me-1"></i>Tambah Pemasukan
                    </button>
                    <button class="btn btn-outline-primary ms-2">
                        <i class="fas fa-file-pdf me-1"></i>Export PDF
                    </button>
                    <button class="btn btn-outline-success ms-2">
                        <i class="fas fa-file-excel me-1"></i>Export Excel
                    </button>
                </div>
                <div>
                    <span class="badge bg-primary">{{ $pemasukan->total() }} Transaksi</span>
                </div>
            </div>

            {{-- Tabel Data --}}
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Sumber</th>
                                    <th>Jumlah</th>
                                    <th>Keterangan</th>
                                    <th>Dibuat Oleh</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pemasukan as $item)
                                    <tr>
                                        <td>{{ ($pemasukan->currentPage() - 1) * $pemasukan->perPage() + $loop->iteration }}</td>
                                        <td>
                                            <span class="text-dark">
                                                <i class="fas fa-calendar me-1 text-primary"></i>
                                                {{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">{{ $item->sumber }}</span>
                                        </td>
                                        <td class="fw-bold text-success">
                                            Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                                        </td>
                                        <td>{{ $item->keterangan ?? '-' }}</td>
                                        <td>
                                            @if($item->user)
                                                <span class="badge bg-light text-dark">{{ $item->user->name }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="btn btn-warning btn-sm" title="Edit" onclick="editPemasukan({{ $item->id }})">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form action="{{ route('laporan.pemasukan.destroy', $item->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Hapus" onclick="return confirm('Yakin ingin menghapus data ini?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <div class="empty-state">
                                                <i class="fas fa-inbox fa-2x mb-3 text-muted"></i>
                                                <h5 class="text-muted">Tidak ada data pemasukan</h5>
                                                <p class="text-muted">Silakan tambah data pemasukan terlebih dahulu</p>
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
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
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
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahPemasukanModalLabel">
                        <i class="fas fa-plus me-2"></i>Tambah Pemasukan Baru
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('laporan.pemasukan.store') }}" method="POST" id="tambahPemasukanForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="tanggal" class="form-label">Tanggal</label>
                            <input type="date" class="form-control" id="tanggal" name="tanggal" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="sumber" class="form-label">Sumber Pemasukan</label>
                            <select class="form-control" id="sumber" name="sumber" required>
                                <option value="">Pilih Sumber</option>
                                <option value="Iuran Bulanan">Iuran Bulanan</option>
                                <option value="Donasi">Donasi</option>
                                <option value="Bantuan Pemerintah">Bantuan Pemerintah</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="jumlah" class="form-label">Jumlah (Rp)</label>
                            <div class="currency-input-group">
                                <input type="text" class="form-control" id="jumlah" name="jumlah" placeholder="0" required>
                            </div>
                            <small class="text-muted">Contoh: 1.000.000</small>
                        </div>
                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan (Opsional)</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Pemasukan -->
    <div class="modal fade" id="editPemasukanModal" tabindex="-1" aria-labelledby="editPemasukanModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPemasukanModalLabel">
                        <i class="fas fa-edit me-2"></i>Edit Pemasukan
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editPemasukanForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_tanggal" class="form-label">Tanggal</label>
                            <input type="date" class="form-control" id="edit_tanggal" name="tanggal" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_sumber" class="form-label">Sumber Pemasukan</label>
                            <select class="form-control" id="edit_sumber" name="sumber" required>
                                <option value="">Pilih Sumber</option>
                                <option value="Iuran Bulanan">Iuran Bulanan</option>
                                <option value="Donasi">Donasi</option>
                                <option value="Bantuan Pemerintah">Bantuan Pemerintah</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_jumlah" class="form-label">Jumlah (Rp)</label>
                            <div class="currency-input-group">
                                <input type="text" class="form-control" id="edit_jumlah" name="jumlah" placeholder="0" required>
                            </div>
                            <small class="text-muted">Contoh: 1.000.000</small>
                        </div>
                        <div class="mb-3">
                            <label for="edit_keterangan" class="form-label">Keterangan (Opsional)</label>
                            <textarea class="form-control" id="edit_keterangan" name="keterangan" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Update</button>
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
                allowClear: true
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
        const labels = {!! $chartData->map(function($item) {
            return \Carbon\Carbon::createFromDate($item->tahun, $item->bulan, 1)->format('M Y');
        }) !!};
        const data = {!! $chartData->pluck('total') !!};

        const pemasukanChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Pemasukan',
                    data: data,
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
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
                        }
                    }
                }
            }
        });
        @endif

        // Edit Pemasukan
        function editPemasukan(id) {
            fetch(`/laporan/pemasukan/edit/${id}`)
                .then(response => response.json())
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
                        document.getElementById('editPemasukanForm').action = `/laporan/pemasukan/update/${id}`;
                        
                        // Show modal
                        const editModal = new bootstrap.Modal(document.getElementById('editPemasukanModal'));
                        editModal.show();
                    } else {
                        alert(data.message || 'Terjadi kesalahan saat mengambil data');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat mengambil data');
                });
        }

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
            }
        });

        // Reset form edit pemasukan saat modal ditutup
        document.getElementById('editPemasukanModal')?.addEventListener('hidden.bs.modal', function () {
            const form = document.getElementById('editPemasukanForm');
            if (form) {
                form.reset();
            }
        });
    </script>
@endpush