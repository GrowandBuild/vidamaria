@extends('layouts.app')

@section('title', 'Editar Taxa')

@section('content')
<div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h2 class="text-2xl font-bold mb-6">💳 Editar Taxa - {{ $formaPagamento->nome }}</h2>

            <form method="POST" action="{{ route('formas-pagamento.update', $formaPagamento) }}">
                @csrf
                @method('PUT')
                
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nome da Forma de Pagamento</label>
                        <input type="text" name="nome" value="{{ old('nome', $formaPagamento->nome) }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-vm-gold focus:ring-vm-gold">
                        @error('nome')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Taxa (%)</label>
                        <input type="number" name="taxa_percentual" value="{{ old('taxa_percentual', $formaPagamento->taxa_percentual) }}" 
                               step="0.01" min="0" max="100" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-vm-gold focus:ring-vm-gold text-2xl font-bold">
                        @error('taxa_percentual')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        <p class="text-xs text-gray-500 mt-1">Digite apenas o número. Ex: 2.50 para 2,50%</p>
                    </div>

                    <!-- Simulador -->
                    <div class="bg-vm-navy-50 p-4 rounded-lg">
                        <h4 class="font-semibold mb-3 text-vm-navy-800">🧮 Simulador de Taxa</h4>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-gray-600">Valor: R$ 100,00</p>
                                <p class="font-bold text-red-600">Taxa: R$ <span id="sim-taxa-100">{{ number_format(100 * $formaPagamento->taxa_percentual / 100, 2, ',', '.') }}</span></p>
                                <p class="text-gray-600">Líquido: R$ <span id="sim-liq-100">{{ number_format(100 - (100 * $formaPagamento->taxa_percentual / 100), 2, ',', '.') }}</span></p>
                            </div>
                            <div>
                                <p class="text-gray-600">Valor: R$ 50,00</p>
                                <p class="font-bold text-red-600">Taxa: R$ <span id="sim-taxa-50">{{ number_format(50 * $formaPagamento->taxa_percentual / 100, 2, ',', '.') }}</span></p>
                                <p class="text-gray-600">Líquido: R$ <span id="sim-liq-50">{{ number_format(50 - (50 * $formaPagamento->taxa_percentual / 100), 2, ',', '.') }}</span></p>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <button type="submit" class="btn-primary">
                            Salvar Taxa
                        </button>
                        <a href="{{ route('formas-pagamento.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Cancelar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Atualizar simulador em tempo real
    const taxaInput = document.querySelector('input[name="taxa_percentual"]');
    
    taxaInput.addEventListener('input', function() {
        const taxa = parseFloat(this.value) || 0;
        
        // Simular para R$ 100
        const taxa100 = (100 * taxa) / 100;
        const liq100 = 100 - taxa100;
        document.getElementById('sim-taxa-100').textContent = taxa100.toFixed(2).replace('.', ',');
        document.getElementById('sim-liq-100').textContent = liq100.toFixed(2).replace('.', ',');
        
        // Simular para R$ 50
        const taxa50 = (50 * taxa) / 100;
        const liq50 = 50 - taxa50;
        document.getElementById('sim-taxa-50').textContent = taxa50.toFixed(2).replace('.', ',');
        document.getElementById('sim-liq-50').textContent = liq50.toFixed(2).replace('.', ',');
    });
</script>
@endsection

