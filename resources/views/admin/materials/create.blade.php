@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Adicionar Novo Material</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.materials.store') }}" enctype="multipart/form-data">
                        @csrf

                        <h5 class="mb-3">Informações Básicas</h5>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nome" class="form-label">Nome do Material *</label>
                                <input type="text" class="form-control @error('nome') is-invalid @enderror"
                                       id="nome" name="nome" value="{{ old('nome') }}" required>
                                @error('nome')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="categoria" class="form-label">Categoria *</label>
                                <select class="form-select @error('categoria') is-invalid @enderror"
                                        id="categoria" name="categoria" required>
                                    <option value="">Selecione...</option>
                                    <option value="Livro" {{ old('categoria') == 'Livro' ? 'selected' : '' }}>Livro</option>
                                    <option value="Brinquedo" {{ old('categoria') == 'Brinquedo' ? 'selected' : '' }}>Brinquedo</option>
                                    <option value="Jogo" {{ old('categoria') == 'Jogo' ? 'selected' : '' }}>Jogo</option>
                                    <option value="Material Pedagógico" {{ old('categoria') == 'Material Pedagógico' ? 'selected' : '' }}>Material Pedagógico</option>
                                </select>
                                @error('categoria')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tipo_material" class="form-label">Tipo de Material *</label>
                                <select class="form-select @error('tipo_material') is-invalid @enderror"
                                    id="tipo_material" name="tipo_material" required>
                                    <option value="">Selecione...</option>
                                    <option value="reutilizavel" {{ old('tipo_material') == 'reutilizavel' ? 'selected' : '' }}>
                                        Reutilizável (pode ser devolvido)
                                    </option>
                                    <option value="consumivel" {{ old('tipo_material') == 'consumivel' ? 'selected' : '' }}>
                                        Consumível (não retorna - ex: tinta, papel)
                                    </option>
                                </select>
                                <small class="text-muted">
                                    <strong>Reutilizável:</strong> Material que pode ser usado várias vezes<br>
                                    <strong>Consumível:</strong> Material que acaba ao ser usado
                                </small>
                                @error('tipo_material')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="origem" class="form-label">Origem do Material *</label>
                                <select class="form-select @error('origem') is-invalid @enderror"
                                    id="origem" name="origem" required>
                                    <option value="">Selecione...</option>
                                    <option value="comprado" {{ old('origem') == 'comprado' ? 'selected' : '' }}>
                                        🛒 Comprado
                                    </option>
                                    <option value="doacao" {{ old('origem') == 'doacao' ? 'selected' : '' }}>
                                        🎁 Doação
                                    </option>
                                </select>
                                <small class="text-muted">
                                    <strong>Comprado:</strong> Material adquirido pela creche<br>
                                    <strong>Doação:</strong> Material recebido de doadores
                                </small>
                                @error('origem')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="descricao" class="form-label">Descrição *</label>
                            <textarea class="form-control @error('descricao') is-invalid @enderror"
                                      id="descricao" name="descricao" rows="4" required>{{ old('descricao') }}</textarea>
                            @error('descricao')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">

                        {{-- 🆕 SEÇÃO DE MÚLTIPLAS PEÇAS --}}
                        <div class="card bg-light mb-4">
                            <div class="card-body">
                                <h5 class="mb-3">🧩 Tipo de Material</h5>

                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           id="possui_multiplas_pecas"
                                           name="possui_multiplas_pecas"
                                           value="1"
                                           {{ old('possui_multiplas_pecas') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="possui_multiplas_pecas">
                                        <strong>Este material possui múltiplas peças</strong>
                                        <br>
                                        <small class="text-muted">
                                            Ex: Lego, quebra-cabeça, jogo de memória, conjunto de blocos
                                        </small>
                                    </label>
                                </div>

                                {{-- Campos condicionais para conjuntos --}}
                                <div id="campos_multiplas_pecas" style="display: none;">
                                    <div class="alert alert-info">
                                        <strong>ℹ️ Material Composto:</strong> Utilize esta opção para brinquedos que possuem várias peças.
                                        O sistema rastreará peças perdidas individualmente.
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="identificacao_conjunto" class="form-label">Identificação do Conjunto *</label>
                                            <input type="text"
                                                   class="form-control @error('identificacao_conjunto') is-invalid @enderror"
                                                   id="identificacao_conjunto"
                                                   name="identificacao_conjunto"
                                                   value="{{ old('identificacao_conjunto') }}"
                                                   placeholder="Ex: Lego Azul Sala 1, Quebra-cabeça Animais">
                                            <small class="text-muted">Como identificar este conjunto/caixa</small>
                                            @error('identificacao_conjunto')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-3 mb-3">
                                            <label for="quantidade_pecas_total" class="form-label">Total de Peças *</label>
                                            <input type="number"
                                                   class="form-control @error('quantidade_pecas_total') is-invalid @enderror"
                                                   id="quantidade_pecas_total"
                                                   name="quantidade_pecas_total"
                                                   value="{{ old('quantidade_pecas_total') }}"
                                                   min="1"
                                                   placeholder="500">
                                            <small class="text-muted">Qtd. de peças no conjunto</small>
                                            @error('quantidade_pecas_total')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-3 mb-3">
                                            <label for="percentual_minimo_utilizavel" class="form-label">% Mínimo Utilizável</label>
                                            <input type="number"
                                                   class="form-control @error('percentual_minimo_utilizavel') is-invalid @enderror"
                                                   id="percentual_minimo_utilizavel"
                                                   name="percentual_minimo_utilizavel"
                                                   value="{{ old('percentual_minimo_utilizavel', 70) }}"
                                                   min="1"
                                                   max="100">
                                            <small class="text-muted">Padrão: 70%</small>
                                            @error('percentual_minimo_utilizavel')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="alert alert-warning mb-0">
                                        <strong>⚠️ Percentual Mínimo:</strong> Define quando o conjunto será marcado como "Indisponível".
                                        Ex: 70% = se perder mais de 30% das peças, não poderá mais ser emprestado.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h5 class="mb-3">Quantidade e Conservação</h5>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="quantidade_total" class="form-label">Quantidade Total *</label>
                                <input type="number" class="form-control @error('quantidade_total') is-invalid @enderror"
                                       id="quantidade_total" name="quantidade_total" value="{{ old('quantidade_total', 1) }}" min="1" required>
                                <small class="text-muted">Quantidade de conjuntos/unidades</small>
                                @error('quantidade_total')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="estado_conservacao" class="form-label">Estado de Conservação *</label>
                                <select class="form-select @error('estado_conservacao') is-invalid @enderror"
                                        id="estado_conservacao" name="estado_conservacao" required>
                                    <option value="">Selecione...</option>
                                    <option value="novo" {{ old('estado_conservacao') == 'novo' ? 'selected' : '' }}>Novo</option>
                                    <option value="bom" {{ old('estado_conservacao', 'bom') == 'bom' ? 'selected' : '' }}>Bom</option>
                                    <option value="gasto" {{ old('estado_conservacao') == 'gasto' ? 'selected' : '' }}>Gasto</option>
                                    <option value="faltando" {{ old('estado_conservacao') == 'faltando' ? 'selected' : '' }}>Faltando peças</option>
                                    <option value="destruido" {{ old('estado_conservacao') == 'destruido' ? 'selected' : '' }}>Destruído</option>
                                </select>
                                @error('estado_conservacao')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="idade_recomendada" class="form-label">Idade Recomendada *</label>
                                <select class="form-select @error('idade_recomendada') is-invalid @enderror"
                                        id="idade_recomendada" name="idade_recomendada" required>
                                    <option value="">Selecione...</option>
                                    @for($i = 0; $i <= 5; $i++)
                                        <option value="{{ $i }}" {{ old('idade_recomendada') == $i ? 'selected' : '' }}>{{ $i }} anos</option>
                                    @endfor
                                </select>
                                @error('idade_recomendada')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="local_guardado" class="form-label">Local Guardado *</label>
                            <input type="text" class="form-control @error('local_guardado') is-invalid @enderror"
                                   id="local_guardado" name="local_guardado" value="{{ old('local_guardado') }}"
                                   placeholder="Ex: Sala 2, Estante A, Prateleira 3" required>
                            @error('local_guardado')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">
                        <h5 class="mb-3">Fotos</h5>

                        <div class="mb-3">
                            <label for="fotos" class="form-label">Fotos do Material (máximo 10)</label>
                            <input type="file" class="form-control @error('fotos.*') is-invalid @enderror"
                                   id="fotos" name="fotos[]" multiple accept="image/*">
                            <small class="text-muted">A primeira foto será a foto de capa</small>
                            @error('fotos.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('admin.materials.index') }}" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-success">Salvar e Publicar</button>
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
    const inputIdentificacao = document.getElementById('identificacao_conjunto');
    const inputQtdPecas = document.getElementById('quantidade_pecas_total');

    function toggleCamposPecas() {
        if (checkboxPecas.checked) {
            camposPecas.style.display = 'block';
            inputIdentificacao.required = true;
            inputQtdPecas.required = true;
        } else {
            camposPecas.style.display = 'none';
            inputIdentificacao.required = false;
            inputQtdPecas.required = false;
        }
    }

    checkboxPecas.addEventListener('change', toggleCamposPecas);

    // Executar na inicialização para manter estado após erro de validação
    toggleCamposPecas();
});
</script>
@endsection
