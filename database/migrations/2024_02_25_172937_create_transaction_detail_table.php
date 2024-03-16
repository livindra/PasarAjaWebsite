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
        Schema::create('sp_1_trx_dtl', function (Blueprint $table) {
            $table->id('id_detail');
            $table->unsignedBigInteger('id_trx');
            $table->unsignedBigInteger('id_product');
            $table->smallInteger('quantity');
            $table->integer('total_price');
            $table->foreign('id_trx')->references('id_trx')
                ->on('sp_1_trx')->onDelete('cascade');
            $table->foreign('id_product')->references('id_product')
                ->on('sp_1_prod')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_detail');
    }
};
