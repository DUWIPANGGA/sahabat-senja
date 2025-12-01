<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\DataLansiaSeeder;
use Database\Seeders\DataperawatSeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            DataperawatSeeder::class,
            DataLansiaSeeder::class,
        ]);
    }
}