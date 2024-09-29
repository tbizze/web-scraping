<?php

namespace App\Exports;

use App\Models\QrCode;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class QrCodeExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return QrCode::get(['id', 'status', 'grupo', 'carne', 'pagseguro_id']);
    }

    public function headings(): array
    {
        return [
            'ID',
            'Status',
            'Grupo',
            'Carnê',
            'ID PagBank',
        ];
    }
}
