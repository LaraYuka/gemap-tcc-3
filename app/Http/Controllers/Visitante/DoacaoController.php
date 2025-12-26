<?php

namespace App\Http\Controllers\Visitante;

use App\Http\Controllers\Controller;
use App\Models\Doacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DoacaoController extends Controller
{
    public function index()
    {
        $doacoes = Doacao::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('visitante.doacoes.index', compact('doacoes'));
    }

    public function create()
    {
        return view('visitante.doacoes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome_doador' => 'required|string|max:255',
            'telefone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'tipo_doacao' => 'required|in:Brinquedo,Livro,Roupa,Material Pedagógico,Alimento,Outro',
            'descricao' => 'required|string',
            'quantidade' => 'required|integer|min:1',
            'estado_conservacao' => 'required|in:novo,bom,usado',
            'data_doacao' => 'required|date',
            'fotos.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $validated['user_id'] = auth()->id();

        if ($request->hasFile('fotos')) {
            $fotos = [];
            foreach ($request->file('fotos') as $foto) {
                $path = $foto->store('doacoes', 'public');
                $fotos[] = $path;
            }
            $validated['fotos'] = $fotos;
        }

        Doacao::create($validated);

        return redirect()->route('visitante.doacoes.index')
            ->with('success', 'Doação registrada com sucesso! Aguarde a análise do administrador.');
    }

    public function show(Doacao $doacao)
    {
        if ($doacao->user_id !== auth()->id()) {
            abort(403);
        }

        return view('visitante.doacoes.show', compact('doacao'));
    }
}
