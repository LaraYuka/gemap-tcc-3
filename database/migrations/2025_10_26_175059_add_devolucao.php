<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agendamentos', function (Blueprint $table) {
            $table->integer('quantidade_devolvida')->default(0)->after('quantidade');
            $table->integer('quantidade_perdida')->default(0)->after('quantidade_devolvida');
            $table->text('observacao_devolucao')->nullable()->after('quantidade_perdida');
        });
    }

    public function down(): void
    {
        Schema::table('agendamentos', function (Blueprint $table) {
            $table->dropColumn(['quantidade_devolvida', 'quantidade_perdida', 'observacao_devolucao']);
        });
    }
};
