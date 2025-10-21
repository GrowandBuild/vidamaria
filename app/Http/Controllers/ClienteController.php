<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClienteController extends Controller
{
    public function index()
    {
        $clientes = Cliente::withCount('agendamentos')->get();
        return view('clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('clientes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'telefone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'observacoes' => 'nullable|string',
        ]);

        $data = $request->except('avatar');
        
        // Upload avatar se fornecido
        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        Cliente::create($data);

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente cadastrado com sucesso!');
    }

    public function show(Cliente $cliente)
    {
        $cliente->load('agendamentos.servico', 'agendamentos.profissional', 'agendamentos.pagamentos');
        
        return view('clientes.show', compact('cliente'));
    }

    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'telefone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'observacoes' => 'nullable|string',
        ]);

        $data = $request->except('avatar');
        
        // Upload novo avatar se fornecido
        if ($request->hasFile('avatar')) {
            // Deletar avatar antigo se existir
            if ($cliente->avatar && Storage::disk('public')->exists($cliente->avatar)) {
                Storage::disk('public')->delete($cliente->avatar);
            }
            
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $cliente->update($data);

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente atualizado com sucesso!');
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();
        
        return redirect()->route('clientes.index')
            ->with('success', 'Cliente removido com sucesso!');
    }
}

