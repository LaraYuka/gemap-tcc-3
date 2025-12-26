@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>🎁 Minhas Doações</h1>
        <a href="{{ route('visitante.doacoes.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Nova Doação
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">📋 Sobre as Doações</h5>
            <p class="card-text">
                Você pode doar brinquedos, livros, roupas, materiais pedagógicos e alimentos para a creche.
                Todas as doações passam por uma análise do administrador antes de serem aceitas.
            </p>
            <ul class="mb-0">
                <li><strong>Pendente:</strong> Aguardando análise do administrador</li>
                <li><strong>Aprovado:</strong> Doação aceita, aguardando entrega</li>
                <li><strong>Recusado:</strong> Doação não pode ser aceita (veja a observação)</li>
                <li><strong>Recebido:</strong> Doação entregue e recebida pela creche</li>
            </ul>
        </div>
    </div>

    @if($doacoes->isEmpty())
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> Você ainda não registrou nenhuma doação.
            <a href="{{ route('visitante.doacoes.create') }}" class="alert-link">Clique aqui para fazer sua primeira doação</a>.
        </div>
    @else
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tipo</th>
                                <th>Descrição</th>
                                <th>Quantidade</th>
                                <th>Data</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($doacoes as $doacao)
                                <tr>
                                    <td>{{ $doacao->id }}</td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $doacao->tipo_doacao }}</span>
                                    </td>
                                    <td>{{ Str::limit($doacao->descricao, 50) }}</td>
                                    <td class="text-center">{{ $doacao->quantidade }}</td>
                                    <td>{{ $doacao->data_doacao->format('d/m/Y') }}</td>
                                    <td>{!! $doacao->getStatusBadge() !!}</td>
                                    <td>
                                        <a href="{{ route('visitante.doacoes.show', $doacao) }}"
                                           class="btn btn-sm btn-info">
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
            </div>
        </div>
    @endif
</div>
@endsection
