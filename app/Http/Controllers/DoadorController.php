<?php

namespace App\Http\Controllers;

use App\Models\Doador;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DoadorController extends Controller
{
    public function index()
    {
        $doadores = Doador::orderBy('nome')->get();
        return Inertia::render('CadastroDoador', [
            'doadores' => $doadores,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome'     => 'required|string|max:255',
            'cpf'      => 'nullable|string|max:20',
            'email'    => 'nullable|email|max:255',
            'telefone' => 'nullable|string|max:20',
        ]);
        Doador::create($request->only(['nome', 'cpf', 'email', 'telefone']));
        return redirect()->route('cadastro-doador')->with('success', 'Doador criado!');
    }

    public function update(Request $request, Doador $doador)
    {
        $request->validate([
            'nome'     => 'required|string|max:255',
            'cpf'      => 'nullable|string|max:20',
            'email'    => 'nullable|email|max:255',
            'telefone' => 'nullable|string|max:20',
        ]);
        $doador->update($request->only(['nome', 'cpf', 'email', 'telefone']));
        return redirect()->route('cadastro-doador')->with('success', 'Doador atualizado!');
    }

    public function destroy(Doador $doador)
    {
        $doador->delete();
        return redirect()->route('cadastro-doador')->with('success', 'Doador removido!');
    }
}