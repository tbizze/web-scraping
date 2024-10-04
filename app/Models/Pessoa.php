<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pessoa extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'data_nascimento',
        'sexo',
        'telefone',
        'pessoa_status_id',
        'notas'
        // 'cpf',
        // 'rg',
        // 'email',
        // 'endereco',
        // 'bairro',
        // 'cidade',
        // 'estado',
        // 'cep',
    ];

    public function pessoaStatus(): BelongsTo
    {
        return $this->belongsTo(PessoaStatus::class);
    }

    public function qrCodes(): HasMany
    {
        return $this->hasMany(QrCode::class)->withDefault('N/D');
    }
}
