@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">🏠 Bem-vindo, {{ Auth::user()->name }}!</h1>

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">👋 Área do Visitante</h5>
                    <p class="card-text">
                        Como visitante, você pode fazer doações de materiais para a creche e acessar relatórios públicos.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card h-100 border-success">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">🎁 Fazer Doação</h5>
                </div>
                <div class="card-body">
                    <p>Doe brinquedos, livros, roupas, materiais pedagógicos e alimentos para ajudar a creche!</p>
                    <ul>
                        <li>Registre sua doação online</li>
                        <li>Acompanhe o status da análise</li>
                        <li>Receba feedback do administrador</li>
                        <li>Veja o histórico de suas doações</li>
                    </ul>
                    <a href="{{ route('visitante.doacoes.create') }}" class="btn btn-success w-100 mt-3">
                        <i class="bi bi-plus-circle"></i> Nova Doação
                    </a>

                    <a href="{{ route('visitante.doacoes.index') }}" class="btn btn-outline-success w-100 mt-2">
                        <i class="bi bi-list-ul"></i> Minhas Doações
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card h-100 border-info">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">📊 Relatórios</h5>
                </div>
                <div class="card-body">
                    <p>Acesse relatórios sobre os materiais disponíveis na creche:</p>
                    <ul>
                        <li>Materiais disponíveis para empréstimo</li>
                        <li>Histórico de agendamentos</li>
                        <li>Estatísticas de uso</li>
                        <li>Exportar em PDF ou CSV</li>
                    </ul>
                    <a href="{{ route('relatorio.index') }}" class="btn btn-info w-100 mt-3">
                        <i class="bi bi-file-earmark-bar-graph"></i> Acessar Relatórios
                    </a>
                </div>
            </div>
        </div>
    </div>

    @php
        $minhasDoacoes = \App\Models\Doacao::where('user_id', auth()->id())->get();
        $totalDoacoes = $minhasDoacoes->count();
        $doacoesPendentes = $minhasDoacoes->where('status', 'pendente')->count();
        $doacoesAprovadas = $minhasDoacoes->where('status', 'aprovado')->count();
        $doacoesRecebidas = $minhasDoacoes->where('status', 'recebido')->count();
    @endphp

    @if($totalDoacoes > 0)
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">📈 Minhas Estatísticas de Doações</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <div class="p-3">
                            <h3 class="display-4 text-primary">{{ $totalDoacoes }}</h3>
                            <p class="text-muted">Total de Doações</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3">
                            <h3 class="display-4 text-warning">{{ $doacoesPendentes }}</h3>
                            <p class="text-muted">Pendentes</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3">
                            <h3 class="display-4 text-success">{{ $doacoesAprovadas }}</h3>
                            <p class="text-muted">Aprovadas</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3">
                            <h3 class="display-4 text-info">{{ $doacoesRecebidas }}</h3>
                            <p class="text-muted">Recebidas</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">📋 Últimas Doações</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tipo</th>
                                <th>Descrição</th>
                                <th>Quantidade</th>
                                <th>Data</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($minhasDoacoes->sortByDesc('created_at')->take(5) as $doacao)
                                <tr>
                                    <td><span class="badge bg-secondary">{{ $doacao->tipo_doacao }}</span></td>
                                    <td>{{ Str::limit($doacao->descricao, 40) }}</td>
                                    <td class="text-center">{{ $doacao->quantidade }}</td>
                                    <td>{{ $doacao->data_doacao->format('d/m/Y') }}</td>
                                    <td>{!! $doacao->getStatusBadge() !!}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="text-center mt-3">
                    <a href="{{ route('visitante.doacoes.index') }}" class="btn btn-outline-primary">
                        Ver Todas as Doações
                    </a>
                </div>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-gift" style="font-size: 4rem; color: #ccc;"></i>
                <h4 class="mt-3">Você ainda não fez nenhuma doação</h4>
                <p class="text-muted">Comece agora a ajudar a creche com suas doações!</p>
                <a href="{{ route('visitante.doacoes.create') }}" class="btn btn-success btn-lg">
                    <i class="bi bi-plus-circle"></i> Fazer Primeira Doação
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
