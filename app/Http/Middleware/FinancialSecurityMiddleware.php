<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FinancialSecurityMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        
        // Log de acesso a operações financeiras
        Log::channel('financial')->info('Acesso financeiro', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_type' => $user->tipo,
            'route' => $request->route()->getName(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now()
        ]);

        // Verificar se é proprietária para operações críticas
        $criticalRoutes = [
            'financeiro',
            'agendamentos.confirmar',
            'agendamentos.deletar',
            'pagamentos.*'
        ];

        foreach ($criticalRoutes as $route) {
            if ($request->routeIs($route)) {
                if (!$user->isProprietaria()) {
                    Log::channel('security')->warning('Tentativa de acesso não autorizado', [
                        'user_id' => $user->id,
                        'route' => $request->route()->getName(),
                        'ip' => $request->ip()
                    ]);
                    
                    abort(403, 'Acesso negado. Apenas a proprietária pode realizar esta operação.');
                }
            }
        }

        // Validar integridade dos dados financeiros
        if ($request->has('valor') || $request->has('valor_empresa') || $request->has('valor_profissional')) {
            $this->validateFinancialData($request);
        }

        return $next($request);
    }

    private function validateFinancialData(Request $request)
    {
        $valor = $request->input('valor');
        $valorEmpresa = $request->input('valor_empresa');
        $valorProfissional = $request->input('valor_profissional');

        // Validar valores positivos
        if ($valor && $valor < 0) {
            Log::channel('security')->error('Tentativa de valor negativo', [
                'valor' => $valor,
                'ip' => $request->ip(),
                'user_id' => auth()->id()
            ]);
            abort(400, 'Valores financeiros não podem ser negativos.');
        }

        // Validar consistência dos valores
        if ($valorEmpresa && $valorProfissional) {
            $soma = $valorEmpresa + $valorProfissional;
            if (abs($soma - $valor) > 0.01) { // Tolerância de 1 centavo
                Log::channel('security')->error('Inconsistência financeira detectada', [
                    'valor_total' => $valor,
                    'valor_empresa' => $valorEmpresa,
                    'valor_profissional' => $valorProfissional,
                    'diferenca' => abs($soma - $valor),
                    'ip' => $request->ip(),
                    'user_id' => auth()->id()
                ]);
                abort(400, 'Inconsistência nos valores financeiros detectada.');
            }
        }

        // Validar limites máximos (proteção contra valores absurdos)
        $maxValue = 10000; // R$ 10.000 por transação
        if ($valor && $valor > $maxValue) {
            Log::channel('security')->error('Valor excessivo detectado', [
                'valor' => $valor,
                'limite' => $maxValue,
                'ip' => $request->ip(),
                'user_id' => auth()->id()
            ]);
            abort(400, 'Valor excede o limite máximo permitido.');
        }
    }
}
