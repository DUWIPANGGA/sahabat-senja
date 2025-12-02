<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pengeluaran - Sahabat Senja</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        :root {
            --primary-color: #8B7355;
            --secondary-color: #A67B5B;
            --accent-color: #D7CCC8;
            --dark-brown: #5D4037;
            --light-bg: #FAF3E0;
            --text-dark: #4E342E;
            --text-light: #8D6E63;
            --danger-color: #dc3545;
            --warning-color: #FFB74D;
            --info-color: #4DB6AC;
            --card-shadow: 0 4px 6px rgba(139, 115, 85, 0.1);
            --hover-shadow: 0 8px 15px rgba(139, 115, 85, 0.15);
        }

        /* Semua style yang sama dengan pemasukan.blade.php */
        /* ... (gunakan semua style dari pemasukan.blade.php) ... */

        .summary-card.expense {
            background: linear-gradient(135deg, var(--danger-color), #fd7e14);
        }

        .summary-card.net {
            background: linear-gradient(135deg, #007bff, #6610f2);
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <h1>
                <i class="fas fa-heartbeat"></i>
                <span>Sahabat Senja</span>
            </h1>
            <div class="toggle-btn" id="toggleSidebar">
                <i class="fas fa-chevron-left"></i>
            </div>
        </div>
        <div class="sidebar-nav">
            <div class="nav-item">
                <a href="{{ route('admin.dashboard') }}" class="nav-link">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('admin.datalansia.index') }}" class="nav-link">
                    <i class="fas fa-user-friends"></i>
                    <span>Data Lansia</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('admin.dataperawat.index') }}" class="nav-link">
                    <i class="fas fa-user-md"></i>
                    <span>Data Perawat</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('laporan.pemasukan') }}" class="nav-link">
                    <i class="fas fa-money-bill-wave"></i>
                    <span>Laporan Pemasukan</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('laporan.pengeluaran') }}" class="nav-link active">
                    <i class="fas fa-receipt"></i>
                    <span>Laporan Pengeluaran</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="#" class="nav-link">
                    <i class="fas fa-chart-bar"></i>
                    <span>Grafik Keseluruhan</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Header -->
        <div class="top-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <button class="mobile-menu-btn" id="mobileMenuBtn">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="header-title">
                    <i class="fas fa-receipt"></i>Laporan Pengeluaran
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

            {{-- Summary Cards --}}
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="summary-card expense">
                        <i class="fas fa-receipt"></i>
                        <h3>Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</h3>
                        <p>Total Pengeluaran</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="summary-card income">
                        <i class="fas fa-money-bill-wave"></i>
                        <h3>{{ $pengeluaran->total() }}</h3>
                        <p>Jumlah Transaksi</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="summary-card net">
                        <i class="fas fa-chart-line"></i>
                        <h3>Rp {{ number_format(($pengeluaran->average('jumlah') ?? 0), 0, ',', '.') }}</h3>
                        <p>Rata-rata per Transaksi</p>
                    </div>
                </div>
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
                        <select class="form-select" id="keterangan" name="keterangan">
                            <option value="">Semua Kategori</option>
                            @foreach($pengeluaran->pluck('keterangan')->unique() as $keterangan)
                                <option value="{{ $keterangan }}" {{ request('keterangan') == $keterangan ? 'selected' : '' }}>{{ $keterangan }}</option>
                            @endforeach
                        </select>
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
                <h5 class="mb-3"><i class="fas fa-chart-line me-2"></i>Grafik Pengeluaran per Bulan</h5>
                <canvas id="pengeluaranChart" height="100"></canvas>
            </div>
            @endif

            {{-- Action Buttons --}}
            <div class="d-flex justify-content-between mb-3">
                <div>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#tambahPengeluaranModal">
                        <i class="fas fa-plus me-1"></i>Tambah Pengeluaran
                    </button>
                    <button class="btn btn-outline-primary ms-2">
                        <i class="fas fa-file-pdf me-1"></i>Export PDF
                    </button>
                    <button class="btn btn-outline-success ms-2">
                        <i class="fas fa-file-excel me-1"></i>Export Excel
                    </button>
                </div>
                <div>
                    <span class="badge bg-primary">{{ $pengeluaran->total() }} Transaksi</span>
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
                                                <i class="fas fa-calendar me-1 text-primary"></i>
                                                {{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-danger">{{ $item->keterangan }}</span>
                                        </td>
                                        <td class="fw-bold text-danger">
                                            Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                                        </td>
                                        <td>
                                            @if($item->bukti)
                                                <a href="{{ asset('storage/' . $item->bukti) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-file-invoice"></i> Lihat
                                                </a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($item->user)
                                                <span class="badge bg-light text-dark">{{ $item->user->name }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="btn btn-warning btn-sm" title="Edit" data-bs-toggle="modal" data-bs-target="#editPengeluaranModal{{ $item->id }}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form action="{{ route('laporan.pengeluaran.destroy', $item->id) }}" method="POST" class="d-inline">
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
                                                <h5 class="text-muted">Tidak ada data pengeluaran</h5>
                                                <p class="text-muted">Silakan tambah data pengeluaran terlebih dahulu</p>
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
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
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
                <p class="mb-0">&copy; 2023 Sahabat Senja. Sistem Informasi Layanan Panti Jompo Berbasis Website & Mobile.</p>
            </div>
        </footer>
    </div>

    <!-- Modal Tambah Pengeluaran -->
    <div class="modal fade" id="tambahPengeluaranModal" tabindex="-1" aria-labelledby="tambahPengeluaranModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahPengeluaranModalLabel">
                        <i class="fas fa-plus me-2"></i>Tambah Pengeluaran Baru
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('laporan.pengeluaran.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="tanggal" class="form-label">Tanggal</label>
                            <input type="date" class="form-control" id="tanggal" name="tanggal" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan Pengeluaran</label>
                            <select class="form-control" id="keterangan" name="keterangan" required>
                                <option value="">Pilih Kategori</option>
                                <option value="Gaji Perawat">Gaji Perawat</option>
                                <option value="Obat-obatan">Obat-obatan</option>
                                <option value="Makanan">Makanan</option>
                                <option value="Listrik">Listrik</option>
                                <option value="Air">Air</option>
                                <option value="Pemeliharaan">Pemeliharaan</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="jumlah" class="form-label">Jumlah (Rp)</label>
                            <input type="number" class="form-control" id="jumlah" name="jumlah" min="0" step="1000" required>
                        </div>
                        <div class="mb-3">
                            <label for="bukti" class="form-label">Bukti Pengeluaran (Opsional)</label>
                            <input type="file" class="form-control" id="bukti" name="bukti" accept="image/*,.pdf">
                            <small class="text-muted">Format: JPG, PNG, PDF (Max: 2MB)</small>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            $('#keterangan').select2({
                placeholder: 'Pilih kategori pengeluaran',
                allowClear: true
            });
        });

        // Chart.js
        @if($chartData->count() > 0)
        const ctx = document.getElementById('pengeluaranChart').getContext('2d');
        const labels = {!! $chartData->map(function($item) {
            return \Carbon\Carbon::createFromDate($item->tahun, $item->bulan, 1)->format('M Y');
        }) !!};
        const data = {!! $chartData->pluck('total') !!};

        const pengeluaranChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Pengeluaran',
                    data: data,
                    borderColor: '#dc3545',
                    backgroundColor: 'rgba(220, 53, 69, 0.1)',
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
    </script>
</body>
</html>