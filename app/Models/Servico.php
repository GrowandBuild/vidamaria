<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Servico extends Model
{
    use HasFactory;

    protected $table = 'servicos';

    protected $fillable = [
        'nome',
        'preco',
        'duracao_minutos',
        'descricao',
        'ativo',
    ];

    protected $casts = [
        'preco' => 'decimal:2',
        'ativo' => 'boolean',
    ];

    // Relacionamentos
    public function agendamentos()
    {
        return $this->hasMany(Agendamento::class);
    }

    // Scope para serviços ativos
    public function scopeAtivo($query)
    {
        return $query->where('ativo', true);
    }
}

