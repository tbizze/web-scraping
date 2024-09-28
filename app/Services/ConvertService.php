<?php

namespace App\Services;

use App\Models\AppConfig;
use App\Models\BankAccount;
use App\Models\QrCode;
use App\Services\Banks\{PagBankService, SantanderService};
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ConvertService
{
    protected mixed $santanderService;

    protected mixed $pagBankService;

    public function __construct() {}

    public function allConvert(string $id)
    {
        // Busca o registro com dados da imagem.
        $qrCode = QrCode::findOrFail($id);

        // Se o QR Code já possui identificador, então sai do método.
        if ($qrCode->status == 1) {
            // Retornar mensagem informando.
            return ['info' => 'Falha na conversão.', 'message' => 'O QR Code já está processado no banco de dados.'];
        }


        // Monta a url para a imagem.
        $imagePath = storage_path($qrCode->path . '/' . $qrCode->name_file);

        // Executa o 'scrap-convert-ocr' para fazer o web scraping --> converter o JPG em TXT.
        $output = shell_exec("node " . base_path('scrap-convert-ocr.cjs') . " " . $imagePath);

        // Verifica se há erro de processamento.
        if (str_contains($output, 'Erro no processamento')) {
            // Se ocorreu erro, grava LOG avisando.
            //Log::error('ERROR CONVERT: '. $output);
            return ['error' => 'Falha no processamento', 'message' => 'Erro ao processar a página de conversão.'];
        }
        // Verifica se há erro de espera de elemento.
        if (str_contains($output, 'Erro na espera')) {
            // Se ocorreu erro, grava LOG avisando.
            //Log::error('ERROR CONVERT: '. $output);
            return ['error' => 'Falha na espera', 'message' => 'Não foi possível obter o retorno do processamento da conversão.'];
        }
        // Verifica se há erro de excesso de tentativas.
        if (str_contains($output, 'Excesso de tentativas')) {

            // Adiciona próxima execução.
            $this->nextProcessing();

            // Se ocorreu erro, grava LOG avisando.
            return ['info' => 'Não há mais tentativa', 'message' => 'É permitido apenas 5 arquivos por hora no modo visitante gratuito.'];
        }

        // Quando houver no resultado o Identificador.
        if (str_contains($output, 'Identificador')) {

            // Limpar e formatar os dados.
            $data = $this->cleanData($output);

            // Salvar informações no banco de dados
            $qrCode->update([
                'content' => $data,
                'status' => 1,
                'pagseguro_id' => $data['id'],
            ]);

            // Retornar mensagem informando.
            return ['success' => 'Conversão realizada', 'message' => 'O QR Code foi convertido com sucesso!'];
        } else {
            Log::error('FALHA: ' . $output);

            // Retorna mensagem informando que não obteve transações.
            return ['error' => 'Falha', 'message' => 'Erro ao executar o método responsável em converter JPG em TXT.'];
        }
    }

    public function nextProcessing()
    {
        // Obtém a data atual.
        $now = Carbon::now();
        // Adiciona 10 minutos (600 segundos) ao tempo atual, para gerar a data de expiração.
        $expire = $now->addSeconds(600);

        // Salvar a data de expiração no banco de dados.
        //AppConfig::create(['next_processing' => $expire]);
    }

    // Função para limpar e formatar os dados
    function cleanData($data)
    {
        // Quebra as linhas em um array para iteração.
        dump($data);
        $linhas = explode("\n", trim($data));

        // Inicializar variáveis para armazenar os valores
        $descricao = null;
        $identificador = null;

        // Iterar sobre as linhas e procurar pelas palavras-chave 'Descricao' e 'Identificador'
        foreach ($linhas as $linha) {
            if (strpos($linha, 'Descricao') !== false && strpos($linha, 'Identificador') !== false) {
                // Usar regex para capturar os valores de 'Descricao' e 'Identificador'
                preg_match('/Descricao\s+(\S+)\s+Identificador\s+(\S+)/', $linha, $matches);
                if (isset($matches[1])) {
                    $descricao = $matches[1];
                }
                if (isset($matches[2])) {
                    $identificador = $matches[2];
                }
            }
        }

        return [
            'desc' => $descricao,
            'id' => $identificador,
        ];
    }
}
