<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profissional extends Model
{
    use HasFactory;

    protected $table = 'profissionais';

    protected $fillable = [
        'user_id',
        'telefone',
        'percentual_comissao',
        'ativo',
    ];

    protected $casts = [
        'percentual_comissao' => 'decimal:2',
        'ativo' => 'boolean',
    ];

    // Relacionamentos
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function agendamentos()
    {
        return $this->hasMany(Agendamento::class);
    }

    // Calcular total ganho da profissional em um período
    public function totalGanho($dataInicio = null, $dataFim = null)
    {
        $query = $this->agendamentos()
            ->where('status', 'concluido')
            ->with('pagamentos');

        if ($dataInicio) {
            $query->where('data_hora', '>=', $dataInicio);
        }
        if ($dataFim) {
            $query->where('data_hora', '<=', $dataFim);
        }

        $agendamentos = $query->get();
        
        $total = 0;
        foreach ($agendamentos as $agendamento) {
            foreach ($agendamento->pagamentos as $pagamento) {
                $total += $pagamento->valor_profissional + $pagamento->gorjeta;
            }
        }

        return $total;
    }

    // Accessors para dados do User
    public function getNomeAttribute()
    {
        return $this->user ? $this->user->name : 'Nome não encontrado';
    }

    public function getAvatarUrlAttribute()
    {
        if ($this->user && $this->user->avatar) {
            return asset('storage/' . $this->user->avatar);
        }
        
        // Avatar padrão com iniciais (cores premium)
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->getNomeAttribute()) . '&color=0A1647&background=D4AF37&bold=true&size=200';
    }
}
