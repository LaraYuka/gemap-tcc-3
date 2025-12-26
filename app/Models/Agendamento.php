<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agendamento extends Model
{
    use HasFactory;

    protected $fillable = [
        'material_id',
        'user_id',
        'data_retirada',
        'horario_retirada',
        'data_devolucao_prevista',
        'horario_devolucao',
        'data_devolucao',
        'quantidade',
        'status',
        'observacoes',
        'quantidade_devolvida',
        'quantidade_perdida',
        'observacao_devolucao',
    ];

    protected $casts = [
        'data_retirada' => 'date',
        'data_devolucao_prevista' => 'date',
        'data_devolucao' => 'date',
    ];

    public static function verificarDisponibilidade($materialId, $dataRetirada, $horarioRetirada, $dataDevolucao, $horarioDevolucao, $quantidade, $agendamentoIdExcluir = null)
    {
        $query = self::where('material_id', $materialId)
            ->where('status', '!=', 'recusado')
            ->where('status', '!=', 'devolvido')
            ->where(function ($q) use ($dataRetirada, $dataDevolucao, $horarioRetirada, $horarioDevolucao) {
                $q->where(function ($query) use ($dataRetirada, $dataDevolucao) {
                    $query->whereBetween('data_retirada', [$dataRetirada, $dataDevolucao])
                        ->orWhereBetween('data_devolucao_prevista', [$dataRetirada, $dataDevolucao])
                        ->orWhere(function ($q) use ($dataRetirada, $dataDevolucao) {
                            $q->where('data_retirada', '<=', $dataRetirada)
                                ->where('data_devolucao_prevista', '>=', $dataDevolucao);
                        });
                });
            });

        if ($agendamentoIdExcluir) {
            $query->where('id', '!=', $agendamentoIdExcluir);
        }

        $quantidadeReservada = $query->sum('quantidade');

        return $quantidadeReservada;
    }

    public function getHorarioRetiradaFormatadoAttribute(): string
    {
        return $this->horario_retirada ?? '-';
    }

    public function getHorarioDevolucaoFormatadoAttribute(): string
    {
        return $this->horario_devolucao ?? '-';
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isPendente(): bool
    {
        return $this->status === 'pendente';
    }

    public function getStatusBadge(): string
    {
        $badges = [
            'pendente' => '<span class="badge bg-warning">Pendente</span>',
            'aprovado' => '<span class="badge bg-success">Aprovado</span>',
            'recusado' => '<span class="badge bg-danger">Recusado</span>',
            'em_uso' => '<span class="badge bg-info">Em Uso</span>',
            'devolvido' => '<span class="badge bg-secondary">Devolvido</span>',
        ];

        return $badges[$this->status] ?? '';
    }
}
