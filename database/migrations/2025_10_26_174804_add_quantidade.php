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
            $table->integer('quantidade_total_comprada')->default(0)->after('tipo_material');

            $table->integer('quantidade_em_uso')->default(0)->after('quantidade_disponivel');

            $table->integer('quantidade_perdida')->default(0)->after('quantidade_em_uso');
        });

        DB::table('materials')->get()->each(function ($material) {
            $totalComprada = $material->quantidade_total;
            $emUso = max(0, $material->quantidade_total - $material->quantidade_disponivel);

            DB::table('materials')
                ->where('id', $material->id)
                ->update([
                    'quantidade_total_comprada' => $totalComprada,
                    'quantidade_em_uso' => $emUso,
                    'quantidade_perdida' => 0
                ]);
        });

        Schema::table('materials', function (Blueprint $table) {
            $table->dropColumn('quantidade_total');
        });
    }

    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->integer('quantidade_total')->default(0);
            $table->dropColumn(['quantidade_total_comprada', 'quantidade_em_uso', 'quantidade_perdida']);
        });
    }
};
