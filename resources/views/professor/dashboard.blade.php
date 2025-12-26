@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Dashboard do Professor</h1>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Bem-vindo, {{ Auth::user()->name }}!</h5>
                </div>
                <div class="card-body">
                    <p>Bem-vindo ao GEMAP - Gerenciador de Materiais Pedagógicos. Aqui você pode:</p>
                    <ul>
                        <li>🔍 Buscar e visualizar materiais disponíveis</li>
                        <li>📅 Agendar retirada de materiais</li>
                        <li>📝 Solicitar novos materiais que não estão cadastrados</li>
                        <li>📊 Acompanhar seus agendamentos e solicitações</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card text-white bg-success mb-3">
                <div class="card-header">Meus Agendamentos</div>
                <div class="card-body">
                    <h5 class="card-title">{{ \App\Models\Agendamento::where('user_id', auth()->id())->count() }}</h5>
                    <p class="card-text">Total de agendamentos</p>
                    <a href="{{ route('professor.agendamentos.index') }}" class="btn btn-light btn-sm">Ver todos</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-white bg-warning mb-3">
                <div class="card-header">Pendentes</div>
                <div class="card-body">
                    <h5 class="card-title">{{ \App\Models\Agendamento::where('user_id', auth()->id())->where('status', 'pendente')->count() }}</h5>
                    <p class="card-text">Aguardando aprovação</p>
                    <a href="{{ route('professor.agendamentos.index') }}" class="btn btn-light btn-sm">Ver detalhes</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-white bg-info mb-3">
                <div class="card-header">Minhas Solicitações</div>
                <div class="card-body">
                    <h5 class="card-title">{{ \App\Models\Solicitacao::where('user_id', auth()->id())->count() }}</h5>
                    <p class="card-text">Total de solicitações</p>
                    <a href="{{ route('professor.solicitacoes.index') }}" class="btn btn-light btn-sm">Ver todas</a>
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
                    <a href="{{ route('professor.materials.index') }}" class="btn btn-primary me-2">
                        🔍 Buscar Materiais
                    </a>
                    <a href="{{ route('professor.solicitacoes.create') }}" class="btn btn-success me-2">
                        📝 Solicitar Material
                    </a>
                    <a href="{{ route('professor.agendamentos.index') }}" class="btn btn-info">
                        📅 Meus Agendamentos
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
