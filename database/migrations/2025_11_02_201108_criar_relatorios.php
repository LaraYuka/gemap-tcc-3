<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('relatorios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('tipo', ['materiais', 'agendamentos', 'completo', 'analise-avancada']);
            $table->date('data_inicio')->nullable();
            $table->date('data_fim')->nullable();
            $table->string('categoria')->nullable();
            $table->string('status')->nullable();
            $table->string('origem')->nullable();
            $table->enum('formato', ['pdf', 'csv']);
            $table->string('caminho_arquivo')->nullable();
            $table->timestamp('data_geracao');
            $table->timestamps();

            $table->index('user_id');
            $table->index('tipo');
            $table->index('data_geracao');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('relatorios');
    }
};
