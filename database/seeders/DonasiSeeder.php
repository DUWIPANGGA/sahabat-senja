<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Donasi;
use App\Models\User;
use App\Models\KampanyeDonasi;
use App\Models\DataLansia;

class DonasiSeeder extends Seeder
{
    public function run()
    {
        $users = User::inRandomOrder()->take(5)->get();
        $kampanye = KampanyeDonasi::inRandomOrder()->first();
        $lansia = DataLansia::inRandomOrder()->first();

        // Jika data wajib belum ada, jangan seed
        if (!$users->count() || !$kampanye || !$lansia) {
            return;
        }

        // Contoh data donasi
        $donasiList = [
            [
                'jumlah' => 50000,
                'metode_pembayaran' => 'transfer',
                'status' => 'pending',
                'nama_donatur' => 'Ahmad Setiawan',
                'email' => 'ahmad@example.com',
                'telepon' => '081212345678',
                'anonim' => false,
                'keterangan' => 'Semoga bermanfaat.',
                'doa_harapan' => 'Semoga sehat selalu.',
            ],
            [
                'jumlah' => 150000,
                'metode_pembayaran' => 'transfer',
                'status' => 'success',
                'nama_donatur' => 'Donatur Anonim',
                'email' => 'anonim@example.com',
                'telepon' => '089999888777',
                'anonim' => true,
                'keterangan' => null,
                'doa_harapan' => 'Tetap semangat!',
            ],
            [
                'jumlah' => 100000,
                'metode_pembayaran' => 'qris',
                'status' => 'success',
                'nama_donatur' => 'Siti Mariam',
                'email' => 'siti@example.com',
                'telepon' => '082134567890',
                'anonim' => false,
                'keterangan' => 'Ikhlas karena Allah',
                'doa_harapan' => 'Semoga diberkahi.',
            ],
        ];

        foreach ($donasiList as $d) {
            Donasi::create([
                'kampanye_donasi_id' => $kampanye->id,
                'user_id' => $users->random()->id,
                'datalansia_id' => $lansia->id,
                'jumlah' => $d['jumlah'],
                'metode_pembayaran' => $d['metode_pembayaran'],
                'status' => $d['status'],
                'nama_donatur' => $d['nama_donatur'],
                'email' => $d['email'],
                'telepon' => $d['telepon'],
                'anonim' => $d['anonim'],
                'keterangan' => $d['keterangan'],
                'doa_harapan' => $d['doa_harapan'],
                'bukti_pembayaran' => null, // kosongkan
            ]);
        }
    }
}
