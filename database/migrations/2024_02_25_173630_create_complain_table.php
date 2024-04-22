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
        Schema::create('sp_1_comp', function (Blueprint $table) {
            $table->id('id_complain');
            $table->unsignedBigInteger('id_user');
            $table->unsignedBigInteger('id_shop');
            $table->unsignedBigInteger('id_trx');
            $table->unsignedBigInteger('id_product');
            $table->text('reason');
            $table->timestamps();
            $table->foreign('id_user')->references('id_user')
                ->on('0users')->onDelete('no action');
            $table->foreign('id_shop')->references('id_shop')
                ->on('0shops')->onDelete('cascade');
            $table->foreign('id_trx')->references('id_trx')
                ->on('sp_1_trx')->onDelete('cascade');
            $table->foreign('id_product')->references('id_product')
                ->on('sp_1_prod')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('0reports');
    }
};
