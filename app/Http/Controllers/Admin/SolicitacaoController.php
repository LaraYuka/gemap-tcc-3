<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Solicitacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SolicitacaoController extends Controller
{
    public function index(Request $request)
    {
        $query = Solicitacao::with('user')->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('pesquisa')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->pesquisa . '%');
            });
        }

        $solicitacoes = $query->paginate(10);

        return view('admin.solicitacoes.index', compact('solicitacoes'));
    }

    public function show(Solicitacao $solicitacao)
    {
        $solicitacao->load('user');
        return view('admin.solicitacoes.show', compact('solicitacao'));
    }

    public function aceitar(Solicitacao $solicitacao)
    {
        $solicitacao->update(['status' => 'aceito']);

        return redirect()->route('admin.solicitacoes.index')
            ->with('success', 'Solicitação aceita com sucesso!');
    }

    public function recusar(Solicitacao $solicitacao)
    {
        $solicitacao->update(['status' => 'recusado']);

        return redirect()->route('admin.solicitacoes.index')
            ->with('success', 'Solicitação recusada.');
    }
}
