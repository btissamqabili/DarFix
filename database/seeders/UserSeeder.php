<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Client Test',
            'email' => 'client@bricolink.com',
            'password' => Hash::make('password123'),
            'role' => 'client',
        ]);

        User::create([
            'name' => 'Prestataire Test',
            'email' => 'prestataire@bricolink.com',
            'password' => Hash::make('password123'),
            'role' => 'prestataire',
        ]);

        User::create([
            'name' => 'Admin Test',
            'email' => 'admin@bricolink.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);
    }
}
