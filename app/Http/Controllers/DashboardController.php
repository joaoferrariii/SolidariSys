<?php

namespace App\Http\Controllers;

use App\Models\Doacao;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $doacoes = Doacao::with(['doador', 'item'])
            ->orderBy('data_doacao', 'desc')
            ->get()
            ->map(fn($d) => [
                'doador'     => $d->doador->nome,
                'item'       => $d->item->nome_item,
                'quantidade' => $d->quantidade,
                'data'       => $d->data_doacao->format('d/m/Y'),
            ]);

        $totalDoacoes    = $doacoes->count();
        $totalItens      = $doacoes->sum('quantidade');
        $totalDoadores   = Doacao::distinct('doador_id')->count('doador_id');

        $itensMaisDoados = Doacao::with('item')
            ->selectRaw('item_id, SUM(quantidade) as total')
            ->groupBy('item_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->map(fn($d) => [
                'item'  => $d->item->nome_item,
                'total' => (int) $d->total,
            ]);

        $doadoresMaisAtivos = Doacao::with('doador')
            ->selectRaw('doador_id, COUNT(*) as total')
            ->groupBy('doador_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->map(fn($d) => [
                'doador' => $d->doador->nome,
                'total'  => (int) $d->total,
            ]);

        return Inertia::render('Dashboard', [
            'doacoes'            => $doacoes,
            'totalDoacoes'       => $totalDoacoes,
            'totalItens'         => $totalItens,
            'totalDoadores'      => $totalDoadores,
            'itensMaisDoados'    => $itensMaisDoados,
            'doadoresMaisAtivos' => $doadoresMaisAtivos,
        ]);
    }
}