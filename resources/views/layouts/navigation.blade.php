<nav x-data="{ open: false }" class="gradient-navy-gold border-b-4 border-vm-gold shadow-xl">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Avatar Mobile (esquerda) -->
            <div class="sm:hidden">
                <x-avatar 
                    :src="Auth::user()->avatar_url" 
                    :name="Auth::user()->name" 
                    size="md" />
            </div>

            <!-- Logo (centro no mobile, esquerda no desktop) -->
            <div class="flex-1 sm:flex-none flex justify-center sm:justify-start">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 transition-transform duration-300 hover:scale-105">
                        <img src="{{ asset('logo.svg') }}" alt="Esmalteria Vida Maria" class="h-10 w-auto">
                    </a>
                </div>

                <!-- Navigation Links (Desktop) -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        Painel
                    </x-nav-link>
                    
                    <x-nav-link :href="route('agendamentos.agenda')" :active="request()->routeIs('agendamentos.*')">
                        Agenda
                    </x-nav-link>

                    @can('isProprietaria')
                        <x-nav-link :href="route('profissionais.index')" :active="request()->routeIs('profissionais.*')">
                            Profissionais
                        </x-nav-link>
                        
                        <x-nav-link :href="route('clientes.index')" :active="request()->routeIs('clientes.*')">
                            Clientes
                        </x-nav-link>
                        
                        <x-nav-link :href="route('servicos.index')" :active="request()->routeIs('servicos.*')">
                            Serviços
                        </x-nav-link>
                        
                        <x-nav-link :href="route('backup.index')" :active="request()->routeIs('backup.*')">
                            💾 Backup
                        </x-nav-link>
                    @endcan
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2 px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white hover:text-vm-gold focus:outline-none transition ease-in-out duration-150">
                            <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" class="w-8 h-8 rounded-full object-cover ring-2 ring-vm-gold">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ml-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Perfil') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Sair') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Bottom Navigation -->
    <div class="sm:hidden fixed bottom-0 left-0 right-0 bg-vm-navy-800 border-t-4 border-vm-gold shadow-2xl z-50">
        <div class="flex justify-around items-center h-16 px-2">
            <!-- Início -->
            <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center flex-1 py-2 transition-all duration-300 {{ request()->routeIs('dashboard') ? 'text-vm-gold' : 'text-white hover:text-vm-gold-300' }}">
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                <span class="text-xs font-medium">Início</span>
            </a>

            <!-- Agenda -->
            <a href="{{ route('agendamentos.agenda') }}" class="flex flex-col items-center justify-center flex-1 py-2 transition-all duration-300 {{ request()->routeIs('agendamentos.*') ? 'text-vm-gold' : 'text-white hover:text-vm-gold-300' }}">
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span class="text-xs font-medium">Agenda</span>
            </a>

            <!-- Financeiro (todos) -->
            <a href="{{ route('financeiro') }}" class="flex flex-col items-center justify-center flex-1 py-2 transition-all duration-300 {{ request()->routeIs('financeiro') ? 'text-vm-gold' : 'text-white hover:text-vm-gold-300' }}">
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-xs font-medium">Financeiro</span>
            </a>

            <!-- Menu (Hamburger) -->
            <button @click="open = ! open" class="flex flex-col items-center justify-center flex-1 py-2 transition-all duration-300" :class="open ? 'text-vm-gold' : 'text-white hover:text-vm-gold-300'">
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path :class="{'hidden': open, 'inline-flex': ! open }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    <path :class="{'hidden': ! open, 'inline-flex': open }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                <span class="text-xs font-medium">Menu</span>
            </button>
        </div>
    </div>

    <!-- Mobile Menu Modal (quando clicar no hamburger) -->
    <div x-show="open" 
         @click.away="open = false"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-full"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform translate-y-full"
         class="sm:hidden fixed bottom-16 left-0 right-0 bg-white shadow-2xl rounded-t-3xl border-t-4 border-vm-gold z-40 max-h-96 overflow-y-auto"
         style="display: none;">
        
        <!-- User Info -->
        <div class="px-6 py-4 bg-vm-navy-800 rounded-t-3xl">
            <div class="flex items-center gap-3">
                <x-avatar 
                    :src="Auth::user()->avatar_url" 
                    :name="Auth::user()->name" 
                    size="3xl" />
                <div class="flex-1">
                    <div class="font-semibold text-white text-lg">{{ Auth::user()->name }}</div>
                    <div class="text-sm text-vm-gold-300">{{ Auth::user()->email }}</div>
                    <div class="mt-1">
                        <span class="badge-gold text-xs">
                            {{ Auth::user()->isProprietaria() ? '👑 Proprietária' : '💅 Profissional' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Menu Items -->
        <div class="py-3">
            <!-- Clientes (todos podem acessar) -->
            <a href="{{ route('clientes.index') }}" class="flex items-center px-6 py-3 text-vm-navy-800 hover:bg-vm-navy-50 transition-colors {{ request()->routeIs('clientes.*') ? 'bg-vm-gold-50 border-l-4 border-vm-gold' : '' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                <span class="font-medium">Clientes</span>
            </a>

            @can('isProprietaria')
                <div class="border-t border-gray-200 my-2"></div>
                
                <a href="{{ route('profissionais.index') }}" class="flex items-center px-6 py-3 text-vm-navy-800 hover:bg-vm-navy-50 transition-colors {{ request()->routeIs('profissionais.*') ? 'bg-vm-gold-50 border-l-4 border-vm-gold' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span class="font-medium">Profissionais</span>
                </a>

                <a href="{{ route('servicos.index') }}" class="flex items-center px-6 py-3 text-vm-navy-800 hover:bg-vm-navy-50 transition-colors {{ request()->routeIs('servicos.*') ? 'bg-vm-gold-50 border-l-4 border-vm-gold' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                    </svg>
                    <span class="font-medium">Serviços</span>
                </a>

                <a href="{{ route('formas-pagamento.index') }}" class="flex items-center px-6 py-3 text-vm-navy-800 hover:bg-vm-navy-50 transition-colors {{ request()->routeIs('formas-pagamento.*') ? 'bg-vm-gold-50 border-l-4 border-vm-gold' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                    <span class="font-medium">Taxas de Pagamento</span>
                </a>

                <a href="{{ route('backup.index') }}" class="flex items-center px-6 py-3 text-vm-navy-800 hover:bg-vm-navy-50 transition-colors {{ request()->routeIs('backup.*') ? 'bg-vm-gold-50 border-l-4 border-vm-gold' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                    </svg>
                    <span class="font-medium">💾 Backup</span>
                </a>
            @endcan

            <div class="border-t border-gray-200 my-2"></div>

            <a href="{{ route('profile.edit') }}" class="flex items-center px-6 py-3 text-vm-navy-800 hover:bg-vm-navy-50 transition-colors">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <span class="font-medium">Perfil</span>
            </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                <button type="submit" class="w-full flex items-center px-6 py-3 text-red-600 hover:bg-red-50 transition-colors">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    <span class="font-medium">Sair</span>
                </button>
                </form>
        </div>
    </div>
</nav>
