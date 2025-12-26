<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Solicitacao extends Model
{
    use HasFactory;

    protected $table = 'solicitacoes';

    protected $fillable = [
        'user_id',
        'nome_material',
        'descricao',
        'data_solicitacao',
        'data_necessaria',
        'status',
        'foto',
    ];

    protected $casts = [
        'data_necessaria' => 'date',
        'data_solicitacao' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusBadge(): string
    {
        $badges = [
            'em_processo' => '<span class="badge bg-warning">Em Processo</span>',
            'aceito' => '<span class="badge bg-success">Aceito</span>',
            'recusado' => '<span class="badge bg-danger">Recusado</span>',
        ];

        return $badges[$this->status] ?? '';
    }
}
