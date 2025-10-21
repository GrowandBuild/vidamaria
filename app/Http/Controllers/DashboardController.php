<?php

namespace App\Http\Controllers;

use App\Models\Agendamento;
use App\Models\Profissional;
use App\Models\Cliente;
use App\Models\Pagamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Filtro de período
        $dataInicio = $request->input('data_inicio', now()->startOfMonth());
        $dataFim = $request->input('data_fim', now()->endOfMonth());

        if ($user->isProprietaria()) {
            return $this->dashboardProprietaria($dataInicio, $dataFim);
        } else {
            return $this->dashboardProfissional($dataInicio, $dataFim, $user->profissional);
        }
    }

    private function dashboardProprietaria($dataInicio, $dataFim)
    {
        // Total da empresa (apenas confirmados)
        $totalEmpresa = Pagamento::whereHas('agendamento', function($query) use ($dataInicio, $dataFim) {
            $query->where('status', 'concluido')
                  ->whereBetween('data_hora', [$dataInicio, $dataFim]);
        })->sum('valor_empresa');

        // Total pré-concluído (aguardando confirmação)
        $totalPreConcluido = Pagamento::whereHas('agendamento', function($query) use ($dataInicio, $dataFim) {
            $query->where('status', 'pre_concluido')
                  ->whereBetween('data_hora', [$dataInicio, $dataFim]);
        })->sum('valor_empresa');

        // Total de cada profissional
        $profissionais = Profissional::with(['agendamentos' => function($query) use ($dataInicio, $dataFim) {
            $query->where('status', 'concluido')
                  ->whereBetween('data_hora', [$dataInicio, $dataFim])
                  ->with('pagamentos');
        }])->get()->map(function($prof) {
            $totalProfissional = 0;
            $totalGorjetas = 0;
            
            foreach ($prof->agendamentos as $agendamento) {
                foreach ($agendamento->pagamentos as $pagamento) {
                    $totalProfissional += $pagamento->valor_profissional;
                    $totalGorjetas += $pagamento->gorjeta;
                }
            }
            
            return [
                'id' => $prof->id,
                'nome' => $prof->nome,
                'avatar_url' => $prof->avatar_url,
                'total' => $totalProfissional + $totalGorjetas,
                'total_comissao' => $totalProfissional,
                'total_gorjetas' => $totalGorjetas,
                'percentual' => $prof->percentual_comissao,
            ];
        });

        // Somatório geral
        $totalGeral = $totalEmpresa + $profissionais->sum('total');

        // Agendamentos de hoje
        $agendamentosHoje = Agendamento::with(['profissional.user', 'servicos', 'cliente'])
            ->whereDate('data_hora', today())
            ->orderBy('data_hora')
            ->get();

        // Ranking de clientes
        $rankingClientes = Cliente::with('agendamentos.pagamentos')
            ->get()
            ->map(function($cliente) {
                return [
                    'id' => $cliente->id,
                    'nome' => $cliente->nome,
                    'total_gasto' => $cliente->totalGasto(),
                    'lucro_gerado' => $cliente->lucroGerado(),
                    'total_atendimentos' => $cliente->agendamentos()->where('status', 'concluido')->count(),
                ];
            })
            ->sortByDesc('lucro_gerado')
            ->take(10);

        // Contar agendamentos pendentes de confirmação
        $agendamentosPendentes = Agendamento::where('status', 'pre_concluido')
            ->whereBetween('data_hora', [$dataInicio, $dataFim])
            ->count();

        return view('dashboard.proprietaria', compact(
            'totalEmpresa',
            'totalPreConcluido',
            'profissionais',
            'totalGeral',
            'agendamentosHoje',
            'rankingClientes',
            'agendamentosPendentes',
            'dataInicio',
            'dataFim'
        ));
    }

    private function dashboardProfissional($dataInicio, $dataFim, $profissional)
    {
        // Meus ganhos confirmados
        $agendamentosConfirmados = Agendamento::where('profissional_id', $profissional->id)
            ->where('status', 'concluido')
            ->whereBetween('data_hora', [$dataInicio, $dataFim])
            ->with('pagamentos')
            ->get();

        $totalComissao = 0;
        $totalGorjetas = 0;
        
        foreach ($agendamentosConfirmados as $agendamento) {
            foreach ($agendamento->pagamentos as $pagamento) {
                $totalComissao += $pagamento->valor_profissional;
                $totalGorjetas += $pagamento->gorjeta;
            }
        }

        $totalGanhos = $totalComissao + $totalGorjetas;

        // Meus ganhos pré-concluídos (aguardando confirmação)
        $agendamentosPreConcluidos = Agendamento::where('profissional_id', $profissional->id)
            ->where('status', 'pre_concluido')
            ->whereBetween('data_hora', [$dataInicio, $dataFim])
            ->with('pagamentos')
            ->get();

        $totalComissaoPreConcluido = 0;
        $totalGorjetasPreConcluido = 0;
        
        foreach ($agendamentosPreConcluidos as $agendamento) {
            foreach ($agendamento->pagamentos as $pagamento) {
                $totalComissaoPreConcluido += $pagamento->valor_profissional;
                $totalGorjetasPreConcluido += $pagamento->gorjeta;
            }
        }

        $totalGanhosPreConcluido = $totalComissaoPreConcluido + $totalGorjetasPreConcluido;

        // Meus agendamentos de hoje
        $agendamentosHoje = Agendamento::where('profissional_id', $profissional->id)
            ->with(['profissional.user', 'servicos', 'cliente'])
            ->whereDate('data_hora', today())
            ->orderBy('data_hora')
            ->get();

        // Meus atendimentos do mês
        $totalAtendimentos = Agendamento::where('profissional_id', $profissional->id)
            ->where('status', 'concluido')
            ->whereBetween('data_hora', [$dataInicio, $dataFim])
            ->count();

        return view('dashboard.profissional', compact(
            'profissional',
            'totalComissao',
            'totalGorjetas',
            'totalGanhos',
            'totalComissaoPreConcluido',
            'totalGorjetasPreConcluido',
            'totalGanhosPreConcluido',
            'agendamentosHoje',
            'totalAtendimentos',
            'dataInicio',
            'dataFim'
        ));
    }
}

