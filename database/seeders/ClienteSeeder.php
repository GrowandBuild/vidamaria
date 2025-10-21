<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cliente;

class ClienteSeeder extends Seeder
{
    public function run()
    {
        $clientes = [
            [
                'nome' => 'Ana Silva',
                'email' => 'ana.silva@email.com',
                'telefone' => '11999887766',
                'observacoes' => 'Cliente preferencial - sempre pontual',
            ],
            [
                'nome' => 'Maria Santos',
                'email' => 'maria.santos@email.com',
                'telefone' => '11988776655',
                'observacoes' => 'Gosta de cores vibrantes',
            ],
            [
                'nome' => 'Joana Costa',
                'email' => 'joana.costa@email.com',
                'telefone' => '11977665544',
                'observacoes' => 'Alérgica a esmaltes com formol',
            ],
            [
                'nome' => 'Carla Oliveira',
                'email' => 'carla.oliveira@email.com',
                'telefone' => '11966554433',
                'observacoes' => 'Prefere unhas curtas',
            ],
            [
                'nome' => 'Fernanda Lima',
                'email' => 'fernanda.lima@email.com',
                'telefone' => '11955443322',
                'observacoes' => 'Cliente VIP - desconto especial',
            ],
        ];

        foreach ($clientes as $cliente) {
            Cliente::create($cliente);
        }
    }
}
