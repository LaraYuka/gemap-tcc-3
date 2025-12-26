<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Resultado do Relatório') }}
            </h2>
            <a href="{{ route('relatorio.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                ← Voltar
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Cabeçalho do Relatório -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-gradient-to-r from-blue-50 to-indigo-50">
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">
                        Relatório de {{ ucfirst($dados['tipo']) }}
                    </h3>
                    <div class="text-sm text-gray-600 space-y-1">
                        <p><strong>Gerado em:</strong> {{ $dados['data_geracao'] }}</p>
                        <p><strong>Gerado por:</strong> {{ $dados['gerado_por'] }}</p>
                        @if($dados['filtros']['data_inicio'] || $dados['filtros']['data_fim'])
                            <p><strong>Período:</strong>
                                {{ $dados['filtros']['data_inicio'] ? \Carbon\Carbon::parse($dados['filtros']['data_inicio'])->format('d/m/Y') : 'Início' }}
                                até
                                {{ $dados['filtros']['data_fim'] ? \Carbon\Carbon::parse($dados['filtros']['data_fim'])->format('d/m/Y') : 'Hoje' }}
                            </p>
                        @endif
                        @if($dados['filtros']['origem'])
                            <p><strong>Origem:</strong> {{ $dados['filtros']['origem'] === 'doacao' ? '🎁 Doação' : '🛒 Comprado' }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Estatísticas de Materiais -->
            @if(isset($dados['estatisticas_materiais']))

                <!-- 📦 Seção 1: Quantidade de Materiais por Status -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold mb-4 text-gray-800">📦 Quantidade de Materiais por Status</h4>
                        <p class="text-sm text-gray-600 mb-4">Contabiliza quantos materiais diferentes existem (ex: 1 Lego, 1 Quebra-cabeça, 1 Boneca = 3 materiais)</p>
                        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-600">Total de Materiais Cadastrados</p>
                                <p class="text-2xl font-bold text-blue-700">{{ $dados['estatisticas_materiais']['total'] }}</p>
                            </div>
                            <div class="bg-green-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-600">Materiais Disponíveis</p>
                                <p class="text-2xl font-bold text-green-700">{{ $dados['estatisticas_materiais']['disponiveis'] }}</p>
                            </div>
                            <div class="bg-yellow-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-600">Materiais Em Uso</p>
                                <p class="text-2xl font-bold text-yellow-700">{{ $dados['estatisticas_materiais']['em_uso'] }}</p>
                            </div>
                            <div class="bg-red-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-600">Materiais Indisponíveis</p>
                                <p class="text-2xl font-bold text-red-700">{{ $dados['estatisticas_materiais']['indisponiveis'] }}</p>
                            </div>
                            <div class="bg-pink-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-600">% de Perda Geral</p>
                                <p class="text-2xl font-bold text-pink-700">{{ $dados['estatisticas_materiais']['percentual_perda'] }}%</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 📊 Seção 2: Quantidade Total de Itens/Unidades -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold mb-4 text-gray-800">📊 Quantidade Total de Itens/Unidades</h4>
                        <p class="text-sm text-gray-600 mb-4">Contabiliza a soma de todas as unidades (ex: 10 bonecas + 500 peças de Lego + 3 livros = 513 itens)</p>
                        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                            <div class="bg-purple-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-600">Itens Comprados (Total Histórico)</p>
                                <p class="text-2xl font-bold text-purple-700">{{ $dados['estatisticas_materiais']['qtd_total_comprada'] }}</p>
                                <p class="text-xs text-gray-500 mt-1">Tudo que foi adquirido</p>
                            </div>
                            <div class="bg-indigo-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-600">Itens Disponíveis para Empréstimo</p>
                                <p class="text-2xl font-bold text-indigo-700">{{ $dados['estatisticas_materiais']['qtd_disponivel'] }}</p>
                                <p class="text-xs text-gray-500 mt-1">Pronto para emprestar agora</p>
                            </div>
                            <div class="bg-orange-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-600">Itens Emprestados Agora</p>
                                <p class="text-2xl font-bold text-orange-700">{{ $dados['estatisticas_materiais']['qtd_em_uso'] }}</p>
                                <p class="text-xs text-gray-500 mt-1">Com professores neste momento</p>
                            </div>
                            <div class="bg-red-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-600">Itens Perdidos (Histórico)</p>
                                <p class="text-2xl font-bold text-red-700">{{ $dados['estatisticas_materiais']['qtd_perdida'] }}</p>
                                <p class="text-xs text-gray-500 mt-1">Perdidos ao longo do tempo</p>
                            </div>
                            <div class="bg-amber-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-600">% Itens em Uso Agora</p>
                                <p class="text-2xl font-bold text-amber-700">{{ $dados['estatisticas_materiais']['percentual_uso'] }}%</p>
                                <p class="text-xs text-gray-500 mt-1">Do total comprado</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 🎁 Seção 3: Origem dos Materiais -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold mb-4 text-gray-800">🎁 Origem dos Materiais</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-teal-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-600">🛒 Materiais Comprados</p>
                                <p class="text-2xl font-bold text-teal-700">{{ $dados['estatisticas_materiais']['total_comprados'] }}</p>
                                <p class="text-xs text-gray-500 mt-1">Adquiridos pela creche</p>
                            </div>
                            <div class="bg-cyan-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-600">🎁 Materiais de Doação</p>
                                <p class="text-2xl font-bold text-cyan-700">{{ $dados['estatisticas_materiais']['total_doacoes'] }}</p>
                                <p class="text-xs text-gray-500 mt-1">Recebidos de doadores</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Estatísticas por Categoria -->
                @if(isset($dados['estatisticas_por_categoria']) && $dados['estatisticas_por_categoria']->isNotEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold mb-4 text-gray-800">📈 Estatísticas por Categoria</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Categoria</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Materiais Diferentes</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Total de Itens</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Itens Disponíveis</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Itens Em Uso</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Itens Perdidos</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($dados['estatisticas_por_categoria'] as $stat)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 font-medium text-gray-900">{{ $stat['categoria'] }}</td>
                                            <td class="px-4 py-3 text-center text-gray-600">{{ $stat['total'] }}</td>
                                            <td class="px-4 py-3 text-center font-semibold text-gray-900">{{ $stat['qtd_total'] }}</td>
                                            <td class="px-4 py-3 text-center text-green-600 font-semibold">{{ $stat['qtd_disponivel'] }}</td>
                                            <td class="px-4 py-3 text-center text-yellow-600 font-semibold">{{ $stat['qtd_em_uso'] }}</td>
                                            <td class="px-4 py-3 text-center text-red-600 font-semibold">{{ $stat['qtd_perdida'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Tabela de Materiais -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold mb-4 text-gray-800">📦 Lista Detalhada de Materiais</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Categoria</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Origem</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Histórico Total</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Disp. Agora</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Uso Agora</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Perdido</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">% Perdido</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($dados['materiais'] as $material)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $material->id }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-900">
                                                {{ $material->nome }}
                                                @if($material->possui_multiplas_pecas)
                                                    <span class="block text-xs text-indigo-600 mt-1">
                                                        🧩 {{ $material->identificacao_conjunto }}
                                                    </span>
                                                    <span class="block text-xs text-gray-500">
                                                        Peças: {{ $material->quantidade_pecas_atual }}/{{ $material->quantidade_pecas_total }} ({{ $material->percentual_pecas_atual }}%)
                                                    </span>
                                                @endif
                                                @if($material->origem === 'doacao' && $material->doacao)
                                                    <span class="text-xs text-gray-500 block">
                                                        Doador: {{ $material->doacao->nome_doador }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">{{ $material->categoria }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">{{ ucfirst($material->tipo_material ?? 'reutilizavel') }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-center">
                                                @if($material->origem === 'doacao')
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                        🎁 Doação
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        🛒 Comprado
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-center text-gray-900 font-semibold">{{ $material->quantidade_total_comprada ?? 0 }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-center text-green-600 font-semibold">{{ $material->quantidade_disponivel }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-center text-yellow-600 font-semibold">{{ $material->quantidade_em_uso ?? 0 }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-center text-red-600 font-semibold">{{ $material->quantidade_perdida ?? 0 }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-center">
                                                @php
                                                    $percentual = $material->percentual_gasto ?? 0;
                                                    $corClasse = $percentual < 30 ? 'text-green-600' : ($percentual < 70 ? 'text-yellow-600' : 'text-red-600');
                                                @endphp
                                                <span class="font-semibold {{ $corClasse }}">{{ number_format($percentual, 1) }}%</span>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                                    @if($material->status === 'DISPONIVEL') bg-green-100 text-green-800
                                                    @elseif($material->status === 'EM_USO') bg-yellow-100 text-yellow-800
                                                    @else bg-red-100 text-red-800
                                                    @endif">
                                                    {{ $material->status }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="11" class="px-4 py-8 text-center text-gray-500">
                                                Nenhum material encontrado com os filtros aplicados.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Estatísticas de Agendamentos -->
            @if(isset($dados['estatisticas_agendamentos']))
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold mb-4 text-gray-800">📅 Resumo de Empréstimos e Devoluções</h4>

                        <!-- Status dos Agendamentos -->
                        <h5 class="text-md font-semibold mb-3 text-gray-700">📋 Status dos Agendamentos</h5>
                        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-600">Total de Agendamentos</p>
                                <p class="text-2xl font-bold text-blue-700">{{ $dados['estatisticas_agendamentos']['total'] }}</p>
                            </div>
                            <div class="bg-yellow-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-600">Aguardando Aprovação</p>
                                <p class="text-2xl font-bold text-yellow-700">{{ $dados['estatisticas_agendamentos']['pendentes'] }}</p>
                            </div>
                            <div class="bg-green-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-600">Aprovados</p>
                                <p class="text-2xl font-bold text-green-700">{{ $dados['estatisticas_agendamentos']['aprovados'] }}</p>
                            </div>
                            <div class="bg-indigo-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-600">Emprestados Agora</p>
                                <p class="text-2xl font-bold text-indigo-700">{{ $dados['estatisticas_agendamentos']['em_uso'] }}</p>
                            </div>
                            <div class="bg-purple-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-600">Já Devolvidos</p>
                                <p class="text-2xl font-bold text-purple-700">{{ $dados['estatisticas_agendamentos']['devolvidos'] }}</p>
                            </div>
                        </div>

                        <!-- Movimentação de Itens -->
                        <h5 class="text-md font-semibold mb-3 text-gray-700">📊 Movimentação de Itens</h5>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-cyan-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-600">Total de Itens Emprestados (Histórico)</p>
                                <p class="text-2xl font-bold text-cyan-700">{{ $dados['estatisticas_agendamentos']['total_itens_emprestados'] }}</p>
                                <p class="text-xs text-gray-500 mt-1">Soma de todos os empréstimos</p>
                            </div>
                            <div class="bg-teal-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-600">Total de Itens Devolvidos</p>
                                <p class="text-2xl font-bold text-teal-700">{{ $dados['estatisticas_agendamentos']['total_itens_devolvidos'] }}</p>
                                <p class="text-xs text-gray-500 mt-1">Que retornaram ao estoque</p>
                            </div>
                            <div class="bg-red-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-600">Total de Itens Perdidos</p>
                                <p class="text-2xl font-bold text-red-700">{{ $dados['estatisticas_agendamentos']['total_itens_perdidos'] }}</p>
                                <p class="text-xs text-gray-500 mt-1">Nas devoluções</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabela de Agendamentos -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold mb-4 text-gray-800">📋 Lista de Agendamentos</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Material</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Professor</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data Retirada</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data Devolução</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Qtd</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($dados['agendamentos'] as $agendamento)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $agendamento->id }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-900">{{ $agendamento->material->nome }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-600">{{ $agendamento->user->name }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                                                {{ \Carbon\Carbon::parse($agendamento->data_retirada)->format('d/m/Y') }}
                                                @if($agendamento->horario_retirada)
                                                    <br><span class="text-xs text-gray-500">{{ $agendamento->horario_retirada }}</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                                                @if($agendamento->data_devolucao)
                                                    {{ \Carbon\Carbon::parse($agendamento->data_devolucao)->format('d/m/Y') }}
                                                @elseif($agendamento->data_devolucao_prevista)
                                                    <span class="text-yellow-600">{{ \Carbon\Carbon::parse($agendamento->data_devolucao_prevista)->format('d/m/Y') }}</span>
                                                    <br><span class="text-xs text-gray-500">(prevista)</span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-center">
                                                <div class="font-semibold">{{ $agendamento->quantidade }}</div>
                                                @if($agendamento->quantidade_devolvida > 0)
                                                    <div class="text-xs text-green-600">Devol: {{ $agendamento->quantidade_devolvida }}</div>
                                                @endif
                                                @if($agendamento->quantidade_perdida > 0)
                                                    <div class="text-xs text-red-600">Perd: {{ $agendamento->quantidade_perdida }}</div>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                                    @if($agendamento->status === 'pendente') bg-yellow-100 text-yellow-800
                                                    @elseif($agendamento->status === 'aprovado') bg-green-100 text-green-800
                                                    @elseif($agendamento->status === 'em_uso') bg-blue-100 text-blue-800
                                                    @elseif($agendamento->status === 'devolvido') bg-purple-100 text-purple-800
                                                    @else bg-red-100 text-red-800
                                                    @endif">
                                                    {{ ucfirst(str_replace('_', ' ', $agendamento->status)) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                                Nenhum agendamento encontrado com os filtros aplicados.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Botões de Exportação -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h4 class="text-lg font-semibold mb-4 text-gray-800">💾 Exportar Relatório</h4>
                    <div class="flex flex-wrap gap-3">
                        <form action="{{ route('relatorio.exportar-pdf') }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="tipo" value="{{ $dados['tipo'] }}">
                            <input type="hidden" name="data_inicio" value="{{ $dados['filtros']['data_inicio'] }}">
                            <input type="hidden" name="data_fim" value="{{ $dados['filtros']['data_fim'] }}">
                            <input type="hidden" name="categoria" value="{{ $dados['filtros']['categoria'] }}">
                            <input type="hidden" name="status" value="{{ $dados['filtros']['status'] }}">
                            <input type="hidden" name="origem" value="{{ $dados['filtros']['origem'] }}">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Baixar PDF
                            </button>
                        </form>

                        <form action="{{ route('relatorio.exportar-csv') }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="tipo" value="{{ $dados['tipo'] }}">
                            <input type="hidden" name="data_inicio" value="{{ $dados['filtros']['data_inicio'] }}">
                            <input type="hidden" name="data_fim" value="{{ $dados['filtros']['data_fim'] }}">
                            <input type="hidden" name="categoria" value="{{ $dados['filtros']['categoria'] }}">
                            <input type="hidden" name="status" value="{{ $dados['filtros']['status'] }}">
                            <input type="hidden" name="origem" value="{{ $dados['filtros']['origem'] }}">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Baixar CSV
                            </button>
                        </form>

                        <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                            Imprimir
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
