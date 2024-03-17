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
        Schema::create('0verifications', function (Blueprint $table) {
            $table->id('id_verification');
            $table->string('email', 100);
            $table->string('otp', 4);
            $table->enum('type', ['Register', 'Forgot']);
            $table->tinyInteger('number');
            $table->bigInteger('expiration_time');
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
        Schema::dropIfExists('0verifications');
    }
};
