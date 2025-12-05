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
            --success-color: #7CB342;
            --warning-color: #FFB74D;
            --info-color: #4DB6AC;
            --danger-color: #e53935;
            --card-shadow: 0 4px 6px rgba(139, 115, 85, 0.1);
            --hover-shadow: 0 8px 15px rgba(139, 115, 85, 0.15);
        }
        
        body {
            background: linear-gradient(135deg, var(--light-bg) 0%, #F5E8D0 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-dark);
            min-height: 100vh;
            transition: all 0.3s;
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
        .sidebar.collapsed .nav-link span {
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
            margin-bottom: 0.5rem;
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
        }
        
        /* Dashboard Cards */
        .dashboard-card {
            background-color: white;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            padding: 1.5rem;
            transition: all 0.3s;
            height: 100%;
            border: none;
        }
        
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--hover-shadow);
        }
        
        .card-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-bottom: 1rem;
        }
        
        .card-icon.expense {
            background-color: rgba(229, 57, 53, 0.1);
            color: var(--danger-color);
        }
        
        .card-icon.income {
            background-color: rgba(124, 179, 66, 0.1);
            color: var(--success-color);
        }
        
        .card-icon.net {
            background-color: rgba(77, 182, 172, 0.1);
            color: var(--info-color);
        }
        
        .card-title {
            font-size: 0.9rem;
            color: var(--text-light);
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
        
        .card-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }
        
        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        /* Filter Container */
        .filter-container {
            background-color: white;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .form-label {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }
        
        /* Chart Container */
        .chart-container {
            background-color: white;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .card-header {
            border-bottom: 1px solid var(--accent-color);
            padding-bottom: 1rem;
            margin-bottom: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .card-header h3 {
            font-weight: 600;
            color: var(--text-dark);
            margin: 0;
            display: flex;
            align-items: center;
        }
        
        .card-header h3 i {
            margin-right: 10px;
            color: var(--primary-color);
        }
        
        /* Table */
        .data-card {
            background-color: white;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            padding: 0;
            overflow: hidden;
        }
        
        .table {
            margin-bottom: 0;
            color: var(--text-dark);
        }
        
        .table thead th {
            background-color: var(--light-bg);
            color: var(--text-dark);
            font-weight: 600;
            border-bottom: 2px solid var(--accent-color);
            padding: 1rem 1.5rem;
            vertical-align: middle;
        }
        
        .table tbody tr {
            transition: all 0.3s;
        }
        
        .table tbody tr:hover {
            background-color: rgba(139, 115, 85, 0.05);
        }
        
        .table tbody td {
            padding: 1rem 1.5rem;
            vertical-align: middle;
        }
        
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn-action {
            width: 35px;
            height: 35px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            transition: all 0.3s;
        }
        
        /* Quick Actions */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .action-btn {
            background-color: white;
            border: none;
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: var(--card-shadow);
            transition: all 0.3s;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        
        .action-btn:hover {
            transform: translateY(-5px);
            box-shadow: var(--hover-shadow);
        }
        
        .action-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 0.8rem;
            background-color: rgba(139, 115, 85, 0.1);
            color: var(--primary-color);
        }
        
        /* Footer */
        .footer {
            background-color: var(--dark-brown);
            color: white;
            padding: 1.5rem 0;
            margin-top: 3rem;
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem 2rem;
        }
        
        .empty-state i {
            font-size: 3rem;
            color: var(--accent-color);
            margin-bottom: 1rem;
        }
        
        /* Modal */
        .modal-header {
            background-color: var(--primary-color);
            color: white;
            border-bottom: none;
            padding: 1.5rem;
        }
        
        .modal-header .btn-close {
            filter: invert(1);
        }
        
        /* Badges */
        .badge-custom {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 500;
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
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
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
            
            .content-container {
                padding: 1rem;
            }
        }
        
        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--primary-color);
            margin-right: 1rem;
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
            <div class="nav-item" style="margin-bottom: 0;">
                <a href="{{ route('admin.dashboard') }}" class="nav-link">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </div>
            <div class="nav-item" style="margin-bottom: 0;">
                <a href="{{ route('admin.datalansia.index') }}" class="nav-link">
                    <i class="fas fa-user-friends"></i>
                    <span>Data Lansia</span>
                </a>
            </div>
            <div class="nav-item" style="margin-bottom: 0;">
                <a href="{{ route('admin.dataperawat.index') }}" class="nav-link">
                    <i class="fas fa-user-md"></i>
                    <span>Data Perawat</span>
                </a>
            </div>
            <div class="nav-item" style="margin-bottom: 0;">
                <a href="{{ route('laporan.pemasukan') }}" class="nav-link">
                    <i class="fas fa-chart-line"></i>
                    <span>Laporan Pemasukan</span>
                </a>
            </div>
            <div class="nav-item" style="margin-bottom: 0;">
                <a href="{{ route('laporan.pengeluaran') }}" class="nav-link active">
                    <i class="fas fa-chart-bar"></i>
                    <span>Laporan Pengeluaran</span>
                </a>
            </div>
            <div class="nav-item" style="margin-bottom: 0;">
                <a href="{{ route('admin.grafik.index') }}" class="nav-link">
                    <i class="fas fa-chart-pie"></i>
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
</body>
</html>