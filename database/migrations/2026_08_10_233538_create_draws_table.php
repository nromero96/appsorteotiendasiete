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
        Schema::create('draws', function (Blueprint $table) {
            $table->string('id', 80)->primary();
            $table->string('title');
            $table->date('draw_date')->nullable();
            $table->time('draw_time')->nullable();
            $table->string('transmission')->nullable();
            $table->string('flyer_path')->nullable();
            $table->decimal('combo_discount', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('draws');
    }
};
