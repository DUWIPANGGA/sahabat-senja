@extends('layouts.app')

@section('title', 'Detail Data Perawat')
@section('page-title', 'Detail Data Perawat')
@section('icon', 'fas fa-eye')

@section('content')
    <div class="content-container">
        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="fas fa-home"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.DataPerawat.index') }}">Data Perawat</a></li>
                <li class="breadcrumb-item active">Detail Perawat</li>
            </ol>
        </nav>

        {{-- Detail Card --}}
        <div class="card border-0 shadow-lg">
            <div class="card-header bg-primary text-white d-flex align-items-center justify-content-between py-3">
                <div class="d-flex align-items-center">
                    <i class="fas fa-user-md me-3 fs-4"></i>
                    <h5 class="mb-0">Detail Data Perawat</h5>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.DataPerawat.edit', $perawat->id) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit me-1"></i>Edit
                    </a>
                    <a href="{{ route('admin.DataPerawat.index') }}" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Kembali
                    </a>
                </div>
            </div>
            
            <div class="card-body p-4">
                {{-- Profil Header --}}
                <div class="row align-items-center mb-5">
                    <div class="col-auto">
                        <div class="avatar-lg bg-primary rounded-circle d-flex align-items-center justify-content-center" 
                             style="width: 100px; height: 100px;">
                            <span class="text-white fw-bold display-6">
                                {{ strtoupper(substr($perawat->nama, 0, 1)) }}
                            </span>
                        </div>
                    </div>
                    <div class="col">
                        <h3 class="mb-1">{{ $perawat->nama }}</h3>
                        <p class="text-muted mb-2">
                            <i class="fas fa-id-card me-2"></i>ID: {{ $perawat->id }}
                        </p>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge bg-info">
                                <i class="fas fa-user-md me-1"></i>Perawat
                            </span>
                            @if($perawat->jenis_kelamin == 'Laki-laki')
                                <span class="badge bg-primary">
                                    <i class="fas fa-mars me-1"></i>{{ $perawat->jenis_kelamin }}
                                </span>
                            @else
                                <span class="badge bg-pink">
                                    <i class="fas fa-venus me-1"></i>{{ $perawat->jenis_kelamin }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Informasi Utama --}}
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="border-bottom pb-2 mb-3 d-flex align-items-center">
                            <i class="fas fa-info-circle me-2 text-primary"></i>
                            Informasi Kontak
                        </h5>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <div class="card h-100 border">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="icon-wrapper bg-primary-subtle rounded-circle p-3 me-3">
                                        <i class="fas fa-envelope text-primary fs-4"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">Email</h6>
                                        <small class="text-muted">Alamat email aktif</small>
                                    </div>
                                </div>
                                <p class="mb-0">
                                    @if($perawat->email)
                                        <a href="mailto:{{ $perawat->email }}" class="text-decoration-none">
                                            {{ $perawat->email }}
                                        </a>
                                    @else
                                        <span class="text-muted">Tidak ada email</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <div class="card h-100 border">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="icon-wrapper bg-success-subtle rounded-circle p-3 me-3">
                                        <i class="fas fa-phone text-success fs-4"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">Nomor Telepon</h6>
                                        <small class="text-muted">Nomor handphone aktif</small>
                                    </div>
                                </div>
                                <p class="mb-0">
                                    @if($perawat->no_hp)
                                        <a href="tel:{{ $perawat->no_hp }}" class="text-decoration-none">
                                            {{ $perawat->no_hp }}
                                        </a>
                                    @else
                                        <span class="text-muted">Tidak ada nomor telepon</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Alamat --}}
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="border-bottom pb-2 mb-3 d-flex align-items-center">
                            <i class="fas fa-map-marker-alt me-2 text-primary"></i>
                            Alamat
                        </h5>
                    </div>
                    
                    <div class="col-12">
                        <div class="card border">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="icon-wrapper bg-warning-subtle rounded-circle p-3 me-3">
                                        <i class="fas fa-home text-warning fs-4"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">Alamat Lengkap</h6>
                                        <small class="text-muted">Tempat tinggal saat ini</small>
                                    </div>
                                </div>
                                <p class="mb-0" style="white-space: pre-line;">{{ $perawat->alamat }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Informasi Tambahan --}}
                @if($perawat->catatan)
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="border-bottom pb-2 mb-3 d-flex align-items-center">
                            <i class="fas fa-sticky-note me-2 text-primary"></i>
                            Catatan Khusus
                        </h5>
                    </div>
                    
                    <div class="col-12">
                        <div class="card border">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="icon-wrapper bg-info-subtle rounded-circle p-3 me-3">
                                        <i class="fas fa-notes-medical text-info fs-4"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">Catatan Tambahan</h6>
                                        <small class="text-muted">Informasi khusus</small>
                                    </div>
                                </div>
                                <p class="mb-0" style="white-space: pre-line;">{{ $perawat->catatan }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Informasi Sistem --}}
                <div class="row">
                    <div class="col-12">
                        <h5 class="border-bottom pb-2 mb-3 d-flex align-items-center">
                            <i class="fas fa-database me-2 text-primary"></i>
                            Informasi Sistem
                        </h5>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <div class="card h-100 border">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="icon-wrapper bg-secondary-subtle rounded-circle p-3 me-3">
                                        <i class="fas fa-calendar-plus text-secondary fs-4"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">Dibuat Pada</h6>
                                        <small class="text-muted">Tanggal pendaftaran</small>
                                    </div>
                                </div>
                                <p class="mb-0">
                                    {{ $perawat->created_at->translatedFormat('d F Y, H:i') }}
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <div class="card h-100 border">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="icon-wrapper bg-secondary-subtle rounded-circle p-3 me-3">
                                        <i class="fas fa-calendar-check text-secondary fs-4"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">Terakhir Diupdate</h6>
                                        <small class="text-muted">Update terakhir data</small>
                                    </div>
                                </div>
                                <p class="mb-0">
                                    {{ $perawat->updated_at->translatedFormat('d F Y, H:i') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Action Footer --}}
            <div class="card-footer bg-light py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted small">
                        <i class="fas fa-clock me-1"></i>
                        Terakhir dilihat: {{ now()->translatedFormat('d F Y, H:i') }}
                    </span>
                    <div class="d-flex gap-2">
                        <form action="{{ route('admin.DataPerawat.destroy', $perawat->id) }}" 
                              method="POST" 
                              onsubmit="return confirmDelete(event)">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                <i class="fas fa-trash me-1"></i>Hapus
                            </button>
                        </form>
                        <a href="{{ route('admin.DataPerawat.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-list me-1"></i>Daftar Perawat
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .content-container {
        padding: 1rem;
    }
    
    .avatar-lg {
        width: 100px;
        height: 100px;
        font-size: 2.5rem;
    }
    
    .icon-wrapper {
        width: 56px;
        height: 56px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .bg-pink {
        background-color: #e83e8c !important;
    }
    
    .bg-primary-subtle {
        background-color: rgba(13, 110, 253, 0.1) !important;
    }
    
    .bg-success-subtle {
        background-color: rgba(25, 135, 84, 0.1) !important;
    }
    
    .bg-warning-subtle {
        background-color: rgba(255, 193, 7, 0.1) !important;
    }
    
    .bg-info-subtle {
        background-color: rgba(13, 202, 240, 0.1) !important;
    }
    
    .bg-secondary-subtle {
        background-color: rgba(108, 117, 125, 0.1) !important;
    }
    
    .card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
    
    .breadcrumb {
        background-color: transparent;
        padding: 0.5rem 0;
    }
    
    .breadcrumb-item a {
        text-decoration: none;
        color: var(--primary-color);
    }
    
    h5 {
        color: var(--dark-brown);
        font-weight: 600;
    }
    
    @media (max-width: 768px) {
        .content-container {
            padding: 0.5rem;
        }
        
        .avatar-lg {
            width: 80px;
            height: 80px;
            font-size: 2rem;
        }
        
        .card-header .d-flex {
            flex-direction: column;
            align-items: stretch !important;
            gap: 1rem;
        }
        
        .card-header .btn {
            width: 100%;
        }
        
        .card-footer .d-flex {
            flex-direction: column;
            gap: 1rem;
            text-align: center;
        }
        
        .card-footer .btn {
            width: 100%;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    function confirmDelete(event) {
        event.preventDefault();
        const form = event.target;
        
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data perawat akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    }
    
    // Print function
    function printDetail() {
        window.print();
    }
    
    // Share function (if needed)
    function shareDetail() {
        if (navigator.share) {
            navigator.share({
                title: 'Detail Perawat - {{ $perawat->nama }}',
                text: 'Lihat detail perawat {{ $perawat->nama }}',
                url: window.location.href
            });
        } else {
            // Fallback for browsers that don't support Web Share API
            navigator.clipboard.writeText(window.location.href).then(() => {
                alert('Link berhasil disalin ke clipboard!');
            });
        }
    }
</script>
@endpush