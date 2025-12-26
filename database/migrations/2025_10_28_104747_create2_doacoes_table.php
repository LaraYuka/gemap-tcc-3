<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nome_doador');
            $table->string('telefone')->nullable();
            $table->string('email')->nullable();
            $table->enum('tipo_doacao', ['Brinquedo', 'Livro', 'Roupa', 'Material Pedagógico', 'Alimento', 'Outro']);
            $table->text('descricao');
            $table->integer('quantidade')->default(1);
            $table->enum('estado_conservacao', ['novo', 'bom', 'usado'])->default('bom');
            $table->json('fotos')->nullable();
            $table->date('data_doacao');
            $table->enum('status', ['pendente', 'aprovado', 'recusado', 'recebido'])->default('pendente');
            $table->text('observacao_admin')->nullable();
            $table->timestamp('data_resposta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doacoes');
    }
};
