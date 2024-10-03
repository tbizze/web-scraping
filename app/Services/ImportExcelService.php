<?php

namespace App\Services;

use App\Models\Leitor;
use App\Models\Status;
use App\Models\TipoPgto;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ImportExcelService
{
    public function __construct() {}

    public function allImport(Collection $rows)
    {
        // Instancia Array para armazenar as informações das transações.
        $data = [];

        // Itera nas linhas, da primeira planilha.
        // Como está iterando $rows[0], desconsidera a existência de outras planilhas.
        foreach ($rows[0] as $row) {
            $data[] = [
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
            ];
        }

        //dd($this->storeRow($data));
        // Chama método para salvar as linhas encontradas.
        // Retorna uma mensagem.
        return $this->storeRow($data);
    }

    // Método para salvar as informações das transações no BD.
    public function storeRow($rows)
    {
        $savedRows = 0;
        $ignoredRows = 0;
        // Salvar os QR Codes no banco de dados
        foreach ($rows as $item) {
            // Procura registro com as informações do arquivo escaneado.
            $transaction = Transaction::query()
                ->where('dt_transacao', '=', $item['dt_transacao'])
                ->where('valor_liquido', '=', $item['valor_liquido'])
                ->where('transacao_id', '=', $item['transacao_id'])
                ->get();

            // Se não existe um registro com os mesmos dados, cria.
            if ($transaction->isEmpty()) {
                $savedRows++;
                Transaction::create($item);
            } else {
                $ignoredRows++;
            }
        }
        // Prepara mensagem de retorno para interface.
        $message = "Salvo $savedRows linhas com sucesso e ignorado $ignoredRows linhas!";

        // Retornar mensagem com a quantidade de linhas salvas.
        return ['rowsSaved' => $savedRows, 'rowsIgnored' => $ignoredRows, 'message' => $message];
    }

    // Método para converter números em formato legível.
    protected function formatNumber(mixed $number): float
    {
        // Verifica se é um número do tipo texto.
        // Caso seja, remove caracteres indesejados e converte para decimal.
        // Caso contrário, retorna o número como está.
        if ($number && !is_numeric($number)) {
            return (float) str_replace(['.', ','], ['', '.'], $number);
        }
        return $number;
    }

    // Método para converter a data no formato numérico do Excel para um formato legível.
    protected function formatDate(string $date)
    {
        if (Carbon::hasFormat($date, 'd/m/Y H:i')) {
            return Carbon::createFromFormat('d/m/Y H:i', $date)->format('Y-m-d H:i');
        } elseif (Carbon::hasFormat($date, 'd/m/Y H:i:s')) {
            return Carbon::createFromFormat('d/m/Y H:i:s', $date)->format('Y-m-d H:i');
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
