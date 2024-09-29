<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transacao_id');
            $table->string('tp_pgto');
            $table->string('status');
            $table->string('valor_bruto');
            $table->string('valor_taxa');
            $table->string('valor_liquido');
            $table->dateTime('dt_transacao');
            $table->dateTime('dt_compensacao');
            $table->string('ref_transacao')->nullable();
            $table->integer('parcelas');
            $table->string('cod_venda')->nullable();
            $table->string('serial_leitor')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
