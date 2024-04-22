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
        Schema::create('0refresh_token', function (Blueprint $table) {
            $table->id('id_token');
            $table->string('email', 100);
            $table->text('token');
            $table->enum('device', ['Mobile', 'Website'])->default('Mobile');
            $table->string('device_name', 20)->nullable()->default('Unknown');
            $table->text('device_token')->nullable();
            $table->tinyInteger('number')->default(1);
            $table->timestamps();
            $table->foreign('email')->references('email')
                ->on('0users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('0refresh_token');
    }
};
