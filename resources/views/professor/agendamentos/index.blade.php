@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Meus Agendamentos</h1>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
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
                                <td>{{ $agendamento->material->nome }}</td>
                                <td>{{ $agendamento->quantidade }}</td>
                                <td>{{ $agendamento->data_retirada->format('d/m/Y') }}
                                <br><small class="text-muted">{{ $agendamento->horario_retirada }}</small></td>
                                <td>
                                    @if($agendamento->data_devolucao_prevista)
                                    {{ $agendamento->data_devolucao_prevista->format('d/m/Y') }}
                                    <br><small class="text-muted">{{ $agendamento->horario_devolucao }}</small>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{!! $agendamento->getStatusBadge() !!}</td>
                                <td>
                                    <a href="{{ route('professor.agendamentos.show', $agendamento) }}" class="btn btn-sm btn-info">Ver</a>

                                    @if($agendamento->status == 'pendente')
                                        <form action="{{ route('professor.agendamentos.destroy', $agendamento) }}" method="POST" class="d-inline" onsubmit="return confirm('Deseja cancelar este agendamento?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Cancelar</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Nenhum agendamento encontrado.</td>
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
