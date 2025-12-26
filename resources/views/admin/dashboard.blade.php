@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Dashboard do Administrador</h1>

    <div class="row">
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3">
                <div class="card-header">Total de Usuários</div>
                <div class="card-body">
                    <h5 class="card-title">{{ $stats['total_usuarios'] }}</h5>
                    <p class="card-text">Usuários cadastrados</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-success mb-3">
                <div class="card-header">Professores</div>
                <div class="card-body">
                    <h5 class="card-title">{{ $stats['total_professores'] }}</h5>
                    <p class="card-text">Professores ativos</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-info mb-3">
                <div class="card-header">Materiais</div>
                <div class="card-body">
                    <h5 class="card-title">{{ $stats['total_materiais'] }}</h5>
                    <p class="card-text">Total cadastrados</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-warning mb-3">
                <div class="card-header">Disponíveis</div>
                <div class="card-body">
                    <h5 class="card-title">{{ $stats['materiais_disponiveis'] }}</h5>
                    <p class="card-text">Materiais disponíveis</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card border-warning mb-3">
                <div class="card-header bg-warning text-white">
                    <strong>Agendamentos Pendentes</strong>
                </div>
                <div class="card-body">
                    <h3 class="card-title">{{ $stats['agendamentos_pendentes'] }}</h3>
                    <p class="card-text">Agendamentos aguardando aprovação</p>
                    <a href="{{ route('admin.agendamentos.index') }}" class="btn btn-warning">Ver Agendamentos</a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-danger mb-3">
                <div class="card-header bg-danger text-white">
                    <strong>Solicitações Pendentes</strong>
                </div>
                <div class="card-body">
                    <h3 class="card-title">{{ $stats['solicitacoes_pendentes'] }}</h3>
                    <p class="card-text">Solicitações aguardando resposta</p>
                    <a href="{{ route('admin.solicitacoes.index') }}" class="btn btn-danger">Ver Solicitações</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Ações Rápidas</h5>
                </div>
                <div class="card-body">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-primary me-2">
                        Gerenciar Usuários
                    </a>
                    <a href="{{ route('admin.users.create') }}" class="btn btn-success me-2">
                        Criar Novo Usuário
                    </a>
                    <a href="{{ route('admin.materials.index') }}" class="btn btn-info me-2">
                        Gerenciar Materiais
                    </a>
                    <a href="{{ route('admin.materials.create') }}" class="btn btn-warning">
                        Adicionar Material
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
