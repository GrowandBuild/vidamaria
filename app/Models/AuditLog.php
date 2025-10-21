<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'user_name',
        'action',
        'model_type',
        'model_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'route',
        'description'
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    // Log automático de operações financeiras
    public static function logFinancialOperation($action, $model, $oldValues = null, $newValues = null, $description = null)
    {
        $user = Auth::user();
        
        return self::create([
            'user_id' => $user ? $user->id : null,
            'user_name' => $user ? $user->name : 'Sistema',
            'action' => $action,
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'route' => request()->route() ? request()->route()->getName() : null,
            'description' => $description
        ]);
    }

    // Log de acesso a dados financeiros
    public static function logFinancialAccess($action, $description = null)
    {
        $user = Auth::user();
        
        return self::create([
            'user_id' => $user ? $user->id : null,
            'user_name' => $user ? $user->name : 'Sistema',
            'action' => $action,
            'model_type' => 'FinancialData',
            'model_id' => null,
            'old_values' => null,
            'new_values' => null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'route' => request()->route() ? request()->route()->getName() : null,
            'description' => $description
        ]);
    }

    // Relacionamentos
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes para consultas
    public function scopeFinancial($query)
    {
        return $query->whereIn('model_type', [
            'App\Models\Pagamento',
            'App\Models\Agendamento',
            'FinancialData'
        ]);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
    }
}
