@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Relatórios</h1>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Gerar Relatório</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.relatorios.gerar') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="tipo" class="form-label">Tipo de Relatório *</label>
                            <select class="form-select" id="tipo" name="tipo" required>
                                <option value="">Selecione...</option>
                                <option value="materiais_falta">Materiais em Falta</option>
                                <option value="materiais_acabando">Materiais Quase no Fim</option>
                                <option value="materiais_utilizados">Materiais Mais Utilizados</option>
                                <option value="todos_materiais">Todos os Materiais</option>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="data_inicio" class="form-label">Data Início (Opcional)</label>
                                <input type="date" class="form-control" id="data_inicio" name="data_inicio">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="data_fim" class="form-label">Data Fim (Opcional)</label>
                                <input type="date" class="form-control" id="data_fim" name="data_fim">
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Gerar Relatório</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
