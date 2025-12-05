<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pemasukan - Sahabat Senja</title>
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
            --success-color: #28a745;
            --warning-color: #FFB74D;
            --info-color: #4DB6AC;
            --card-shadow: 0 4px 6px rgba(139, 115, 85, 0.1);
            --hover-shadow: 0 8px 15px rgba(139, 115, 85, 0.15);
        }

        body {
            background: linear-gradient(135deg, var(--light-bg) 0%, #F5E8D0 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-dark);
            min-height: 100vh;
            transition: all 0.3s;
            display: flex;
            flex-direction: column;
        }

        /* Sidebar */
        .sidebar {
            background: linear-gradient(180deg, var(--primary-color) 0%, var(--dark-brown) 100%);
            color: white;
            width: 280px;
            min-height: 100vh;
            position: fixed;
            box-shadow: 2px 0 10px rgba(93, 64, 55, 0.2);
            z-index: 1000;
            transition: all 0.3s;
            left: 0;
        }

        .sidebar.collapsed {
            width: 80px;
        }

        .sidebar.collapsed .sidebar-brand h1 span,
        .sidebar.collapsed .nav-link span,
        .sidebar.collapsed .nav-item .dropdown-toggle::after {
            display: none;
        }

        .sidebar.collapsed .sidebar-brand i,
        .sidebar.collapsed .nav-link i {
            margin-right: 0;
        }

        .sidebar-brand {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
            position: relative;
        }

        .sidebar-brand h1 {
            font-weight: 700;
            font-size: 1.5rem;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }

        .sidebar-brand i {
            margin-right: 10px;
            font-size: 1.8rem;
            transition: all 0.3s;
        }

        .toggle-btn {
            position: absolute;
            right: -12px;
            top: 50%;
            transform: translateY(-50%);
            background: var(--primary-color);
            border: 2px solid white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: white;
            font-size: 0.8rem;
            transition: all 0.3s;
        }

        .toggle-btn:hover {
            background: var(--dark-brown);
        }

        .sidebar.collapsed .toggle-btn {
            transform: translateY(-50%) rotate(180deg);
        }

        .sidebar-nav {
            padding: 1rem 0;
        }

        .nav-item {
            margin-bottom: 0;
            position: relative;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.85);
            padding: 0.8rem 1.5rem;
            display: flex;
            align-items: center;
            transition: all 0.3s;
            border-left: 4px solid transparent;
            white-space: nowrap;
            overflow: hidden;
            text-decoration: none;
        }

        .nav-link:hover, .nav-link.active {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            border-left-color: var(--accent-color);
        }

        .nav-link i {
            width: 24px;
            margin-right: 12px;
            font-size: 1.1rem;
            transition: all 0.3s;
            flex-shrink: 0;
        }

        /* Main Content */
        .main-content {
            margin-left: 280px;
            padding: 0;
            min-height: 100vh;
            transition: all 0.3s;
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .main-content.expanded {
            margin-left: 80px;
        }

        /* Header */
        .top-header {
            background-color: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            padding: 1rem 2rem;
            position: sticky;
            top: 0;
            z-index: 100;
            flex-shrink: 0;
        }

        .header-title {
            font-weight: 600;
            color: var(--dark-brown);
            margin: 0;
            display: flex;
            align-items: center;
        }

        .header-title i {
            margin-right: 10px;
            color: var(--primary-color);
        }

        .user-info {
            display: flex;
            align-items: center;
            margin-right: 1.5rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            margin-right: 10px;
        }

        .logout-btn {
            background-color: var(--primary-color);
            border: none;
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .logout-btn:hover {
            background-color: var(--dark-brown);
            transform: translateY(-2px);
        }

        /* Content Container */
        .content-container {
            padding: 2rem;
            flex: 1;
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            transition: transform 0.3s, box-shadow 0.3s;
            margin-bottom: 1.5rem;
            background-color: white;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: var(--hover-shadow);
        }

        .card-header {
            border-radius: 12px 12px 0 0 !important;
            font-weight: 600;
            padding: 1rem 1.5rem;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
        }

        /* Form Elements */
        .form-control, .btn {
            border-radius: 8px;
        }

        .form-control {
            border: 1px solid var(--accent-color);
            padding: 0.75rem;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(139, 115, 85, 0.25);
        }

        .btn {
            font-weight: 500;
            padding: 0.5rem 1.2rem;
            transition: all 0.3s;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--dark-brown);
            border-color: var(--dark-brown);
            transform: translateY(-2px);
        }

        .btn-success {
            background-color: var(--success-color);
            border-color: var(--success-color);
        }

        .btn-success:hover {
            background-color: #218838;
            border-color: #218838;
            transform: translateY(-2px);
        }

        .btn-warning {
            background-color: var(--warning-color);
            border-color: var(--warning-color);
            color: var(--text-dark);
        }

        .btn-outline-secondary {
            color: var(--text-light);
            border-color: var(--accent-color);
        }

        .btn-outline-secondary:hover {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
            color: var(--text-dark);
        }

        /* Summary Cards */
        .summary-card {
            border-radius: 12px;
            padding: 1.5rem;
            color: white;
            text-align: center;
            transition: transform 0.3s;
        }

        .summary-card:hover {
            transform: translateY(-5px);
        }

        .summary-card.income {
            background: linear-gradient(135deg, #28a745, #20c997);
        }

        .summary-card.expense {
            background: linear-gradient(135deg, #dc3545, #fd7e14);
        }

        .summary-card.net {
            background: linear-gradient(135deg, #007bff, #6610f2);
        }

        .summary-card i {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .summary-card h3 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .summary-card p {
            opacity: 0.9;
            margin: 0;
        }

        /* Table */
        .table {
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid var(--accent-color);
            margin-bottom: 0;
        }

        .table thead {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
        }

        .table th {
            border: none;
            font-weight: 600;
            padding: 1rem 0.75rem;
            white-space: nowrap;
        }

        .table td {
            padding: 0.75rem;
            vertical-align: middle;
            border-color: var(--accent-color);
        }

        .table tbody tr {
            transition: background-color 0.2s;
        }

        .table tbody tr:hover {
            background-color: rgba(139, 115, 85, 0.05);
        }

        /* Badges */
        .badge {
            font-size: 0.75rem;
            padding: 0.4em 0.8em;
            border-radius: 20px;
        }

        .badge.bg-success {
            background-color: var(--success-color) !important;
        }

        .badge.bg-primary {
            background-color: var(--primary-color) !important;
        }

        /* Filter Container */
        .filter-container {
            background-color: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            margin-bottom: 1.5rem;
        }

        /* Chart Container */
        .chart-container {
            background-color: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            margin-bottom: 1.5rem;
        }

        /* Modal */
        .modal-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: 12px 12px 0 0;
        }

        /* Footer */
        .footer {
            background-color: var(--dark-brown);
            color: white;
            padding: 1.5rem 0;
            margin-top: auto;
            flex-shrink: 0;
        }

        /* Mobile Menu Button */
        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--primary-color);
            margin-right: 1rem;
        }

        /* Currency Input */
        .currency-input-group {
            position: relative;
        }
        
        .currency-input-group::before {
            content: "Rp";
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);
            font-weight: 500;
            z-index: 10;
        }
        
        .currency-input-group input {
            padding-left: 45px !important;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .sidebar {
                width: 80px;
            }

            .sidebar .sidebar-brand h1 span,
            .sidebar .nav-link span {
                display: none;
            }

            .sidebar .sidebar-brand i,
            .sidebar .nav-link i {
                margin-right: 0;
            }

            .main-content {
                margin-left: 80px;
            }

            .toggle-btn {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 0;
                transform: translateX(-100%);
            }

            .sidebar.show {
                width: 280px;
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .mobile-menu-btn {
                display: block !important;
            }

            .user-info {
                display: none;
            }

            .action-buttons {
                flex-direction: column;
            }

            .summary-card {
                margin-bottom: 1rem;
            }
        }

        /* Select2 Custom */
        .select2-container--default .select2-selection--single {
            border: 1px solid var(--accent-color) !important;
            border-radius: 8px !important;
            height: calc(1.5em + 0.75rem + 2px) !important;
            padding: 0.375rem 0.75rem !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: calc(1.5em + 0.75rem + 2px) !important;
        }

        .select2-container--default.select2-container--focus .select2-selection--single,
        .select2-container--default.select2-container--open .select2-selection--single {
            border-color: var(--primary-color) !important;
            box-shadow: 0 0 0 0.25rem rgba(139, 115, 85, 0.25) !important;
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
                <a href="{{ route('laporan.pemasukan') }}" class="nav-link active">
                    <i class="fas fa-money-bill-wave"></i>
                    <span>Laporan Pemasukan</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('laporan.pengeluaran') }}" class="nav-link">
                    <i class="fas fa-receipt"></i>
                    <span>Laporan Pengeluaran</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('admin.grafik.index') }}" class="nav-link">
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
                    <i class="fas fa-money-bill-wave"></i>Laporan Pemasukan
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

        <!-- Footer -->
        <footer class="footer">
            <div class="container text-center">
                <p class="mb-0">&copy; {{ date('Y') }} Sahabat Senja. Sistem Informasi Layanan Panti Jompo Berbasis Website & Mobile.</p>
            </div>
        </footer>
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
</body>
</html>