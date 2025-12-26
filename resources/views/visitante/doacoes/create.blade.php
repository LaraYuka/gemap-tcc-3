@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>🎁 Nova Doação</h1>
        <a href="{{ route('visitante.doacoes.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('visitante.doacoes.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nome_doador" class="form-label">Nome do Doador *</label>
                        <input type="text"
                               class="form-control @error('nome_doador') is-invalid @enderror"
                               id="nome_doador"
                               name="nome_doador"
                               value="{{ old('nome_doador', auth()->user()->name) }}"
                               required>
                        @error('nome_doador')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="telefone" class="form-label">Telefone</label>
                        <input type="text"
                               class="form-control @error('telefone') is-invalid @enderror"
                               id="telefone"
                               name="telefone"
                               value="{{ old('telefone') }}"
                               placeholder="(00) 00000-0000">
                        @error('telefone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">E-mail</label>
                        <input type="email"
                               class="form-control @error('email') is-invalid @enderror"
                               id="email"
                               name="email"
                               value="{{ old('email', auth()->user()->email) }}">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="data_doacao" class="form-label">Data da Doação *</label>
                        <input type="date"
                               class="form-control @error('data_doacao') is-invalid @enderror"
                               id="data_doacao"
                               name="data_doacao"
                               value="{{ old('data_doacao', now()->format('Y-m-d')) }}"
                               required>
                        @error('data_doacao')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="tipo_doacao" class="form-label">Tipo de Doação *</label>
                        <select class="form-select @error('tipo_doacao') is-invalid @enderror"
                                id="tipo_doacao"
                                name="tipo_doacao"
                                required>
                            <option value="">Selecione...</option>
                            <option value="Brinquedo" {{ old('tipo_doacao') == 'Brinquedo' ? 'selected' : '' }}>Brinquedo</option>
                            <option value="Livro" {{ old('tipo_doacao') == 'Livro' ? 'selected' : '' }}>Livro</option>
                            <option value="Roupa" {{ old('tipo_doacao') == 'Roupa' ? 'selected' : '' }}>Roupa</option>
                            <option value="Material Pedagógico" {{ old('tipo_doacao') == 'Material Pedagógico' ? 'selected' : '' }}>Material Pedagógico</option>
                            <option value="Alimento" {{ old('tipo_doacao') == 'Alimento' ? 'selected' : '' }}>Alimento</option>
                            <option value="Outro" {{ old('tipo_doacao') == 'Outro' ? 'selected' : '' }}>Outro</option>
                        </select>
                        @error('tipo_doacao')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="quantidade" class="form-label">Quantidade *</label>
                        <input type="number"
                               class="form-control @error('quantidade') is-invalid @enderror"
                               id="quantidade"
                               name="quantidade"
                               value="{{ old('quantidade', 1) }}"
                               min="1"
                               required>
                        @error('quantidade')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="estado_conservacao" class="form-label">Estado de Conservação *</label>
                        <select class="form-select @error('estado_conservacao') is-invalid @enderror"
                                id="estado_conservacao"
                                name="estado_conservacao"
                                required>
                            <option value="">Selecione...</option>
                            <option value="novo" {{ old('estado_conservacao') == 'novo' ? 'selected' : '' }}>Novo</option>
                            <option value="bom" {{ old('estado_conservacao') == 'bom' ? 'selected' : '' }}>Bom</option>
                            <option value="usado" {{ old('estado_conservacao') == 'usado' ? 'selected' : '' }}>Usado</option>
                        </select>
                        @error('estado_conservacao')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="descricao" class="form-label">Descrição *</label>
                    <textarea class="form-control @error('descricao') is-invalid @enderror"
                              id="descricao"
                              name="descricao"
                              rows="4"
                              required
                              placeholder="Descreva detalhadamente o que deseja doar (marca, tamanho, cor, idade recomendada, etc.)">{{ old('descricao') }}</textarea>
                    @error('descricao')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="fotos" class="form-label">Fotos (opcional)</label>
                    <input type="file"
                           class="form-control @error('fotos.*') is-invalid @enderror"
                           id="fotos"
                           name="fotos[]"
                           multiple
                           accept="image/*">
                    <small class="text-muted">Você pode enviar várias fotos (máx. 2MB cada)</small>
                    @error('fotos.*')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i>
                    <strong>Importante:</strong> Todas as doações passam por análise do administrador.
                    Você receberá uma resposta em breve sobre a aceitação da sua doação.
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('visitante.doacoes.index') }}" class="btn btn-secondary">
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Registrar Doação
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
