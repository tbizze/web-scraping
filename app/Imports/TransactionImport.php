<?php

namespace App\Imports;

use App\Models\Transaction;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class TransactionImport implements ToModel, WithHeadingRow
{
    // Método para importar os dados na tabela do BD.
    public function model(array $row)
    {
        return new Transaction([
            'transacao_id' => $row['transacao_id'],
            'tp_pgto' => $row['tipo_pagamento'],
            'status' => $row['status'],
            'valor_bruto' => $row['valor_bruto'],
            'valor_taxa' => $row['valor_taxa'],
            'valor_liquido' => $row['valor_liquido'],
            'dt_transacao' => $this->formatDate($row['data_transacao']),
            'dt_compensacao' => $this->formatDate($row['data_compensacao']),
            'ref_transacao' => $row['ref_transacao'],
            'parcelas' => $row['parcelas'],
            'cod_venda' => $row['codigo_venda'],
            'serial_leitor' => $row['serial_leitor'],
        ]);
    }

    // Método para converter a data no formato numérico do Excel para um formato legível.
    protected function formatDate(string $date)
    {
        return Date::excelToDateTimeObject($date);
    }
}
