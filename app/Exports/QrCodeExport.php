<?php

namespace App\Exports;

use App\Models\QrCode;
use Maatwebsite\Excel\Concerns\FromCollection;

class QrCodeExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return QrCode::all();
    }
}
