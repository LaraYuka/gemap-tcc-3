<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->enum('origem', ['comprado', 'doacao'])->default('comprado')->after('tipo_material');
            $table->foreignId('doacao_id')->nullable()->constrained('doacoes')->nullOnDelete()->after('origem');
        });
    }

    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->dropForeign(['doacao_id']);
            $table->dropColumn(['origem', 'doacao_id']);
        });
    }
};
