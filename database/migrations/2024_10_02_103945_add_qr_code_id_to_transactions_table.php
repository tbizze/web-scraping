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
        Schema::table('transactions', function (Blueprint $table) {
            // Chave estrangeira: Transaction.
            $table->foreignId('qr_code_id')->after('leitor_id')->nullable()->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Remove a chave estrangeira.
            $table->dropForeign(['qr_code_id']);

            // Remove a coluna.
            $table->dropColumn('qr_code_id');
        });
    }
};
