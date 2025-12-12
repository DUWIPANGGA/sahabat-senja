<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sahabat Senja') - Sistem Informasi Layanan Panti Jompo</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    
    <!-- Additional CSS per page -->
    @stack('styles')
    <script>
        // tailwind.config.js
module.exports = {
  theme: {
    extend: {
      colors: {
        // Warna solid seperti Bootstrap
        'primary': '#3490ff',
        'secondary': '#6c757d',
        'success': '#50b450',
        'warning': '#ffbe00',
        'danger': '#ff0000',
        'info': '#00afff',
        'light': '#f8f9fa',
        'dark': '#212529',
      }
    }
  }
}
    </script>
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
        /* Profile Styles */
.profile-container {
    max-width: 1200px;
    margin: 0 auto;
}

.profile-card {
    border-radius: 15px;
    overflow: hidden;
    transition: transform 0.3s, box-shadow 0.3s;
}

.profile-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
}

.profile-photo {
    width: 150px;
    height: 150px;
    object-fit: cover;
    border: 5px solid #fff;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.profile-action-btn {
    transition: all 0.3s;
}

.profile-action-btn:hover {
    transform: scale(1.05);
}

.detail-item {
    padding: 1rem;
    border-bottom: 1px solid #f0f0f0;
}

.detail-item:last-child {
    border-bottom: none;
}

.detail-label {
    font-weight: 600;
    color: #495057;
    min-width: 150px;
}

.detail-value {
    color: #212529;
}

.timeline-vertical {
    position: relative;
    padding-left: 2rem;
}

.timeline-vertical::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 2px;
    background: linear-gradient(to bottom, #4e73df, #1cc88a);
}

.timeline-item {
    position: relative;
    margin-bottom: 1.5rem;
    padding-left: 1.5rem;
}

.timeline-marker {
    position: absolute;
    left: -0.5rem;
    top: 0;
    width: 1rem;
    height: 1rem;
    border-radius: 50%;
    background: #4e73df;
    border: 3px solid #fff;
    box-shadow: 0 0 0 3px #4e73df;
}

/* Responsive Profile */
@media (max-width: 768px) {
    .profile-photo {
        width: 120px;
        height: 120px;
    }
    
    .detail-item {
        flex-direction: column;
        align-items: flex-start !important;
    }
    
    .detail-label {
        min-width: auto;
        margin-bottom: 0.5rem;
    }
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
        /* Form Styles */
.form-label {
    font-weight: 600;
    color: var(--dark-brown);
    margin-bottom: 0.5rem;
}

.form-control, .form-select {
    border: 1px solid var(--accent-color);
    border-radius: 8px;
    padding: 0.75rem 1rem;
    transition: all 0.3s;
}

.form-control:focus, .form-select:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.25rem rgba(139, 115, 85, 0.25);
}

.form-check-input:checked {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}

.input-group-text {
    background-color: var(--light-bg);
    border: 1px solid var(--accent-color);
    color: var(--text-dark);
}

/* Badge Status */
.badge {
    padding: 0.5em 1em;
    font-weight: 500;
}

/* Progress Bar Custom */
.progress {
    background-color: var(--accent-color);
    border-radius: 10px;
    overflow: hidden;
}

.progress-bar {
    border-radius: 10px;
    transition: width 0.6s ease;
}

/* Alert Styles */
.alert {
    border-radius: 10px;
    border: none;
}

.alert-success {
    background-color: rgba(124, 179, 66, 0.1);
    color: var(--success-color);
    border-left: 4px solid var(--success-color);
}

.alert-warning {
    background-color: rgba(255, 183, 77, 0.1);
    color: var(--warning-color);
    border-left: 4px solid var(--warning-color);
}

.alert-danger {
    background-color: rgba(229, 57, 53, 0.1);
    color: #e53935;
    border-left: 4px solid #e53935;
}
/* Iuran Specific Styles */
.iuran-status {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
}

.iuran-status.pending {
    background-color: rgba(255, 193, 7, 0.2);
    color: #ff9800;
    border: 1px solid #ff9800;
}

.iuran-status.lunas {
    background-color: rgba(76, 175, 80, 0.2);
    color: #4caf50;
    border: 1px solid #4caf50;
}

.iuran-status.terlambat {
    background-color: rgba(244, 67, 54, 0.2);
    color: #f44336;
    border: 1px solid #f44336;
}

.iuran-status.dibatalkan {
    background-color: rgba(158, 158, 158, 0.2);
    color: #9e9e9e;
    border: 1px solid #9e9e9e;
}

/* Table hover effect */
.table-hover tbody tr:hover {
    background-color: rgba(139, 115, 85, 0.05);
}

/* Badge styles */
.badge-auto {
    background-color: #17a2b8;
    color: white;
}

/* Progress indicator */
.iuran-progress {
    height: 8px;
    border-radius: 4px;
    overflow: hidden;
    background-color: #e9ecef;
}

.iuran-progress-bar {
    height: 100%;
    background-color: var(--primary-color);
    transition: width 0.6s ease;
}

    </style>
    
    <!-- Additional Head Content -->
    @stack('head')
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
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </div>
            <div class="nav-item" style="margin-bottom: 0;">
                <a href="{{ route('admin.datalansia.index') }}" class="nav-link {{ request()->routeIs('admin.datalansia.*') ? 'active' : '' }}">
                    <i class="fas fa-user-friends"></i>
                    <span>Data Lansia</span>
                </a>
            </div>
            <div class="nav-item" style="margin-bottom: 0;">
                <a href="{{ route('admin.DataPerawat.index') }}" class="nav-link {{ request()->routeIs('admin.dataperawat.*') ? 'active' : '' }}">
                    <i class="fas fa-user-md"></i>
                    <span>Data Perawat</span>
                </a>
            </div>

            <div class="nav-item" style="margin-bottom: 0;">
                <a href="{{ route('laporan.pemasukan') }}" class="nav-link {{ request()->routeIs('laporan.pemasukan') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i>
                    <span>Laporan Pemasukan</span>
                </a>
            </div>

            <div class="nav-item" style="margin-bottom: 0;">
                <a href="{{ route('laporan.pengeluaran') }}" class="nav-link {{ request()->routeIs('laporan.pengeluaran') ? 'active' : '' }}">
                    <i class="fas fa-chart-bar"></i>
                    <span>Laporan Pengeluaran</span>
                </a>
            </div>

            <div class="nav-item" style="margin-bottom: 0;">
                <a href="{{ route('admin.grafik.index') }}" class="nav-link {{ request()->routeIs('admin.grafik.*') ? 'active' : '' }}">
                    <i class="fas fa-chart-pie"></i>
                    <span>Grafik Keseluruhan</span>
                </a>
            </div>
<div class="nav-item" style="margin-bottom: 0;">
    <a href="{{ route('admin.kampanye.index') }}" class="nav-link {{ request()->routeIs('admin.kampanye.*') ? 'active' : '' }}">
        <i class="fas fa-hand-holding-heart"></i>
        <span>Kampanye Donasi</span>
    </a>
</div>
<div class="nav-item" style="margin-bottom: 0;">
    <a href="{{ route('admin.donasi.index') }}" class="nav-link {{ request()->routeIs('admin.donasi.*') ? 'active' : '' }}">
        <i class="fas fa-donate"></i>
        <span>Donasi</span>
    </a>
</div>
<div class="nav-item" style="margin-bottom: 0;">
    <a href="{{ route('admin.iuran.index') }}" class="nav-link {{ request()->routeIs('admin.iuran.*') ? 'active' : '' }}">
        <i class="fas fa-money-bill-wave"></i>
        <span>Iuran</span>
    </a>
</div>
<div class="nav-item" style="margin-bottom: 0;">
    <a href="{{ route('admin.notifications.index') }}" class="nav-link {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}">
        <i class="fas fa-bell"></i>
        <span>notifikasi</span>
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
                    <i class="@yield('icon', 'fas fa-home')"></i>
                    @yield('page-title', 'Dashboard')
                </h1>
            </div>
            
            <div class="d-flex align-items-center">
                <div class="user-info">
                    <div class="user-avatar">N</div>
                    <div>
                        <div class="fw-bold">{{ Auth::user()->name ?? 'Admin' }}</div>
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

        <!-- Main Content -->
        <div class="container-fluid p-4">
            <!-- Page Content -->
            @yield('content')
        </div>
        
        <!-- Footer -->
        <footer class="footer">
            <div class="container text-center">
                <p class="mb-0">&copy; {{ date('Y') }} Sahabat Senja. Sistem Informasi Layanan Panti Jompo Berbasis Website & Mobile.</p>
            </div>
        </footer>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script src="{{ asset('js/app.js') }}"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle Sidebar
            const toggleSidebar = document.getElementById('toggleSidebar');
            if (toggleSidebar) {
                toggleSidebar.addEventListener('click', function() {
                    const sidebar = document.getElementById('sidebar');
                    const mainContent = document.getElementById('mainContent');
                    
                    sidebar.classList.toggle('collapsed');
                    mainContent.classList.toggle('expanded');
                });
            }

            // Mobile Menu Toggle
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            if (mobileMenuBtn) {
                mobileMenuBtn.addEventListener('click', function() {
                    const sidebar = document.getElementById('sidebar');
                    sidebar.classList.toggle('show');
                });
            }

            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(event) {
                const sidebar = document.getElementById('sidebar');
                const mobileMenuBtn = document.getElementById('mobileMenuBtn');
                
                if (window.innerWidth <= 768 && 
                    sidebar && 
                    mobileMenuBtn &&
                    !sidebar.contains(event.target) && 
                    !mobileMenuBtn.contains(event.target) &&
                    sidebar.classList.contains('show')) {
                    sidebar.classList.remove('show');
                }
            });

            // Handle window resize
            window.addEventListener('resize', function() {
                const sidebar = document.getElementById('sidebar');
                if (sidebar && window.innerWidth > 768) {
                    sidebar.classList.remove('show');
                }
            });
        });
    </script>
    
    <!-- Additional Scripts -->
    @stack('scripts')
</body>
</html>