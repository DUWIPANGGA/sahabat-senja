@extends('layouts.app')

@section('title', 'Edit Data Perawat')
@section('page-title', 'Edit Data Perawat')
@section('icon', 'fas fa-chart-pie')

@section('content')

<!-- Content -->
<div class="content-container">
    {{-- DEBUG: Tampilkan semua data yang diterima --}}
    @php
        echo "<!-- Debug Info: -->\n";
        echo "<!-- All Variables: " . json_encode(get_defined_vars()) . " -->\n";
        echo "<!-- Session Data: " . json_encode(session()->all()) . " -->\n";
        if(isset($DataPerawat)) {
            echo "<!-- DataPerawat exists: " . $DataPerawat->id . " -->\n";
        }
        if(isset($perawat)) {
            echo "<!-- perawat exists: " . $perawat->id . " -->\n";
        }
    @endphp

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
            Edit Data Perawat
        </div>
        <div class="card-body">
            {{-- Gunakan variabel yang TERSEDIA --}}
            @php
                // Coba cari variabel yang ada
                $data = isset($perawat) ? $perawat : (isset($DataPerawat) ? $DataPerawat : null);
            @endphp
            
            @if($data)
            <form action="{{ route('admin.DataPerawat.update', $data->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" value="{{ old('nama', $data->nama) }}" required placeholder="Masukkan nama lengkap">
                        @error('nama')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $data->email) }}" required placeholder="Masukkan email">
                        @error('email')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">No HP</label>
                        <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp', $data->no_hp) }}" maxlength="15" required placeholder="Masukkan nomor HP">
                        @error('no_hp')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-control" required>
                            <option value="">-- Pilih Jenis Kelamin --</option>
                            <option value="Laki-laki" {{ old('jenis_kelamin', $data->jenis_kelamin) == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Perempuan" {{ old('jenis_kelamin', $data->jenis_kelamin) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('jenis_kelamin')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Alamat</label>
                    <textarea name="alamat" class="form-control" rows="3" required placeholder="Masukkan alamat lengkap">{{ old('alamat', $data->alamat) }}</textarea>
                    @error('alamat')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Catatan (Opsional)</label>
                    <textarea name="catatan" class="form-control" rows="2" placeholder="Masukkan catatan tambahan">{{ old('catatan', $data->catatan ?? '') }}</textarea>
                    @error('catatan')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-sync-alt me-1"></i>Update Data
                    </button>
                    <a href="{{ route('admin.DataPerawat.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>Batal
                    </a>
                </div>
            </form>
            @else
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    Data tidak ditemukan. Silakan kembali ke halaman daftar.
                    <div class="mt-2">
                        <a href="{{ route('admin.DataPerawat.index') }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-arrow-left me-1"></i>Kembali
                        </a>
                    </div>
                </div>
            @endif
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