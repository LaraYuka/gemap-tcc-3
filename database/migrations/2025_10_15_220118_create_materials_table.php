<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->text('descricao');
            $table->enum('categoria', [
                'Brinquedo',
                'Livro',
                'Artes',
                'Sensorial',
                'Fantasia',
                'Outro'
            ]);
            $table->integer('quantidade_total');
            $table->integer('quantidade_disponivel'); // ADICIONADO
            $table->enum('estado_conservacao', ['novo', 'bom', 'gasto', 'faltando', 'destruido']); // CORRIGIDO
            $table->string('local_guardado');
            $table->integer('idade_recomendada');
            $table->json('fotos')->nullable(); // CORRIGIDO: era string, agora é JSON
            $table->enum('status', ['disponível', 'emprestado'])->default('disponível');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
