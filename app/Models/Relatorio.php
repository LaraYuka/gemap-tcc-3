<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Relatorio extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tipo',
        'data_inicio',
        'data_fim',
        'categoria',
        'status',
        'origem',
        'formato',
        'caminho_arquivo',
        'data_geracao',
    ];

    protected $casts = [
        'data_inicio' => 'date',
        'data_fim' => 'date',
        'data_geracao' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getTipoNomeAttribute()
    {
        return match ($this->tipo) {
            'materiais' => 'Relatório de Materiais',
            'agendamentos' => 'Relatório de Agendamentos',
            'completo' => 'Relatório Completo',
            'analise-avancada' => 'Análise Avançada',
            default => $this->tipo,
        };
    }

    public function getFiltrosFormatadosAttribute()
    {
        $filtros = [];

        if ($this->data_inicio) {
            $filtros[] = "De: " . $this->data_inicio->format('d/m/Y');
        }

        if ($this->data_fim) {
            $filtros[] = "Até: " . $this->data_fim->format('d/m/Y');
        }

        if ($this->categoria) {
            $filtros[] = "Categoria: {$this->categoria}";
        }

        if ($this->status) {
            $filtros[] = "Status: {$this->status}";
        }

        if ($this->origem) {
            $filtros[] = "Origem: " . ($this->origem === 'doacao' ? 'Doação' : 'Comprado');
        }

        return empty($filtros) ? 'Sem filtros' : implode(' | ', $filtros);
    }

    public function arquivoExiste()
    {
        if (!$this->caminho_arquivo) {
            return false;
        }

        return file_exists(storage_path('app/' . $this->caminho_arquivo));
    }

    public function getTamanhoArquivo()
    {
        if (!$this->arquivoExiste()) {
            return null;
        }

        $bytes = filesize(storage_path('app/' . $this->caminho_arquivo));

        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }

        return $bytes . ' bytes';
    }
}
