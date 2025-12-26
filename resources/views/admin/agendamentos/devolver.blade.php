@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0">📦 Registrar Devolução</h4>
                </div>
                <div class="card-body">

                    {{-- Informações do Agendamento --}}
                    <div class="alert alert-info">
                        <h5>Informações do Empréstimo:</h5>
                        <ul class="mb-0">
                            <li><strong>Professor:</strong> {{ $agendamento->user->name }}</li>
                            <li><strong>Material:</strong> {{ $agendamento->material->nome }}</li>
                            <li><strong>Quantidade Emprestada:</strong> {{ $agendamento->quantidade }}
                                @if($agendamento->material->possui_multiplas_pecas)
                                    conjunto(s)
                                @else
                                    unidade(s)
                                @endif
                            </li>
                            <li><strong>Tipo:</strong>
                                @if($agendamento->material->isReutilizavel())
                                    <span class="badge bg-info">Reutilizável</span>
                                @else
                                    <span class="badge bg-warning">Consumível</span>
                                @endif
                                @if($agendamento->material->possui_multiplas_pecas)
                                    <span class="badge bg-primary">🧩 Conjunto com Peças</span>
                                @endif
                            </li>
                        </ul>
                    </div>

                    {{-- 🆕 Informações do Conjunto (se aplicável) --}}
                    @if($agendamento->material->possui_multiplas_pecas)
                        <div class="alert alert-warning">
                            <h6 class="alert-heading">🧩 Material Composto por Peças</h6>
                            <p class="mb-1">
                                <strong>Identificação:</strong> {{ $agendamento->material->identificacao_conjunto }}
                            </p>
                            <p class="mb-1">
                                <strong>Peças por conjunto:</strong> {{ $agendamento->material->quantidade_pecas_total }} peças
                            </p>
                            <p class="mb-0">
                                <strong>Total emprestado:</strong>
                                {{ $agendamento->quantidade }} conjunto(s) =
                                {{ $agendamento->quantidade * $agendamento->material->quantidade_pecas_total }} peças no total
                            </p>
                        </div>
                    @endif

                    <form action="{{ route('admin.agendamentos.processar-devolucao', $agendamento) }}" method="POST">
                        @csrf

                        {{-- Quantidade Devolvida --}}
                        <div class="mb-4">
                            <label class="form-label">
                                <strong>✅ Quantidade Devolvida:</strong>
                            </label>
                            <input type="number"
                                   name="quantidade_devolvida"
                                   class="form-control form-control-lg @error('quantidade_devolvida') is-invalid @enderror"
                                   min="0"
                                   max="{{ $agendamento->quantidade }}"
                                   value="{{ old('quantidade_devolvida', $agendamento->quantidade) }}"
                                   id="qtd_devolvida"
                                   required>
                            <small class="form-text text-muted">
                                @if($agendamento->material->possui_multiplas_pecas)
                                    Conjuntos que voltaram (podem estar incompletos)
                                @elseif($agendamento->material->isReutilizavel())
                                    Unidades que voltaram em bom estado e retornarão ao estoque
                                @else
                                    Material consumível - não retorna ao estoque
                                @endif
                            </small>
                            @error('quantidade_devolvida')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Quantidade Perdida --}}
                        <div class="mb-4">
                            <label class="form-label">
                                <strong>❌ Quantidade Perdida/Quebrada:</strong>
                            </label>
                            <input type="number"
                                   name="quantidade_perdida"
                                   class="form-control form-control-lg @error('quantidade_perdida') is-invalid @enderror"
                                   min="0"
                                   max="{{ $agendamento->quantidade }}"
                                   value="{{ old('quantidade_perdida', 0) }}"
                                   id="qtd_perdida"
                                   required>
                            <small class="form-text text-muted">
                                @if($agendamento->material->possui_multiplas_pecas)
                                    Conjuntos que foram totalmente perdidos ou não retornaram
                                @else
                                    Unidades que foram perdidas, quebradas ou não retornaram
                                @endif
                            </small>
                            @error('quantidade_perdida')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- 🆕 Campo especial para perda de peças (conjuntos) --}}
                        @if($agendamento->material->possui_multiplas_pecas)
                            <div class="mb-4">
                                <label class="form-label">
                                    <strong>🧩 Peças Perdidas dos Conjuntos Devolvidos:</strong>
                                </label>
                                <input type="number"
                                       name="pecas_perdidas"
                                       class="form-control form-control-lg @error('pecas_perdidas') is-invalid @enderror"
                                       min="0"
                                       value="{{ old('pecas_perdidas', 0) }}"
                                       id="pecas_perdidas">
                                <small class="form-text text-muted">
                                    Quantas peças foram perdidas dos conjuntos que estão sendo devolvidos?
                                    (Ex: se voltaram 2 conjuntos mas faltam 5 peças no total, digite 5)
                                </small>
                                @error('pecas_perdidas')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif

                        {{-- Validação visual --}}
                        <div class="alert alert-secondary" id="validacao">
                            <strong>Validação:</strong>
                            <span id="soma_texto">Devolvida + Perdida = ?</span>
                        </div>

                        {{-- Observações --}}
                        <div class="mb-4">
                            <label class="form-label">
                                <strong>📝 Observações (opcional):</strong>
                            </label>
                            <textarea name="observacao_devolucao"
                                      class="form-control"
                                      rows="3"
                                      placeholder="Ex: Material voltou com pequenos desgastes, perdeu 2 peças pequenas, etc.">{{ old('observacao_devolucao') }}</textarea>
                        </div>

                        {{-- Exemplos de uso --}}
                        <div class="card bg-light mb-4">
                            <div class="card-body">
                                <h6 class="card-title">💡 Exemplos:</h6>
                                <ul class="mb-0 small">
                                    @if($agendamento->material->possui_multiplas_pecas)
                                        <li><strong>Tudo OK:</strong> Devolvida: {{ $agendamento->quantidade }} | Perdida: 0 | Peças perdidas: 0</li>
                                        @if($agendamento->quantidade >= 2)
                                            <li><strong>Perdeu 1 conjunto completo:</strong> Devolvida: {{ $agendamento->quantidade - 1 }} | Perdida: 1 | Peças perdidas: 0</li>
                                            <li><strong>Voltaram todos mas faltam peças:</strong> Devolvida: {{ $agendamento->quantidade }} | Perdida: 0 | Peças perdidas: 10</li>
                                        @endif
                                    @else
                                        <li><strong>Tudo OK:</strong> Devolvida: {{ $agendamento->quantidade }} | Perdida: 0</li>
                                        @if($agendamento->quantidade >= 2)
                                            <li><strong>Perdeu 2:</strong> Devolvida: {{ $agendamento->quantidade - 2 }} | Perdida: 2</li>
                                        @endif
                                        @if($agendamento->material->isConsumivel())
                                            <li><strong>Consumível usado:</strong> Devolvida: {{ $agendamento->quantidade }} | Perdida: 0 (não volta ao estoque)</li>
                                        @endif
                                    @endif
                                </ul>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.agendamentos.index') }}" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-success btn-lg" id="btn_confirmar">
                                ✅ Confirmar Devolução
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const qtdDevolvida = document.getElementById('qtd_devolvida');
    const qtdPerdida = document.getElementById('qtd_perdida');
    const validacao = document.getElementById('validacao');
    const somaTexto = document.getElementById('soma_texto');
    const btnConfirmar = document.getElementById('btn_confirmar');
    const totalEmprestado = {{ $agendamento->quantidade }};

    function validarQuantidades() {
        const devolvida = parseInt(qtdDevolvida.value) || 0;
        const perdida = parseInt(qtdPerdida.value) || 0;
        const soma = devolvida + perdida;

        somaTexto.textContent = `Devolvida (${devolvida}) + Perdida (${perdida}) = ${soma} de ${totalEmprestado}`;

        if (soma === totalEmprestado) {
            validacao.className = 'alert alert-success';
            somaTexto.innerHTML = `✅ Devolvida (${devolvida}) + Perdida (${perdida}) = ${soma} de ${totalEmprestado} <strong>- OK!</strong>`;
            btnConfirmar.disabled = false;
        } else if (soma > totalEmprestado) {
            validacao.className = 'alert alert-danger';
            somaTexto.innerHTML = `❌ Devolvida (${devolvida}) + Perdida (${perdida}) = ${soma} de ${totalEmprestado} <strong>- Soma maior que emprestado!</strong>`;
            btnConfirmar.disabled = true;
        } else {
            validacao.className = 'alert alert-warning';
            somaTexto.innerHTML = `⚠️ Devolvida (${devolvida}) + Perdida (${perdida}) = ${soma} de ${totalEmprestado} <strong>- Faltam ${totalEmprestado - soma} unidade(s) para contabilizar!</strong>`;
            btnConfirmar.disabled = true;
        }
    }

    qtdDevolvida.addEventListener('input', validarQuantidades);
    qtdPerdida.addEventListener('input', validarQuantidades);

    // Validação inicial
    validarQuantidades();
});
</script>
@endsection
