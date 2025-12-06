<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Lansia - Sahabat Senja</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            background-color: #689F38;
            border-color: #689F38;
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
        .badge.bg-primary {
            background-color: var(--primary-color) !important;
        }

        .badge.bg-light {
            background-color: var(--accent-color) !important;
            color: var(--text-dark);
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
        }

        /* Search Container */
        .search-container {
            background-color: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            margin-bottom: 1.5rem;
        }

        /* Pagination */
        .pagination {
            justify-content: center;
            margin-top: 1.5rem;
            margin-bottom: 0;
        }

        .page-link {
            color: var(--primary-color);
            border-color: var(--accent-color);
        }

        .page-link:hover {
            color: var(--dark-brown);
            background-color: var(--accent-color);
            border-color: var(--accent-color);
        }

        .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: var(--text-light);
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: var(--accent-color);
        }

        /* Alert */
        .alert-success {
            background-color: rgba(124, 179, 66, 0.1);
            border-color: var(--success-color);
            color: #2E7D32;
            border-radius: 8px;
        }

        .alert-danger {
            background-color: rgba(211, 47, 47, 0.1);
            border-color: #d32f2f;
            color: #c62828;
            border-radius: 8px;
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
        }
        
        /* Section Header */
        .section-header {
            background-color: var(--light-bg);
            border-left: 4px solid var(--primary-color);
            padding: 0.75rem 1rem;
            margin: 1.5rem 0 1rem;
            border-radius: 0 8px 8px 0;
        }
        
        .section-header h5 {
            margin: 0;
            color: var(--dark-brown);
            font-weight: 600;
            display: flex;
            align-items: center;
        }
        
        .section-header i {
            margin-right: 8px;
            color: var(--primary-color);
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
                <a href="{{ route('admin.DataPerawat.index') }}" class="nav-link">
                    <i class="fas fa-user-md"></i>
                    <span>Data Perawat</span>
                </a>
            </div>
            <div class="nav-item" style="margin-bottom: 0;">
                <a href="#" class="nav-link">
                    <i class="fas fa-money-bill-wave"></i>
                <span>Laporan Pemasukkan</span>
                </a>
            </div>
            <div class="nav-item" style="margin-bottom: 0;">
                <a href="#" class="nav-link">
                    <i class="fas fa-receipt me-2"></i>
                    <span>Laporan Pengeluaran</span>
                </a>
            </div>
            <div class="nav-item" style="margin-bottom: 0;">
                <a href="#" class="nav-link">
                    <i class="fas fa-chart-bar"></i>
                    <span>Grafik Keseluruhan</span>
                </a>
            </div>
            {{-- <div class="nav-item" style="margin-bottom: 0;">
                <a href="#" class="nav-link">
                    <i class="fas fa-cog"></i>
                    <span>Pengaturan</span>
                </a>
            </div> --}}
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
                    <i class="fas fa-edit"></i>Edit Data Lansia
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

        <!-- Content -->
        <div class="content-container">
            {{-- Alert error --}}
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Terjadi kesalahan:</strong>
                    <ul class="mb-0 mt-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Form Edit --}}
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <i class="fas fa-edit me-2"></i>
                    Edit Data Lansia
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.datalansia.update', $datalansia->id) }}" method="POST">
                        @csrf

                        <!-- Data Lansia Section -->
                        <div class="section-header">
                            <h5><i class="fas fa-user-friends"></i> Data Lansia</h5>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Nama Lansia</label>
                                <input type="text" name="nama_lansia" class="form-control" value="{{ old('nama_lansia', $datalansia->nama_lansia) }}" required placeholder="Masukkan nama lansia">
                                @error('nama_lansia')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Umur Lansia</label>
                                <input type="number" name="umur_lansia" class="form-control"
                                value="{{ old('umur_lansia', $datalansia->umur_lansia) }}"
                                min="40" max="160" required
                                placeholder="Umur lansia">
                                @error('umur_lansia')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tempat Lahir Lansia</label>
                                <input type="text" name="tempat_lahir_lansia" class="form-control" value="{{ old('tempat_lahir_lansia', $datalansia->tempat_lahir_lansia) }}" placeholder="Kota/Kabupaten">
                                @error('tempat_lahir_lansia')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tanggal Lahir Lansia</label>
                                <input type="date" name="tanggal_lahir_lansia" class="form-control" value="{{ old('tanggal_lahir_lansia', $datalansia->tanggal_lahir_lansia) }}">
                                @error('tanggal_lahir_lansia')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label class="form-label">Jenis Kelamin Lansia</label>
                                <select name="jenis_kelamin_lansia" class="form-control" required>
                                    <option value="">-- Pilih Jenis Kelamin --</option>
                                    <option value="Laki-laki" {{ old('jenis_kelamin_lansia', $datalansia->jenis_kelamin_lansia) == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="Perempuan" {{ old('jenis_kelamin_lansia', $datalansia->jenis_kelamin_lansia) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                @error('jenis_kelamin_lansia')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Gol Darah Lansia</label>
                                <select name="gol_darah_lansia" class="form-control">
                                    <option value="">-- Pilih Golongan Darah --</option>
                                    <option value="A" {{ old('gol_darah_lansia', $datalansia->gol_darah_lansia) == 'A' ? 'selected' : '' }}>A</option>
                                    <option value="B" {{ old('gol_darah_lansia', $datalansia->gol_darah_lansia) == 'B' ? 'selected' : '' }}>B</option>
                                    <option value="AB" {{ old('gol_darah_lansia', $datalansia->gol_darah_lansia) == 'AB' ? 'selected' : '' }}>AB</option>
                                    <option value="O" {{ old('gol_darah_lansia', $datalansia->gol_darah_lansia) == 'O' ? 'selected' : '' }}>O</option>
                                </select>
                                @error('gol_darah_lansia')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Riwayat Penyakit Lansia</label>
                                <input type="text" name="riwayat_penyakit_lansia" class="form-control" value="{{ old('riwayat_penyakit_lansia', $datalansia->riwayat_penyakit_lansia) }}" placeholder="Riwayat penyakit yang diderita">
                                @error('riwayat_penyakit_lansia')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Alergi Lansia</label>
                                <input type="text" name="alergi_lansia" class="form-control" value="{{ old('alergi_lansia', $datalansia->alergi_lansia) }}" placeholder="Alergi obat/makanan (jika ada)">
                                @error('alergi_lansia')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Obat Rutin Lansia</label>
                                <input type="text" name="obat_rutin_lansia" class="form-control" value="{{ old('obat_rutin_lansia', $datalansia->obat_rutin_lansia) }}" placeholder="Obat rutin yang dikonsumsi">
                                @error('obat_rutin_lansia')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Data Anak Section -->
                        <div class="section-header">
                            <h5><i class="fas fa-user"></i> Data Anak</h5>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Nama Anak</label>
                                <input type="text" name="nama_anak" class="form-control" value="{{ old('nama_anak', $datalansia->nama_anak) }}" placeholder="Masukkan nama anak">
                                @error('nama_anak')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">No HP Anak</label>
                                <input type="text" name="no_hp_anak" class="form-control"
                                value="{{ old('no_hp_anak', $datalansia->no_hp_anak) }}"
                                maxlength="15"
                                placeholder="No HP anak">
                                @error('no_hp_anak')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Email Anak</label>
                                <input type="email" name="email_anak" class="form-control" value="{{ old('email_anak', $datalansia->email_anak) }}" placeholder="Email anak">
                                @error('email_anak')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Alamat Section -->
                        <div class="section-header">
                            <h5><i class="fas fa-home"></i> Alamat Lengkap</h5>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label">Alamat Lengkap</label>
                                <textarea name="alamat_lengkap" class="form-control" rows="3" placeholder="Masukkan alamat lengkap">{{ old('alamat_lengkap', $datalansia->alamat_lengkap) }}</textarea>
                                @error('alamat_lengkap')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-sync-alt me-1"></i>Update Data
                            </button>
                            <a href="{{ route('admin.datalansia.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="footer">
            <div class="container text-center">
                <p class="mb-0">&copy; 2023 Sahabat Senja. Sistem Informasi Layanan Panti Jompo Berbasis Website & Mobile.</p>
            </div>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
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
    </script>
</body>
</html>