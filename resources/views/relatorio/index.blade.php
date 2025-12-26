@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">📊 Relatórios</h1>

    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Gerar Relatório</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('relatorio.gerar') }}" method="POST" id="formRelatorio">
                @csrf

                <div class="mb-3">
                    <label for="tipo" class="form-label">Tipo de Relatório *</label>
                    <select name="tipo" id="tipo" class="form-select" required>
                        <option value="materiais">📦 Materiais</option>
                        <option value="agendamentos">📅 Agendamentos</option>
                        <option value="completo">📋 Relatório Completo</option>
                        <option value="analise-avancada">🎯 Análise Avançada (Recomendado)</option>
                    </select>
                    <small class="text-muted">
                        Análise Avançada inclui: gráficos, alertas, histórico, ranking e comparativos
                    </small>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="data_inicio" class="form-label">Data Início</label>
                        <input type="date" name="data_inicio" id="data_inicio" class="form-control">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="data_fim" class="form-label">Data Fim</label>
                        <input type="date" name="data_fim" id="data_fim" class="form-control">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="categoria" class="form-label">Categoria (Materiais)</label>
                    <select name="categoria" id="categoria" class="form-select">
                        <option value="">Todas</option>
                        <option value="Livro">Livro</option>
                        <option value="Brinquedo">Brinquedo</option>
                        <option value="Jogo">Jogo</option>
                        <option value="Material Pedagógico">Material Pedagógico</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="status" class="form-label">Status (Materiais)</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">Todos</option>
                        <option value="DISPONIVEL">Disponível</option>
                        <option value="EM_USO">Em Uso</option>
                        <option value="INDISPONIVEL">Indisponível</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="origem" class="form-label">Origem (Materiais)</label>
                    <select name="origem" id="origem" class="form-select">
                        <option value="">Todos</option>
                        <option value="comprado">🛒 Comprado</option>
                        <option value="doacao">🎁 Doação</option>
                    </select>
                    <small class="text-muted">
                        Filtre materiais por origem: comprados pela creche ou recebidos por doação
                    </small>
                </div>

                    <button type="button" onclick="exportarPdf()" class="btn btn-danger">
                        <i class="bi bi-file-pdf"></i> Baixar PDF
                    </button>

                    <button type="button" onclick="exportarCsv()" class="btn btn-success">
                        <i class="bi bi-file-earmark-spreadsheet"></i> Baixar CSV
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="alert alert-info mt-4">
        <h5 class="alert-heading">📋 Sobre os Relatórios:</h5>
        <ul class="mb-0">
            <li><strong>Materiais:</strong> Lista todos os materiais com suas quantidades e status</li>
            <li><strong>Agendamentos:</strong> Histórico de empréstimos e devoluções</li>
            <li><strong>Completo:</strong> Inclui materiais e agendamentos em um único relatório</li>
            <li><strong>Análise Avançada:</strong> Relatório completo com:
                <ul>
                    <li>📊 Gráficos visuais de distribuição</li>
                    <li>⚠️ Alertas de materiais com problemas</li>
                    <li>📈 Histórico mensal (últimos 12 meses)</li>
                    <li>🏆 Ranking de materiais mais usados</li>
                    <li>💰 Análise de perdas e custos</li>
                </ul>
            </li>
            <li><strong>PDF:</strong> Formato ideal para impressão e visualização</li>
            <li><strong>CSV:</strong> Formato ideal para análise em Excel/Google Sheets</li>
        </ul>
    </div>
</div>

<script>
    function exportarPdf() {
        const form = document.getElementById('formRelatorio');
        const originalAction = form.action;
        form.action = '{{ route("relatorio.exportar-pdf") }}';
        form.submit();
        form.action = originalAction;
    }


    function exportarCsv() {
        const form = document.getElementById('formRelatorio');
        const originalAction = form.action;
        form.action = '{{ route("relatorio.exportar-csv") }}';
        form.submit();
        form.action = originalAction;
    }
</script>
@endsection
