<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agendamentos', function (Blueprint $table) {
            $table->enum('horario_retirada', ['7h30-9h30', '9h30-11h30', '13h10-15h10', '15h10-17h10'])->after('data_retirada');
            $table->date('data_devolucao_prevista')->after('horario_retirada');
            $table->enum('horario_devolucao', ['7h30-9h30', '9h30-11h30', '13h10-15h10', '15h10-17h10'])->after('data_devolucao_prevista');
        });
    }

    public function down(): void
    {
        Schema::table('agendamentos', function (Blueprint $table) {
            $table->dropColumn(['horario_retirada', 'data_devolucao_prevista', 'horario_devolucao']);
        });
    }
};
