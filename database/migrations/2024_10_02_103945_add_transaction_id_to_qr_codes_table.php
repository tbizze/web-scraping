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
        Schema::table('qr_codes', function (Blueprint $table) {
            // Chave estrangeira: Transaction.
            $table->foreignId('transaction_id')->after('pagseguro_id')->nullable()->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('qr_codes', function (Blueprint $table) {
            // Remove a chave estrangeira.
            $table->dropForeign(['transaction_id']);

            // Remove a coluna.
            $table->dropColumn('transaction_id');
        });
    }
};
