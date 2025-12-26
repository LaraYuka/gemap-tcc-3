<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE agendamentos MODIFY COLUMN data_devolucao_prevista DATE NULL');
        DB::statement("ALTER TABLE agendamentos MODIFY COLUMN horario_devolucao ENUM('7h30-9h30', '9h30-11h30', '13h10-15h10', '15h10-17h10') NULL");
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE agendamentos MODIFY COLUMN data_devolucao_prevista DATE NOT NULL');
        DB::statement("ALTER TABLE agendamentos MODIFY COLUMN horario_devolucao ENUM('7h30-9h30', '9h30-11h30', '13h10-15h10', '15h10-17h10') NOT NULL");
    }
};
