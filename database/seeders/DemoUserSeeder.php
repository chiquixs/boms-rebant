<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class DemoUserSeeder extends Seeder
{
    /**
     * Satu akun per role. Password dibaca dari .env (SEED_USER_PASSWORD), tidak ditulis di kode.
     * Ganti email di bawah dengan email asli sebelum menjalankan.
     */
    public function run(): void
    {
        $password = env('SEED_USER_PASSWORD');
        if (! $password) {
            throw new RuntimeException('Isi SEED_USER_PASSWORD di .env terlebih dulu (lalu php artisan config:clear).');
        }

        $accounts = [
            ['owner', 'Owner REBANT', 'owner@rebant.test'],
            ['admin', 'Admin REBANT', 'admin@rebant.test'],
            ['production', 'Production REBANT', 'production@rebant.test'],
        ];

        foreach ($accounts as [$role, $name, $email]) {
            if (DB::table('users')->where('email', $email)->exists()) {
                continue;
            }

            $roleId = DB::table('roles')->where('name', $role)->value('id');
            if (! $roleId) {
                throw new RuntimeException("Role {$role} belum ada. Jalankan BaseDataSeeder dulu.");
            }

            DB::table('users')->insert([
                'role_id' => $roleId,
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
