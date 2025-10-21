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
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('avatar-preview').src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });

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
