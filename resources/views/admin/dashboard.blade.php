@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')
@section('icon', 'fas fa-tachometer-alt')

@section('content')
<div class="content-container">
    <!-- Stats Cards -->
    <div class="stats-grid">
        <!-- Total Perawat -->
        <div class="dashboard-card">
            <div class="card-icon primary">
                <i class="fas fa-user-md"></i>
            </div>
            <div class="card-title">Total Perawat</div>
            <div class="card-value">{{ $stats['total_perawat'] ?? 0 }}</div>
            <div class="card-change">
                <a href="{{ route('admin.DataPerawat.index') }}" class="text-decoration-none d-flex align-items-center">
                    <span class="me-2">Lihat Detail</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>

        <!-- Total Lansia -->
        <div class="dashboard-card">
            <div class="card-icon success">
                <i class="fas fa-user-friends"></i>
            </div>
            <div class="card-title">Total Lansia</div>
            <div class="card-value">{{ $stats['total_lansia'] ?? 0 }}</div>
            <div class="card-change">
                <a href="{{ route('admin.datalansia.index') }}" class="text-decoration-none d-flex align-items-center">
                    <span class="me-2">Lihat Detail</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>

        <!-- Donasi Terkumpul -->
        <div class="dashboard-card">
            <div class="card-icon warning">
                <i class="fas fa-hand-holding-heart"></i>
            </div>
            <div class="card-title">Donasi Terkumpul</div>
            <div class="card-value">Rp {{ number_format($stats['total_donasi'] ?? 0, 0, ',', '.') }}</div>
            <div class="card-change positive">
                @php
                    $donasiChange = $stats['donasi_change'] ?? 0;
                @endphp
                <i class="fas fa-arrow-{{ $donasiChange >= 0 ? 'up' : 'down' }} me-1"></i>
                {{ abs($donasiChange) }}% dari bulan lalu
            </div>
        </div>

        <!-- Saldo Bersih -->
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

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0 fw-semibold d-flex align-items-center">
                        <i class="fas fa-bolt me-2 text-warning"></i>
                        Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="quick-actions">
                        <button class="action-btn" onclick="window.location.href='{{ route('admin.datalansia.create') }}'">
                            <div class="action-icon" style="background-color: rgba(139, 115, 85, 0.1); color: var(--primary-color);">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <span class="fw-bold">Tambah Lansia</span>
                            <small class="text-muted">Registrasi lansia baru</small>
                        </button>

                        <button class="action-btn" onclick="window.location.href='{{ route('admin.DataPerawat.create') }}'">
                            <div class="action-icon" style="background-color: rgba(124, 179, 66, 0.1); color: var(--success-color);">
                                <i class="fas fa-user-md"></i>
                            </div>
                            <span class="fw-bold">Tambah Perawat</span>
                            <small class="text-muted">Tambah staf perawat baru</small>
                        </button>

                        <button class="action-btn" onclick="window.location.href='{{ route('admin.kampanye.create') }}'">
                            <div class="action-icon" style="background-color: rgba(255, 183, 77, 0.1); color: var(--warning-color);">
                                <i class="fas fa-hand-holding-heart"></i>
                            </div>
                            <span class="fw-bold">Buat Kampanye</span>
                            <small class="text-muted">Buat kampanye donasi baru</small>
                        </button>

                        <button class="action-btn" onclick="window.location.href='{{ route('admin.iuran.create') }}'">
                            <div class="action-icon" style="background-color: rgba(77, 182, 172, 0.1); color: var(--info-color);">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                            <span class="fw-bold">Catat Iuran</span>
                            <small class="text-muted">Input pembayaran iuran</small>
                        </button>

                        <button class="action-btn" onclick="window.location.href='{{ route('laporan.pemasukan.store') }}'">
                            <div class="action-icon" style="background-color: rgba(139, 115, 85, 0.1); color: var(--primary-color);">
                                <i class="fas fa-plus-circle"></i>
                            </div>
                            <span class="fw-bold">Tambah Pemasukan</span>
                            <small class="text-muted">Catat pemasukan baru</small>
                        </button>

                        <button class="action-btn" onclick="window.location.href='{{ route('laporan.pengeluaran.store') }}'">
                            <div class="action-icon" style="background-color: rgba(124, 179, 66, 0.1); color: var(--success-color);">
                                <i class="fas fa-minus-circle"></i>
                            </div>
                            <span class="fw-bold">Tambah Pengeluaran</span>
                            <small class="text-muted">Catat pengeluaran baru</small>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0 fw-semibold d-flex align-items-center">
                        <i class="fas fa-filter me-2 text-primary"></i>
                        Filter Grafik Keuangan
                    </h5>
                </div>
                <div class="card-body">
                    <form id="filterForm" method="GET" action="{{ route('admin.dashboard') }}">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Pilih Tahun</label>
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
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Pilih Rentang</label>
                                <select id="rentangFilter" name="rentang" class="form-select">
                                    <option value="bulan" {{ request('rentang', 'bulan') == 'bulan' ? 'selected' : '' }}>Per Bulan</option>
                                    <option value="minggu" {{ request('rentang') == 'minggu' ? 'selected' : '' }}>Per Minggu</option>
                                    <option value="hari" {{ request('rentang') == 'hari' ? 'selected' : '' }}>Per Hari</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Kategori</label>
                                <select name="kategori" class="form-select">
                                    <option value="">Semua Kategori</option>
                                    <option value="donasi" {{ request('kategori') == 'donasi' ? 'selected' : '' }}>Donasi</option>
                                    <option value="iuran" {{ request('kategori') == 'iuran' ? 'selected' : '' }}>Iuran</option>
                                    <option value="operasional" {{ request('kategori') == 'operasional' ? 'selected' : '' }}>Operasional</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <div class="d-flex gap-2 w-100">
                                    <button type="submit" class="btn btn-primary flex-grow-1">
                                        <i class="fas fa-filter me-2"></i>Terapkan
                                    </button>
                                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-redo"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Statistics -->
    <div class="row mb-4">
        <!-- Main Chart -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-semibold d-flex align-items-center">
                        <i class="fas fa-chart-line me-2 text-primary"></i>
                        Grafik Pemasukan vs Pengeluaran
                        @if(request('rentang', 'bulan') == 'bulan')
                            Tahun {{ request('tahun', date('Y')) }}
                        @elseif(request('rentang') == 'minggu')
                            Mingguan
                        @else
                            30 Hari Terakhir
                        @endif
                    </h5>
                    <div class="chart-type-toggle">
                        <button class="chart-type-btn active" data-chart-type="bar">
                            <i class="fas fa-chart-bar"></i>
                        </button>
                        <button class="chart-type-btn" data-chart-type="line">
                            <i class="fas fa-chart-line"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-wrapper">
                        <canvas id="keuanganChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-semibold d-flex align-items-center">
                        <i class="fas fa-history me-2 text-primary"></i>
                        Transaksi Terbaru
                    </h5>
                    <a href="{{ route('admin.grafik.index') }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-external-link-alt me-1"></i>Detail
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="activity-list">
                        @if(isset($transaksiTerbaru) && count($transaksiTerbaru) > 0)
                            @foreach($transaksiTerbaru as $transaksi)
                                <div class="activity-item">
                                    <div class="activity-icon 
                                        @if($transaksi->type == 'pemasukan') bg-success @else bg-danger @endif">
                                        @if($transaksi->type == 'pemasukan')
                                            <i class="fas fa-arrow-down"></i>
                                        @else
                                            <i class="fas fa-arrow-up"></i>
                                        @endif
                                    </div>
                                    <div class="activity-content">
                                        <div class="activity-title text-truncate">
                                            {{ $transaksi->description ?? $transaksi->sumber ?? $transaksi->keterangan }}
                                        </div>
                                        <div class="activity-time d-flex justify-content-between align-items-center">
                                            <span>{{ \Carbon\Carbon::parse($transaksi->tanggal)->format('d/m/Y') }}</span>
                                            <span class="fw-bold 
                                                @if($transaksi->type == 'pemasukan') text-success @else text-danger @endif">
                                                Rp {{ number_format($transaksi->jumlah, 0, ',', '.') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted mb-0">Belum ada transaksi</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Stats -->
    <div class="row">
        <!-- Donation Progress -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0 fw-semibold d-flex align-items-center">
                        <i class="fas fa-hand-holding-usd me-2 text-warning"></i>
                        Progress Kampanye Donasi
                    </h5>
                </div>
                <div class="card-body">
                    @if(isset($kampanyeAktif) && count($kampanyeAktif) > 0)
                        @foreach($kampanyeAktif as $kampanye)
                            <div class="mb-4">
                                <div class="d-flex justify-content-between mb-2">
                                    <h6 class="mb-0 fw-semibold">{{ $kampanye->judul }}</h6>
                                    <span class="badge bg-primary">
                                        {{ round(($kampanye->terkumpul / $kampanye->target) * 100) }}%
                                    </span>
                                </div>
                                <div class="progress mb-2" style="height: 10px;">
                                    <div class="progress-bar bg-warning" 
                                         role="progressbar" 
                                         style="width: {{ min(100, ($kampanye->terkumpul / $kampanye->target) * 100) }}%"
                                         aria-valuenow="{{ $kampanye->terkumpul }}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="{{ $kampanye->target }}">
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between text-muted small">
                                    <span>Rp {{ number_format($kampanye->terkumpul, 0, ',', '.') }}</span>
                                    <span>Target: Rp {{ number_format($kampanye->target, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        @endforeach
                        <a href="{{ route('admin.kampanye.index') }}" class="btn btn-outline-primary w-100 mt-3">
                            <i class="fas fa-external-link-alt me-2"></i>Lihat Semua Kampanye
                        </a>
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-hand-holding-heart fa-2x text-muted mb-3"></i>
                            <p class="text-muted mb-0">Tidak ada kampanye aktif</p>
                            <a href="{{ route('admin.kampanye.create') }}" class="btn btn-primary mt-3">
                                <i class="fas fa-plus me-2"></i>Buat Kampanye
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Iuran Status -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0 fw-semibold d-flex align-items-center">
                        <i class="fas fa-money-bill-wave me-2 text-success"></i>
                        Status Iuran Bulan Ini
                    </h5>
                </div>
                <div class="card-body">
                    @if(isset($iuranStatus) && count($iuranStatus) > 0)
                        <div class="table-responsive">
                            <table class="table table-borderless mb-0">
                                <thead>
                                    <tr>
                                        <th>Status</th>
                                        <th>Jumlah</th>
                                        <th>Persentase</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($iuranStatus as $status => $data)
                                        <tr>
                                            <td>
                                                <span class="badge 
                                                    @if($status == 'lunas') bg-success 
                                                    @elseif($status == 'belum_lunas') bg-warning 
                                                    @elseif($status == 'terlambat') bg-danger 
                                                    @else bg-secondary @endif">
                                                    {{ ucfirst(str_replace('_', ' ', $status)) }}
                                                </span>
                                            </td>
                                            <td class="fw-semibold">{{ $data['count'] }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                                        <div class="progress-bar 
                                                            @if($status == 'lunas') bg-success 
                                                            @elseif($status == 'belum_lunas') bg-warning 
                                                            @elseif($status == 'terlambat') bg-danger 
                                                            @else bg-secondary @endif"
                                                            role="progressbar" 
                                                            style="width: {{ $data['percentage'] }}%"
                                                            aria-valuenow="{{ $data['percentage'] }}" 
                                                            aria-valuemin="0" 
                                                            aria-valuemax="100">
                                                        </div>
                                                    </div>
                                                    <span class="text-muted small">{{ $data['percentage'] }}%</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <a href="{{ route('admin.iuran.index') }}" class="btn btn-outline-primary w-100 mt-3">
                            <i class="fas fa-external-link-alt me-2"></i>Kelola Iuran
                        </a>
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-money-bill-wave fa-2x text-muted mb-3"></i>
                            <p class="text-muted mb-0">Belum ada data iuran bulan ini</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .dashboard-card {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: var(--card-shadow);
        transition: all 0.3s ease;
        border: 1px solid rgba(139, 115, 85, 0.1);
    }
    
    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--hover-shadow);
        border-color: var(--primary-color);
    }
    
    .card-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }
    
    .card-title {
        font-size: 0.9rem;
        color: var(--text-light);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }
    
    .card-value {
        font-size: 2rem;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 0.5rem;
        line-height: 1;
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
        color: var(--danger-color);
    }
    
    .card-change a {
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s;
    }
    
    .card-change a:hover {
        color: var(--dark-brown);
        transform: translateX(3px);
    }
    
    .quick-actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }
    
    .action-btn {
        background: white;
        border: 2px solid rgba(139, 115, 85, 0.1);
        border-radius: 12px;
        padding: 1.5rem;
        text-align: center;
        transition: all 0.3s;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 100%;
    }
    
    .action-btn:hover {
        transform: translateY(-3px);
        box-shadow: var(--hover-shadow);
        border-color: var(--primary-color);
    }
    
    .action-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        margin-bottom: 1rem;
    }
    
    .action-btn span {
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 0.25rem;
    }
    
    .action-btn small {
        color: var(--text-light);
        font-size: 0.85rem;
    }
    
    .chart-wrapper {
        position: relative;
        height: 350px;
        width: 100%;
    }
    
    .chart-type-toggle {
        display: flex;
        gap: 0.5rem;
    }
    
    .chart-type-btn {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        border: 2px solid var(--accent-color);
        background: white;
        color: var(--text-light);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .chart-type-btn:hover {
        border-color: var(--primary-color);
        color: var(--primary-color);
    }
    
    .chart-type-btn.active {
        background: var(--primary-color);
        border-color: var(--primary-color);
        color: white;
    }
    
    .activity-list {
        max-height: 400px;
        overflow-y: auto;
    }
    
    .activity-item {
        padding: 1rem;
        border-bottom: 1px solid var(--accent-color);
        display: flex;
        align-items: center;
        transition: all 0.3s;
    }
    
    .activity-item:hover {
        background-color: rgba(139, 115, 85, 0.05);
    }
    
    .activity-item:last-child {
        border-bottom: none;
    }
    
    .activity-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        color: white;
        flex-shrink: 0;
    }
    
    .activity-content {
        flex: 1;
        min-width: 0;
    }
    
    .activity-title {
        font-weight: 500;
        margin-bottom: 0.25rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .activity-time {
        font-size: 0.85rem;
        color: var(--text-light);
    }
    
    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
        
        .quick-actions {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .card-value {
            font-size: 1.75rem;
        }
        
        .chart-wrapper {
            height: 250px;
        }
    }
    
    @media (max-width: 576px) {
        .quick-actions {
            grid-template-columns: 1fr;
        }
        
        .dashboard-card {
            padding: 1.25rem;
        }
        
        .card-value {
            font-size: 1.5rem;
        }
        
        .action-btn {
            padding: 1rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ============================================
        // 1. DATA PREPARATION
        // ============================================
        
        // Get chart data from PHP or use fallback
        const rawChartData = {!! json_encode($chartData ?? null) !!};
        
        // Prepare fallback data
        const fallbackData = {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'],
            pemasukan: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            pengeluaran: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
        };
        
        // Use chart data or fallback
        const chartData = rawChartData && typeof rawChartData === 'object' ? rawChartData : fallbackData;
        
        // Validate data structure
        if (!chartData.labels || !Array.isArray(chartData.labels)) {
            chartData.labels = fallbackData.labels;
        }
        if (!chartData.pemasukan || !Array.isArray(chartData.pemasukan)) {
            chartData.pemasukan = fallbackData.pemasukan;
        }
        if (!chartData.pengeluaran || !Array.isArray(chartData.pengeluaran)) {
            chartData.pengeluaran = fallbackData.pengeluaran;
        }
        
        // ============================================
        // 2. CHART INITIALIZATION
        // ============================================
        
        const ctx = document.getElementById('keuanganChart');
        if (!ctx) {
            console.error('Chart canvas not found');
            return;
        }
        
        let keuanganChart = new Chart(ctx.getContext('2d'), {
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
                        tension: 0.4
                    },
                    {
                        label: 'Pengeluaran',
                        data: chartData.pengeluaran,
                        backgroundColor: 'rgba(255, 183, 77, 0.8)',
                        borderColor: 'rgba(255, 183, 77, 1)',
                        borderWidth: 2,
                        borderRadius: 8,
                        borderSkipped: false,
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
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
                            color: 'var(--text-dark)'
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
                                const value = context.parsed.y || 0;
                                label += 'Rp ' + value.toLocaleString('id-ID');
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
                                if (value >= 1000000) {
                                    return 'Rp ' + (value / 1000000).toFixed(1) + 'jt';
                                }
                                if (value >= 1000) {
                                    return 'Rp ' + (value / 1000).toFixed(0) + 'rb';
                                }
                                return 'Rp ' + value.toLocaleString('id-ID');
                            },
                            font: {
                                size: 12
                            },
                            color: 'var(--text-light)'
                        },
                        title: {
                            display: true,
                            text: 'Jumlah (Rupiah)',
                            color: 'var(--text-dark)',
                            font: {
                                size: 12,
                                weight: 'bold'
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
                            },
                            color: 'var(--text-light)'
                        }
                    }
                },
                animation: {
                    duration: 1000,
                    easing: 'easeOutQuart'
                }
            }
        });
        
        // ============================================
        // 3. CHART TYPE TOGGLE FUNCTIONALITY
        // ============================================
        
        const chartTypeButtons = document.querySelectorAll('.chart-type-btn');
        chartTypeButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Remove active class from all buttons
                chartTypeButtons.forEach(btn => {
                    btn.classList.remove('active');
                });
                
                // Add active class to clicked button
                this.classList.add('active');
                
                // Update chart type
                const chartType = this.getAttribute('data-chart-type');
                keuanganChart.config.type = chartType;
                
                // Adjust line tension for line charts
                if (chartType === 'line') {
                    keuanganChart.data.datasets.forEach(dataset => {
                        dataset.tension = 0.4;
                    });
                } else {
                    keuanganChart.data.datasets.forEach(dataset => {
                        dataset.tension = 0;
                    });
                }
                
                // Update chart
                keuanganChart.update();
            });
        });
        
        // ============================================
        // 4. FILTER FORM FUNCTIONALITY
        // ============================================
        
        const filterForm = document.getElementById('filterForm');
        if (filterForm) {
            const filterBtn = filterForm.querySelector('button[type="submit"]');
            
            filterForm.addEventListener('submit', function(event) {
                if (filterBtn) {
                    // Show loading state
                    const originalText = filterBtn.innerHTML;
                    filterBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memuat...';
                    filterBtn.disabled = true;
                    
                    // Re-enable button after 3 seconds (fallback)
                    setTimeout(() => {
                        filterBtn.innerHTML = originalText;
                        filterBtn.disabled = false;
                    }, 3000);
                }
            });
            
            // Real-time year filter update
            const tahunFilter = document.getElementById('tahunFilter');
            const rentangFilter = document.getElementById('rentangFilter');
            
            if (tahunFilter) {
                tahunFilter.addEventListener('change', function() {
                    updateChartTitle();
                });
            }
            
            if (rentangFilter) {
                rentangFilter.addEventListener('change', function() {
                    updateChartTitle();
                });
            }
            
            function updateChartTitle() {
                const tahun = tahunFilter ? tahunFilter.value : new Date().getFullYear();
                const rentang = rentangFilter ? rentangFilter.value : 'bulan';
                const chartTitle = document.querySelector('.card-header h5');
                
                if (chartTitle) {
                    let titleText = 'Grafik Pemasukan vs Pengeluaran ';
                    
                    if (rentang === 'bulan') {
                        titleText += `Tahun ${tahun}`;
                    } else if (rentang === 'minggu') {
                        titleText += 'Mingguan';
                    } else {
                        titleText += '30 Hari Terakhir';
                    }
                    
                    chartTitle.innerHTML = `<i class="fas fa-chart-line me-2 text-primary"></i>${titleText}`;
                }
            }
        }
        
        // ============================================
        // 5. QUICK ACTIONS FUNCTIONALITY
        // ============================================
        
        // PDF Export function
        window.generateLaporan = function() {
            const btn = event?.target?.closest('.action-btn');
            
            if (btn) {
                // Show loading state
                const originalHTML = btn.innerHTML;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Membuat...';
                btn.disabled = true;
                
                // Simulate PDF generation
                setTimeout(() => {
                    showNotification('Laporan PDF berhasil dibuat!', 'success');
                    
                    // Restore button
                    btn.innerHTML = originalHTML;
                    btn.disabled = false;
                    
                    // Here you would typically trigger PDF download
                    // window.open('/generate-pdf-report', '_blank');
                }, 1500);
            } else {
                // Fallback if button not found
                showNotification('Membuat laporan PDF...', 'info');
                setTimeout(() => {
                    showNotification('Laporan PDF berhasil dibuat!', 'success');
                }, 1500);
            }
        };
        
        // ============================================
        // 6. NOTIFICATION SYSTEM
        // ============================================
        
        function showNotification(message, type = 'info') {
            // Remove existing notifications
            document.querySelectorAll('.custom-notification').forEach(el => {
                el.remove();
            });
            
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `custom-notification position-fixed top-0 end-0 m-4 p-3 rounded shadow-lg`;
            
            // Set colors based on type
            const colors = {
                success: 'bg-success text-white',
                error: 'bg-danger text-white',
                warning: 'bg-warning text-dark',
                info: 'bg-info text-white'
            };
            
            notification.classList.add(...colors[type].split(' '));
            notification.style.zIndex = '9999';
            notification.style.maxWidth = '350px';
            notification.style.transition = 'all 0.3s ease';
            
            // Set icon based on type
            const icons = {
                success: 'check-circle',
                error: 'exclamation-circle',
                warning: 'exclamation-triangle',
                info: 'info-circle'
            };
            
            notification.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="fas fa-${icons[type]} me-2 fs-5"></i>
                    <div class="flex-grow-1">${message}</div>
                    <button type="button" class="btn-close btn-close-white ms-2" onclick="this.parentElement.parentElement.remove()"></button>
                </div>
            `;
            
            // Add to DOM
            document.body.appendChild(notification);
            
            // Animate in
            setTimeout(() => {
                notification.style.transform = 'translateX(0)';
                notification.style.opacity = '1';
            }, 10);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.style.opacity = '0';
                    notification.style.transform = 'translateX(100%)';
                    setTimeout(() => {
                        if (notification.parentNode) {
                            notification.remove();
                        }
                    }, 300);
                }
            }, 5000);
            
            // Close button functionality
            notification.querySelector('.btn-close').addEventListener('click', function() {
                notification.style.opacity = '0';
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.remove();
                    }
                }, 300);
            });
        }
        
        // ============================================
        // 7. DATA REFRESH FUNCTIONALITY
        // ============================================
        
        // Auto-refresh dashboard data every 5 minutes
        let refreshInterval;
        
        function startAutoRefresh() {
            // Clear existing interval
            if (refreshInterval) {
                clearInterval(refreshInterval);
            }
            
            // Set new interval (5 minutes = 300000 ms)
            refreshInterval = setInterval(refreshDashboardData, 300000);
        }
        
        function refreshDashboardData() {
            console.log('Refreshing dashboard data...');
            
            // Show loading indicator
            const refreshIndicator = document.createElement('div');
            refreshIndicator.className = 'position-fixed bottom-0 start-0 m-3 p-2 bg-primary text-white rounded small';
            refreshIndicator.innerHTML = '<i class="fas fa-sync-alt fa-spin me-2"></i>Memperbarui data...';
            refreshIndicator.style.zIndex = '9998';
            document.body.appendChild(refreshIndicator);
            
            // Fetch updated data
            fetch(window.location.href + '?refresh=1', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Update stats cards
                    updateStatsCards(data.stats);
                    
                    // Update chart if data changed
                    if (data.chartData) {
                        updateChartData(data.chartData);
                    }
                    
                    showNotification('Data berhasil diperbarui', 'success');
                }
            })
            .catch(error => {
                console.error('Error refreshing data:', error);
                showNotification('Gagal memperbarui data', 'error');
            })
            .finally(() => {
                // Remove loading indicator
                setTimeout(() => {
                    if (refreshIndicator.parentNode) {
                        refreshIndicator.remove();
                    }
                }, 2000);
            });
        }
        
        function updateStatsCards(stats) {
            // Update each stat card if element exists
            const statUpdates = {
                'total_perawat': '.dashboard-card:nth-child(1) .card-value',
                'total_lansia': '.dashboard-card:nth-child(2) .card-value',
                'total_donasi': '.dashboard-card:nth-child(3) .card-value',
                'saldo_bersih': '.dashboard-card:nth-child(4) .card-value'
            };
            
            for (const [key, selector] of Object.entries(statUpdates)) {
                const element = document.querySelector(selector);
                if (element && stats[key] !== undefined) {
                    const oldValue = element.textContent.trim();
                    const newValue = key.includes('donasi') || key.includes('saldo') 
                        ? 'Rp ' + formatNumber(stats[key])
                        : stats[key];
                    
                    if (oldValue !== newValue) {
                        // Add animation class
                        element.classList.add('text-update');
                        
                        // Update value
                        element.textContent = newValue;
                        
                        // Remove animation class after animation completes
                        setTimeout(() => {
                            element.classList.remove('text-update');
                        }, 1000);
                    }
                }
            }
        }
        
        function updateChartData(newData) {
            if (keuanganChart && newData) {
                keuanganChart.data.labels = newData.labels || chartData.labels;
                keuanganChart.data.datasets[0].data = newData.pemasukan || chartData.pemasukan;
                keuanganChart.data.datasets[1].data = newData.pengeluaran || chartData.pengeluaran;
                keuanganChart.update('none'); // Update without animation
            }
        }
        
        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }
        
        // Start auto-refresh
        startAutoRefresh();
        
        // ============================================
        // 8. RESPONSIVE ADJUSTMENTS
        // ============================================
        
        function handleResponsiveAdjustments() {
            const chartContainer = document.querySelector('.chart-wrapper');
            const quickActions = document.querySelector('.quick-actions');
            
            if (window.innerWidth < 768) {
                // Mobile adjustments
                if (chartContainer) {
                    chartContainer.style.height = '250px';
                }
                
                if (quickActions) {
                    quickActions.style.gridTemplateColumns = 'repeat(2, 1fr)';
                }
            } else if (window.innerWidth < 992) {
                // Tablet adjustments
                if (chartContainer) {
                    chartContainer.style.height = '300px';
                }
                
                if (quickActions) {
                    quickActions.style.gridTemplateColumns = 'repeat(3, 1fr)';
                }
            } else {
                // Desktop adjustments
                if (chartContainer) {
                    chartContainer.style.height = '350px';
                }
                
                if (quickActions) {
                    quickActions.style.gridTemplateColumns = 'repeat(auto-fit, minmax(200px, 1fr))';
                }
            }
        }
        
        // Initial adjustment
        handleResponsiveAdjustments();
        
        // Adjust on resize
        window.addEventListener('resize', handleResponsiveAdjustments);
        
        // ============================================
        // 9. TOOLTIP INITIALIZATION
        // ============================================
        
        // Initialize Bootstrap tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl, {
                trigger: 'hover'
            });
        });
        
        // ============================================
        // 10. ERROR HANDLING
        // ============================================
        
        // Global error handler for dashboard
        window.addEventListener('error', function(event) {
            console.error('Dashboard error:', event.error);
            
            // Don't show notification for Chart.js errors (they're usually harmless)
            if (!event.message.includes('Chart')) {
                showNotification('Terjadi kesalahan pada dashboard', 'error');
            }
            
            // Prevent default error handling
            event.preventDefault();
        });
        
        // Catch unhandled promise rejections
        window.addEventListener('unhandledrejection', function(event) {
            console.error('Unhandled promise rejection:', event.reason);
            showNotification('Terjadi kesalahan pada sistem', 'error');
        });
        
        console.log('Dashboard initialized successfully');
    });
</script>

<style>
    /* Additional styles for JavaScript functionality */
    .text-update {
        animation: pulse 0.5s ease-in-out;
        color: var(--primary-color) !important;
    }
    
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }
    
    .custom-notification {
        transform: translateX(100%);
        opacity: 0;
    }
    
    /* Loading spinner for buttons */
    .spinner-border-sm {
        width: 1rem;
        height: 1rem;
        border-width: 0.2em;
    }
    
    /* Responsive adjustments */
    @media (max-width: 576px) {
        .quick-actions {
            grid-template-columns: 1fr !important;
        }
    }
</style>
@endpush