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
            $table->unsignedBigInteger('id_cp_shop');
            $table->string('shop_name', 50);
            $table->text('description')->nullable();
            $table->text('benchmark')->nullable();
            $table->text('operational')->nullable()->default('{"Senin":"05:00-16:00","Selasa":"05:00-16:00","Rabu":"05:00-16:00","Kamis":"05:00-16:00","Jumat":"05:00-16:00","Sabtu":"05:00-16:00","Minggu":"05:00-16:00"}');
            $table->text('photo')->nullable();
            $table->timestamps();
            $table->foreign('id_user')->references('id_user')
                ->on('0users')->onDelete('cascade');
            $table->foreign('id_cp_shop')->references('id_cp_shop')
                ->on('0shop_categories')->onUpdate('cascade')->onDelete('no action');
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
