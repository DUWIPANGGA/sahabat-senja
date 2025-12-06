<?php

namespace Database\Seeders;
use App\Models\DataPerawat;
use Illuminate\Database\Seeder;

class DataPerawatSeeder extends Seeder
{
    public function run(): void
    {




        $perawat = [
            [
                'nama' => 'Sri Handayani, S.Kep',
                'email' => 'sri.perawat@carelansia.com',
                'alamat' => 'Jl. Perawat No. 10, Jakarta Pusat',
                'no_hp' => '081234567891',
                'jenis_kelamin' => 'Perempuan'
            ],
            [
                'nama' => 'Budi Santoso, S.Kep',
                'email' => 'budi.perawat@carelansia.com',
                'alamat' => 'Jl. Sehat No. 15, Jakarta Selatan',
                'no_hp' => '081234567892',
                'jenis_kelamin' => 'Laki-laki'
            ],
            [
                'nama' => 'Dewi Anggraeni, S.Kep',
                'email' => 'dewi.perawat@carelansia.com',
                'alamat' => 'Jl. Bahagia No. 20, Jakarta Timur',
                'no_hp' => '081234567896',
                'jenis_kelamin' => 'Perempuan'
            ],
            [
                'nama' => 'Ahmad Fauzi, S.Kep',
                'email' => 'ahmad.perawat@carelansia.com',
                'alamat' => 'Jl. Sejahtera No. 25, Jakarta Barat',
                'no_hp' => '081234567897',
                'jenis_kelamin' => 'Laki-laki'
            ]
        ];

        foreach ($perawat as $data) {
            DataPerawat::create($data);
        }
    }
}