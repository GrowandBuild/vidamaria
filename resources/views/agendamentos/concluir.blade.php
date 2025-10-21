@extends('layouts.app')

@section('title', 'Concluir Atendimento')

@section('content')
<div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h2 class="text-2xl font-bold mb-6">Finalizar Pagamento</h2>

            <!-- Resumo do Atendimento -->
            <div class="bg-gray-50 p-4 rounded-lg mb-6">
                <h3 class="font-semibold mb-2">Detalhes do Atendimento</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-600">Cliente:</span>
                        <span class="font-medium ml-2">{{ $agendamento->nome_cliente }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Profissional:</span>
                        <span class="font-medium ml-2">{{ $agendamento->profissional->nome }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Serviço:</span>
                        <span class="font-medium ml-2">{{ $agendamento->servico ? $agendamento->servico->nome : 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Valor do Serviço:</span>
                        <span class="font-medium ml-2 text-lg text-green-600">R$ {{ $agendamento->servico ? number_format($agendamento->servico->preco, 2, ',', '.') : '0,00' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Comissão Profissional:</span>
                        <span class="font-medium ml-2">{{ $agendamento->profissional->percentual_comissao }}%</span>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('agendamentos.finalizar', $agendamento) }}" id="pagamentoForm">
                @csrf

                <div class="space-y-6">
                    <h3 class="text-lg font-semibold">Formas de Pagamento</h3>
                    
                    <div id="pagamentos-container">
                        <div class="pagamento-item border p-4 rounded-lg mb-4 bg-white">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Forma de Pagamento *</label>
                                    <select name="pagamentos[0][forma_pagamento_id]" required 
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-vm-gold focus:ring-vm-gold forma-pagamento-select"
                                            data-index="0">
                                        <option value="">Selecione...</option>
                                        @foreach($formasPagamento as $forma)
                                            <option value="{{ $forma->id }}" data-taxa="{{ $forma->taxa_percentual }}">
                                                {{ $forma->nome }} 
                                                @if($forma->taxa_percentual > 0)
                                                    (Taxa: {{ $forma->taxa_percentual }}%)
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Gorjeta (opcional)</label>
                                    <input type="number" name="pagamentos[0][gorjeta]" step="0.01" min="0" value="0"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-vm-gold focus:ring-vm-gold gorjeta-input"
                                           data-index="0">
                                </div>
                            </div>
                            
                            <!-- Valor do serviço (hidden, pego automaticamente) -->
                            <input type="hidden" name="pagamentos[0][valor]" value="{{ $agendamento->servico ? $agendamento->servico->preco : 0 }}" class="valor-input" data-index="0">
                            
                            <div class="mt-3 text-sm text-gray-600" id="calc-0">
                                <div class="grid grid-cols-3 gap-2 p-3 bg-vm-navy-50 rounded-lg">
                                    <div class="text-center">
                                        <div class="text-xs text-gray-500">Taxa</div>
                                        <div class="font-bold text-red-600 taxa-valor">R$ 0,00</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-xs text-gray-500">Profissional</div>
                                        <div class="font-bold text-blue-600 prof-valor">R$ 0,00</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-xs text-gray-500">Empresa</div>
                                        <div class="font-bold text-green-600 emp-valor">R$ 0,00</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="button" id="addPagamento" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">
                        + Adicionar Forma de Pagamento
                    </button>

                    <!-- Resumo Total -->
                    <div class="bg-indigo-50 p-4 rounded-lg">
                        <h3 class="font-semibold mb-3">Resumo do Pagamento</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                            <div>
                                <div class="text-gray-600">Total Pago</div>
                                <div class="text-2xl font-bold text-gray-800" id="total-pago">R$ 0,00</div>
                            </div>
                            <div>
                                <div class="text-gray-600">Total Taxas</div>
                                <div class="text-xl font-semibold text-red-600" id="total-taxas">R$ 0,00</div>
                            </div>
                            <div>
                                <div class="text-gray-600">Profissional</div>
                                <div class="text-xl font-semibold text-blue-600" id="total-profissional">R$ 0,00</div>
                            </div>
                            <div>
                                <div class="text-gray-600">Empresa</div>
                                <div class="text-xl font-semibold text-green-600" id="total-empresa">R$ 0,00</div>
                            </div>
                        </div>
                    </div>

                    <!-- Botões -->
                    <div class="flex gap-3">
                        <button type="submit" class="px-6 py-3 bg-green-600 text-white rounded-md hover:bg-green-700 font-semibold">
                            Finalizar Atendimento
                        </button>
                        <a href="{{ route('agendamentos.agenda') }}" class="px-6 py-3 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Cancelar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let pagamentoIndex = 1;
    const percentualComissao = {{ $agendamento->profissional->percentual_comissao }};

    function calcularValores(index) {
        const select = document.querySelector(`select[data-index="${index}"]`);
        const valorInput = document.querySelector(`input[name="pagamentos[${index}][valor]"]`);
        
        if (!select || !valorInput) return;
        
        const selectedOption = select.options[select.selectedIndex];
        const taxa = parseFloat(selectedOption.dataset.taxa || 0);
        const valor = parseFloat(valorInput.value || 0);
        
        const taxaValor = (valor * taxa) / 100;
        const valorLiquido = valor - taxaValor;
        const valorProf = (valorLiquido * percentualComissao) / 100;
        const valorEmp = valorLiquido - valorProf;
        
        const calcDiv = document.getElementById(`calc-${index}`);
        if (calcDiv) {
            calcDiv.querySelector('.taxa-valor').textContent = `R$ ${taxaValor.toFixed(2).replace('.', ',')}`;
            calcDiv.querySelector('.prof-valor').textContent = `R$ ${valorProf.toFixed(2).replace('.', ',')}`;
            calcDiv.querySelector('.emp-valor').textContent = `R$ ${valorEmp.toFixed(2).replace('.', ',')}`;
        }
        
        calcularTotais();
    }

    function calcularTotais() {
        let totalPago = 0;
        let totalTaxas = 0;
        let totalProf = 0;
        let totalEmp = 0;
        
        document.querySelectorAll('.valor-input').forEach((input, idx) => {
            const select = document.querySelector(`select[data-index="${input.dataset.index}"]`);
            if (!select || !select.value) return;
            
            const selectedOption = select.options[select.selectedIndex];
            const taxa = parseFloat(selectedOption.dataset.taxa || 0);
            const valor = parseFloat(input.value || 0);
            const gorjetaInput = document.querySelector(`input[name="pagamentos[${input.dataset.index}][gorjeta]"]`);
            const gorjeta = parseFloat(gorjetaInput ? gorjetaInput.value : 0) || 0;
            
            const taxaValor = (valor * taxa) / 100;
            const valorLiquido = valor - taxaValor;
            const valorProf = (valorLiquido * percentualComissao) / 100;
            const valorEmp = valorLiquido - valorProf;
            
            totalPago += valor;
            totalTaxas += taxaValor;
            totalProf += valorProf + gorjeta;
            totalEmp += valorEmp;
        });
        
        document.getElementById('total-pago').textContent = `R$ ${totalPago.toFixed(2).replace('.', ',')}`;
        document.getElementById('total-taxas').textContent = `R$ ${totalTaxas.toFixed(2).replace('.', ',')}`;
        document.getElementById('total-profissional').textContent = `R$ ${totalProf.toFixed(2).replace('.', ',')}`;
        document.getElementById('total-empresa').textContent = `R$ ${totalEmp.toFixed(2).replace('.', ',')}`;
    }
    
    // Calcular ao carregar (já com o valor do serviço)
    document.addEventListener('DOMContentLoaded', function() {
        calcularValores(0);
    });

    document.getElementById('addPagamento').addEventListener('click', function() {
        const container = document.getElementById('pagamentos-container');
        const newItem = document.querySelector('.pagamento-item').cloneNode(true);
        
        newItem.querySelectorAll('input, select').forEach(el => {
            const name = el.getAttribute('name');
            if (name) {
                el.setAttribute('name', name.replace(/\[\d+\]/, `[${pagamentoIndex}]`));
            }
            if (el.dataset.index !== undefined) {
                el.dataset.index = pagamentoIndex;
            }
            if (el.tagName === 'INPUT') {
                el.value = el.type === 'number' ? '0' : '';
            }
        });
        
        newItem.querySelector('[id^="calc-"]').id = `calc-${pagamentoIndex}`;
        
        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'mt-2 px-3 py-1 bg-red-600 text-white rounded text-sm hover:bg-red-700';
        removeBtn.textContent = 'Remover';
        removeBtn.onclick = function() {
            newItem.remove();
            calcularTotais();
        };
        newItem.appendChild(removeBtn);
        
        container.appendChild(newItem);
        pagamentoIndex++;
        
        attachEventListeners(newItem);
    });

    function attachEventListeners(element) {
        element.querySelectorAll('.forma-pagamento-select, .valor-input, .gorjeta-input').forEach(el => {
            el.addEventListener('change', function() {
                calcularValores(this.dataset.index);
            });
            el.addEventListener('input', function() {
                calcularValores(this.dataset.index);
            });
        });
    }

    attachEventListeners(document);
</script>
@endsection

