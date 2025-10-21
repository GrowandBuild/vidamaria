@extends('layouts.app')

@section('title', 'Editar Agendamento')

@section('content')
<div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h2 class="text-2xl font-bold mb-6">Editar Agendamento</h2>

            <form method="POST" action="{{ route('agendamentos.update', $agendamento) }}">
                @csrf
                @method('PUT')

                <div class="space-y-4">
                    <!-- Profissional -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Profissional *</label>
                        <select name="profissional_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @foreach($profissionais as $prof)
                                <option value="{{ $prof->id }}" {{ $agendamento->profissional_id == $prof->id ? 'selected' : '' }}>
                                    {{ $prof->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Serviço -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Serviço *</label>
                        <select name="servico_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @foreach($servicos as $servico)
                                <option value="{{ $servico->id }}" {{ $agendamento->servico_id == $servico->id ? 'selected' : '' }}>
                                    {{ $servico->nome }} - R$ {{ number_format($servico->preco, 2, ',', '.') }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Data e Hora -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Data *</label>
                            <input type="date" name="data" value="{{ $agendamento->data_hora->format('Y-m-d') }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Hora *</label>
                            <input type="time" name="hora" value="{{ $agendamento->data_hora->format('H:i') }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>

                    <!-- Cliente -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Cliente</label>
                        <select name="cliente_id" id="cliente_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Cliente avulso</option>
                            @foreach($clientes as $cliente)
                                <option value="{{ $cliente->id }}" {{ $agendamento->cliente_id == $cliente->id ? 'selected' : '' }}>
                                    {{ $cliente->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Nome do Cliente Avulso -->
                    <div id="cliente_avulso_div">
                        <label class="block text-sm font-medium text-gray-700">Nome do Cliente *</label>
                        <input type="text" name="cliente_avulso" value="{{ $agendamento->cliente_avulso }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <!-- Observações -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Observações</label>
                        <textarea name="observacoes" rows="3"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ $agendamento->observacoes }}</textarea>
                    </div>

                    <!-- Botões -->
                    <div class="flex gap-3">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            Salvar Alterações
                        </button>
                        <a href="{{ route('agendamentos.agenda') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Cancelar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const clienteSelect = document.getElementById('cliente_id');
    const clienteAvulsoDiv = document.getElementById('cliente_avulso_div');
    const clienteAvulsoInput = document.querySelector('input[name="cliente_avulso"]');
    
    function toggleClienteAvulso() {
        if (clienteSelect.value === '') {
            clienteAvulsoDiv.style.display = 'block';
            clienteAvulsoInput.required = true;
        } else {
            clienteAvulsoDiv.style.display = 'none';
            clienteAvulsoInput.required = false;
            clienteAvulsoInput.value = '';
        }
    }
    
    clienteSelect.addEventListener('change', toggleClienteAvulso);
    toggleClienteAvulso();
</script>
@endsection

