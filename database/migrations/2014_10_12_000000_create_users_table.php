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
        Schema::create('0users', function (Blueprint $table) {
            $table->id('id_user');
            $table->string('phone_number', 15)->nullable();
            $table->string('email', 100)->unique();
            $table->string('full_name', 50);
            $table->text('password');
            $table->text('pin');
            $table->enum('level', ['Pengelola', 'Penjual', 'Pembeli'])->default('Pembeli');
            $table->boolean('is_verified')->default(0);
            $table->string('photo', 15)->nullable()->default('photo-profile.png');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('0users');
    }
};
