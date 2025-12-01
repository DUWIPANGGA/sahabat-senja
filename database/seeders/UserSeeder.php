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
            
            // Perawat
            [
                'name' => 'Perawat Sri Handayani',
                'email' => 'sri.perawat@carelansia.com',
                'password' => Hash::make('password123'),
                'role' => 'perawat',
                'no_telepon' => '081234567891',
                'alamat' => 'Jl. Perawat No. 10, Jakarta'
            ],
            [
                'name' => 'Perawat Budi Santoso',
                'email' => 'budi.perawat@carelansia.com',
                'password' => Hash::make('password123'),
                'role' => 'perawat',
                'no_telepon' => '081234567892',
                'alamat' => 'Jl. Sehat No. 15, Jakarta'
            ],
            
            // Keluarga
            [
                'name' => 'Keluarga Ahmad',
                'email' => 'ahmad.keluarga@carelansia.com',
                'password' => Hash::make('password123'),
                'role' => 'keluarga',
                'no_telepon' => '081234567893',
                'alamat' => 'Jl. Keluarga No. 20, Jakarta'
            ],
            [
                'name' => 'Keluarga Sari',
                'email' => 'sari.keluarga@carelansia.com',
                'password' => Hash::make('password123'),
                'role' => 'keluarga',
                'no_telepon' => '081234567894',
                'alamat' => 'Jl. Bahagia No. 25, Jakarta'
            ]
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}