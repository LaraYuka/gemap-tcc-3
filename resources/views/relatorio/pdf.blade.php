<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório - {{ ucfirst($dados['tipo']) }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 9px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 24px;
            margin-bottom: 10px;
        }
        .header-info {
            font-size: 10px;
            margin-top: 8px;
        }
        .section {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        .section-title {
            font-size: 13px;
            font-weight: bold;
            background-color: #f3f4f6;
            padding: 8px;
            margin-bottom: 10px;
            border-left: 4px solid #667eea;
        }
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        .stats-row {
            display: table-row;
        }
        .stat-box {
            display: table-cell;
            width: 20%;
            padding: 8px;
            text-align: center;
            border: 1px solid #e5e7eb;
        }
        .stat-label {
            font-size: 8px;
            color: #6b7280;
            margin-bottom: 4px;
        }
        .stat-value {
            font-size: 16px;
            font-weight: bold;
            color: #1f2937;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th {
            background-color: #f9fafb;
            border: 1px solid #d1d5db;
            padding: 6px 3px;
            text-align: left;
            font-size: 8px;
            font-weight: bold;
            color: #374151;
        }
        td {
            border: 1px solid #e5e7eb;
            padding: 5px 3px;
            font-size: 8px;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .badge {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 8px;
            font-size: 7px;
            font-weight: bold;
        }
        .badge-origem-comprado { background-color: #dbeafe; color: #1e40af; }
        .badge-origem-doacao { background-color: #f3e8ff; color: #6b21a8; }
        .status-disponivel { background-color: #d1fae5; color: #065f46; }
        .status-em-uso { background-color: #fef3c7; color: #92400e; }
        .status-indisponivel { background-color: #fee2e2; color: #991b1b; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 7px;
            color: #6b7280;
            padding: 10px 0;
            border-top: 1px solid #e5e7eb;
        }
        .page-break {
            page-break-after: always;
        }
        .percent-low { color: #059669; }
        .percent-medium { color: #d97706; }
        .percent-high { color: #dc2626; }
        .subsection {
            background-color: #f9fafb;
            padding: 10px;
            margin-bottom: 10px;
            border-left: 3px solid #9ca3af;
        }
        .subsection-title {
            font-size: 11px;
            font-weight: bold;
            color: #4b5563;
            margin-bottom: 8px;
        }
    </style>
</head>
<body>
    <!-- Cabeçalho -->
    <div class="header">
        <h1>Relatório de {{ ucfirst($dados['tipo']) }}</h1>
        <div class="header-info">
            <strong>Gerado em:</strong> {{ $dados['data_geracao'] }} |
            <strong>Gerado por:</strong> {{ $dados['gerado_por'] }}
            @if($dados['filtros']['data_inicio'] || $dados['filtros']['data_fim'])
                <br>
                <strong>Período:</strong>
                {{ $dados['filtros']['data_inicio'] ? \Carbon\Carbon::parse($dados['filtros']['data_inicio'])->format('d/m/Y') : 'Início' }}
                até
                {{ $dados['filtros']['data_fim'] ? \Carbon\Carbon::parse($dados['filtros']['data_fim'])->format('d/m/Y') : 'Hoje' }}
            @endif
            @if($dados['filtros']['origem'])
                <br><strong>Filtro Origem:</strong> {{ $dados['filtros']['origem'] === 'doacao' ? 'Doação' : 'Comprado' }}
            @endif
        </div>
    </div>

    <!-- Estatísticas e Dados de Materiais -->
    @if(isset($dados['estatisticas_materiais']))
        <div class="section">
            <div class="section-title">📊 Resumo Geral do Estoque</div>

            {{-- Primeira Linha: Contadores de Materiais --}}
            <div class="subsection">
                <div class="subsection-title">📦 Quantidade de Materiais por Status</div>
                <div class="stats-grid">
                    <div class="stats-row">
                        <div class="stat-box">
                            <div class="stat-label">Total de Materiais Cadastrados</div>
                            <div class="stat-value">{{ $dados['estatisticas_materiais']['total'] }}</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-label">Materiais Disponíveis</div>
                            <div class="stat-value" style="color: #059669;">{{ $dados['estatisticas_materiais']['disponiveis'] }}</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-label">Materiais Em Uso</div>
                            <div class="stat-value" style="color: #d97706;">{{ $dados['estatisticas_materiais']['em_uso'] }}</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-label">Materiais Indisponíveis</div>
                            <div class="stat-value" style="color: #dc2626;">{{ $dados['estatisticas_materiais']['indisponiveis'] }}</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-label">% de Perda Geral</div>
                            <div class="stat-value" style="color: #dc2626;">{{ $dados['estatisticas_materiais']['percentual_perda'] }}%</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Segunda Linha: Quantidades de Itens --}}
            <div class="subsection">
                <div class="subsection-title">📊 Quantidade Total de Itens/Unidades</div>
                <div class="stats-grid">
                    <div class="stats-row">
                        <div class="stat-box">
                            <div class="stat-label">Itens Comprados (Total Histórico)</div>
                            <div class="stat-value">{{ $dados['estatisticas_materiais']['qtd_total_comprada'] }}</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-label">Itens Disponíveis para Empréstimo</div>
                            <div class="stat-value" style="color: #059669;">{{ $dados['estatisticas_materiais']['qtd_disponivel'] }}</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-label">Itens Emprestados Agora</div>
                            <div class="stat-value" style="color: #d97706;">{{ $dados['estatisticas_materiais']['qtd_em_uso'] }}</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-label">Itens Perdidos (Histórico)</div>
                            <div class="stat-value" style="color: #dc2626;">{{ $dados['estatisticas_materiais']['qtd_perdida'] }}</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-label">% Itens em Uso Agora</div>
                            <div class="stat-value" style="color: #d97706;">{{ $dados['estatisticas_materiais']['percentual_uso'] }}%</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Terceira Linha: Origem dos Materiais --}}
            <div class="subsection">
                <div class="subsection-title">🎁 Origem dos Materiais</div>
                <div class="stats-grid">
                    <div class="stats-row">
                        <div class="stat-box">
                            <div class="stat-label">🛒 Materiais Comprados</div>
                            <div class="stat-value" style="color: #1e40af;">{{ $dados['estatisticas_materiais']['total_comprados'] }}</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-label">🎁 Materiais de Doação</div>
                            <div class="stat-value" style="color: #6b21a8;">{{ $dados['estatisticas_materiais']['total_doacoes'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">📦 Lista Detalhada de Materiais</div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 3%;">ID</th>
                        <th style="width: 20%;">Nome</th>
                        <th style="width: 10%;">Categoria</th>
                        <th style="width: 8%;">Tipo</th>
                        <th class="text-center" style="width: 8%;">Origem</th>
                        <th class="text-center" style="width: 5%;">Histórico Total</th>
                        <th class="text-center" style="width: 5%;">Disp. Agora</th>
                        <th class="text-center" style="width: 5%;">Uso Agora</th>
                        <th class="text-center" style="width: 5%;">Perdido</th>
                        <th class="text-center" style="width: 6%;">% Perdido</th>
                        <th style="width: 9%;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dados['materiais'] as $material)
                        <tr>
                            <td class="text-center">{{ $material->id }}</td>
                            <td>
                                {{ $material->nome }}
                                @if($material->possui_multiplas_pecas)
                                    <br><span style="font-size: 7px; color: #6b7280;">🧩 {{ $material->identificacao_conjunto }}</span>
                                    <br><span style="font-size: 7px; color: #6b7280;">Peças: {{ $material->quantidade_pecas_atual }}/{{ $material->quantidade_pecas_total }}</span>
                                @endif
                                @if($material->origem === 'doacao' && $material->doacao)
                                    <br><span style="font-size: 7px; color: #6b7280;">Doador: {{ $material->doacao->nome_doador }}</span>
                                @endif
                            </td>
                            <td>{{ $material->categoria }}</td>
                            <td>{{ ucfirst($material->tipo_material ?? 'reutilizavel') }}</td>
                            <td class="text-center">
                                @if($material->origem === 'doacao')
                                    <span class="badge badge-origem-doacao">🎁 Doação</span>
                                @else
                                    <span class="badge badge-origem-comprado">🛒 Comprado</span>
                                @endif
                            </td>
                            <td class="text-center font-bold">{{ $material->quantidade_total_comprada ?? 0 }}</td>
                            <td class="text-center font-bold" style="color: #059669;">{{ $material->quantidade_disponivel }}</td>
                            <td class="text-center font-bold" style="color: #d97706;">{{ $material->quantidade_em_uso ?? 0 }}</td>
                            <td class="text-center font-bold" style="color: #dc2626;">{{ $material->quantidade_perdida ?? 0 }}</td>
                            <td class="text-center font-bold">
                                @php
                                    $percentual = $material->percentual_gasto ?? 0;
                                    $cor = $percentual < 30 ? '#059669' : ($percentual < 70 ? '#d97706' : '#dc2626');
                                @endphp
                                <span style="color: {{ $cor }};">{{ number_format($percentual, 1) }}%</span>
                            </td>
                            <td>
                                <span class="badge status-{{ strtolower(str_replace('_', '-', $material->status)) }}">
                                    {{ $material->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center" style="padding: 20px;">
                                Nenhum material encontrado com os filtros aplicados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($dados['agendamentos']))
            <div class="page-break"></div>
        @endif
    @endif

    <!-- Estatísticas e Dados de Agendamentos -->
    @if(isset($dados['estatisticas_agendamentos']))
        <div class="section">
            <div class="section-title">📅 Resumo de Empréstimos e Devoluções</div>

            <div class="subsection">
                <div class="subsection-title">📋 Status dos Agendamentos</div>
                <div class="stats-grid">
                    <div class="stats-row">
                        <div class="stat-box">
                            <div class="stat-label">Total de Agendamentos</div>
                            <div class="stat-value">{{ $dados['estatisticas_agendamentos']['total'] }}</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-label">Aguardando Aprovação</div>
                            <div class="stat-value" style="color: #d97706;">{{ $dados['estatisticas_agendamentos']['pendentes'] }}</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-label">Emprestados Agora</div>
                            <div class="stat-value" style="color: #2563eb;">{{ $dados['estatisticas_agendamentos']['em_uso'] }}</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-label">Já Devolvidos</div>
                            <div class="stat-value" style="color: #059669;">{{ $dados['estatisticas_agendamentos']['devolvidos'] }}</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-label">Itens Perdidos (Total)</div>
                            <div class="stat-value" style="color: #dc2626;">{{ $dados['estatisticas_agendamentos']['total_itens_perdidos'] }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="subsection">
                <div class="subsection-title">📊 Movimentação de Itens</div>
                <div class="stats-grid">
                    <div class="stats-row">
                        <div class="stat-box">
                            <div class="stat-label">Total de Itens Emprestados (Histórico)</div>
                            <div class="stat-value" style="color: #2563eb;">{{ $dados['estatisticas_agendamentos']['total_itens_emprestados'] }}</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-label">Total de Itens Devolvidos</div>
                            <div class="stat-value" style="color: #059669;">{{ $dados['estatisticas_agendamentos']['total_itens_devolvidos'] }}</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-label">Total de Itens Perdidos</div>
                            <div class="stat-value" style="color: #dc2626;">{{ $dados['estatisticas_agendamentos']['total_itens_perdidos'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">📋 Lista de Agendamentos</div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 4%;">ID</th>
                        <th style="width: 25%;">Material</th>
                        <th style="width: 18%;">Professor</th>
                        <th style="width: 11%;">Data Retirada</th>
                        <th style="width: 11%;">Data Devolução</th>
                        <th class="text-center" style="width: 8%;">Qtd</th>
                        <th style="width: 12%;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dados['agendamentos'] as $agendamento)
                        <tr>
                            <td class="text-center">{{ $agendamento->id }}</td>
                            <td>{{ $agendamento->material->nome }}</td>
                            <td>{{ $agendamento->user->name }}</td>
                            <td>
                                {{ \Carbon\Carbon::parse($agendamento->data_retirada)->format('d/m/Y') }}
                                @if($agendamento->horario_retirada)
                                    <br><span style="font-size: 7px; color: #6b7280;">{{ $agendamento->horario_retirada }}</span>
                                @endif
                            </td>
                            <td>
                                @if($agendamento->data_devolucao)
                                    {{ \Carbon\Carbon::parse($agendamento->data_devolucao)->format('d/m/Y') }}
                                @elseif($agendamento->data_devolucao_prevista)
                                    {{ \Carbon\Carbon::parse($agendamento->data_devolucao_prevista)->format('d/m/Y') }}
                                    <br><span style="font-size: 7px; color: #d97706;">(prevista)</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="font-bold">{{ $agendamento->quantidade }}</div>
                                @if($agendamento->quantidade_devolvida > 0)
                                    <div style="font-size: 7px; color: #059669;">Devol: {{ $agendamento->quantidade_devolvida }}</div>
                                @endif
                                @if($agendamento->quantidade_perdida > 0)
                                    <div style="font-size: 7px; color: #dc2626;">Perd: {{ $agendamento->quantidade_perdida }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="badge status-{{ strtolower($agendamento->status) }}">
                                    {{ ucfirst(str_replace('_', ' ', $agendamento->status)) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center" style="padding: 20px;">
                                Nenhum agendamento encontrado com os filtros aplicados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif

    <div class="footer">
        Sistema de Gerenciamento de Materiais - Gerado em {{ $dados['data_geracao'] }}
    </div>
</body>
</html>
