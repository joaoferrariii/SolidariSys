<?php

namespace App\Http\Controllers;

use App\Models\Doacao;
use App\Models\Doador;
use App\Models\Item;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DoacaoController extends Controller
{
    public function index()
    {
        $doacoes = Doacao::with(['doador', 'item.categoria'])
            ->orderBy('id')
            ->get()
            ->map(fn($d) => [
                'id'         => $d->id,
                'doador_id'  => $d->doador_id,
                'item_id'    => $d->item_id,
                'doador'     => $d->doador->nome,
                'item'       => $d->item->nome_item,
                'categoria'  => $d->item->categoria->nome_categoria,
                'quantidade' => $d->quantidade,
                'data'       => $d->data_doacao->format('Y-m-d'),
            ]);

        $doadores = Doador::orderBy('nome')->get(['id', 'nome']);
        $itens    = Item::orderBy('nome_item')->get(['id', 'nome_item']);

        return Inertia::render('Doacao', [
            'doacoes'  => $doacoes,
            'doadores' => $doadores,
            'itens'    => $itens,
        ]);
    }

    public function update(Request $request, Doacao $doacao)
    {
        $request->validate([
            'doador_id'  => 'required|exists:doadores,id',
            'item_id'    => 'required|exists:itens,id',
            'quantidade' => 'required|integer|min:1',
            'data'       => 'required|date',
        ]);

        $doacao->update([
            'doador_id'   => $request->doador_id,
            'item_id'     => $request->item_id,
            'quantidade'  => $request->quantidade,
            'data_doacao' => substr($request->data, 0, 10),
        ]);

        return redirect()->route('doacao')->with('success', 'Doação atualizada!');
    }

    public function store(Request $request)
    {
        $request->validate([
            'doador_id'  => 'required|exists:doadores,id',
            'item_id'    => 'required|exists:itens,id',
            'quantidade' => 'required|integer|min:1',
            'data'       => 'required|date',
        ]);

        Doacao::create([
            'doador_id'   => $request->doador_id,
            'item_id'     => $request->item_id,
            'quantidade'  => $request->quantidade,
            'data_doacao' => substr($request->data, 0, 10),
            'user_id'     => auth()->id(),
        ]);

        return redirect()->route('doacao')->with('success', 'Doação criada!');
    }

    public function destroy(Doacao $doacao)
    {
        $doacao->delete();
        return redirect()->route('doacao')->with('success', 'Doação removida!');
    }
}