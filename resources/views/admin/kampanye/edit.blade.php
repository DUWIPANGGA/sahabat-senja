@extends('layouts.app')

@section('title', 'Edit Kampanye Donasi')
@section('page-title', 'Edit Kampanye Donasi')
@section('icon', 'fas fa-edit')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="activity-card">
                <div class="card-header">
                    <h3><i class="fas fa-hand-holding-heart me-2"></i>Edit Kampanye: {{ $kampanye->judul }}</h3>
                    <a href="{{ route('admin.kampanye.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Kembali
                    </a>
                </div>
                
                <form action="{{ route('admin.kampanye.update', $kampanye) }}" method="POST" enctype="multipart/form-data" id="kampanyeForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <!-- Kolom Kiri -->
                        <div class="col-md-8">
                            <!-- Judul Kampanye -->
                            <div class="mb-3">
                                <label for="judul" class="form-label">Judul Kampanye <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('judul') is-invalid @enderror" 
                                       id="judul" name="judul" value="{{ old('judul', $kampanye->judul) }}" required>
                                @error('judul')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Deskripsi Singkat -->
                            <div class="mb-3">
                                <label for="deskripsi_singkat" class="form-label">Deskripsi Singkat <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('deskripsi_singkat') is-invalid @enderror" 
                                          id="deskripsi_singkat" name="deskripsi_singkat" 
                                          rows="3" maxlength="500" required>{{ old('deskripsi_singkat', $kampanye->deskripsi_singkat) }}</textarea>
                                @error('deskripsi_singkat')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div id="deskripsi_singkat_counter" class="text-end text-muted mt-1">
                                    {{ strlen($kampanye->deskripsi_singkat) }}/500
                                </div>
                            </div>

                            <!-- Deskripsi Lengkap -->
                            <div class="mb-3">
                                <label for="deskripsi" class="form-label">Deskripsi Lengkap <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('deskripsi') is-invalid @enderror" 
                                          id="deskripsi" name="deskripsi" rows="5" required>{{ old('deskripsi', $kampanye->deskripsi) }}</textarea>
                                @error('deskripsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Cerita Lengkap -->
                            <div class="mb-3">
                                <label for="cerita_lengkap" class="form-label">Cerita Lengkap (Opsional)</label>
                                <textarea class="form-control @error('cerita_lengkap') is-invalid @enderror" 
                                          id="cerita_lengkap" name="cerita_lengkap" rows="8">{{ old('cerita_lengkap', $kampanye->cerita_lengkap) }}</textarea>
                                @error('cerita_lengkap')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Pesan Terima Kasih -->
                            <div class="mb-3">
                                <label for="terima_kasih_pesan" class="form-label">Pesan Terima Kasih</label>
                                <textarea class="form-control @error('terima_kasih_pesan') is-invalid @enderror" 
                                          id="terima_kasih_pesan" name="terima_kasih_pesan" 
                                          rows="3">{{ old('terima_kasih_pesan', $kampanye->terima_kasih_pesan) }}</textarea>
                                @error('terima_kasih_pesan')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Kolom Kanan -->
                        <div class="col-md-4">
                            <!-- Status -->
                            <div class="mb-3">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" 
                                        id="status" name="status" required>
                                    <option value="draft" {{ old('status', $kampanye->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="aktif" {{ old('status', $kampanye->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="selesai" {{ old('status', $kampanye->status) == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                    <option value="ditutup" {{ old('status', $kampanye->status) == 'ditutup' ? 'selected' : '' }}>Ditutup</option>
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
                                    <option value="lansia" {{ old('kategori', $kampanye->kategori) == 'lansia' ? 'selected' : '' }}>Lansia</option>
                                    <option value="kesehatan" {{ old('kategori', $kampanye->kategori) == 'kesehatan' ? 'selected' : '' }}>Kesehatan</option>
                                    <option value="pendidikan" {{ old('kategori', $kampanye->kategori) == 'pendidikan' ? 'selected' : '' }}>Pendidikan</option>
                                    <option value="bencana" {{ old('kategori', $kampanye->kategori) == 'bencana' ? 'selected' : '' }}>Bencana</option>
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
                                    <option value="{{ $lansia->id }}" {{ old('datalansia_id', $kampanye->datalansia_id) == $lansia->id ? 'selected' : '' }}>
                                        {{ $lansia->nama }} ({{ $lansia->nik }})
                                    </option>
                                    @endforeach
                                </select>
                                @error('datalansia_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Target Dana -->
                            <div class="mb-3">
                                <label for="target_dana" class="form-label">Target Dana <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control @error('target_dana') is-invalid @enderror" 
                                           id="target_dana" name="target_dana" 
                                           value="{{ old('target_dana', $kampanye->target_dana) }}" min="100000" required>
                                    @error('target_dana')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Tanggal -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="tanggal_mulai" class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('tanggal_mulai') is-invalid @enderror" 
                                           id="tanggal_mulai" name="tanggal_mulai" 
                                           value="{{ old('tanggal_mulai', $kampanye->tanggal_mulai->format('Y-m-d')) }}" required>
                                    @error('tanggal_mulai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="tanggal_selesai" class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('tanggal_selesai') is-invalid @enderror" 
                                           id="tanggal_selesai" name="tanggal_selesai" 
                                           value="{{ old('tanggal_selesai', $kampanye->tanggal_selesai->format('Y-m-d')) }}" required>
                                    @error('tanggal_selesai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Featured -->
                            <div class="mb-3 form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" 
                                       id="is_featured" name="is_featured" value="1" 
                                       {{ old('is_featured', $kampanye->is_featured) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_featured">Featured</label>
                            </div>

                            <!-- Gambar Saat Ini -->
                            <div class="mb-3">
                                <label class="form-label d-block">Gambar Utama Saat Ini</label>
                                @if($kampanye->gambar)
                                <div class="position-relative mb-2">
                                    <img src="{{ asset('storage/' . $kampanye->gambar) }}" 
                                         alt="Gambar Saat Ini" class="img-fluid rounded" style="max-height: 150px;">
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" 
                                               id="hapus_gambar" name="hapus_gambar" value="1">
                                        <label class="form-check-label text-danger" for="hapus_gambar">
                                            Hapus gambar ini
                                        </label>
                                    </div>
                                </div>
                                @else
                                <div class="alert alert-warning py-2">
                                    <i class="fas fa-exclamation-triangle me-2"></i>Belum ada gambar
                                </div>
                                @endif
                                <input type="file" class="form-control mt-2" id="gambar" name="gambar" accept="image/*">
                                <small class="text-muted">Kosongkan jika tidak ingin mengubah</small>
                            </div>

                            <!-- Thumbnail Saat Ini -->
                            <div class="mb-3">
                                <label class="form-label d-block">Thumbnail Saat Ini</label>
                                @if($kampanye->thumbnail)
                                <div class="position-relative mb-2">
                                    <img src="{{ asset('storage/' . $kampanye->thumbnail) }}" 
                                         alt="Thumbnail Saat Ini" class="img-fluid rounded" style="max-height: 100px;">
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" 
                                               id="hapus_thumbnail" name="hapus_thumbnail" value="1">
                                        <label class="form-check-label text-danger" for="hapus_thumbnail">
                                            Hapus thumbnail ini
                                        </label>
                                    </div>
                                </div>
                                @else
                                <div class="alert alert-warning py-2">
                                    <i class="fas fa-exclamation-triangle me-2"></i>Belum ada thumbnail
                                </div>
                                @endif
                                <input type="file" class="form-control mt-2" id="thumbnail" name="thumbnail" accept="image/*">
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <a href="{{ route('admin.kampanye.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Batal
                        </a>
                        <div>
                            <a href="{{ route('admin.kampanye.show', $kampanye) }}" class="btn btn-info me-2">
                                <i class="fas fa-eye me-2"></i>Lihat
                            </a>
                            <button type="submit" class="btn btn-primary" style="background-color: var(--primary-color);">
                                <i class="fas fa-save me-2"></i>Update Kampanye
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Karakter counter
        const descInput = document.getElementById('deskripsi_singkat');
        const counter = document.getElementById('deskripsi_singkat_counter');
        
        descInput.addEventListener('input', function() {
            counter.textContent = `${this.value.length}/500`;
        });

        // Preview gambar baru
        document.getElementById('gambar').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    alert('Gambar baru telah dipilih. Gambar lama akan diganti setelah disimpan.');
                };
                reader.readAsDataURL(file);
            }
        });
    });
</script>
@endpush
@endsection