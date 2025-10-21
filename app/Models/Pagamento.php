<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\AuditLog;

class Pagamento extends Model
{
    use HasFactory;

    protected $table = 'pagamentos';

    protected $fillable = [
        'agendamento_id',
        'forma_pagamento_id',
        'valor',
        'taxa',
        'valor_liquido',
        'valor_profissional',
        'valor_empresa',
        'gorjeta',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'taxa' => 'decimal:2',
        'valor_liquido' => 'decimal:2',
        'valor_profissional' => 'decimal:2',
        'valor_empresa' => 'decimal:2',
        'gorjeta' => 'decimal:2',
    ];

    // Relacionamentos
    public function agendamento()
    {
        return $this->belongsTo(Agendamento::class);
    }

    public function formaPagamento()
    {
        return $this->belongsTo(FormaPagamento::class);
    }

    // Eventos do modelo para auditoria
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($pagamento) {
            // Log de criação
            AuditLog::logFinancialOperation(
                'created',
                $pagamento,
                null,
                $pagamento->toArray(),
                'Pagamento criado'
            );
        });

        static::updating(function ($pagamento) {
            // Log de atualização
            AuditLog::logFinancialOperation(
                'updated',
                $pagamento,
                $pagamento->getOriginal(),
                $pagamento->getChanges(),
                'Pagamento atualizado'
            );
        });

        static::deleting(function ($pagamento) {
            // Log de exclusão
            AuditLog::logFinancialOperation(
                'deleted',
                $pagamento,
                $pagamento->toArray(),
                null,
                'Pagamento excluído'
            );
        });
    }

    // Calcular automaticamente os valores ao criar pagamento
    public static function calcularValores($valor, FormaPagamento $formaPagamento, Profissional $profissional, $gorjeta = 0)
    {
        // Validações de segurança
        if ($valor <= 0) {
            throw new \InvalidArgumentException('Valor deve ser positivo');
        }

        if ($valor > 10000) {
            throw new \InvalidArgumentException('Valor excede limite máximo');
        }

        // 1. Calcular taxa
        $taxa = $formaPagamento->calcularTaxa($valor);
        
        // 2. Valor líquido (após taxa)
        $valorLiquido = $valor - $taxa;
        
        // 3. Dividir entre profissional e empresa
        $percentualProfissional = $profissional->percentual_comissao;
        $valorProfissional = ($valorLiquido * $percentualProfissional) / 100;
        $valorEmpresa = $valorLiquido - $valorProfissional;

        // Validação de consistência
        $soma = $valorProfissional + $valorEmpresa;
        if (abs($soma - $valorLiquido) > 0.01) {
            throw new \InvalidArgumentException('Erro no cálculo dos valores');
        }

        return [
            'valor' => round($valor, 2),
            'taxa' => round($taxa, 2),
            'valor_liquido' => round($valorLiquido, 2),
            'valor_profissional' => round($valorProfissional, 2),
            'valor_empresa' => round($valorEmpresa, 2),
            'gorjeta' => round($gorjeta, 2),
        ];
    }

    // Validações de integridade
    public function validateIntegrity()
    {
        $errors = [];

        // Verificar se valores são positivos
        if ($this->valor < 0) $errors[] = 'Valor não pode ser negativo';
        if ($this->taxa < 0) $errors[] = 'Taxa não pode ser negativa';
        if ($this->valor_profissional < 0) $errors[] = 'Valor do profissional não pode ser negativo';
        if ($this->valor_empresa < 0) $errors[] = 'Valor da empresa não pode ser negativo';

        // Verificar consistência
        $soma = $this->valor_profissional + $this->valor_empresa;
        if (abs($soma - $this->valor_liquido) > 0.01) {
            $errors[] = 'Inconsistência nos valores calculados';
        }

        return $errors;
    }
}

