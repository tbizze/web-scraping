<?php

namespace App\Imports;

use App\Models\Pessoa;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class PessoaImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Pessoa([
            'nome' => capitalize_words($row['nome']),
            'data_nascimento' => $this->formatDate($row['nascimento']),
            'telefone' => $this->formatFone($row['telefone']),
            'notas' => $row['carne'],
            'pessoa_status_id' => 5,
        ]);
    }

    // Método para converter o telefone para um formato numérico.
    protected function formatFone(string|null $fone)
    {
        // Se variável for null, retorna null.
        if ($fone == null) {
            return null;
        }

        if (str_contains($fone, '(')) {
            return null;
        }

        // Se não possui parênteses, adiciona os parênteses com DD e retorna.
        return '(11)' . $fone;
    }

    // Método para converter a data no formato numérico do Excel para um formato legível.
    protected function formatDate(string|null $date)
    {
        try {

            // Se data é null, retorna null.
            if (!$date) {
                return null;
            }


            // Quebra o $date em três partes: dia/mês/ano.
            $date_string = explode('/', $date);
            // Se existe três partes. Formata.
            if (count($date_string) == 3) {

                // Se parte do ano é vazio, retorna null
                if (empty($date_string[2])) {
                    return null;
                }

                // Retorna a data formatada no formato 'Y-m-d'
                return date_to_db($date);
            }

            // Verifica se a data é numérica (formato Excel)
            try {
                return Date::excelToDateTimeObject((float)$date);
            } catch (\Exception $e) {
                // Se a data não é numérica, retorna null
                return null;
            }
        } catch (\Exception $e) {
            // Registre o erro ou trate-o conforme necessário
            dd($e->getMessage());
            //return $e->getMessage();
            //return null;
        }
    }
}
