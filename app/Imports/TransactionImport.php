<?php

namespace App\Imports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TransactionImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Transaction([
            'transacao_id' => $row['Transacao_ID'],
            'tp_pgto' => $row['Tipo_Pagamento'],
            'status' => $row['Status'],
            'valor_bruto' => $row['Valor_Bruto'],
            'valor_taxa' => $row['Valor_Taxa'],
            'valor_liquido' => $row['Valor_Liquido'],
            'dt_transacao' => $row['Data_Transacao'],
            'dt_compensacao' => $row['Data_Compensacao'],
            'ref_transacao' => $row['Ref_Transacao'],
            'parcelas' => $row['Parcelas'],
            'cod_venda' => $row['Codigo_Venda'],
            'serial_leitor' => $row['Serial_Leitor'],
        ]);
    }
}
