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
        Schema::create('0product_categories', function (Blueprint $table) {
            $table->id('id_cp_prod');
            $table->smallInteger('category_code')->unique();
            $table->string('category_name', 30);
            $table->string('photo', 15)->nullable()->default('category.png');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('0product_categories');
    }
};
