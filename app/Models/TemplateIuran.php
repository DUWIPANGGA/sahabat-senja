<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TemplateIuran extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'template_iuran';
    
    protected $fillable = [
        'nama_template',
        'deskripsi',
        'jumlah',
        'target_type',
        'target_ids',
        'is_recurring',
        'interval_bulan',
        'tanggal_jatuh_tempo',
        'include_denda',
        'persentase_denda',
        'metadata',
        'is_active'
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'target_ids' => 'array',
        'is_recurring' => 'boolean',
        'include_denda' => 'boolean',
        'persentase_denda' => 'decimal:2',
        'metadata' => 'array',
        'is_active' => 'boolean'
    ];

    /**
     * Generate iuran dari template untuk bulan tertentu
     */
    public function generateIuranForMonth($year, $month)
    {
        $tanggalJatuhTempo = Carbon::create($year, $month, $this->tanggal_jatuh_tempo);
        
        // Sesuaikan jika tanggal > hari terakhir bulan
        if ($tanggalJatuhTempo->day > $tanggalJatuhTempo->daysInMonth) {
            $tanggalJatuhTempo = $tanggalJatuhTempo->endOfMonth();
        }

        $periode = $tanggalJatuhTempo->format('Y-m');
        
        // Cek target berdasarkan type
        switch ($this->target_type) {
            case 'semua':
                $targets = $this->getAllTargets();
                break;
            case 'per_kamar':
                $targets = $this->getKamarTargets();
                break;
            case 'per_lansia':
                $targets = $this->getLansiaTargets();
                break;
            case 'per_user':
                $targets = $this->getUserTargets();
                break;
            default:
                $targets = [];
        }

        $iurans = [];
        foreach ($targets as $target) {
            $iuran = IuranBulanan::create([
                'nama_iuran' => $this->nama_template,
                'deskripsi' => $this->deskripsi,
                'jumlah' => $this->jumlah,
                'periode' => $periode,
                'tanggal_jatuh_tempo' => $tanggalJatuhTempo->format('Y-m-d'),
                'status' => 'pending',
                'is_otomatis' => $this->is_recurring,
                'interval_bulan' => $this->interval_bulan,
                'berlaku_dari' => $tanggalJatuhTempo->format('Y-m-d'),
                'berlaku_sampai' => $this->is_recurring ? null : $tanggalJatuhTempo->format('Y-m-d'),
            ]);

            // Assign target berdasarkan type
            switch ($this->target_type) {
                case 'per_kamar':
                    // Cari lansia di kamar ini
                    $lansias = Datalansia::where('kamar_id', $target['id'])->get();
                    foreach ($lansias as $lansia) {
                        $iuran->update([
                            'datalansia_id' => $lansia->id,
                            'user_id' => $lansia->user_id
                        ]);
                    }
                    break;
                case 'per_lansia':
                    $iuran->update([
                        'datalansia_id' => $target['id'],
                        'user_id' => $target['user_id'] ?? null
                    ]);
                    break;
                case 'per_user':
                    $iuran->update([
                        'user_id' => $target['id']
                    ]);
                    break;
            }

            $iurans[] = $iuran;
        }

        return $iurans;
    }

    private function getAllTargets()
    {
        // Return semua lansia aktif
        return Datalansia::whereHas('kamar', function($q) {
            $q->where('status', 'terisi');
        })->get()->map(function($lansia) {
            return [
                'id' => $lansia->id,
                'user_id' => $lansia->user_id
            ];
        })->toArray();
    }

    private function getKamarTargets()
    {
        if (empty($this->target_ids)) {
            return Kamar::where('status', 'terisi')->get()->toArray();
        }
        
        return Kamar::whereIn('id', $this->target_ids)->get()->toArray();
    }

    private function getLansiaTargets()
    {
        if (empty($this->target_ids)) {
            return Datalansia::all()->map(function($lansia) {
                return [
                    'id' => $lansia->id,
                    'user_id' => $lansia->user_id
                ];
            })->toArray();
        }
        
        return Datalansia::whereIn('id', $this->target_ids)->get()
            ->map(function($lansia) {
                return [
                    'id' => $lansia->id,
                    'user_id' => $lansia->user_id
                ];
            })->toArray();
    }

    private function getUserTargets()
    {
        if (empty($this->target_ids)) {
            return User::where('role', 'keluarga')->get()->toArray();
        }
        
        return User::whereIn('id', $this->target_ids)->get()->toArray();
    }
}