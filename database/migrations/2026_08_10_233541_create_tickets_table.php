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
        Schema::create('tickets', function (Blueprint $table) {
            $table->string('id', 100)->primary();
            $table->string('draw_id', 80);
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('ticket_number', 50);
            $table->enum('purchase_type', ['individual', 'combo']);
            $table->decimal('total_amount', 10, 2);
            $table->enum('status', ['pendiente', 'aprobado', 'rechazado'])->default('pendiente');
            $table->string('payment_method', 50)->nullable();
            $table->string('transaction_id', 150)->nullable();
            $table->string('voucher_path')->nullable();
            $table->text('notes')->nullable();
            $table->foreign('draw_id')->references('id')->on('draws')->cascadeOnDelete();
            $table->unique(['draw_id', 'ticket_number']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
