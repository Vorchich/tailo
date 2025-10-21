<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate([
            'role' => 'admin',
            'name' => 'Admin Redwing',
            'email' => 'red@wing.com',
        ], [
            'permission' => 'admin',
            'password' => Hash::make('redPasswordWing'),
            'email_verified_at' => now(),
        ]);

        $admin = User::firstOrCreate([
            'role' => 'admin',
            'name' => 'Admin',
            'email' => 'admin@admin.com',
        ], [
            'permission' => 'admin',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
    }
}
