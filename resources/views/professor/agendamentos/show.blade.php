@extends('layouts.app')

@section('content')
<div class="container">
    <div class="mb-3">
        <a href="{{ route('professor.agendamentos.index') }}" class="btn btn-secondary">← Voltar</a>
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
                    </div>

                    <h6>Material</h6>
                    <p>{{ $agendamento->material->nome }}</p>

                    <h6>Quantidade</h6>
                    <p>{{ $agendamento->quantidade }} unidade(s)</p>

                    <h6>Data de Retirada</h6>
                    <p>{{ $agendamento->data_retirada->format('d/m/Y') }}</p>


                    <h6>Horário de Retirada</h6>
                    <p>{{ $agendamento->horario_retirada }}</p>

                    <h6>Data de Devolução Prevista</h6>
                    <p>{{ $agendamento->data_devolucao_prevista->format('d/m/Y') }}</p>

                    <h6>Horário de Devolução</h6>
                    <p>{{ $agendamento->horario_devolucao }}</p>

                    @if($agendamento->data_devolucao)
                        <h6>Data de Devolução</h6>
                        <p>{{ $agendamento->data_devolucao->format('d/m/Y') }}</p>
                    @endif

                    @if($agendamento->observacoes)
                        <h6>Observações</h6>
                        <p>{{ $agendamento->observacoes }}</p>
                    @endif

                    <h6>Solicitado em</h6>
                    <p>{{ $agendamento->created_at->format('d/m/Y H:i') }}</p>

                    @if($agendamento->status == 'pendente')
                        <hr>
                        <form action="{{ route('professor.agendamentos.destroy', $agendamento) }}" method="POST" onsubmit="return confirm('Deseja cancelar este agendamento?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Cancelar Agendamento</button>
                        </form>
                    @endif
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
                        <img src="{{ asset('storage/' . $agendamento->material->fotos[0]) }}" class="img-fluid mb-3" alt="{{ $agendamento->material->nome }}">
                    @endif

                    <p><strong>Categoria:</strong> {{ $agendamento->material->categoria }}</p>
                    <p><strong>Local:</strong> {{ $agendamento->material->local_guardado }}</p>
                    <p><strong>Estado:</strong> {{ ucfirst($agendamento->material->estado_conservacao) }}</p>

                    <a href="{{ route('professor.materials.show', $agendamento->material) }}" class="btn btn-sm btn-primary w-100">Ver Material</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
