@extends('layouts.app')

@section('title', 'Grafik Keuangan')
@section('page-title', 'Grafik Keuangan')
@section('icon', 'fas fa-chart-pie')

@section('content')
<div class="container-fluid py-4">
    {{-- Filter Section --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 fw-bold text-dark"><i class="fas fa-filter me-2"></i>Filter Data</h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label text-muted">Pilih Tahun</label>
                    <select id="tahunFilter" class="form-select">
                        @foreach($tahunList as $tahunItem)
                        <option value="{{ $tahunItem }}" {{ $tahun == $tahunItem ? 'selected' : '' }}>
                            Tahun {{ $tahunItem }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted">Pilih Bulan</label>
                    <select id="bulanFilter" class="form-select">
                        @foreach([
                            '1' => 'Januari', '2' => 'Februari', '3' => 'Maret', 
                            '4' => 'April', '5' => 'Mei', '6' => 'Juni', 
                            '7' => 'Juli', '8' => 'Agustus', '9' => 'September',
                            '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                        ] as $key => $value)
                        <option value="{{ $key }}" {{ request('bulan', date('m')) == $key ? 'selected' : '' }}>
                            {{ $value }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button id="filterBtn" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-2"></i>Terapkan Filter
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-start border-success border-4 shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-success text-uppercase mb-1">
                                Total Pemasukan
                            </div>
                            <div class="h5 mb-0 fw-bold">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</div>
                            @if($totalPemasukanBulanLalu > 0)
                            <div class="text-xs mt-1">
                                <span class="{{ ($totalPemasukan - $totalPemasukanBulanLalu) >= 0 ? 'text-success' : 'text-danger' }}">
                                    <i class="fas fa-arrow-{{ ($totalPemasukan - $totalPemasukanBulanLalu) >= 0 ? 'up' : 'down' }} me-1"></i>
                                    {{ number_format(abs(($totalPemasukan - $totalPemasukanBulanLalu) / $totalPemasukanBulanLalu * 100), 1) }}%
                                    dari bulan lalu
                                </span>
                            </div>
                            @endif
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-start border-danger border-4 shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-danger text-uppercase mb-1">
                                Total Pengeluaran
                            </div>
                            <div class="h5 mb-0 fw-bold">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</div>
                            @if($totalPengeluaranBulanLalu > 0)
                            <div class="text-xs mt-1">
                                <span class="{{ ($totalPengeluaran - $totalPengeluaranBulanLalu) >= 0 ? 'text-danger' : 'text-success' }}">
                                    <i class="fas fa-arrow-{{ ($totalPengeluaran - $totalPengeluaranBulanLalu) >= 0 ? 'up' : 'down' }} me-1"></i>
                                    {{ number_format(abs(($totalPengeluaran - $totalPengeluaranBulanLalu) / $totalPengeluaranBulanLalu * 100), 1) }}%
                                    dari bulan lalu
                                </span>
                            </div>
                            @endif
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hand-holding-usd fa-2x text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-start border-primary border-4 shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-primary text-uppercase mb-1">
                                Saldo Bersih
                            </div>
                            <div class="h5 mb-0 fw-bold {{ $saldo >= 0 ? 'text-success' : 'text-danger' }}">
                                Rp {{ number_format($saldo, 0, ',', '.') }}
                            </div>
                            @if($saldoBulanLalu != 0)
                            <div class="text-xs mt-1">
                                <span class="{{ ($saldo - $saldoBulanLalu) >= 0 ? 'text-success' : 'text-danger' }}">
                                    <i class="fas fa-arrow-{{ ($saldo - $saldoBulanLalu) >= 0 ? 'up' : 'down' }} me-1"></i>
                                    {{ number_format(abs(($saldo - $saldoBulanLalu) / abs($saldoBulanLalu) * 100), 1) }}%
                                    dari bulan lalu
                                </span>
                            </div>
                            @endif
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-piggy-bank fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Chart --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 fw-bold text-dark">
                <i class="fas fa-chart-line me-2"></i>Grafik Pemasukan vs Pengeluaran Tahun {{ $tahun }}
            </h6>
            <div class="btn-group btn-group-sm">
                <button type="button" class="btn btn-outline-primary active chart-type-btn" data-chart-type="bar">
                    <i class="fas fa-chart-bar"></i>
                </button>
                <button type="button" class="btn btn-outline-primary chart-type-btn" data-chart-type="line">
                    <i class="fas fa-chart-line"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div style="height: 400px;">
                <canvas id="mainChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Pie Charts --}}
    <div class="row mb-4">
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-dark">
                        <i class="fas fa-chart-pie me-2"></i>Pemasukan per Sumber
                    </h6>
                    <small class="text-muted">Bulan {{ $namaBulan }} {{ $tahun }}</small>
                </div>
                <div class="card-body">
                    <div style="height: 350px;">
                        <canvas id="pemasukanChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-dark">
                        <i class="fas fa-chart-pie me-2"></i>Pengeluaran per Kategori
                    </h6>
                    <small class="text-muted">Bulan {{ $namaBulan }} {{ $tahun }}</small>
                </div>
                <div class="card-body">
                    <div style="height: 350px;">
                        <canvas id="pengeluaranChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Transactions --}}
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-dark">
                        <i class="fas fa-money-bill-wave me-2"></i>Pemasukan Terbaru
                    </h6>
                    <a href="{{ route('laporan.pemasukan') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-eye me-1"></i> Lihat Semua
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-4 py-3">Tanggal</th>
                                    <th class="px-4 py-3">Sumber</th>
                                    <th class="px-4 py-3 text-end">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentPemasukan as $item)
                                <tr>
                                    <td class="px-4 py-3">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                                    <td class="px-4 py-3">
                                        <span class="badge bg-success">{{ $item->sumber }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-end text-success fw-bold">
                                        + Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-5 text-muted">
                                        <i class="fas fa-inbox fa-2x mb-3 opacity-50"></i>
                                        <p>Tidak ada data pemasukan</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-dark">
                        <i class="fas fa-hand-holding-usd me-2"></i>Pengeluaran Terbaru
                    </h6>
                    <a href="{{ route('laporan.pengeluaran') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-eye me-1"></i> Lihat Semua
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-4 py-3">Tanggal</th>
                                    <th class="px-4 py-3">Keterangan</th>
                                    <th class="px-4 py-3 text-end">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentPengeluaran as $item)
                                <tr>
                                    <td class="px-4 py-3">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                                    <td class="px-4 py-3">
                                        <span class="badge bg-danger">{{ $item->keterangan }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-end text-danger fw-bold">
                                        - Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-5 text-muted">
                                        <i class="fas fa-inbox fa-2x mb-3 opacity-50"></i>
                                        <p>Tidak ada data pengeluaran</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .card {
        border: none;
        border-radius: 10px;
    }
    
    .table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
    }
    
    .btn-group-sm .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
    
    @media (max-width: 768px) {
        .btn-group {
            width: 100%;
        }
        
        .btn-group .btn {
            flex: 1;
        }
        
        .table-responsive {
            font-size: 0.875rem;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        // Colors for charts
        const primaryColor = 'rgba(139, 115, 85, 0.8)'; // var(--primary-color)
        const secondaryColor = 'rgba(255, 183, 77, 0.8)'; // var(--warning-color)
        const borderColors = [
            'rgba(139, 115, 85, 1)',
            'rgba(93, 64, 55, 1)',
            'rgba(165, 123, 91, 1)',
            'rgba(215, 204, 200, 1)',
            'rgba(124, 179, 66, 1)',
            'rgba(77, 182, 172, 1)',
        ];
        const backgroundColors = [
            'rgba(139, 115, 85, 0.8)',
            'rgba(93, 64, 55, 0.8)',
            'rgba(165, 123, 91, 0.8)',
            'rgba(215, 204, 200, 0.8)',
            'rgba(124, 179, 66, 0.8)',
            'rgba(77, 182, 172, 0.8)',
        ];
        const warningColors = [
            'rgba(255, 183, 77, 0.8)',
            'rgba(255, 152, 0, 0.8)',
            'rgba(255, 193, 7, 0.8)',
            'rgba(255, 224, 130, 0.8)',
            'rgba(255, 167, 38, 0.8)',
            'rgba(251, 192, 45, 0.8)',
        ];

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
                        backgroundColor: primaryColor,
                        borderColor: 'rgba(139, 115, 85, 1)',
                        borderWidth: 1,
                        borderRadius: 4,
                    },
                    {
                        label: 'Pengeluaran',
                        data: @json($pengeluaranData),
                        backgroundColor: secondaryColor,
                        borderColor: 'rgba(255, 183, 77, 1)',
                        borderWidth: 1,
                        borderRadius: 4,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: {
                                size: 12
                            },
                            padding: 10,
                            usePointStyle: true,
                        }
                    },
                    tooltip: {
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
                                size: 11
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 11
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

        // Pemasukan Pie Chart
        const pemasukanCtx = document.getElementById('pemasukanChart').getContext('2d');
        new Chart(pemasukanCtx, {
            type: 'doughnut',
            data: {
                labels: @json($pemasukanLabels),
                datasets: [{
                    data: @json($pemasukanValues),
                    backgroundColor: backgroundColors.slice(0, @json($pemasukanLabels).length),
                    borderColor: borderColors.slice(0, @json($pemasukanLabels).length),
                    borderWidth: 1
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
                                size: 11
                            },
                            padding: 10,
                            usePointStyle: true,
                            pointStyle: 'circle',
                        }
                    },
                    tooltip: {
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

        // Pengeluaran Pie Chart
        const pengeluaranCtx = document.getElementById('pengeluaranChart').getContext('2d');
        new Chart(pengeluaranCtx, {
            type: 'doughnut',
            data: {
                labels: @json($pengeluaranLabels),
                datasets: [{
                    data: @json($pengeluaranValues),
                    backgroundColor: warningColors.slice(0, @json($pengeluaranLabels).length),
                    borderColor: borderColors.slice(0, @json($pengeluaranLabels).length),
                    borderWidth: 1
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
                                size: 11
                            },
                            padding: 10,
                            usePointStyle: true,
                            pointStyle: 'circle',
                        }
                    },
                    tooltip: {
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
@endsection