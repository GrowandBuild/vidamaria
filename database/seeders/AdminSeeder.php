<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        // Conta da Proprietária (Val)
        User::updateOrCreate(
            ['email' => 'val@vidamaria.com.br'],
            [
                'name' => 'Val',
                'email' => 'val@vidamaria.com.br',
                'password' => Hash::make('admin123'),
                'tipo' => 'proprietaria',
                'email_verified_at' => now(),
            ]
        );

        // Conta do Desenvolvedor (Alexandre)
        User::updateOrCreate(
            ['email' => 'alexandre@dev.com'],
            [
                'name' => 'Alexandre Desenvolvedor',
                'email' => 'alexandre@dev.com',
                'password' => Hash::make('dev123'),
                'tipo' => 'proprietaria', // Acesso total para desenvolvimento
                'email_verified_at' => now(),
            ]
        );

        // Profissional de exemplo (Maria)
        $maria = User::updateOrCreate(
            ['email' => 'maria@vidamaria.com.br'],
            [
                'name' => 'Maria Silva',
                'email' => 'maria@vidamaria.com.br',
                'password' => Hash::make('maria123'),
                'tipo' => 'profissional',
                'email_verified_at' => now(),
            ]
        );

        // Criar perfil profissional para Maria
        if ($maria) {
            $maria->profissional()->updateOrCreate(
                ['user_id' => $maria->id],
                [
                    'telefone' => '11999887766',
                    'percentual_comissao' => 50.0,
                    'ativo' => true,
                ]
            );
        }
    }
}
