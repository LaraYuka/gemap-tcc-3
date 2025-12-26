<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use App\Models\Material;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function index(Request $request)
    {
        $query = Material::query();

        if ($request->filled('pesquisa')) {
            $query->where('nome', 'like', '%' . $request->pesquisa . '%');
        }

        if ($request->filled('categoria')) {
            $query->where('categoria', $request->categoria);
        }

        if ($request->filled('idade')) {
            $query->where('idade_recomendada', $request->idade);
        }

        if ($request->filled('disponibilidade')) {
            if ($request->disponibilidade === 'disponivel') {
                $query->where('quantidade_disponivel', '>', 0);
            } elseif ($request->disponibilidade === 'indisponivel') {
                $query->where('quantidade_disponivel', 0);
            }
        }

        if ($request->filled('quantidade')) {
            $query->where('quantidade_disponivel', '>=', $request->quantidade);
        }

        $materials = $query->orderBy('created_at', 'desc')->paginate(12);

        return view('professor.materials.index', compact('materials'));
    }

    public function show(Material $material)
    {
        $material->load('agendamentos');
        return view('professor.materials.show', compact('material'));
    }
}
