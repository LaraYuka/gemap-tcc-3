<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use App\Models\Solicitacao;
use Illuminate\Http\Request;

class SolicitacaoController extends Controller
{
    public function index()
    {
        $solicitacoes = Solicitacao::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('professor.solicitacoes.index', compact('solicitacoes'));
    }

    public function create()
    {
        return view('professor.solicitacoes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome_material' => 'required|string|max:255',
            'descricao' => 'required|string',
            'data_necessaria' => 'required|date|after_or_equal:today',
            'foto' => 'nullable|image|max:2048',
        ]);

        $fotoPath = null;

        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('solicitacoes', 'public');
        }

        Solicitacao::create([
            'user_id' => auth()->id(),
            'nome_material' => $request->nome_material,
            'descricao' => $request->descricao,
            'data_solicitacao' => now(),
            'data_necessaria' => $request->data_necessaria,
            'foto' => $fotoPath,
            'status' => 'em_processo',
        ]);

        return redirect()->route('professor.solicitacoes.index')
            ->with('success', 'Solicitação enviada com sucesso!');
    }

    public function show(Solicitacao $solicitacao)
    {
        if ($solicitacao->user_id !== auth()->id()) {
            abort(403);
        }

        return view('professor.solicitacoes.show', compact('solicitacao'));
    }
}
