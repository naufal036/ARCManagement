<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Buat role Admin utama
        $adminRoleId = DB::table('roles')->insertGetId([
            'name' => 'Admin',
            'description' => 'Administrator Sistem',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Buat role lainnya agar langsung tersedia di dropdown
        DB::table('roles')->insert([
            [
                'name' => 'Manajer',
                'description' => 'Manajer Proyek',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Karyawan',
                'description' => 'Staf Proyek',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 3. Buat akun Admin utama
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin123'),
            'role_id' => $adminRoleId,
            'phone' => '081234567890',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
    }
}
