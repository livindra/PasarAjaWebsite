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
        Schema::create('sp_1_trx', function (Blueprint $table) {
            $table->id('id_trx');
            $table->unsignedBigInteger('id_user');
            $table->text('order_code')->unique();
            $table->string('order_pin', 4);
            $table->enum('status', ['Request', 'Accepted', 'Rejected']);
            $table->smallInteger('total_product');
            $table->integer('total_price');
            $table->date('taken_date');
            $table->bigInteger('expiration_time');
            $table->integer('confirmed_by');
            $table->text('rejected_message');
            $table->timestamps();
            $table->foreign('id_user')->references('id_user')
            ->on('0users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction');
    }
};
