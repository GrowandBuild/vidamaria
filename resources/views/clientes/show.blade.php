@extends('layouts.app')

@section('title', 'Detalhes do Cliente')

@section('content')
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
        <div class="p-6">
            <div class="flex justify-between items-start">
                <div>
                    <h2 class="text-2xl font-bold">{{ $cliente->nome }}</h2>
                    <p class="text-gray-600 mt-1">{{ $cliente->telefone }} • {{ $cliente->email }}</p>
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-600">Total Gasto</div>
                    <div class="text-3xl font-bold text-green-600">R$ {{ number_format($cliente->totalGasto(), 2, ',', '.') }}</div>
                    <div class="text-xs text-gray-500">Lucro gerado: R$ {{ number_format($cliente->lucroGerado(), 2, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold mb-4">Histórico de Atendimentos</h3>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Serviço</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Profissional</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Valor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($cliente->agendamentos as $agendamento)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $agendamento->data_hora->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $agendamento->servico ? $agendamento->servico->nome : 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $agendamento->profissional->nome }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">R$ {{ number_format($agendamento->valorTotal(), 2, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs rounded-full 
                                    @if($agendamento->status == 'concluido') bg-green-100 text-green-800
                                    @elseif($agendamento->status == 'agendado') bg-blue-100 text-blue-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ ucfirst($agendamento->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">Nenhum atendimento</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

