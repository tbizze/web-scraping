<?php

namespace App\Console\Commands;

use App\Jobs\ConvertImageJob;
use Illuminate\Console\Command;

class ExecAutoConvertCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:auto-convert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inicia rotina de Auto-Import';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        ConvertImageJob::dispatch();

        // Emite mensagem de retorno.
        $this->info('Tarefa realizada com sucesso.');

        return 0; // Comando executado com sucesso.
    }
}
