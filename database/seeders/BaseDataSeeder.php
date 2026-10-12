<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BaseDataSeeder extends Seeder
{
    /** Role dan platform dasar. Aman dijalankan berulang: data yang sudah ada dilewati. */
    public function run(): void
    {
        foreach (['owner', 'admin', 'production'] as $name) {
            if (! DB::table('roles')->where('name', $name)->exists()) {
                DB::table('roles')->insert(['name' => $name, 'created_at' => now(), 'updated_at' => now()]);
            }
        }

        foreach (['Shopee', 'TikTok Shop'] as $name) {
            if (! DB::table('platforms')->where('name', $name)->exists()) {
                DB::table('platforms')->insert(['name' => $name, 'created_at' => now(), 'updated_at' => now()]);
            }
        }
    }
}
