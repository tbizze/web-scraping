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
            $table->foreignId('tipo_pgto_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('status_id')->nullable()->constrained()->onDelete('cascade');
            $table->decimal('valor_bruto');
            $table->decimal('valor_taxa')->nullable();
            $table->decimal('valor_liquido');
            $table->dateTime('dt_transacao');
            $table->dateTime('dt_compensacao');
            $table->string('ref_transacao')->nullable();
            $table->integer('parcelas');
            $table->string('cod_venda')->nullable();
            $table->foreignId('leitor_id')->nullable()->constrained()->onDelete('cascade');
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
