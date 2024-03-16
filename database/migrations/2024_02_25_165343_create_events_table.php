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
        Schema::create('0events', function (Blueprint $table) {
            $table->id('id_event');
            $table->unsignedBigInteger('id_user');
            $table->string('event_name', 50);
            $table->text('description');
            $table->date('start_date');
            $table->date('end_date');
            $table->time('start_hour');
            $table->time('end_hour');
            $table->text('photo');
            $table->timestamps();
            $table->foreign('id_user')->references('id_user')
                ->on('0users')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('0events');
    }
};
