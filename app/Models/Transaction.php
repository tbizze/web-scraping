<?php

namespace App\Models;

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

    // protected $casts = [
    //     'dt_transacao' => 'datetime:Y-m-d',
    //     'dt_compensacao' => 'datetime:Y-m-d',
    // ];

    public function tipoPgto(): BelongsTo
    {
        return $this->belongsTo(TipoPgto::class);
    }
    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }
    public function leitor(): BelongsTo
    {
        return $this->belongsTo(Leitor::class)->withDefault('N/D');
    }
}
