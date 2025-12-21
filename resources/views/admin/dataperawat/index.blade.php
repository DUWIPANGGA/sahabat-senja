@extends('layouts.app')

@section('title', 'Data Perawat')
@section('page-title', 'Data Perawat')
@section('icon', 'fas fa-user-nurse')

@section('content')
    <div class="content-container">
        {{-- Alert sukses --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <strong>Sukses!</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <strong>Error!</strong> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Search and Filter Card --}}
        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('admin.DataPerawat.index') }}" method="GET" class="row g-3 align-items-center">
                    <div class="col-md-8">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" 
                                   name="search" 
                                   class="form-control border-start-0" 
                                   placeholder="Cari berdasarkan nama, email, no HP, atau alamat..." 
                                   value="{{ request('search') }}"
                                   aria-label="Search">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1">
                                <i class="fas fa-search me-2"></i>Cari
                            </button>
                            <a href="{{ route('admin.DataPerawat.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-refresh"></i>
                            </a>
                            {{-- <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exportModal">
                                <i class="fas fa-download me-2"></i>Export
                            </button> --}}
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Data Table Card --}}
        <div class="card">
            <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center py-3">
                <div class="mb-2 mb-md-0">
                    <h5 class="card-title mb-0 d-flex align-items-center">
                        <i class="fas fa-user-nurse me-2 text-primary"></i>
                        Daftar Perawat
                    </h5>
                    <p class="text-muted mb-0 small mt-1">
                        Total {{ $DataPerawat->total() }} data perawat
                        @if(request('search'))
                            <span class="text-primary">
                                • Hasil pencarian: "{{ request('search') }}"
                            </span>
                        @endif
                    </p>
                </div>
                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary d-flex align-items-center">
                            <i class="fas fa-users me-1"></i>
                            {{ $DataPerawat->total() }} Data
                        </span>
                        <span class="badge bg-info d-flex align-items-center">
                            <i class="fas fa-user me-1"></i>
                            {{ $DataPerawat->where('jenis_kelamin', 'Laki-laki')->count() }} Laki-laki
                        </span>
                        <span class="badge bg-warning d-flex align-items-center">
                            <i class="fas fa-user me-1"></i>
                            {{ $DataPerawat->where('jenis_kelamin', 'Perempuan')->count() }} Perempuan
                        </span>
                    </div>
                    <a href="{{ route('admin.DataPerawat.create') }}" class="btn btn-success">
                        <i class="fas fa-plus me-2"></i>Tambah Perawat
                    </a>
                </div>
            </div>
            
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" width="60">No</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>No HP</th>
                                <th>Jenis Kelamin</th>
                                <th class="text-center" width="180">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($DataPerawat as $index => $perawat)
                                <tr>
                                    <td class="text-center fw-bold">
                                        {{ ($DataPerawat->currentPage() - 1) * $DataPerawat->perPage() + $index + 1 }}
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-3">
                                                <span class="text-white fw-bold">
                                                    {{ strtoupper(substr($perawat->nama, 0, 1)) }}
                                                </span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $perawat->nama }}</h6>
                                                <small class="text-muted">ID: PER{{ str_pad($perawat->id, 4, '0', STR_PAD_LEFT) }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($perawat->email)
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-envelope me-2 text-primary"></i>
                                                <a href="mailto:{{ $perawat->email }}" 
                                                   class="text-decoration-none text-truncate" 
                                                   style="max-width: 200px;"
                                                   title="{{ $perawat->email }}">
                                                    {{ $perawat->email }}
                                                </a>
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($perawat->no_hp)
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-phone me-2 text-success"></i>
                                                <a href="tel:{{ $perawat->no_hp }}" 
                                                   class="text-decoration-none">
                                                    {{ $perawat->no_hp }}
                                                </a>
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($perawat->jenis_kelamin == 'Laki-laki')
                                            <span class="badge bg-info d-inline-flex align-items-center">
                                                <i class="fas fa-mars me-1"></i>{{ $perawat->jenis_kelamin }}
                                            </span>
                                        @else
                                            <span class="badge bg-warning d-inline-flex align-items-center">
                                                <i class="fas fa-venus me-1"></i>{{ $perawat->jenis_kelamin }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('admin.DataPerawat.show', $perawat->id) }}" 
                                               class="btn btn-info" 
                                               title="Detail"
                                               data-bs-toggle="tooltip">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.DataPerawat.edit', $perawat->id) }}" 
                                               class="btn btn-warning" 
                                               title="Edit"
                                               data-bs-toggle="tooltip">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.DataPerawat.destroy', $perawat->id) }}" 
                                                  method="POST" 
                                                  class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-danger" 
                                                        title="Hapus"
                                                        data-bs-toggle="tooltip"
                                                        onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="empty-state">
                                            <i class="fas fa-user-nurse fa-3x text-muted mb-3"></i>
                                            <h5 class="mb-2">Tidak ada data perawat</h5>
                                            <p class="text-muted mb-4">
                                                @if(request('search'))
                                                    Hasil tidak ditemukan untuk pencarian "{{ request('search') }}"
                                                @else
                                                    Silakan tambah data perawat pertama Anda
                                                @endif
                                            </p>
                                            <a href="{{ route('admin.DataPerawat.create') }}" class="btn btn-primary">
                                                <i class="fas fa-plus me-2"></i>Tambah Perawat
                                            </a>
                                            @if(request('search'))
                                                <a href="{{ route('admin.DataPerawat.index') }}" class="btn btn-outline-secondary ms-2">
                                                    <i class="fas fa-times me-2"></i>Hapus Pencarian
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            @if($DataPerawat->hasPages())
                <div class="card-footer">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                        <div class="text-muted small mb-2 mb-md-0">
                            Menampilkan {{ $DataPerawat->firstItem() ?? 0 }} - {{ $DataPerawat->lastItem() ?? 0 }} dari {{ $DataPerawat->total() }} data
                        </div>
                        <div>
                            {{ $DataPerawat->withQueryString()->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Export Modal --}}
    <div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exportModalLabel">
                        <i class="fas fa-download me-2"></i>Export Data Perawat
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-4">Pilih format untuk mengexport data perawat:</p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="card border-primary text-center h-100">
                                <div class="card-body d-flex flex-column justify-content-center py-4">
                                    <i class="fas fa-file-excel text-success fa-3x mb-3"></i>
                                    <h6 class="card-title">Excel (.xlsx)</h6>
                                    <p class="card-text small text-muted">Export data dalam format Excel</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-primary text-center h-100">
                                <div class="card-body d-flex flex-column justify-content-center py-4">
                                    <i class="fas fa-file-pdf text-danger fa-3x mb-3"></i>
                                    <h6 class="card-title">PDF (.pdf)</h6>
                                    <p class="card-text small text-muted">Export data dalam format PDF</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary">
                        <i class="fas fa-download me-2"></i>Export Sekarang
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .content-container {
        padding: 0;
    }
    
    .avatar-sm {
        width: 40px;
        height: 40px;
        font-size: 1rem;
    }
    
    .btn-group .btn {
        border-radius: 6px !important;
        margin: 0 2px;
        padding: 0.375rem 0.75rem;
    }
    
    .card-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--text-dark);
    }
    
    .table tbody tr {
        transition: all 0.2s;
        border-bottom: 1px solid var(--border-color);
    }
    
    .table tbody tr:hover {
        background-color: rgba(var(--primary-color-rgb, 139, 115, 85), 0.05);
        transform: translateX(5px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    
    .empty-state {
        padding: 2rem;
        text-align: center;
    }
    
    .empty-state i {
        opacity: 0.5;
    }
    
    .badge {
        font-size: 0.8em;
        padding: 0.35em 0.65em;
        border-radius: 0.375rem;
    }
    
    .table thead th {
        background-color: var(--light-bg);
        border-bottom: 2px solid var(--primary-color);
        color: var(--text-dark);
        font-weight: 600;
        padding: 1rem 0.75rem;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
    }
    
    .table tbody td {
        padding: 1rem 0.75rem;
        vertical-align: middle;
    }
    
    .modal-content {
        border: none;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        border-radius: 0.75rem;
    }
    
    .modal-body .card {
        cursor: pointer;
        transition: all 0.3s;
        border: 2px solid transparent;
    }
    
    .modal-body .card:hover {
        border-color: var(--primary-color);
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
    }
    
    @media (max-width: 768px) {
        .table-responsive {
            font-size: 0.9rem;
        }
        
        .btn-group {
            flex-direction: column;
            gap: 0.25rem;
        }
        
        .btn-group .btn {
            width: 100%;
            margin: 0;
        }
        
        .card-header {
            flex-direction: column;
            align-items: stretch !important;
            gap: 1rem;
        }
        
        .d-flex.align-items-center.gap-2 {
            flex-wrap: wrap;
            justify-content: center;
        }
        
        .avatar-sm {
            width: 32px;
            height: 32px;
            font-size: 0.875rem;
        }
        
        .table td, .table th {
            padding: 0.75rem 0.5rem;
        }
        
        .empty-state {
            padding: 1rem;
        }
        
        .empty-state i {
            font-size: 2rem;
        }
    }
    
    @media (max-width: 576px) {
        .avatar-sm {
            width: 30px;
            height: 30px;
            font-size: 0.75rem;
        }
        
        .btn-group-sm .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        
        .card-title {
            font-size: 1.1rem;
        }
        
        .card-body {
            padding: 1rem !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Auto-hide alerts setelah 5 detik
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        });
        
        // Highlight search term
        const searchParam = new URLSearchParams(window.location.search).get('search');
        if (searchParam) {
            const searchWords = searchParam.toLowerCase().split(' ').filter(word => word.length > 2);
            const tableCells = document.querySelectorAll('.table tbody td:not(:last-child)');
            
            tableCells.forEach(cell => {
                const originalText = cell.textContent;
                let highlightedText = originalText;
                
                searchWords.forEach(word => {
                    if (word.length > 2) {
                        const regex = new RegExp(`(${word})`, 'gi');
                        highlightedText = highlightedText.replace(
                            regex, 
                            '<mark class="bg-warning">$1</mark>'
                        );
                    }
                });
                
                if (highlightedText !== originalText) {
                    cell.innerHTML = highlightedText;
                }
            });
        }
        
        // Export modal functionality
        const exportModal = document.getElementById('exportModal');
        if (exportModal) {
            const exportButtons = exportModal.querySelectorAll('.modal-body .card');
            const exportConfirmBtn = exportModal.querySelector('.modal-footer .btn-primary');
            
            let selectedFormat = null;
            
            exportButtons.forEach(card => {
                card.addEventListener('click', function() {
                    // Remove active class from all cards
                    exportButtons.forEach(c => c.classList.remove('border-primary', 'bg-light'));
                    
                    // Add active class to selected card
                    this.classList.add('border-primary', 'bg-light');
                    
                    // Determine selected format
                    const title = this.querySelector('.card-title').textContent;
                    if (title.includes('Excel')) {
                        selectedFormat = 'excel';
                    } else if (title.includes('PDF')) {
                        selectedFormat = 'pdf';
                    }
                    
                    // Update confirm button text
                    if (exportConfirmBtn && selectedFormat) {
                        exportConfirmBtn.innerHTML = `
                            <i class="fas fa-download me-2"></i>
                            Export ${selectedFormat.toUpperCase()}
                        `;
                    }
                });
            });
            
            exportConfirmBtn.addEventListener('click', function() {
                if (!selectedFormat) {
                    alert('Silakan pilih format export terlebih dahulu');
                    return;
                }
                
                // Show loading
                const originalText = this.innerHTML;
                this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Memproses...';
                this.disabled = true;
                
                // Simulate export process
                setTimeout(() => {
                    alert(`Export ${selectedFormat.toUpperCase()} berhasil dilakukan!`);
                    const modal = bootstrap.Modal.getInstance(exportModal);
                    modal.hide();
                    
                    // Reset button
                    this.innerHTML = originalText;
                    this.disabled = false;
                    selectedFormat = null;
                    
                    // Reset cards
                    exportButtons.forEach(c => c.classList.remove('border-primary', 'bg-light'));
                }, 1500);
            });
        }
        
        // Delete confirmation with SweetAlert2 if available
        const deleteForms = document.querySelectorAll('.delete-form');
        deleteForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                if (typeof Swal !== 'undefined') {
                    e.preventDefault();
                    const form = this;
                    
                    Swal.fire({
                        title: 'Apakah Anda yakin?',
                        text: "Data perawat akan dihapus permanen dan tidak dapat dikembalikan!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal',
                        reverseButtons: true,
                        showLoaderOnConfirm: true,
                        preConfirm: () => {
                            return form.submit();
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Terhapus!',
                                text: 'Data perawat telah dihapus.',
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    });
                }
                // If SweetAlert2 is not available, browser's default confirm will work
            });
        });
        
        // Quick actions with keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl/Cmd + N untuk tambah data
            if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
                e.preventDefault();
                window.location.href = "{{ route('admin.DataPerawat.create') }}";
            }
            
            // Esc untuk clear search
            if (e.key === 'Escape' && searchParam) {
                window.location.href = "{{ route('admin.DataPerawat.index') }}";
            }
        });
        
        // Add search focus with forward slash
        document.addEventListener('keydown', function(e) {
            if (e.key === '/' && e.target.tagName !== 'INPUT' && e.target.tagName !== 'TEXTAREA') {
                e.preventDefault();
                const searchInput = document.querySelector('input[name="search"]');
                if (searchInput) {
                    searchInput.focus();
                }
            }
        });
    });
</script>
@endpush