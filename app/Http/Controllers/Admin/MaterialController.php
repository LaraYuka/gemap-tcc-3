<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

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

        if ($request->filled('origem')) {
            $query->where('origem', $request->origem);
        }

        $materials = $query->orderBy('created_at', 'desc')->paginate(12);

        return view('admin.materials.index', compact('materials'));
    }

    public function create()
    {
        return view('admin.materials.create');
    }

    public function store(Request $request)
    {
        $rules = [
            'nome' => 'required|string|max:255',
            'descricao' => 'required|string',
            'categoria' => 'required|in:Brinquedo,Livro,Jogo,Material Pedagógico',
            'tipo_material' => 'required|in:reutilizavel,consumivel',
            'origem' => 'required|in:comprado,doacao',
            'quantidade_total' => 'required|integer|min:1',
            'estado_conservacao' => 'required|in:novo,bom,gasto,faltando,destruido',
            'local_guardado' => 'required|string|max:255',
            'idade_recomendada' => 'required|integer|min:0',
            'fotos.*' => 'nullable|image|max:2048',

            // 🆕 NOVOS CAMPOS
            'possui_multiplas_pecas' => 'nullable|boolean',
            'quantidade_pecas_total' => 'nullable|required_if:possui_multiplas_pecas,1|integer|min:1',
            'percentual_minimo_utilizavel' => 'nullable|integer|min:1|max:100',
            'identificacao_conjunto' => 'nullable|required_if:possui_multiplas_pecas,1|string|max:255',
        ];

        $request->validate($rules);

        $fotosArray = [];

        if ($request->hasFile('fotos')) {
            foreach ($request->file('fotos') as $foto) {
                $path = $foto->store('materials', 'public');
                $fotosArray[] = $path;
            }
        }

        $data = [
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'categoria' => $request->categoria,
            'tipo_material' => $request->tipo_material,
            'origem' => $request->origem,
            'quantidade_total_comprada' => $request->quantidade_total,
            'quantidade_disponivel' => $request->quantidade_total,
            'quantidade_em_uso' => 0,
            'quantidade_perdida' => 0,
            'estado_conservacao' => $request->estado_conservacao,
            'local_guardado' => $request->local_guardado,
            'idade_recomendada' => $request->idade_recomendada,
            'fotos' => $fotosArray,
        ];

        // 🆕 Adiciona campos de múltiplas peças se aplicável
        if ($request->has('possui_multiplas_pecas') && $request->possui_multiplas_pecas) {
            $data['possui_multiplas_pecas'] = true;
            $data['quantidade_pecas_total'] = $request->quantidade_pecas_total;
            $data['quantidade_pecas_atual'] = $request->quantidade_pecas_total; // Começa completo
            $data['percentual_minimo_utilizavel'] = $request->percentual_minimo_utilizavel ?? 70;
            $data['identificacao_conjunto'] = $request->identificacao_conjunto;
        } else {
            $data['possui_multiplas_pecas'] = false;
        }

        Material::create($data);

        return redirect()->route('admin.materials.index')
            ->with('success', 'Material cadastrado com sucesso!');
    }

    public function show(Material $material)
    {
        $material->load('doacao');
        return view('admin.materials.show', compact('material'));
    }

    public function edit(Material $material)
    {
        return view('admin.materials.edit', compact('material'));
    }

    public function update(Request $request, Material $material)
    {
        try {
            $rules = [
                'nome' => 'required|string|max:255',
                'descricao' => 'required|string',
                'categoria' => 'required|in:Brinquedo,Livro,Jogo,Material Pedagógico',
                'tipo_material' => 'required|in:reutilizavel,consumivel',
                'origem' => 'required|in:comprado,doacao',
                'quantidade_disponivel' => 'required|integer|min:0',
                'quantidade_adicional' => 'nullable|integer|min:0',
                'estado_conservacao' => 'required|in:novo,bom,gasto,faltando,destruido',
                'local_guardado' => 'required|string|max:255',
                'idade_recomendada' => 'required|integer|min:0',
                'fotos.*' => 'nullable|image|max:2048',

                // 🆕 NOVOS CAMPOS
                'possui_multiplas_pecas' => 'nullable|boolean',
                'quantidade_pecas_total' => 'nullable|required_if:possui_multiplas_pecas,1|integer|min:1',
                'quantidade_pecas_atual' => 'nullable|integer|min:0',
                'percentual_minimo_utilizavel' => 'nullable|integer|min:1|max:100',
                'identificacao_conjunto' => 'nullable|string|max:255',
            ];

            $validated = $request->validate($rules);

            $fotosArray = $material->fotos ?? [];

            if ($request->hasFile('fotos')) {
                foreach ($request->file('fotos') as $foto) {
                    $path = $foto->store('materials', 'public');
                    $fotosArray[] = $path;
                }
            }

            // Se adicionou mais unidades (nova compra)
            if ($request->filled('quantidade_adicional') && $request->quantidade_adicional > 0) {
                $material->quantidade_total_comprada += $request->quantidade_adicional;
                $material->quantidade_disponivel += $request->quantidade_adicional;
            } else {
                $material->quantidade_disponivel = $validated['quantidade_disponivel'];
            }

            $updateData = [
                'nome' => $validated['nome'],
                'descricao' => $validated['descricao'],
                'categoria' => $validated['categoria'],
                'tipo_material' => $validated['tipo_material'],
                'origem' => $validated['origem'],
                'quantidade_disponivel' => $material->quantidade_disponivel,
                'quantidade_total_comprada' => $material->quantidade_total_comprada,
                'estado_conservacao' => $validated['estado_conservacao'],
                'local_guardado' => $validated['local_guardado'],
                'idade_recomendada' => $validated['idade_recomendada'],
                'fotos' => $fotosArray,
            ];

            // 🆕 Atualiza campos de múltiplas peças
            if ($request->has('possui_multiplas_pecas') && $request->possui_multiplas_pecas) {
                $updateData['possui_multiplas_pecas'] = true;
                $updateData['quantidade_pecas_total'] = $validated['quantidade_pecas_total'];
                $updateData['quantidade_pecas_atual'] = $request->quantidade_pecas_atual ?? $material->quantidade_pecas_atual;
                $updateData['percentual_minimo_utilizavel'] = $request->percentual_minimo_utilizavel ?? 70;
                $updateData['identificacao_conjunto'] = $request->identificacao_conjunto;
            } else {
                $updateData['possui_multiplas_pecas'] = false;
                $updateData['quantidade_pecas_total'] = null;
                $updateData['quantidade_pecas_atual'] = null;
                $updateData['percentual_minimo_utilizavel'] = 70;
                $updateData['identificacao_conjunto'] = null;
            }

            $material->update($updateData);

            return redirect()->route('admin.materials.index')
                ->with('success', 'Material atualizado com sucesso!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Erro de validação. Verifique os campos.');
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar material: ' . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', 'Erro ao atualizar material: ' . $e->getMessage());
        }
    }

    public function destroy(Material $material)
    {
        $agendamentosAtivos = $material->agendamentos()
            ->whereIn('status', ['pendente', 'aprovado', 'em_uso'])
            ->count();

        if ($agendamentosAtivos > 0) {
            return back()->with('error', "Não é possível excluir! Este material tem {$agendamentosAtivos} agendamento(s) ativo(s).");
        }

        if ($material->fotos) {
            foreach ($material->fotos as $foto) {
                Storage::disk('public')->delete($foto);
            }
        }

        $material->delete();

        return redirect()->route('admin.materials.index')
            ->with('success', 'Material deletado com sucesso!');
    }

    public function removerFoto(Material $material, $index)
    {
        $fotos = $material->fotos ?? [];

        if (isset($fotos[$index])) {
            Storage::disk('public')->delete($fotos[$index]);
            unset($fotos[$index]);
            $material->update(['fotos' => array_values($fotos)]);
        }

        return back()->with('success', 'Foto removida com sucesso!');
    }
}
