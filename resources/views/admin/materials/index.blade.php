@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Gerenciar Materiais</h1>
        <a href="{{ route('admin.materials.create') }}" class="btn btn-success">
            + Adicionar Material
        </a>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.materials.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <input type="text" name="pesquisa" class="form-control" placeholder="Pesquisar material..." value="{{ request('pesquisa') }}">
                    </div>
                    <div class="col-md-2">
                        <select name="categoria" class="form-select">
                            <option value="">Todas Categorias</option>
                            <option value="Brinquedo" {{ request('categoria') == 'Brinquedo' ? 'selected' : '' }}>Brinquedo</option>
                            <option value="Livro" {{ request('categoria') == 'Livro' ? 'selected' : '' }}>Livro</option>
                            <option value="Jogo" {{ request('categoria') == 'Jogo' ? 'selected' : '' }}>Jogo</option>
                            <option value="Material Pedagógico" {{ request('categoria') == 'Material Pedagógico' ? 'selected' : '' }}>Material Pedagógico</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="idade" class="form-select">
                            <option value="">Todas Idades</option>
                            @for($i = 0; $i <= 10; $i++)
                                <option value="{{ $i }}" {{ request('idade') == $i ? 'selected' : '' }}>{{ $i }} anos</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="disponibilidade" class="form-select">
                            <option value="">Todas</option>
                            <option value="disponivel" {{ request('disponibilidade') == 'disponivel' ? 'selected' : '' }}>Disponível</option>
                            <option value="indisponivel" {{ request('disponibilidade') == 'indisponivel' ? 'selected' : '' }}>Indisponível</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary me-2">Filtrar</button>
                        <a href="{{ route('admin.materials.index') }}" class="btn btn-secondary">Limpar</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Grid de Materiais -->
    <div class="row">
        @forelse($materials as $material)
            <div class="col-md-3 mb-4">
                <div class="card h-100">
                    @if($material->fotos && count($material->fotos) > 0)
                        <img src="{{ asset('storage/' . $material->fotos[0]) }}" class="card-img-top" alt="{{ $material->nome }}" style="height: 200px; object-fit: cover;">
                    @else
                        <div class="bg-secondary d-flex align-items-center justify-content-center" style="height: 200px;">
                            <span class="text-white">Sem foto</span>
                        </div>
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ $material->nome }}</h5>
                        <p class="card-text text-muted small">{{ Str::limit($material->descricao, 80) }}</p>

                        <div class="mb-2">
                            {{-- Status Badge --}}
                            <span class="badge bg-{{ $material->status_color }}">
                                {{ $material->status_label }}
                            </span>

                            {{-- 🆕 Badge para Conjuntos com Múltiplas Peças --}}
                            @if($material->possui_multiplas_pecas)
                                <span class="badge bg-info" title="Material com múltiplas peças">
                                    🧩 Conjunto
                                </span>
                                @php
                                    $percentual = $material->percentual_pecas_atual;
                                    $badgeClass = $percentual >= 90 ? 'success' : ($percentual >= $material->percentual_minimo_utilizavel ? 'warning' : 'danger');
                                @endphp
                                <span class="badge bg-{{ $badgeClass }}" title="Completude do conjunto">
                                    {{ $percentual }}%
                                </span>
                                <br>
                                <small class="text-muted">
                                    {{ $material->quantidade_pecas_atual }}/{{ $material->quantidade_pecas_total }} peças
                                </small>
                            @else
                                {{-- Quantidade Badge (para materiais unitários) --}}
                                @php
                                    $disponivel = $material->quantidade_disponivel ?? 0;
                                    $emUso = $material->quantidade_em_uso ?? 0;
                                @endphp

                                @if($emUso > 0 || $disponivel == 0)
                                    <span class="badge bg-info">
                                        {{ $disponivel }} disp. • {{ $emUso }} em uso
                                    </span>
                                @else
                                    <span class="badge bg-info">
                                        {{ $disponivel }} disponíveis
                                    </span>
                                @endif
                            @endif

                            {{-- Tipo Badge --}}
                            @if($material->tipo_material === 'consumivel')
                                <span class="badge bg-warning">Consumível</span>
                            @else
                                <span class="badge bg-info">Reutilizável</span>
                            @endif
                        </div>

                        <p class="small mb-1"><strong>Categoria:</strong> {{ $material->categoria }}</p>
                        <p class="small mb-2"><strong>Idade:</strong> {{ $material->idade_recomendada }} anos</p>

                        {{-- Informações adicionais se houver perdas --}}
                        @if($material->possui_multiplas_pecas)
                            @php
                                $pecasPerdidas = $material->quantidade_pecas_total - $material->quantidade_pecas_atual;
                            @endphp
                            @if($pecasPerdidas > 0)
                                <p class="small text-danger mb-2">
                                    <strong>⚠️ Peças perdidas:</strong> {{ $pecasPerdidas }}
                                </p>
                            @endif
                        @elseif(isset($material->quantidade_perdida) && $material->quantidade_perdida > 0)
                            <p class="small text-danger mb-2">
                                <strong>⚠️ Perdidas:</strong> {{ $material->quantidade_perdida }}
                            </p>
                        @endif
                    </div>
                    <div class="card-footer bg-white">
                        <a href="{{ route('admin.materials.show', $material) }}" class="btn btn-sm btn-info">Ver</a>
                        <a href="{{ route('admin.materials.edit', $material) }}" class="btn btn-sm btn-primary">Editar</a>
                        <form action="{{ route('admin.materials.destroy', $material) }}" method="POST" class="d-inline" onsubmit="return confirm('Deseja realmente excluir este material?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">Nenhum material encontrado.</div>
            </div>
        @endforelse
    </div>

    <!-- Paginação -->
    <div class="mt-4">
        {{ $materials->links() }}
    </div>
</div>
@endsection
