@extends('layouts.app')

@section('title', 'Relatório Financeiro')

@section('content')
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-4 sm:mb-6 px-4 sm:px-0">
        <h1 class="text-2xl sm:text-3xl font-bold text-vm-navy-800">💰 Relatório Financeiro</h1>
        <p class="text-sm sm:text-base text-gray-600 mt-1">Visão completa do desempenho financeiro da esmalteria</p>
    </div>

    <!-- Cards de Faturamento por Período -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6 px-4 sm:px-0">
        <!-- Hoje -->
        <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 border-t-4 border-vm-gold">
            <div class="flex justify-between items-start mb-3">
                <div>
                    <h3 class="text-xs sm:text-sm font-semibold text-gray-600 uppercase">Hoje</h3>
                    <p class="text-2xl sm:text-3xl font-bold text-green-600 mt-2">R$ {{ number_format($totalDia, 2, ',', '.') }}</p>
                </div>
                <span class="text-2xl">📅</span>
            </div>
            @if($pendentesDia > 0)
                <div class="mt-3 pt-3 border-t border-gray-200 flex items-center justify-between">
                    <p class="text-xs text-orange-600 font-medium">Aguardando confirmação</p>
                    <span class="bg-orange-100 text-orange-700 rounded-full px-2 py-1 text-xs font-bold">{{ $pendentesDia }}</span>
                </div>
            @endif
        </div>

        <!-- Semana -->
        <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 border-t-4 border-blue-500">
            <div class="flex justify-between items-start mb-3">
                <div>
                    <h3 class="text-xs sm:text-sm font-semibold text-gray-600 uppercase">Esta Semana</h3>
                    <p class="text-2xl sm:text-3xl font-bold text-blue-600 mt-2">R$ {{ number_format($totalSemana, 2, ',', '.') }}</p>
                </div>
                <span class="text-2xl">📊</span>
            </div>
            @if($pendentesSemana > 0)
                <div class="mt-3 pt-3 border-t border-gray-200 flex items-center justify-between">
                    <p class="text-xs text-orange-600 font-medium">Aguardando</p>
                    <span class="bg-orange-100 text-orange-700 rounded-full px-2 py-1 text-xs font-bold">{{ $pendentesSemana }}</span>
                </div>
            @endif
        </div>

        <!-- Mês -->
        <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 border-t-4 border-purple-500">
            <div class="flex justify-between items-start mb-3">
                <div>
                    <h3 class="text-xs sm:text-sm font-semibold text-gray-600 uppercase">Este Mês</h3>
                    <p class="text-2xl sm:text-3xl font-bold text-purple-600 mt-2">R$ {{ number_format($totalMes, 2, ',', '.') }}</p>
                </div>
                <span class="text-2xl">📈</span>
            </div>
            @if($pendentesMes > 0)
                <div class="mt-3 pt-3 border-t border-gray-200 flex items-center justify-between">
                    <p class="text-xs text-orange-600 font-medium">Aguardando</p>
                    <span class="bg-orange-100 text-orange-700 rounded-full px-2 py-1 text-xs font-bold">{{ $pendentesMes }}</span>
                </div>
            @endif
        </div>

        <!-- Ano -->
        <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 border-t-4 border-indigo-500">
            <div class="flex justify-between items-start mb-3">
                <div>
                    <h3 class="text-xs sm:text-sm font-semibold text-gray-600 uppercase">Este Ano ({{ now()->year }})</h3>
                    <p class="text-2xl sm:text-3xl font-bold text-indigo-600 mt-2">R$ {{ number_format($totalAno, 2, ',', '.') }}</p>
                </div>
                <span class="text-2xl">🎯</span>
            </div>
            @if($pendentesAno > 0)
                <div class="mt-3 pt-3 border-t border-gray-200 flex items-center justify-between">
                    <p class="text-xs text-orange-600 font-medium">Aguardando</p>
                    <span class="bg-orange-100 text-orange-700 rounded-full px-2 py-1 text-xs font-bold">{{ $pendentesAno }}</span>
                </div>
            @endif
        </div>
    </div>

    <!-- Evolução Mensal -->
    <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 mb-6 mx-4 sm:mx-0">
        <h3 class="text-lg sm:text-xl font-bold mb-6 text-vm-navy-800">📈 Evolução Mensal da Empresa</h3>
        
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
                            <div class="bg-gradient-to-r from-vm-navy-600 to-vm-navy-500 h-full rounded-full transition-all duration-500" 
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

    <!-- Ações Rápidas -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 px-4 sm:px-0">
        <a href="{{ route('agendamentos.agenda') }}" class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-6 text-white hover:shadow-xl transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold">Ver Agenda</h3>
                    <p class="text-sm opacity-90 mt-1">Acompanhar atendimentos</p>
                </div>
                <svg class="w-12 h-12 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
        </a>

        <a href="{{ route('dashboard') }}" class="bg-gradient-to-r from-vm-gold-500 to-vm-gold-600 rounded-xl p-6 text-white hover:shadow-xl transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold">Dashboard Completo</h3>
                    <p class="text-sm opacity-90 mt-1">Visão geral e ranking</p>
                </div>
                <svg class="w-12 h-12 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
        </a>
    </div>
</div>
@endsection

