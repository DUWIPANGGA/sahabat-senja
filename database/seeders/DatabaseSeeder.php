<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\DataLansiaSeeder;
use Database\Seeders\DataPerawatSeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            DataPerawatSeeder::class,
            DatalansiaSeeder::class,
        ]);
    }
}