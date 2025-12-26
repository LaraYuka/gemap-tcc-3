@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Editar Material</h4>
                </div>
                <div class="card-body">

                    {{-- 📊 ESTATÍSTICAS DO MATERIAL --}}
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $material->quantidade_total_comprada }}</h3>
                                    <small>Total Comprado</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $material->quantidade_disponivel }}</h3>
                                    <small>Disponível</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-dark">
                                <div class="card-body text-center">
                                    <h3>{{ $material->quantidade_em_uso }}</h3>
                                    <small>Em Uso</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $material->quantidade_perdida }}</h3>
                                    <small>Perdidas ({{ $material->percentual_perda }}%)</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 🆕 ESTATÍSTICAS DE PEÇAS (se aplicável) --}}
                    @if($material->possui_multiplas_pecas)
                        <div class="alert alert-info mb-4">
                            <h5 class="alert-heading">🧩 Informações do Conjunto</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Identificação:</strong> {{ $material->identificacao_conjunto }}<br>
                                    <strong>Total de Peças:</strong> {{ $material->quantidade_pecas_total }}<br>
                                    <strong>Peças Atuais:</strong> {{ $material->quantidade_pecas_atual }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Percentual Atual:</strong> {{ $material->percentual_pecas_atual }}%<br>
                                    <strong>Mínimo Utilizável:</strong> {{ $material->percentual_minimo_utilizavel }}%<br>
                                    {!! $material->getStatusConjuntoBadge() !!}
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- 📌 ALERTA DE STATUS ATUAL --}}
                    <div class="alert alert-{{ $material->status_color }} mb-4">
                        <strong>Status Atual:</strong> {{ $material->status_label }}
                        <p class="mb-0 small">{{ $material->status_descricao }}</p>
                    </div>

                    {{-- 📸 SEÇÃO DE FOTOS EXISTENTES --}}
                    @if($material->fotos && count($material->fotos) > 0)
                        <div class="mb-4">
                            <h5>Fotos Atuais</h5>
                            <div class="row">
                                @foreach($material->fotos as $index => $foto)
                                    <div class="col-md-3 mb-3">
                                        <div class="card shadow-sm">
                                            <img src="{{ asset('storage/' . $foto) }}" class="card-img-top rounded" alt="Foto do material">
                                            <div class="card-body p-2">
                                                <form action="{{ route('admin.materials.remover-foto', [$material->id, $index]) }}"
                                                      method="POST"
                                                      onsubmit="return confirm('Remover esta foto?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger w-100">Remover</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- 📝 FORMULÁRIO PRINCIPAL --}}
                    <form method="POST" action="{{ url('/admin/materials/' . $material->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <strong>Nome:</strong>
                            <input type="text" class="form-control" name="nome" value="{{ $material->nome }}" required>
                        </div>

                        <div class="mb-3">
                            <strong>Descrição:</strong>
                            <textarea class="form-control" name="descricao" rows="2" required>{{ $material->descricao }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <strong>Categoria:</strong>
                                <select class="form-select" name="categoria" required>
                                    <option value="Brinquedo" {{ $material->categoria == 'Brinquedo' ? 'selected' : '' }}>Brinquedo</option>
                                    <option value="Livro" {{ $material->categoria == 'Livro' ? 'selected' : '' }}>Livro</option>
                                    <option value="Jogo" {{ $material->categoria == 'Jogo' ? 'selected' : '' }}>Jogo</option>
                                    <option value="Material Pedagógico" {{ $material->categoria == 'Material Pedagógico' ? 'selected' : '' }}>Material Pedagógico</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <strong>Tipo:</strong>
                                <select class="form-select" name="tipo_material" required>
                                    <option value="reutilizavel" {{ $material->tipo_material == 'reutilizavel' ? 'selected' : '' }}>Reutilizável</option>
                                    <option value="consumivel" {{ $material->tipo_material == 'consumivel' ? 'selected' : '' }}>Consumível</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <strong>Origem:</strong>
                                <select class="form-select" name="origem" required>
                                    <option value="comprado" {{ $material->origem == 'comprado' ? 'selected' : '' }}>🛒 Comprado</option>
                                    <option value="doacao" {{ $material->origem == 'doacao' ? 'selected' : '' }}>🎁 Doação</option>
                                </select>
                                @if($material->isDoacacao() && $material->doacao)
                                    <small class="text-muted d-block mt-1">
                                        <i class="bi bi-gift"></i> Este material veio da doação de
                                        <strong>{{ $material->doacao->nome_doador }}</strong>
                                        <a href="{{ route('admin.doacoes.show', $material->doacao) }}" target="_blank" class="text-decoration-none">
                                            (ver doação)
                                        </a>
                                    </small>
                                @endif
                            </div>
                        </div>

                        <hr class="my-4">

                        {{-- 🆕 SEÇÃO DE MÚLTIPLAS PEÇAS --}}
                        <div class="card bg-light mb-4">
                            <div class="card-body">
                                <h5 class="mb-3">🧩 Configuração de Peças</h5>

                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           id="possui_multiplas_pecas"
                                           name="possui_multiplas_pecas"
                                           value="1"
                                           {{ $material->possui_multiplas_pecas ? 'checked' : '' }}>
                                    <label class="form-check-label" for="possui_multiplas_pecas">
                                        <strong>Este material possui múltiplas peças</strong>
                                    </label>
                                </div>

                                <div id="campos_multiplas_pecas" style="display: {{ $material->possui_multiplas_pecas ? 'block' : 'none' }};">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="identificacao_conjunto" class="form-label">Identificação do Conjunto</label>
                                            <input type="text"
                                                   class="form-control"
                                                   id="identificacao_conjunto"
                                                   name="identificacao_conjunto"
                                                   value="{{ $material->identificacao_conjunto }}"
                                                   placeholder="Ex: Lego Azul Sala 1">
                                        </div>

                                        <div class="col-md-3 mb-3">
                                            <label for="quantidade_pecas_total" class="form-label">Total de Peças</label>
                                            <input type="number"
                                                   class="form-control"
                                                   id="quantidade_pecas_total"
                                                   name="quantidade_pecas_total"
                                                   value="{{ $material->quantidade_pecas_total }}"
                                                   min="1">
                                        </div>

                                        <div class="col-md-3 mb-3">
                                            <label for="quantidade_pecas_atual" class="form-label">Peças Atuais</label>
                                            <input type="number"
                                                   class="form-control"
                                                   id="quantidade_pecas_atual"
                                                   name="quantidade_pecas_atual"
                                                   value="{{ $material->quantidade_pecas_atual }}"
                                                   min="0">
                                            <small class="text-muted">Ajuste manual de peças</small>
                                        </div>

                                        <div class="col-md-12 mb-3">
                                            <label for="percentual_minimo_utilizavel" class="form-label">% Mínimo Utilizável</label>
                                            <input type="number"
                                                   class="form-control"
                                                   id="percentual_minimo_utilizavel"
                                                   name="percentual_minimo_utilizavel"
                                                   value="{{ $material->percentual_minimo_utilizavel ?? 70 }}"
                                                   min="1"
                                                   max="100">
                                            <small class="text-muted">
                                                Abaixo deste percentual, o conjunto será marcado como indisponível
                                            </small>
                                        </div>
                                    </div>

                                    @if($material->possui_multiplas_pecas)
                                        <div class="alert alert-warning mb-0">
                                            <strong>⚠️ Atenção:</strong>
                                            Peças perdidas: <strong>{{ $material->quantidade_pecas_total - $material->quantidade_pecas_atual }}</strong>
                                            ({{ number_format($material->percentual_perda, 1) }}%)
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- 📦 GESTÃO DE QUANTIDADE --}}
                        <div class="card bg-light mb-4">
                            <div class="card-body">
                                <h5 class="card-title mb-3">📦 Gestão de Estoque</h5>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            <strong>Quantidade Disponível:</strong>
                                        </label>
                                        <input type="number" class="form-control form-control-lg"
                                               name="quantidade_disponivel"
                                               value="{{ $material->quantidade_disponivel }}"
                                               min="0" required>
                                        <small class="text-muted">
                                            📦 Unidades que estão na brinquedoteca para emprestar agora
                                        </small>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            <strong>➕ Adicionar Nova Compra:</strong>
                                        </label>
                                        <input type="number" class="form-control form-control-lg"
                                               name="quantidade_adicional"
                                               value="0"
                                               min="0">
                                        <small class="text-muted">
                                            🛒 Se comprou mais unidades, digite aqui (será somado ao total)
                                        </small>
                                    </div>
                                </div>

                                <div class="alert alert-info mb-0">
                                    <strong>ℹ️ Como funciona:</strong>
                                    <ul class="mb-0 mt-2 small">
                                        <li><strong>Total Comprado:</strong> {{ $material->quantidade_total_comprada }} (histórico para relatórios)</li>
                                        <li><strong>Em Uso:</strong> {{ $material->quantidade_em_uso }} (emprestado no momento)</li>
                                        <li><strong>Perdidas:</strong> {{ $material->quantidade_perdida }} (registradas nas devoluções)</li>
                                        <li><strong>Disponível:</strong> O que sobra para emprestar</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <strong>Estado de Conservação:</strong>
                                <select class="form-select" name="estado_conservacao" required>
                                    <option value="novo" {{ $material->estado_conservacao == 'novo' ? 'selected' : '' }}>Novo</option>
                                    <option value="bom" {{ $material->estado_conservacao == 'bom' ? 'selected' : '' }}>Bom</option>
                                    <option value="gasto" {{ $material->estado_conservacao == 'gasto' ? 'selected' : '' }}>Gasto</option>
                                    <option value="faltando" {{ $material->estado_conservacao == 'faltando' ? 'selected' : '' }}>Faltando peças</option>
                                    <option value="destruido" {{ $material->estado_conservacao == 'destruido' ? 'selected' : '' }}>Destruído</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <strong>Idade Recomendada:</strong>
                                <select class="form-select" name="idade_recomendada" required>
                                    @for($i = 0; $i <= 10; $i++)
                                        <option value="{{ $i }}" {{ $material->idade_recomendada == $i ? 'selected' : '' }}>{{ $i }} anos</option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <strong>Local Guardado:</strong>
                            <input type="text" class="form-control" name="local_guardado" value="{{ $material->local_guardado }}" required>
                        </div>

                        {{-- 📸 UPLOAD DE NOVAS FOTOS --}}
                        <hr class="my-3">
                        <h5 class="mb-3">Adicionar Novas Fotos</h5>
                        <input type="file" class="form-control" name="fotos[]" multiple accept="image/*">
                        <small class="text-muted">A primeira foto será usada como capa.</small>

                        <hr>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.materials.index') }}" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-success btn-lg">💾 Salvar Alterações</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxPecas = document.getElementById('possui_multiplas_pecas');
    const camposPecas = document.getElementById('campos_multiplas_pecas');

    function toggleCamposPecas() {
        if (checkboxPecas.checked) {
            camposPecas.style.display = 'block';
        } else {
            camposPecas.style.display = 'none';
        }
    }

    checkboxPecas.addEventListener('change', toggleCamposPecas);
});
</script>
@endsection
