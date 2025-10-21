@extends('layouts.app')

@section('title', 'Meus Ganhos')

@section('content')
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <!-- Filtro de Período -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-4 sm:mb-6 mx-4 sm:mx-0">
        <div class="p-4 sm:p-6">
            <form method="GET" action="{{ route('dashboard') }}" class="flex flex-col sm:flex-row gap-3 sm:gap-4">
                <div class="flex-1">
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Data Início</label>
                    <input type="date" name="data_inicio" value="{{ $dataInicio }}" 
                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-vm-gold focus:ring-vm-gold text-sm">
                </div>
                <div class="flex-1">
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Data Fim</label>
                    <input type="date" name="data_fim" value="{{ $dataFim }}" 
                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-vm-gold focus:ring-vm-gold text-sm">
                </div>
                <button type="submit" class="btn-primary w-full sm:w-auto sm:mt-5">
                    Filtrar Período
                </button>
            </form>
        </div>
    </div>

    <!-- Cards Resumo -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-4 mb-4 sm:mb-6 px-4 sm:px-0">
        <!-- Total Confirmado -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 overflow-hidden shadow-lg rounded-lg">
            <div class="p-4 sm:p-6 text-white">
                <div class="text-xs sm:text-sm font-semibold uppercase">✓ Confirmado</div>
                <div class="mt-2 text-xl sm:text-3xl font-bold">R$ {{ number_format($totalGanhos, 2, ',', '.') }}</div>
                <div class="mt-1 text-xs opacity-80">Oficial</div>
            </div>
        </div>

        <!-- Total Pré-Concluído (Aguardando) -->
        <div class="bg-gradient-to-br from-orange-400 to-orange-500 overflow-hidden shadow-lg rounded-lg">
            <div class="p-4 sm:p-6 text-white">
                <div class="text-xs sm:text-sm font-semibold uppercase">⏳ Aguardando</div>
                <div class="mt-2 text-xl sm:text-3xl font-bold">R$ {{ number_format($totalGanhosPreConcluido, 2, ',', '.') }}</div>
                <div class="mt-1 text-xs opacity-80">A confirmar</div>
            </div>
        </div>

        <!-- Comissões -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 overflow-hidden shadow-lg rounded-lg">
            <div class="p-4 sm:p-6 text-white">
                <div class="text-xs sm:text-sm font-semibold uppercase">Comissões</div>
                <div class="mt-2 text-xl sm:text-3xl font-bold">R$ {{ number_format($totalComissao, 2, ',', '.') }}</div>
                <div class="mt-1 text-xs opacity-80">{{ $profissional->percentual_comissao }}%</div>
            </div>
        </div>

        <!-- Gorjetas -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 overflow-hidden shadow-lg rounded-lg">
            <div class="p-4 sm:p-6 text-white">
                <div class="text-xs sm:text-sm font-semibold uppercase">Gorjetas</div>
                <div class="mt-2 text-xl sm:text-3xl font-bold">R$ {{ number_format($totalGorjetas, 2, ',', '.') }}</div>
                <div class="mt-1 text-xs opacity-80">100%</div>
            </div>
        </div>

        <!-- Total de Atendimentos -->
        <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 overflow-hidden shadow-lg rounded-lg">
            <div class="p-4 sm:p-6 text-white">
                <div class="text-xs sm:text-sm font-semibold uppercase">Atendimentos</div>
                <div class="mt-2 text-xl sm:text-3xl font-bold">{{ $totalAtendimentos }}</div>
                <div class="mt-1 text-xs opacity-80">Confirmados</div>
            </div>
        </div>
    </div>

    <!-- Minha Agenda de Hoje -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg px-4 sm:px-0">
        <div class="p-3 sm:p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-base sm:text-lg font-semibold">Minha Agenda de Hoje</h3>
                <a href="{{ route('agendamentos.agenda') }}" class="text-vm-gold hover:text-vm-gold-600 text-xs sm:text-sm font-medium">
                    Ver agenda completa →
                </a>
            </div>
            
            <div class="space-y-3">
                @forelse($agendamentosHoje as $agendamento)
                    <div class="rounded-lg p-3 sm:p-4 
                        @if($agendamento->status == 'concluido') 
                            bg-gradient-to-r from-green-500 to-green-600 text-white
                        @elseif($agendamento->status == 'pre_concluido') 
                            bg-gradient-to-r from-orange-400 to-orange-500 text-white
                        @elseif($agendamento->status == 'agendado') 
                            bg-gradient-to-r from-blue-500 to-blue-600 text-white
                        @else 
                            bg-gradient-to-r from-gray-400 to-gray-500 text-white
                        @endif
                        shadow-md hover:shadow-lg transition-all">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-3 flex-1">
                                <div class="text-xl sm:text-2xl font-bold">
                                    {{ $agendamento->data_hora->format('H:i') }}
                                </div>
                                
                                <!-- Avatar do Profissional -->
                                <div class="flex-shrink-0">
                                    @if($agendamento->profissional && $agendamento->profissional->user && $agendamento->profissional->user->avatar)
                                        <img src="{{ asset('storage/' . $agendamento->profissional->user->avatar) }}" 
                                             alt="{{ $agendamento->profissional->nome }}" 
                                             class="w-10 h-10 rounded-full object-cover ring-2 ring-white shadow-lg">
                                    @else
                                        <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center ring-2 ring-white shadow-lg">
                                            <span class="text-white font-bold text-sm">{{ strtoupper(substr($agendamento->profissional->nome, 0, 1)) }}</span>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="flex-1">
                                    <div class="font-semibold text-sm sm:text-base">{{ $agendamento->nome_cliente }}</div>
                                    @if($agendamento->servico)
                                        <div class="text-xs sm:text-sm opacity-90">
                                            {{ $agendamento->servico->nome }} - R$ {{ number_format($agendamento->servico->preco, 2, ',', '.') }}
                                        </div>
                                    @else
                                        <div class="text-xs sm:text-sm opacity-90">Serviço não especificado</div>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-3 py-1 text-xs rounded-full font-bold bg-white/20 backdrop-blur-sm border border-white/30">
                                    @if($agendamento->status == 'concluido') ✓ Confirmado
                                    @elseif($agendamento->status == 'pre_concluido') ⏳ Aguardando
                                    @elseif($agendamento->status == 'agendado') ⏰ Agendado
                                    @else ✕ Cancelado
                                    @endif
                                </span>
                                @if($agendamento->status == 'agendado')
                                    <a href="{{ route('agendamentos.concluir', $agendamento) }}" 
                                       class="px-2 sm:px-3 py-1 bg-white text-green-700 text-xs font-semibold rounded hover:bg-green-50 whitespace-nowrap shadow-md">
                                        ✓ Concluir
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500">
                        Nenhum agendamento para hoje
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

