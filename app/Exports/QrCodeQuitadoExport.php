<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

class QrCodeQuitadoExport implements FromQuery, WithHeadings
{
    use Exportable;

    public $year;

    public function __construct(string|null $year)
    {
        $this->year = $year;
    }

    /**
     * @return \Illuminate\Database\Query\Builder
     */
    public function query()
    {
        return Transaction::query()
            ->select('id', 'valor_bruto',  'valor_taxa', 'valor_liquido', 'dt_transacao', 'ref_transacao')
            ->with('qr_code')
            ->has('qr_code')
            ->withAggregate('qr_code', 'grupo')
            ->withAggregate('qr_code', 'carne')
            ->withAggregate('status', 'description')
            ->orderBy('qr_code_carne')
            ->orderBy('dt_transacao')
        ;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Bruto',
            'Taxa',
            'Líquido',
            'Dt. Transação',
            'Referência',
            'Grupo',
            'Nº Carnê',
            'Status',
        ];
    }
}
