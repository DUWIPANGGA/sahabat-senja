@extends('layouts.app')

@section('title', 'Tambah Iuran Bulanan')
@section('page-title', 'Tambah Iuran')
@section('icon', 'fas fa-plus-circle')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
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
                <form action="{{ route('admin.iuran.store') }}" method="POST" id="iuranForm">
                    @csrf
                    
                    <div class="card-body">
                        <!-- Informasi Iuran Section -->
                        <div class="mb-5">
                            <h5 class="fw-bold text-dark mb-4">
                                <i class="fas fa-info-circle me-2"></i>Informasi Iuran
                            </h5>
                            
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="nama_iuran" class="form-label fw-bold">
                                        Nama Iuran <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('nama_iuran') is-invalid @enderror" 
                                           id="nama_iuran" name="nama_iuran" value="{{ old('nama_iuran') }}" 
                                           placeholder="Contoh: Iuran Bulanan Januari 2024" required>
                                    @error('nama_iuran')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Berikan nama yang jelas untuk iuran ini</small>
                                </div>
                                
                                <div class="col-md-6 mb-4">
                                    <label for="jumlah" class="form-label fw-bold">
                                        Jumlah Iuran <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" class="form-control @error('jumlah') is-invalid @enderror" 
                                               id="jumlah" name="jumlah" value="{{ old('jumlah') }}" 
                                               placeholder="100000" required>
                                    </div>
                                    @error('jumlah')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Minimum Rp 1.000. Contoh: 100000</small>
                                </div>
                                
                                <div class="col-12 mb-4">
                                    <label for="deskripsi" class="form-label fw-bold">Deskripsi</label>
                                    <textarea class="form-control @error('deskripsi') is-invalid @enderror" 
                                              id="deskripsi" name="deskripsi" rows="3">{{ old('deskripsi') }}</textarea>
                                    @error('deskripsi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Deskripsi opsional untuk iuran ini</small>
                                </div>
                            </div>
                        </div>

                        <!-- Target Iuran Section -->
                        <div class="mb-5">
                            <h5 class="fw-bold text-dark mb-4">
                                <i class="fas fa-bullseye me-2"></i>Target Iuran
                            </h5>
                            
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="datalansia_id" class="form-label fw-bold">
                                        Pilih Lansia <span class="text-danger">*</span>
                                    </label>
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
                                            {{ $lansia->nama_lansia }} ({{ $lansia->nik ?? 'N/A' }})
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('datalansia_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-bold">Informasi Keluarga</label>
                                    <div id="keluargaInfo" class="border rounded p-3">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-users me-3 text-muted"></i>
                                            <div>
                                                <span id="keluargaNama" class="text-muted">Pilih lansia terlebih dahulu</span>
                                                <input type="hidden" id="user_id" name="user_id" value="{{ old('user_id') }}">
                                            </div>
                                        </div>
                                    </div>
                                    <small class="text-muted">Data keluarga akan otomatis terisi</small>
                                </div>
                            </div>
                        </div>

                        <!-- Periode dan Tanggal Section -->
                        <div class="mb-5">
                            <h5 class="fw-bold text-dark mb-4">
                                <i class="fas fa-calendar-alt me-2"></i>Periode dan Tanggal
                            </h5>
                            
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="periode" class="form-label fw-bold">
                                        Periode <span class="text-danger">*</span>
                                    </label>
                                    <input type="month" class="form-control @error('periode') is-invalid @enderror" 
                                           id="periode" name="periode" value="{{ old('periode', date('Y-m')) }}" required>
                                    @error('periode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Format: YYYY-MM (contoh: 2024-01)</small>
                                </div>
                                
                                <div class="col-md-6 mb-4">
                                    <label for="tanggal_jatuh_tempo" class="form-label fw-bold">
                                        Tanggal Jatuh Tempo <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" class="form-control @error('tanggal_jatuh_tempo') is-invalid @enderror" 
                                           id="tanggal_jatuh_tempo" name="tanggal_jatuh_tempo" 
                                           value="{{ old('tanggal_jatuh_tempo', date('Y-m-d', strtotime('+7 days'))) }}" required>
                                    @error('tanggal_jatuh_tempo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Tanggal terakhir untuk membayar iuran</small>
                                </div>
                            </div>
                        </div>

                        <!-- Pengaturan Tambahan Section -->
                        <div class="mb-4">
                            <h5 class="fw-bold text-dark mb-4">
                                <i class="fas fa-cogs me-2"></i>Pengaturan Tambahan
                            </h5>
                            
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" role="switch" 
                                               id="is_otomatis" name="is_otomatis" value="1" 
                                               {{ old('is_otomatis') ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold" for="is_otomatis">
                                            Iuran Otomatis
                                        </label>
                                    </div>
                                    <small class="text-muted">Iuran akan dibuat otomatis setiap bulan</small>
                                    
                                    <div id="otomatisSettings" class="mt-3 border rounded p-3 bg-light {{ old('is_otomatis') ? '' : 'd-none' }}">
                                        <div class="mb-3">
                                            <label for="interval_bulan" class="form-label">Interval (Bulan)</label>
                                            <select class="form-select" id="interval_bulan" name="interval_bulan">
                                                <option value="1" selected>Setiap Bulan</option>
                                                <option value="3">Setiap 3 Bulan</option>
                                                <option value="6">Setiap 6 Bulan</option>
                                                <option value="12">Setiap Tahun</option>
                                            </select>
                                        </div>
                                        <small class="text-muted">Frekuensi pembuatan iuran otomatis</small>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-4">
                                    <label for="catatan_admin" class="form-label fw-bold">Catatan Admin</label>
                                    <textarea class="form-control @error('catatan_admin') is-invalid @enderror" 
                                              id="catatan_admin" name="catatan_admin" rows="4">{{ old('catatan_admin') }}</textarea>
                                    @error('catatan_admin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Catatan internal untuk admin</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Footer -->
                    <div class="card-footer bg-white border-top">
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('admin.iuran.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Simpan Iuran
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
    
    #keluargaInfo {
        border: 1px solid #dee2e6;
        background-color: #f8f9fa;
        transition: all 0.3s ease;
    }
    
    #keluargaInfo.success {
        border-color: #28a745;
        background-color: rgba(40, 167, 69, 0.05);
    }
    
    #keluargaInfo.warning {
        border-color: #ffc107;
        background-color: rgba(255, 193, 7, 0.05);
    }
    
    .border {
        border-width: 1px !important;
    }
    
    @media (max-width: 768px) {
        .row > .col-md-6 {
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
    // Update keluarga info based on selected lansia
    function updateKeluargaInfo() {
        const select = document.getElementById('datalansia_id');
        const selectedOption = select.options[select.selectedIndex];
        const keluargaInfo = document.getElementById('keluargaInfo');
        const userIdInput = document.getElementById('user_id');
        
        if (!selectedOption || selectedOption.value === '') {
            keluargaInfo.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="fas fa-users me-3 text-muted"></i>
                    <div>
                        <span class="text-muted">Pilih lansia terlebih dahulu</span>
                    </div>
                </div>
            `;
            keluargaInfo.className = 'border rounded p-3';
            userIdInput.value = '';
            return;
        }
        
        const userId = selectedOption.getAttribute('data-user-id');
        const userName = selectedOption.getAttribute('data-user-name');
        const userEmail = selectedOption.getAttribute('data-user-email');
        
        if (userId && userId !== '') {
            // Ada keluarga terdaftar
            keluargaInfo.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle me-3 text-success"></i>
                    <div>
                        <strong class="text-success">${userName}</strong>
                        <br>
                        <small class="text-muted">${userEmail}</small>
                    </div>
                </div>
            `;
            keluargaInfo.className = 'border rounded p-3 success';
            userIdInput.value = userId;
        } else {
            // Tidak ada keluarga terdaftar
            keluargaInfo.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle me-3 text-warning"></i>
                    <div>
                        <span class="text-warning">Tidak ada keluarga terdaftar</span>
                        <br>
                        <small class="text-muted">Iuran akan dibuat tanpa penanggung jawab keluarga</small>
                    </div>
                </div>
            `;
            keluargaInfo.className = 'border rounded p-3 warning';
            userIdInput.value = '';
        }
    }

    // Toggle otomatis settings
    const otomatisCheck = document.getElementById('is_otomatis');
    const otomatisSettings = document.getElementById('otomatisSettings');
    
    otomatisCheck.addEventListener('change', function() {
        if (this.checked) {
            otomatisSettings.classList.remove('d-none');
        } else {
            otomatisSettings.classList.add('d-none');
        }
    });

    // Format amount input
    const jumlahInput = document.getElementById('jumlah');
    
    jumlahInput.addEventListener('input', function(e) {
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

    // Set min date for due date
    function setMinDate() {
        const today = new Date().toISOString().split('T')[0];
        const dueDateInput = document.getElementById('tanggal_jatuh_tempo');
        dueDateInput.min = today;
    }

    // Calculate due date based on period
    function setDefaultDueDate() {
        const periodInput = document.getElementById('periode');
        const dueDateInput = document.getElementById('tanggal_jatuh_tempo');
        
        periodInput.addEventListener('change', function() {
            const period = this.value;
            if (period) {
                const [year, month] = period.split('-');
                
                // Default to last day of month
                const lastDay = new Date(year, month, 0).getDate();
                const defaultDate = `${year}-${month}-${lastDay.toString().padStart(2, '0')}`;
                
                // Only set if not already set by user
                if (!dueDateInput.value) {
                    dueDateInput.value = defaultDate;
                }
            }
        });
    }

    // Prepare form for submission
    document.getElementById('iuranForm').addEventListener('submit', function(e) {
        // Remove thousand separators from amount
        if (jumlahInput.value) {
            const rawValue = jumlahInput.value.replace(/\./g, '');
            jumlahInput.value = rawValue;
        }
        
        // Validate minimum amount
        const numericValue = parseInt(jumlahInput.value || '0');
        if (numericValue < 1000) {
            e.preventDefault();
            alert('Jumlah iuran minimal Rp 1.000');
            jumlahInput.focus();
            jumlahInput.value = '';
            return false;
        }
        
        // Validate due date not in past
        const dueDate = new Date(document.getElementById('tanggal_jatuh_tempo').value);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        if (dueDate < today) {
            e.preventDefault();
            alert('Tanggal jatuh tempo tidak boleh di masa lalu');
            document.getElementById('tanggal_jatuh_tempo').focus();
            return false;
        }
    });

    // Initialize on DOM loaded
    document.addEventListener('DOMContentLoaded', function() {
        setMinDate();
        setDefaultDueDate();
        
        // Update keluarga info if there's already a selection
        const datalansiaIdSelect = document.getElementById('datalansia_id');
        if (datalansiaIdSelect.value) {
            updateKeluargaInfo();
        }
        
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