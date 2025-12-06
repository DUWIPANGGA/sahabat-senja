@extends('layouts.app')

@section('title', 'Grafik Keuangan')
@section('page-title', 'Grafik Keuangan')
@section('icon', 'fas fa-chart-pie')

@section('content')
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
@endsection

@push('styles')
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .filter-card {
            background-color: white;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
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
        
        .chart-wrapper {
            position: relative;
            height: 400px;
            width: 100%;
        }
        
        .donut-chart-container {
            background-color: white;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            padding: 1.5rem;
            margin-bottom: 2rem;
            height: 100%;
        }
        
        .donut-chart-wrapper {
            position: relative;
            height: 300px;
            width: 100%;
        }
        
        .table-container {
            background-color: white;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .table-header {
            border-bottom: 1px solid var(--accent-color);
            padding-bottom: 1rem;
            margin-bottom: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .custom-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .custom-table thead {
            background-color: rgba(139, 115, 85, 0.05);
        }
        
        .custom-table th {
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: var(--text-dark);
            border-bottom: 2px solid var(--accent-color);
        }
        
        .custom-table td {
            padding: 1rem;
            border-bottom: 1px solid var(--accent-color);
        }
        
        .custom-table tbody tr:hover {
            background-color: rgba(139, 115, 85, 0.05);
        }
        
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
        
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
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
        
        .card-change.positive {
            color: var(--success-color);
        }
        
        .card-change.negative {
            color: #e53935;
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
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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