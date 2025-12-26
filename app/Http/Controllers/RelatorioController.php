<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\Agendamento;
use App\Models\Relatorio;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RelatorioController extends Controller
{
    public function index()
    {
        $historico = null;

        if (auth()->check() && auth()->user()->role === 'admin') {
            $historico = Relatorio::with('user')
                ->orderBy('data_geracao', 'desc')
                ->limit(5)
                ->get();
        }

        return view('relatorio.index', compact('historico'));
    }

    public function gerar(Request $request)
    {
        $request->validate([
            'tipo' => 'required|in:materiais,agendamentos,completo,analise-avancada',
            'data_inicio' => 'nullable|date',
            'data_fim' => 'nullable|date|after_or_equal:data_inicio',
            'categoria' => 'nullable|in:Livro,Brinquedo,Jogo,Material Pedagógico',
            'status' => 'nullable|in:DISPONIVEL,EM_USO,INDISPONIVEL',
            'origem' => 'nullable|in:comprado,doacao',
        ]);

        $dados = $this->montarDados($request);

        return view('relatorio.resultado', compact('dados'));
    }

    public function exportarPdf(Request $request)
    {
        $dados = $this->montarDados($request);

        $pdf = Pdf::loadView('relatorio.pdf', compact('dados'))
            ->setPaper('a4', 'landscape');

        $nomeArquivo = 'relatorio_' . $dados['tipo'] . '_' . date('Y-m-d_His') . '.pdf';
        $caminhoArquivo = 'relatorios/' . auth()->id() . '/' . $nomeArquivo;

        Storage::put($caminhoArquivo, $pdf->output());
        $this->registrarHistorico($request, 'pdf', $caminhoArquivo);

        return $pdf->download($nomeArquivo);
    }

    public function exportarCsv(Request $request)
    {
        $dados = $this->montarDados($request);

        $nomeArquivo = 'relatorio_' . $dados['tipo'] . '_' . date('Y-m-d_His') . '.csv';
        $caminhoArquivo = 'relatorios/' . auth()->id() . '/' . $nomeArquivo;

        $csvContent = $this->gerarCsvContent($dados);
        Storage::put($caminhoArquivo, $csvContent);
        $this->registrarHistorico($request, 'csv', $caminhoArquivo);

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$nomeArquivo\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        return response()->stream(function () use ($csvContent) {
            echo $csvContent;
        }, 200, $headers);
    }

    public function historico()
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            abort(403, 'Acesso negado. Apenas administradores podem acessar o histórico de relatórios.');
        }

        $relatorios = Relatorio::with('user')
            ->orderBy('data_geracao', 'desc')
            ->paginate(15);

        return view('relatorio.historico', compact('relatorios'));
    }

    public function download($id)
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            abort(403, 'Acesso negado.');
        }

        $relatorio = Relatorio::findOrFail($id);

        if (!$relatorio->arquivoExiste()) {
            return redirect()->back()->with('error', 'Arquivo não encontrado.');
        }

        return Storage::download($relatorio->caminho_arquivo);
    }

    public function excluir($id)
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            abort(403, 'Acesso negado.');
        }

        $relatorio = Relatorio::findOrFail($id);

        if ($relatorio->caminho_arquivo && Storage::exists($relatorio->caminho_arquivo)) {
            Storage::delete($relatorio->caminho_arquivo);
        }

        $relatorio->delete();

        return redirect()->back()->with('success', 'Relatório excluído com sucesso!');
    }

    private function gerarCsvContent($dados)
    {
        $output = fopen('php://temp', 'r+');
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        if ($dados['tipo'] === 'materiais' || $dados['tipo'] === 'completo' || $dados['tipo'] === 'analise-avancada') {
            fputcsv($output, [
                'ID',
                'Nome',
                'Categoria',
                'Tipo Material',
                'Origem',
                'Possui Múltiplas Peças',
                'Identificação Conjunto',
                'Peças Total',
                'Peças Atual',
                '% Peças',
                'Qtd. Total Comprada',
                'Qtd. Disponível',
                'Qtd. Em Uso',
                'Qtd. Perdida',
                '% Gasto',
                'Status',
                'Estado Conservação',
                'Local Guardado',
                'Idade Recomendada'
            ], ';');

            foreach ($dados['materiais'] as $material) {
                fputcsv($output, [
                    $material->id,
                    $material->nome,
                    $material->categoria,
                    $material->tipo_material,
                    $material->origem === 'doacao' ? 'Doação' : 'Comprado',
                    $material->possui_multiplas_pecas ? 'Sim' : 'Não',
                    $material->identificacao_conjunto ?? '-',
                    $material->quantidade_pecas_total ?? '-',
                    $material->quantidade_pecas_atual ?? '-',
                    $material->possui_multiplas_pecas ? number_format($material->percentual_pecas_atual, 1) . '%' : '-',
                    $material->quantidade_total_comprada ?? 0,
                    $material->quantidade_disponivel,
                    $material->quantidade_em_uso ?? 0,
                    $material->quantidade_perdida ?? 0,
                    number_format($material->percentual_gasto, 1) . '%',
                    $material->status,
                    $material->estado_conservacao,
                    $material->local_guardado,
                    $material->idade_recomendada
                ], ';');
            }
        }

        if ($dados['tipo'] === 'agendamentos' || $dados['tipo'] === 'completo' || $dados['tipo'] === 'analise-avancada') {
            if ($dados['tipo'] !== 'agendamentos') {
                fputcsv($output, [], ';');
                fputcsv($output, [], ';');
            }

            fputcsv($output, [
                'ID',
                'Material',
                'Professor',
                'Data Retirada',
                'Horário Retirada',
                'Data Devolução Prevista',
                'Horário Devolução',
                'Data Devolução Real',
                'Quantidade',
                'Qtd. Devolvida',
                'Qtd. Perdida',
                'Status',
                'Observações'
            ], ';');

            foreach ($dados['agendamentos'] as $agendamento) {
                fputcsv($output, [
                    $agendamento->id,
                    $agendamento->material->nome,
                    $agendamento->user->name,
                    $agendamento->data_retirada,
                    $agendamento->horario_retirada ?? '-',
                    $agendamento->data_devolucao_prevista ?? '-',
                    $agendamento->horario_devolucao ?? '-',
                    $agendamento->data_devolucao ?? '-',
                    $agendamento->quantidade,
                    $agendamento->quantidade_devolvida ?? 0,
                    $agendamento->quantidade_perdida ?? 0,
                    $agendamento->status,
                    $agendamento->observacoes ?? '-'
                ], ';');
            }
        }

        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);

        return $csvContent;
    }

    private function registrarHistorico(Request $request, $formato, $caminhoArquivo)
    {
        Relatorio::create([
            'user_id' => auth()->id(),
            'tipo' => $request->input('tipo'),
            'data_inicio' => $request->input('data_inicio'),
            'data_fim' => $request->input('data_fim'),
            'categoria' => $request->input('categoria'),
            'status' => $request->input('status'),
            'origem' => $request->input('origem'),
            'formato' => $formato,
            'caminho_arquivo' => $caminhoArquivo,
            'data_geracao' => Carbon::now(),
        ]);
    }

    private function montarDados(Request $request)
    {
        $tipo = $request->input('tipo', 'materiais');
        $dataInicio = $request->input('data_inicio');
        $dataFim = $request->input('data_fim');
        $categoria = $request->input('categoria');
        $status = $request->input('status');
        $origem = $request->input('origem');

        $dados = [
            'tipo' => $tipo,
            'filtros' => [
                'data_inicio' => $dataInicio,
                'data_fim' => $dataFim,
                'categoria' => $categoria,
                'status' => $status,
                'origem' => $origem,
            ],
            'data_geracao' => Carbon::now()->format('d/m/Y H:i:s'),
            'gerado_por' => auth()->user()->name,
        ];

        if ($tipo === 'materiais' || $tipo === 'completo' || $tipo === 'analise-avancada') {
            $query = Material::query()->with('doacao');

            if ($categoria) $query->where('categoria', $categoria);
            if ($status) $query->where('status', $status);
            if ($origem) $query->where('origem', $origem);

            $materiais = $query->orderBy('nome')->get();

            $materiais->each(function ($material) {
                $totalComprado = $material->quantidade_total_comprada ?? 0;

                if ($material->possui_multiplas_pecas) {
                    if ($material->quantidade_pecas_total > 0) {
                        $pecasPerdidas = $material->quantidade_pecas_total - $material->quantidade_pecas_atual;
                        $material->percentual_gasto = ($pecasPerdidas / $material->quantidade_pecas_total) * 100;
                        $material->percentual_pecas_atual = ($material->quantidade_pecas_atual / $material->quantidade_pecas_total) * 100;
                    } else {
                        $material->percentual_gasto = 0;
                        $material->percentual_pecas_atual = 0;
                    }
                } else {
                    if ($totalComprado > 0) {
                        $totalGasto = ($material->quantidade_em_uso ?? 0) + ($material->quantidade_perdida ?? 0);
                        $material->percentual_gasto = ($totalGasto / $totalComprado) * 100;
                    } else {
                        $material->percentual_gasto = 0;
                    }
                }
            });

            $dados['materiais'] = $materiais;

            $dados['estatisticas_materiais'] = [
                'total' => $materiais->count(),
                'disponiveis' => $materiais->where('status', 'DISPONIVEL')->count(),
                'em_uso' => $materiais->where('status', 'EM_USO')->count(),
                'indisponiveis' => $materiais->where('status', 'INDISPONIVEL')->count(),
                'qtd_total_comprada' => $materiais->sum('quantidade_total_comprada'),
                'qtd_disponivel' => $materiais->sum('quantidade_disponivel'),
                'qtd_em_uso' => $materiais->sum('quantidade_em_uso'),
                'qtd_perdida' => $materiais->sum('quantidade_perdida'),
                'total_comprados' => $materiais->where('origem', 'comprado')->count(),
                'total_doacoes' => $materiais->where('origem', 'doacao')->count(),
                'percentual_uso' => $materiais->sum('quantidade_total_comprada') > 0
                    ? round(($materiais->sum('quantidade_em_uso') / $materiais->sum('quantidade_total_comprada')) * 100, 1)
                    : 0,
                'percentual_perda' => $materiais->sum('quantidade_total_comprada') > 0
                    ? round(($materiais->sum('quantidade_perdida') / $materiais->sum('quantidade_total_comprada')) * 100, 1)
                    : 0,
            ];

            $dados['estatisticas_por_categoria'] = $materiais->groupBy('categoria')->map(function ($items, $categoria) {
                return [
                    'categoria' => $categoria,
                    'total' => $items->count(),
                    'qtd_total' => $items->sum('quantidade_total_comprada'),
                    'qtd_disponivel' => $items->sum('quantidade_disponivel'),
                    'qtd_em_uso' => $items->sum('quantidade_em_uso'),
                    'qtd_perdida' => $items->sum('quantidade_perdida'),
                ];
            });

            if ($tipo === 'analise-avancada') {
                $dados['alertas'] = [
                    'alta_perda' => $materiais->filter(fn($m) => $m->percentual_gasto > 70)->sortByDesc('percentual_gasto')->values(),
                    'estoque_baixo' => $materiais->filter(function ($m) {
                        $total = $m->quantidade_total_comprada ?? 0;
                        return $total > 0 && ($m->quantidade_disponivel / $total) < 0.3;
                    })->values(),
                    'nunca_usado' => $materiais->filter(function ($m) {
                        return ($m->quantidade_em_uso ?? 0) == 0 &&
                            ($m->quantidade_perdida ?? 0) == 0 &&
                            $m->created_at < Carbon::now()->subMonths(3);
                    })->values(),
                    'conjuntos_incompletos' => $materiais->filter(function ($m) {
                        return $m->possui_multiplas_pecas &&
                            $m->percentual_pecas_atual < $m->percentual_minimo_utilizavel;
                    })->values(),
                ];

                $dados['graficos'] = [
                    'distribuicao_categoria' => $materiais->groupBy('categoria')->map->count(),
                    'distribuicao_origem' => [
                        'Comprado' => $materiais->where('origem', 'comprado')->count(),
                        'Doação' => $materiais->where('origem', 'doacao')->count(),
                    ],
                    'distribuicao_status' => [
                        'Disponível' => $materiais->where('status', 'DISPONIVEL')->count(),
                        'Em Uso' => $materiais->where('status', 'EM_USO')->count(),
                        'Indisponível' => $materiais->where('status', 'INDISPONIVEL')->count(),
                    ],
                    'quantidade_por_categoria' => $materiais->groupBy('categoria')->map(function ($items) {
                        return [
                            'total' => $items->sum('quantidade_total_comprada'),
                            'disponivel' => $items->sum('quantidade_disponivel'),
                            'uso' => $items->sum('quantidade_em_uso'),
                            'perdida' => $items->sum('quantidade_perdida'),
                        ];
                    }),
                    'total_conjuntos' => $materiais->where('possui_multiplas_pecas', true)->count(),
                    'total_unitarios' => $materiais->where('possui_multiplas_pecas', false)->count(),
                ];

                // CORREÇÃO: Passa dataInicio e dataFim para o método
                $dados['historico_mensal'] = $this->calcularHistoricoMensal($dataInicio, $dataFim);

                // CORREÇÃO: Adiciona filtro de data no ranking
                $rankingQuery = Agendamento::select('material_id', DB::raw('COUNT(*) as total_agendamentos'))
                    ->groupBy('material_id');

                if ($dataInicio) $rankingQuery->where('data_retirada', '>=', $dataInicio);
                if ($dataFim) $rankingQuery->where('data_retirada', '<=', $dataFim);

                $dados['ranking'] = [
                    'mais_usados' => $rankingQuery
                        ->orderByDesc('total_agendamentos')
                        ->limit(10)
                        ->with('material')
                        ->get(),
                    'maior_perda' => $materiais->sortByDesc('quantidade_perdida')->take(10)->values(),
                    'conjuntos_maior_perda_pecas' => $materiais
                        ->where('possui_multiplas_pecas', true)
                        ->sortByDesc(fn($m) => $m->quantidade_pecas_total - $m->quantidade_pecas_atual)
                        ->take(10)
                        ->values(),
                ];

                $dados['analise_custos'] = [
                    'total_itens' => $materiais->sum('quantidade_total_comprada'),
                    'itens_perdidos' => $materiais->sum('quantidade_perdida'),
                    'percentual_perda_geral' => $dados['estatisticas_materiais']['percentual_perda'],
                ];
            }
        }

        if ($tipo === 'agendamentos' || $tipo === 'completo' || $tipo === 'analise-avancada') {
            $query = Agendamento::with(['material', 'user']);

            if ($dataInicio) $query->where('data_retirada', '>=', $dataInicio);
            if ($dataFim) $query->where('data_retirada', '<=', $dataFim);

            $dados['agendamentos'] = $query->orderBy('data_retirada', 'desc')->get();

            $dados['estatisticas_agendamentos'] = [
                'total' => $dados['agendamentos']->count(),
                'pendentes' => $dados['agendamentos']->where('status', 'pendente')->count(),
                'aprovados' => $dados['agendamentos']->where('status', 'aprovado')->count(),
                'em_uso' => $dados['agendamentos']->where('status', 'em_uso')->count(),
                'devolvidos' => $dados['agendamentos']->where('status', 'devolvido')->count(),
                'recusados' => $dados['agendamentos']->where('status', 'recusado')->count(),
                'total_itens_emprestados' => $dados['agendamentos']->sum('quantidade'),
                'total_itens_devolvidos' => $dados['agendamentos']->sum('quantidade_devolvida'),
                'total_itens_perdidos' => $dados['agendamentos']->sum('quantidade_perdida'),
            ];

            if ($tipo === 'analise-avancada') {
                // CORREÇÃO: Adiciona filtro de data nos professores mais ativos
                $professoresQuery = Agendamento::select('user_id', DB::raw('COUNT(*) as total'))
                    ->groupBy('user_id');

                if ($dataInicio) $professoresQuery->where('data_retirada', '>=', $dataInicio);
                if ($dataFim) $professoresQuery->where('data_retirada', '<=', $dataFim);

                $dados['analise_agendamentos'] = [
                    'atrasos' => $dados['agendamentos']->filter(function ($a) {
                        return $a->status === 'em_uso' &&
                            $a->data_devolucao_prevista &&
                            Carbon::parse($a->data_devolucao_prevista)->isPast();
                    })->count(),
                    'professores_mais_ativos' => $professoresQuery
                        ->orderByDesc('total')
                        ->limit(5)
                        ->with('user')
                        ->get(),
                ];
            }
        }

        return $dados;
    }

    // MÉTODO ATUALIZADO: Agora recebe os parâmetros de data
    private function calcularHistoricoMensal($dataInicio = null, $dataFim = null)
    {
        $historico = [];

        // Define o período baseado nos filtros
        $dataFinal = $dataFim ? Carbon::parse($dataFim) : Carbon::now();
        $dataInicial = $dataInicio ? Carbon::parse($dataInicio) : $dataFinal->copy()->subMonths(11);

        // Calcula quantos meses cobrir
        $mesesDiferenca = $dataInicial->diffInMonths($dataFinal);
        $mesesParaIterar = min($mesesDiferenca + 1, 12);

        for ($i = $mesesParaIterar - 1; $i >= 0; $i--) {
            $mes = $dataFinal->copy()->subMonths($i);
            $inicioMes = $mes->copy()->startOfMonth();
            $fimMes = $mes->copy()->endOfMonth();

            // Ajusta para respeitar os limites informados pelo usuário
            if ($dataInicio && $inicioMes->lt(Carbon::parse($dataInicio))) {
                $inicioMes = Carbon::parse($dataInicio);
            }
            if ($dataFim && $fimMes->gt(Carbon::parse($dataFim))) {
                $fimMes = Carbon::parse($dataFim);
            }

            $agendamentos = Agendamento::whereBetween('data_retirada', [$inicioMes, $fimMes])->get();

            $historico[] = [
                'mes' => $mes->format('M/Y'),
                'mes_completo' => $mes->locale('pt_BR')->isoFormat('MMMM YYYY'),
                'total_agendamentos' => $agendamentos->count(),
                'itens_emprestados' => $agendamentos->sum('quantidade'),
                'itens_devolvidos' => $agendamentos->sum('quantidade_devolvida'),
                'itens_perdidos' => $agendamentos->sum('quantidade_perdida'),
            ];
        }

        return $historico;
    }
}
