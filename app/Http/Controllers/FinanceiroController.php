<?php

namespace App\Http\Controllers;

use App\Models\Agendamento;
use App\Models\Profissional;
use App\Models\Pagamento;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FinanceiroController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        if ($user->isProprietaria()) {
            return $this->financeiroProprietaria($request);
        } else {
            return $this->financeiroProfissional($request, $user->profissional);
        }
    }

    private function financeiroProprietaria(Request $request)
    {
        $periodo = $request->input('periodo', 'mes');
        
        // Calcular datas baseado no período
        $datas = $this->calcularPeriodo($periodo);
        
        // Total da empresa por período
        $totalDia = $this->calcularTotalEmpresa(today(), today()->endOfDay());
        $totalSemana = $this->calcularTotalEmpresa(now()->startOfWeek(), now()->endOfWeek());
        $totalMes = $this->calcularTotalEmpresa(now()->startOfMonth(), now()->endOfMonth());
        $totalAno = $this->calcularTotalEmpresa(now()->startOfYear(), now()->endOfYear());

        // Pendentes de confirmação
        $pendentesDia = $this->contarPendentes(today(), today()->endOfDay());
        $pendentesSemana = $this->contarPendentes(now()->startOfWeek(), now()->endOfWeek());
        $pendentesMes = $this->contarPendentes(now()->startOfMonth(), now()->endOfMonth());
        $pendentesAno = $this->contarPendentes(now()->startOfYear(), now()->endOfYear());

        // Gráfico de evolução mensal do ano atual
        $evolucaoMensal = $this->evolucaoMensalEmpresa();

        return view('financeiro.proprietaria', compact(
            'totalDia',
            'totalSemana',
            'totalMes',
            'totalAno',
            'pendentesDia',
            'pendentesSemana',
            'pendentesMes',
            'pendentesAno',
            'evolucaoMensal',
            'periodo'
        ));
    }

    private function financeiroProfissional(Request $request, $profissional)
    {
        // Ganhos por período (apenas confirmados)
        $ganhoDia = $this->calcularGanhoProfissional($profissional->id, today(), today()->endOfDay(), 'concluido');
        $ganhoSemana = $this->calcularGanhoProfissional($profissional->id, now()->startOfWeek(), now()->endOfWeek(), 'concluido');
        $ganhoMes = $this->calcularGanhoProfissional($profissional->id, now()->startOfMonth(), now()->endOfMonth(), 'concluido');
        $ganhoAno = $this->calcularGanhoProfissional($profissional->id, now()->startOfYear(), now()->endOfYear(), 'concluido');

        // Ganhos pré-concluídos (aguardando confirmação)
        $preConcluidoDia = $this->calcularGanhoProfissional($profissional->id, today(), today()->endOfDay(), 'pre_concluido');
        $preConcluidoSemana = $this->calcularGanhoProfissional($profissional->id, now()->startOfWeek(), now()->endOfWeek(), 'pre_concluido');
        $preConcluidoMes = $this->calcularGanhoProfissional($profissional->id, now()->startOfMonth(), now()->endOfMonth(), 'pre_concluido');
        $preConcluidoAno = $this->calcularGanhoProfissional($profissional->id, now()->startOfYear(), now()->endOfYear(), 'pre_concluido');

        // Atendimentos por período
        $atendimentosDia = $this->contarAtendimentos($profissional->id, today(), today()->endOfDay());
        $atendimentosSemana = $this->contarAtendimentos($profissional->id, now()->startOfWeek(), now()->endOfWeek());
        $atendimentosMes = $this->contarAtendimentos($profissional->id, now()->startOfMonth(), now()->endOfMonth());
        $atendimentosAno = $this->contarAtendimentos($profissional->id, now()->startOfYear(), now()->endOfYear());

        // Evolução mensal
        $evolucaoMensal = $this->evolucaoMensalProfissional($profissional->id);

        return view('financeiro.profissional', compact(
            'profissional',
            'ganhoDia',
            'ganhoSemana',
            'ganhoMes',
            'ganhoAno',
            'preConcluidoDia',
            'preConcluidoSemana',
            'preConcluidoMes',
            'preConcluidoAno',
            'atendimentosDia',
            'atendimentosSemana',
            'atendimentosMes',
            'atendimentosAno',
            'evolucaoMensal'
        ));
    }

    // Métodos auxiliares
    private function calcularPeriodo($periodo)
    {
        switch ($periodo) {
            case 'dia':
                return [today(), today()->endOfDay()];
            case 'semana':
                return [now()->startOfWeek(), now()->endOfWeek()];
            case 'mes':
                return [now()->startOfMonth(), now()->endOfMonth()];
            case 'ano':
                return [now()->startOfYear(), now()->endOfYear()];
            default:
                return [now()->startOfMonth(), now()->endOfMonth()];
        }
    }

    private function calcularTotalEmpresa($dataInicio, $dataFim)
    {
        return Pagamento::whereHas('agendamento', function($query) use ($dataInicio, $dataFim) {
            $query->where('status', 'concluido')
                  ->whereBetween('data_hora', [$dataInicio, $dataFim]);
        })->sum('valor_empresa');
    }

    private function contarPendentes($dataInicio, $dataFim)
    {
        return Agendamento::where('status', 'pre_concluido')
            ->whereBetween('data_hora', [$dataInicio, $dataFim])
            ->count();
    }

    private function calcularGanhoProfissional($profissionalId, $dataInicio, $dataFim, $status)
    {
        $agendamentos = Agendamento::where('profissional_id', $profissionalId)
            ->where('status', $status)
            ->whereBetween('data_hora', [$dataInicio, $dataFim])
            ->with('pagamentos')
            ->get();

        $total = 0;
        foreach ($agendamentos as $agendamento) {
            foreach ($agendamento->pagamentos as $pagamento) {
                $total += $pagamento->valor_profissional + $pagamento->gorjeta;
            }
        }

        return $total;
    }

    private function contarAtendimentos($profissionalId, $dataInicio, $dataFim)
    {
        return Agendamento::where('profissional_id', $profissionalId)
            ->where('status', 'concluido')
            ->whereBetween('data_hora', [$dataInicio, $dataFim])
            ->count();
    }

    private function evolucaoMensalEmpresa()
    {
        $meses = [];
        for ($i = 11; $i >= 0; $i--) {
            $inicio = now()->subMonths($i)->startOfMonth();
            $fim = now()->subMonths($i)->endOfMonth();
            
            $total = $this->calcularTotalEmpresa($inicio, $fim);
            
            $meses[] = [
                'mes' => $inicio->format('M/y'),
                'total' => $total,
            ];
        }
        return $meses;
    }

    private function evolucaoMensalProfissional($profissionalId)
    {
        $meses = [];
        for ($i = 11; $i >= 0; $i--) {
            $inicio = now()->subMonths($i)->startOfMonth();
            $fim = now()->subMonths($i)->endOfMonth();
            
            $total = $this->calcularGanhoProfissional($profissionalId, $inicio, $fim, 'concluido');
            
            $meses[] = [
                'mes' => $inicio->format('M/y'),
                'total' => $total,
            ];
        }
        return $meses;
    }
}

