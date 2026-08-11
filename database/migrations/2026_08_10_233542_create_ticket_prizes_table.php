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
        Schema::create('ticket_prizes', function (Blueprint $table) {
            $table->string('ticket_id', 100);
            $table->string('prize_id', 80);
            $table->decimal('unit_price', 10, 2);
            $table->primary(['ticket_id', 'prize_id']);
            $table->foreign('ticket_id')->references('id')->on('tickets')->cascadeOnDelete();
            $table->foreign('prize_id')->references('id')->on('prizes')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_prizes');
    }
};
