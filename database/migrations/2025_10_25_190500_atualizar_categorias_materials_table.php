<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{

    public function up(): void
    {

        DB::statement("ALTER TABLE materials MODIFY COLUMN categoria ENUM('Livro', 'Brinquedo', 'Jogo', 'Material Pedagógico') NOT NULL");


        if (!Schema::hasColumn('materials', 'tipo_material')) {
            Schema::table('materials', function (Blueprint $table) {
                $table->enum('tipo_material', ['reutilizavel', 'consumivel'])
                    ->default('reutilizavel')
                    ->after('categoria');
            });
        }
    }

    public function down(): void
    {

        DB::statement("ALTER TABLE materials MODIFY COLUMN categoria ENUM('Brinquedo', 'Livro', 'Artes', 'Sensorial', 'Fantasia', 'Outro') NOT NULL");

        if (Schema::hasColumn('materials', 'tipo_material')) {
            Schema::table('materials', function (Blueprint $table) {
                $table->dropColumn('tipo_material');
            });
        }
    }
};
