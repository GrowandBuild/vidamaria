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

                    @can('isProprietaria')
                    <!-- Seção de Credenciais - Apenas para Administradora -->
                    <div class="border-t pt-6 mt-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">🔐 Credenciais de Acesso</h3>
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                <p class="text-sm text-yellow-800">
                                    <strong>Atenção:</strong> Alterações aqui afetam o login da profissional no sistema.
                                </p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Email de Login</label>
                                <input type="email" name="email" value="{{ old('email', $profissional->user->email) }}" required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-vm-gold focus:ring-vm-gold">
                                <p class="text-xs text-gray-500 mt-1">Email usado para fazer login no sistema</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nova Senha</label>
                                <div class="relative">
                                    <input type="password" name="password" id="password-field"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-vm-gold focus:ring-vm-gold pr-10"
                                           placeholder="Deixe em branco para manter a senha atual">
                                    <button type="button" onclick="togglePassword()" 
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                        <svg id="eye-icon" class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Mínimo 8 caracteres. Deixe em branco para manter a senha atual</p>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <button type="button" onclick="generatePassword()" 
                                    class="px-3 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 text-sm">
                                🎲 Gerar Senha Aleatória
                            </button>
                            <button type="button" onclick="copyPassword()" 
                                    class="px-3 py-2 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 text-sm ml-2">
                                📋 Copiar Senha
                            </button>
                        </div>
                    </div>
                    @endcan

                    <div class="flex gap-3 pt-6">
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

    // Funcionalidades de senha
    function togglePassword() {
        const passwordField = document.getElementById('password-field');
        const eyeIcon = document.getElementById('eye-icon');
        
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            eyeIcon.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
            `;
        } else {
            passwordField.type = 'password';
            eyeIcon.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            `;
        }
    }

    function generatePassword() {
        const length = 12;
        const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%&*";
        let password = "";
        
        for (let i = 0; i < length; i++) {
            password += charset.charAt(Math.floor(Math.random() * charset.length));
        }
        
        document.getElementById('password-field').value = password;
        
        // Mostrar notificação
        showNotification('Senha gerada com sucesso!', 'success');
    }

    function copyPassword() {
        const passwordField = document.getElementById('password-field');
        if (passwordField.value) {
            passwordField.select();
            document.execCommand('copy');
            showNotification('Senha copiada para a área de transferência!', 'success');
        } else {
            showNotification('Digite ou gere uma senha primeiro!', 'error');
        }
    }

    function showNotification(message, type) {
        const notification = document.createElement('div');
        const bgColor = type === 'success' ? '#10B981' : '#EF4444';
        notification.innerHTML = `
            <div style="position: fixed; top: 80px; right: 20px; background: ${bgColor}; color: white; padding: 16px 20px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); z-index: 1001; max-width: 300px; font-size: 14px; font-weight: 500; animation: slideIn 0.3s ease;">
                ${message}
            </div>
        `;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 3000);
    }

    // Adicionar CSS para animação
    if (!document.querySelector('#notification-styles')) {
        const style = document.createElement('style');
        style.id = 'notification-styles';
        style.textContent = `
            @keyframes slideIn {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
        `;
        document.head.appendChild(style);
    }
</script>
@endsection

