<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class UsuarioController extends Controller
{
    public function index()
    {
        if (auth()->user()->tipo_usuario_id !== 1) abort(403);

        $usuarios = User::with('tipoUsuario')
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'cpf', 'tipo_usuario_id'])
            ->map(fn($u) => [
                'id'              => $u->id,
                'name'            => $u->name,
                'email'           => $u->email,
                'cpf'             => $u->cpf,
                'tipo_usuario_id' => $u->tipo_usuario_id,
                'tipo'            => $u->tipoUsuario->nome ?? '-',
            ]);

        return Inertia::render('CadastroUsuario', [
            'usuarios' => $usuarios,
        ]);
    }

    public function store(Request $request)
    {
        if (auth()->user()->tipo_usuario_id !== 1) abort(403);

        $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => 'required|email|unique:users,email',
            'cpf'             => 'required|string|regex:/^\d{3}\.\d{3}\.\d{3}-\d{2}$/|unique:users,cpf',
            'tipo_usuario_id' => 'required|in:1,2',
        ]);

        $cpfNumeros = preg_replace('/\D/', '', $request->cpf);
        $senha = substr($cpfNumeros, 0, 6);

        User::create([
            'name'            => $request->name,
            'email'           => $request->email,
            'cpf'             => $request->cpf,
            'tipo_usuario_id' => $request->tipo_usuario_id,
            'password'        => Hash::make($senha),
        ]);

        return redirect()->route('cadastro-usuario')->with('success', 'Usuário criado! Senha: ' . $senha);
    }

    public function update(Request $request, User $user)
    {
        if (auth()->user()->tipo_usuario_id !== 1) abort(403);

        $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => 'required|email|unique:users,email,' . $user->id,
            'cpf'             => 'required|string|regex:/^\d{3}\.\d{3}\.\d{3}-\d{2}$/|unique:users,cpf,' . $user->id,
            'tipo_usuario_id' => 'required|in:1,2',
        ]);

        $user->update($request->only(['name', 'email', 'cpf', 'tipo_usuario_id']));

        return redirect()->route('cadastro-usuario')->with('success', 'Usuário atualizado!');
    }

    public function destroy(User $user)
    {
        if (auth()->user()->tipo_usuario_id !== 1) abort(403);

        if ($user->id === auth()->id()) {
            return redirect()->route('cadastro-usuario')->with('error', 'Você não pode deletar seu próprio usuário.');
        }

        $user->delete();
        return redirect()->route('cadastro-usuario')->with('success', 'Usuário removido!');
    }
}