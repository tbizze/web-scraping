<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transacao_id',
        'tipo_pgto_id',
        'status_id',
        'valor_bruto',
        'valor_taxa',
        'valor_liquido',
        'dt_transacao',
        'dt_compensacao',
        'ref_transacao',
        'parcelas',
        'cod_venda',
        'leitor_id',
    ];

    protected $casts = [
        'dt_transacao' => 'datetime',
        'dt_compensacao' => 'datetime',
    ];

    // Formata data para interface.
    protected function dtTransacao(): Attribute
    {
        return Attribute::make(
            get: fn(string $value) => Carbon::parse($value)->format('d/m/Y H:i'),
        );
    }

    // Formata data para interface.
    protected function dtCompensacao(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ? Carbon::parse($value)->format('d/m/Y H:i') : null,
        );
    }

    public function tipoPgto(): BelongsTo
    {
        return $this->belongsTo(TipoPgto::class);
    }
    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class)->withDefault('N/D');
    }
    public function leitor(): BelongsTo
    {
        return $this->belongsTo(Leitor::class)->withDefault('N/D');
    }
}
