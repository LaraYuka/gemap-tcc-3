@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Agendar Material: {{ $material->nome }}</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <div class="row">
                            <div class="col-md-3">
                                @if($material->fotos && count($material->fotos) > 0)
                                    <img src="{{ asset('storage/' . $material->fotos[0]) }}" class="img-fluid rounded" alt="{{ $material->nome }}">
                                @endif
                            </div>
                            <div class="col-md-9">
                                <h5>{{ $material->nome }}</h5>
                                <p class="mb-1"><strong>Categoria:</strong> {{ $material->categoria }}</p>
                                <p class="mb-1"><strong>Tipo:</strong> {!! $material->getTipoMaterialBadge() !!}</p>
                                <p class="mb-1"><strong>Quantidade Disponível:</strong> {{ $material->quantidade_disponivel }}</p>
                                <p class="mb-0"><strong>Local:</strong> {{ $material->local_guardado }}</p>
                            </div>
                        </div>
                    </div>

                    @if($material->isConsumivel())
                        <div class="alert alert-warning">
                            <strong>⚠️ Material Consumível</strong><br>
                            Este material <strong>não retorna</strong> após o uso. A quantidade será <strong>deduzida permanentemente</strong> do estoque quando você confirmar o agendamento.
                        </div>
                    @endif

                    <form method="POST" action="{{ route('professor.agendamentos.store') }}" id="formAgendamento">
                        @csrf
                        <input type="hidden" name="material_id" value="{{ $material->id }}">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="data_retirada" class="form-label">Data de Retirada *</label>
                                <input type="date"
                                       class="form-control @error('data_retirada') is-invalid @enderror"
                                       id="data_retirada"
                                       name="data_retirada"
                                       value="{{ old('data_retirada') }}"
                                       min="{{ date('Y-m-d') }}"
                                       required
                                       onchange="verificarDisponibilidade()">
                                @error('data_retirada')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="horario_retirada" class="form-label">Horário de Retirada *</label>
                                <select class="form-select @error('horario_retirada') is-invalid @enderror"
                                        id="horario_retirada"
                                        name="horario_retirada"
                                        required
                                        onchange="verificarDisponibilidade()">
                                    <option value="">Selecione...</option>
                                    <option value="7h30-9h30">7h30 às 9h30</option>
                                    <option value="9h30-11h30">9h30 às 11h30</option>
                                    <option value="13h10-15h10">13h10 às 15h10</option>
                                    <option value="15h10-17h10">15h10 às 17h10</option>
                                </select>
                                @error('horario_retirada')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        @if($material->isReutilizavel())
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="data_devolucao_prevista" class="form-label">Data de Devolução *</label>
                                    <input type="date" class="form-control @error('data_devolucao_prevista') is-invalid @enderror" id="data_devolucao_prevista" name="data_devolucao_prevista" value="{{ old('data_devolucao_prevista') }}" min="{{ date('Y-m-d') }}" required onchange="verificarDisponibilidade()">
                                    @error('data_devolucao_prevista')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="horario_devolucao" class="form-label">Horário de Devolução *</label>
                                    <select class="form-select @error('horario_devolucao') is-invalid @enderror"
                                            id="horario_devolucao"
                                            name="horario_devolucao"
                                            required
                                            onchange="verificarDisponibilidade()">
                                        <option value="">Selecione...</option>
                                        <option value="7h30-9h30">7h30 às 9h30</option>
                                        <option value="9h30-11h30">9h30 às 11h30</option>
                                        <option value="13h10-15h10">13h10 às 15h10</option>
                                        <option value="15h10-17h10">15h10 às 17h10</option>
                                    </select>
                                    @error('horario_devolucao')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label for="quantidade" class="form-label">Quantidade *</label>
                            <input type="number"
                                   class="form-control @error('quantidade') is-invalid @enderror"
                                   id="quantidade"
                                   name="quantidade"
                                   value="{{ old('quantidade', 1) }}"
                                   min="1"
                                   max="{{ $material->quantidade_disponivel }}"
                                   required
                                   onchange="verificarDisponibilidade()">
                            <small class="text-muted">
                                @if($material->isConsumivel())
                                    Disponível no estoque: {{ $material->quantidade_disponivel }}
                                @else
                                    Quantidade total: {{ $material->quantidade_total }}
                                @endif
                            </small>
                            @error('quantidade')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div id="disponibilidadeArea" class="alert" style="display: none;"></div>

                        <div class="mb-3">
                            <label for="observacoes" class="form-label">Observações (Opcional)</label>
                            <textarea class="form-control @error('observacoes') is-invalid @enderror" id="observacoes" name="observacoes" rows="3" placeholder="Adicione informações adicionais...">{{ old('observacoes') }}</textarea>
                            @error('observacoes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('professor.materials.show', $material) }}" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-success" id="btnAgendar">
                                @if($material->isConsumivel())
                                    Confirmar Retirada
                                @else
                                    Confirmar Agendamento
                                @endif
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const materialConsumivel = {{ $material->isConsumivel() ? 'true' : 'false' }};

function verificarDisponibilidade() {
    const dataRetirada = document.getElementById('data_retirada').value;
    const horarioRetirada = document.getElementById('horario_retirada').value;
    const quantidade = document.getElementById('quantidade').value;

    if (!dataRetirada || !horarioRetirada || !quantidade) {
        return;
    }

    if (materialConsumivel) {
        const quantidadeDisponivel = {{ $material->quantidade_disponivel }};
        const area = document.getElementById('disponibilidadeArea');
        const btn = document.getElementById('btnAgendar');

        area.style.display = 'block';

        if (quantidadeDisponivel >= quantidade) {
            area.className = 'alert alert-success';
            area.innerHTML = `<strong>✓ Disponível!</strong> ${quantidadeDisponivel} unidade(s) em estoque.`;
            btn.disabled = false;
        } else {
            area.className = 'alert alert-danger';
            area.innerHTML = `<strong>✗ Estoque insuficiente!</strong> Apenas ${quantidadeDisponivel} unidade(s) disponível(is).`;
            btn.disabled = true;
        }
        return;
    }

    const dataDevolucao = document.getElementById('data_devolucao_prevista').value;
    const horarioDevolucao = document.getElementById('horario_devolucao').value;

    if (!dataDevolucao || !horarioDevolucao) {
        return;
    }

    fetch('{{ route("professor.agendamentos.verificar-disponibilidade") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            material_id: {{ $material->id }},
            data_retirada: dataRetirada,
            horario_retirada: horarioRetirada,
            data_devolucao_prevista: dataDevolucao,
            horario_devolucao: horarioDevolucao,
            quantidade: quantidade
        })
    })
    .then(response => response.json())
    .then(data => {
        const area = document.getElementById('disponibilidadeArea');
        const btn = document.getElementById('btnAgendar');

        area.style.display = 'block';

        if (data.disponivel) {
            area.className = 'alert alert-success';
            area.innerHTML = `<strong>✓ Disponível!</strong> ${data.quantidade_disponivel} unidade(s) disponível(is) no período selecionado.`;
            btn.disabled = false;
        } else {
            area.className = 'alert alert-danger';
            area.innerHTML = `<strong>✗ Indisponível!</strong> ${data.mensagem}`;
            btn.disabled = true;
        }
    });
}
</script>
@endsection
