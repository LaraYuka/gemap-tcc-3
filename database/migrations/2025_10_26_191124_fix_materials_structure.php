<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            if (!Schema::hasColumn('materials', 'quantidade_total_comprada')) {
                $table->integer('quantidade_total_comprada')->default(0)->after('tipo_material');
            }
            if (!Schema::hasColumn('materials', 'quantidade_em_uso')) {
                $table->integer('quantidade_em_uso')->default(0)->after('quantidade_disponivel');
            }
            if (!Schema::hasColumn('materials', 'quantidade_perdida')) {
                $table->integer('quantidade_perdida')->default(0)->after('quantidade_em_uso');
            }
        });

        $materials = DB::table('materials')->get();
        foreach ($materials as $material) {
            $totalComprado = $material->quantidade_total ?? 1;
            $disponivel = $material->quantidade_disponivel ?? 0;
            $emUso = max(0, $totalComprado - $disponivel);

            DB::table('materials')
                ->where('id', $material->id)
                ->update([
                    'quantidade_total_comprada' => $totalComprado,
                    'quantidade_em_uso' => $emUso,
                    'quantidade_perdida' => 0,
                    'status' => $disponivel == 0 && $emUso > 0 ? 'EM_USO' : ($disponivel > 0 ? 'DISPONIVEL' : 'INDISPONIVEL')
                ]);
        }

        Schema::table('agendamentos', function (Blueprint $table) {
            if (!Schema::hasColumn('agendamentos', 'quantidade_devolvida')) {
                $table->integer('quantidade_devolvida')->default(0)->after('quantidade');
            }
            if (!Schema::hasColumn('agendamentos', 'quantidade_perdida')) {
                $table->integer('quantidade_perdida')->default(0)->after('quantidade_devolvida');
            }
            if (!Schema::hasColumn('agendamentos', 'observacao_devolucao')) {
                $table->text('observacao_devolucao')->nullable()->after('quantidade_perdida');
            }
        });
    }

    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->dropColumn(['quantidade_total_comprada', 'quantidade_em_uso', 'quantidade_perdida']);
        });

        Schema::table('agendamentos', function (Blueprint $table) {
            $table->dropColumn(['quantidade_devolvida', 'quantidade_perdida', 'observacao_devolucao']);
        });
    }
};
