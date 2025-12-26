<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doacao extends Model
{
    use HasFactory;

    protected $table = 'doacoes';

    protected $fillable = [
        'user_id',
        'nome_doador',
        'telefone',
        'email',
        'tipo_doacao',
        'descricao',
        'quantidade',
        'estado_conservacao',
        'fotos',
        'data_doacao',
        'status',
        'observacao_admin',
        'data_resposta',
    ];

    protected $casts = [
        'fotos' => 'array',
        'data_doacao' => 'date',
        'data_resposta' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isPendente(): bool
    {
        return $this->status === 'pendente';
    }

    public function isAprovado(): bool
    {
        return $this->status === 'aprovado';
    }

    public function getStatusBadge(): string
    {
        $badges = [
            'pendente' => '<span class="badge bg-warning">Pendente</span>',
            'aprovado' => '<span class="badge bg-success">Aprovado</span>',
            'recusado' => '<span class="badge bg-danger">Recusado</span>',
            'recebido' => '<span class="badge bg-info">Recebido</span>',
        ];

        return $badges[$this->status] ?? '';
    }

    public function getStatusColor(): string
    {
        return match ($this->status) {
            'pendente' => 'warning',
            'aprovado' => 'success',
            'recusado' => 'danger',
            'recebido' => 'info',
            default => 'secondary',
        };
    }

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            'pendente' => 'Pendente',
            'aprovado' => 'Aprovado',
            'recusado' => 'Recusado',
            'recebido' => 'Recebido',
            default => 'Desconhecido',
        };
    }
}
