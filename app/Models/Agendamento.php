<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agendamento extends Model
{
    use HasFactory;

    protected $table = 'agendamentos';

    protected $fillable = [
        'profissional_id',
        'cliente_id',
        'data_hora',
        'status',
        'cliente_avulso',
        'observacoes',
    ];

    protected $casts = [
        'data_hora' => 'datetime',
    ];

    // Relacionamentos
    public function profissional()
    {
        return $this->belongsTo(Profissional::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function servicos()
    {
        return $this->belongsToMany(Servico::class, 'agendamento_servico')
                    ->withPivot('preco_cobrado')
                    ->withTimestamps();
    }

    // Accessor para compatibilidade (pegar primeiro serviço)
    public function getServicoAttribute()
    {
        return $this->servicos->first();
    }

    public function pagamentos()
    {
        return $this->hasMany(Pagamento::class);
    }
    
    // Calcular valor total dos serviços
    public function valorTotalServicos()
    {
        return $this->servicos->sum('pivot.preco_cobrado');
    }

    // Accessor para nome do cliente (cadastrado ou avulso)
    public function getNomeClienteAttribute()
    {
        return $this->cliente ? $this->cliente->nome : $this->cliente_avulso;
    }

    // Calcular valor total do agendamento
    public function valorTotal()
    {
        return $this->pagamentos->sum('valor');
    }

    // Calcular valor total das gorjetas
    public function totalGorjetas()
    {
        return $this->pagamentos->sum('gorjeta');
    }

    // Scopes
    public function scopeAgendado($query)
    {
        return $query->where('status', 'agendado');
    }

    public function scopeConcluido($query)
    {
        return $query->where('status', 'concluido');
    }

    public function scopeHoje($query)
    {
        return $query->whereDate('data_hora', today());
    }
}

