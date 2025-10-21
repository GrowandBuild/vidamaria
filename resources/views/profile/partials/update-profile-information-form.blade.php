<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <!-- Avatar -->
        <div>
            <x-input-label for="avatar" value="Foto de Perfil" />
            <div class="mt-2 flex items-center gap-6">
                <!-- Preview da foto -->
                <div class="relative group">
                    <img src="{{ $user->avatar_url }}" 
                         id="avatar-preview"
                         alt="{{ $user->name }}" 
                         class="w-24 h-24 rounded-full object-cover ring-4 ring-vm-gold shadow-lg transition-all duration-300 group-hover:ring-vm-gold-600">
                    <!-- Overlay de upload -->
                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 rounded-full transition-all duration-300 flex items-center justify-center opacity-0 group-hover:opacity-100">
                        <span class="text-white text-xs font-semibold">Trocar</span>
                    </div>
                </div>
                
                <!-- Controles -->
                <div class="flex-1 space-y-3">
                    <!-- Input de arquivo estilizado -->
                    <div class="relative">
                        <input type="file" 
                               id="avatar-input"
                               name="avatar" 
                               accept="image/*"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        <div class="px-4 py-3 bg-vm-gold text-vm-navy-900 rounded-lg font-semibold text-center hover:bg-vm-gold-600 transition-colors cursor-pointer">
                            📸 Escolher Nova Foto
                        </div>
                    </div>
                    
                    <!-- Botão remover foto (se tiver foto) -->
                    @if($user->avatar)
                    <button type="button" 
                            id="remove-avatar-btn"
                            class="w-full px-4 py-2 bg-red-100 text-red-700 rounded-lg font-medium hover:bg-red-200 transition-colors">
                        🗑️ Remover Foto
                    </button>
                    @endif
                    
                    <p class="text-xs text-gray-500">JPG, PNG ou GIF (máx. 2MB)</p>
                </div>
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
        </div>

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>

    <script>
        // Verificar permissões de mídia
        async function checkMediaPermissions() {
            try {
                // Verificar se a API de permissões está disponível
                if ('permissions' in navigator) {
                    const permission = await navigator.permissions.query({ name: 'camera' });
                    return permission.state;
                }
                return 'unknown';
            } catch (error) {
                console.log('Erro ao verificar permissões:', error);
                return 'unknown';
            }
        }

        // Mostrar modal de permissão
        function showPermissionModal() {
            const modal = document.createElement('div');
            modal.innerHTML = `
                <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 9999; display: flex; align-items: center; justify-content: center;">
                    <div style="background: white; padding: 30px; border-radius: 15px; max-width: 400px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
                        <div style="font-size: 48px; margin-bottom: 20px;">📷</div>
                        <h3 style="color: #0A1647; margin-bottom: 15px; font-size: 24px;">Permissão de Câmera Necessária</h3>
                        <p style="color: #666; margin-bottom: 25px; line-height: 1.5;">
                            Para fazer upload de fotos, precisamos da permissão de acesso à câmera e galeria.
                        </p>
                        <div style="display: flex; gap: 10px; justify-content: center; flex-direction: column;">
                            <button id="open-settings-btn" style="background: #D4AF37; color: #0A1647; border: none; padding: 12px 24px; border-radius: 8px; font-weight: bold; cursor: pointer; font-size: 16px;">
                                ⚙️ Ir para Configurações
                            </button>
                            <button id="try-again-btn" style="background: #3B82F6; color: white; border: none; padding: 12px 24px; border-radius: 8px; cursor: pointer; font-size: 16px;">
                                🔄 Tentar Novamente
                            </button>
                            <button id="cancel-permission-btn" style="background: #f5f5f5; color: #666; border: none; padding: 12px 24px; border-radius: 8px; cursor: pointer; font-size: 16px;">
                                Cancelar
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
            
            // Event listeners
            document.getElementById('open-settings-btn').addEventListener('click', function() {
                // Tentar abrir configurações do navegador
                if (navigator.userAgent.includes('Chrome')) {
                    // Para Chrome, mostrar instruções
                    alert('Para habilitar a permissão:\n\n1. Clique no ícone de câmera na barra de endereços\n2. Selecione "Permitir"\n3. Recarregue a página');
                } else if (navigator.userAgent.includes('Safari')) {
                    alert('Para habilitar a permissão:\n\n1. Vá em Configurações > Safari\n2. Privacidade e Segurança > Câmera\n3. Permita para este site');
                } else {
                    alert('Para habilitar a permissão:\n\n1. Clique no ícone de câmera na barra de endereços\n2. Selecione "Permitir"\n3. Recarregue a página');
                }
                document.body.removeChild(modal);
            });
            
            document.getElementById('try-again-btn').addEventListener('click', function() {
                document.body.removeChild(modal);
                // Tentar novamente
                document.getElementById('avatar-input').click();
            });
            
            document.getElementById('cancel-permission-btn').addEventListener('click', function() {
                document.body.removeChild(modal);
            });
        }

        // Preview da nova foto
        document.getElementById('avatar-input').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validar tamanho (2MB)
                if (file.size > 2 * 1024 * 1024) {
                    alert('A imagem deve ter no máximo 2MB!');
                    this.value = '';
                    return;
                }
                
                // Validar tipo de arquivo
                const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Formato de arquivo não suportado! Use JPG, PNG ou GIF.');
                    this.value = '';
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('avatar-preview');
                    preview.src = e.target.result;
                    // Forçar reload da imagem
                    preview.onload = function() {
                        console.log('Imagem carregada com sucesso');
                    };
                }
                reader.readAsDataURL(file);
            }
        });

        // Interceptar clique no input de arquivo
        document.getElementById('avatar-input').addEventListener('click', async function(e) {
            // Verificar se é mobile
            const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
            
            if (isMobile) {
                // Verificar permissões antes de abrir o seletor
                const permission = await checkMediaPermissions();
                
                if (permission === 'denied') {
                    e.preventDefault();
                    showPermissionModal();
                    return;
                }
            }
        });

        // Detectar quando o usuário cancela a seleção de arquivo
        document.getElementById('avatar-input').addEventListener('cancel', function() {
            console.log('Seleção de arquivo cancelada');
        });

        // Detectar erros de permissão
        document.getElementById('avatar-input').addEventListener('error', function(e) {
            console.log('Erro no input de arquivo:', e);
            if (e.error && e.error.name === 'NotAllowedError') {
                showPermissionModal();
            }
        });

        // Detectar quando a página volta ao foco (usuário pode ter ido às configurações)
        let permissionCheckTimeout;
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                // Página voltou ao foco, verificar permissões após um pequeno delay
                clearTimeout(permissionCheckTimeout);
                permissionCheckTimeout = setTimeout(async () => {
                    const permission = await checkMediaPermissions();
                    if (permission === 'granted') {
                        // Mostrar notificação de sucesso
                        const notification = document.createElement('div');
                        notification.innerHTML = `
                            <div style="position: fixed; top: 80px; right: 20px; background: #10B981; color: white; padding: 16px 20px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); z-index: 1001; max-width: 300px; font-size: 14px; font-weight: 500; animation: slideIn 0.3s ease;">
                                ✅ Permissão concedida! Agora você pode fazer upload de fotos.
                            </div>
                        `;
                        document.body.appendChild(notification);
                        
                        // Remover após 3 segundos
                        setTimeout(() => {
                            if (notification.parentNode) {
                                notification.remove();
                            }
                        }, 3000);
                    }
                }, 1000);
            }
        });

        // Adicionar CSS para animação
        if (!document.querySelector('#permission-styles')) {
            const style = document.createElement('style');
            style.id = 'permission-styles';
            style.textContent = `
                @keyframes slideIn {
                    from { transform: translateX(100%); opacity: 0; }
                    to { transform: translateX(0); opacity: 1; }
                }
            `;
            document.head.appendChild(style);
        }

        // Remover foto
        document.getElementById('remove-avatar-btn')?.addEventListener('click', function() {
            if (confirm('Deseja realmente remover sua foto de perfil?')) {
                // Resetar input
                document.getElementById('avatar-input').value = '';
                
                // Voltar para foto padrão
                document.getElementById('avatar-preview').src = '{{ asset("logo.svg") }}';
                
                // Adicionar flag para remover foto
                const form = document.querySelector('form');
                const removeInput = document.createElement('input');
                removeInput.type = 'hidden';
                removeInput.name = 'remove_avatar';
                removeInput.value = '1';
                form.appendChild(removeInput);
            }
        });

        // Melhorar feedback visual
        document.getElementById('avatar-input').addEventListener('click', function() {
            this.value = '';
        });
    </script>
</section>
