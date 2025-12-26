<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Atualiza categorias antigas para as novas
        DB::statement("UPDATE materials SET categoria = 'Jogo' WHERE categoria = 'Artes'");
        DB::statement("UPDATE materials SET categoria = 'Material Pedagógico' WHERE categoria = 'Sensorial'");
        DB::statement("UPDATE materials SET categoria = 'Material Pedagógico' WHERE categoria = 'Fantasia'");
        DB::statement("UPDATE materials SET categoria = 'Material Pedagógico' WHERE categoria = 'Outro'");

        // Altera o ENUM
        DB::statement("ALTER TABLE materials MODIFY COLUMN categoria ENUM('Livro', 'Brinquedo', 'Jogo', 'Material Pedagógico') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE materials MODIFY COLUMN categoria ENUM('Brinquedo', 'Livro', 'Artes', 'Sensorial', 'Fantasia', 'Outro') NOT NULL");
    }
};
