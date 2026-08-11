<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SaleController extends Controller
{
    public function index()
    {
        $sales = Sale::query()
            ->with(['draw', 'customer', 'tickets.prize'])
            ->latest()
            ->paginate(15);

        $totals = [
            'approved' => Sale::where('status', 'aprobado')->sum('total_amount'),
            'pending' => Sale::where('status', 'pendiente')->sum('total_amount'),
            'discounts' => Sale::sum('discount_amount'),
        ];

        return view('sales.index', compact('sales', 'totals'));
    }

    public function updateStatus(Request $request, Sale $sale)
    {
        $data = $request->validate([
            'status' => 'required|in:pendiente,aprobado,rechazado',
            'transaction_id' => 'nullable|string|max:150',
        ]);

        $transactionId = Str::upper(trim((string) ($data['transaction_id'] ?? '')));

        if ($data['status'] === 'aprobado' && $transactionId === '') {
            throw ValidationException::withMessages([
                'transaction_id' => ['Debes registrar la ID de transacción para aprobar esta venta.'],
            ]);
        }

        if ($sale->approval_transaction_id && $sale->approval_transaction_id !== $transactionId) {
            throw ValidationException::withMessages([
                'transaction_id' => ['La referencia de una venta ya aprobada no puede cambiarse.'],
            ]);
        }

        DB::transaction(function () use ($sale, $data, $transactionId) {
            if ($data['status'] === 'aprobado') {
                $duplicate = Sale::query()
                    ->where('approval_transaction_id', $transactionId)
                    ->where('id', '!=', $sale->id)
                    ->lockForUpdate()
                    ->exists();

                if ($duplicate) {
                    throw ValidationException::withMessages([
                        'transaction_id' => ['Esta ID de transacción ya fue utilizada para aprobar otra venta.'],
                    ]);
                }
            }

            $sale->update([
                'status' => $data['status'],
                'transaction_id' => $transactionId ?: null,
                'approval_transaction_id' => $data['status'] === 'aprobado' ? $transactionId : $sale->approval_transaction_id,
            ]);
            Ticket::where('sale_id', $sale->id)->update([
                'status' => $data['status'],
                'transaction_id' => $transactionId ?: null,
            ]);
        });

        return back()->with('success', 'Estado de la venta actualizado.');
    }
}
