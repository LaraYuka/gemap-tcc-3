<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    const STATUS_DISPONIVEL = 'DISPONIVEL';
    const STATUS_EM_USO = 'EM_USO';
    const STATUS_INDISPONIVEL = 'INDISPONIVEL';

    protected $table = 'materials';

    protected $fillable = [
        'nome',
        'descricao',
        'categoria',
        'tipo_material',
        'origem',
        'doacao_id',
        'quantidade_total',
        'quantidade_total_comprada',
        'quantidade_disponivel',
        'quantidade_em_uso',
        'quantidade_perdida',
        'possui_multiplas_pecas',           // NOVO
        'quantidade_pecas_total',           // NOVO
        'quantidade_pecas_atual',           // NOVO
        'percentual_minimo_utilizavel',     // NOVO
        'identificacao_conjunto',           // NOVO
        'estado_conservacao',
        'local_guardado',
        'idade_recomendada',
        'fotos',
        'status',
    ];

    protected $casts = [
        'idade_recomendada' => 'integer',
        'quantidade_total' => 'integer',
        'quantidade_total_comprada' => 'integer',
        'quantidade_disponivel' => 'integer',
        'quantidade_em_uso' => 'integer',
        'quantidade_perdida' => 'integer',
        'possui_multiplas_pecas' => 'boolean',      // NOVO
        'quantidade_pecas_total' => 'integer',      // NOVO
        'quantidade_pecas_atual' => 'integer',      // NOVO
        'percentual_minimo_utilizavel' => 'integer', // NOVO
        'fotos' => 'array',
    ];

    // 🔄 Atualiza status automaticamente
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($material) {
            $material->atualizarStatusAutomatico();
        });

        static::saved(function ($material) {
            if ($material->wasChanged(['quantidade_disponivel', 'quantidade_em_uso', 'quantidade_pecas_atual'])) {
                $material->atualizarStatusAutomatico();
                $material->saveQuietly();
            }
        });
    }

    // 📊 Quantidade total atual
    public function getQuantidadeTotalAttribute(): int
    {
        return ($this->quantidade_disponivel ?? 0) + ($this->quantidade_em_uso ?? 0);
    }

    // 🎯 Lógica automática de status
    public function atualizarStatusAutomatico(): void
    {
        // Se for conjunto com múltiplas peças, verifica o percentual
        if ($this->possui_multiplas_pecas && $this->quantidade_pecas_total > 0) {
            $percentualAtual = ($this->quantidade_pecas_atual / $this->quantidade_pecas_total) * 100;

            if ($percentualAtual < $this->percentual_minimo_utilizavel) {
                $this->status = self::STATUS_INDISPONIVEL;
                return;
            }
        }

        // Lógica padrão para unitários
        if (($this->quantidade_disponivel ?? 0) == 0 && ($this->quantidade_em_uso ?? 0) == 0) {
            $this->status = self::STATUS_INDISPONIVEL;
        } elseif (($this->quantidade_disponivel ?? 0) > 0) {
            $this->status = self::STATUS_DISPONIVEL;
        } else {
            $this->status = self::STATUS_EM_USO;
        }
    }

    // 🧩 NOVOS MÉTODOS PARA MÚLTIPLAS PEÇAS

    /**
     * Verifica se é um conjunto de peças
     */
    public function isConjunto(): bool
    {
        return $this->possui_multiplas_pecas === true;
    }

    /**
     * Retorna o percentual atual de peças
     */
    public function getPercentualPecasAtualAttribute(): float
    {
        if (!$this->possui_multiplas_pecas || !$this->quantidade_pecas_total) {
            return 100.0;
        }

        return round(($this->quantidade_pecas_atual / $this->quantidade_pecas_total) * 100, 1);
    }

    /**
     * Verifica se o conjunto está utilizável baseado no percentual mínimo
     */
    public function isConjuntoUtilizavel(): bool
    {
        if (!$this->possui_multiplas_pecas) {
            return true; // Materiais unitários são sempre utilizáveis se existirem
        }

        return $this->percentual_pecas_atual >= $this->percentual_minimo_utilizavel;
    }

    /**
     * Retorna badge do status do conjunto
     */
    public function getStatusConjuntoBadge(): string
    {
        if (!$this->possui_multiplas_pecas) {
            return '';
        }

        $percentual = $this->percentual_pecas_atual;

        if ($percentual >= 90) {
            return '<span class="badge bg-success">Completo (' . $percentual . '%)</span>';
        } elseif ($percentual >= $this->percentual_minimo_utilizavel) {
            return '<span class="badge bg-warning">Utilizável (' . $percentual . '%)</span>';
        } else {
            return '<span class="badge bg-danger">Incompleto (' . $percentual . '%)</span>';
        }
    }

    /**
     * Registra perda de peças
     */
    public function registrarPerdaPecas(int $quantidadePerdida): void
    {
        if ($this->possui_multiplas_pecas) {
            $this->quantidade_pecas_atual = max(0, $this->quantidade_pecas_atual - $quantidadePerdida);
            $this->quantidade_perdida = ($this->quantidade_perdida ?? 0) + $quantidadePerdida;
        }
    }

    // 🎁 Métodos de origem (já existentes)
    public function isDoacacao(): bool
    {
        return $this->origem === 'doacao';
    }

    public function isComprado(): bool
    {
        return $this->origem === 'comprado';
    }

    public function getOrigemBadge(): string
    {
        if ($this->isDoacacao()) {
            return '<span class="badge bg-success"><i class="bi bi-gift"></i> Doação</span>';
        }
        return '<span class="badge bg-primary"><i class="bi bi-cart"></i> Comprado</span>';
    }

    public function getOrigemLabel(): string
    {
        return $this->isDoacacao() ? 'Doação' : 'Comprado';
    }

    // 🔹 Accessor: label amigável
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_DISPONIVEL => 'Disponível',
            self::STATUS_EM_USO => 'Em uso',
            self::STATUS_INDISPONIVEL => 'Indisponível',
            default => 'Desconhecido',
        };
    }

    // 🔹 Accessor: classe Bootstrap da badge
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_DISPONIVEL => 'success',
            self::STATUS_EM_USO => 'warning',
            self::STATUS_INDISPONIVEL => 'danger',
            default => 'secondary',
        };
    }

    // 🧩 Tipo de material
    public function isConsumivel(): bool
    {
        return $this->tipo_material === 'consumivel';
    }

    public function isReutilizavel(): bool
    {
        return $this->tipo_material === 'reutilizavel';
    }

    public function getTipoMaterialBadge(): string
    {
        if ($this->isConsumivel()) {
            return '<span class="badge bg-warning">Consumível</span>';
        }
        return '<span class="badge bg-info">Reutilizável</span>';
    }

    // 🔗 Relacionamentos
    public function agendamentos()
    {
        return $this->hasMany(Agendamento::class);
    }

    public function doacao()
    {
        return $this->belongsTo(Doacao::class);
    }

    // 🔍 Verifica se está disponível
    public function isDisponivel(): bool
    {
        return ($this->quantidade_disponivel ?? 0) > 0 && $this->isConjuntoUtilizavel();
    }

    // 🧾 Descrição detalhada do status
    public function getStatusDescricaoAttribute(): string
    {
        if ($this->possui_multiplas_pecas) {
            return "{$this->quantidade_pecas_atual} de {$this->quantidade_pecas_total} peças ({$this->percentual_pecas_atual}%)";
        }

        return match ($this->status) {
            self::STATUS_DISPONIVEL => "{$this->quantidade_disponivel} disponível(is) • {$this->quantidade_em_uso} em uso",
            self::STATUS_EM_USO => "Todas as {$this->quantidade_em_uso} unidades estão emprestadas",
            self::STATUS_INDISPONIVEL => "Material indisponível (esgotado)",
            default => 'Status desconhecido',
        };
    }

    // 📈 Total comprado
    public function getTotalCompradoAttribute(): int
    {
        return $this->quantidade_total_comprada ?? 0;
    }

    // 📉 Percentual de perda
    public function getPercentualPerdaAttribute(): float
    {
        if ($this->possui_multiplas_pecas && $this->quantidade_pecas_total > 0) {
            $pecasPerdidas = $this->quantidade_pecas_total - $this->quantidade_pecas_atual;
            return round(($pecasPerdidas / $this->quantidade_pecas_total) * 100, 1);
        }

        if (($this->quantidade_total_comprada ?? 0) == 0) {
            return 0;
        }
        return round(($this->quantidade_perdida / $this->quantidade_total_comprada) * 100, 1);
    }

    // 🧩 Retorna o status formatado
    public function getStatusFormatadoAttribute(): string
    {
        if (($this->quantidade_disponivel ?? 0) == 0) {
            return 'Indisponível';
        } elseif ($this->status === 'emprestado' || $this->status === self::STATUS_EM_USO) {
            return 'Em uso';
        } else {
            return 'Disponível';
        }
    }
}
