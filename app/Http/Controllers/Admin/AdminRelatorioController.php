<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Relatorio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminRelatorioController extends Controller
{
    /**
     * Exibe o histórico de relatórios com filtros e paginação.
     */
    public function historico(Request $request)
    {
        $query = Relatorio::with('user')->orderByDesc('created_at');

        // 🔎 Filtro de busca por nome ou e-mail
        if ($request->filled('busca')) {
            $busca = $request->busca;
            $query->whereHas('user', function ($q) use ($busca) {
                $q->where('name', 'like', "%{$busca}%")
                    ->orWhere('email', 'like', "%{$busca}%");
            });
        }

        // 📂 Filtro por formato (PDF ou CSV)
        if ($request->filled('formato')) {
            $query->where('formato', $request->formato);
        }

        // 👥 Filtro por tipo de usuário
        if ($request->filled('tipo_usuario')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('role', $request->tipo_usuario);
            });
        }

        // 📅 Filtro por data (intervalo)
        if ($request->filled('data_inicio')) {
            $query->whereDate('created_at', '>=', $request->data_inicio);
        }

        if ($request->filled('data_fim')) {
            $query->whereDate('created_at', '<=', $request->data_fim);
        }

        // 📢 Paginação
        $relatorios = $query->paginate(10)->withQueryString();

        return view('admin.relatorios.historico', compact('relatorios'));
    }

    /**
     * Faz o download de um relatório gerado.
     */
    public function download($id)
    {
        $relatorio = Relatorio::findOrFail($id);

        if (!Storage::exists($relatorio->caminho_arquivo)) {
            return back()->with('error', 'O arquivo não foi encontrado ou foi removido.');
        }

        return Storage::download($relatorio->caminho_arquivo);
    }

    /**
     * Exclui um relatório específico.
     */
    public function excluir($id)
    {
        $relatorio = Relatorio::findOrFail($id);

        // Se o arquivo existir, apaga do disco
        if (Storage::exists($relatorio->caminho_arquivo)) {
            Storage::delete($relatorio->caminho_arquivo);
        }

        $relatorio->delete();

        return back()->with('success', 'Relatório excluído com sucesso.');
    }
}
