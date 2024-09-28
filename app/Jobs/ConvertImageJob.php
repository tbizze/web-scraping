<?php

namespace App\Jobs;

use App\Http\Controllers\QrCodeController;
use App\Models\QrCode;
use App\Services\ConvertService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ConvertImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(ConvertService $convertService): void
    {
        // Busca o registro com dados da imagem.
        $qrCodes = QrCode::query()
            ->where('status', '=', 0)
            ->limit(3)
            ->get();

        // Para cada item, realiza a requisição para converter JPG em TXT.
        foreach ($qrCodes as $item) {

            // Registra um log
            Log::info('>>> Start automatic convert: ' . $item->id . ' | ' . $item->grupo . '/' . $item->carne);

            // Chama o serviço que gerencia requisições ao site de conversão de JPG para TXT.
            // Passa como parâmetro o ID.
            $convert = $convertService->allConvert($item->id);

            //dd($convert);


            // Se retornou erro.
            if (isset($convert) && is_array($convert) && array_key_exists('error', $convert)) {

                // Grava LOG.
                Log::error('ERROR CONVERT: ' . $convert['error'] . '. ' . $convert['message']);

                // Se não retornou transações.
            } elseif (isset($convert) && is_array($convert) && array_key_exists('info', $convert)) {

                // Grava LOG.
                Log::info('FAIL CONVERT: ' . $convert['info'] . '. ' . $convert['message']);
            } else {

                // Se retornado transações. Registra um log.
                Log::info('SUCCESS CONVERT: ' . $convert['success'] . '. ' . $convert['message']);
            }
        }
    }
}
