@extends('layouts.app')

@section('title', 'Formas de Pagamento')

@section('content')
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-4 sm:p-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-xl sm:text-2xl font-bold">💳 Formas de Pagamento</h2>
                    <p class="text-sm text-gray-600 mt-1">Gerencie as taxas de cada forma de pagamento</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($formasPagamento as $forma)
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-all">
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex-1">
                                <h3 class="font-bold text-lg text-gray-800">{{ $forma->nome }}</h3>
                                <div class="mt-2">
                                    <span class="text-2xl font-bold {{ $forma->taxa_percentual > 0 ? 'text-red-600' : 'text-green-600' }}">
                                        {{ number_format($forma->taxa_percentual, 2, ',', '.') }}%
                                    </span>
                                    <span class="text-sm text-gray-600 ml-2">de taxa</span>
                                </div>
                                @if($forma->taxa_percentual > 0)
                                    <p class="text-xs text-gray-500 mt-1">
                                        Ex: R$ 100 → Taxa: R$ {{ number_format(100 * $forma->taxa_percentual / 100, 2, ',', '.') }}
                                    </p>
                                @else
                                    <p class="text-xs text-green-600 mt-1">✓ Sem taxa</p>
                                @endif
                            </div>
                            <div>
                                <span class="px-3 py-1 text-xs rounded-full {{ $forma->ativo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $forma->ativo ? 'Ativo' : 'Inativo' }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="flex gap-2 mt-4">
                            <a href="{{ route('formas-pagamento.edit', $forma) }}" 
                               class="flex-1 text-center px-4 py-2 bg-vm-gold hover:bg-vm-gold-600 text-vm-navy-900 font-semibold rounded-lg shadow transition-all text-sm">
                                ✎ Editar Taxa
                            </a>
                            
                            <form method="POST" action="{{ route('formas-pagamento.toggle', $forma) }}" class="flex-1">
                                @csrf
                                <button type="submit" 
                                        class="w-full px-4 py-2 {{ $forma->ativo ? 'bg-gray-500 hover:bg-gray-600' : 'bg-green-500 hover:bg-green-600' }} text-white font-semibold rounded-lg shadow transition-all text-sm">
                                    {{ $forma->ativo ? '✕ Desativar' : '✓ Ativar' }}
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

