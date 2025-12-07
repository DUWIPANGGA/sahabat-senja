<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KampanyeDonasi;
use Carbon\Carbon;

class KampanyeDonasiSeeder extends Seeder
{
    public function run()
    {
        $kampanyes = [
            [
                'judul' => 'Bantu Lansia Jompo Tinggal Sendiri',
                'slug' => 'bantu-lansia-jompo-tinggal-sendiri',
                'deskripsi_singkat' => 'Bantu kebutuhan sehari-hari lansia yang tinggal sendiri tanpa keluarga',
                'deskripsi' => 'Banyak lansia di sekitar kita yang harus hidup sendiri tanpa dukungan keluarga. Mereka membutuhkan bantuan untuk memenuhi kebutuhan pokok seperti makanan, obat-obatan, dan perawatan kesehatan.',
                'target_dana' => 50000000,
                'dana_terkumpul' => 12500000,
                'tanggal_mulai' => Carbon::now()->subDays(10),
                'tanggal_selesai' => Carbon::now()->addDays(50),
                'kategori' => 'lansia',
                'status' => 'aktif',
                'is_featured' => true,
            ],
            [
                'judul' => 'Operasi Katarak untuk Nenek Sumirah',
                'slug' => 'operasi-katarak-untuk-nenek-sumirah',
                'deskripsi_singkat' => 'Bantu biaya operasi katarak agar nenek Sumirah bisa melihat kembali',
                'deskripsi' => 'Nenek Sumirah, 75 tahun, mengalami katarak yang semakin parah. Dokter mengatakan beliau perlu operasi segera agar tidak buta permanen. Biaya operasi sangat mahal dan tidak terjangkau oleh keluarga.',
                'target_dana' => 15000000,
                'dana_terkumpul' => 7500000,
                'tanggal_mulai' => Carbon::now()->subDays(5),
                'tanggal_selesai' => Carbon::now()->addDays(30),
                'kategori' => 'kesehatan',
                'status' => 'aktif',
                'is_featured' => false,
            ],
        ];

        foreach ($kampanyes as $kampanye) {
            KampanyeDonasi::create($kampanye);
        }
    }
}