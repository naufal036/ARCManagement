<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $roleAdmin = \App\Models\Role::create(['nama_role' => 'Admin']);
        \App\Models\Role::create(['nama_role' => 'Direktur']);
        \App\Models\Role::create(['nama_role' => 'Staf']);

        \App\Models\User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('admin123'),
            'role_id' => $roleAdmin->id,
        ]);
    }
}
