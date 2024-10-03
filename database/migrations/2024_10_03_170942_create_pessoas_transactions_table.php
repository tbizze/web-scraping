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
        Schema::create('pessoas_transactions', function (Blueprint $table) {
            $table->foreignId('pessoa_id')->unsigned();
            $table->foreignId('transaction_id')->unsigned();
            //$table->primary(['pessoa_id', 'transaction_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pessoas_transactions');
    }
};
