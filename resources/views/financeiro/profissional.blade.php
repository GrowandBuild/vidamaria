@extends('layouts.app')

@section('title', 'Meus Ganhos')

@section('content')
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-4 sm:mb-6 px-4 sm:px-0">
        <h1 class="text-2xl sm:text-3xl font-bold text-vm-navy-800">💰 Meus Ganhos</h1>
        <p class="text-sm sm:text-base text-gray-600 mt-1">Acompanhe seus ganhos em tempo real</p>
    </div>

    <!-- Cards de Ganhos por Período -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6 px-4 sm:px-0">
        <!-- Hoje -->
        <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 border-t-4 border-vm-gold">
            <div class="flex justify-between items-start mb-3">
                <div>
                    <h3 class="text-xs sm:text-sm font-semibold text-gray-600 uppercase">Hoje</h3>
                    <p class="text-2xl sm:text-3xl font-bold text-green-600 mt-2">R$ {{ number_format($ganhoDia, 2, ',', '.') }}</p>
                </div>
                <span class="text-2xl">📅</span>
            </div>
            @if($preConcluidoDia > 0)
                <div class="mt-3 pt-3 border-t border-gray-200">
                    <p class="text-xs text-orange-600 font-medium">+ R$ {{ number_format($preConcluidoDia, 2, ',', '.') }} aguardando</p>
                </div>
            @endif
            <div class="mt-2 text-xs text-gray-500">
                {{ $atendimentosDia }} atendimentos
            </div>
        </div>

        <!-- Semana -->
        <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 border-t-4 border-blue-500">
            <div class="flex justify-between items-start mb-3">
                <div>
                    <h3 class="text-xs sm:text-sm font-semibold text-gray-600 uppercase">Esta Semana</h3>
                    <p class="text-2xl sm:text-3xl font-bold text-blue-600 mt-2">R$ {{ number_format($ganhoSemana, 2, ',', '.') }}</p>
                </div>
                <span class="text-2xl">📊</span>
            </div>
            @if($preConcluidoSemana > 0)
                <div class="mt-3 pt-3 border-t border-gray-200">
                    <p class="text-xs text-orange-600 font-medium">+ R$ {{ number_format($preConcluidoSemana, 2, ',', '.') }} aguardando</p>
                </div>
            @endif
            <div class="mt-2 text-xs text-gray-500">
                {{ $atendimentosSemana }} atendimentos
            </div>
        </div>

        <!-- Mês -->
        <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 border-t-4 border-purple-500">
            <div class="flex justify-between items-start mb-3">
                <div>
                    <h3 class="text-xs sm:text-sm font-semibold text-gray-600 uppercase">Este Mês</h3>
                    <p class="text-2xl sm:text-3xl font-bold text-purple-600 mt-2">R$ {{ number_format($ganhoMes, 2, ',', '.') }}</p>
                </div>
                <span class="text-2xl">📈</span>
            </div>
            @if($preConcluidoMes > 0)
                <div class="mt-3 pt-3 border-t border-gray-200">
                    <p class="text-xs text-orange-600 font-medium">+ R$ {{ number_format($preConcluidoMes, 2, ',', '.') }} aguardando</p>
                </div>
            @endif
            <div class="mt-2 text-xs text-gray-500">
                {{ $atendimentosMes }} atendimentos
            </div>
        </div>

        <!-- Ano -->
        <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 border-t-4 border-indigo-500">
            <div class="flex justify-between items-start mb-3">
                <div>
                    <h3 class="text-xs sm:text-sm font-semibold text-gray-600 uppercase">Este Ano</h3>
                    <p class="text-2xl sm:text-3xl font-bold text-indigo-600 mt-2">R$ {{ number_format($ganhoAno, 2, ',', '.') }}</p>
                </div>
                <span class="text-2xl">🎯</span>
            </div>
            @if($preConcluidoAno > 0)
                <div class="mt-3 pt-3 border-t border-gray-200">
                    <p class="text-xs text-orange-600 font-medium">+ R$ {{ number_format($preConcluidoAno, 2, ',', '.') }} aguardando</p>
                </div>
            @endif
            <div class="mt-2 text-xs text-gray-500">
                {{ $atendimentosAno }} atendimentos
            </div>
        </div>
    </div>

    <!-- Evolução Mensal -->
    <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 mb-6 mx-4 sm:mx-0">
        <h3 class="text-lg sm:text-xl font-bold mb-6 text-vm-navy-800">📈 Evolução dos Últimos 12 Meses</h3>
        
        <div class="space-y-3">
            @foreach($evolucaoMensal as $mes)
                <div class="flex items-center gap-3">
                    <div class="w-20 sm:w-24 text-xs sm:text-sm font-medium text-gray-600">{{ $mes['mes'] }}</div>
                    <div class="flex-1">
                        <div class="bg-gray-200 rounded-full h-8 relative overflow-hidden">
                            @php
                                $maxValor = collect($evolucaoMensal)->max('total');
                                $percentual = $maxValor > 0 ? ($mes['total'] / $maxValor) * 100 : 0;
                            @endphp
                            <div class="gradient-gold h-full rounded-full transition-all duration-500" 
                                 style="width: {{ $percentual }}%"></div>
                            <div class="absolute inset-0 flex items-center px-3">
                                <span class="text-xs sm:text-sm font-bold {{ $mes['total'] > 0 ? 'text-white' : 'text-gray-600' }}">
                                    R$ {{ number_format($mes['total'], 2, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Legenda -->
    <div class="bg-vm-navy-50 rounded-xl p-4 sm:p-6 mx-4 sm:mx-0">
        <h4 class="font-semibold text-vm-navy-800 mb-3">ℹ️ Informações Importantes</h4>
        <div class="space-y-2 text-sm text-gray-700">
            <p>✓ <strong class="text-green-600">Valores Confirmados:</strong> Já foram aprovados pela proprietária</p>
            <p>⏳ <strong class="text-orange-600">Valores Aguardando:</strong> Seus atendimentos finalizados, aguardando confirmação da proprietária</p>
            <p>💡 <strong>Dica:</strong> Os valores "aguardando" geralmente são confirmados em até 24h</p>
        </div>
    </div>
</div>
@endsection

