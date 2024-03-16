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
        Schema::create('refresh_token', function (Blueprint $table) {
            $table->id('id_session');
            $table->string('email', 100);
            $table->text('device_token');
            $table->enum('device', ['Mobile', 'Website'])->default('Mobile');
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
        Schema::dropIfExists('refresh_token');
    }
};
