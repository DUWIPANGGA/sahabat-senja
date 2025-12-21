@extends('layouts.app')

@section('title', 'Buat Notifikasi Baru')
@section('page-title', 'Buat Notifikasi')
@section('icon', 'fas fa-bell')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="activity-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0"><i class="fas fa-plus me-2"></i>Form Buat Notifikasi</h3>
                    <a href="{{ route('admin.notifications.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Kembali
                    </a>
                </div>
                
                <form action="{{ route('admin.notifications.store') }}" method="POST" id="notificationForm">
                    @csrf
                    
                    <div class="card-body">
                        <!-- Informasi Dasar -->
                        <div class="mb-4">
                            <h5 class="mb-3" style="color: var(--primary-color);">
                                <i class="fas fa-info-circle me-2"></i>Informasi Dasar
                            </h5>
                            
                            <div class="mb-3">
                                <label for="title" class="form-label">Judul Notifikasi <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       id="title" name="title" value="{{ old('title') }}" 
                                       placeholder="Contoh: Pengingat Pembayaran Iuran" required maxlength="200">
                                @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="message" class="form-label">Isi Pesan <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('message') is-invalid @enderror" 
                                          id="message" name="message" rows="5" 
                                          placeholder="Tulis pesan notifikasi di sini..." 
                                          required maxlength="1000">{{ old('message') }}</textarea>
                                @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="d-flex justify-content-between mt-1">
                                    <small class="text-muted">Maksimal 1000 karakter</small>
                                    <small><span id="charCount">0</span>/1000</small>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="type" class="form-label">Tipe <span class="text-danger">*</span></label>
                                    <select class="form-select @error('type') is-invalid @enderror" 
                                            id="type" name="type" required>
                                        <option value="">Pilih Tipe...</option>
                                        <option value="info" {{ old('type', 'info') == 'info' ? 'selected' : '' }}>Info</option>
                                        <option value="warning" {{ old('type') == 'warning' ? 'selected' : '' }}>Peringatan</option>
                                        <option value="emergency" {{ old('type') == 'emergency' ? 'selected' : '' }}>Darurat</option>
                                        <option value="system" {{ old('type') == 'system' ? 'selected' : '' }}>Sistem</option>
                                    </select>
                                    @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="category" class="form-label">Kategori <span class="text-danger">*</span></label>
                                    <select class="form-select @error('category') is-invalid @enderror" 
                                            id="category" name="category" required>
                                        <option value="">Pilih Kategori...</option>
                                        <option value="kesehatan" {{ old('category') == 'kesehatan' ? 'selected' : '' }}>Kesehatan</option>
                                        <option value="iuran" {{ old('category', 'iuran') == 'iuran' ? 'selected' : '' }}>Iuran</option>
                                        <option value="pengobatan" {{ old('category') == 'pengobatan' ? 'selected' : '' }}>Pengobatan</option>
                                        <option value="administrasi" {{ old('category') == 'administrasi' ? 'selected' : '' }}>Administrasi</option>
                                        <option value="sistem" {{ old('category') == 'sistem' ? 'selected' : '' }}>Sistem</option>
                                    </select>
                                    @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="urgency_level" class="form-label">Tingkat Urgensi</label>
                                    <select class="form-select @error('urgency_level') is-invalid @enderror" 
                                            id="urgency_level" name="urgency_level">
                                        <option value="low" {{ old('urgency_level', 'low') == 'low' ? 'selected' : '' }}>Rendah</option>
                                        <option value="medium" {{ old('urgency_level') == 'medium' ? 'selected' : '' }}>Sedang</option>
                                        <option value="high" {{ old('urgency_level') == 'high' ? 'selected' : '' }}>Tinggi</option>
                                        <option value="critical" {{ old('urgency_level') == 'critical' ? 'selected' : '' }}>Kritis</option>
                                    </select>
                                    @error('urgency_level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="datalansia_id" class="form-label">Kaitkan dengan Lansia (Opsional)</label>
                                    <select class="form-select @error('datalansia_id') is-invalid @enderror" 
                                            id="datalansia_id" name="datalansia_id">
                                        <option value="">-- Pilih Lansia --</option>
                                        @foreach($lansias ?? [] as $lansia)
                                        <option value="{{ $lansia->id }}" {{ old('datalansia_id') == $lansia->id ? 'selected' : '' }}>
                                            {{ $lansia->nama_lansia }} ({{ $lansia->umur_lansia }} tahun)
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('datalansia_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <!-- Target Penerima -->
                        <div class="mb-4">
                            <h5 class="mb-3" style="color: var(--primary-color);">
                                <i class="fas fa-users me-2"></i>Target Penerima
                            </h5>
                            
                            <div class="mb-3">
                                <label class="form-label">Kirim ke:</label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="radio" name="target_type" 
                                                   id="target_specific" value="specific_user" 
                                                   {{ old('target_type', 'specific_user') == 'specific_user' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="target_specific">
                                                <i class="fas fa-user me-1"></i> User Tertentu
                                            </label>
                                        </div>
                                        
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="radio" name="target_type" 
                                                   id="target_family" value="all_family"
                                                   {{ old('target_type') == 'all_family' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="target_family">
                                                <i class="fas fa-users me-1"></i> Semua Keluarga
                                            </label>
                                        </div>
                                        
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="radio" name="target_type" 
                                                   id="target_nurses" value="all_nurses"
                                                   {{ old('target_type') == 'all_nurses' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="target_nurses">
                                                <i class="fas fa-user-md me-1"></i> Semua Perawat
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="radio" name="target_type" 
                                                   id="target_admins" value="all_admins"
                                                   {{ old('target_type') == 'all_admins' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="target_admins">
                                                <i class="fas fa-user-shield me-1"></i> Semua Admin
                                            </label>
                                        </div>
                                        
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="radio" name="target_type" 
                                                   id="target_all" value="all_users"
                                                   {{ old('target_type') == 'all_users' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="target_all">
                                                <i class="fas fa-globe me-1"></i> Semua User
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                @error('target_type')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Pilih User Tertentu -->
                            <div class="mb-3" id="userSelection" style="{{ old('target_type', 'specific_user') == 'specific_user' ? '' : 'display: none;' }}">
                                <label for="user_id" class="form-label">Pilih User <span class="text-danger">*</span></label>
                                <select class="form-select @error('user_id') is-invalid @enderror" 
                                        id="user_id" name="user_id" {{ old('target_type', 'specific_user') == 'specific_user' ? '' : 'disabled' }}>
                                    <option value="">-- Pilih User --</option>
                                    @foreach($users ?? [] as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->role }})
                                    </option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <!-- Pengaturan Tambahan -->
                        <div class="mb-4">
                            <h5 class="mb-3" style="color: var(--primary-color);">
                                <i class="fas fa-cog me-2"></i>Pengaturan Tambahan
                            </h5>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="action_url" class="form-label">URL Tindakan (Opsional)</label>
                                    <input type="text" class="form-control @error('action_url') is-invalid @enderror" 
                                           id="action_url" name="action_url" value="{{ old('action_url') }}" 
                                           placeholder="Contoh: /admin/iuran">
                                    @error('action_url')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="action_text" class="form-label">Teks Tombol (Opsional)</label>
                                    <input type="text" class="form-control @error('action_text') is-invalid @enderror" 
                                           id="action_text" name="action_text" value="{{ old('action_text') }}" 
                                           placeholder="Contoh: Lihat Detail" maxlength="30">
                                    @error('action_text')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="scheduled_at" class="form-label">Jadwalkan Pengiriman</label>
                                    <input type="datetime-local" class="form-control @error('scheduled_at') is-invalid @enderror" 
                                           id="scheduled_at" name="scheduled_at" value="{{ old('scheduled_at') }}">
                                    @error('scheduled_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Kosongkan untuk kirim sekarang</small>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="expires_at" class="form-label">Batas Waktu</label>
                                    <input type="datetime-local" class="form-control @error('expires_at') is-invalid @enderror" 
                                           id="expires_at" name="expires_at" value="{{ old('expires_at') }}">
                                    @error('expires_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Notifikasi akan otomatis diarsipkan setelah waktu ini</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tombol Aksi -->
                    <div class="card-footer bg-white border-top">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.notifications.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-paper-plane me-2"></i>Kirim Notifikasi
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
    .activity-card {
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .card-header {
        background-color: var(--light-bg);
        padding: 15px 20px;
        border-bottom: 1px solid #dee2e6;
    }
    
    .card-body {
        padding: 20px;
    }
    
    .card-footer {
        padding: 15px 20px;
    }
    
    .form-check-input:checked {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }
    
    .form-check-label {
        cursor: pointer;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const messageInput = document.getElementById('message');
        const charCount = document.getElementById('charCount');
        const userSelection = document.getElementById('userSelection');
        const userIdSelect = document.getElementById('user_id');
        const submitBtn = document.getElementById('submitBtn');
        const notificationForm = document.getElementById('notificationForm');
        const scheduledAt = document.getElementById('scheduled_at');
        const expiresAt = document.getElementById('expires_at');
        
        // Initialize character count
        updateCharCount();
        
        // Update character count
        function updateCharCount() {
            const length = messageInput.value.length;
            charCount.textContent = length;
            
            if (length > 1000) {
                charCount.style.color = '#dc3545';
            } else if (length > 800) {
                charCount.style.color = '#ffc107';
            } else {
                charCount.style.color = '#6c757d';
            }
        }
        
        // Event listener for character count
        messageInput.addEventListener('input', updateCharCount);
        
        // Toggle user selection based on target type
        document.querySelectorAll('input[name="target_type"]').forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'specific_user') {
                    userSelection.style.display = 'block';
                    userIdSelect.disabled = false;
                    userIdSelect.required = true;
                } else {
                    userSelection.style.display = 'none';
                    userIdSelect.disabled = true;
                    userIdSelect.required = false;
                    userIdSelect.value = '';
                }
            });
        });
        
        // Initialize user selection state
        const selectedTarget = document.querySelector('input[name="target_type"]:checked');
        if (selectedTarget && selectedTarget.value !== 'specific_user') {
            userIdSelect.disabled = true;
            userIdSelect.required = false;
        }
        
        // Set default times
        function setDefaultTimes() {
            const now = new Date();
            const localDateTime = new Date(now.getTime() - now.getTimezoneOffset() * 60000).toISOString().slice(0, 16);
            
            // Set scheduled_at to 1 hour from now if empty
            if (!scheduledAt.value) {
                const oneHourLater = new Date(now);
                oneHourLater.setHours(oneHourLater.getHours() + 1);
                oneHourLater.setMinutes(0);
                oneHourLater.setSeconds(0);
                scheduledAt.value = oneHourLater.toISOString().slice(0, 16);
            }
            
            // Set expires_at to 24 hours from scheduled if empty
            if (!expiresAt.value && scheduledAt.value) {
                const scheduledTime = new Date(scheduledAt.value);
                const twentyFourHoursLater = new Date(scheduledTime);
                twentyFourHoursLater.setHours(twentyFourHoursLater.getHours() + 24);
                expiresAt.value = twentyFourHoursLater.toISOString().slice(0, 16);
            }
        }
        
        // Auto-update expires_at when scheduled_at changes
        scheduledAt.addEventListener('change', function() {
            if (this.value) {
                const scheduledTime = new Date(this.value);
                const twentyFourHoursLater = new Date(scheduledTime);
                twentyFourHoursLater.setHours(twentyFourHoursLater.getHours() + 24);
                
                // Only update if expires_at is empty or earlier than scheduled
                if (!expiresAt.value || new Date(expiresAt.value) <= scheduledTime) {
                    expiresAt.value = twentyFourHoursLater.toISOString().slice(0, 16);
                }
            }
        });
        
        // Form validation
        notificationForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Clear previous errors
            clearErrors();
            
            let isValid = true;
            
            // Validate required fields
            const title = document.getElementById('title').value.trim();
            const message = document.getElementById('message').value.trim();
            const type = document.getElementById('type').value;
            const category = document.getElementById('category').value;
            const targetType = document.querySelector('input[name="target_type"]:checked');
            
            if (!title) {
                showError('title', 'Judul tidak boleh kosong');
                isValid = false;
            }
            
            if (!message) {
                showError('message', 'Pesan tidak boleh kosong');
                isValid = false;
            }
            
            if (message.length > 1000) {
                showError('message', 'Pesan tidak boleh lebih dari 1000 karakter');
                isValid = false;
            }
            
            if (!type) {
                showError('type', 'Tipe tidak boleh kosong');
                isValid = false;
            }
            
            if (!category) {
                showError('category', 'Kategori tidak boleh kosong');
                isValid = false;
            }
            
            if (!targetType) {
                showError('target_type', 'Pilih target penerima');
                isValid = false;
            } else if (targetType.value === 'specific_user') {
                const userId = document.getElementById('user_id').value;
                if (!userId) {
                    showError('user_id', 'User harus dipilih');
                    isValid = false;
                }
            }
            
            // Validate datetime
            if (scheduledAt.value && expiresAt.value) {
                const scheduleDate = new Date(scheduledAt.value);
                const expireDate = new Date(expiresAt.value);
                
                if (expireDate <= scheduleDate) {
                    showError('expires_at', 'Waktu kadaluarsa harus setelah waktu penjadwalan');
                    isValid = false;
                }
            }
            
            // Validate scheduled time not in past
            if (scheduledAt.value) {
                const scheduleDate = new Date(scheduledAt.value);
                const now = new Date();
                if (scheduleDate < now) {
                    showError('scheduled_at', 'Waktu penjadwalan tidak boleh di masa lalu');
                    isValid = false;
                }
            }
            
            // If emergency or high urgency, show confirmation
            const urgency = document.getElementById('urgency_level').value;
            const isEmergency = document.getElementById('type').value === 'emergency';
            
            if (isValid) {
                if (isEmergency || urgency === 'critical' || urgency === 'high') {
                    const confirmationMessage = isEmergency 
                        ? '⚠️ PERHATIAN!\n\nAnda akan mengirim notifikasi DARURAT.\nNotifikasi ini akan dikirim dengan prioritas tertinggi.\n\nYakin ingin melanjutkan?'
                        : '⚠️ PERHATIAN!\n\nAnda akan mengirim notifikasi dengan prioritas tinggi.\n\nYakin ingin melanjutkan?';
                    
                    if (confirm(confirmationMessage)) {
                        // Show loading state
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mengirim...';
                        submitBtn.disabled = true;
                        
                        // Submit form
                        this.submit();
                    }
                } else {
                    // Show loading state
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mengirim...';
                    submitBtn.disabled = true;
                    
                    // Submit form
                    this.submit();
                }
            }
        });
        
        // Helper function to show error
        function showError(fieldId, message) {
            const field = document.getElementById(fieldId);
            const feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            feedback.textContent = message;
            
            field.classList.add('is-invalid');
            
            // Remove existing feedback
            const existingFeedback = field.nextElementSibling;
            if (existingFeedback && existingFeedback.classList.contains('invalid-feedback')) {
                existingFeedback.remove();
            }
            
            field.parentNode.insertBefore(feedback, field.nextSibling);
        }
        
        // Helper function to clear errors
        function clearErrors() {
            document.querySelectorAll('.is-invalid').forEach(field => {
                field.classList.remove('is-invalid');
                const feedback = field.nextElementSibling;
                if (feedback && feedback.classList.contains('invalid-feedback')) {
                    feedback.remove();
                }
            });
        }
        
        // Initialize default times
        setDefaultTimes();
        
        // Add real-time validation
        messageInput.addEventListener('blur', function() {
            if (this.value.length > 1000) {
                showError('message', 'Pesan tidak boleh lebih dari 1000 karakter');
            }
        });
        
        document.getElementById('title').addEventListener('blur', function() {
            if (!this.value.trim()) {
                showError('title', 'Judul tidak boleh kosong');
            }
        });
    });
</script>
@endpush
@endsection