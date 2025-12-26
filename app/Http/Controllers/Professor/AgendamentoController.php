<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use App\Models\Agendamento;
use App\Models\Material;
use Illuminate\Http\Request;

class AgendamentoController extends Controller
{
    public function index()
    {
        $agendamentos = Agendamento::with('material')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('professor.agendamentos.index', compact('agendamentos'));
    }

    public function verificarDisponibilidade(Request $request)
    {
        $material = Material::findOrFail($request->material_id);

        $quantidadeReservada = Agendamento::verificarDisponibilidade(
            $request->material_id,
            $request->data_retirada,
            $request->horario_retirada,
            $request->data_devolucao_prevista,
            $request->horario_devolucao,
            $request->quantidade
        );

        $quantidadeDisponivel = $material->quantidade_disponivel - $quantidadeReservada;
        $disponivel = $quantidadeDisponivel >= $request->quantidade;

        return response()->json([
            'disponivel' => $disponivel,
            'quantidade_disponivel' => max(0, $quantidadeDisponivel),
            'mensagem' => $disponivel ? '' : "Apenas {$quantidadeDisponivel} unidade(s) disponível(is) no período."
        ]);
    }

    public function create(Material $material)
    {
        return view('professor.agendamentos.create', compact('material'));
    }

    public function store(Request $request)
    {
        $material = Material::findOrFail($request->material_id);

        if ($material->isConsumivel()) {
            $request->validate([
                'material_id' => 'required|exists:materials,id',
                'data_retirada' => 'required|date|after_or_equal:today',
                'horario_retirada' => 'required|in:7h30-9h30,9h30-11h30,13h10-15h10,15h10-17h10',
                'quantidade' => 'required|integer|min:1',
                'observacoes' => 'nullable|string',
            ]);

            if ($material->quantidade_disponivel < $request->quantidade) {
                return back()->with('error', "Quantidade insuficiente! Disponível: {$material->quantidade_disponivel}");
            }

            Agendamento::create([
                'material_id' => $request->material_id,
                'user_id' => auth()->id(),
                'data_retirada' => $request->data_retirada,
                'horario_retirada' => $request->horario_retirada,
                'data_devolucao_prevista' => null,
                'horario_devolucao' => null,
                'quantidade' => $request->quantidade,
                'observacoes' => $request->observacoes,
                'status' => 'pendente',
            ]);

            return redirect()->route('professor.agendamentos.index')
                ->with('success', 'Solicitação enviada! Aguarde aprovação do administrador.');
        } else {
            $request->validate([
                'material_id' => 'required|exists:materials,id',
                'data_retirada' => 'required|date|after_or_equal:today',
                'horario_retirada' => 'required|in:7h30-9h30,9h30-11h30,13h10-15h10,15h10-17h10',
                'data_devolucao_prevista' => 'required|date|after_or_equal:data_retirada',
                'horario_devolucao' => 'required|in:7h30-9h30,9h30-11h30,13h10-15h10,15h10-17h10',
                'quantidade' => 'required|integer|min:1',
                'observacoes' => 'nullable|string',
            ]);

            $quantidadeReservada = Agendamento::verificarDisponibilidade(
                $request->material_id,
                $request->data_retirada,
                $request->horario_retirada,
                $request->data_devolucao_prevista,
                $request->horario_devolucao,
                $request->quantidade
            );

            $quantidadeDisponivel = $material->quantidade_disponivel - $quantidadeReservada;

            if ($quantidadeDisponivel < $request->quantidade) {
                return back()->with('error', "Indisponível! Apenas {$quantidadeDisponivel} unidade(s) disponível(is) no período.");
            }

            Agendamento::create([
                'material_id' => $request->material_id,
                'user_id' => auth()->id(),
                'data_retirada' => $request->data_retirada,
                'horario_retirada' => $request->horario_retirada,
                'data_devolucao_prevista' => $request->data_devolucao_prevista,
                'horario_devolucao' => $request->horario_devolucao,
                'quantidade' => $request->quantidade,
                'observacoes' => $request->observacoes,
                'status' => 'pendente',
            ]);

            return redirect()->route('professor.agendamentos.index')
                ->with('success', 'Agendamento realizado! Aguarde aprovação.');
        }
    }

    public function show(Agendamento $agendamento)
    {
        if ($agendamento->user_id !== auth()->id()) {
            abort(403);
        }

        $agendamento->load('material');
        return view('professor.agendamentos.show', compact('agendamento'));
    }

    public function destroy(Agendamento $agendamento)
    {
        if ($agendamento->user_id !== auth()->id()) {
            abort(403);
        }

        if ($agendamento->status !== 'pendente') {
            return back()->with('error', 'Não é possível cancelar este agendamento!');
        }

        $agendamento->delete();

        return redirect()->route('professor.agendamentos.index')
            ->with('success', 'Agendamento cancelado com sucesso!');
    }
}
