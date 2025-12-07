@extends('layouts.app')

@section('title', 'Tambah Kampanye Donasi')
@section('page-title', 'Tambah Kampanye Donasi')
@section('icon', 'fas fa-plus-circle')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="activity-card">
                <div class="card-header">
                    <h3><i class="fas fa-hand-holding-heart me-2"></i>Form Tambah Kampanye Donasi</h3>
                    <a href="{{ route('admin.kampanye.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Kembali
                    </a>
                </div>
                @if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
                <form action="{{ route('admin.kampanye.store') }}" method="POST" enctype="multipart/form-data" id="kampanyeForm">
                    @csrf
                    
                    <div class="row">
                        <!-- Kolom Kiri -->
                        <div class="col-md-8">
                            <!-- Judul Kampanye -->
                            <div class="mb-3">
                                <label for="judul" class="form-label">Judul Kampanye <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('judul') is-invalid @enderror" 
                                       id="judul" name="judul" value="{{ old('judul') }}" 
                                       placeholder="Contoh: Bantu Operasi Katarak Nenek Sumirah" required>
                                @error('judul')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Buat judul yang menarik dan jelas (maks. 255 karakter)</small>
                            </div>

                            <!-- Deskripsi Singkat -->
                            <div class="mb-3">
                                <label for="deskripsi_singkat" class="form-label">Deskripsi Singkat <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('deskripsi_singkat') is-invalid @enderror" 
                                          id="deskripsi_singkat" name="deskripsi_singkat" 
                                          rows="3" maxlength="500" required>{{ old('deskripsi_singkat') }}</textarea>
                                @error('deskripsi_singkat')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Deskripsi singkat yang muncul di halaman utama (maks. 500 karakter)</small>
                                <div id="deskripsi_singkat_counter" class="text-end text-muted mt-1">0/500</div>
                            </div>

                            <!-- Deskripsi Lengkap -->
                            <div class="mb-3">
                                <label for="deskripsi" class="form-label">Deskripsi Lengkap <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('deskripsi') is-invalid @enderror" 
                                          id="deskripsi" name="deskripsi" rows="5" required>{{ old('deskripsi') }}</textarea>
                                @error('deskripsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Ceritakan detail kampanye, latar belakang, dan tujuan</small>
                            </div>

                            <!-- Cerita Lengkap -->
                            <div class="mb-3">
                                <label for="cerita_lengkap" class="form-label">Cerita Lengkap (Opsional)</label>
                                <textarea class="form-control @error('cerita_lengkap') is-invalid @enderror" 
                                          id="cerita_lengkap" name="cerita_lengkap" rows="8">{{ old('cerita_lengkap') }}</textarea>
                                @error('cerita_lengkap')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Cerita lebih detail tentang penerima manfaat</small>
                            </div>

                            <!-- Pesan Terima Kasih -->
                            <div class="mb-3">
                                <label for="terima_kasih_pesan" class="form-label">Pesan Terima Kasih</label>
                                <textarea class="form-control @error('terima_kasih_pesan') is-invalid @enderror" 
                                          id="terima_kasih_pesan" name="terima_kasih_pesan" 
                                          rows="3">{{ old('terima_kasih_pesan') }}</textarea>
                                @error('terima_kasih_pesan')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Pesan yang akan ditampilkan setelah donasi berhasil</small>
                            </div>
                        </div>

                        <!-- Kolom Kanan -->
                        <div class="col-md-4">
                            <!-- Status -->
                            <div class="mb-3">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
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

                            <!-- Kategori -->
                            <div class="mb-3">
                                <label for="kategori" class="form-label">Kategori <span class="text-danger">*</span></label>
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

                            <!-- Data Lansia -->
                            <div class="mb-3">
                                <label for="datalansia_id" class="form-label">Penerima Manfaat (Lansia)</label>
                                <select class="form-select @error('datalansia_id') is-invalid @enderror" 
                                        id="datalansia_id" name="datalansia_id">
                                    <option value="">Pilih Lansia (Opsional)</option>
                                    @foreach($datalansia as $lansia)
                                    <option value="{{ $lansia->id }}" {{ old('datalansia_id') == $lansia->id ? 'selected' : '' }}>
                                        {{ $lansia->nama }} ({{ $lansia->nik }})
                                    </option>
                                    @endforeach
                                </select>
                                @error('datalansia_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Pilih jika kampanye untuk lansia tertentu</small>
                            </div>

                            <!-- Target Dana -->
                   <div class="mb-3">
    <label for="target_dana" class="form-label">Target Dana <span class="text-danger">*</span></label>
    <div class="input-group">
        <span class="input-group-text">Rp</span>

        <input type="text" 
            class="form-control @error('target_dana') is-invalid @enderror"
            id="target_dana"
            name="target_dana"
            value="{{ old('target_dana') }}"
            inputmode="numeric"
            placeholder="100000"
            required>

        @error('target_dana')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <small class="text-muted">Minimal Rp 100.000</small>
</div>

                            <!-- Tanggal Mulai & Selesai -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="tanggal_mulai" class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('tanggal_mulai') is-invalid @enderror" 
                                           id="tanggal_mulai" name="tanggal_mulai" 
                                           value="{{ old('tanggal_mulai', date('Y-m-d')) }}" required>
                                    @error('tanggal_mulai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="tanggal_selesai" class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('tanggal_selesai') is-invalid @enderror" 
                                           id="tanggal_selesai" name="tanggal_selesai" 
                                           value="{{ old('tanggal_selesai', date('Y-m-d', strtotime('+30 days'))) }}" required>
                                    @error('tanggal_selesai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Featured -->
                            <div class="mb-3 form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" 
                                       id="is_featured" name="is_featured" value="1" 
                                       {{ old('is_featured') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_featured">Tampilkan sebagai Featured</label>
                                <small class="d-block text-muted">Kampanye akan ditampilkan di halaman utama</small>
                            </div>

                            <!-- Upload Gambar Utama -->
                            <div class="mb-3">
                                <label for="gambar" class="form-label">Gambar Utama</label>
                                <input type="file" class="form-control @error('gambar') is-invalid @enderror" 
                                       id="gambar" name="gambar" accept="image/*">
                                @error('gambar')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Ukuran maksimal 2MB. Format: JPG, PNG, GIF</small>
                                <div id="gambar_preview" class="mt-2 d-none">
                                    <img src="" alt="Preview" class="img-fluid rounded" style="max-height: 200px;">
                                </div>
                            </div>

                            <!-- Upload Thumbnail -->
                            <div class="mb-3">
                                <label for="thumbnail" class="form-label">Thumbnail</label>
                                <input type="file" class="form-control @error('thumbnail') is-invalid @enderror" 
                                       id="thumbnail" name="thumbnail" accept="image/*">
                                @error('thumbnail')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Ukuran maksimal 1MB. Akan ditampilkan di daftar kampanye</small>
                                <div id="thumbnail_preview" class="mt-2 d-none">
                                    <img src="" alt="Preview" class="img-fluid rounded" style="max-height: 150px;">
                                </div>
                            </div>

                            <!-- Upload Galeri -->
                            <div class="mb-4">
                                <label for="galeri" class="form-label">Galeri Gambar</label>
                                <input type="file" class="form-control @error('galeri') is-invalid @enderror" 
                                       id="galeri" name="galeri[]" accept="image/*" multiple>
                                @error('galeri')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Pilih beberapa gambar untuk galeri (maks. 5 gambar, 2MB per gambar)</small>
                                <div id="galeri_preview" class="row mt-2 g-2"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <a href="{{ route('admin.kampanye.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Batal
                        </a>
                        <button type="submit" class="btn btn-primary" style="background-color: var(--primary-color);">
                            <i class="fas fa-save me-2"></i>Simpan Kampanye
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Karakter counter untuk deskripsi singkat
        const descInput = document.getElementById('deskripsi_singkat');
        const counter = document.getElementById('deskripsi_singkat_counter');
        
        descInput.addEventListener('input', function() {
            counter.textContent = `${this.value.length}/500`;
        });
        
        // Inisialisasi counter
        counter.textContent = `${descInput.value.length}/500`;

        // Preview gambar utama
        document.getElementById('gambar').addEventListener('change', function(e) {
            const preview = document.getElementById('gambar_preview');
            const file = e.target.files[0];
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.querySelector('img').src = e.target.result;
                    preview.classList.remove('d-none');
                };
                reader.readAsDataURL(file);
            } else {
                preview.classList.add('d-none');
            }
        });

        // Preview thumbnail
        document.getElementById('thumbnail').addEventListener('change', function(e) {
            const preview = document.getElementById('thumbnail_preview');
            const file = e.target.files[0];
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.querySelector('img').src = e.target.result;
                    preview.classList.remove('d-none');
                };
                reader.readAsDataURL(file);
            } else {
                preview.classList.add('d-none');
            }
        });

        // Preview galeri
        document.getElementById('galeri').addEventListener('change', function(e) {
            const previewContainer = document.getElementById('galeri_preview');
            previewContainer.innerHTML = '';
            
            const files = Array.from(e.target.files);
            files.slice(0, 5).forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const col = document.createElement('div');
                    col.className = 'col-6 col-md-4';
                    col.innerHTML = `
                        <div class="position-relative">
                            <img src="${e.target.result}" alt="Galeri ${index + 1}" 
                                 class="img-fluid rounded" style="height: 100px; object-fit: cover;">
                            <small class="position-absolute bottom-0 start-0 bg-dark text-white px-2 rounded-end">
                                ${index + 1}
                            </small>
                        </div>
                    `;
                    previewContainer.appendChild(col);
                };
                reader.readAsDataURL(file);
            });
        });

        // Validasi tanggal
        const tanggalMulai = document.getElementById('tanggal_mulai');
        const tanggalSelesai = document.getElementById('tanggal_selesai');
        
        tanggalMulai.addEventListener('change', function() {
            tanggalSelesai.min = this.value;
        });
        
        tanggalSelesai.addEventListener('change', function() {
            if (this.value < tanggalMulai.value) {
                alert('Tanggal selesai harus setelah tanggal mulai');
                this.value = '';
            }
        });

        // Format input target dana
        // const targetDana = document.getElementById('target_dana');
        // targetDana.addEventListener('blur', function() {
        //     this.value = parseInt(this.value).toLocaleString('id-ID');
        // });
        
        targetDana.addEventListener('focus', function() {
            this.value = this.value.replace(/\./g, '');
        });
    });
</script>
@endpush
@endsection