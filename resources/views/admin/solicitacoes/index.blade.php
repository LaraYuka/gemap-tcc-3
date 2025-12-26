@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">Gerenciar Solicitações de Materiais</h1>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.solicitacoes.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <input type="text" name="pesquisa" class="form-control" placeholder="Pesquisar professor..." value="{{ request('pesquisa') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">Todos os Status</option>
                            <option value="em_processo" {{ request('status') == 'em_processo' ? 'selected' : '' }}>Em Processo</option>
                            <option value="aceito" {{ request('status') == 'aceito' ? 'selected' : '' }}>Aceito</option>
                            <option value="recusado" {{ request('status') == 'recusado' ? 'selected' : '' }}>Recusado</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary">Filtrar</button>
                        <a href="{{ route('admin.solicitacoes.index') }}" class="btn btn-secondary">Limpar</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        @forelse($solicitacoes as $solicitacao)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    @if($solicitacao->foto)
                        <img src="{{ asset('storage/' . $solicitacao->foto) }}" class="card-img-top" alt="Foto" style="height: 200px; object-fit: cover;">
                    @else
                        <div class="bg-secondary d-flex align-items-center justify-content-center" style="height: 200px;">
                            <span class="text-white">Sem foto</span>
                        </div>
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ $solicitacao->nome_material }}</h5>
                        <p class="card-text"><strong>Solicitante:</strong> {{ $solicitacao->user->name }}</p>
                        <p class="card-text"><strong>Data Necessária:</strong> {{ $solicitacao->data_necessaria->format('d/m/Y') }}</p>
                        <p class="card-text text-muted small">{{ Str::limit($solicitacao->descricao, 100) }}</p>

                        <div class="mb-3">
                            {!! $solicitacao->getStatusBadge() !!}
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        <a href="{{ route('admin.solicitacoes.show', $solicitacao) }}" class="btn btn-sm btn-info w-100 mb-2">Ver Detalhes</a>

                        @if($solicitacao->status == 'em_processo')
                            <div class="d-flex gap-2">
                                <form action="{{ route('admin.solicitacoes.aceitar', $solicitacao) }}" method="POST" class="flex-fill">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success w-100">Aceitar</button>
                                </form>
                                <form action="{{ route('admin.solicitacoes.recusar', $solicitacao) }}" method="POST" class="flex-fill">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger w-100">Recusar</button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">Nenhuma solicitação encontrada.</div>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $solicitacoes->links() }}
    </div>
</div>
@endsection
