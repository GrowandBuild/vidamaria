@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-6">
                    {{ __('Backup e Manutenção') }}
                </h2>

    <!-- Status do Backup -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold mb-4">Status do Backup</h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            @if($lastBackup)
                                <div class="flex items-center space-x-3">
                                    <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                    <div>
                                        <p class="text-sm text-gray-600">Último Backup</p>
                                        <p class="text-lg font-semibold text-gray-900">
                                            {{ \Carbon\Carbon::parse($lastBackup['created_at'])->format('d/m/Y H:i:s') }}
                                        </p>
                </div>
            </div>
                                <div class="mt-2 text-sm text-gray-500">
                                    Tamanho: {{ number_format($lastBackup['size'] / 1024, 2) }} KB
                                </div>
                            @else
                                <div class="flex items-center space-x-3">
                                    <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                                    <p class="text-gray-600">Nenhum backup encontrado</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Estatísticas -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold mb-4">Estatísticas do Sistema</h3>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="bg-blue-50 rounded-lg p-4">
                                <div class="text-2xl font-bold text-blue-600">{{ $stats['usuarios'] }}</div>
                                <div class="text-sm text-blue-800">Usuários</div>
                            </div>
                            <div class="bg-green-50 rounded-lg p-4">
                                <div class="text-2xl font-bold text-green-600">{{ $stats['agendamentos'] }}</div>
                                <div class="text-sm text-green-800">Agendamentos</div>
                            </div>
                            <div class="bg-purple-50 rounded-lg p-4">
                                <div class="text-2xl font-bold text-purple-600">{{ $stats['clientes'] }}</div>
                                <div class="text-sm text-purple-800">Clientes</div>
                            </div>
                            <div class="bg-orange-50 rounded-lg p-4">
                                <div class="text-2xl font-bold text-orange-600">{{ $stats['profissionais'] }}</div>
                                <div class="text-sm text-orange-800">Profissionais</div>
                            </div>
                        </div>
                    </div>

                    <!-- Ações de Backup -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold mb-4">Ações de Backup</h3>
                        <div class="flex flex-wrap gap-4">
                            <form method="POST" action="{{ route('backup.create') }}" class="inline">
                                @csrf
                                <button type="submit" 
                                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                                    📦 Criar Backup
                                </button>
                            </form>
                            
                            @if($lastBackup)
                                <a href="{{ route('backup.download') }}" 
                                   class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                                    ⬇️ Baixar Backup
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- ZONA PERIGOSA - RESET DO BANCO -->
                    <div class="border-t-4 border-red-500 bg-red-50 rounded-lg p-6">
                        <div class="flex items-center mb-4">
                            <div class="text-red-600 text-2xl mr-3">⚠️</div>
                            <h3 class="text-lg font-semibold text-red-800">Zona Perigosa - Reset do Banco de Dados</h3>
                </div>

                        <div class="bg-red-100 border border-red-300 rounded-lg p-4 mb-4">
                            <h4 class="font-semibold text-red-800 mb-2">⚠️ ATENÇÃO - OPERAÇÃO IRREVERSÍVEL</h4>
                            <ul class="text-sm text-red-700 space-y-1">
                                <li>• <strong>TODOS os dados serão perdidos permanentemente</strong></li>
                                <li>• Agendamentos, clientes, profissionais - tudo será apagado</li>
                                <li>• O sistema voltará ao estado inicial (dados de exemplo)</li>
                                <li>• Esta ação não pode ser desfeita</li>
                                <li>• Um backup será criado automaticamente antes do reset</li>
                            </ul>
                        </div>

                        <div class="bg-yellow-100 border border-yellow-300 rounded-lg p-4 mb-4">
                            <h4 class="font-semibold text-yellow-800 mb-2">📋 O que será restaurado:</h4>
                            <ul class="text-sm text-yellow-700 space-y-1">
                                <li>• Conta da proprietária: val@vidamaria.com.br / admin123</li>
                                <li>• Conta do desenvolvedor: alexandre@dev.com / dev123</li>
                                <li>• Serviços padrão da esmalteria</li>
                                <li>• Formas de pagamento</li>
                                <li>• Clientes de exemplo</li>
                            </ul>
                </div>

                        <form method="POST" action="{{ route('backup.reset') }}" 
                              onsubmit="return confirmReset()" class="inline">
                            @csrf
                            <button type="submit" 
                                    class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-lg transition duration-200 shadow-lg">
                                🔥 RESETAR BANCO DE DADOS
                    </button>
                        </form>
    </div>

            </div>
        </div>
    </div>
</div>

<script>
    function confirmReset() {
        const confirmMessage = `⚠️ CONFIRMAÇÃO FINAL ⚠️

Você está prestes a APAGAR TODOS os dados do sistema!

Isso inclui:
• Todos os agendamentos
• Todos os clientes
• Todos os profissionais (exceto as contas admin)
• Todos os pagamentos
• Todos os dados de produção

Esta ação é IRREVERSÍVEL!

Digite "RESETAR" para confirmar:`;

        const userInput = prompt(confirmMessage);
        
        if (userInput === "RESETAR") {
            const finalConfirm = confirm("🚨 ÚLTIMA CONFIRMAÇÃO 🚨\n\nVocê tem CERTEZA ABSOLUTA que quer apagar TUDO?\n\nClique OK apenas se tiver certeza total!");
            return finalConfirm;
        }
        
        return false;
    }
</script>
@endsection