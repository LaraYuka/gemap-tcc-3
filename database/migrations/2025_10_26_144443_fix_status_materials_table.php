<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->string('status_new')->nullable()->after('fotos');
        });

        DB::table('materials')->get()->each(function ($row) {
            $new = null;
            $old = $row->status;

            if (is_null($old)) {
                $new = 'INDISPONIVEL';
            } else {
                $oldLower = mb_strtolower($old);
                if ($oldLower === 'disponível' || $oldLower === 'disponivel' || $oldLower === 'disponible' || $oldLower === 'disponivel') {
                    $new = 'DISPONIVEL';
                } elseif ($oldLower === 'emprestado' || $oldLower === 'em uso' || $oldLower === 'em_uso') {
                    $new = 'EM_USO';
                } elseif ($row->quantidade_disponivel == 0) {
                    $new = 'INDISPONIVEL';
                } else {
                    $new = ($row->quantidade_disponivel > 0) ? 'DISPONIVEL' : 'INDISPONIVEL';
                }
            }

            DB::table('materials')->where('id', $row->id)->update(['status_new' => $new]);
        });

        Schema::table('materials', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('materials', function (Blueprint $table) {
            $table->string('status')->default('DISPONIVEL')->after('fotos');
        });

        DB::table('materials')->get()->each(function ($row) {
            DB::table('materials')->where('id', $row->id)->update(['status' => $row->status_new]);
        });

        Schema::table('materials', function (Blueprint $table) {
            $table->dropColumn('status_new');
        });
    }

    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->string('status_old')->nullable();
        });
    }
};
