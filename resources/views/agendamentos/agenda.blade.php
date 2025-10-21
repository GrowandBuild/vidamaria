@extends('layouts.app')

@section('title', 'Agenda')

@section('content')
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <!-- Header com Filtros -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-4 sm:mb-6">
        <div class="p-4 sm:p-6">
            <div class="flex flex-col gap-4">
                <div class="flex-1">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Agenda de Atendimentos</h2>
                </div>
                
                <form method="GET" action="{{ route('agendamentos.agenda') }}" class="flex flex-col sm:flex-row gap-3 sm:gap-4 w-full">
                    <div class="flex-1">
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Data</label>
                        <input type="date" name="data" value="{{ $data }}" 
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-vm-gold focus:ring-vm-gold text-sm">
                    </div>
                    
                    @can('isProprietaria')
                        <div class="flex-1">
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Profissional</label>
                            <select name="profissional_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-vm-gold focus:ring-vm-gold text-sm">
                                <option value="">Todos</option>
                                @foreach($profissionais as $prof)
                                    <option value="{{ $prof->id }}" {{ $profissionalId == $prof->id ? 'selected' : '' }}>
                                        {{ $prof->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endcan
                    
                    <button type="submit" class="btn-secondary w-full sm:w-auto sm:mt-6">
                        Filtrar
                    </button>
                </form>

                <a href="{{ route('agendamentos.create') }}" class="btn-primary w-full sm:w-auto text-center">
                    + Novo Agendamento
                </a>
            </div>
        </div>
    </div>

    <!-- Lista de Agendamentos -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-3 sm:p-6">
            <div class="space-y-3 sm:space-y-4">
                @forelse($agendamentos as $agendamento)
                    <div class="rounded-lg p-3 sm:p-4 hover:shadow-xl transition-all duration-300 border-l-4
                        @if($agendamento->status == 'concluido') 
                            bg-gradient-to-r from-green-500 to-green-600 border-green-700 text-white
                        @elseif($agendamento->status == 'pre_concluido') 
                            bg-gradient-to-r from-orange-400 to-orange-500 border-orange-600 text-white
                        @elseif($agendamento->status == 'agendado') 
                            bg-gradient-to-r from-blue-500 to-blue-600 border-blue-700 text-white
                        @else 
                            bg-gradient-to-r from-gray-400 to-gray-500 border-gray-600 text-white
                        @endif">
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3">
                            <div class="flex-1">
                                <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3">
                                    <div class="text-2xl sm:text-3xl font-bold text-white drop-shadow-lg">
                                        {{ $agendamento->data_hora->format('H:i') }}
                                    </div>
                                    
                                    <!-- Avatar do Profissional -->
                                    <div class="flex-shrink-0">
                                        @if($agendamento->profissional && $agendamento->profissional->user && $agendamento->profissional->user->avatar)
                                            <img src="{{ asset('storage/' . $agendamento->profissional->user->avatar) }}" 
                                                 alt="{{ $agendamento->profissional->nome }}" 
                                                 class="w-12 h-12 rounded-full object-cover ring-2 ring-white shadow-lg">
                                        @else
                                            <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center ring-2 ring-white shadow-lg">
                                                <span class="text-white font-bold text-lg">{{ strtoupper(substr($agendamento->profissional->nome, 0, 1)) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div class="flex-1">
                                        <div class="font-bold text-white text-base sm:text-lg">{{ $agendamento->nome_cliente }}</div>
                                        @if($agendamento->servico)
                                            <div class="text-sm sm:text-base text-white/90">{{ $agendamento->servico->nome }} - R$ {{ number_format($agendamento->servico->preco, 2, ',', '.') }}</div>
                                        @else
                                            <div class="text-sm sm:text-base text-white/70">Serviço não especificado</div>
                                        @endif
                                        <div class="flex items-center gap-2 text-xs sm:text-sm text-white/80 mt-1">
                                            <x-avatar 
                                                :src="$agendamento->profissional->avatar_url" 
                                                :name="$agendamento->profissional->nome" 
                                                size="xs" />
                                            <span>Profissional: {{ $agendamento->profissional->nome }}</span>
                                            @if($agendamento->servico && $agendamento->servico->duracao_minutos)
                                                <span>• {{ $agendamento->servico->duracao_minutos }}min</span>
                                            @endif
                                        </div>
                                        @if($agendamento->observacoes)
                                            <div class="text-xs sm:text-sm text-white/80 mt-1 bg-white/10 rounded px-2 py-1 inline-block">
                                                <strong>Obs:</strong> {{ $agendamento->observacoes }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="px-3 py-1 text-xs rounded-full font-bold bg-white/20 backdrop-blur-sm text-white border border-white/30">
                                    @if($agendamento->status == 'concluido') ✓ Confirmado
                                    @elseif($agendamento->status == 'pre_concluido') ⏳ Pré-Concluído
                                    @elseif($agendamento->status == 'agendado') ⏰ Agendado
                                    @else ✕ Cancelado
                                    @endif
                                </span>
                                
                                @if($agendamento->status == 'agendado')
                                    <a href="{{ route('agendamentos.concluir', $agendamento) }}" 
                                       class="px-2 sm:px-3 py-1 bg-white text-green-700 text-xs font-semibold rounded hover:bg-green-50 whitespace-nowrap shadow-md">
                                        ✓ Concluir
                                    </a>
                                    
                                    @can('isProprietaria')
                                        <a href="{{ route('agendamentos.edit', $agendamento) }}" 
                                           class="px-2 sm:px-3 py-1 bg-white text-blue-700 text-xs font-semibold rounded hover:bg-blue-50 whitespace-nowrap shadow-md">
                                            ✎ Editar
                                        </a>
                                        
                                        <form method="POST" action="{{ route('agendamentos.deletar', $agendamento) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('Deseja realmente cancelar?')"
                                                    class="px-2 sm:px-3 py-1 bg-white text-red-700 text-xs font-semibold rounded hover:bg-red-50 whitespace-nowrap shadow-md">
                                                ✕
                                            </button>
                                        </form>
                                    @endcan
                                @endif
                                
                                @if($agendamento->status == 'pre_concluido')
                                    @can('isProprietaria')
                                        <form method="POST" action="{{ route('agendamentos.confirmar', $agendamento) }}" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="px-2 sm:px-3 py-1 bg-white text-green-700 text-xs font-semibold rounded hover:bg-green-50 whitespace-nowrap shadow-md">
                                                ✓ Confirmar
                                            </button>
                                        </form>
                                        
                                        <form method="POST" action="{{ route('agendamentos.deletar', $agendamento) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('Cancelar e estornar este atendimento?')"
                                                    class="px-2 sm:px-3 py-1 bg-white text-red-700 text-xs font-semibold rounded hover:bg-red-50 whitespace-nowrap shadow-md">
                                                ✕ Cancelar
                                            </button>
                                        </form>
                                    @endcan
                                @endif
                                
                                @if($agendamento->status == 'concluido')
                                    @can('isProprietaria')
                                        <form method="POST" action="{{ route('agendamentos.deletar', $agendamento) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('ATENÇÃO: Cancelar este agendamento irá REMOVER os pagamentos registrados e ALTERAR os valores financeiros. Deseja realmente continuar?')"
                                                    class="px-2 sm:px-3 py-1 bg-white text-orange-700 text-xs font-semibold rounded hover:bg-orange-50 whitespace-nowrap shadow-md">
                                                <span class="hidden sm:inline">↩ Cancelar e Estornar</span>
                                                <span class="sm:hidden">↩ Estornar</span>
                                            </button>
                                        </form>
                                    @endcan
                                @endif

                                @if($agendamento->status == 'cancelado' || $agendamento->status == 'concluido')
                                    @can('isProprietaria')
                                        <form method="POST" action="{{ route('agendamentos.deletar', $agendamento) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('ATENÇÃO: Esta ação é IRREVERSÍVEL! O agendamento será DELETADO PERMANENTEMENTE do banco de dados. Deseja realmente continuar?')"
                                                    class="px-2 sm:px-3 py-1 bg-white text-red-700 text-xs font-semibold rounded hover:bg-red-50 whitespace-nowrap shadow-md">
                                                🗑️ <span class="hidden sm:inline">Deletar</span>
                                            </button>
                                        </form>
                                    @endcan
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum agendamento</h3>
                        <p class="mt-1 text-sm text-gray-500">Não há agendamentos para esta data.</p>
                        @can('isProprietaria')
                            <div class="mt-6">
                                <a href="{{ route('agendamentos.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                    + Novo Agendamento
                                </a>
                            </div>
                        @endcan
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

