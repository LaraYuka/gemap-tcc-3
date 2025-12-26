@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">🎁 Gerenciar Doações</h1>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total</h5>
                    <p class="display-4">{{ $stats['total'] }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">Pendentes</h5>
                    <p class="display-4">{{ $stats['pendentes'] }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Aprovadas</h5>
                    <p class="display-4">{{ $stats['aprovadas'] }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Recebidas</h5>
                    <p class="display-4">{{ $stats['recebidas'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.doacoes.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">Todos</option>
                        <option value="pendente" {{ request('status') == 'pendente' ? 'selected' : '' }}>Pendente</option>
                        <option value="aprovado" {{ request('status') == 'aprovado' ? 'selected' : '' }}>Aprovado</option>
                        <option value="recusado" {{ request('status') == 'recusado' ? 'selected' : '' }}>Recusado</option>
                        <option value="recebido" {{ request('status') == 'recebido' ? 'selected' : '' }}>Recebido</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="tipo_doacao" class="form-label">Tipo</label>
                    <select name="tipo_doacao" id="tipo_doacao" class="form-select">
                        <option value="">Todos</option>
                        <option value="Brinquedo" {{ request('tipo_doacao') == 'Brinquedo' ? 'selected' : '' }}>Brinquedo</option>
                        <option value="Livro" {{ request('tipo_doacao') == 'Livro' ? 'selected' : '' }}>Livro</option>
                        <option value="Roupa" {{ request('tipo_doacao') == 'Roupa' ? 'selected' : '' }}>Roupa</option>
                        <option value="Material Pedagógico" {{ request('tipo_doacao') == 'Material Pedagógico' ? 'selected' : '' }}>Material Pedagógico</option>
                        <option value="Alimento" {{ request('tipo_doacao') == 'Alimento' ? 'selected' : '' }}>Alimento</option>
                        <option value="Outro" {{ request('tipo_doacao') == 'Outro' ? 'selected' : '' }}>Outro</option>
                    </select>
                </div>

                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="bi bi-funnel"></i> Filtrar
                    </button>
                    <a href="{{ route('admin.doacoes.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Limpar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            @if($doacoes->isEmpty())
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Nenhuma doação encontrada.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Doador</th>
                                <th>Tipo</th>
                                <th>Descrição</th>
                                <th>Qtd</th>
                                <th>Data</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($doacoes as $doacao)
                                <tr class="{{ $doacao->status === 'pendente' ? 'table-warning' : '' }}">
                                    <td>{{ $doacao->id }}</td>
                                    <td>
                                        <strong>{{ $doacao->nome_doador }}</strong><br>
                                        <small class="text-muted">{{ $doacao->user->email }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $doacao->tipo_doacao }}</span>
                                    </td>
                                    <td>{{ Str::limit($doacao->descricao, 50) }}</td>
                                    <td class="text-center fw-bold">{{ $doacao->quantidade }}</td>
                                    <td>{{ $doacao->data_doacao->format('d/m/Y') }}</td>
                                    <td>{!! $doacao->getStatusBadge() !!}</td>
                                    <td>
                                        <a href="{{ route('admin.doacoes.show', $doacao) }}"
                                           class="btn btn-sm btn-info"
                                           title="Ver detalhes">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $doacoes->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
