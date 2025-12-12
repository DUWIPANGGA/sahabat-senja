@extends('layouts.app')

@section('title', 'Data Perawat')
@section('page-title', 'Data Perawat')
@section('icon', 'fas fa-chart-pie')

@section('content')
    <div class="content-container">
        {{-- Alert sukses --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Search --}}
        <div class="search-container mb-4">
            <form action="{{ route('admin.DataPerawat.index') }}" method="GET" class="d-flex flex-column flex-md-row gap-3">
                <div class="flex-grow-1 position-relative">
                    <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                    <input type="text" name="search" class="form-control ps-5 py-2" placeholder="Cari berdasarkan nama..." value="{{ $search ?? '' }}">
                </div>
                <div class="d-flex gap-3">
                    <button class="btn btn-primary px-4">
                        <i class="fas fa-search me-2"></i>Cari
                    </button>
                    <a href="{{ route('admin.DataPerawat.index') }}" class="btn btn-outline-secondary px-4">
                        <i class="fas fa-refresh me-2"></i>Reset
                    </a>
                </div>
            </form>
        </div>

        {{-- Tabel Data --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3 px-4 border-bottom">
                <h5 class="mb-0 fw-semibold">
                    <i class="fas fa-list me-2 text-primary"></i>Daftar Perawat
                </h5>
                <div class="d-flex align-items-center gap-3">
                    <span class="badge bg-primary px-3 py-2">{{ $DataPerawat->total() }} Data</span>
                    <a href="{{ route('admin.DataPerawat.create') }}" class="btn btn-success px-4">
                        <i class="fas fa-plus me-2"></i>Tambah Perawat
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4 py-3">ID</th>
                                <th class="px-4 py-3">Nama</th>
                                <th class="px-4 py-3">Email</th>
                                <th class="px-4 py-3">Alamat</th>
                                <th class="px-4 py-3">No HP</th>
                                <th class="px-4 py-3">Jenis Kelamin</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($DataPerawat as $perawat)
                                <tr class="border-bottom">
                                    <td class="px-4 py-3 fw-bold">{{ $perawat->id }}</td>
                                    <td class="px-4 py-3">{{ $perawat->nama }}</td>
                                    <td class="px-4 py-3">
                                        @if($perawat->email)
                                            <a href="mailto:{{ $perawat->email }}" class="text-decoration-none">
                                                <i class="fas fa-envelope me-2 text-primary"></i>{{ Str::limit($perawat->email, 18) }}
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">{{ $perawat->alamat }}</td>
                                    <td class="px-4 py-3">
                                        @if($perawat->no_hp)
                                            <a href="tel:{{ $perawat->no_hp }}" class="text-decoration-none">
                                                <i class="fas fa-phone me-2 text-success"></i>{{ $perawat->no_hp }}
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="badge bg-light text-dark border px-3 py-1">
                                            {{ $perawat->jenis_kelamin }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="{{ route('admin.DataPerawat.edit', $perawat->id) }}" 
                                               class="btn btn-warning btn-sm px-3" 
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('admin.DataPerawat.destroy', $perawat->id) }}"
                                               class="btn btn-danger btn-sm px-3"
                                               onclick="return confirm('Yakin mau hapus data ini?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center px-4 py-5">
                                        <div class="empty-state">
                                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                            <h5 class="mb-2">Tidak ada data ditemukan</h5>
                                            <p class="text-muted mb-0">
                                                Silakan tambah data perawat atau ubah kata kunci pencarian
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($DataPerawat->hasPages())
                <div class="card-footer bg-white py-3 px-4 border-top">
                    {{ $DataPerawat->links() }}
                </div>
            @endif
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