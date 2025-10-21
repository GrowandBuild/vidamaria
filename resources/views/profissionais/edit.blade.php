@extends('layouts.app')

@section('title', 'Editar Profissional')

@section('content')
<div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h2 class="text-2xl font-bold mb-6">Editar Profissional</h2>

            <form method="POST" action="{{ route('profissionais.update', $profissional) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <!-- Foto Atual e Upload -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Foto</label>
                        <div class="flex items-center gap-4">
                            <div>
                                <x-avatar 
                                    :src="$profissional->avatar_url" 
                                    :name="$profissional->nome" 
                                    size="2xl"
                                    id="avatar-preview"
                                    class="cursor-pointer hover:ring-4 transition-all duration-200" />
                            </div>
                            <div class="flex-1">
                                <input type="file" name="avatar" accept="image/*" id="avatar-input"
                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-vm-gold file:text-vm-navy-900 hover:file:bg-vm-gold-600">
                                <p class="text-xs text-gray-500 mt-1">Deixe em branco para manter a foto atual</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nome *</label>
                        <input type="text" name="nome" value="{{ old('nome', $profissional->nome) }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-vm-gold focus:ring-vm-gold">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Telefone</label>
                        <input type="text" name="telefone" value="{{ old('telefone', $profissional->telefone) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Percentual de Comissão (%) *</label>
                        <input type="number" name="percentual_comissao" value="{{ old('percentual_comissao', $profissional->percentual_comissao) }}" min="0" max="100" step="0.01" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div class="flex gap-3">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            Salvar
                        </button>
                        <a href="{{ route('profissionais.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Cancelar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('avatar-input').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('avatar-preview').querySelector('img');
                if (img) {
                    img.src = e.target.result;
                }
            }
            reader.readAsDataURL(file);
        }
    });
</script>
@endsection

