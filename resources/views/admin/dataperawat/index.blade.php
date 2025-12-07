@extends('layouts.app')

@section('title', 'Data Perawat')
@section('page-title', 'Data Perawat')
@section('icon', 'fas fa-chart-pie')

@section('content')

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
                <form action="{{ route('admin.DataPerawat.index') }}" method="GET" class="d-flex flex-column flex-md-row gap-3">
                    <div class="flex-grow-1 position-relative">
                        <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                        <input type="text" name="search" class="form-control ps-5" placeholder="Cari berdasarkan nama..." value="{{ $search ?? '' }}">
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary">
                            <i class="fas fa-search me-1"></i>Cari
                        </button>
                        <a href="{{ route('admin.DataPerawat.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-refresh me-1"></i>Reset
                        </a>
                    </div>
                </form>
            </div>

            {{-- Tabel Data --}}
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Daftar Perawat</h5>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-primary me-3">{{ $DataPerawat->total() }} Data</span>
                        <a href="{{ route('admin.DataPerawat.create') }}" class="btn btn-success">
                            <i class="fas fa-plus me-1"></i>Tambah Perawat
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Alamat</th>
                                    <th>No HP</th>
                                    <th>Jenis Kelamin</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($DataPerawat as $perawat)
                                    <tr>
                                        <td class="fw-bold">{{ $perawat->id }}</td>
                                        <td>{{ $perawat->nama }}</td>
                                        <td>
                                            @if($perawat->email)
                                            <a href="mailto:{{ $perawat->email }}" class="text-decoration-none">
                                                <i class="fas fa-envelope me-1 text-primary"></i>{{ Str::limit($perawat->email, 18) }}
                                            </a>
                                            @else
                                            <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>{{ $perawat->alamat }}</td>
                                        <td>
                                            @if($perawat->no_hp)
                                            <a href="tel:{{ $perawat->no_hp }}" class="text-decoration-none">
                                                <i class="fas fa-phone me-1 text-success"></i>{{ $perawat->no_hp }}
                                            </a>
                                            @else
                                            <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-light">{{ $perawat->jenis_kelamin }}</span>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="{{ route('admin.DataPerawat.edit', $perawat->id) }}" class="btn btn-warning btn-sm" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="{{ route('admin.DataPerawat.destroy', $perawat->id) }}"
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
                                                <p>Silakan tambah data perawat atau ubah kata kunci pencarian</p>
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
@push('scripts')
    

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
