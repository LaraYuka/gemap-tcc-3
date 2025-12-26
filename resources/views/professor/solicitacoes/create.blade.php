@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Solicitar Novo Material</h4>
                </div>
                <div class="card-body">
                    <p class="text-muted">Use este formulário para solicitar materiais que não estão cadastrados no sistema ou que você precisa que sejam adquiridos.</p>

                    <form method="POST" action="{{ route('professor.solicitacoes.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="nome_material" class="form-label">Nome do Material *</label>
                            <input type="text"
                                   class="form-control @error('nome_material') is-invalid @enderror"
                                   id="nome_material"
                                   name="nome_material"
                                   value="{{ old('nome_material') }}"
                                   placeholder="Ex: Livro Infantil - A Pequena Sereia"
                                   required>
                            @error('nome_material')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="descricao" class="form-label">Descrição / Motivo da Solicitação *</label>
                            <textarea class="form-control @error('descricao') is-invalid @enderror"
                                      id="descricao"
                                      name="descricao"
                                      rows="5"
                                      placeholder="Descreva o material que você precisa e o motivo da solicitação. Quanto mais detalhes, melhor!"
                                      required>{{ old('descricao') }}</textarea>
                            @error('descricao')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="data_necessaria" class="form-label">Data que Você Precisa *</label>
                            <input type="date"
                                   class="form-control @error('data_necessaria') is-invalid @enderror"
                                   id="data_necessaria"
                                   name="data_necessaria"
                                   value="{{ old('data_necessaria') }}"
                                   min="{{ date('Y-m-d') }}"
                                   required>
                            @error('data_necessaria')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="foto" class="form-label">Foto do Material (Opcional)</label>
                            <input type="file"
                                   class="form-control @error('foto') is-invalid @enderror"
                                   id="foto"
                                   name="foto"
                                   accept="image/*">
                            <small class="text-muted">Se você tiver uma foto ou referência do material, anexe aqui</small>
                            @error('foto')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info">
                            <strong>ℹ️ Informação:</strong> Sua solicitação será analisada pelo administrador. Você receberá uma resposta em breve sobre a disponibilidade ou aquisição do material.
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('professor.solicitacoes.index') }}" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-success">Enviar Solicitação</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
