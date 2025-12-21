@extends('layouts.app')

@section('title', 'Detail Kampanye: ' . $kampanye->judul)
@section('page-title', 'Detail Kampanye')
@section('icon', 'fas fa-info-circle')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-2" style="color: var(--dark-brown);">{{ $kampanye->judul }}</h2>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-{{ $kampanye->status == 'aktif' ? 'success' : ($kampanye->status == 'selesai' ? 'primary' : 'secondary') }}">
                    {{ ucfirst($kampanye->status) }}
                </span>
                <span class="badge bg-info">{{ $kampanye->kategori }}</span>
                @if($kampanye->is_featured)
                <span class="badge bg-warning">Featured</span>
                @endif
                <span class="text-muted ms-2">
                    <i class="far fa-calendar me-1"></i>{{ $kampanye->created_at->format('d M Y') }}
                </span>
            </div>
        </div>
        <div>
            <a href="{{ route('admin.kampanye.edit', $kampanye) }}" class="btn btn-warning me-2">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            <a href="{{ route('admin.kampanye.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Kolom Kiri - Informasi Utama -->
        <div class="col-md-8">
            <!-- Gambar Utama -->
            @if($kampanye->gambar)
            <div class="activity-card mb-4">
                <img src="{{ asset('storage/' . $kampanye->gambar) }}" 
                     alt="{{ $kampanye->judul }}" 
                     class="img-fluid rounded" 
                     style="max-height: 400px; width: 100%; object-fit: cover;">
            </div>
            @endif

            <!-- Progress Donasi -->
            <div class="activity-card mb-4">
                <div class="card-header">
                    <h3><i class="fas fa-chart-line me-2"></i>Progress Donasi</h3>
                    <div class="text-end">
                        <h4 class="mb-0">Rp {{ number_format($kampanye->dana_terkumpul, 0, ',', '.') }}</h4>
                        <small class="text-muted">terkumpul dari Rp {{ number_format($kampanye->target_dana, 0, ',', '.') }}</small>
                    </div>
                </div>
                <div class="p-3">
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar" role="progressbar" 
                             style="width: {{ $kampanye->progress }}%; background-color: var(--primary-color);" 
                             aria-valuenow="{{ $kampanye->progress }}" 
                             aria-valuemin="0" aria-valuemax="100">
                            {{ number_format($kampanye->progress, 1) }}%
                        </div>
                    </div>
                    <div class="row mt-3 text-center">
                        <div class="col-md-4">
                            <div class="p-3 rounded" style="background-color: var(--light-bg);">
                                <h5 class="fw-bold mb-1">{{ number_format($kampanye->progress, 1) }}%</h5>
                                <small class="text-muted">Progress</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 rounded" style="background-color: var(--light-bg);">
                                <h5 class="fw-bold mb-1">{{ $kampanye->jumlah_donatur }}</h5>
                                <small class="text-muted">Donatur</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 rounded" style="background-color: var(--light-bg);">
                                <h5 class="fw-bold mb-1">{{ $kampanye->hari_tersisa }} hari</h5>
                                <small class="text-muted">Sisa Waktu</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Deskripsi -->
            <div class="activity-card mb-4">
                <div class="card-header">
                    <h3><i class="fas fa-align-left me-2"></i>Deskripsi Kampanye</h3>
                </div>
                <div class="p-3">
                    <h5 class="mb-3" style="color: var(--primary-color);">{{ $kampanye->deskripsi_singkat }}</h5>
                    <div class="mb-4">
                        {!! nl2br(e($kampanye->deskripsi)) !!}
                    </div>
                    
                    @if($kampanye->cerita_lengkap)
                    <h5 class="mt-4 mb-3" style="color: var(--primary-color);">Cerita Lengkap</h5>
                    <div class="mb-4">
                        {!! nl2br(e($kampanye->cerita_lengkap)) !!}
                    </div>
                    @endif
                    
                    @if($kampanye->terima_kasih_pesan)
                    <div class="alert alert-success mt-3">
                        <h6><i class="fas fa-heart me-2"></i>Pesan Terima Kasih:</h6>
                        <p class="mb-0">{!! nl2br(e($kampanye->terima_kasih_pesan)) !!}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Galeri -->
            @if($kampanye->galeri && count(json_decode($kampanye->galeri, true)) > 0)
            <div class="activity-card mb-4">
                <div class="card-header">
                    <h3><i class="fas fa-images me-2"></i>Galeri</h3>
                </div>
                <div class="p-3">
                    <div class="row g-3">
                        @foreach(json_decode($kampanye->galeri, true) as $index => $gambar)
                        <div class="col-md-4 col-sm-6">
                            <img src="{{ asset('storage/' . $gambar) }}" 
                                 alt="Galeri {{ $index + 1 }}" 
                                 class="img-fluid rounded" 
                                 style="height: 200px; width: 100%; object-fit: cover;"
                                 data-bs-toggle="modal" 
                                 data-bs-target="#galleryModal"
                                 onclick="showGalleryImage('{{ asset('storage/' . $gambar) }}')">
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Kolom Kanan - Info Tambahan -->
        <div class="col-md-4">
            <!-- Info Singkat -->
            <div class="activity-card mb-4">
                <div class="card-header">
                    <h3><i class="fas fa-info-circle me-2"></i>Informasi</h3>
                </div>
                <div class="p-3">
                    <div class="mb-3">
                        <h6 class="text-muted mb-2">Slug URL</h6>
                        <div class="d-flex align-items-center">
                            <code class="bg-light p-2 rounded">{{ $kampanye->slug }}</code>
                            <button class="btn btn-sm btn-outline-secondary ms-2" onclick="copyToClipboard('{{ $kampanye->slug }}')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="text-muted mb-2">Tanggal Kampanye</h6>
                        <div class="d-flex justify-content-between">
                            <div>
                                <small>Mulai</small>
                                <p class="mb-0 fw-bold">{{ $kampanye->tanggal_mulai->format('d M Y') }}</p>
                            </div>
                            <div class="text-end">
                                <small>Selesai</small>
                                <p class="mb-0 fw-bold">{{ $kampanye->tanggal_selesai->format('d M Y') }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="text-muted mb-2">Status</h6>
                        <form action="{{ route('admin.kampanye.updateStatus', $kampanye) }}" method="POST" class="d-flex gap-2">
                            @csrf
                            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="draft" {{ $kampanye->status == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="aktif" {{ $kampanye->status == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="selesai" {{ $kampanye->status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                <option value="ditutup" {{ $kampanye->status == 'ditutup' ? 'selected' : '' }}>Ditutup</option>
                            </select>
                            <button type="submit" class="btn btn-sm btn-primary">Update</button>
                        </form>
                    </div>
                    
                    @if($kampanye->datalansia)
                    <div class="mb-3">
                        <h6 class="text-muted mb-2">Penerima Manfaat</h6>
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" 
                                 style="width: 40px; height: 40px;">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="ms-3">
                                <p class="mb-0 fw-bold">{{ $kampanye->datalansia->nama }}</p>
                                <small class="text-muted">{{ $kampanye->datalansia->nik }}</small>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <div class="mb-3">
                        <h6 class="text-muted mb-2">Statistik</h6>
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="p-2 rounded" style="background-color: var(--light-bg);">
                                    <p class="mb-0 fw-bold">{{ $kampanye->jumlah_dilihat }}</p>
                                    <small class="text-muted">Dilihat</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 rounded" style="background-color: var(--light-bg);">
                                    <p class="mb-0 fw-bold">{{ $kampanye->jumlah_donatur }}</p>
                                    <small class="text-muted">Donatur</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Donasi Terbaru -->
            <div class="activity-card mb-4">
                <div class="card-header">
                    <h3><i class="fas fa-history me-2"></i>Donasi Terbaru</h3>
                    <a href="{{ route('admin.donasi.index') }}?kampanye={{ $kampanye->id }}" class="btn btn-sm btn-outline-primary">
                        Lihat Semua
                    </a>
                </div>
                <div class="p-3" style="max-height: 400px; overflow-y: auto;">
                    @if($kampanye->donasis->isEmpty())
                    <div class="text-center py-3">
                        <i class="fas fa-inbox fa-2x mb-2 text-muted"></i>
                        <p class="text-muted mb-0">Belum ada donasi</p>
                    </div>
                    @else
                    <div class="activity-list">
                        @foreach($kampanye->donasis->take(10) as $donasi)
                        <div class="activity-item">
                            <div class="activity-icon" style="background-color: rgba(124, 179, 66, 0.1); color: var(--success-color);">
                                <i class="fas fa-donate"></i>
                            </div>
                            <div class="activity-content">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="activity-title">
                                        @if($donasi->anonim)
                                        <span class="text-muted">Donatur Anonim</span>
                                        @else
                                        {{ $donasi->nama_donatur }}
                                        @endif
                                    </div>
                                    <div class="fw-bold" style="color: var(--success-color);">
                                        Rp {{ number_format($donasi->jumlah, 0, ',', '.') }}
                                    </div>
                                </div>
                                <div class="activity-time">
                                    <i class="far fa-clock me-1"></i>{{ $donasi->created_at->diffForHumans() }}
                                    @if($donasi->doa_harapan)
                                    <span class="ms-2"><i class="fas fa-pray"></i></span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="activity-card">
                <div class="card-header">
                    <h3><i class="fas fa-bolt me-2"></i>Quick Actions</h3>
                </div>
                <div class="p-3">
                    <div class="d-grid gap-2">
                        <a href="#" class="btn btn-outline-primary" onclick="copyDonationLink()">
                            <i class="fas fa-link me-2"></i>Copy Link Donasi
                        </a>
                        <a href="{{ route('admin.donasi.index') }}?kampanye={{ $kampanye->id }}" class="btn btn-outline-success">
                            <i class="fas fa-list me-2"></i>Kelola Donasi
                        </a>
                        <button class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#shareModal">
                            <i class="fas fa-share-alt me-2"></i>Bagikan
                        </button>
                        <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="fas fa-trash me-2"></i>Hapus Kampanye
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Share -->
<div class="modal fade" id="shareModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bagikan Kampanye</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Link Kampanye</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="shareLink" 
                               value="{{ route('admin.donasi.show', $kampanye->slug) }}" readonly>
                        <button class="btn btn-primary" onclick="copyShareLink()">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
                <div class="text-center">
                    <h6>Bagikan ke:</h6>
                    <div class="d-flex justify-content-center gap-3 mt-3">
                        <a href="#" class="btn btn-primary rounded-circle" style="width: 50px; height: 50px;">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="btn btn-info rounded-circle" style="width: 50px; height: 50px;">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="btn btn-success rounded-circle" style="width: 50px; height: 50px;">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                        <a href="#" class="btn btn-danger rounded-circle" style="width: 50px; height: 50px;">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Hapus -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus kampanye ini?</p>
                <p><strong>"{{ $kampanye->judul }}"</strong></p>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Peringatan:</strong> Semua data donasi yang terkait akan ikut terhapus!
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form action="{{ route('admin.kampanye.destroy', $kampanye) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Gallery -->
<div class="modal fade" id="galleryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body text-center">
                <img id="galleryImage" src="" alt="" class="img-fluid">
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            alert('Berhasil disalin ke clipboard!');
        });
    }
    
    function copyDonationLink() {
        const link = '{{ route('admin.donasi.show', $kampanye->slug) }}';
        copyToClipboard(link);
    }
    
    function copyShareLink() {
        const input = document.getElementById('shareLink');
        input.select();
        document.execCommand('copy');
        alert('Link berhasil disalin!');
    }
    
    function showGalleryImage(src) {
        document.getElementById('galleryImage').src = src;
    }
    
    // Update progress bar saat page load
    document.addEventListener('DOMContentLoaded', function() {
        const progress = {{ $kampanye->progress }};
        const progressBar = document.querySelector('.progress-bar');
        if (progressBar) {
            progressBar.style.width = `${progress}%`;
            progressBar.textContent = `${progress.toFixed(1)}%`;
        }
    });
</script>
@endpush
@endsection