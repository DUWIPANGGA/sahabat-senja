@extends('layouts.app')

@section('title', 'Grafik Keuangan')
@section('page-title', 'Grafik Keuangan')
@section('icon', 'fas fa-chart-pie')

@section('content')

        <div class="container-fluid p-4">
            <!-- Filter Card -->
            <div class="filter-card">
                <h3 class="mb-4"><i class="fas fa-filter"></i>Filter Data</h3>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Pilih Tahun</label>
                        <select id="tahunFilter" class="form-select">
                            @foreach($tahunList as $tahunItem)
                                <option value="{{ $tahunItem }}" {{ $tahun == $tahunItem ? 'selected' : '' }}>
                                    Tahun {{ $tahunItem }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Pilih Bulan</label>
                        <select id="bulanFilter" class="form-select">
                            @foreach(['1'=>'Januari','2'=>'Februari','3'=>'Maret','4'=>'April','5'=>'Mei','6'=>'Juni','7'=>'Juli','8'=>'Agustus','9'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'] as $key => $value)
                                <option value="{{ $key }}" {{ request('bulan', date('m')) == $key ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3 d-flex align-items-end">
                        <button id="filterBtn" class="btn w-100" style="background-color: var(--primary-color); color: white; padding: 0.6rem;">
                            <i class="fas fa-filter me-2"></i>Terapkan Filter
                        </button>
                    </div>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="stats-grid">
                <div class="dashboard-card">
                    <div class="d-flex align-items-center">
                        <div class="card-icon primary">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <div>
                            <div class="card-title">Total Pemasukan</div>
                            <div class="card-value">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</div>
                            <div class="card-change positive">
                                <i class="fas fa-arrow-up me-1"></i> 
                                @if($totalPemasukanBulanLalu > 0)
                                    {{ number_format(($totalPemasukan - $totalPemasukanBulanLalu) / $totalPemasukanBulanLalu * 100, 1) }}%
                                @else
                                    0%
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="dashboard-card">
                    <div class="d-flex align-items-center">
                        <div class="card-icon warning">
                            <i class="fas fa-hand-holding-usd"></i>
                        </div>
                        <div>
                            <div class="card-title">Total Pengeluaran</div>
                            <div class="card-value">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</div>
                            <div class="card-change {{ ($totalPengeluaran - $totalPengeluaranBulanLalu) >= 0 ? 'negative' : 'positive' }}">
                                <i class="fas fa-arrow-{{ ($totalPengeluaran - $totalPengeluaranBulanLalu) >= 0 ? 'up' : 'down' }} me-1"></i> 
                                @if($totalPengeluaranBulanLalu > 0)
                                    {{ number_format(abs(($totalPengeluaran - $totalPengeluaranBulanLalu) / $totalPengeluaranBulanLalu * 100), 1) }}%
                                @else
                                    0%
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="dashboard-card">
                    <div class="d-flex align-items-center">
                        <div class="card-icon {{ $saldo >= 0 ? 'success' : 'danger' }}">
                            <i class="fas fa-piggy-bank"></i>
                        </div>
                        <div>
                            <div class="card-title">Saldo Bersih</div>
                            <div class="card-value">Rp {{ number_format($saldo, 0, ',', '.') }}</div>
                            <div class="card-change {{ $saldoBulanLalu <= $saldo ? 'positive' : 'negative' }}">
                                <i class="fas fa-arrow-{{ $saldoBulanLalu <= $saldo ? 'up' : 'down' }} me-1"></i> 
                                @if($saldoBulanLalu > 0)
                                    {{ number_format((($saldo - $saldoBulanLalu) / $saldoBulanLalu) * 100, 1) }}%
                                @else
                                    0%
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Chart -->
            <div class="chart-container">
                <div class="chart-header">
                    <h3><i class="fas fa-chart-line"></i>Grafik Pemasukan vs Pengeluaran Tahun {{ $tahun }}</h3>
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
                    <canvas id="mainChart"></canvas>
                </div>
            </div>

            <!-- Donut Charts -->
            <div class="row mb-4">
                <div class="col-lg-6">
                    <div class="donut-chart-container">
                        <div class="chart-header">
                            <h3><i class="fas fa-chart-pie"></i>Pemasukan per Sumber</h3>
                            <small class="text-muted">Bulan {{ $namaBulan }} {{ $tahun }}</small>
                        </div>
                        <div class="donut-chart-wrapper">
                            <canvas id="pemasukanChart"></canvas>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="donut-chart-container">
                        <div class="chart-header">
                            <h3><i class="fas fa-chart-pie"></i>Pengeluaran per Kategori</h3>
                            <small class="text-muted">Bulan {{ $namaBulan }} {{ $tahun }}</small>
                        </div>
                        <div class="donut-chart-wrapper">
                            <canvas id="pengeluaranChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="row">
                <div class="col-lg-6">
                    <div class="table-container">
                        <div class="table-header">
                            <h3><i class="fas fa-money-bill-wave"></i>Pemasukan Terbaru</h3>
                            <a href="{{ route('laporan.pemasukan') }}" class="btn btn-sm" style="background-color: var(--primary-color); color: white;">
                                <i class="fas fa-eye me-1"></i> Lihat Semua
                            </a>
                        </div>
                        <div class="table-responsive">
                            <table class="custom-table">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Sumber</th>
                                        <th class="text-end">Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentPemasukan as $item)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                                            <td>{{ $item->sumber }}</td>
                                            <td class="text-end text-success fw-bold">+ Rp {{ number_format($item->jumlah, 0, ',', '.') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-4">Tidak ada data pemasukan</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="table-container">
                        <div class="table-header">
                            <h3><i class="fas fa-hand-holding-usd"></i>Pengeluaran Terbaru</h3>
                            <a href="{{ route('laporan.pengeluaran') }}" class="btn btn-sm" style="background-color: var(--primary-color); color: white;">
                                <i class="fas fa-eye me-1"></i> Lihat Semua
                            </a>
                        </div>
                        <div class="table-responsive">
                            <table class="custom-table">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Keterangan</th>
                                        <th class="text-end">Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentPengeluaran as $item)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                                            <td>{{ $item->keterangan }}</td>
                                            <td class="text-end text-danger fw-bold">- Rp {{ number_format($item->jumlah, 0, ',', '.') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-4">Tidak ada data pengeluaran</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
@endsection
@push('scripts')
    


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

            // Filter Button
            document.getElementById('filterBtn').addEventListener('click', function() {
                const tahun = document.getElementById('tahunFilter').value;
                const bulan = document.getElementById('bulanFilter').value;
                window.location.href = `{{ route('admin.grafik.index') }}?tahun=${tahun}&bulan=${bulan}`;
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

            // Initialize Main Chart
            const mainChartCtx = document.getElementById('mainChart').getContext('2d');
            let mainChart = new Chart(mainChartCtx, {
                type: 'bar',
                data: {
                    labels: @json($labels),
                    datasets: [
                        {
                            label: 'Pemasukan',
                            data: @json($pemasukanData),
                            backgroundColor: 'rgba(139, 115, 85, 0.8)',
                            borderColor: 'rgba(139, 115, 85, 1)',
                            borderWidth: 2,
                            borderRadius: 8,
                            borderSkipped: false,
                        },
                        {
                            label: 'Pengeluaran',
                            data: @json($pengeluaranData),
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
                mainChart.config.type = type;
                mainChart.update();
            }

            // Pemasukan Chart (Donut)
            const pemasukanCtx = document.getElementById('pemasukanChart').getContext('2d');
            new Chart(pemasukanCtx, {
                type: 'doughnut',
                data: {
                    labels: @json($pemasukanLabels),
                    datasets: [{
                        data: @json($pemasukanValues),
                        backgroundColor: [
                            'rgba(139, 115, 85, 0.8)',
                            'rgba(93, 64, 55, 0.8)',
                            'rgba(165, 123, 91, 0.8)',
                            'rgba(215, 204, 200, 0.8)',
                            'rgba(124, 179, 66, 0.8)',
                            'rgba(77, 182, 172, 0.8)',
                        ],
                        borderColor: [
                            'rgba(139, 115, 85, 1)',
                            'rgba(93, 64, 55, 1)',
                            'rgba(165, 123, 91, 1)',
                            'rgba(215, 204, 200, 1)',
                            'rgba(124, 179, 66, 1)',
                            'rgba(77, 182, 172, 1)',
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                font: {
                                    size: 12,
                                    family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                                },
                                padding: 15,
                                usePointStyle: true,
                                pointStyle: 'circle',
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
                                    const label = context.label || '';
                                    const value = context.raw;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                    return `${label}: Rp ${value.toLocaleString('id-ID')} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });

            // Pengeluaran Chart (Donut)
            const pengeluaranCtx = document.getElementById('pengeluaranChart').getContext('2d');
            new Chart(pengeluaranCtx, {
                type: 'doughnut',
                data: {
                    labels: @json($pengeluaranLabels),
                    datasets: [{
                        data: @json($pengeluaranValues),
                        backgroundColor: [
                            'rgba(255, 183, 77, 0.8)',
                            'rgba(255, 152, 0, 0.8)',
                            'rgba(255, 193, 7, 0.8)',
                            'rgba(255, 224, 130, 0.8)',
                            'rgba(255, 167, 38, 0.8)',
                            'rgba(251, 192, 45, 0.8)',
                        ],
                        borderColor: [
                            'rgba(255, 183, 77, 1)',
                            'rgba(255, 152, 0, 1)',
                            'rgba(255, 193, 7, 1)',
                            'rgba(255, 224, 130, 1)',
                            'rgba(255, 167, 38, 1)',
                            'rgba(251, 192, 45, 1)',
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                font: {
                                    size: 12,
                                    family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                                },
                                padding: 15,
                                usePointStyle: true,
                                pointStyle: 'circle',
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
                                    const label = context.label || '';
                                    const value = context.raw;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                    return `${label}: Rp ${value.toLocaleString('id-ID')} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@endpush