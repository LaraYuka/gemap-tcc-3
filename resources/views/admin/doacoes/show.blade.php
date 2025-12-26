@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>🎁 Doação #{{ $doacao->id }}</h1>
        <a href="{{ route('admin.doacoes.index') }}" class="btn btn-secondary">
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
                            <strong>Usuário:</strong>
                            <p>{{ $doacao->user->name }} ({{ $doacao->user->email }})</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Telefone:</strong>
                            <p>{{ $doacao->telefone ?? 'Não informado' }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>E-mail:</strong>
                            <p>{{ $doacao->email ?? 'Não informado' }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Data da Doação:</strong>
                            <p>{{ $doacao->data_doacao->format('d/m/Y') }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Registrado em:</strong>
                            <p>{{ $doacao->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Tipo:</strong>
                            <p><span class="badge bg-secondary fs-6">{{ $doacao->tipo_doacao }}</span></p>
                        </div>
                        <div class="col-md-4">
                            <strong>Quantidade:</strong>
                            <p class="fs-4 fw-bold text-primary">{{ $doacao->quantidade }}</p>
                        </div>
                        <div class="col-md-4">
                            <strong>Estado:</strong>
                            <p class="fs-6">{{ ucfirst($doacao->estado_conservacao) }}</p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <strong>Descrição:</strong>
                        <p class="text-muted border p-3 rounded">{{ $doacao->descricao }}</p>
                    </div>

                    @if($doacao->fotos)
                        <div class="mb-3">
                            <strong>Fotos:</strong>
                            <div class="row g-2 mt-2">
                                @foreach($doacao->fotos as $foto)
                                    <div class="col-md-3">
                                        <img src="{{ Storage::url($foto) }}"
                                             class="img-fluid rounded border"
                                             alt="Foto da doação"
                                             style="cursor: pointer; height: 150px; object-fit: cover; width: 100%;"
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
                    <h5 class="mb-0">Status Atual</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <span class="badge bg-{{ $doacao->getStatusColor() }} fs-5">
                            {{ $doacao->getStatusLabel() }}
                        </span>
                    </div>

                    @if($doacao->data_resposta)
                        <div class="mb-2">
                            <strong>Resposta em:</strong>
                            <p>{{ $doacao->data_resposta->format('d/m/Y H:i') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            @if($doacao->observacao_admin)
                <div class="card mb-3">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Observação Registrada</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $doacao->observacao_admin }}</p>
                    </div>
                </div>
            @endif

            @if($doacao->status === 'pendente')
                <div class="card mb-3">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">Ações</h5>
                    </div>
                    <div class="card-body">
                        <button type="button" class="btn btn-success w-100 mb-2" data-bs-toggle="modal" data-bs-target="#aprovarModal">
                            <i class="bi bi-check-circle"></i> Aprovar Doação
                        </button>

                        <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#recusarModal">
                            <i class="bi bi-x-circle"></i> Recusar Doação
                        </button>
                    </div>
                </div>
            @elseif($doacao->status === 'aprovado')
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">Ações</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.doacoes.marcar-recebido', $doacao) }}" method="POST" class="mb-2">
                            @csrf
                            <button type="submit" class="btn btn-info w-100">
                                <i class="bi bi-check2-all"></i> Marcar como Recebido
                            </button>
                        </form>
                    </div>
                </div>
            @elseif($doacao->status === 'recebido')
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">Ações</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.doacoes.converter-material', $doacao) }}" method="POST" onsubmit="return confirm('Converter esta doação em material do estoque?')">
                            @csrf
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-arrow-right-circle"></i> Converter em Material
                            </button>
                        </form>
                        <small class="text-muted d-block mt-2">
                            Isso criará um novo material no estoque baseado nesta doação
                        </small>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="modal fade" id="aprovarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.doacoes.aprovar', $doacao) }}" method="POST">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Aprovar Doação</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Você está prestes a <strong>aprovar</strong> esta doação.</p>

                    <div class="mb-3">
                        <label for="observacao_admin_aprovar" class="form-label">Observação (opcional)</label>
                        <textarea class="form-control"
                                  id="observacao_admin_aprovar"
                                  name="observacao_admin"
                                  rows="3"
                                  placeholder="Ex: Entrar em contato para combinar a entrega"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle"></i> Aprovar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="recusarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.doacoes.recusar', $doacao) }}" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Recusar Doação</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Você está prestes a <strong>recusar</strong> esta doação.</p>

                    <div class="mb-3">
                        <label for="observacao_admin_recusar" class="form-label">Motivo da recusa *</label>
                        <textarea class="form-control"
                                  id="observacao_admin_recusar"
                                  name="observacao_admin"
                                  rows="3"
                                  required
                                  placeholder="Explique o motivo da recusa para o doador"></textarea>
                    </div>

                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i> O doador será notificado sobre a recusa e verá sua observação.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-x-circle"></i> Recusar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
