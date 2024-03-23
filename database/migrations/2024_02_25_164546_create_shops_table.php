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
        Schema::create('0shops', function (Blueprint $table) {
            $table->id('id_shop');
            $table->unsignedBigInteger('id_user')->unique();
            $table->string('phone_number', 15)->unique();
            $table->string('shop_name', 50);
            $table->text('description')->nullable();
            $table->text('benchmark')->nullable();
            $table->text('operational')->nullable()->default('{"Senin":"05:00-16:00","Selasa":"05:00-16:00","Rabu":"05:00-16:00","Kamis":"05:00-16:00","Jumat":"05:00-16:00","Sabtu":"05:00-16:00","Minggu":"05:00-16:00"}');
            $table->string('photo', 15)->nullable()->default('shop.png');
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
        Schema::dropIfExists('0shops');
    }
};
