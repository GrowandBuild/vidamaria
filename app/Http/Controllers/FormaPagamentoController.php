<?php

namespace App\Http\Controllers;

use App\Models\FormaPagamento;
use Illuminate\Http\Request;

class FormaPagamentoController extends Controller
{
    public function index()
    {
        $formasPagamento = FormaPagamento::all();
        return view('formas-pagamento.index', compact('formasPagamento'));
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

    public function toggleStatus(FormaPagamento $formaPagamento)
    {
        $formaPagamento->update(['ativo' => !$formaPagamento->ativo]);
        
        return redirect()->route('formas-pagamento.index')
            ->with('success', 'Status atualizado com sucesso!');
    }
}

