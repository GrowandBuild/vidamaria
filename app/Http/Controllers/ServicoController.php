<?php

namespace App\Http\Controllers;

use App\Models\Servico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class ServicoController extends Controller
{
    public function index()
    {
        $servicos = Servico::all();
        return view('servicos.index', compact('servicos'));
    }

    public function create()
    {
        return view('servicos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'preco' => 'required|numeric|min:0',
            'duracao_minutos' => 'nullable|integer|min:0',
            'descricao' => 'nullable|string',
        ]);

        Servico::create($request->all());

        // Sincronizar com o seeder
        $this->syncServicesToSeeder();

        return redirect()->route('servicos.index')
            ->with('success', 'Serviço cadastrado com sucesso!');
    }

    public function edit(Servico $servico)
    {
        return view('servicos.edit', compact('servico'));
    }

    public function update(Request $request, Servico $servico)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'preco' => 'required|numeric|min:0',
            'duracao_minutos' => 'nullable|integer|min:0',
            'descricao' => 'nullable|string',
        ]);

        $servico->update($request->all());

        // Sincronizar com o seeder
        $this->syncServicesToSeeder();

        return redirect()->route('servicos.index')
            ->with('success', 'Serviço atualizado com sucesso!');
    }

    public function destroy(Servico $servico)
    {
        $servico->delete();
        
        return redirect()->route('servicos.index')
            ->with('success', 'Serviço removido com sucesso!');
    }

    public function toggleStatus(Servico $servico)
    {
        $servico->update(['ativo' => !$servico->ativo]);
        
        // Sincronizar com o seeder
        $this->syncServicesToSeeder();
        
        return redirect()->route('servicos.index')
            ->with('success', 'Status atualizado com sucesso!');
    }

    private function syncServicesToSeeder()
    {
        try {
            Artisan::call('services:sync-seeder');
        } catch (\Exception $e) {
            // Log do erro mas não interrompe o fluxo
            \Log::warning('Erro ao sincronizar serviços com seeder: ' . $e->getMessage());
        }
    }
}

