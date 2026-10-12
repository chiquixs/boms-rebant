<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            BaseDataSeeder::class,
            RebantCatalogSeeder::class, // butuh minimal satu user role admin
        ]);
    }
}