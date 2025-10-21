<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar usuário proprietária
        User::create([
            'name' => 'Val',
            'email' => 'val@vidamaria.com.br',
            'password' => Hash::make('admin123'),
            'tipo' => 'proprietaria',
        ]);
    }
}

