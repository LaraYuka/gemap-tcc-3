<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agendamento;
use App\Models\Material;
use Illuminate\Http\Request;

class AgendamentoController extends Controller
{
    public function index(Request $request)
    {
        $query = Agendamento::with(['material', 'user'])->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $agendamentos = $query->paginate(15);

        return view('admin.agendamentos.index', compact('agendamentos'));
    }

    public function show(Agendamento $agendamento)
    {
        $agendamento->load(['material', 'user']);
        return view('admin.agendamentos.show', compact('agendamento'));
    }

    public function aprovar(Agendamento $agendamento)
    {
        if ($agendamento->status !== 'pendente') {
            return back()->with('error', 'Este agendamento já foi processado!');
        }

        if ($agendamento->material->quantidade_disponivel < $agendamento->quantidade) {
            return back()->with('error', 'Quantidade insuficiente disponível!');
        }

        $agendamento->update(['status' => 'aprovado']);

        return redirect()->route('admin.agendamentos.index')
            ->with('success', 'Agendamento aprovado! O estoque será reduzido quando marcar como "Retirado".');
    }

    public function recusar(Agendamento $agendamento)
    {
        if ($agendamento->status !== 'pendente') {
            return back()->with('error', 'Este agendamento já foi processado!');
        }

        $agendamento->update(['status' => 'recusado']);

        return redirect()->route('admin.agendamentos.index')
            ->with('success', 'Agendamento recusado.');
    }

    public function retirar(Agendamento $agendamento)
    {
        if ($agendamento->status !== 'aprovado') {
            return back()->with('error', 'Apenas agendamentos aprovados podem ser marcados como retirados!');
        }

        if ($agendamento->material->quantidade_disponivel < $agendamento->quantidade) {
            return back()->with('error', 'Quantidade insuficiente disponível no momento!');
        }

        $agendamento->material->decrement('quantidade_disponivel', $agendamento->quantidade);
        $agendamento->material->increment('quantidade_em_uso', $agendamento->quantidade);

        $agendamento->material->refresh();
        $agendamento->material->atualizarStatusAutomatico();
        $agendamento->material->save();

        $agendamento->update(['status' => 'em_uso']);

        return redirect()->route('admin.agendamentos.index')
            ->with('success', 'Material marcado como retirado! Estoque atualizado.');
    }

    public function formDevolucao(Agendamento $agendamento)
    {
        if ($agendamento->status !== 'em_uso') {
            return back()->with('error', 'Apenas materiais em uso podem ser devolvidos!');
        }

        return view('admin.agendamentos.devolver', compact('agendamento'));
    }

    // Processa a devolução
    public function devolver(Request $request, Agendamento $agendamento)
    {
        if ($agendamento->status !== 'em_uso') {
            return back()->with('error', 'Apenas materiais em uso podem ser devolvidos!');
        }

        $validated = $request->validate([
            'quantidade_devolvida' => 'required|integer|min:0|max:' . $agendamento->quantidade,
            'quantidade_perdida' => 'required|integer|min:0|max:' . $agendamento->quantidade,
            'pecas_perdidas' => 'nullable|integer|min:0', // 🆕 Para conjuntos
            'observacao_devolucao' => 'nullable|string|max:500',
        ]);

        // Validação: soma deve ser igual ao total emprestado
        if (($validated['quantidade_devolvida'] + $validated['quantidade_perdida']) != $agendamento->quantidade) {
            return back()->with('error', 'A soma de devolvidas + perdidas deve ser igual à quantidade emprestada!');
        }

        $material = $agendamento->material;

        // Remove de "em uso"
        $material->decrement('quantidade_em_uso', $agendamento->quantidade);

        // Reutilizável: devolve ao estoque o que voltou
        if ($material->isReutilizavel()) {
            $material->increment('quantidade_disponivel', $validated['quantidade_devolvida']);
        }

        // Registra perdas permanentes de unidades completas
        if ($validated['quantidade_perdida'] > 0) {
            $material->increment('quantidade_perdida', $validated['quantidade_perdida']);
        }

        // 🆕 LÓGICA PARA CONJUNTOS COM MÚLTIPLAS PEÇAS
        if ($material->possui_multiplas_pecas && isset($validated['pecas_perdidas']) && $validated['pecas_perdidas'] > 0) {
            // Registra perda de peças individuais
            $material->registrarPerdaPecas($validated['pecas_perdidas']);

            // Adiciona informação na observação
            $observacao = $validated['observacao_devolucao'] ?? '';
            $observacao .= "\n[Sistema] Registrada perda de {$validated['pecas_perdidas']} peça(s) do conjunto.";
            $validated['observacao_devolucao'] = trim($observacao);
        }

        // 🔥 Força atualização do status
        $material->refresh();
        $material->atualizarStatusAutomatico();
        $material->save();

        $agendamento->update([
            'status' => 'devolvido',
            'data_devolucao' => now(),
            'quantidade_devolvida' => $validated['quantidade_devolvida'],
            'quantidade_perdida' => $validated['quantidade_perdida'],
            'observacao_devolucao' => $validated['observacao_devolucao'],
        ]);

        // Monta mensagem de sucesso
        $mensagem = "Devolução registrada! ";

        if ($validated['quantidade_devolvida'] > 0 && $material->isReutilizavel()) {
            $mensagem .= "{$validated['quantidade_devolvida']} unidade(s) retornou ao estoque. ";
        }

        if ($validated['quantidade_perdida'] > 0) {
            $mensagem .= "{$validated['quantidade_perdida']} unidade(s) registrada como perda. ";
        }

        if ($material->possui_multiplas_pecas && isset($validated['pecas_perdidas']) && $validated['pecas_perdidas'] > 0) {
            $mensagem .= "{$validated['pecas_perdidas']} peça(s) perdida(s) dos conjuntos.";
        }

        return redirect()->route('admin.agendamentos.index')
            ->with('success', $mensagem);
    }
}
