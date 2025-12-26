@extends('layouts.app')

@section('content')
<div class="container">
    <div class="mb-3">
        <a href="{{ route('admin.materials.index') }}" class="btn btn-secondary">← Voltar</a>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Fotos do Material</h5>
                </div>
                <div class="card-body">
                    @if($material->fotos && count($material->fotos) > 0)
                        <div id="carouselFotos" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                @foreach($material->fotos as $index => $foto)
                                    <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                        <img src="{{ asset('storage/' . $foto) }}" class="d-block w-100" alt="Foto {{ $index + 1 }}" style="height: 400px; object-fit: contain;">
                                    </div>
                                @endforeach
                            </div>
                            @if(count($material->fotos) > 1)
                                <button class="carousel-control-prev" type="button" data-bs-target="#carouselFotos" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#carouselFotos" data-bs-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                </button>
                            @endif
                        </div>
                    @else
                        <div class="bg-secondary d-flex align-items-center justify-content-center" style="height: 400px;">
                            <span class="text-white fs-4">Sem fotos</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">{{ $material->nome }}</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        @if($material->status == 'DISPONIVEL')
                            <span class="badge bg-success fs-6">Disponível</span>
                        @elseif($material->status == 'EM_USO')
                            <span class="badge bg-warning fs-6">Em uso</span>
                        @else
                            <span class="badge bg-danger fs-6">Indisponível</span>
                        @endif

                        {!! $material->getOrigemBadge() !!}
                        {!! $material->getTipoMaterialBadge() !!}
                    </div>

                    @if($material->isDoacacao() && $material->doacao)
                        <div class="alert alert-info">
                            <strong><i class="bi bi-gift"></i> Material de Doação</strong><br>
                            <small>
                                Doado por: {{ $material->doacao->nome_doador }}<br>
                                Data: {{ $material->doacao->data_doacao->format('d/m/Y') }}
                                <a href="{{ route('admin.doacoes.show', $material->doacao) }}" class="alert-link">Ver detalhes da doação</a>
                            </small>
                        </div>
                    @endif

                    {{-- 🆕 INFORMAÇÕES DE CONJUNTO COM MÚLTIPLAS PEÇAS --}}
                    @if($material->possui_multiplas_pecas)
                        <div class="alert alert-warning">
                            <h6 class="alert-heading">🧩 Material Composto</h6>
                            <div class="row">
                                <div class="col-6">
                                    <strong>Identificação:</strong><br>
                                    {{ $material->identificacao_conjunto }}
                                </div>
                                <div class="col-6">
                                    {!! $material->getStatusConjuntoBadge() !!}
                                </div>
                            </div>
                            <hr>
                            <div class="row mt-2">
                                <div class="col-4 text-center">
                                    <strong>Total de Peças</strong><br>
                                    <span class="fs-4 text-primary">{{ $material->quantidade_pecas_total }}</span>
                                </div>
                                <div class="col-4 text-center">
                                    <strong>Peças Atuais</strong><br>
                                    <span class="fs-4 text-success">{{ $material->quantidade_pecas_atual }}</span>
                                </div>
                                <div class="col-4 text-center">
                                    <strong>Peças Perdidas</strong><br>
                                    <span class="fs-4 text-danger">{{ $material->quantidade_pecas_total - $material->quantidade_pecas_atual }}</span>
                                </div>
                            </div>
                            <div class="progress mt-3" style="height: 25px;">
                                <div class="progress-bar
                                    @if($material->percentual_pecas_atual >= 90) bg-success
                                    @elseif($material->percentual_pecas_atual >= $material->percentual_minimo_utilizavel) bg-warning
                                    @else bg-danger
                                    @endif"
                                    role="progressbar"
                                    style="width: {{ $material->percentual_pecas_atual }}%;"
                                    aria-valuenow="{{ $material->percentual_pecas_atual }}"
                                    aria-valuemin="0"
                                    aria-valuemax="100">
                                    {{ $material->percentual_pecas_atual }}%
                                </div>
                            </div>
                            <small class="text-muted mt-2 d-block">
                                Mínimo utilizável: {{ $material->percentual_minimo_utilizavel }}%
                            </small>
                        </div>
                    @endif

                    <h6>Descrição</h6>
                    <p>{{ $material->descricao }}</p>

                    <hr>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <strong>Categoria:</strong><br>
                            {{ $material->categoria }}
                        </div>
                        <div class="col-6 mb-3">
                            <strong>Tipo de Material:</strong><br>
                            {!! $material->getTipoMaterialBadge() !!}
                        </div>
                        <div class="col-6 mb-3">
                            <strong>Origem:</strong><br>
                            {!! $material->getOrigemBadge() !!}
                        </div>
                        <div class="col-6 mb-3">
                            <strong>Idade Recomendada:</strong><br>
                            {{ $material->idade_recomendada }} anos
                        </div>

                        {{-- Informações de Quantidade --}}
                        @if(!$material->possui_multiplas_pecas)
                            <div class="col-6 mb-3">
                                <strong>Quantidade Total Comprada:</strong><br>
                                {{ $material->quantidade_total_comprada }}
                            </div>
                            <div class="col-6 mb-3">
                                <strong>Quantidade Disponível:</strong><br>
                                <span class="badge bg-success fs-6">{{ $material->quantidade_disponivel }}</span>
                            </div>
                            <div class="col-6 mb-3">
                                <strong>Quantidade Em Uso:</strong><br>
                                <span class="badge bg-warning fs-6">{{ $material->quantidade_em_uso }}</span>
                            </div>
                            <div class="col-6 mb-3">
                                <strong>Quantidade Perdida:</strong><br>
                                <span class="badge bg-danger fs-6">{{ $material->quantidade_perdida }}</span>
                            </div>
                        @else
                            <div class="col-6 mb-3">
                                <strong>Conjuntos Disponíveis:</strong><br>
                                <span class="badge bg-success fs-6">{{ $material->quantidade_disponivel }}</span>
                            </div>
                            <div class="col-6 mb-3">
                                <strong>Conjuntos Em Uso:</strong><br>
                                <span class="badge bg-warning fs-6">{{ $material->quantidade_em_uso }}</span>
                            </div>
                        @endif

                        <div class="col-6 mb-3">
                            <strong>Estado de Conservação:</strong><br>
                            @if($material->estado_conservacao == 'novo')
                                <span class="badge bg-success">Novo</span>
                            @elseif($material->estado_conservacao == 'bom')
                                <span class="badge bg-primary">Bom</span>
                            @elseif($material->estado_conservacao == 'gasto')
                                <span class="badge bg-warning">Gasto</span>
                            @else
                                <span class="badge bg-danger">Destruído</span>
                            @endif
                        </div>
                        <div class="col-6 mb-3">
                            <strong>Local Guardado:</strong><br>
                            📍 {{ $material->local_guardado }}
                        </div>
                    </div>

                    <hr>

                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.materials.edit', $material) }}" class="btn btn-primary">
                            Editar Material
                        </a>
                        <form action="{{ route('admin.materials.destroy', $material) }}" method="POST" onsubmit="return confirm('Deseja realmente excluir este material?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">Excluir Material</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
