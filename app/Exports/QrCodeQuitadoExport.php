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
        // return Transaction::query()
        //     ->select('id', 'valor_bruto',  'valor_taxa', 'valor_liquido', 'dt_transacao', 'ref_transacao')
        //     ->with(['qrCode', 'qrCode.pessoa'])
        //     ->has('qrCode')
        //     ->withAggregate('qrCode', 'grupo')
        //     ->withAggregate('qrCode', 'carne')
        //     ->withAggregate('status', 'description')
        //     ->orderBy('qr_code_carne')
        //     ->orderBy('dt_transacao')
        // ;

        return Transaction::query()
            ->select(
                'transactions.id',
                'valor_bruto',
                'valor_taxa',
                'valor_liquido',
                'dt_transacao',
                'qr_codes.grupo',
                'qr_codes.carne',
                'pessoas.nome',
                'ref_transacao',
                'statuses.description',
            )
            ->with(['qrCode', 'qrCode.pessoa'])
            ->leftJoin('statuses', 'statuses.id', '=', 'transactions.status_id')
            ->join('qr_codes', 'qr_codes.id', '=', 'transactions.qr_code_id')
            ->leftJoin('pessoas', 'pessoas.id', '=', 'qr_codes.pessoa_id')
            ->orderBy('qr_codes.carne')
            ->orderBy('dt_transacao');
    }

    public function headings(): array
    {
        return [
            'ID',
            'Bruto',
            'Taxa',
            'Líquido',
            'Dt. Transação',
            'Grupo',
            'Nº Carnê',
            'Nome',
            'Referência',
            'Status',
        ];
    }
}
