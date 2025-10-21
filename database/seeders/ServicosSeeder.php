<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Servico;

class ServicosSeeder extends Seeder
{
    public function run()
    {
        // Serviços sincronizados automaticamente em 21/10/2025 14:36:47
        // Total de serviços: 6

        Servico::updateOrCreate(
            ['nome' => 'Manicure'],
            [
                'nome' => 'Manicure',
                'descricao' => 'Manicure tradicional com esmaltação',
                'preco' => 30.00,
                'duracao_minutos' => 60,
                'ativo' => true,
            ]
        );

        Servico::updateOrCreate(
            ['nome' => 'Pedicure'],
            [
                'nome' => 'Pedicure',
                'descricao' => 'Pedicure tradicional com esmaltação',
                'preco' => 35.00,
                'duracao_minutos' => 90,
                'ativo' => true,
            ]
        );

        Servico::updateOrCreate(
            ['nome' => 'Esmaltação em Gel'],
            [
                'nome' => 'Esmaltação em Gel',
                'descricao' => 'Esmaltação em gel com durabilidade de até 3 semanas',
                'preco' => 50.00,
                'duracao_minutos' => 120,
                'ativo' => true,
            ]
        );

        Servico::updateOrCreate(
            ['nome' => 'Spa dos Pés'],
            [
                'nome' => 'Spa dos Pés',
                'descricao' => 'Tratamento completo dos pés com hidratação e esmaltação',
                'preco' => 60.00,
                'duracao_minutos' => 120,
                'ativo' => true,
            ]
        );

        Servico::updateOrCreate(
            ['nome' => 'Unhas Decoradas'],
            [
                'nome' => 'Unhas Decoradas',
                'descricao' => 'Esmaltação com decorações e artes especiais',
                'preco' => 70.00,
                'duracao_minutos' => 150,
                'ativo' => true,
            ]
        );

        Servico::updateOrCreate(
            ['nome' => 'Manicure + Pedicure'],
            [
                'nome' => 'Manicure + Pedicure',
                'descricao' => 'Pacote completo: manicure e pedicure',
                'preco' => 55.00,
                'duracao_minutos' => 120,
                'ativo' => true,
            ]
        );

    }
}
