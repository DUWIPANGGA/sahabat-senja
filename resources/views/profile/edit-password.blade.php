@extends('layouts.app')

@section('title', 'Ganti Password')
@section('page-title', 'Ganti Password')
@section('icon', 'fas fa-key')

@section('content')
    <div class="content-container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="mb-0"><i class="fas fa-key me-2"></i>Ganti Password</h5>
                            <a href="{{ route('profile.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-arrow-left me-1"></i>Kembali ke Profile
                            </a>
                        </div>
                    </div>
                    
                    <div class="card-body p-4">
                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <strong>Terdapat kesalahan:</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form action="{{ route('profile.update-password') }}" method="POST" id="passwordForm">
                            @csrf
                            @method('PUT')
                            
                            <div class="row g-4">
                                <!-- Password Saat Ini -->
                                <div class="col-12">
                                    <div class="mb-4">
                                        <label for="current_password" class="form-label">
                                            Password Saat Ini <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <input type="password" 
                                                   class="form-control py-2 px-3" 
                                                   id="current_password" 
                                                   name="current_password" 
                                                   required>
                                            <button type="button" 
                                                    class="input-group-text bg-light" 
                                                    onclick="togglePassword('current_password', this)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                        @error('current_password')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Password Baru -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="password" class="form-label">
                                            Password Baru <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <input type="password" 
                                                   class="form-control py-2 px-3" 
                                                   id="password" 
                                                   name="password" 
                                                   required
                                                   minlength="8">
                                            <button type="button" 
                                                    class="input-group-text bg-light" 
                                                    onclick="togglePassword('password', this)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                        <div class="password-requirements mt-2">
                                            <small class="text-muted">Password harus:</small>
                                            <ul class="list-unstyled mt-1 small">
                                                <li id="length" class="text-danger">
                                                    <i class="fas fa-times me-1"></i>Minimal 8 karakter
                                                </li>
                                                <li id="uppercase" class="text-danger">
                                                    <i class="fas fa-times me-1"></i>Mengandung huruf besar
                                                </li>
                                                <li id="lowercase" class="text-danger">
                                                    <i class="fas fa-times me-1"></i>Mengandung huruf kecil
                                                </li>
                                                <li id="number" class="text-danger">
                                                    <i class="fas fa-times me-1"></i>Mengandung angka
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <!-- Konfirmasi Password -->
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="password_confirmation" class="form-label">
                                            Konfirmasi Password Baru <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <input type="password" 
                                                   class="form-control py-2 px-3" 
                                                   id="password_confirmation" 
                                                   name="password_confirmation" 
                                                   required>
                                            <button type="button" 
                                                    class="input-group-text bg-light" 
                                                    onclick="togglePassword('password_confirmation', this)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                        <div id="matchMessage" class="small mt-1"></div>
                                    </div>
                                </div>

                                <!-- Password Strength Meter -->
                                <div class="col-12">
                                    <div class="password-strength mb-4">
                                        <div class="d-flex justify-content-between mb-1">
                                            <small>Kekuatan Password:</small>
                                            <small id="strengthText" class="text-muted">Lemah</small>
                                        </div>
                                        <div class="progress" style="height: 6px;">
                                            <div id="strengthBar" 
                                                 class="progress-bar bg-danger" 
                                                 role="progressbar" 
                                                 style="width: 0%"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="col-12 mt-2">
                                    <div class="d-flex justify-content-between">
                                        <a href="{{ route('profile.index') }}" class="btn btn-outline-secondary px-4 py-2">
                                            <i class="fas fa-times me-2"></i>Batal
                                        </a>
                                        <button type="submit" class="btn btn-primary px-4 py-2" id="submitBtn" disabled>
                                            <i class="fas fa-save me-2"></i>Update Password
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .password-requirements {
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 8px;
        border: 1px solid #e9ecef;
    }
    
    .password-requirements li {
        margin-bottom: 0.25rem;
    }
    
    .password-requirements li.valid {
        color: #28a745;
    }
    
    .password-requirements li.invalid {
        color: #dc3545;
    }
    
    .password-strength .progress {
        border-radius: 3px;
    }
</style>
@endpush

@push('scripts')
<script>
    // Toggle password visibility
    function togglePassword(inputId, button) {
        const input = document.getElementById(inputId);
        const icon = button.querySelector('i');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
    
    // Password validation
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('password_confirmation');
    const submitBtn = document.getElementById('submitBtn');
    const matchMessage = document.getElementById('matchMessage');
    
    // Check password requirements
    function checkPassword() {
        const password = passwordInput.value;
        const confirm = confirmInput.value;
        
        // Requirements check
        const hasLength = password.length >= 8;
        const hasUpperCase = /[A-Z]/.test(password);
        const hasLowerCase = /[a-z]/.test(password);
        const hasNumber = /\d/.test(password);
        const passwordsMatch = password === confirm && password.length > 0;
        
        // Update requirement indicators
        updateRequirement('length', hasLength);
        updateRequirement('uppercase', hasUpperCase);
        updateRequirement('lowercase', hasLowerCase);
        updateRequirement('number', hasNumber);
        
        // Update match message
        if (confirm.length > 0) {
            if (passwordsMatch) {
                matchMessage.innerHTML = '<i class="fas fa-check text-success me-1"></i>Password cocok';
                matchMessage.className = 'text-success small mt-1';
            } else {
                matchMessage.innerHTML = '<i class="fas fa-times text-danger me-1"></i>Password tidak cocok';
                matchMessage.className = 'text-danger small mt-1';
            }
        } else {
            matchMessage.innerHTML = '';
        }
        
        // Calculate strength
        let strength = 0;
        if (hasLength) strength += 25;
        if (hasUpperCase) strength += 25;
        if (hasLowerCase) strength += 25;
        if (hasNumber) strength += 25;
        
        // Update strength bar
        const strengthBar = document.getElementById('strengthBar');
        const strengthText = document.getElementById('strengthText');
        
        strengthBar.style.width = strength + '%';
        
        if (strength <= 25) {
            strengthBar.className = 'progress-bar bg-danger';
            strengthText.textContent = 'Lemah';
            strengthText.className = 'text-danger';
        } else if (strength <= 50) {
            strengthBar.className = 'progress-bar bg-warning';
            strengthText.textContent = 'Cukup';
            strengthText.className = 'text-warning';
        } else if (strength <= 75) {
            strengthBar.className = 'progress-bar bg-info';
            strengthText.textContent = 'Baik';
            strengthText.className = 'text-info';
        } else {
            strengthBar.className = 'progress-bar bg-success';
            strengthText.textContent = 'Sangat Baik';
            strengthText.className = 'text-success';
        }
        
        // Enable/disable submit button
        const allValid = hasLength && hasUpperCase && hasLowerCase && hasNumber && passwordsMatch;
        submitBtn.disabled = !allValid;
    }
    
    function updateRequirement(elementId, isValid) {
        const element = document.getElementById(elementId);
        if (isValid) {
            element.innerHTML = '<i class="fas fa-check me-1"></i>' + element.textContent.replace(/^.*?\)/, '');
            element.className = 'text-success';
        } else {
            element.innerHTML = '<i class="fas fa-times me-1"></i>' + element.textContent.replace(/^.*?\)/, '');
            element.className = 'text-danger';
        }
    }
    
    // Add event listeners
    passwordInput.addEventListener('input', checkPassword);
    confirmInput.addEventListener('input', checkPassword);
    
    // Form submission validation
    document.getElementById('passwordForm').addEventListener('submit', function(e) {
        const currentPassword = document.getElementById('current_password').value;
        const newPassword = document.getElementById('password').value;
        
        if (currentPassword === newPassword) {
            e.preventDefault();
            alert('Password baru tidak boleh sama dengan password saat ini!');
            return false;
        }
        
        return true;
    });
    
    // Initial check
    checkPassword();
</script>
@endpush