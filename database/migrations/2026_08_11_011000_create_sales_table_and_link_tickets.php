<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->string('id', 100)->primary();
            $table->string('draw_id', 80);
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->enum('purchase_type', ['individual', 'combo']);
            $table->decimal('subtotal_amount', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->enum('status', ['pendiente', 'aprobado', 'rechazado'])->default('pendiente');
            $table->string('payment_method', 50)->nullable();
            $table->string('transaction_id', 150)->nullable();
            $table->string('voucher_path')->nullable();
            $table->text('notes')->nullable();
            $table->foreign('draw_id')->references('id')->on('draws')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->string('sale_id', 100)->nullable()->after('id')->index();
        });

        $groups = [];
        $tickets = DB::table('tickets')
            ->leftJoin('prizes', 'prizes.id', '=', 'tickets.prize_id')
            ->select('tickets.*', 'prizes.price as current_prize_price')
            ->orderBy('tickets.created_at')
            ->get();

        foreach ($tickets as $ticket) {
            $key = $ticket->purchase_type === 'combo'
                ? implode('|', [$ticket->draw_id, $ticket->customer_id, $ticket->status, $ticket->payment_method, $ticket->transaction_id, $ticket->voucher_path, $ticket->created_at])
                : $ticket->id;

            if (!isset($groups[$key])) {
                $groups[$key] = [
                    'id' => 'sale-'.Str::uuid(),
                    'draw_id' => $ticket->draw_id,
                    'customer_id' => $ticket->customer_id,
                    'purchase_type' => $ticket->purchase_type,
                    'status' => $ticket->status,
                    'payment_method' => $ticket->payment_method,
                    'transaction_id' => $ticket->transaction_id,
                    'voucher_path' => $ticket->voucher_path,
                    'notes' => $ticket->notes,
                    'subtotal' => 0,
                    'total' => 0,
                    'created_at' => $ticket->created_at,
                    'updated_at' => $ticket->updated_at,
                    'ticket_ids' => [],
                ];
            }

            $groups[$key]['subtotal'] += (float) ($ticket->current_prize_price ?? $ticket->total_amount);
            $groups[$key]['total'] += (float) $ticket->total_amount;
            $groups[$key]['ticket_ids'][] = $ticket->id;
        }

        foreach ($groups as $sale) {
            $discount = $sale['purchase_type'] === 'combo' ? max(0, $sale['subtotal'] - $sale['total']) : 0;
            DB::table('sales')->insert([
                'id' => $sale['id'],
                'draw_id' => $sale['draw_id'],
                'customer_id' => $sale['customer_id'],
                'purchase_type' => $sale['purchase_type'],
                'subtotal_amount' => round($sale['subtotal'], 2),
                'discount_amount' => round($discount, 2),
                'total_amount' => round($sale['total'], 2),
                'status' => $sale['status'],
                'payment_method' => $sale['payment_method'],
                'transaction_id' => $sale['transaction_id'],
                'voucher_path' => $sale['voucher_path'],
                'notes' => $sale['notes'],
                'created_at' => $sale['created_at'],
                'updated_at' => $sale['updated_at'],
            ]);
            DB::table('tickets')->whereIn('id', $sale['ticket_ids'])->update(['sale_id' => $sale['id']]);
        }

        Schema::table('tickets', function (Blueprint $table) {
            $table->foreign('sale_id')->references('id')->on('sales')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['sale_id']);
            $table->dropColumn('sale_id');
        });
        Schema::dropIfExists('sales');
    }
};
