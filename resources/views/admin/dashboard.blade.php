@extends('layouts.app')
@section('title','Admin - Sahabat Senja')
@section('content')

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
                        @for($year = $currentYear - 2; $year <= $currentYear + 1; $year++) <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
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

@endsection
@push('script')
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
            type: 'bar'
            , data: {
                labels: chartData.labels
                , datasets: [{
                        label: 'Pemasukan'
                        , data: chartData.pemasukan
                        , backgroundColor: 'rgba(139, 115, 85, 0.8)'
                        , borderColor: 'rgba(139, 115, 85, 1)'
                        , borderWidth: 2
                        , borderRadius: 8
                        , borderSkipped: false
                    , }
                    , {
                        label: 'Pengeluaran'
                        , data: chartData.pengeluaran
                        , backgroundColor: 'rgba(255, 183, 77, 0.8)'
                        , borderColor: 'rgba(255, 183, 77, 1)'
                        , borderWidth: 2
                        , borderRadius: 8
                        , borderSkipped: false
                    , }
                ]
            }
            , options: {
                responsive: true
                , maintainAspectRatio: false
                , interaction: {
                    intersect: false
                    , mode: 'index'
                , }
                , plugins: {
                    legend: {
                        position: 'top'
                        , labels: {
                            font: {
                                size: 14
                                , family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                            }
                            , padding: 20
                            , usePointStyle: true
                        , }
                    }
                    , tooltip: {
                        backgroundColor: 'rgba(255, 255, 255, 0.95)'
                        , titleColor: 'var(--text-dark)'
                        , bodyColor: 'var(--text-dark)'
                        , borderColor: 'var(--accent-color)'
                        , borderWidth: 1
                        , padding: 12
                        , callbacks: {
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
                }
                , scales: {
                    y: {
                        beginAtZero: true
                        , grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                        , ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                            , font: {
                                size: 12
                            }
                        }
                    }
                    , x: {
                        grid: {
                            display: false
                        }
                        , ticks: {
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
@endpush
