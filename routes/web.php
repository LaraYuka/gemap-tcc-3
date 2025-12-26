<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\MaterialController as AdminMaterialController;
use App\Http\Controllers\Admin\AgendamentoController as AdminAgendamentoController;
use App\Http\Controllers\Admin\SolicitacaoController as AdminSolicitacaoController;
use App\Http\Controllers\Professor\DashboardController as ProfessorDashboard;
use App\Http\Controllers\Professor\MaterialController as ProfessorMaterialController;
use App\Http\Controllers\Professor\AgendamentoController as ProfessorAgendamentoController;
use App\Http\Controllers\Professor\SolicitacaoController as ProfessorSolicitacaoController;
use App\Http\Controllers\Visitante\DashboardController as VisitanteDashboard;
use App\Http\Controllers\Admin\AdminRelatorioController as AdminRelatorioController;
use App\Http\Controllers\RelatorioController;
use App\Http\Controllers\Admin\DoacaoController as AdminDoacaoController;
use App\Http\Controllers\Visitante\DoacaoController as VisitanteDoacaoController;

use Illuminate\Support\Facades\Route;

Route::get('/manifest.json', function () {
    return response()
        ->view('manifest')
        ->header('Content-Type', 'application/json');
})->name('manifest');


Route::get('/', function () {
    return redirect()->route('login');
});


Route::middleware('auth')->group(function () {

    // Redirecionamento após login baseado no role
    Route::get('/dashboard', function () {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isProfessor()) {
            return redirect()->route('professor.dashboard');
        } else {
            return redirect()->route('visitante.dashboard');
        }
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Rotas do Admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

    // Usuários
    Route::resource('users', UserController::class);

    // Materiais
    Route::resource('materials', AdminMaterialController::class);
    Route::delete('/materials/{material}/foto/{index}', [AdminMaterialController::class, 'removerFoto'])->name('materials.remover-foto');

    // Agendamentos
    Route::get('/agendamentos', [AdminAgendamentoController::class, 'index'])->name('agendamentos.index');
    Route::get('/agendamentos/{agendamento}', [AdminAgendamentoController::class, 'show'])->name('agendamentos.show');
    Route::post('/agendamentos/{agendamento}/aprovar', [AdminAgendamentoController::class, 'aprovar'])->name('agendamentos.aprovar');
    Route::post('/agendamentos/{agendamento}/recusar', [AdminAgendamentoController::class, 'recusar'])->name('agendamentos.recusar');
    Route::post('/agendamentos/{agendamento}/retirar', [AdminAgendamentoController::class, 'retirar'])->name('agendamentos.retirar');

    // Rotas de devolução
    Route::get('/agendamentos/{agendamento}/devolver', [AdminAgendamentoController::class, 'formDevolucao'])->name('agendamentos.form-devolucao');
    Route::post('/agendamentos/{agendamento}/processar-devolucao', [AdminAgendamentoController::class, 'devolver'])->name('agendamentos.processar-devolucao');

    // Solicitações
    Route::get('/solicitacoes', [AdminSolicitacaoController::class, 'index'])->name('solicitacoes.index');
    Route::get('/solicitacoes/{solicitacao}', [AdminSolicitacaoController::class, 'show'])->name('solicitacoes.show');
    Route::post('/solicitacoes/{solicitacao}/aceitar', [AdminSolicitacaoController::class, 'aceitar'])->name('solicitacoes.aceitar');
    Route::post('/solicitacoes/{solicitacao}/recusar', [AdminSolicitacaoController::class, 'recusar'])->name('solicitacoes.recusar');

    // Doações (Admin)
    Route::prefix('doacoes')->name('doacoes.')->group(function () {
        Route::get('/', [AdminDoacaoController::class, 'index'])->name('index');
        Route::get('/{doacao}', [AdminDoacaoController::class, 'show'])->name('show');
        Route::post('/{doacao}/aprovar', [AdminDoacaoController::class, 'aprovar'])->name('aprovar');
        Route::post('/{doacao}/recusar', [AdminDoacaoController::class, 'recusar'])->name('recusar');
        Route::post('/{doacao}/marcar-recebido', [AdminDoacaoController::class, 'marcarRecebido'])->name('marcar-recebido');
        Route::post('/{doacao}/converter-material', [AdminDoacaoController::class, 'converterEmMaterial'])->name('converter-material');
    });

    // ROTAS DE RELATÓRIOS (Admin)
    Route::prefix('relatorios')->name('relatorios.')->group(function () {
        Route::get('/historico', [AdminRelatorioController::class, 'historico'])->name('historico');
        Route::get('/{id}/download', [AdminRelatorioController::class, 'download'])->name('download');
        Route::delete('/{id}', [AdminRelatorioController::class, 'excluir'])->name('excluir');
    });
});

// Rotas do Professor
Route::middleware(['auth', 'role:admin,professor'])->prefix('professor')->name('professor.')->group(function () {
    Route::get('/dashboard', [ProfessorDashboard::class, 'index'])->name('dashboard');

    // Materiais (visualização)
    Route::get('/materials', [ProfessorMaterialController::class, 'index'])->name('materials.index');
    Route::get('/materials/{material}', [ProfessorMaterialController::class, 'show'])->name('materials.show');

    // Agendamentos
    Route::get('/agendamentos', [ProfessorAgendamentoController::class, 'index'])->name('agendamentos.index');
    Route::get('/agendamentos/create/{material}', [ProfessorAgendamentoController::class, 'create'])->name('agendamentos.create');
    Route::post('/agendamentos', [ProfessorAgendamentoController::class, 'store'])->name('agendamentos.store');
    Route::get('/agendamentos/{agendamento}', [ProfessorAgendamentoController::class, 'show'])->name('agendamentos.show');
    Route::delete('/agendamentos/{agendamento}', [ProfessorAgendamentoController::class, 'destroy'])->name('agendamentos.destroy');
    Route::post('/agendamentos/verificar-disponibilidade', [ProfessorAgendamentoController::class, 'verificarDisponibilidade'])->name('agendamentos.verificar-disponibilidade');

    // Solicitações
    Route::prefix('solicitacoes')->name('solicitacoes.')->group(function () {
        Route::get('/', [ProfessorSolicitacaoController::class, 'index'])->name('index');
        Route::get('/create', [ProfessorSolicitacaoController::class, 'create'])->name('create');
        Route::post('/', [ProfessorSolicitacaoController::class, 'store'])->name('store');
        Route::get('/{solicitacao}', [ProfessorSolicitacaoController::class, 'show'])->name('show');
    });
});

// Rotas do Visitante
Route::middleware(['auth', 'role:admin,professor,visitante'])->prefix('visitante')->name('visitante.')->group(function () {

    Route::get('/dashboard', [VisitanteDashboard::class, 'index'])->name('dashboard');

    // Doações
    Route::get('/doacoes', [VisitanteDoacaoController::class, 'index'])->name('doacoes.index');
    Route::get('/doacoes/create/', [VisitanteDoacaoController::class, 'create'])->name('doacoes.create');
    Route::get('/doacoes/{doacao}', [VisitanteDoacaoController::class, 'show'])->name('doacoes.show');
    Route::post('/doacoes', [VisitanteDoacaoController::class, 'store'])->name('doacoes.store');
});

// Rotas de Relatórios (disponível para todos os usuários autenticados)
Route::prefix('relatorio')->name('relatorio.')->middleware(['auth'])->group(function () {
    Route::get('/', [RelatorioController::class, 'index'])->name('index');
    Route::post('/gerar', [RelatorioController::class, 'gerar'])->name('gerar');
    Route::post('/exportar-pdf', [RelatorioController::class, 'exportarPdf'])->name('exportar-pdf');
    Route::post('/exportar-csv', [RelatorioController::class, 'exportarCsv'])->name('exportar-csv');
});

require __DIR__ . '/auth.php';
