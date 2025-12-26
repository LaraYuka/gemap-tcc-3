@extends('layouts.app')

@section('content')
<div class="container">
    <div class="mb-3">
        <a href="{{ route('admin.agendamentos.index') }}" class="btn btn-secondary">← Voltar</a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Detalhes do Agendamento #{{ $agendamento->id }}</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        {!! $agendamento->getStatusBadge() !!}
                        @if($agendamento->material->isConsumivel())
                            <span class="badge bg-warning">Material Consumível</span>
                        @endif
                    </div>

                    <h6>Professor</h6>
                    <p>{{ $agendamento->user->name }} ({{ $agendamento->user->email }})</p>

                    <h6>Material</h6>
                    <p>{{ $agendamento->material->nome }}</p>

                    <h6>Tipo de Material</h6>
                    <p>{!! $agendamento->material->getTipoMaterialBadge() !!}</p>

                    <h6>Quantidade</h6>
                    <p>{{ $agendamento->quantidade }} unidade(s)</p>

                    <h6>Data de Retirada</h6>
                    <p>{{ $agendamento->data_retirada->format('d/m/Y') }}</p>

                    <h6>Horário de Retirada</h6>
                    <p>{{ $agendamento->horario_retirada }}</p>

                    @if($agendamento->material->isReutilizavel())
                        <h6>Data de Devolução Prevista</h6>
                        <p>{{ $agendamento->data_devolucao_prevista ? $agendamento->data_devolucao_prevista->format('d/m/Y') : '-' }}</p>

                        <h6>Horário de Devolução</h6>
                        <p>{{ $agendamento->horario_devolucao ?? '-' }}</p>
                    @else
                        <div class="alert alert-info">
                            <strong>ℹ️ Material Consumível</strong><br>
                            Este material não retorna após o uso. Não há data de devolução.
                        </div>
                    @endif

                    @if($agendamento->data_devolucao)
                        <h6>Data de Devolução Efetiva</h6>
                        <p>{{ $agendamento->data_devolucao->format('d/m/Y H:i') }}</p>
                    @endif

                    @if($agendamento->observacoes)
                        <h6>Observações</h6>
                        <p>{{ $agendamento->observacoes }}</p>
                    @endif

                    <h6>Solicitado em</h6>
                    <p>{{ $agendamento->created_at->format('d/m/Y H:i') }}</p>

                    <hr>

                    <h6>Ações</h6>
                    <div class="d-flex gap-2 flex-wrap">
                        @if($agendamento->status == 'pendente')
                            <form action="{{ route('admin.agendamentos.aprovar', $agendamento) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success">✓ Aprovar</button>
                            </form>
                            <form action="{{ route('admin.agendamentos.recusar', $agendamento) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Deseja recusar este agendamento?')">✗ Recusar</button>
                            </form>
                        @endif

                        @if($agendamento->status == 'aprovado')
                            <form action="{{ route('admin.agendamentos.retirar', $agendamento) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-primary">📦 Marcar como Retirado</button>
                            </form>
                        @endif

                        @if($agendamento->status == 'em_uso')
                            <form action="{{ route('admin.agendamentos.devolver', $agendamento) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-warning">
                                    @if($agendamento->material->isConsumivel())
                                        ✓ Confirmar Uso
                                    @else
                                        ↩️ Registrar Devolução
                                    @endif
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Informações do Material</h6>
                </div>
                <div class="card-body">
                    @if($agendamento->material->fotos && count($agendamento->material->fotos) > 0)
                        <img src="{{ asset('storage/' . $agendamento->material->fotos[0]) }}" class="img-fluid mb-3 rounded" alt="{{ $agendamento->material->nome }}">
                    @endif

                    <p><strong>Categoria:</strong> {{ $agendamento->material->categoria }}</p>
                    <p><strong>Quantidade Total:</strong> {{ $agendamento->material->quantidade_total }}</p>
                    <p><strong>Quantidade Disponível:</strong> <span class="badge bg-info">{{ $agendamento->material->quantidade_disponivel }}</span></p>
                    <p><strong>Local:</strong> 📍 {{ $agendamento->material->local_guardado }}</p>
                    <p><strong>Estado:</strong> {{ ucfirst($agendamento->material->estado_conservacao) }}</p>

                    <a href="{{ route('admin.materials.show', $agendamento->material) }}" class="btn btn-sm btn-primary w-100">Ver Material Completo</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
