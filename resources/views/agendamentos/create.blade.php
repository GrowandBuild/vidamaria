@extends('layouts.app')

@section('title', 'Novo Agendamento')

@section('content')
<div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h2 class="text-2xl font-bold mb-6">Novo Agendamento</h2>

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <strong>Erros encontrados:</strong>
                    <ul class="list-disc list-inside mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('agendamentos.store') }}">
                @csrf

                <div class="space-y-4">
                    <!-- Profissional -->
                    @if(auth()->user()->isProprietaria())
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Profissional *</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @foreach($profissionais as $prof)
                                    <label class="relative cursor-pointer">
                                        <input type="radio" name="profissional_id" value="{{ $prof->id }}" 
                                               {{ old('profissional_id') == $prof->id ? 'checked' : '' }}
                                               class="peer sr-only" required>
                                        <div class="flex items-center gap-3 p-3 border-2 border-gray-300 rounded-lg peer-checked:border-vm-gold peer-checked:bg-vm-gold-50 hover:border-vm-gold-300 transition-all">
                                            <x-avatar 
                                                :src="$prof->avatar_url" 
                                                :name="$prof->nome" 
                                                size="lg" />
                                            <div class="flex-1">
                                                <div class="font-semibold text-gray-800">{{ $prof->nome }}</div>
                                                <div class="text-xs text-gray-500">{{ $prof->percentual_comissao }}% comissão</div>
                                            </div>
                                            <div class="peer-checked:block hidden">
                                                <svg class="w-6 h-6 text-vm-gold" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            @error('profissional_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    @else
                        <!-- Profissional cria apenas para si mesma -->
                        <input type="hidden" name="profissional_id" value="{{ $profissionalSelecionado }}">
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 border-l-4 border-blue-700 p-4 rounded-lg flex items-center gap-3">
                            <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-12 h-12 rounded-full object-cover ring-2 ring-white shadow-lg">
                            <div class="text-white">
                                <p class="font-semibold">📌 Agendamento para você</p>
                                <p class="text-sm opacity-90">{{ auth()->user()->name }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Serviço -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Serviço *</label>
                        <select name="servico_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Selecione...</option>
                            @foreach($servicos as $servico)
                                <option value="{{ $servico->id }}" {{ old('servico_id') == $servico->id ? 'selected' : '' }}>
                                    {{ $servico->nome }} - R$ {{ number_format($servico->preco, 2, ',', '.') }}
                                    @if($servico->duracao_minutos) ({{ $servico->duracao_minutos }} min) @endif
                                </option>
                            @endforeach
                        </select>
                        @error('servico_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Data e Hora -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Data *</label>
                            <input type="date" name="data" value="{{ old('data', now()->format('Y-m-d')) }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('data')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Hora *</label>
                            <input type="time" name="hora" value="{{ old('hora') }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('hora')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Cliente -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Cliente</label>
                        <select name="cliente_id" id="cliente_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Cliente avulso (sem cadastro)</option>
                            @foreach($clientes as $cliente)
                                <option value="{{ $cliente->id }}">{{ $cliente->nome }} - {{ $cliente->telefone }}</option>
                            @endforeach
                        </select>
                        @error('cliente_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nome do Cliente Avulso -->
                    <div id="cliente_avulso_div">
                        <label class="block text-sm font-medium text-gray-700">Nome do Cliente *</label>
                        <input type="text" name="cliente_avulso" value="{{ old('cliente_avulso') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('cliente_avulso')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Observações -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Observações</label>
                        <textarea name="observacoes" rows="3"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('observacoes') }}</textarea>
                        @error('observacoes')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Botões -->
                    <div class="flex gap-3">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            Criar Agendamento
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
    // Mostrar/ocultar campo cliente avulso
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

