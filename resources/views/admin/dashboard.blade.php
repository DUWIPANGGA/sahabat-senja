<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Sahabat Senja</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #8B7355; /* Coklat susu utama */
            --secondary-color: #A67B5B; /* Coklat susu lebih terang */
            --accent-color: #D7CCC8; /* Coklat susu sangat terang */
            --dark-brown: #5D4037; /* Coklat tua untuk kontras */
            --light-bg: #FAF3E0; /* Cream sangat terang */
            --text-dark: #4E342E; /* Coklat tua untuk teks */
            --text-light: #8D6E63; /* Coklat medium untuk teks sekunder */
            --success-color: #7CB342; /* Hijau yang cocok dengan tema */
            --warning-color: #FFB74D; /* Oranye yang cocok dengan tema */
            --info-color: #4DB6AC; /* Biru kehijauan yang cocok */
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
        .sidebar.collapsed .nav-link span,
        .sidebar.collapsed .nav-item .dropdown-toggle::after {
            display: none;
        }
        
        .sidebar.collapsed .sidebar-brand i,
        .sidebar.collapsed .nav-link i {
            margin-right: 0;
        }
        
        .sidebar.collapsed .dropdown-menu {
            display: none !important;
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
        
        .dropdown-menu {
            background-color: var(--dark-brown);
            border: none;
            border-radius: 0 0 8px 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-top: 0;
            padding: 0.5rem 0;
        }
        
        .dropdown-item {
            color: rgba(255, 255, 255, 0.85);
            padding: 0.6rem 1.5rem;
            transition: all 0.3s;
        }
        
        .dropdown-item:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
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
        
        .card-icon.primary {
            background-color: rgba(139, 115, 85, 0.1);
            color: var(--primary-color);
        }
        
        .card-icon.success {
            background-color: rgba(124, 179, 66, 0.1);
            color: var(--success-color);
        }
        
        .card-icon.warning {
            background-color: rgba(255, 183, 77, 0.1);
            color: var(--warning-color);
        }
        
        .card-icon.info {
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
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }
        
        .card-change {
            font-size: 0.85rem;
            display: flex;
            align-items: center;
        }
        
        .card-change.positive {
            color: var(--success-color);
        }
        
        .card-change.negative {
            color: #e53935;
        }
        
        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        /* Recent Activity */
        .activity-card {
            background-color: white;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .card-header {
            border-bottom: 1px solid var(--accent-color);
            padding-bottom: 1rem;
            margin-bottom: 1rem;
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
        
        .activity-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .activity-item {
            padding: 1rem 0;
            border-bottom: 1px solid var(--accent-color);
            display: flex;
            align-items: center;
        }
        
        .activity-item:last-child {
            border-bottom: none;
        }
        
        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            background-color: rgba(139, 115, 85, 0.1);
            color: var(--primary-color);
        }
        
        .activity-content {
            flex: 1;
        }
        
        .activity-title {
            font-weight: 500;
            margin-bottom: 0.25rem;
        }
        
        .activity-time {
            font-size: 0.85rem;
            color: var(--text-light);
        }
        
        /* Charts Section */
        .chart-container {
            background-color: white;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .chart-header {
            border-bottom: 1px solid var(--accent-color);
            padding-bottom: 1rem;
            margin-bottom: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .chart-header h3 {
            font-weight: 600;
            color: var(--text-dark);
            margin: 0;
            display: flex;
            align-items: center;
        }
        
        .chart-header h3 i {
            margin-right: 10px;
            color: var(--primary-color);
        }
        
        .chart-wrapper {
            position: relative;
            height: 400px;
            width: 100%;
        }
        
        /* Chart Type Toggle */
        .chart-type-toggle {
            display: flex;
            gap: 0.5rem;
        }
        
        .chart-type-btn {
            background-color: transparent;
            border: 1px solid var(--primary-color);
            color: var(--primary-color);
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s;
            cursor: pointer;
        }
        
        .chart-type-btn:hover {
            background-color: rgba(139, 115, 85, 0.1);
        }
        
        .chart-type-btn.active {
            background-color: var(--primary-color);
            color: white;
        }
        
        /* Filter Card */
        .filter-card {
            background-color: white;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            padding: 1.5rem;
            margin-bottom: 2rem;
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
        
        /* Responsive */
        @media (max-width: 992px) {
            .sidebar {
                width: 80px;
            }
            
            .sidebar .sidebar-brand h1 span,
            .sidebar .nav-link span,
            .sidebar .nav-item .dropdown-toggle::after {
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
            
            .chart-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            
            .chart-type-toggle {
                align-self: flex-end;
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
                <a href="{{ route('admin.dashboard') }}" class="nav-link active">
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
                <a href="{{ route('admin.DataPerawat.index') }}" class="nav-link">
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
                <a href="{{ route('laporan.pengeluaran') }}" class="nav-link">
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
                    <i class="fas fa-tachometer-alt"></i>Dashboard Admin
                </h1>
            </div>
            
            <div class="d-flex align-items-center">
                <div class="user-info">
                    <div class="user-avatar">N</div>
                    <div>
                        <div class="fw-bold">Notifikasi</div>
                        <small class="text-muted">Profil</small>
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

        <div class="container-fluid p-4">
            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="dashboard-card">
                    <div class="card-icon primary">
                        <i class="fas fa-user-md"></i>
                    </div>
                    <div class="card-title">Total Perawat</div>
                    <div class="card-value">{{ $stats['total_perawat'] ?? 0 }}</div>
                    <div class="card-change positive">
                        <i class="fas fa-arrow-up me-1"></i> 12% dari bulan lalu
                    </div>
                </div>
                
                <div class="dashboard-card">
                    <div class="card-icon success">
                        <i class="fas fa-user-friends"></i>
                    </div>
                    <div class="card-title">Total Lansia</div>
                    <div class="card-value">{{ $stats['total_lansia'] ?? 0 }}</div>
                    <div class="card-change positive">
                        <i class="fas fa-arrow-up me-1"></i> 8% dari bulan lalu
                    </div>
                </div>
                
                <div class="dashboard-card">
                    <div class="card-icon warning">
                        <i class="fas fa-hand-holding-heart"></i>
                    </div>
                    <div class="card-title">Donasi Untuk Lansia</div>
                    <div class="card-value">{{ $stats['jadwal_hari_ini'] ?? 0 }}</div>
                    <div class="card-change negative">
                        <i class="fas fa-arrow-down me-1"></i> 2 dari kemarin
                    </div>
                </div>
                
                <div class="dashboard-card">
                    <div class="card-icon info">
                        <i class="fas fa-piggy-bank"></i>
                    </div>
                    <div class="card-title">Saldo Bersih</div>
                    <div class="card-value">Rp {{ number_format($saldoBersih ?? 0, 0, ',', '.') }}</div>
                    <div class="card-change {{ ($saldoBersih ?? 0) >= 0 ? 'positive' : 'negative' }}">
                        <i class="fas fa-arrow-{{ ($saldoBersih ?? 0) >= 0 ? 'up' : 'down' }} me-1"></i> 
                        {{ ($saldoBersih ?? 0) >= 0 ? 'Positif' : 'Negatif' }}
                    </div>
                </div>
            </div>
            
            <!-- Filter Section -->
            <div class="filter-card">
                <h3 class="mb-4"><i class="fas fa-filter"></i>Filter Grafik Keuangan</h3>
                <form id="filterForm" method="GET" action="{{ route('admin.dashboard') }}">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Pilih Tahun</label>
                            <select id="tahunFilter" name="tahun" class="form-select">
                                @php
                                    $currentYear = date('Y');
                                    $selectedYear = request('tahun', $currentYear);
                                @endphp
                                @for($year = $currentYear - 2; $year <= $currentYear + 1; $year++)
                                    <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Pilih Rentang Waktu</label>
                            <select id="rentangFilter" name="rentang" class="form-select">
                                <option value="bulan" {{ request('rentang', 'bulan') == 'bulan' ? 'selected' : '' }}>Per Bulan</option>
                                <option value="minggu" {{ request('rentang') == 'minggu' ? 'selected' : '' }}>Per Minggu</option>
                                <option value="hari" {{ request('rentang') == 'hari' ? 'selected' : '' }}>Per Hari (30 hari terakhir)</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3 d-flex align-items-end">
                            <button type="submit" id="filterBtn" class="btn w-100" style="background-color: var(--primary-color); color: white; padding: 0.6rem;">
                                <i class="fas fa-filter me-2"></i>Terapkan Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Charts and Recent Activity -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="chart-container">
                        <div class="chart-header">
                            <h3><i class="fas fa-chart-line"></i>Grafik Pemasukan vs Pengeluaran 
                                @if(request('rentang', 'bulan') == 'bulan')
                                    Tahun {{ request('tahun', date('Y')) }}
                                @elseif(request('rentang') == 'minggu')
                                    Mingguan
                                @else
                                    30 Hari Terakhir
                                @endif
                            </h3>
                            <div class="chart-type-toggle">
                                <button class="chart-type-btn active" data-chart-type="bar">
                                    <i class="fas fa-chart-bar"></i> Batang
                                </button>
                                <button class="chart-type-btn" data-chart-type="line">
                                    <i class="fas fa-chart-line"></i> Garis
                                </button>
                            </div>
                        </div>
                        <div class="chart-wrapper">
                            <canvas id="keuanganChart"></canvas>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="activity-card">
                        <div class="card-header">
                            <h3><i class="fas fa-clock"></i>Transaksi Terbaru</h3>
                            <a href="{{ route('admin.grafik.index') }}" class="btn btn-sm btn-outline-primary">Detail</a>
                        </div>
                        <ul class="activity-list">
                            @if(isset($transaksiTerbaru) && count($transaksiTerbaru) > 0)
                                @foreach($transaksiTerbaru as $transaksi)
                                    <li class="activity-item">
                                        <div class="activity-icon">
                                            @if($transaksi->type == 'pemasukan')
                                                <i class="fas fa-money-bill-wave"></i>
                                            @else
                                                <i class="fas fa-hand-holding-usd"></i>
                                            @endif
                                        </div>
                                        <div class="activity-content">
                                            <div class="activity-title">
                                                {{ $transaksi->description ?? $transaksi->sumber ?? $transaksi->keterangan }}
                                            </div>
                                            <div class="activity-time">
                                                {{ \Carbon\Carbon::parse($transaksi->tanggal)->format('d/m/Y H:i') }}
                                                <span class="badge ms-2 {{ $transaksi->type == 'pemasukan' ? 'bg-success' : 'bg-danger' }}">
                                                    Rp {{ number_format($transaksi->jumlah, 0, ',', '.') }}
                                                </span>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            @else
                                <li class="activity-item">
                                    <div class="activity-icon">
                                        <i class="fas fa-info-circle"></i>
                                    </div>
                                    <div class="activity-content">
                                        <div class="activity-title">Tidak ada transaksi terbaru</div>
                                        <div class="activity-time">Belum ada data transaksi</div>
                                    </div>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="quick-actions">
                <button class="action-btn" onclick="window.location.href='{{ route('laporan.pemasukan') }}'">
                    <div class="action-icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <span class="fw-bold">Tambah Pemasukan</span>
                    <small class="text-muted">Catat pemasukan baru</small>
                </button>
                
                <button class="action-btn" onclick="window.location.href='{{ route('laporan.pengeluaran') }}'">
                    <div class="action-icon">
                        <i class="fas fa-hand-holding-usd"></i>
                    </div>
                    <span class="fw-bold">Tambah Pengeluaran</span>
                    <small class="text-muted">Catat pengeluaran baru</small>
                </button>
                
                <button class="action-btn" onclick="window.location.href='{{ route('admin.grafik.index') }}'">
                    <div class="action-icon">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    <span class="fw-bold">Detail Grafik</span>
                    <small class="text-muted">Analisis lengkap keuangan</small>
                </button>
                
                <button class="action-btn" onclick="generateLaporan()">
                    <div class="action-icon">
                        <i class="fas fa-file-pdf"></i>
                    </div>
                    <span class="fw-bold">Export Laporan</span>
                    <small class="text-muted">Download PDF laporan</small>
                </button>
            </div>
        </div>
        
        <!-- Footer -->
        <footer class="footer">
            <div class="container text-center">
                <p class="mb-0">&copy; {{ date('Y') }} Sahabat Senja. Sistem Informasi Layanan Panti Jompo Berbasis Website & Mobile.</p>
            </div>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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

            // Chart Type Toggle
            document.querySelectorAll('.chart-type-btn').forEach(button => {
                button.addEventListener('click', function() {
                    document.querySelectorAll('.chart-type-btn').forEach(btn => {
                        btn.classList.remove('active');
                    });
                    this.classList.add('active');
                    updateChartType(this.dataset.chartType);
                });
            });

            // Get data from PHP variables passed to JavaScript
            const chartData = @json($chartData);
            
            // Initialize Chart with real data
            const ctx = document.getElementById('keuanganChart').getContext('2d');
            let keuanganChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartData.labels,
                    datasets: [
                        {
                            label: 'Pemasukan',
                            data: chartData.pemasukan,
                            backgroundColor: 'rgba(139, 115, 85, 0.8)',
                            borderColor: 'rgba(139, 115, 85, 1)',
                            borderWidth: 2,
                            borderRadius: 8,
                            borderSkipped: false,
                        },
                        {
                            label: 'Pengeluaran',
                            data: chartData.pengeluaran,
                            backgroundColor: 'rgba(255, 183, 77, 0.8)',
                            borderColor: 'rgba(255, 183, 77, 1)',
                            borderWidth: 2,
                            borderRadius: 8,
                            borderSkipped: false,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        intersect: false,
                        mode: 'index',
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                font: {
                                    size: 14,
                                    family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                                },
                                padding: 20,
                                usePointStyle: true,
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(255, 255, 255, 0.95)',
                            titleColor: 'var(--text-dark)',
                            bodyColor: 'var(--text-dark)',
                            borderColor: 'var(--accent-color)',
                            borderWidth: 1,
                            padding: 12,
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                                    return label;
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
                                callback: function(value) {
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                },
                                font: {
                                    size: 12
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 12
                                }
                            }
                        }
                    }
                }
            });

            // Function to update chart type
            function updateChartType(type) {
                keuanganChart.config.type = type;
                keuanganChart.update();
            }

            // Function to show notification
            function showNotification(message, type) {
                const notification = document.createElement('div');
                notification.className = `position-fixed top-0 end-0 m-4 p-3 rounded shadow-lg ${type === 'success' ? 'bg-success' : 'bg-danger'} text-white`;
                notification.style.zIndex = '9999';
                notification.innerHTML = `
                    <div class="d-flex align-items-center">
                        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
                        <span>${message}</span>
                    </div>
                `;
                
                document.body.appendChild(notification);
                
                setTimeout(() => {
                    notification.remove();
                }, 3000);
            }

            // Function to generate report
            function generateLaporan() {
                showNotification('Membuat laporan PDF...', 'success');
                // Simulate PDF generation
                setTimeout(() => {
                    showNotification('Laporan berhasil dibuat!', 'success');
                }, 2000);
            }
        });
    </script>
</body>
</html>