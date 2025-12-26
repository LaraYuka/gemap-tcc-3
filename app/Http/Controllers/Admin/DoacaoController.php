<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doacao;
use App\Models\Material;
use Illuminate\Http\Request;

class DoacaoController extends Controller
{
    public function index(Request $request)
    {
        $query = Doacao::with('user')->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tipo_doacao')) {
            $query->where('tipo_doacao', $request->tipo_doacao);
        }

        $doacoes = $query->paginate(15);

        $stats = [
            'total' => Doacao::count(),
            'pendentes' => Doacao::where('status', 'pendente')->count(),
            'aprovadas' => Doacao::where('status', 'aprovado')->count(),
            'recebidas' => Doacao::where('status', 'recebido')->count(),
        ];

        return view('admin.doacoes.index', compact('doacoes', 'stats'));
    }

    public function show(Doacao $doacao)
    {
        $doacao->load('user');
        return view('admin.doacoes.show', compact('doacao'));
    }

    public function aprovar(Request $request, Doacao $doacao)
    {
        $request->validate([
            'observacao_admin' => 'nullable|string|max:1000',
        ]);

        $doacao->update([
            'status' => 'aprovado',
            'observacao_admin' => $request->observacao_admin,
            'data_resposta' => now(),
        ]);

        return redirect()->route('admin.doacoes.show', $doacao)
            ->with('success', 'Doação aprovada com sucesso!');
    }

    public function recusar(Request $request, Doacao $doacao)
    {
        $request->validate([
            'observacao_admin' => 'required|string|max:1000',
        ]);

        $doacao->update([
            'status' => 'recusado',
            'observacao_admin' => $request->observacao_admin,
            'data_resposta' => now(),
        ]);

        return redirect()->route('admin.doacoes.show', $doacao)
            ->with('success', 'Doação recusada.');
    }

    public function marcarRecebido(Doacao $doacao)
    {
        if ($doacao->status !== 'aprovado') {
            return redirect()->back()
                ->with('error', 'Apenas doações aprovadas podem ser marcadas como recebidas.');
        }

        $doacao->update([
            'status' => 'recebido',
        ]);

        return redirect()->route('admin.doacoes.show', $doacao)
            ->with('success', 'Doação marcada como recebida!');
    }

    public function converterEmMaterial(Doacao $doacao)
    {
        if ($doacao->status !== 'recebido') {
            return redirect()->back()
                ->with('error', 'Apenas doações recebidas podem ser convertidas em material.');
        }

        $categoriaMap = [
            'Brinquedo' => 'Brinquedo',
            'Livro' => 'Livro',
            'Material Pedagógico' => 'Material Pedagógico',
            'Roupa' => 'Material Pedagógico',
            'Alimento' => 'Material Pedagógico',
            'Outro' => 'Material Pedagógico',
        ];

        $categoria = $categoriaMap[$doacao->tipo_doacao] ?? 'Material Pedagógico';

        $estadoMap = [
            'novo' => 'novo',
            'bom' => 'bom',
            'usado' => 'gasto',
        ];

        $estado = $estadoMap[$doacao->estado_conservacao] ?? 'bom';

        $material = Material::create([
            'nome' => $doacao->descricao,
            'descricao' => "Doação de: {$doacao->nome_doador}\n\n{$doacao->descricao}",
            'categoria' => $categoria,
            'tipo_material' => 'reutilizavel',
            'origem' => 'doacao',
            'doacao_id' => $doacao->id,
            'quantidade_total_comprada' => $doacao->quantidade,
            'quantidade_disponivel' => $doacao->quantidade,
            'quantidade_em_uso' => 0,
            'quantidade_perdida' => 0,
            'estado_conservacao' => $estado,
            'local_guardado' => 'A definir',
            'idade_recomendada' => 0,
            'fotos' => $doacao->fotos ?? [],
        ]);

        return redirect()->route('admin.materials.edit', $material)
            ->with('success', 'Doação convertida em material! Complete as informações necessárias.');
    }
}
