<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            // Indica se o material possui múltiplas peças
            $table->boolean('possui_multiplas_pecas')->default(false)->after('tipo_material');

            // Quantidade total de peças do conjunto (ex: Lego tem 500 peças)
            $table->integer('quantidade_pecas_total')->nullable()->after('possui_multiplas_pecas');

            // Quantidade atual de peças (pode diminuir com perdas)
            $table->integer('quantidade_pecas_atual')->nullable()->after('quantidade_pecas_total');

            // Percentual mínimo de peças para considerar o conjunto utilizável
            // Ex: 70% = se tiver 70% das peças, ainda pode ser usado
            $table->integer('percentual_minimo_utilizavel')->default(70)->after('quantidade_pecas_atual');

            // Identificação da caixa/conjunto (ex: "Caixa Azul Sala 1", "Lego Vermelho")
            $table->string('identificacao_conjunto')->nullable()->after('percentual_minimo_utilizavel');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->dropColumn([
                'possui_multiplas_pecas',
                'quantidade_pecas_total',
                'quantidade_pecas_atual',
                'percentual_minimo_utilizavel',
                'identificacao_conjunto'
            ]);
        });
    }
};
