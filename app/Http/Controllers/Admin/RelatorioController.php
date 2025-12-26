<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\Agendamento;
use Illuminate\Http\Request;

class RelatorioController extends Controller
{
    public function index()
    {
        return view('admin.relatorios.index');
    }

    public function gerar(Request $request)
    {
        $request->validate([
            'tipo' => 'required|in:materiais_falta,materiais_acabando,materiais_utilizados,todos_materiais',
        ]);

        $tipo = $request->tipo;
        $dataInicio = $request->data_inicio;
        $dataFim = $request->data_fim;

        $dados = [];

        switch ($tipo) {
            case 'materiais_falta':
                $dados = Material::where('quantidade_disponivel', 0)->get();
                break;

            case 'materiais_acabando':
                $dados = Material::whereRaw('quantidade_disponivel <= (quantidade_total * 0.2)')
                    ->where('quantidade_disponivel', '>', 0)
                    ->get();
                break;

            case 'materiais_utilizados':
                $query = Agendamento::with('material')
                    ->selectRaw('material_id, COUNT(*) as total_usos')
                    ->groupBy('material_id')
                    ->orderByDesc('total_usos');

                if ($dataInicio) {
                    $query->where('created_at', '>=', $dataInicio);
                }
                if ($dataFim) {
                    $query->where('created_at', '<=', $dataFim);
                }

                $dados = $query->get();
                break;

            case 'todos_materiais':
                $dados = Material::all();
                break;
        }

        return view('admin.relatorios.resultado', compact('dados', 'tipo', 'dataInicio', 'dataFim'));
    }

    public function exportarCsv(Request $request)
    {
        $tipo = $request->tipo;
        $filename = "relatorio_" . $tipo . "_" . date('Y-m-d') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($tipo, $request) {
            $file = fopen('php://output', 'w');

            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            if ($tipo === 'materiais_utilizados') {
                fputcsv($file, ['Material', 'Total de Usos']);

                $agendamentos = Agendamento::with('material')
                    ->selectRaw('material_id, COUNT(*) as total_usos')
                    ->groupBy('material_id')
                    ->orderByDesc('total_usos')
                    ->get();

                foreach ($agendamentos as $agendamento) {
                    fputcsv($file, [
                        $agendamento->material->nome,
                        $agendamento->total_usos,
                    ]);
                }
            } else {
                fputcsv($file, ['ID', 'Nome', 'Categoria', 'Qtd Total', 'Qtd Disponível', 'Estado', 'Local']);

                $materiais = Material::all();
                foreach ($materiais as $material) {
                    fputcsv($file, [
                        $material->id,
                        $material->nome,
                        $material->categoria,
                        $material->quantidade_total,
                        $material->quantidade_disponivel,
                        $material->estado_conservacao,
                        $material->local_guardado,
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
