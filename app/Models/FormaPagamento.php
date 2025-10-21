<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormaPagamento extends Model
{
    use HasFactory;

    protected $table = 'formas_pagamento';

    protected $fillable = [
        'nome',
        'taxa_percentual',
        'ativo',
    ];

    protected $casts = [
        'taxa_percentual' => 'decimal:2',
        'ativo' => 'boolean',
    ];

    // Relacionamentos
    public function pagamentos()
    {
        return $this->hasMany(Pagamento::class);
    }

    // Scope para formas ativas
    public function scopeAtivo($query)
    {
        return $query->where('ativo', true);
    }

    // Calcular taxa sobre um valor
    public function calcularTaxa($valor)
    {
        return ($valor * $this->taxa_percentual) / 100;
    }
}

