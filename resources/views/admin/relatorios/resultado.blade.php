@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Resultado do Relatório</h1>
        <div>
            <form action="{{ route('admin.relatorios.exportar') }}" method="POST" class="d-inline">
                @csrf
                <input type="hidden" name="tipo" value="{{ $tipo }}">
                <button type="submit" class="btn btn-success">📥 Exportar CSV</button>
            </form>
            <a href="{{ route('admin.relatorios.index') }}" class="btn btn-secondary">← Voltar</a>
        </div>
    </div>

    @if($tipo === 'materiais_falta')
        <h4 class="mb-3">Materiais em Falta</h4>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Categoria</th>
                        <th>Local</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dados as $material)
                        <tr>
                            <td>{{ $material->id }}</td>
                            <td>{{ $material->nome }}</td>
                            <td>{{ $material->categoria }}</td>
                            <td>{{ $material->local_guardado }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Nenhum material em falta!</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    @elseif($tipo === 'materiais_acabando')
        <h4 class="mb-3">Materiais Quase no Fim (≤ 20% do total)</h4>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-warning">
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Categoria</th>
                        <th>Qtd Total</th>
                        <th>Qtd Disponível</th>
                        <th>%</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dados as $material)
                        <tr>
                            <td>{{ $material->id }}</td>
                            <td>{{ $material->nome }}</td>
                            <td>{{ $material->categoria }}</td>
                            <td>{{ $material->quantidade_total }}</td>
                            <td>{{ $material->quantidade_disponivel }}</td>
                            <td>{{ round(($material->quantidade_disponivel / $material->quantidade_total) * 100) }}%</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Todos os materiais estão em boa quantidade!</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    @elseif($tipo === 'materiais_utilizados')
        <h4 class="mb-3">Materiais Mais Utilizados</h4>
        @if($dataInicio || $dataFim)
            <p class="text-muted">Período: {{ $dataInicio ? \Carbon\Carbon::parse($dataInicio)->format('d/m/Y') : 'Início' }} até {{ $dataFim ? \Carbon\Carbon::parse($dataFim)->format('d/m/Y') : 'Hoje' }}</p>
        @endif
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-info">
                    <tr>
                        <th>Material</th>
                        <th>Categoria</th>
                        <th>Total de Usos</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dados as $item)
                        <tr>
                            <td>{{ $item->material->nome }}</td>
                            <td>{{ $item->material->categoria }}</td>
                            <td><span class="badge bg-info">{{ $item->total_usos }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">Nenhum agendamento registrado no período.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    @elseif($tipo === 'todos_materiais')
        <h4 class="mb-3">Todos os Materiais Cadastrados</h4>
        <div class="table-responsive">
            <table class="table table-bordered table-sm">
                <thead class="table-primary">
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Categoria</th>
                        <th>Qtd Total</th>
                        <th>Qtd Disponível</th>
                        <th>Estado</th>
                        <th>Local</th>
                        <th>Idade</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dados as $material)
                        <tr>
                            <td>{{ $material->id }}</td>
                            <td>{{ $material->nome }}</td>
                            <td>{{ $material->categoria }}</td>
                            <td>{{ $material->quantidade_total }}</td>
                            <td>{{ $material->quantidade_disponivel }}</td>
                            <td>{{ ucfirst($material->estado_conservacao) }}</td>
                            <td>{{ $material->local_guardado }}</td>
                            <td>{{ $material->idade_recomendada }} anos</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">Nenhum material cadastrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
