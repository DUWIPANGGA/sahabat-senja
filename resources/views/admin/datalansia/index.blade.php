@extends('layouts.app')

@section('title', 'Data Lansia')
@section('page-title', 'Data Lansia')
@section('icon', 'fas fa-chart-pie')

@section('content')

        <!-- Content -->
        <div class="content-container">
            {{-- Alert sukses --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Search --}}
            <div class="search-container">
                <form action="{{ route('admin.datalansia.index') }}" method="GET" class="d-flex flex-column flex-md-row gap-3">
                    <div class="flex-grow-1 position-relative">
                        <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                        <input type="text" name="search" class="form-control ps-5" placeholder="Cari berdasarkan nama lansia..." value="{{ $search ?? '' }}">
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary">
                            <i class="fas fa-search me-1"></i>Cari
                        </button>
                        <a href="{{ route('admin.datalansia.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-refresh me-1"></i>Reset
                        </a>
                    </div>
                </form>
            </div>

            {{-- Tabel Data --}}
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Daftar Lansia</h5>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-primary me-3">{{ $datalansia->total() }} Data</span>
                        <a href="{{ route('admin.datalansia.create') }}" class="btn btn-success">
                            <i class="fas fa-plus me-1"></i>Tambah Lansia
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nama Lansia</th>
                                    <th>Umur Lansia</th>
                                    <th width="10%">Tempat Lahir Lansia</th>
                                    <th width="10%">Tanggal Lahir Lansia</th>
                                    <th width="8%">Jenis Kelamin Lansia</th>
                                    <th width="8%">Gol Darah Lansia</th>
                                    <th>Riwayat Penyakit Lansia</th>
                                    <th width="12%">Alergi Lansia</th>
                                    <th width="12%">Obat Rutin Lansia</th>
                                    <th>Nama Anak</th>
                                    <th>No HP Anak</th>
                                    <th>Email Anak</th>
                                    <th>Alamat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($datalansia as $lansia)
                                    <tr>
                                        <td class="fw-bold">{{ $lansia->id }}</td>
                                        <td>{{ $lansia->nama_lansia }}</td>
                                        <td>
                                            @if($lansia->umur_lansia >= 60)
                                            <span class="badge bg-warning">{{ $lansia->umur_lansia }} Thn</span>
                                            @else
                                            <span class="badge bg-info">{{ $lansia->umur_lansia }} Thn</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($lansia->tempat_lahir_lansia)
                                            <span class="badge bg-light text-dark">
                                                <i class="fas fa-map-marker-alt me-1"></i>{{ $lansia->tempat_lahir_lansia }}
                                            </span>
                                            @else
                                            <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($lansia->tanggal_lahir_lansia)
                                            <span class="text-dark">
                                                <i class="fas fa-calendar me-1 text-primary"></i>
                                                {{ \Carbon\Carbon::parse($lansia->tanggal_lahir_lansia)->format('d/m/Y') }}
                                            </span>
                                            @else
                                            <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($lansia->jenis_kelamin_lansia == 'Laki-laki')
                                            <span class="badge bg-primary p-2">
                                                <i class="fas fa-mars me-1"></i>Laki-laki
                                            </span>
                                            @elseif($lansia->jenis_kelamin_lansia == 'Perempuan')
                                            <span class="badge bg-pink p-2">
                                                <i class="fas fa-venus me-1"></i>Perempuan
                                            </span>
                                            @else
                                            <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($lansia->gol_darah_lansia)
                                            <span class="badge bg-danger text-white p-2">
                                                <i class="fas fa-tint me-1"></i>{{ $lansia->gol_darah_lansia }}
                                            </span>
                                            @else
                                            <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($lansia->riwayat_penyakit_lansia)
                                            <span class="badge bg-light">{{ $lansia->riwayat_penyakit_lansia }}</span>
                                            @else
                                            <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($lansia->alergi_lansia)
                                            <span class="badge bg-warning text-dark" title="{{ $lansia->alergi_lansia }}">
                                                <i class="fas fa-allergies me-1"></i>{{ Str::limit($lansia->alergi_lansia, 20) }}
                                            </span>
                                            @else
                                            <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($lansia->obat_rutin_lansia)
                                            <span class="badge bg-success text-white" title="{{ $lansia->obat_rutin_lansia }}">
                                                <i class="fas fa-pills me-1"></i>{{ Str::limit($lansia->obat_rutin_lansia, 20) }}
                                            </span>
                                            @else
                                            <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>{{ $lansia->nama_anak ?? '-' }}</td>
                                        <td>
                                            @if($lansia->no_hp_anak)
                                            <a href="tel:{{ $lansia->no_hp_anak }}" class="text-decoration-none">
                                                <i class="fas fa-phone me-1 text-success"></i>{{ $lansia->no_hp_anak }}
                                            </a>
                                            @else
                                            <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($lansia->email_anak)
                                            <a href="mailto:{{ $lansia->email_anak }}" class="text-decoration-none">
                                                <i class="fas fa-envelope me-1 text-primary"></i>{{ Str::limit($lansia->email_anak, 18) }}
                                            </a>
                                            @else
                                            <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>{{ Str::limit($lansia->alamat_lengkap, 25) }}</td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="{{ route('admin.datalansia.edit', $lansia->id) }}" class="btn btn-warning btn-sm" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="{{ route('admin.datalansia.destroy', $lansia->id) }}"
                                                    class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Yakin mau hapus data ini?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7">
                                            <div class="empty-state">
                                                <i class="fas fa-inbox"></i>
                                                <h5>Tidak ada data ditemukan</h5>
                                                <p>Silakan tambah data lansia atau ubah kata kunci pencarian</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
@endsection
@push('scrips')
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
@endpush