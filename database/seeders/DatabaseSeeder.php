<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\DataLansiaSeeder;
use Database\Seeders\DataPerawatSeeder;
use Database\Seeders\KampanyeDonasiSeeder;
use Database\Seeders\NotificationTemplateSeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            DataPerawatSeeder::class,
            DatalansiaSeeder::class,
            KampanyeDonasiSeeder::class,
                    NotificationTemplateSeeder::class,

        ]);
    }
}