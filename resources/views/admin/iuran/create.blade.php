@extends('layouts.app')

@section('title', 'Tambah Iuran Bulanan')
@section('page-title', 'Tambah Iuran')
@section('icon', 'fas fa-plus-circle')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="activity-card">
                <div class="card-header">
                    <h3><i class="fas fa-money-bill-wave me-2"></i>Form Tambah Iuran</h3>
                    <a href="{{ route('admin.iuran.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Kembali
                    </a>
                </div>
                
                <div class="card-body">
                    <form action="{{ route('admin.iuran.store') }}" method="POST" id="iuranForm">
                        @csrf
                        
                        <div class="row">
                            <!-- Informasi Iuran -->
                            <div class="col-md-12">
                                <h5 class="mb-3" style="color: var(--primary-color);">
                                    <i class="fas fa-info-circle me-2"></i>Informasi Iuran
                                </h5>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="nama_iuran" class="form-label">Nama Iuran <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nama_iuran') is-invalid @enderror" 
                                       id="nama_iuran" name="nama_iuran" value="{{ old('nama_iuran') }}" 
                                       placeholder="Contoh: Iuran Bulanan Januari 2024" required>
                                @error('nama_iuran')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="jumlah" class="form-label">Jumlah Iuran <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control @error('jumlah') is-invalid @enderror" 
                                           id="jumlah" name="jumlah" value="{{ old('jumlah') }}" 
                                           min="1000" step="1000" required>
                                    @error('jumlah')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="text-muted">Minimum Rp 1.000</small>
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label for="deskripsi" class="form-label">Deskripsi (Opsional)</label>
                                <textarea class="form-control @error('deskripsi') is-invalid @enderror" 
                                          id="deskripsi" name="deskripsi" rows="2">{{ old('deskripsi') }}</textarea>
                                @error('deskripsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="row">
                            <!-- Target Iuran -->
                            <div class="col-md-12">
                                <h5 class="mb-3" style="color: var(--primary-color);">
                                    <i class="fas fa-bullseye me-2"></i>Target Iuran
                                </h5>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="datalansia_id" class="form-label">Pilih Lansia <span class="text-danger">*</span></label>
                                <select class="form-select @error('datalansia_id') is-invalid @enderror" 
                                        id="datalansia_id" name="datalansia_id" required
                                        onchange="updateKeluargaInfo()">
                                    <option value="">Pilih Lansia...</option>
                                    @foreach($lansias as $lansia)
                                    <option value="{{ $lansia->id }}" 
                                            data-user-id="{{ $lansia->user_id ?? '' }}"
                                            data-user-name="{{ $lansia->user->name ?? 'Tidak ada keluarga' }}"
                                            data-user-email="{{ $lansia->user->email ?? '' }}"
                                            {{ old('datalansia_id') == $lansia->id ? 'selected' : '' }}>
                                        {{ $lansia->nama_lansia }} - {{ $lansia->user->name ?? 'Tidak ada keluarga' }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('datalansia_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Keluarga Terkait</label>
                                <div id="keluargaInfo" class="card p-3 bg-light">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-users me-2 text-muted"></i>
                                        <div>
                                            <span id="keluargaNama" class="text-muted">Pilih lansia terlebih dahulu</span>
                                            <input type="hidden" id="user_id" name="user_id" value="{{ old('user_id') }}">
                                        </div>
                                    </div>
                                </div>
                                <small class="text-muted">Keluarga akan otomatis terisi berdasarkan data lansia</small>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="row">
                            <!-- Periode dan Tanggal -->
                            <div class="col-md-12">
                                <h5 class="mb-3" style="color: var(--primary-color);">
                                    <i class="fas fa-calendar-alt me-2"></i>Periode dan Tanggal
                                </h5>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="periode" class="form-label">Periode <span class="text-danger">*</span></label>
                                <input type="month" class="form-control @error('periode') is-invalid @enderror" 
                                       id="periode" name="periode" value="{{ old('periode', date('Y-m')) }}" required>
                                @error('periode')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Format: YYYY-MM (contoh: 2024-01)</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="tanggal_jatuh_tempo" class="form-label">Tanggal Jatuh Tempo <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('tanggal_jatuh_tempo') is-invalid @enderror" 
                                       id="tanggal_jatuh_tempo" name="tanggal_jatuh_tempo" 
                                       value="{{ old('tanggal_jatuh_tempo', date('Y-m-d', strtotime('+7 days'))) }}" required>
                                @error('tanggal_jatuh_tempo')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="row">
                            <!-- Pengaturan Tambahan -->
                            <div class="col-md-12">
                                <h5 class="mb-3" style="color: var(--primary-color);">
                                    <i class="fas fa-cogs me-2"></i>Pengaturan Tambahan
                                </h5>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" 
                                           id="is_otomatis" name="is_otomatis" value="1" 
                                           {{ old('is_otomatis') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_otomatis">Iuran Otomatis</label>
                                    <small class="d-block text-muted">Iuran akan dibuat otomatis setiap bulan</small>
                                </div>
                                
                                <div id="otomatisSettings" class="mt-3 d-none">
                                    <div class="mb-3">
                                        <label for="interval_bulan" class="form-label">Interval (Bulan)</label>
                                        <select class="form-select" id="interval_bulan" name="interval_bulan">
                                            <option value="1" selected>Setiap Bulan</option>
                                            <option value="3">Setiap 3 Bulan</option>
                                            <option value="6">Setiap 6 Bulan</option>
                                            <option value="12">Setiap Tahun</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="catatan_admin" class="form-label">Catatan Admin (Opsional)</label>
                                <textarea class="form-control @error('catatan_admin') is-invalid @enderror" 
                                          id="catatan_admin" name="catatan_admin" rows="3">{{ old('catatan_admin') }}</textarea>
                                @error('catatan_admin')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            <a href="{{ route('admin.iuran.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-primary" style="background-color: var(--primary-color);">
                                <i class="fas fa-save me-2"></i>Simpan Iuran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function updateKeluargaInfo() {
        const select = document.getElementById('datalansia_id');
        const selectedOption = select.options[select.selectedIndex];
        const keluargaInfo = document.getElementById('keluargaInfo');
        const keluargaNama = document.getElementById('keluargaNama');
        const userIdInput = document.getElementById('user_id');
        
        if (selectedOption.value === '') {
            keluargaNama.textContent = 'Pilih lansia terlebih dahulu';
            keluargaInfo.classList.remove('border-success', 'border-warning');
            userIdInput.value = '';
            return;
        }
        
        const userId = selectedOption.getAttribute('data-user-id');
        const userName = selectedOption.getAttribute('data-user-name');
        const userEmail = selectedOption.getAttribute('data-user-email');
        
        if (userId && userId !== '') {
            // Ada keluarga
            keluargaNama.textContent = `${userName} (${userEmail})`;
            keluargaInfo.classList.remove('border-warning');
            keluargaInfo.classList.add('border-success');
            userIdInput.value = userId;
            
            // Tambah ikon check
            keluargaInfo.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle me-2 text-success"></i>
                    <div>
                        <strong>${userName}</strong>
                        <br>
                        <small class="text-muted">${userEmail}</small>
                    </div>
                </div>
            `;
        } else {
            // Tidak ada keluarga
            keluargaNama.textContent = 'Tidak ada keluarga terdaftar';
            keluargaInfo.classList.remove('border-success');
            keluargaInfo.classList.add('border-warning');
            userIdInput.value = '';
            
            // Tambah ikon warning
            keluargaInfo.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle me-2 text-warning"></i>
                    <div>
                        <span class="text-warning">Tidak ada keluarga terdaftar</span>
                        <br>
                        <small class="text-muted">Iuran akan dibuat tanpa penanggung jawab keluarga</small>
                    </div>
                </div>
            `;
        }
    }
    
    // Toggle pengaturan otomatis
    document.getElementById('is_otomatis').addEventListener('change', function() {
        const settings = document.getElementById('otomatisSettings');
        if (this.checked) {
            settings.classList.remove('d-none');
        } else {
            settings.classList.add('d-none');
        }
    });
    
    // Inisialisasi
    document.addEventListener('DOMContentLoaded', function() {
        // Set min date untuk tanggal jatuh tempo
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('tanggal_jatuh_tempo').min = today;
        
        // Inisialisasi toggle otomatis
        const otomatisCheck = document.getElementById('is_otomatis');
        const otomatisSettings = document.getElementById('otomatisSettings');
        if (otomatisCheck.checked) {
            otomatisSettings.classList.remove('d-none');
        }
        
        // Update keluarga info jika sudah ada data dari old input
        const datalansiaIdSelect = document.getElementById('datalansia_id');
        if (datalansiaIdSelect.value) {
            updateKeluargaInfo();
        }
        
        // Format tanggal jatuh tempo otomatis berdasarkan periode
        document.getElementById('periode').addEventListener('change', function() {
            const periode = this.value;
            if (periode) {
                const [year, month] = periode.split('-');
                const tanggalJatuhTempo = document.getElementById('tanggal_jatuh_tempo');
                
                // Set default tanggal jatuh tempo: akhir bulan
                const lastDay = new Date(year, month, 0).getDate();
                tanggalJatuhTempo.value = `${year}-${month}-${lastDay}`;
            }
        });
    });
</script>

<style>
    #keluargaInfo {
        border: 1px dashed #dee2e6;
        transition: all 0.3s ease;
    }
    
    #keluargaInfo.border-success {
        border: 1px solid #28a745;
        background-color: rgba(40, 167, 69, 0.05);
    }
    
    #keluargaInfo.border-warning {
        border: 1px solid #ffc107;
        background-color: rgba(255, 193, 7, 0.05);
    }
</style>
@endpush
@endsection