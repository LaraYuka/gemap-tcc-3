@extends('layouts.app')

@section('content')
<div class="container">
    <div class="mb-3">
        <a href="{{ route('professor.solicitacoes.index') }}" class="btn btn-secondary">← Voltar</a>
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

                    <h6>Data Necessária</h6>
                    <p>{{ $solicitacao->data_necessaria->format('d/m/Y') }}</p>

                    <h6>Descrição / Motivo</h6>
                    <p>{{ $solicitacao->descricao }}</p>

                    <h6>Data da Solicitação</h6>
                    <p>{{ $solicitacao->created_at->format('d/m/Y H:i') }}</p>

                    @if($solicitacao->status == 'aceito')
                        <div class="alert alert-success">
                            ✓ Sua solicitação foi aceita! O material está sendo providenciado.
                        </div>
                    @elseif($solicitacao->status == 'recusado')
                        <div class="alert alert-danger">
                            ✗ Sua solicitação foi recusada.
                        </div>
                    @else
                        <div class="alert alert-warning">
                            ⏳ Sua solicitação está sendo analisada pelo administrador.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
