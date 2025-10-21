<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FormaPagamento;

class FormasPagamentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $formasPagamento = [
            ['nome' => 'Dinheiro', 'taxa_percentual' => 0.00],
            ['nome' => 'PIX', 'taxa_percentual' => 0.00],
            ['nome' => 'Cartão de Débito', 'taxa_percentual' => 2.00],
            ['nome' => 'Cartão de Crédito', 'taxa_percentual' => 3.50],
        ];

        foreach ($formasPagamento as $forma) {
            FormaPagamento::create($forma);
        }
    }
}

