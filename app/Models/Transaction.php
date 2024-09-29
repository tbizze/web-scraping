<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transacao_id',
        'tp_pgto',
        'status',
        'valor_bruto',
        'valor_taxa',
        'valor_liquido',
        'dt_transacao',
        'dt_compensacao',
        'ref_transacao',
        'parcelas',
        'cod_venda',
        'serial_leitor',
    ];

    // protected $casts = [
    //     'dt_transacao' => 'datetime:Y-m-d',
    //     'dt_compensacao' => 'datetime:Y-m-d',
    // ];
}
