@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Minhas Solicitações</h1>
        <a href="{{ route('professor.solicitacoes.create') }}" class="btn btn-success">+ Nova Solicitação</a>
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
                        <p class="card-text"><strong>Data Necessária:</strong> {{ $solicitacao->data_necessaria->format('d/m/Y') }}</p>
                        <p class="card-text text-muted small">{{ Str::limit($solicitacao->descricao, 100) }}</p>

                        <div class="mb-3">
                            {!! $solicitacao->getStatusBadge() !!}
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        <a href="{{ route('professor.solicitacoes.show', $solicitacao->id) }}" class="btn btn-sm btn-info w-100">Ver detalhes</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">Nenhuma solicitação encontrada. <a href="{{ route('professor.solicitacoes.create') }}">Criar nova solicitação</a></div>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $solicitacoes->links() }}
    </div>
</div>
@endsection
