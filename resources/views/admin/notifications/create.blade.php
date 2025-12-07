@extends('layouts.app')

@section('title', 'Buat Notifikasi Baru')
@section('page-title', 'Buat Notifikasi')
@section('icon', 'fas fa-bell')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="activity-card">
                <div class="card-header">
                    <h3><i class="fas fa-plus me-2"></i>Form Buat Notifikasi</h3>
                    <a href="{{ route('admin.notifications.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Kembali
                    </a>
                </div>
                
                <form action="{{ route('admin.notifications.store') }}" method="POST" id="notificationForm">
                    @csrf
                    
                    <!-- Informasi Dasar -->
                    <div class="card-body">
                        <h5 class="mb-4" style="color: var(--primary-color);">
                            <i class="fas fa-info-circle me-2"></i>Informasi Dasar
                        </h5>
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Judul Notifikasi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title') }}" 
                                   placeholder="Contoh: Pengingat Pembayaran Iuran" required>
                            @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="message" class="form-label">Isi Pesan <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('message') is-invalid @enderror" 
                                      id="message" name="message" rows="5" 
                                      placeholder="Tulis pesan notifikasi di sini..." required>{{ old('message') }}</textarea>
                            @error('message')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Maksimal 1000 karakter. <span id="charCount">0/1000</span></small>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
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
                            
                            <div class="col-md-6">
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
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
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
                            
                            <div class="col-md-6">
                                <label for="datalansia_id" class="form-label">Kaitkan dengan Lansia (Opsional)</label>
                                <select class="form-select @error('datalansia_id') is-invalid @enderror" 
                                        id="datalansia_id" name="datalansia_id">
                                    <option value="">-- Pilih Lansia --</option>
                                    @foreach($lansias as $lansia)
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
                        
                        <hr class="my-4">
                        
                        <!-- Target Penerima -->
                        <h5 class="mb-4" style="color: var(--primary-color);">
                            <i class="fas fa-users me-2"></i>Target Penerima
                        </h5>
                        
                        <div class="mb-3">
                            <label class="form-label">Kirim ke:</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="target_type" 
                                               id="target_specific" value="specific_user" 
                                               {{ old('target_type', 'specific_user') == 'specific_user' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="target_specific">
                                            <i class="fas fa-user me-1"></i> User Tertentu
                                        </label>
                                    </div>
                                    
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="target_type" 
                                               id="target_family" value="all_family"
                                               {{ old('target_type') == 'all_family' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="target_family">
                                            <i class="fas fa-users me-1"></i> Semua Keluarga
                                        </label>
                                    </div>
                                    
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="target_type" 
                                               id="target_nurses" value="all_nurses"
                                               {{ old('target_type') == 'all_nurses' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="target_nurses">
                                            <i class="fas fa-user-md me-1"></i> Semua Perawat
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="target_type" 
                                               id="target_admins" value="all_admins"
                                               {{ old('target_type') == 'all_admins' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="target_admins">
                                            <i class="fas fa-user-shield me-1"></i> Semua Admin
                                        </label>
                                    </div>
                                    
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="target_type" 
                                               id="target_all" value="all_users"
                                               {{ old('target_type') == 'all_users' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="target_all">
                                            <i class="fas fa-globe me-1"></i> Semua User
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Pilih User Tertentu -->
                        <div class="mb-3" id="userSelection" style="{{ old('target_type', 'specific_user') == 'specific_user' ? '' : 'display: none;' }}">
                            <label for="user_id" class="form-label">Pilih User <span class="text-danger">*</span></label>
                            <select class="form-select @error('user_id') is-invalid @enderror" 
                                    id="user_id" name="user_id">
                                <option value="">-- Pilih User --</option>
                                @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->role }})
                                </option>
                                @endforeach
                            </select>
                            @error('user_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <hr class="my-4">
                        
                        <!-- Pengaturan Tambahan -->
                        <h5 class="mb-4" style="color: var(--primary-color);">
                            <i class="fas fa-cog me-2"></i>Pengaturan Tambahan
                        </h5>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="action_url" class="form-label">URL Tindakan (Opsional)</label>
                                <input type="text" class="form-control @error('action_url') is-invalid @enderror" 
                                       id="action_url" name="action_url" value="{{ old('action_url') }}" 
                                       placeholder="Contoh: /admin/iuran">
                                @error('action_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="action_text" class="form-label">Teks Tombol (Opsional)</label>
                                <input type="text" class="form-control @error('action_text') is-invalid @enderror" 
                                       id="action_text" name="action_text" value="{{ old('action_text') }}" 
                                       placeholder="Contoh: Lihat Detail" maxlength="30">
                                @error('action_text')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="scheduled_at" class="form-label">Jadwalkan Pengiriman</label>
                                <input type="datetime-local" class="form-control @error('scheduled_at') is-invalid @enderror" 
                                       id="scheduled_at" name="scheduled_at" value="{{ old('scheduled_at') }}">
                                @error('scheduled_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Kosongkan untuk kirim sekarang</small>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="expires_at" class="form-label">Batas Waktu</label>
                                <input type="datetime-local" class="form-control @error('expires_at') is-invalid @enderror" 
                                       id="expires_at" name="expires_at" value="{{ old('expires_at') }}">
                                @error('expires_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Notifikasi akan otomatis dihapus setelah waktu ini</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tombol Aksi -->
                    <div class="card-footer bg-white">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.notifications.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>Kirim Notifikasi
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
    // Toggle user selection
    document.querySelectorAll('input[name="target_type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const userSelection = document.getElementById('userSelection');
            if (this.value === 'specific_user') {
                userSelection.style.display = 'block';
            } else {
                userSelection.style.display = 'none';
            }
        });
    });
    
    // Hitung karakter
    const messageInput = document.getElementById('message');
    const charCount = document.getElementById('charCount');
    
    messageInput.addEventListener('input', function() {
        const length = this.value.length;
        charCount.textContent = `${length}/1000`;
        
        if (length > 1000) {
            charCount.style.color = '#dc3545';
            this.value = this.value.substring(0, 1000);
        } else if (length > 800) {
            charCount.style.color = '#ffc107';
        } else {
            charCount.style.color = '#6c757d';
        }
    });
    
    // Validasi form
    document.getElementById('notificationForm').addEventListener('submit', function(e) {
        const title = document.getElementById('title').value.trim();
        const message = document.getElementById('message').value.trim();
        const targetType = document.querySelector('input[name="target_type"]:checked').value;
        
        // Validasi judul dan pesan
        if (!title) {
            e.preventDefault();
            alert('Judul notifikasi harus diisi!');
            document.getElementById('title').focus();
            return;
        }
        
        if (!message) {
            e.preventDefault();
            alert('Pesan notifikasi harus diisi!');
            document.getElementById('message').focus();
            return;
        }
        
        if (message.length > 1000) {
            e.preventDefault();
            alert('Pesan tidak boleh lebih dari 1000 karakter!');
            document.getElementById('message').focus();
            return;
        }
        
        // Validasi user khusus
        if (targetType === 'specific_user') {
            const userId = document.getElementById('user_id').value;
            if (!userId) {
                e.preventDefault();
                alert('Silakan pilih user untuk notifikasi ini!');
                document.getElementById('user_id').focus();
                return;
            }
        }
        
        // Konfirmasi notifikasi darurat
        const urgency = document.getElementById('urgency_level').value;
        if (urgency === 'critical' || urgency === 'high') {
            if (!confirm('⚠️ PERHATIAN!\n\nAnda akan mengirim notifikasi dengan prioritas tinggi.\n\nYakin ingin melanjutkan?')) {
                e.preventDefault();
                return;
            }
        }
        
        // Validasi tanggal
        const scheduledAt = document.getElementById('scheduled_at').value;
        const expiresAt = document.getElementById('expires_at').value;
        
        if (scheduledAt && expiresAt) {
            const scheduleDate = new Date(scheduledAt);
            const expireDate = new Date(expiresAt);
            
            if (expireDate <= scheduleDate) {
                e.preventDefault();
                alert('Waktu kadaluarsa harus setelah waktu penjadwalan!');
                document.getElementById('expires_at').focus();
                return;
            }
        }
    });
    
    // Auto-set waktu
    document.addEventListener('DOMContentLoaded', function() {
        // Set waktu default untuk scheduled_at (1 jam dari sekarang)
        const now = new Date();
        now.setHours(now.getHours() + 1);
        now.setMinutes(0);
        now.setSeconds(0);
        
        if (!document.getElementById('scheduled_at').value) {
            document.getElementById('scheduled_at').value = now.toISOString().slice(0, 16);
        }
        
        // Set waktu default untuk expires_at (24 jam dari scheduled)
        const expireTime = new Date(now);
        expireTime.setHours(expireTime.getHours() + 24);
        
        if (!document.getElementById('expires_at').value) {
            document.getElementById('expires_at').value = expireTime.toISOString().slice(0, 16);
        }
        
        // Initialize character count
        charCount.textContent = `${messageInput.value.length}/1000`;
        
        // Auto-update expires_at when scheduled_at changes
        document.getElementById('scheduled_at').addEventListener('change', function() {
            if (this.value) {
                const newScheduled = new Date(this.value);
                newScheduled.setHours(newScheduled.getHours() + 24);
                
                const expiresInput = document.getElementById('expires_at');
                if (!expiresInput.value || new Date(expiresInput.value) <= new Date(this.value)) {
                    expiresInput.value = newScheduled.toISOString().slice(0, 16);
                }
            }
        });
    });
</script>
@endpush
@endsection