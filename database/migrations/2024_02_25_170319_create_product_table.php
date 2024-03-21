<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use function PHPUnit\Framework\once;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sp_1_prod', function (Blueprint $table) {
            $table->id('id_product');
            $table->unsignedBigInteger('id_shop');
            $table->unsignedBigInteger('id_cp_prod');
            $table->string('product_name', 50)->unique();
            $table->text('description')->nullable();
            $table->integer('selling_unit');
            $table->enum('unit', ['Gram', 'Kilogram', 'Ons', 'Kuintal', 'Ton', 'Liter', 'Milliliter', 'Sendok', 'Cangkir', 'Mangkok', 'Botol', 'Karton', 'Dus', 'Buah', 'Ekor']);
            $table->integer('price');
            $table->smallInteger('total_sold')->default(0);
            $table->text('settings')->nullable()->default('{"is_recommended": false, "is_shown": false, "is_available": false}');
            $table->text('promos')->nullable()->default('{"default_price": 80000, "promo_price": 70000, "promo_start": "2024-01-01", "promo_end": "2024-01-17"}');
            $table->text('photo');
            $table->timestamps();
            $table->foreign('id_shop')->references('id_shop')
                ->on('0shops')->onDelete('cascade');
            $table->foreign('id_cp_prod')->references('id_cp_prod')
                ->on('0product_categories')->onDelete('no action');
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
