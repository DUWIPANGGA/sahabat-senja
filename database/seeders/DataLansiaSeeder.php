<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Datalansia;
use Carbon\Carbon;

class DatalansiaSeeder extends Seeder
{
    public function run(): void
    {
        $lansia = [
            [
                'nama_lansia' => 'Siti Rahayu',
                'umur_lansia' => 75,
                'tempat_lahir_lansia' => 'Jakarta',
                'tanggal_lahir_lansia' => Carbon::create(1949, 5, 15),
                'jenis_kelamin_lansia' => 'Perempuan',
                'gol_darah_lansia' => 'B',
                'riwayat_penyakit_lansia' => 'Hipertensi, Diabetes',
                'alergi_lansia' => 'Udang, Kacang',
                'obat_rutin_lansia' => 'Obat hipertensi, Insulin',
                'nama_anak' => 'Ahmad Wijaya',
                'alamat_lengkap' => 'Jl. Melati No. 123, Jakarta Selatan',
                'no_hp_anak' => '081234567893',
                'email_anak' => 'ahmad.keluarga@carelansia.com'
            ],
            [
                'nama_lansia' => 'Bambang Sutrisno',
                'umur_lansia' => 82,
                'tempat_lahir_lansia' => 'Bandung',
                'tanggal_lahir_lansia' => Carbon::create(1942, 8, 22),
                'jenis_kelamin_lansia' => 'Laki-laki',
                'gol_darah_lansia' => 'O',
                'riwayat_penyakit_lansia' => 'Asam Urat, Jantung',
                'alergi_lansia' => 'Tidak ada',
                'obat_rutin_lansia' => 'Obat jantung, Allopurinol',
                'nama_anak' => 'Sari Dewi',
                'alamat_lengkap' => 'Jl. Mawar No. 45, Jakarta Timur',
                'no_hp_anak' => '081234567894',
                'email_anak' => 'sari.keluarga@carelansia.com'
            ],
            [
                'nama_lansia' => 'Marta Sari',
                'umur_lansia' => 68,
                'tempat_lahir_lansia' => 'Surabaya',
                'tanggal_lahir_lansia' => Carbon::create(1956, 3, 10),
                'jenis_kelamin_lansia' => 'Perempuan',
                'gol_darah_lansia' => 'A',
                'riwayat_penyakit_lansia' => 'Asma, Osteoporosis',
                'alergi_lansia' => 'Debu, Bulu kucing',
                'obat_rutin_lansia' => 'Inhaler, Kalsium',
                'nama_anak' => 'Rina Handayani',
                'alamat_lengkap' => 'Jl. Kenanga No. 78, Jakarta Barat',
                'no_hp_anak' => '081234567895',
                'email_anak' => 'rina@example.com'
            ]
        ];

        foreach ($lansia as $data) {
            Datalansia::create($data);
        }
    }
}