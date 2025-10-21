<?php

namespace App\Http\Controllers;

use App\Models\Profissional;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfissionalController extends Controller
{
    public function index()
    {
        $profissionais = Profissional::with('user')->get();
        return view('profissionais.index', compact('profissionais'));
    }

    public function create()
    {
        return view('profissionais.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'telefone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8',
            'percentual_comissao' => 'required|numeric|min:0|max:100',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Upload avatar
        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
        }

        // Criar usuário
        $user = User::create([
            'name' => $request->nome,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'tipo' => 'profissional',
            'avatar' => $avatarPath,
        ]);

        // Criar profissional
        Profissional::create([
            'user_id' => $user->id,
            'telefone' => $request->telefone,
            'percentual_comissao' => $request->percentual_comissao,
        ]);

        return redirect()->route('profissionais.index')
            ->with('success', 'Profissional cadastrado com sucesso!');
    }

    public function edit(Profissional $profissional)
    {
        $profissional->load('user');
        return view('profissionais.edit', compact('profissional'));
    }

    public function update(Request $request, Profissional $profissional)
    {
        $validationRules = [
            'nome' => 'required|string|max:255',
            'telefone' => 'nullable|string|max:20',
            'percentual_comissao' => 'required|numeric|min:0|max:100',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        // Adicionar validações de credenciais apenas para proprietária
        if (auth()->user()->isProprietaria()) {
            $validationRules['email'] = 'required|email|max:255|unique:users,email,' . $profissional->user_id;
            $validationRules['password'] = 'nullable|string|min:8';
        }

        $request->validate($validationRules);

        // Debug: verificar se arquivo foi enviado
        \Log::info('Avatar upload debug', [
            'has_file' => $request->hasFile('avatar'),
            'file_valid' => $request->hasFile('avatar') ? $request->file('avatar')->isValid() : false,
            'old_avatar' => $profissional->user ? $profissional->user->avatar : 'N/A'
        ]);

        $dados = [
            'telefone' => $request->telefone,
            'percentual_comissao' => $request->percentual_comissao,
        ];

        // Upload novo avatar se fornecido
        if ($request->hasFile('avatar')) {
            // Deletar avatar antigo se existir
            if ($profissional->user->avatar && Storage::disk('public')->exists($profissional->user->avatar)) {
                Storage::disk('public')->delete($profissional->user->avatar);
            }
            
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            
            // Debug: verificar se upload foi bem sucedido
            \Log::info('Avatar uploaded successfully', [
                'new_path' => $avatarPath,
                'file_exists' => Storage::disk('public')->exists($avatarPath)
            ]);
        }

        $profissional->update($dados);

        // Atualizar user também
        $dadosUser = ['name' => $request->nome];
        if (isset($avatarPath)) {
            $dadosUser['avatar'] = $avatarPath;
        }

        // Atualizar credenciais se for proprietária
        if (auth()->user()->isProprietaria()) {
            $dadosUser['email'] = $request->email;
            
            // Atualizar senha apenas se fornecida
            if ($request->filled('password')) {
                $dadosUser['password'] = Hash::make($request->password);
            }
        }

        $profissional->user->update($dadosUser);

        return redirect()->route('profissionais.index')
            ->with('success', 'Profissional atualizado com sucesso!');
    }

    public function destroy(Profissional $profissional)
    {
        $profissional->user->delete();
        return redirect()->route('profissionais.index')
            ->with('success', 'Profissional removido com sucesso!');
    }

    public function toggleStatus(Profissional $profissional)
    {
        $profissional->update(['ativo' => !$profissional->ativo]);
        
        return redirect()->route('profissionais.index')
            ->with('success', 'Status atualizado com sucesso!');
    }
}

