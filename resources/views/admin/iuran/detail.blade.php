<div class="row">
    <div class="col-md-12">
        <div class="mb-3">
            <h4 class="fw-bold mb-3" style="color: var(--primary-color);">{{ $iuran->nama_iuran }}</h4>
            <div class="d-flex flex-wrap gap-2 mb-3">
                <span class="badge bg-primary">{{ $iuran->kode_iuran }}</span>
                @if($iuran->is_otomatis)
                <span class="badge bg-info">Otomatis</span>
                @endif
                <span class="badge bg-{{ $iuran->status == 'lunas' ? 'success' : ($iuran->status == 'terlambat' ? 'danger' : 'warning') }}">
                    {{ ucfirst($iuran->status) }}
                </span>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <h6 class="text-muted mb-2">Informasi Iuran</h6>
        <table class="table table-sm">
            <tr>
                <td width="40%"><strong>Jumlah</strong></td>
                <td>Rp {{ number_format($iuran->jumlah, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>Periode</strong></td>
                <td>{{ \Carbon\Carbon::parse($iuran->periode . '-01')->format('F Y') }}</td>
            </tr>
            <tr>
                <td><strong>Jatuh Tempo</strong></td>
                <td>{{ $iuran->tanggal_jatuh_tempo->format('d F Y') }}</td>
            </tr>
            @if($iuran->is_otomatis)
            <tr>
                <td><strong>Interval</strong></td>
                <td>Setiap {{ $iuran->interval_bulan }} bulan</td>
            </tr>
            @endif
        </table>
    </div>
    
    <div class="col-md-6">
        <h6 class="text-muted mb-2">Informasi Pembayar</h6>
        <table class="table table-sm">
            @if($iuran->datalansia)
            <tr>
                <td width="40%"><strong>Nama Lansia</strong></td>
                <td>{{ $iuran->datalansia->nama_lansia }}</td>
            </tr>
            @endif
            @if($iuran->user)
            <tr>
                <td><strong>Nama Keluarga</strong></td>
                <td>{{ $iuran->user->name }}</td>
            </tr>
            <tr>
                <td><strong>Email</strong></td>
                <td>{{ $iuran->user->email }}</td>
            </tr>
            @endif
        </table>
    </div>
</div>

@if($iuran->is_terlambat)
<div class="row">
    <div class="col-md-12">
        <div class="alert alert-danger">
            <h6><i class="fas fa-exclamation-triangle me-2"></i>Iuran Terlambat</h6>
            <p class="mb-1">Terlambat: {{ \Carbon\Carbon::now()->diffInDays($iuran->tanggal_jatuh_tempo) }} hari</p>
            <p class="mb-0">Denda: Rp {{ number_format($iuran->denda, 0, ',', '.') }}</p>
            <p class="mb-0 fw-bold">Total yang harus dibayar: Rp {{ number_format($iuran->total_bayar, 0, ',', '.') }}</p>
        </div>
    </div>
</div>
@endif

@if($iuran->status == 'lunas')
<div class="row">
    <div class="col-md-12">
        <div class="alert alert-success">
            <h6><i class="fas fa-check-circle me-2"></i>Iuran Lunas</h6>
            <p class="mb-1">Tanggal Bayar: {{ $iuran->tanggal_bayar->format('d F Y H:i') }}</p>
            <p class="mb-0">Metode: {{ $iuran->metode_pembayaran ?? 'Manual' }}</p>
        </div>
    </div>
</div>
@endif

@if($iuran->deskripsi)
<div class="row">
    <div class="col-md-12">
        <h6 class="text-muted mb-2">Deskripsi</h6>
        <p class="mb-0">{{ $iuran->deskripsi }}</p>
    </div>
</div>
@endif

@if($iuran->catatan_admin)
<div class="row">
    <div class="col-md-12">
        <h6 class="text-muted mb-2">Catatan Admin</h6>
        <div class="bg-light p-3 rounded">
            {!! nl2br(e($iuran->catatan_admin)) !!}
        </div>
    </div>
</div>
@endif

<div class="row mt-3">
    <div class="col-md-12 text-end">
        <small class="text-muted">
            Dibuat: {{ $iuran->created_at->format('d/m/Y H:i') }} | 
            Terakhir diupdate: {{ $iuran->updated_at->format('d/m/Y H:i') }}
        </small>
    </div>
</div>