@extends('layouts.app')

@section('title', 'Edit Profile')
@section('page-title', 'Edit Profile')
@section('icon', 'fas fa-user-edit')

@section('content')
    <div class="content-container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Profile</h5>
                            <a href="{{ route('profile.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-arrow-left me-1"></i>Kembali
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

                        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <div class="row g-4">
                                <!-- Foto Profil -->
                                <div class="col-12">
                                    <div class="text-center mb-4">
                                        <div class="profile-photo-edit mb-3">
                                            @if($user->foto_profil)
                                                <img src="{{ Storage::url($user->foto_profil) }}" 
                                                     alt="Profile Photo" 
                                                     id="profilePreview"
                                                     class="rounded-circle shadow mb-2"
                                                     style="width: 120px; height: 120px; object-fit: cover;">
                                            @else
                                                <div id="profilePreview" 
                                                     class="rounded-circle bg-primary d-flex align-items-center justify-content-center mx-auto shadow mb-2"
                                                     style="width: 120px; height: 120px;">
                                                    <i class="fas fa-user fa-2x text-white"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <label for="foto_profil" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-upload me-1"></i>Upload Foto Baru
                                        </label>
                                        <input type="file" 
                                               class="form-control d-none" 
                                               id="foto_profil" 
                                               name="foto_profil" 
                                               accept="image/*" 
                                               onchange="previewImage(this)">
                                        <div class="form-text mt-2">
                                            Ukuran maksimal 2MB. Format: JPG, PNG, GIF
                                        </div>
                                    </div>
                                </div>

                                <!-- Informasi Pribadi -->
                                <div class="col-md-6">
                                    <h6 class="mb-3 text-primary"><i class="fas fa-user me-2"></i>Informasi Pribadi</h6>
                                    
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               class="form-control py-2 px-3" 
                                               id="name" 
                                               name="name" 
                                               value="{{ old('name', $user->name) }}" 
                                               required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="email" 
                                               class="form-control py-2 px-3" 
                                               id="email" 
                                               name="email" 
                                               value="{{ old('email', $user->email) }}" 
                                               required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="role" class="form-label">Role</label>
                                        <input type="text" 
                                               class="form-control py-2 px-3 bg-light" 
                                               id="role" 
                                               value="{{ ucfirst($user->role) }}" 
                                               readonly>
                                        <small class="text-muted">Role tidak dapat diubah</small>
                                    </div>
                                </div>

                                <!-- Kontak & Alamat -->
                                <div class="col-md-6">
                                    <h6 class="mb-3 text-primary"><i class="fas fa-address-book me-2"></i>Kontak & Alamat</h6>
                                    
                                    <div class="mb-3">
                                        <label for="no_telepon" class="form-label">Nomor Telepon</label>
                                        <div class="input-group">
                                            <span class="input-group-text py-2 px-3 bg-light">+62</span>
                                            <input type="text" 
                                                   class="form-control py-2 px-3" 
                                                   id="no_telepon" 
                                                   name="no_telepon" 
                                                   value="{{ old('no_telepon', $user->no_telepon) }}"
                                                   placeholder="81234567890">
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="alamat" class="form-label">Alamat</label>
                                        <textarea class="form-control py-2 px-3" 
                                                  id="alamat" 
                                                  name="alamat" 
                                                  rows="4"
                                                  placeholder="Masukkan alamat lengkap">{{ old('alamat', $user->alamat) }}</textarea>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="col-12 mt-4 pt-3 border-top">
                                    <div class="d-flex justify-content-between">
                                        <a href="{{ route('profile.index') }}" class="btn btn-outline-secondary px-4 py-2">
                                            <i class="fas fa-times me-2"></i>Batal
                                        </a>
                                        <button type="submit" class="btn btn-primary px-4 py-2">
                                            <i class="fas fa-save me-2"></i>Simpan Perubahan
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

@push('scripts')
<script>
    function previewImage(input) {
        const preview = document.getElementById('profilePreview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                if (preview.tagName === 'IMG') {
                    preview.src = e.target.result;
                } else {
                    // Convert div to img
                    const img = document.createElement('img');
                    img.id = 'profilePreview';
                    img.className = 'rounded-circle shadow mb-2';
                    img.style.width = '120px';
                    img.style.height = '120px';
                    img.style.objectFit = 'cover';
                    img.src = e.target.result;
                    preview.parentNode.replaceChild(img, preview);
                }
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    
    // Validasi input
    document.querySelector('form').addEventListener('submit', function(e) {
        const email = document.getElementById('email').value;
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (!emailPattern.test(email)) {
            e.preventDefault();
            alert('Format email tidak valid!');
            return false;
        }
        
        const phone = document.getElementById('no_telepon').value;
        if (phone && !/^[0-9]+$/.test(phone)) {
            e.preventDefault();
            alert('Nomor telepon hanya boleh mengandung angka!');
            return false;
        }
    });
</script>
@endpush