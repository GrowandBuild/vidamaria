@extends('layouts.app')

@section('title', 'Nova Forma de Pagamento')

@section('content')
<div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-4 sm:p-6">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-800">➕ Nova Forma de Pagamento</h2>
                <p class="text-sm text-gray-600 mt-1">Adicione uma nova forma de pagamento com sua taxa específica</p>
            </div>

            <form method="POST" action="{{ route('formas-pagamento.store') }}" class="space-y-6">
                @csrf

                <div>
                    <x-input-label for="nome" :value="__('Nome da Forma de Pagamento')" />
                    <x-text-input id="nome" 
                                  name="nome" 
                                  type="text" 
                                  class="mt-1 block w-full" 
                                  :value="old('nome')" 
                                  placeholder="Ex: Maquininha Stone, Maquininha Cielo, etc."
                                  required 
                                  autofocus />
                    <x-input-error :messages="$errors->get('nome')" class="mt-2" />
                    <p class="mt-1 text-sm text-gray-500">Ex: Maquininha Stone, Maquininha Cielo, PicPay, etc.</p>
                </div>

                <div>
                    <x-input-label for="taxa_percentual" :value="__('Taxa Percentual (%)')" />
                    <div class="mt-1 relative">
                        <x-text-input id="taxa_percentual" 
                                      name="taxa_percentual" 
                                      type="number" 
                                      step="0.01"
                                      min="0"
                                      max="100"
                                      class="block w-full pr-8" 
                                      :value="old('taxa_percentual', '0.00')" 
                                      placeholder="0.00"
                                      required />
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">%</span>
                        </div>
                    </div>
                    <x-input-error :messages="$errors->get('taxa_percentual')" class="mt-2" />
                    <p class="mt-1 text-sm text-gray-500">Ex: 2.50 para 2,50% de taxa</p>
                </div>

                <!-- Preview da taxa -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-sm font-medium text-gray-700 mb-2">📊 Preview da Taxa</h3>
                    <div class="text-sm text-gray-600">
                        <p>Para um serviço de <strong>R$ 100,00</strong>:</p>
                        <p id="taxa-preview" class="mt-1">
                            Taxa: <span class="font-semibold text-red-600">R$ 0,00</span>
                        </p>
                        <p class="mt-1">
                            Valor final: <span class="font-semibold text-green-600">R$ 100,00</span>
                        </p>
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-4">
                    <a href="{{ route('formas-pagamento.index') }}" 
                       class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 font-medium rounded-lg transition-colors">
                        Cancelar
                    </a>
                    <x-primary-button>
                        💾 Criar Forma de Pagamento
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const taxaInput = document.getElementById('taxa_percentual');
    const preview = document.getElementById('taxa-preview');
    
    function updatePreview() {
        const taxa = parseFloat(taxaInput.value) || 0;
        const valorServico = 100;
        const valorTaxa = (valorServico * taxa / 100).toFixed(2);
        const valorFinal = (valorServico + parseFloat(valorTaxa)).toFixed(2);
        
        preview.innerHTML = `
            Taxa: <span class="font-semibold ${taxa > 0 ? 'text-red-600' : 'text-green-600'}">R$ ${valorTaxa.replace('.', ',')}</span>
        `;
        
        // Atualizar valor final também
        const valorFinalElement = preview.nextElementSibling;
        if (valorFinalElement) {
            valorFinalElement.innerHTML = `
                Valor final: <span class="font-semibold text-green-600">R$ ${valorFinal.replace('.', ',')}</span>
            `;
        }
    }
    
    taxaInput.addEventListener('input', updatePreview);
    updatePreview(); // Atualizar na carga inicial
});
</script>
@endsection
