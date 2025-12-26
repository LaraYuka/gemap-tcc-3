@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">Gerenciar Agendamentos</h1>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.agendamentos.index') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <select name="status" class="form-select">
                            <option value="">Todos os Status</option>
                            <option value="pendente" {{ request('status') == 'pendente' ? 'selected' : '' }}>Pendente</option>
                            <option value="aprovado" {{ request('status') == 'aprovado' ? 'selected' : '' }}>Aprovado</option>
                            <option value="recusado" {{ request('status') == 'recusado' ? 'selected' : '' }}>Recusado</option>
                            <option value="em_uso" {{ request('status') == 'em_uso' ? 'selected' : '' }}>Em Uso</option>
                            <option value="devolvido" {{ request('status') == 'devolvido' ? 'selected' : '' }}>Devolvido</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary">Filtrar</button>
                        <a href="{{ route('admin.agendamentos.index') }}" class="btn btn-secondary">Limpar</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Professor</th>
                            <th>Material</th>
                            <th>Quantidade</th>
                            <th>Data Retirada</th>
                            <th>Data Devolução</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($agendamentos as $agendamento)
                            <tr>
                                <td>{{ $agendamento->id }}</td>
                                <td>{{ $agendamento->user->name }}</td>
                                <td>
                                    {{ $agendamento->material->nome }}
                                    @if($agendamento->material->isConsumivel())
                                        <span class="badge bg-warning text-dark">Consumível</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $agendamento->quantidade }}
                                    @if($agendamento->status == 'devolvido' && isset($agendamento->quantidade_devolvida))
                                        <br>
                                        <small class="text-success">✅ {{ $agendamento->quantidade_devolvida }}</small>
                                        @if($agendamento->quantidade_perdida > 0)
                                            <small class="text-danger">❌ {{ $agendamento->quantidade_perdida }}</small>
                                        @endif
                                    @endif
                                </td>

                                <td>
                                    {{ \Carbon\Carbon::parse($agendamento->data_retirada)->format('d/m/Y') }}
                                    <br><small class="text-muted">{{ $agendamento->horario_retirada }}</small>
                                </td>
                                <td>
                                    @if($agendamento->data_devolucao_prevista)
                                        {{ \Carbon\Carbon::parse($agendamento->data_devolucao_prevista)->format('d/m/Y') }}
                                        <br><small class="text-muted">{{ $agendamento->horario_devolucao }}</small>
                                    @elseif($agendamento->data_devolucao)
                                        {{ \Carbon\Carbon::parse($agendamento->data_devolucao)->format('d/m/Y') }}
                                        <br><small class="badge bg-success">Devolvido</small>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $statusBadge = match($agendamento->status) {
                                            'pendente' => 'warning',
                                            'aprovado' => 'info',
                                            'recusado' => 'danger',
                                            'em_uso' => 'primary',
                                            'devolvido' => 'success',
                                            default => 'secondary'
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $statusBadge }}">
                                        {{ ucfirst(str_replace('_', ' ', $agendamento->status)) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.agendamentos.show', $agendamento) }}" class="btn btn-sm btn-info">Ver</a>

                                    @if($agendamento->status == 'pendente')
                                        <form action="{{ route('admin.agendamentos.aprovar', $agendamento) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success">Aprovar</button>
                                        </form>
                                        <form action="{{ route('admin.agendamentos.recusar', $agendamento) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-danger">Recusar</button>
                                        </form>
                                    @endif

                                    @if($agendamento->status == 'aprovado')
                                        <form action="{{ route('admin.agendamentos.retirar', $agendamento) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-primary">Marcar Retirado</button>
                                        </form>
                                    @endif

                                    @if($agendamento->status == 'em_uso')
                                        <a href="{{ route('admin.agendamentos.form-devolucao', $agendamento) }}" class="btn btn-sm btn-warning">
                                            📦 Registrar Devolução
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">Nenhum agendamento encontrado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $agendamentos->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
