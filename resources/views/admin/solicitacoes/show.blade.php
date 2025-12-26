@extends('layouts.app')

@section('content')
<div class="container">
    <div class="mb-3">
        <a href="{{ route('admin.solicitacoes.index') }}" class="btn btn-secondary">← Voltar</a>
    </div>

    <div class="row">
        <div class="col-md-6">
            @if($solicitacao->foto)
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">Foto Enviada</h5>
                    </div>
                    <div class="card-body">
                        <img src="{{ asset('storage/' . $solicitacao->foto) }}" class="img-fluid" alt="Foto da solicitação">
                    </div>
                </div>
            @endif
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Detalhes da Solicitação</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        {!! $solicitacao->getStatusBadge() !!}
                    </div>

                    <h6>Nome do Material Solicitado</h6>
                    <p>{{ $solicitacao->nome_material }}</p>

                    <h6>Solicitante</h6>
                    <p>{{ $solicitacao->user->name }} ({{ $solicitacao->user->email }})</p>

                    <h6>Data Necessária</h6>
                    <p>{{ $solicitacao->data_necessaria->format('d/m/Y') }}</p>

                    <h6>Descrição / Motivo</h6>
                    <p>{{ $solicitacao->descricao }}</p>

                    <h6>Data da Solicitação</h6>
                    <p>{{ $solicitacao->created_at->format('d/m/Y H:i') }}</p>

                    @if($solicitacao->status == 'em_processo')
                        <hr>
                        <h6>Ações</h6>
                        <div class="d-flex gap-2">
                            <form action="{{ route('admin.solicitacoes.aceitar', $solicitacao) }}" method="POST" class="flex-fill">
                                @csrf
                                <button type="submit" class="btn btn-success w-100">
                                    ✓ Aceitar Solicitação
                                </button>
                            </form>
                            <form action="{{ route('admin.solicitacoes.recusar', $solicitacao) }}" method="POST" class="flex-fill">
                                @csrf
                                <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Tem certeza que deseja recusar esta solicitação?')">
                                    ✗ Recusar Solicitação
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
