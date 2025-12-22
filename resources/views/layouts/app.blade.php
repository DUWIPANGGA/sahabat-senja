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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <!-- Additional CSS per page -->
    @stack('styles')

    <style>
        /* VARIABLES */
        :root {
            --primary-color: #8B7355;
            /* Coklat susu utama */
            --secondary-color: #A67B5B;
            /* Coklat susu lebih terang */
            --accent-color: #D7CCC8;
            /* Coklat susu sangat terang */
            --dark-brown: #5D4037;
            /* Coklat tua untuk kontras */
            --light-bg: #FAF3E0;
            /* Cream sangat terang */
            --text-dark: #4E342E;
            /* Coklat tua untuk teks */
            --text-light: #8D6E63;
            /* Coklat medium untuk teks sekunder */
            --success-color: #7CB342;
            /* Hijau yang cocok dengan tema */
            --warning-color: #FFB74D;
            /* Oranye yang cocok dengan tema */
            --info-color: #4DB6AC;
            /* Biru kehijauan yang cocok */
            --danger-color: #e53935;
            /* Merah untuk danger */
            --card-shadow: 0 4px 6px rgba(139, 115, 85, 0.1);
            --hover-shadow: 0 8px 15px rgba(139, 115, 85, 0.15);
        }

        /* RESET & STRUCTURE */
        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background: linear-gradient(135deg, var(--light-bg) 0%, #F5E8D0 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-dark);
            transition: all 0.3s ease;
        }

        /* APP WRAPPER */
        #app-wrapper {
            display: flex;
            flex: 1;
            min-height: 100vh;
        }

        /* SIDEBAR */
        .sidebar {
            background: linear-gradient(180deg, var(--primary-color) 0%, var(--dark-brown) 100%);
            color: white;
            width: 280px;
            min-height: 100vh;
            height: 100%;
            position: fixed;
            top: 0;
            left: 0;
            box-shadow: 2px 0 10px rgba(93, 64, 55, 0.2);
            z-index: 1000;
            transition: all 0.3s ease;
            overflow-y: auto;
            overflow-x: hidden;
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

        /* SIDEBAR BRAND */
        .sidebar-brand {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
            position: relative;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sidebar-brand h1 {
            font-weight: 700;
            font-size: 1.5rem;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
            white-space: nowrap;
        }

        .sidebar-brand i {
            margin-right: 10px;
            font-size: 1.8rem;
            transition: all 0.3s;
        }

        /* TOGGLE BUTTON */
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
            z-index: 1001;
        }

        .toggle-btn:hover {
            background: var(--dark-brown);
            transform: translateY(-50%) scale(1.1);
        }

        .sidebar.collapsed .toggle-btn {
            transform: translateY(-50%) rotate(180deg);
        }

        /* SIDEBAR NAV */
        .sidebar-nav {
            padding: 1rem 0;
            flex: 1;
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
            text-decoration: none;
        }

        .nav-link:hover,
        .nav-link.active {
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

        /* MAIN CONTENT AREA */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin-left: 280px;
            transition: all 0.3s ease;
        }

        .main-content.expanded {
            margin-left: 80px;
        }

        /* TOP HEADER */
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
            font-size: 1.5rem;
        }

        .header-title i {
            margin-right: 10px;
            color: var(--primary-color);
        }

        /* USER INFO */
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
            font-size: 1.2rem;
        }

        .logout-btn {
            background-color: var(--primary-color);
            border: none;
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logout-btn:hover {
            background-color: var(--dark-brown);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* MAIN CONTAINER */
        .main-container {
            flex: 1 0 auto;
            padding: 2rem;
            min-height: calc(100vh - 180px);
        }

        /* STICKY FOOTER */
        .sticky-footer {
            background: linear-gradient(90deg, var(--dark-brown) 0%, var(--primary-color) 100%);
            color: white;
            padding: 1.5rem 0;
            margin-top: auto;
            flex-shrink: 0;
            width: 100%;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 99;
        }

        .sticky-footer .container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 60px;
        }

        .sticky-footer p {
            margin: 0;
            font-size: 0.95rem;
            opacity: 0.9;
            text-align: center;
        }

        /* MOBILE MENU BUTTON */
        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--primary-color);
            margin-right: 1rem;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 6px;
            transition: all 0.3s;
        }

        .mobile-menu-btn:hover {
            background-color: rgba(139, 115, 85, 0.1);
        }

        /* TABLE STYLES */
        .table {
            width: 100%;
            margin-bottom: 1rem;
            color: var(--text-dark);
            border-collapse: separate;
            border-spacing: 0;
        }

        .table thead th {
            background-color: rgba(139, 115, 85, 0.1);
            border-bottom: 2px solid var(--accent-color);
            padding: 1rem;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        .table tbody tr {
            border-bottom: 1px solid var(--accent-color);
            transition: all 0.3s;
        }

        .table tbody tr:hover {
            background-color: rgba(139, 115, 85, 0.05);
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* BUTTON STYLES */
        .btn {
            padding: 0.5rem 1.5rem;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--dark-brown);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-success {
            background-color: var(--success-color);
            color: white;
        }

        .btn-success:hover {
            background-color: #689F38;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-warning {
            background-color: var(--warning-color);
            color: var(--text-dark);
        }

        .btn-warning:hover {
            background-color: #FFA000;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-danger {
            background-color: var(--danger-color);
            color: white;
        }

        .btn-danger:hover {
            background-color: #c62828;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-outline-secondary {
            background-color: transparent;
            border: 1px solid var(--text-light);
            color: var(--text-light);
        }

        .btn-outline-secondary:hover {
            background-color: var(--text-light);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-sm {
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
        }

        /* CARD STYLES */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
            margin-bottom: 1.5rem;
            transition: all 0.3s;
        }

        .card:hover {
            box-shadow: var(--hover-shadow);
        }

        .card-header {
            background-color: white;
            border-bottom: 1px solid var(--accent-color);
            padding: 1.5rem;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* FORM STYLES */
        .form-control,
        .form-select {
            border: 1px solid var(--accent-color);
            border-radius: 8px;
            padding: 0.75rem 1rem;
            transition: all 0.3s;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(139, 115, 85, 0.25);
        }

        /* BADGE STYLES */
        .badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 500;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .bg-primary {
            background-color: var(--primary-color) !important;
        }

        .bg-info {
            background-color: var(--info-color) !important;
        }

        .bg-warning {
            background-color: var(--warning-color) !important;
        }

        /* ALERT STYLES */
        .alert {
            border-radius: 10px;
            border: none;
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .alert-success {
            background-color: rgba(124, 179, 66, 0.1);
            color: var(--success-color);
            border-left: 4px solid var(--success-color);
        }

        .alert-dismissible .btn-close {
            padding: 1rem;
        }

        /* PAGINATION */
        .pagination {
            margin-bottom: 0;
        }

        .page-link {
            color: var(--primary-color);
            border: 1px solid var(--accent-color);
            margin: 0 3px;
            border-radius: 6px !important;
            transition: all 0.3s;
        }

        .page-link:hover {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }

        /* EMPTY STATE */
        .empty-state {
            padding: 3rem 1rem;
            text-align: center;
        }

        .empty-state i {
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        /* RESPONSIVE STYLES */
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
            .sidebar {
                width: 0;
                transform: translateX(-100%);
                position: fixed;
                height: 100vh;
                z-index: 1050;
            }

            .sidebar.show {
                width: 280px;
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .mobile-menu-btn {
                display: block;
            }

            .user-info {
                display: none;
            }

            .main-container {
                padding: 1rem;
            }

            .top-header {
                padding: 1rem;
            }

            .sticky-footer {
                padding: 1rem 0;
            }

            .sticky-footer p {
                font-size: 0.85rem;
                padding: 0 1rem;
            }

            .table-responsive {
                font-size: 0.9rem;
            }
        }

        @media (max-width: 576px) {
            .header-title {
                font-size: 1.2rem;
            }

            .logout-btn span {
                display: none;
            }

            .logout-btn i {
                margin-right: 0;
            }

            .btn {
                width: 100%;
                margin-bottom: 0.5rem;
            }

            .d-flex.flex-md-row {
                flex-direction: column !important;
            }
        }

        /* ANIMATIONS */
        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }

        .slide-in-up {
            animation: slideInUp 0.5s ease-out;
        }

    </style>

    <!-- Additional Head Content -->
    @stack('head')
</head>
<body>
    <!-- App Wrapper -->
    <div id="app-wrapper" class="fade-in">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-brand">
                <h1>
                    <i class="fas fa-heartbeat"></i>
                    <span>Sahabat Senja</span>
                </h1>
                <div class="toggle-btn" id="toggleSidebar" title="Toggle Sidebar">
                    <i class="fas fa-chevron-left"></i>
                </div>
            </div>

            <div class="sidebar-nav">
                <div class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('admin.datalansia.index') }}" class="nav-link {{ request()->routeIs('admin.datalansia.*') ? 'active' : '' }}">
                        <i class="fas fa-user-friends"></i>
                        <span>Data Lansia</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('admin.DataPerawat.index') }}" class="nav-link {{ request()->routeIs('admin.DataPerawat.*') ? 'active' : '' }}">
                        <i class="fas fa-user-md"></i>
                        <span>Data Perawat</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('laporan.pemasukan') }}" class="nav-link {{ request()->routeIs('laporan.pemasukan') ? 'active' : '' }}">
                        <i class="fas fa-chart-line"></i>
                        <span>Laporan Pemasukan</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('laporan.pengeluaran') }}" class="nav-link {{ request()->routeIs('laporan.pengeluaran') ? 'active' : '' }}">
                        <i class="fas fa-chart-bar"></i>
                        <span>Laporan Pengeluaran</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('admin.grafik.index') }}" class="nav-link {{ request()->routeIs('admin.grafik.*') ? 'active' : '' }}">
                        <i class="fas fa-chart-pie"></i>
                        <span>Grafik Keseluruhan</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('admin.kampanye.index') }}" class="nav-link {{ request()->routeIs('admin.kampanye.*') ? 'active' : '' }}">
                        <i class="fas fa-hand-holding-heart"></i>
                        <span>Kampanye Donasi</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('admin.donasi.index') }}" class="nav-link {{ request()->routeIs('admin.donasi.*') ? 'active' : '' }}">
                        <i class="fas fa-donate"></i>
                        <span>Donasi</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('admin.iuran.index') }}" class="nav-link {{ request()->routeIs('admin.iuran.*') ? 'active' : '' }}">
                        <i class="fas fa-money-bill-wave"></i>
                        <span>Iuran</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('admin.notifications.index') }}" class="nav-link {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}">
                        <i class="fas fa-bell"></i>
                        <span>Notifikasi</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="main-content" id="mainContent">
            <!-- Top Header -->
            <div class="top-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="Toggle Menu">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1 class="header-title">
                        <i class="@yield('icon', 'fas fa-home')"></i>
                        @yield('page-title', 'Dashboard')
                    </h1>
                </div>

                <div class="d-flex align-items-center gap-3">
    <a href="{{ route('profile.index') }}" 
       class="text-decoration-none text-dark user-info-link">
        <div class="user-info d-none d-md-flex align-items-center">
            <div class="user-avatar">
                {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
            </div>
            <div class="ms-2">
                <div class="fw-bold">{{ Auth::user()->name ?? 'Admin' }}</div>
                <small class="text-muted">Administrator</small>
            </div>
        </div>
    </a>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="logout-btn">
            <i class="fas fa-sign-out-alt me-2"></i>
            Logout
        </button>
    </form>
</div>

            </div>

            <!-- Main Container -->
            <div class="container-fluid main-container slide-in-up">
                <!-- Page Content -->
                @yield('content')
            </div>

            <!-- Sticky Footer -->
            <footer class="sticky-footer">
                <div class="container">
                    <p class="mb-0">&copy; {{ date('Y') }} Sahabat Senja. Sistem Informasi Layanan Panti Jompo Berbasis Website & Mobile.</p>
                </div>
            </footer>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle Sidebar
            const toggleSidebar = document.getElementById('toggleSidebar');
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');

            if (toggleSidebar) {
                toggleSidebar.addEventListener('click', function() {
                    sidebar.classList.toggle('collapsed');
                    mainContent.classList.toggle('expanded');

                    // Update icon
                    const icon = this.querySelector('i');
                    if (sidebar.classList.contains('collapsed')) {
                        icon.className = 'fas fa-chevron-right';
                    } else {
                        icon.className = 'fas fa-chevron-left';
                    }

                    // Save state to localStorage
                    localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
                });
            }

            // Mobile Menu Toggle
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            if (mobileMenuBtn) {
                mobileMenuBtn.addEventListener('click', function() {
                    sidebar.classList.toggle('show');

                    // Update icon
                    const icon = this.querySelector('i');
                    if (sidebar.classList.contains('show')) {
                        icon.className = 'fas fa-times';
                        document.body.style.overflow = 'hidden';
                    } else {
                        icon.className = 'fas fa-bars';
                        document.body.style.overflow = '';
                    }
                });
            }

            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(event) {
                if (window.innerWidth <= 768 &&
                    sidebar &&
                    mobileMenuBtn &&
                    !sidebar.contains(event.target) &&
                    !mobileMenuBtn.contains(event.target) &&
                    sidebar.classList.contains('show')) {
                    sidebar.classList.remove('show');
                    mobileMenuBtn.querySelector('i').className = 'fas fa-bars';
                    document.body.style.overflow = '';
                }
            });

            // Load sidebar state from localStorage
            function loadSidebarState() {
                const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
                if (isCollapsed && sidebar && mainContent && toggleSidebar) {
                    sidebar.classList.add('collapsed');
                    mainContent.classList.add('expanded');
                    toggleSidebar.querySelector('i').className = 'fas fa-chevron-right';
                }
            }

            // Handle window resize
            function handleResize() {
                if (window.innerWidth > 768) {
                    sidebar.classList.remove('show');
                    if (mobileMenuBtn) {
                        mobileMenuBtn.querySelector('i').className = 'fas fa-bars';
                    }
                    document.body.style.overflow = '';
                }

                // Adjust content padding for footer
                adjustContentPadding();
            }

            // Adjust content padding based on footer height
            function adjustContentPadding() {
                const footer = document.querySelector('.sticky-footer');
                const mainContainer = document.querySelector('.main-container');

                if (footer && mainContainer) {
                    const footerHeight = footer.offsetHeight;
                    mainContainer.style.paddingBottom = (footerHeight + 20) + 'px';
                }
            }

            // Initialize
            loadSidebarState();
            handleResize();
            adjustContentPadding();

            // Event listeners
            window.addEventListener('resize', handleResize);

            // Auto-hide sidebar on mobile after clicking a link
            const navLinks = document.querySelectorAll('.sidebar-nav .nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth <= 768 && sidebar.classList.contains('show')) {
                        sidebar.classList.remove('show');
                        if (mobileMenuBtn) {
                            mobileMenuBtn.querySelector('i').className = 'fas fa-bars';
                        }
                        document.body.style.overflow = '';
                    }
                });
            });

            // Add smooth scroll to top
            const scrollToTop = document.createElement('button');
            scrollToTop.innerHTML = '<i class="fas fa-chevron-up"></i>';
            scrollToTop.className = 'btn btn-primary scroll-to-top';
            scrollToTop.style.position = 'fixed';
            scrollToTop.style.bottom = '20px';
            scrollToTop.style.right = '20px';
            scrollToTop.style.zIndex = '100';
            scrollToTop.style.borderRadius = '50%';
            scrollToTop.style.width = '50px';
            scrollToTop.style.height = '50px';
            scrollToTop.style.display = 'none';
            scrollToTop.style.alignItems = 'center';
            scrollToTop.style.justifyContent = 'center';
            document.body.appendChild(scrollToTop);

            scrollToTop.addEventListener('click', function() {
                window.scrollTo({
                    top: 0
                    , behavior: 'smooth'
                });
            });

            window.addEventListener('scroll', function() {
                if (window.pageYOffset > 300) {
                    scrollToTop.style.display = 'flex';
                } else {
                    scrollToTop.style.display = 'none';
                }
            });
        });

    </script>

    <!-- Additional Scripts -->
    @stack('scripts')
</body>
</html>
