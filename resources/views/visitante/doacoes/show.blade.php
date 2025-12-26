@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>🎁 Detalhes da Doação #{{ $doacao->id }}</h1>
        <a href="{{ route('visitante.doacoes.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Informações da Doação</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Nome do Doador:</strong>
                            <p>{{ $doacao->nome_doador }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Telefone:</strong>
                            <p>{{ $doacao->telefone ?? 'Não informado' }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>E-mail:</strong>
                            <p>{{ $doacao->email ?? 'Não informado' }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Data da Doação:</strong>
                            <p>{{ $doacao->data_doacao->format('d/m/Y') }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Tipo:</strong>
                            <p><span class="badge bg-secondary">{{ $doacao->tipo_doacao }}</span></p>
                        </div>
                        <div class="col-md-4">
                            <strong>Quantidade:</strong>
                            <p class="fs-5 fw-bold">{{ $doacao->quantidade }}</p>
                        </div>
                        <div class="col-md-4">
                            <strong>Estado:</strong>
                            <p>{{ ucfirst($doacao->estado_conservacao) }}</p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <strong>Descrição:</strong>
                        <p class="text-muted">{{ $doacao->descricao }}</p>
                    </div>

                    @if($doacao->fotos)
                        <div class="mb-3">
                            <strong>Fotos:</strong>
                            <div class="row g-2 mt-2">
                                @foreach($doacao->fotos as $foto)
                                    <div class="col-md-3">
                                        <img src="{{ Storage::url($foto) }}"
                                             class="img-fluid rounded"
                                             alt="Foto da doação"
                                             style="cursor: pointer;"
                                             onclick="window.open('{{ Storage::url($foto) }}', '_blank')">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header bg-{{ $doacao->getStatusColor() }} text-white">
                    <h5 class="mb-0">Status</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        {!! $doacao->getStatusBadge() !!}
                    </div>

                    <div class="mb-3">
                        <strong>Registrado em:</strong>
                        <p>{{ $doacao->created_at->format('d/m/Y H:i') }}</p>
                    </div>

                    @if($doacao->data_resposta)
                        <div class="mb-3">
                            <strong>Resposta em:</strong>
                            <p>{{ $doacao->data_resposta->format('d/m/Y H:i') }}</p>
                        </div>
                    @endif

                    @if($doacao->status === 'pendente')
                        <div class="alert alert-warning mb-0">
                            <i class="bi bi-clock"></i> Sua doação está aguardando análise do administrador.
                        </div>
                    @elseif($doacao->status === 'aprovado')
                        <div class="alert alert-success mb-0">
                            <i class="bi bi-check-circle"></i> Sua doação foi aprovada! Aguarde contato para combinar a entrega.
                        </div>
                    @elseif($doacao->status === 'recusado')
                        <div class="alert alert-danger mb-0">
                            <i class="bi bi-x-circle"></i> Infelizmente sua doação não pôde ser aceita. Veja a observação abaixo.
                        </div>
                    @elseif($doacao->status === 'recebido')
                        <div class="alert alert-info mb-0">
                            <i class="bi bi-check2-all"></i> Doação recebida! Obrigado pela contribuição! 🎉
                        </div>
                    @endif
                </div>
            </div>

            @if($doacao->observacao_admin)
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Observação do Administrador</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $doacao->observacao_admin }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
