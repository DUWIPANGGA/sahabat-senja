<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kamar;
use App\Models\Datalansia;
use App\Models\JadwalObat;
use App\Models\Dataperawat;
use App\Models\JadwalAktivitas;
use Illuminate\Support\Facades\Request;
class DashboardController extends Controller
{
    public function adminDashboard()
    {
        $data = [
            'total_lansia' => Datalansia::count(),
            'total_perawat' => User::where('role', 'perawat')->count(),
            'total_kamar' => Kamar::count(),
            'kamar_terisi' => Kamar::whereHas('lansia')->count(),
            'obat_minggu_ini' => JadwalObat::whereBetween('created_at', 
                [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'kegiatan_terdekat' => JadwalAktivitas::whereDate('tanggal', '>=', now())
                ->orderBy('tanggal')->limit(5)->get(),
        ];
        
        return response()->json(['status' => 'success', 'data' => $data]);
    }
    
    public function perawatDashboard(Request $request)
    {
        $user = $request->user();
        
        // Hanya lansia di kamar yang ditugaskan
        $kamar = Kamar::where('perawat_id', $user->id)->first();
        $lansia = $kamar ? $kamar->lansia : collect();
        
        $data = [
            'total_lansia' => $lansia->count(),
            'lansia_list' => $lansia,
            'reminder_pemeriksaan' => $this->getReminderPemeriksaan($lansia),
            'jadwal_kegiatan' => JadwalAktivitas::whereIn('datalansia_id', $lansia->pluck('id'))
                ->whereDate('tanggal', now())->get(),
            'jadwal_obat' => JadwalObat::whereIn('datalansia_id', $lansia->pluck('id'))
                ->whereDate('tanggal_mulai', '<=', now())
                ->where(function($q) {
                    $q->whereNull('tanggal_selesai')
                      ->orWhere('tanggal_selesai', '>=', now());
                })->get(),
        ];
        
        return response()->json(['status' => 'success', 'data' => $data]);
    }
}