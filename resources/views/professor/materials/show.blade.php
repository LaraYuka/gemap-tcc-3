@extends('layouts.app')

@section('content')
<div class="container">
    <div class="mb-3">
        <a href="{{ route('professor.materials.index') }}" class="btn btn-secondary">← Voltar para Busca</a>
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
                        <span class="badge bg-{{ $material->status_color }} fs-6">
                            {{ $material->status_label }}
                        </span>

                        @php
                            $disponivel = $material->quantidade_disponivel ?? 0;
                            $emUso = $material->quantidade_em_uso ?? 0;
                        @endphp

                        @if($emUso > 0)
                            <span class="badge bg-info fs-6">
                                {{ $disponivel }} disp. • {{ $emUso }} em uso
                            </span>
                        @else
                            <span class="badge bg-info fs-6">
                                {{ $disponivel }} disponíveis
                            </span>
                        @endif

                        @if($material->tipo_material === 'consumivel')
                            <span class="badge bg-warning fs-6">Consumível</span>
                        @else
                            <span class="badge bg-info fs-6">Reutilizável</span>
                        @endif
                    </div>

                    <h6>Descrição</h6>
                    <p>{{ $material->descricao }}</p>

                    <hr>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <strong>Categoria:</strong><br>
                            {{ $material->categoria }}
                        </div>
                        <div class="col-6 mb-3">
                            <strong>Idade Recomendada:</strong><br>
                            {{ $material->idade_recomendada }} anos
                        </div>
                        <div class="col-6 mb-3">
                            <strong>Quantidade Disponível:</strong><br>
                            <span class="badge bg-success fs-6">{{ $material->quantidade_disponivel }}</span>
                        </div>
                        <div class="col-6 mb-3">
                            <strong>Quantidade em Uso:</strong><br>
                            <span class="badge bg-warning fs-6">{{ $material->quantidade_em_uso ?? 0 }}</span>
                        </div>
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
                            <strong>Tipo de Material:</strong><br>
                            @if($material->tipo_material === 'consumivel')
                                <span class="badge bg-warning">Consumível</span>
                            @else
                                <span class="badge bg-info">Reutilizável</span>
                            @endif
                        </div>
                        <div class="col-12 mb-3">
                            <strong>Local Guardado:</strong><br>
                            📍 {{ $material->local_guardado }}
                        </div>

                        @if(isset($material->quantidade_perdida) && $material->quantidade_perdida > 0)
                            <div class="col-12 mb-3">
                                <div class="alert alert-warning">
                                    <strong>⚠️ Atenção:</strong> {{ $material->quantidade_perdida }} unidade(s) perdida(s)
                                </div>
                            </div>
                        @endif
                    </div>

                    <hr>

                    @if($material->quantidade_disponivel > 0)
                        <div class="alert alert-success">
                            <strong>✓ Material disponível para agendamento!</strong>
                        </div>
                        <a href="{{ route('professor.agendamentos.create', $material) }}" class="btn btn-success btn-lg w-100">
                            📅 Agendar Este Material
                        </a>
                    @else
                        <div class="alert alert-danger">
                            <strong>✗ Material indisponível no momento</strong>
                            @if($emUso > 0)
                                <br><small>{{ $emUso }} unidade(s) em uso por outros professores</small>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
