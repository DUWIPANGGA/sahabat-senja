<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class IuranBulanan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'iuran_bulanan';
    
    protected $fillable = [
        'user_id',
        'datalansia_id',
        'nama_iuran',
        'deskripsi',
        'jumlah',
        'periode',
        'tanggal_jatuh_tempo',
        'tanggal_bayar',
        'status',
        'metode_pembayaran',
        'bukti_pembayaran',
        'catatan_admin',
        'is_otomatis',
        'interval_bulan',
        'berlaku_dari',
        'berlaku_sampai',
        'kode_iuran',
        'metadata'
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'tanggal_jatuh_tempo' => 'date',
        'tanggal_bayar' => 'date',
        'berlaku_dari' => 'date',
        'berlaku_sampai' => 'date',
        'is_otomatis' => 'boolean',
        'metadata' => 'array'
    ];

    /**
     * Generate kode iuran otomatis
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($iuran) {
            if (empty($iuran->kode_iuran)) {
                $iuran->kode_iuran = 'IUR-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -8));
            }
            
            // Jika iuran otomatis, set periode berdasarkan tanggal jatuh tempo
            if ($iuran->is_otomatis && empty($iuran->periode)) {
                $iuran->periode = Carbon::parse($iuran->tanggal_jatuh_tempo)->format('Y-m');
            }
        });
    }

    /**
     * Relasi ke User (keluarga yang membayar)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke DataLansia
     */
    public function datalansia()
    {
        return $this->belongsTo(Datalansia::class);
    }

    /**
     * Cek apakah iuran sudah jatuh tempo
     */
    public function getIsTerlambatAttribute()
    {
        return $this->status == 'pending' && 
               Carbon::now()->gt(Carbon::parse($this->tanggal_jatuh_tempo));
    }

    /**
     * Hitung denda jika terlambat
     */
    public function getDendaAttribute()
    {
        if (!$this->is_terlambat) return 0;
        
        $hari_terlambat = Carbon::now()->diffInDays(Carbon::parse($this->tanggal_jatuh_tempo));
        $denda_per_hari = $this->jumlah * 0.002; // 0.2% per hari
        $total_denda = $denda_per_hari * $hari_terlambat;
        
        // Maksimal denda 10% dari jumlah iuran
        $max_denda = $this->jumlah * 0.1;
        
        return min($total_denda, $max_denda);
    }

    /**
     * Total yang harus dibayar (iuran + denda)
     */
    public function getTotalBayarAttribute()
    {
        return $this->jumlah + $this->denda;
    }

    /**
     * Format jumlah iuran
     */
    public function getJumlahFormattedAttribute()
    {
        return 'Rp ' . number_format($this->jumlah, 0, ',', '.');
    }

    /**
     * Format total bayar
     */
    public function getTotalBayarFormattedAttribute()
    {
        return 'Rp ' . number_format($this->total_bayar, 0, ',', '.');
    }

    /**
     * Cek apakah iuran bisa dibayar
     */
    public function getIsPayableAttribute()
    {
        return $this->status == 'pending';
    }

    /**
     * Scope untuk iuran aktif (pending atau terlambat)
     */
    public function scopeAktif($query)
    {
        return $query->whereIn('status', ['pending', 'terlambat']);
    }

    /**
     * Scope untuk iuran berdasarkan periode
     */
    public function scopePeriode($query, $periode)
    {
        return $query->where('periode', $periode);
    }

    /**
     * Scope untuk iuran berdasarkan user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope untuk iuran berdasarkan lansia
     */
    public function scopeByLansia($query, $lansiaId)
    {
        return $query->where('datalansia_id', $lansiaId);
    }

    /**
     * Generate iuran untuk bulan berikutnya (untuk recurring)
     */
    public function generateNextIuran()
    {
        if (!$this->is_otomatis) return null;

        $nextDate = Carbon::parse($this->tanggal_jatuh_tempo)->addMonths($this->interval_bulan);
        
        // Cek apakah sudah melewati tanggal berlaku
        if ($this->berlaku_sampai && $nextDate->gt(Carbon::parse($this->berlaku_sampai))) {
            return null;
        }

        return self::create([
            'user_id' => $this->user_id,
            'datalansia_id' => $this->datalansia_id,
            'nama_iuran' => $this->nama_iuran,
            'deskripsi' => $this->deskripsi,
            'jumlah' => $this->jumlah,
            'periode' => $nextDate->format('Y-m'),
            'tanggal_jatuh_tempo' => $nextDate->format('Y-m-d'),
            'status' => 'pending',
            'is_otomatis' => true,
            'interval_bulan' => $this->interval_bulan,
            'berlaku_dari' => $this->berlaku_dari,
            'berlaku_sampai' => $this->berlaku_sampai,
        ]);
    }

    /**
     * Proses pembayaran iuran
     */
    public function prosesPembayaran($metode, $bukti = null)
    {
        $this->update([
            'status' => 'lunas',
            'metode_pembayaran' => $metode,
            'bukti_pembayaran' => $bukti,
            'tanggal_bayar' => Carbon::now(),
        ]);

        // Generate iuran berikutnya jika recurring
        if ($this->is_otomatis) {
            $this->generateNextIuran();
        }

        return $this;
    }
}