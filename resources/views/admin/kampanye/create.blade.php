@extends('layouts.app')

@section('title', 'Tambah Kampanye Donasi')
@section('page-title', 'Tambah Kampanye Donasi')
@section('icon', 'fas fa-plus-circle')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Error Messages -->
            @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Terjadi kesalahan:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            <!-- Main Form -->
            <div class="card shadow-sm">
                <form action="{{ route('admin.kampanye.store') }}" method="POST" enctype="multipart/form-data" id="kampanyeForm">
                    @csrf
                    
                    <div class="card-body">
                        <div class="row">
                            <!-- Left Column -->
                            <div class="col-lg-8">
                                <!-- Campaign Title -->
                                <div class="mb-4">
                                    <label for="judul" class="form-label fw-bold">
                                        Judul Kampanye <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('judul') is-invalid @enderror" 
                                           id="judul" name="judul" value="{{ old('judul') }}" 
                                           placeholder="Contoh: Bantu Operasi Katarak Nenek Sumirah" required>
                                    @error('judul')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Buat judul yang menarik dan jelas (maks. 255 karakter)</small>
                                </div>

                                <!-- Short Description -->
                                <div class="mb-4">
                                    <label for="deskripsi_singkat" class="form-label fw-bold">
                                        Deskripsi Singkat <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control @error('deskripsi_singkat') is-invalid @enderror" 
                                              id="deskripsi_singkat" name="deskripsi_singkat" 
                                              rows="3" maxlength="500" required>{{ old('deskripsi_singkat') }}</textarea>
                                    @error('deskripsi_singkat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="d-flex justify-content-between align-items-center mt-1">
                                        <small class="text-muted">Deskripsi yang muncul di halaman utama (maks. 500 karakter)</small>
                                        <span id="deskripsi_singkat_counter" class="text-muted">0/500</span>
                                    </div>
                                </div>

                                <!-- Full Description -->
                                <div class="mb-4">
                                    <label for="deskripsi" class="form-label fw-bold">
                                        Deskripsi Lengkap <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control @error('deskripsi') is-invalid @enderror" 
                                              id="deskripsi" name="deskripsi" rows="6" required>{{ old('deskripsi') }}</textarea>
                                    @error('deskripsi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Ceritakan detail kampanye, latar belakang, dan tujuan</small>
                                </div>

                                <!-- Full Story -->
                                <div class="mb-4">
                                    <label for="cerita_lengkap" class="form-label fw-bold">
                                        Cerita Lengkap
                                    </label>
                                    <textarea class="form-control @error('cerita_lengkap') is-invalid @enderror" 
                                              id="cerita_lengkap" name="cerita_lengkap" rows="8">{{ old('cerita_lengkap') }}</textarea>
                                    @error('cerita_lengkap')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Cerita lebih detail tentang penerima manfaat</small>
                                </div>

                                <!-- Thank You Message -->
                                <div class="mb-4">
                                    <label for="terima_kasih_pesan" class="form-label fw-bold">
                                        Pesan Terima Kasih
                                    </label>
                                    <textarea class="form-control @error('terima_kasih_pesan') is-invalid @enderror" 
                                              id="terima_kasih_pesan" name="terima_kasih_pesan" 
                                              rows="4">{{ old('terima_kasih_pesan') }}</textarea>
                                    @error('terima_kasih_pesan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Pesan yang akan ditampilkan setelah donasi berhasil</small>
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="col-lg-4">
                                <!-- Status -->
                                <div class="mb-4">
                                    <label for="status" class="form-label fw-bold">
                                        Status <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('status') is-invalid @enderror" 
                                            id="status" name="status" required>
                                        <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                        <option value="aktif" {{ old('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                        <option value="ditutup" {{ old('status') == 'ditutup' ? 'selected' : '' }}>Ditutup</option>
                                    </select>
                                    @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Category -->
                                <div class="mb-4">
                                    <label for="kategori" class="form-label fw-bold">
                                        Kategori <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('kategori') is-invalid @enderror" 
                                            id="kategori" name="kategori" required>
                                        <option value="">Pilih Kategori</option>
                                        <option value="lansia" {{ old('kategori') == 'lansia' ? 'selected' : '' }}>Lansia</option>
                                        <option value="kesehatan" {{ old('kategori') == 'kesehatan' ? 'selected' : '' }}>Kesehatan</option>
                                        <option value="pendidikan" {{ old('kategori') == 'pendidikan' ? 'selected' : '' }}>Pendidikan</option>
                                        <option value="bencana" {{ old('kategori') == 'bencana' ? 'selected' : '' }}>Bencana</option>
                                        <option value="infrastruktur" {{ old('kategori') == 'infrastruktur' ? 'selected' : '' }}>Infrastruktur</option>
                                        <option value="lainnya" {{ old('kategori') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                                    </select>
                                    @error('kategori')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Elderly Data -->
                                <div class="mb-4">
                                    <label for="datalansia_id" class="form-label fw-bold">
                                        Penerima Manfaat (Lansia)
                                    </label>
                                    <select class="form-select @error('datalansia_id') is-invalid @enderror" 
                                            id="datalansia_id" name="datalansia_id">
                                        <option value="">Pilih Lansia (Opsional)</option>
                                        @foreach($datalansia as $lansia)
                                        <option value="{{ $lansia->id }}" {{ old('datalansia_id') == $lansia->id ? 'selected' : '' }}>
                                            {{ $lansia->nama_lansia }} ({{ $lansia->nik ?? 'N/A' }})
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('datalansia_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Pilih jika kampanye untuk lansia tertentu</small>
                                </div>

                                <!-- Target Fund -->
                                <div class="mb-4">
                                    <label for="target_dana" class="form-label fw-bold">
                                        Target Dana <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" 
                                            class="form-control @error('target_dana') is-invalid @enderror"
                                            id="target_dana"
                                            name="target_dana"
                                            value="{{ old('target_dana') }}"
                                            placeholder="1000000"
                                            required>
                                    </div>
                                    @error('target_dana')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Minimal Rp 100.000. Contoh: 1000000</small>
                                </div>

                                <!-- Date Range -->
                                <div class="row mb-4">
                                    <div class="col-6">
                                        <label for="tanggal_mulai" class="form-label fw-bold">
                                            Tanggal Mulai <span class="text-danger">*</span>
                                        </label>
                                        <input type="date" class="form-control @error('tanggal_mulai') is-invalid @enderror" 
                                               id="tanggal_mulai" name="tanggal_mulai" 
                                               value="{{ old('tanggal_mulai', date('Y-m-d')) }}" required>
                                        @error('tanggal_mulai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-6">
                                        <label for="tanggal_selesai" class="form-label fw-bold">
                                            Tanggal Selesai <span class="text-danger">*</span>
                                        </label>
                                        <input type="date" class="form-control @error('tanggal_selesai') is-invalid @enderror" 
                                               id="tanggal_selesai" name="tanggal_selesai" 
                                               value="{{ old('tanggal_selesai', date('Y-m-d', strtotime('+30 days'))) }}" required>
                                        @error('tanggal_selesai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Featured -->
                                <div class="mb-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" 
                                               id="is_featured" name="is_featured" value="1" 
                                               {{ old('is_featured') ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold" for="is_featured">
                                            Tampilkan sebagai Featured
                                        </label>
                                    </div>
                                    <small class="text-muted">Kampanye akan ditampilkan di halaman utama</small>
                                </div>

                                <!-- Main Image -->
                                <div class="mb-4">
                                    <label for="gambar" class="form-label fw-bold">
                                        Gambar Utama
                                    </label>
                                    <input type="file" class="form-control @error('gambar') is-invalid @enderror" 
                                           id="gambar" name="gambar" accept="image/*">
                                    @error('gambar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Ukuran maksimal 2MB. Format: JPG, PNG</small>
                                    <div id="gambar_preview" class="mt-2 text-center d-none">
                                        <img src="" alt="Preview" class="img-fluid rounded" style="max-height: 200px;">
                                    </div>
                                </div>

                                <!-- Thumbnail -->
                                <div class="mb-4">
                                    <label for="thumbnail" class="form-label fw-bold">
                                        Thumbnail
                                    </label>
                                    <input type="file" class="form-control @error('thumbnail') is-invalid @enderror" 
                                           id="thumbnail" name="thumbnail" accept="image/*">
                                    @error('thumbnail')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Ukuran maksimal 1MB. Format: JPG, PNG</small>
                                    <div id="thumbnail_preview" class="mt-2 text-center d-none">
                                        <img src="" alt="Preview" class="img-fluid rounded" style="max-height: 150px;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Footer -->
                    <div class="card-footer bg-white border-top">
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('admin.kampanye.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Simpan Kampanye
                            </button>
                        </div>
                    </div>
                </form>
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
    
    .form-label {
        color: var(--text-dark);
    }
    
    .form-control:focus, .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(139, 115, 85, 0.25);
    }
    
    .form-check-input:checked {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }
    
    textarea {
        resize: vertical;
        min-height: 100px;
    }
    
    @media (max-width: 768px) {
        .row > .col-lg-8, .row > .col-lg-4 {
            padding-bottom: 1rem;
        }
        
        .card-body {
            padding: 1rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Character counter for short description
        const descInput = document.getElementById('deskripsi_singkat');
        const counter = document.getElementById('deskripsi_singkat_counter');
        
        function updateCounter() {
            counter.textContent = `${descInput.value.length}/500`;
        }
        
        descInput.addEventListener('input', updateCounter);
        updateCounter(); // Initialize

        // Image preview for main image
        const gambarInput = document.getElementById('gambar');
        const gambarPreview = document.getElementById('gambar_preview');
        const gambarImg = gambarPreview.querySelector('img');
        
        gambarInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            
            if (file) {
                if (file.size > 2 * 1024 * 1024) {
                    alert('Ukuran file terlalu besar. Maksimal 2MB.');
                    this.value = '';
                    gambarPreview.classList.add('d-none');
                    return;
                }
                
                if (!file.type.match('image/jpeg') && !file.type.match('image/png')) {
                    alert('Format file tidak didukung. Gunakan JPG atau PNG.');
                    this.value = '';
                    gambarPreview.classList.add('d-none');
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    gambarImg.src = e.target.result;
                    gambarPreview.classList.remove('d-none');
                };
                reader.readAsDataURL(file);
            } else {
                gambarPreview.classList.add('d-none');
            }
        });

        // Image preview for thumbnail
        const thumbnailInput = document.getElementById('thumbnail');
        const thumbnailPreview = document.getElementById('thumbnail_preview');
        const thumbnailImg = thumbnailPreview.querySelector('img');
        
        thumbnailInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            
            if (file) {
                if (file.size > 1 * 1024 * 1024) {
                    alert('Ukuran file terlalu besar. Maksimal 1MB.');
                    this.value = '';
                    thumbnailPreview.classList.add('d-none');
                    return;
                }
                
                if (!file.type.match('image/jpeg') && !file.type.match('image/png')) {
                    alert('Format file tidak didukung. Gunakan JPG atau PNG.');
                    this.value = '';
                    thumbnailPreview.classList.add('d-none');
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    thumbnailImg.src = e.target.result;
                    thumbnailPreview.classList.remove('d-none');
                };
                reader.readAsDataURL(file);
            } else {
                thumbnailPreview.classList.add('d-none');
            }
        });

        // Date validation
        const tanggalMulai = document.getElementById('tanggal_mulai');
        const tanggalSelesai = document.getElementById('tanggal_selesai');
        
        tanggalMulai.addEventListener('change', function() {
            tanggalSelesai.min = this.value;
            
            if (tanggalSelesai.value && tanggalSelesai.value < this.value) {
                tanggalSelesai.value = '';
            }
        });
        
        tanggalSelesai.addEventListener('change', function() {
            if (this.value && tanggalMulai.value && this.value < tanggalMulai.value) {
                alert('Tanggal selesai harus setelah tanggal mulai');
                this.value = '';
            }
        });

        // Format target fund input
        const targetDana = document.getElementById('target_dana');
        
        targetDana.addEventListener('input', function(e) {
            // Remove non-numeric characters
            let value = this.value.replace(/[^\d]/g, '');
            
            // Remove leading zeros
            value = value.replace(/^0+/, '');
            
            // Format with thousand separators
            if (value) {
                this.value = parseInt(value).toLocaleString('id-ID');
            } else {
                this.value = '';
            }
        });

        // Prepare form data before submission
        document.getElementById('kampanyeForm').addEventListener('submit', function(e) {
            // Remove thousand separators from target_dana
            if (targetDana.value) {
                const rawValue = targetDana.value.replace(/\./g, '');
                targetDana.value = rawValue;
            }
            
            // Validate target minimum
            const numericValue = parseInt(targetDana.value || '0');
            if (numericValue < 100000) {
                e.preventDefault();
                alert('Target dana minimal Rp 100.000');
                targetDana.focus();
                return false;
            }
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => {
                new bootstrap.Alert(alert).close();
            });
        }, 5000);
    });
</script>
@endpush
@endsection