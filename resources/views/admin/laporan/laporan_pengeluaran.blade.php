@extends('layouts.app')

@section('title', 'Grafik Keuangan')
@section('page-title', 'Grafik Keuangan')
@section('icon', 'fas fa-chart-pie')

@section('content')

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Header -->
        <div class="top-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <button class="mobile-menu-btn" id="mobileMenuBtn">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="header-title">
                    <i class="fas fa-chart-bar"></i>Laporan Pengeluaran
                </h1>
            </div>
            
            <div class="d-flex align-items-center">
                <div class="user-info">
                    <div class="user-avatar">A</div>
                    <div>
                        <div class="fw-bold">Admin</div>
                        <small class="text-muted">Administrator</small>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                    </button>
                </form>
            </div>
        </div>

        <!-- Content -->
        <div class="content-container">
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

            {{-- Summary Cards --}}
            <div class="stats-grid mb-4">
                <div class="dashboard-card">
                    <div class="card-icon expense">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <div class="card-title">Total Pengeluaran</div>
                    <div class="card-value">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</div>
                </div>
                <div class="dashboard-card">
                    <div class="card-icon income">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="card-title">Jumlah Transaksi</div>
                    <div class="card-value">{{ $pengeluaran->total() }}</div>
                </div>
                <div class="dashboard-card">
                    <div class="card-icon net">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="card-title">Rata-rata per Transaksi</div>
                    <div class="card-value">Rp {{ number_format(($pengeluaran->avg('jumlah') ?? 0), 0, ',', '.') }}</div>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="quick-actions mb-4">
                <button class="action-btn" data-bs-toggle="modal" data-bs-target="#tambahPengeluaranModal">
                    <div class="action-icon">
                        <i class="fas fa-plus"></i>
                    </div>
                    <span>Tambah Pengeluaran</span>
                </button>
                <button class="action-btn">
                    <div class="action-icon">
                        <i class="fas fa-file-pdf"></i>
                    </div>
                    <span>Export PDF</span>
                </button>
                <button class="action-btn">
                    <div class="action-icon">
                        <i class="fas fa-file-excel"></i>
                    </div>
                    <span>Export Excel</span>
                </button>
                <button class="action-btn">
                    <div class="action-icon">
                        <i class="fas fa-filter"></i>
                    </div>
                    <span>Filter Lanjutan</span>
                </button>
            </div>

            {{-- Filter --}}
            <div class="filter-container">
                <form action="{{ route('laporan.pengeluaran') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label for="dari_tanggal" class="form-label">Dari Tanggal</label>
                        <input type="date" class="form-control" id="dari_tanggal" name="dari_tanggal" value="{{ request('dari_tanggal') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="sampai_tanggal" class="form-label">Sampai Tanggal</label>
                        <input type="date" class="form-control" id="sampai_tanggal" name="sampai_tanggal" value="{{ request('sampai_tanggal') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <input type="text" class="form-control" id="keterangan" name="keterangan" value="{{ request('keterangan') }}" placeholder="Cari keterangan...">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <div class="d-flex gap-2 w-100">
                            <button type="submit" class="btn btn-primary flex-grow-1">
                                <i class="fas fa-filter me-1"></i>Filter
                            </button>
                            <a href="{{ route('laporan.pengeluaran') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-refresh"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Chart --}}
            @if($chartData->count() > 0)
            <div class="chart-container">
                <div class="card-header">
                    <h3><i class="fas fa-chart-line"></i>Grafik Pengeluaran per Bulan</h3>
                </div>
                <canvas id="pengeluaranChart" height="100"></canvas>
            </div>
            @endif

            {{-- Tabel Data --}}
            <div class="data-card">
                <div class="card-header">
                    <h3><i class="fas fa-list"></i>Data Pengeluaran</h3>
                    <div>
                        <span class="badge bg-primary badge-custom">{{ $pengeluaran->total() }} Transaksi</span>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Keterangan</th>
                                <th>Jumlah</th>
                                <th>Bukti</th>
                                <th>Dibuat Oleh</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pengeluaran as $item)
                                <tr>
                                    <td>{{ ($pengeluaran->currentPage() - 1) * $pengeluaran->perPage() + $loop->iteration }}</td>
                                    <td>
                                        <span class="text-dark">
                                            <i class="fas fa-calendar me-2 text-primary"></i>
                                            {{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-danger badge-custom">{{ $item->keterangan }}</span>
                                    </td>
                                    <td class="fw-bold text-danger">
                                        Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                                    </td>
                                    <td>
                                        @if($item->bukti)
                                            <a href="{{ asset('storage/' . $item->bukti) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-file-invoice me-1"></i>Lihat
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->user)
                                            <div class="d-flex align-items-center">
                                                <div class="user-avatar" style="width: 30px; height: 30px; font-size: 0.8rem; margin-right: 8px;">
                                                    {{ substr($item->user->name, 0, 1) }}
                                                </div>
                                                <span>{{ $item->user->name }}</span>
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn btn-warning btn-action" title="Edit" onclick="editPengeluaran({{ $item->id }})">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('laporan.pengeluaran.destroy', $item->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-action" title="Hapus" onclick="return confirm('Yakin ingin menghapus data ini?')">
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
                                            <i class="fas fa-inbox fa-3x mb-3"></i>
                                            <h5 class="text-muted">Tidak ada data pengeluaran</h5>
                                            <p class="text-muted">Silakan tambah data pengeluaran terlebih dahulu</p>
                                            <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#tambahPengeluaranModal">
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

            {{-- Pagination --}}
            @if($pengeluaran->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Menampilkan {{ $pengeluaran->firstItem() }} - {{ $pengeluaran->lastItem() }} dari {{ $pengeluaran->total() }} data
                </div>
                <div>
                    {{ $pengeluaran->links() }}
                </div>
            </div>
            @endif
        </div>

        <!-- Footer -->
        <footer class="footer">
            <div class="container text-center">
                <p class="mb-0">&copy; {{ date('Y') }} Sahabat Senja. Sistem Informasi Layanan Panti Jompo Berbasis Website & Mobile.</p>
            </div>
        </footer>
    </div>

    <!-- Modal Tambah Pengeluaran -->
    <div class="modal fade" id="tambahPengeluaranModal" tabindex="-1" aria-labelledby="tambahPengeluaranModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-plus me-2"></i>Tambah Pengeluaran Baru
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('laporan.pengeluaran.store') }}" method="POST" enctype="multipart/form-data" id="tambahPengeluaranForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="tanggal" class="form-label">Tanggal</label>
                            <input type="date" class="form-control" id="tanggal" name="tanggal" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan Pengeluaran</label>
                            <input type="text" class="form-control" id="keterangan" name="keterangan" placeholder="Contoh: Gaji Perawat, Obat-obatan, dll" required>
                        </div>
                        <div class="mb-3">
                            <label for="jumlah" class="form-label">Jumlah (Rp)</label>
                            <div class="currency-input-group">
                                <input type="text" class="form-control" id="jumlah" name="jumlah" placeholder="0" required>
                            </div>
                            <small class="text-muted">Contoh: 1.000.000</small>
                        </div>
                        <div class="mb-3">
                            <label for="bukti" class="form-label">Bukti Pengeluaran (Opsional)</label>
                            <input type="file" class="form-control" id="bukti" name="bukti" accept="image/*,.pdf">
                            <small class="text-muted">Format: JPG, PNG, PDF (Max: 2MB)</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Pengeluaran -->
    <div class="modal fade" id="editPengeluaranModal" tabindex="-1" aria-labelledby="editPengeluaranModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-edit me-2"></i>Edit Pengeluaran
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editPengeluaranForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_tanggal" class="form-label">Tanggal</label>
                            <input type="date" class="form-control" id="edit_tanggal" name="tanggal" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_keterangan" class="form-label">Keterangan Pengeluaran</label>
                            <input type="text" class="form-control" id="edit_keterangan" name="keterangan" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_jumlah" class="form-label">Jumlah (Rp)</label>
                            <div class="currency-input-group">
                                <input type="text" class="form-control" id="edit_jumlah" name="jumlah" placeholder="0" required>
                            </div>
                            <small class="text-muted">Contoh: 1.000.000</small>
                        </div>
                        <div class="mb-3">
                            <label for="edit_bukti" class="form-label">Bukti Pengeluaran (Opsional)</label>
                            <input type="file" class="form-control" id="edit_bukti" name="bukti" accept="image/*,.pdf">
                            <small class="text-muted">Biarkan kosong jika tidak ingin mengubah</small>
                            <div id="current_bukti" class="mt-2"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('script')
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
            // Form tambah pengeluaran
            const formTambah = document.getElementById('tambahPengeluaranForm');
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

            // Form edit pengeluaran
            const formEdit = document.getElementById('editPengeluaranForm');
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
        const ctx = document.getElementById('pengeluaranChart').getContext('2d');
        const labels = {!! json_encode($chartData->map(function($item) {
            return \Carbon\Carbon::createFromFormat('Y-m', $item->bulan_tahun)->format('M Y');
        })) !!};
        
        const data = {!! json_encode($chartData->pluck('total')) !!};

        const pengeluaranChart = new Chart(ctx, {
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

        // Edit Pengeluaran
        function editPengeluaran(id) {
            fetch(`/laporan/pengeluaran/edit/${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('edit_tanggal').value = data.pengeluaran.tanggal;
                        document.getElementById('edit_keterangan').value = data.pengeluaran.keterangan;
                        
                        // Format jumlah dengan pemisah ribuan
                        const jumlah = data.pengeluaran.jumlah;
                        if (jumlah) {
                            document.getElementById('edit_jumlah').value = 
                                parseInt(jumlah).toLocaleString('id-ID');
                        }
                        
                        // Set form action
                        document.getElementById('editPengeluaranForm').action = `/laporan/pengeluaran/update/${id}`;
                        
                        // Show current bukti if exists
                        const buktiContainer = document.getElementById('current_bukti');
                        if (data.pengeluaran.bukti) {
                            buktiContainer.innerHTML = `
                                <div class="alert alert-info">
                                    <i class="fas fa-file me-2"></i> Bukti saat ini: 
                                    <a href="/storage/${data.pengeluaran.bukti}" target="_blank" class="text-decoration-underline">
                                        Lihat Bukti
                                    </a>
                                </div>
                            `;
                        } else {
                            buktiContainer.innerHTML = '';
                        }
                        
                        // Show modal
                        const editModal = new bootstrap.Modal(document.getElementById('editPengeluaranModal'));
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

        // Reset form tambah pengeluaran saat modal ditutup
        document.getElementById('tambahPengeluaranModal')?.addEventListener('hidden.bs.modal', function () {
            const form = document.getElementById('tambahPengeluaranForm');
            if (form) {
                form.reset();
                document.getElementById('tanggal').value = new Date().toISOString().split('T')[0];
            }
        });

        // Reset form edit pengeluaran saat modal ditutup
        document.getElementById('editPengeluaranModal')?.addEventListener('hidden.bs.modal', function () {
            document.getElementById('current_bukti').innerHTML = '';
        });
    </script>
@endsection