<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            // Admin
            [
                'name' => 'Admin Utama',
                'email' => 'admin@carelansia.com',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'no_telepon' => '081234567890',
                'alamat' => 'Jl. Admin No. 1, Jakarta'
            ],
            
            // Perawat - EMAIL DIUBAH KARENA SUDAH ADA DI DATAPERAWAT
            [
                'name' => 'Perawat Sri Handayani',
                'email' => 'sri.perawat.user@carelansia.com', // EMAIL DIUBAH
                'password' => Hash::make('password123'),
                'role' => 'perawat',
                'no_telepon' => '081234567891',
                'alamat' => 'Jl. Perawat No. 10, Jakarta'
            ],
            [
                'name' => 'Perawat Budi Santoso',
                'email' => 'budi.perawat.user@carelansia.com', // EMAIL DIUBAH
                'password' => Hash::make('password123'),
                'role' => 'perawat',
                'no_telepon' => '081234567892',
                'alamat' => 'Jl. Sehat No. 15, Jakarta'
            ],
            
            // Keluarga - EMAIL DIUBAH KARENA SUDAH ADA DI DATALANSIA
            [
                'name' => 'Keluarga Ahmad Wijaya',
                'email' => 'ahmad.keluarga.user@carelansia.com', // EMAIL DIUBAH
                'password' => Hash::make('password123'),
                'role' => 'keluarga',
                'no_telepon' => '081234567893',
                'alamat' => 'Jl. Keluarga No. 20, Jakarta'
            ],
            [
                'name' => 'Keluarga Sari Dewi',
                'email' => 'sari.keluarga.user@carelansia.com', // EMAIL DIUBAH
                'password' => Hash::make('password123'),
                'role' => 'keluarga',
                'no_telepon' => '081234567894',
                'alamat' => 'Jl. Bahagia No. 25, Jakarta'
            ],
            [
                'name' => 'Keluarga Rina Handayani',
                'email' => 'rina.handayani@carelansia.com', // EMAIL BARU
                'password' => Hash::make('password123'),
                'role' => 'keluarga',
                'no_telepon' => '081234567895',
                'alamat' => 'Jl. Kenanga No. 78, Jakarta Barat'
            ]
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}