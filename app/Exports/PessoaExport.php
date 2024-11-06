<?php

namespace App\Exports;

use App\Models\Pessoa;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PessoaExport implements FromQuery, WithHeadings
{
    use Exportable;

    public $year;

    // public function __construct(string|null $year)
    // {
    //     $this->year = $year;
    // }

    /**
     * @return \Illuminate\Database\Query\Builder
     */
    public function query()
    {
        //return Pessoa::query()
        // $x = Pessoa::query()
        //     ->select('id', 'nome', 'data_nascimento', 'telefone', 'notas')
        //     ->orderBy('nome')->get();

        return Pessoa::query()
            ->select('id', 'nome', 'data_nascimento', 'telefone', 'notas')
            ->orderBy('nome');
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nome',
            'Data nasc.',
            'Telefone',
            'Carnê',
        ];
    }
}
