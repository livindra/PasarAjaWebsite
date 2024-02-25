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
        Schema::create('product', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('id_shop');
            $table->unsignedInteger('id_cp_product');
            $table->string('product_name', 30);
            $table->text('description')->nullable();
            $table->smallInteger('total_sold');
            $table->text('settings');
            $table->enum('unit', ['gram', 'kilogram', 'liter']);
            $table->integer('harga');
            $table->text('promos');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product');
    }
};
