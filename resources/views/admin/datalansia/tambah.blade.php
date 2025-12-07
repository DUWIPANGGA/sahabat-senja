@extends('layouts.app')

@section('title', 'Tambah Data Perawat')
@section('page-title', 'Tambah Data Perawat')
@section('icon', 'fas fa-chart-pie')

@section('content')
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

            {{-- Form Tambah --}}
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <i class="fas fa-user-plus me-2"></i>
                    Tambah Data Lansia
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.datalansia.store') }}" method="POST">
                        @csrf

                        <!-- Data Lansia Section -->
                        <div class="section-header">
                            <h5><i class="fas fa-user-friends"></i> Data Lansia</h5>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Nama Lansia</label>
                                <input type="text" name="nama_lansia" class="form-control" value="{{ old('nama_lansia') }}" required placeholder="Masukkan nama lansia">
                                @error('nama_lansia')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Umur Lansia</label>
                                <input type="number" name="umur_lansia" class="form-control"
                                value="{{ old('umur_lansia') }}"
                                min="40" max="160" required
                                placeholder="Umur lansia">
                                @error('umur_lansia')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tempat Lahir Lansia</label>
                                <input type="text" name="tempat_lahir_lansia" class="form-control" value="{{ old('tempat_lahir_lansia') }}" placeholder="Kota/Kabupaten">
                                @error('tempat_lahir_lansia')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tanggal Lahir Lansia</label>
                                <input type="date" name="tanggal_lahir_lansia" class="form-control" value="{{ old('tanggal_lahir_lansia') }}">
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
                                    <option value="Laki-laki" {{ old('jenis_kelamin_lansia') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="Perempuan" {{ old('jenis_kelamin_lansia') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                @error('jenis_kelamin_lansia')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Gol Darah Lansia</label>
                                <select name="gol_darah_lansia" class="form-control">
                                    <option value="">-- Pilih Golongan Darah --</option>
                                    <option value="A" {{ old('gol_darah_lansia') == 'A' ? 'selected' : '' }}>A</option>
                                    <option value="B" {{ old('gol_darah_lansia') == 'B' ? 'selected' : '' }}>B</option>
                                    <option value="AB" {{ old('gol_darah_lansia') == 'AB' ? 'selected' : '' }}>AB</option>
                                    <option value="O" {{ old('gol_darah_lansia') == 'O' ? 'selected' : '' }}>O</option>
                                </select>
                                @error('gol_darah_lansia')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Riwayat Penyakit Lansia</label>
                                <input type="text" name="riwayat_penyakit_lansia" class="form-control" value="{{ old('riwayat_penyakit_lansia') }}" placeholder="Riwayat penyakit yang diderita">
                                @error('riwayat_penyakit_lansia')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Alergi Lansia</label>
                                <input type="text" name="alergi_lansia" class="form-control" value="{{ old('alergi_lansia') }}" placeholder="Alergi obat/makanan (jika ada)">
                                @error('alergi_lansia')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Obat Rutin Lansia</label>
                                <input type="text" name="obat_rutin_lansia" class="form-control" value="{{ old('obat_rutin_lansia') }}" placeholder="Obat rutin yang dikonsumsi">
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
                                <input type="text" name="nama_anak" class="form-control" value="{{ old('nama_anak') }}" placeholder="Masukkan nama anak">
                                @error('nama_anak')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">No HP Anak</label>
                                <input type="text" name="no_hp_anak" class="form-control"
                                value="{{ old('no_hp_anak') }}"
                                maxlength="15"
                                placeholder="No HP anak">
                                @error('no_hp_anak')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Email Anak</label>
                                <input type="email" name="email_anak" class="form-control" value="{{ old('email_anak') }}" placeholder="Email anak">
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
                                <textarea name="alamat_lengkap" class="form-control" rows="3" placeholder="Masukkan alamat lengkap">{{ old('alamat_lengkap') }}</textarea>
                                @error('alamat_lengkap')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-1"></i>Simpan Data
                            </button>
                            <a href="{{ route('admin.datalansia.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Kembali
                            </a>
                        </div>
                    </form>
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
