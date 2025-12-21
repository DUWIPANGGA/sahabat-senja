@extends('layouts.app')

@section('title', 'Tambah Data Perawat')
@section('page-title', 'Tambah Data Perawat')
@section('icon', 'fas fa-user-plus')

@section('content')
    <div class="content-container">
        {{-- Alert error --}}
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle me-3 fs-4"></i>
                    <div>
                        <h6 class="mb-1">Terjadi kesalahan!</h6>
                        <ul class="mb-0 ps-3" style="list-style-type: disc;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Form Card --}}
        <div class="card border-0 shadow-lg">
            <div class="card-body p-4">
                <form action="{{ route('admin.DataPerawat.store') }}" method="POST" id="perawatForm">
                    @csrf

                    {{-- Informasi Dasar --}}
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="border-bottom pb-2 mb-3 d-flex align-items-center">
                                <i class="fas fa-id-card me-2 text-primary"></i>
                                Informasi Dasar
                            </h5>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="nama" class="form-label fw-semibold">
                                Nama Lengkap <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-user text-muted"></i>
                                </span>
                                <input type="text" 
                                       name="nama" 
                                       id="nama" 
                                       class="form-control @error('nama') is-invalid @enderror" 
                                       value="{{ old('nama') }}" 
                                       required 
                                       placeholder="Masukkan nama lengkap"
                                       autofocus>
                                @error('nama')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="text-muted">Contoh: Dr. John Doe, S.Kep</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label fw-semibold">
                                Email <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-envelope text-muted"></i>
                                </span>
                                <input type="email" 
                                       name="email" 
                                       id="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       value="{{ old('email') }}" 
                                       required 
                                       placeholder="Masukkan alamat email">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="text-muted">Email aktif untuk notifikasi</small>
                        </div>
                    </div>

                    {{-- Kontak & Jenis Kelamin --}}
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label for="no_hp" class="form-label fw-semibold">
                                No. Handphone <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-phone text-muted"></i>
                                </span>
                                <input type="text" 
                                       name="no_hp" 
                                       id="no_hp" 
                                       class="form-control @error('no_hp') is-invalid @enderror" 
                                       value="{{ old('no_hp') }}" 
                                       required 
                                       placeholder="Masukkan nomor handphone"
                                       maxlength="15">
                                @error('no_hp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="text-muted">Format: 08xxxxxxxxxx</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="jenis_kelamin" class="form-label fw-semibold">
                                Jenis Kelamin <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-venus-mars text-muted"></i>
                                </span>
                                <select name="jenis_kelamin" 
                                        id="jenis_kelamin" 
                                        class="form-select @error('jenis_kelamin') is-invalid @enderror" 
                                        required>
                                    <option value="">-- Pilih Jenis Kelamin --</option>
                                    <option value="Laki-laki" {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>
                                        Laki-laki
                                    </option>
                                    <option value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>
                                        Perempuan
                                    </option>
                                </select>
                                @error('jenis_kelamin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Alamat --}}
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="border-bottom pb-2 mb-3 d-flex align-items-center">
                                <i class="fas fa-map-marker-alt me-2 text-primary"></i>
                                Alamat Lengkap
                            </h5>
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label for="alamat" class="form-label fw-semibold">
                                Alamat <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light align-items-start pt-3">
                                    <i class="fas fa-home text-muted"></i>
                                </span>
                                <textarea name="alamat" 
                                          id="alamat" 
                                          class="form-control @error('alamat') is-invalid @enderror" 
                                          rows="4" 
                                          required 
                                          placeholder="Masukkan alamat lengkap (jalan, RT/RW, kelurahan, kecamatan, kota)">{{ old('alamat') }}</textarea>
                                @error('alamat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="text-muted">Contoh: Jl. Merdeka No. 123, RT 01/RW 02, Kel. Sukajadi, Kec. Bogor Tengah, Kota Bogor</small>
                        </div>
                    </div>

                    {{-- Catatan (Opsional) --}}
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="border-bottom pb-2 mb-3 d-flex align-items-center">
                                <i class="fas fa-sticky-note me-2 text-primary"></i>
                                Informasi Tambahan (Opsional)
                            </h5>
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label for="catatan" class="form-label fw-semibold">
                                Catatan Khusus
                            </label>
                            <textarea name="catatan" 
                                      id="catatan" 
                                      class="form-control" 
                                      rows="3" 
                                      placeholder="Masukkan catatan khusus (jika ada)">{{ old('catatan') }}</textarea>
                            <small class="text-muted">Contoh: Spesialis geriatri, berpengalaman 5 tahun, dll.</small>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="row mt-5">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center border-top pt-4">
                                <div>
                                    <span class="text-muted small">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Tanda <span class="text-danger">*</span> wajib diisi
                                    </span>
                                </div>
                                <div class="d-flex gap-3">
                                    <a href="{{ route('admin.DataPerawat.index') }}" 
                                       class="btn btn-outline-secondary px-4">
                                        <i class="fas fa-times me-2"></i>Batal
                                    </a>
                                    <button type="button" 
                                            class="btn btn-warning px-4" 
                                            onclick="resetForm()">
                                        <i class="fas fa-redo me-2"></i>Reset
                                    </button>
                                    <button type="submit" 
                                            class="btn btn-success px-4" 
                                            id="submitBtn">
                                        <i class="fas fa-save me-2"></i>Simpan Data
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .content-container {
        padding: 1rem;
    }

    .card-header .icon-wrapper {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .form-label {
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: var(--text-dark);
    }
    
    .input-group-text {
        background-color: var(--light-bg);
        border: 1px solid var(--accent-color);
        color: var(--text-light);
    }
    
    .form-control:focus, .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.25rem rgba(139, 115, 85, 0.25);
    }
    
    .is-invalid {
        border-color: #dc3545 !important;
    }
    
    .is-invalid:focus {
        box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25) !important;
    }
    
    .invalid-feedback {
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }
    
    h5 {
        color: var(--dark-brown);
        font-weight: 600;
    }
    
    #submitBtn {
        position: relative;
        overflow: hidden;
    }
    
    #submitBtn .spinner-border {
        display: none;
        width: 1rem;
        height: 1rem;
        margin-right: 0.5rem;
    }
    
    #submitBtn.loading .spinner-border {
        display: inline-block;
    }
    
    @media (max-width: 768px) {
        .content-container {
            padding: 0.5rem;
        }
        
        .breadcrumb {
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
        }
        
        .d-flex.justify-content-between {
            flex-direction: column;
            gap: 1rem;
        }
        
        .d-flex.gap-3 {
            width: 100%;
            justify-content: stretch;
        }
        
        .btn {
            flex: 1;
        }
    }
    
    @media (max-width: 576px) {
        .card-body {
            padding: 1rem !important;
        }
        
        .row.mt-4 {
            margin-top: 1rem !important;
        }
        
        .card {
            margin-bottom: 1rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Format nomor HP
        const noHpInput = document.getElementById('no_hp');
        if (noHpInput) {
            noHpInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.startsWith('0')) {
                    value = value.substring(0, 12);
                } else if (value.startsWith('62')) {
                    value = value.substring(0, 14);
                } else {
                    value = value.substring(0, 15);
                }
                e.target.value = value;
            });
        }
        
        // Validasi email
        const emailInput = document.getElementById('email');
        if (emailInput) {
            emailInput.addEventListener('blur', function() {
                const email = this.value.trim();
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                
                if (email && !emailRegex.test(email)) {
                    if (!this.classList.contains('is-invalid')) {
                        this.classList.add('is-invalid');
                        const feedback = document.createElement('div');
                        feedback.className = 'invalid-feedback';
                        feedback.textContent = 'Format email tidak valid';
                        this.parentNode.appendChild(feedback);
                    }
                } else if (this.classList.contains('is-invalid')) {
                    this.classList.remove('is-invalid');
                    const feedback = this.parentNode.querySelector('.invalid-feedback');
                    if (feedback) {
                        feedback.remove();
                    }
                }
            });
        }
        
        // Submit form dengan loading
        const form = document.getElementById('perawatForm');
        const submitBtn = document.getElementById('submitBtn');
        
        if (form && submitBtn) {
            form.addEventListener('submit', function(e) {
                // Validasi client-side
                const nama = document.getElementById('nama').value.trim();
                const email = document.getElementById('email').value.trim();
                const noHp = document.getElementById('no_hp').value.trim();
                const jenisKelamin = document.getElementById('jenis_kelamin').value;
                const alamat = document.getElementById('alamat').value.trim();
                
                let isValid = true;
                
                // Reset previous validations
                document.querySelectorAll('.is-invalid').forEach(el => {
                    el.classList.remove('is-invalid');
                });
                document.querySelectorAll('.invalid-feedback').forEach(el => {
                    el.remove();
                });
                
                // Validasi nama
                if (!nama) {
                    showError('nama', 'Nama lengkap wajib diisi');
                    isValid = false;
                } else if (nama.length < 3) {
                    showError('nama', 'Nama minimal 3 karakter');
                    isValid = false;
                }
                
                // Validasi email
                if (!email) {
                    showError('email', 'Email wajib diisi');
                    isValid = false;
                } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                    showError('email', 'Format email tidak valid');
                    isValid = false;
                }
                
                // Validasi no HP
                if (!noHp) {
                    showError('no_hp', 'Nomor handphone wajib diisi');
                    isValid = false;
                } else if (!/^\d+$/.test(noHp)) {
                    showError('no_hp', 'Nomor handphone hanya boleh berisi angka');
                    isValid = false;
                } else if (noHp.length < 10 || noHp.length > 15) {
                    showError('no_hp', 'Nomor handphone harus 10-15 digit');
                    isValid = false;
                }
                
                // Validasi jenis kelamin
                if (!jenisKelamin) {
                    showError('jenis_kelamin', 'Jenis kelamin wajib dipilih');
                    isValid = false;
                }
                
                // Validasi alamat
                if (!alamat) {
                    showError('alamat', 'Alamat wajib diisi');
                    isValid = false;
                } else if (alamat.length < 10) {
                    showError('alamat', 'Alamat terlalu pendek, minimal 10 karakter');
                    isValid = false;
                }
                
                if (!isValid) {
                    e.preventDefault();
                    // Scroll to first error
                    const firstError = document.querySelector('.is-invalid');
                    if (firstError) {
                        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                } else {
                    // Show loading
                    submitBtn.classList.add('loading');
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Menyimpan...';
                }
            });
        }
        
        function showError(fieldId, message) {
            const field = document.getElementById(fieldId);
            const parent = field.parentNode;
            
            field.classList.add('is-invalid');
            
            const feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            feedback.textContent = message;
            
            parent.appendChild(feedback);
        }
        
        // Auto-focus nama field
        const namaField = document.getElementById('nama');
        if (namaField && !namaField.value) {
            namaField.focus();
        }
    });
    
    // Reset form
    function resetForm() {
        if (confirm('Apakah Anda yakin ingin mengosongkan semua data yang sudah diisi?')) {
            document.getElementById('perawatForm').reset();
            
            // Remove validation errors
            document.querySelectorAll('.is-invalid').forEach(el => {
                el.classList.remove('is-invalid');
            });
            document.querySelectorAll('.invalid-feedback').forEach(el => {
                el.remove();
            });
            
            // Reset button state
            const submitBtn = document.getElementById('submitBtn');
            if (submitBtn) {
                submitBtn.classList.remove('loading');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Simpan Data';
            }
            
            // Focus on nama field
            document.getElementById('nama').focus();
        }
    }
    
    // Form autosave (optional)
    const form = document.getElementById('perawatForm');
    if (form) {
        const fields = form.querySelectorAll('input, textarea, select');
        const autosaveKey = 'perawat_form_autosave';
        
        // Load saved data
        const savedData = localStorage.getItem(autosaveKey);
        if (savedData) {
            try {
                const data = JSON.parse(savedData);
                fields.forEach(field => {
                    if (data[field.name] && !field.value) {
                        field.value = data[field.name];
                    }
                });
                
                // Ask user if they want to restore
                if (confirm('Ada data yang tersimpan dari sesi sebelumnya. Ingin memulihkannya?')) {
                    // Data already loaded
                } else {
                    localStorage.removeItem(autosaveKey);
                }
            } catch (e) {
                console.error('Error loading autosave:', e);
                localStorage.removeItem(autosaveKey);
            }
        }
        
        // Save on input
        fields.forEach(field => {
            field.addEventListener('input', debounce(() => {
                const formData = {};
                fields.forEach(f => {
                    if (f.name) {
                        formData[f.name] = f.value;
                    }
                });
                localStorage.setItem(autosaveKey, JSON.stringify(formData));
            }, 1000));
        });
        
        // Clear on submit
        form.addEventListener('submit', () => {
            localStorage.removeItem(autosaveKey);
        });
        
        // Clear on page unload if form is submitted
        window.addEventListener('beforeunload', (e) => {
            if (form.checkValidity()) {
                localStorage.removeItem(autosaveKey);
            }
        });
    }
    
    // Debounce function
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
</script>
@endpush