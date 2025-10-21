<?php

namespace App\Http\Controllers;

use App\Models\FormaPagamento;
use Illuminate\Http\Request;

class FormaPagamentoController extends Controller
{
    public function index()
    {
        $formasPagamento = FormaPagamento::orderBy('nome')->get();
        return view('formas-pagamento.index', compact('formasPagamento'));
    }

    public function create()
    {
        return view('formas-pagamento.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255|unique:formas_pagamento,nome',
            'taxa_percentual' => 'required|numeric|min:0|max:100',
        ]);

        FormaPagamento::create($request->all());

        return redirect()->route('formas-pagamento.index')
            ->with('success', 'Forma de pagamento criada com sucesso!');
    }

    public function edit(FormaPagamento $formaPagamento)
    {
        return view('formas-pagamento.edit', compact('formaPagamento'));
    }

    public function update(Request $request, FormaPagamento $formaPagamento)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'taxa_percentual' => 'required|numeric|min:0|max:100',
        ]);

        $formaPagamento->update($request->all());

        return redirect()->route('formas-pagamento.index')
            ->with('success', 'Taxa atualizada com sucesso!');
    }

    public function destroy(FormaPagamento $formaPagamento)
    {
        // Verificar se a forma de pagamento está sendo usada em agendamentos
        $agendamentosComEstaForma = \App\Models\Agendamento::where('forma_pagamento_id', $formaPagamento->id)->count();
        
        if ($agendamentosComEstaForma > 0) {
            return redirect()->route('formas-pagamento.index')
                ->with('error', 'Não é possível excluir esta forma de pagamento pois ela está sendo usada em ' . $agendamentosComEstaForma . ' agendamento(s).');
        }
        
        $formaPagamento->delete();
        
        return redirect()->route('formas-pagamento.index')
            ->with('success', 'Forma de pagamento excluída com sucesso!');
    }

    public function toggleStatus(FormaPagamento $formaPagamento)
    {
        $formaPagamento->update(['ativo' => !$formaPagamento->ativo]);
        
        return redirect()->route('formas-pagamento.index')
            ->with('success', 'Status atualizado com sucesso!');
    }
}

