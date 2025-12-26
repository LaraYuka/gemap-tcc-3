<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Material;
use App\Models\Agendamento;
use App\Models\Solicitacao;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_usuarios' => User::count(),
            'total_professores' => User::where('role', 'professor')->count(),
            'total_visitantes' => User::where('role', 'visitante')->count(),
            'total_materiais' => Material::count(),
            'materiais_disponiveis' => Material::where('quantidade_disponivel', '>', 0)->count(),
            'agendamentos_pendentes' => Agendamento::where('status', 'pendente')->count(),
            'solicitacoes_pendentes' => Solicitacao::where('status', 'em_processo')->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
