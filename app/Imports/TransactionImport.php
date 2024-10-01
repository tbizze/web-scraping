<?php

namespace App\Imports;

use App\Models\Leitor;
use App\Models\Status;
use App\Models\TipoPgto;
use App\Models\Transaction;
use Carbon\Carbon;
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
            'tipo_pgto_id' => $this->getTpPgto($row['tipo_pagamento']),
            'status_id' => $this->getStatus($row['status']),
            'valor_bruto' => $this->formatNumber($row['valor_bruto']),
            'valor_taxa' => $this->formatNumber($row['valor_taxa']),
            'valor_liquido' => $this->formatNumber($row['valor_liquido']),
            'dt_transacao' => $this->formatDate($row['data_transacao']),
            'dt_compensacao' => $this->formatDate($row['data_compensacao']),
            'ref_transacao' => $row['ref_transacao'],
            'parcelas' => $row['parcelas'],
            'cod_venda' => $row['codigo_venda'],
            'leitor_id' => $this->getLeitor($row['serial_leitor']),
        ]);
    }

    // Método para converter números em formato legível.
    protected function formatNumber($number)
    {
        if ($number) {
            return (float) str_replace(['.', ','], ['', '.'], $number);
        }
    }

    // Método para converter a data no formato numérico do Excel para um formato legível.
    protected function formatDate(string $date)
    {
        if (Carbon::hasFormat($date, 'd/m/Y H:i')) {
            return Carbon::createFromFormat('d/m/Y H:i', $date)->format('Y-m-d H:i');
        }
        return Date::excelToDateTimeObject($date);
    }

    // Método para inserir tipo de pagamento no BD, resgatando ID.
    private function getTpPgto(string $tp_pgto)
    {
        // Se não existir, cria um novo.
        $data = TipoPgto::firstOrCreate(
            ['description' =>  $tp_pgto],
        );
        return $data->id;
    }

    // Método para inserir status no BD, resgatando o ID.
    private function getStatus(string $status)
    {
        // Se não existir, cria um novo.
        $data = Status::firstOrCreate(
            ['description' =>  $status],
        );
        return $data->id;
    }

    // Método para inserir Leitor no BD, resgatando o ID.
    private function getLeitor(string|null $leitor)
    {
        // Se variável for null, retorna null.
        if ($leitor == null) {
            return null;
        }

        // Se não existir, cria um novo.
        $data = Leitor::firstOrCreate(
            ['description' =>  $leitor],
        );
        return $data->id;
    }
}
