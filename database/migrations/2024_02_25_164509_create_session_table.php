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
        Schema::create('0session', function (Blueprint $table) {
            $table->id('id_session');
            $table->unsignedBigInteger('id_user');
            $table->text('device_token');
            $table->enum('device', ['Mobile', 'Website'])->default('Mobile');
            $table->tinyInteger('number')->default(1);
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
        Schema::dropIfExists('0session');
    }
};
