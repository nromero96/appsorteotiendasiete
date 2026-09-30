<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Draw;
use App\Models\Sale;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TicketController extends Controller
{
    public function lookup(Request $request)
    {
        $data = $request->validate([
            'ticket_number' => ['nullable', 'string', 'max:50'],
        ]);

        $ticketNumber = preg_replace('/\D+/', '', $data['ticket_number'] ?? '');
        $ticket = null;

        if ($ticketNumber !== '') {
            $ticket = Ticket::query()
                ->with(['customer', 'draw', 'prize', 'sale'])
                ->where('ticket_number', $ticketNumber)
                ->latest()
                ->first();
        }

        return view('tickets.lookup', [
            'ticket' => $ticket,
            'ticketNumber' => $ticketNumber,
            'searched' => $request->filled('ticket_number'),
        ]);
    }

    public function create(Draw $draw)
    {
        if ($draw->isTicketRegistrationClosed()) {
            return redirect()->route('draws.show', $draw)->with('error', 'El registro de tickets se cerró 30 minutos antes del sorteo.');
        }

        return view('tickets.create', ['draw' => $draw->load('prizes')]);
    }

    public function store(Request $request, Draw $draw) {
        if ($draw->isTicketRegistrationClosed()) {
            return redirect()->route('draws.show', $draw)->with('error', 'El registro de tickets se cerró 30 minutos antes del sorteo.');
        }

        $data = $request->validate(['prize_ids' => 'required|array|min:1', 'prize_ids.*' => 'string', 'full_name' => 'required|max:255', 'phone' => 'required|max:50', 'document_number' => 'required|max:50', 'payment_method' => 'required|in:Yape,Plin,Transferencia,Compra', 'transaction_id' => 'nullable|max:150', 'status' => 'required|in:pendiente,aprobado,rechazado', 'voucher' => 'nullable|image|max:5120']);
        $activePrizes = $draw->prizes()->where('active', true)->get();
        $selectedIds = collect($data['prize_ids'])->unique()->values();
        $prizes = $activePrizes->whereIn('id', $selectedIds);
        abort_if($prizes->isEmpty() || $prizes->count() !== $selectedIds->count(), 422);

        $isCombo = $activePrizes->count() > 1 && $prizes->count() === $activePrizes->count();
        abort_unless($prizes->count() === 1 || $isCombo, 422);

        $voucher = $request->hasFile('voucher') ? $request->file('voucher')->store('vouchers', 'public') : null;
        $subtotal = (float) $prizes->sum('price');
        $discount = $isCombo ? min(max((float) $draw->combo_discount, 0), $subtotal) : 0;

        DB::transaction(function () use ($draw, $data, $voucher, $prizes, $subtotal, $discount, $isCombo) {
            $customer = Customer::create(['full_name' => $data['full_name'], 'phone' => $data['phone'], 'document_number' => $data['document_number']]);
            $sale = Sale::create([
                'id' => 'sale-'.Str::uuid(),
                'draw_id' => $draw->id,
                'customer_id' => $customer->id,
                'purchase_type' => $isCombo ? 'combo' : 'individual',
                'subtotal_amount' => $subtotal,
                'discount_amount' => $discount,
                'total_amount' => round($subtotal - $discount, 2),
                'status' => $data['status'],
                'payment_method' => $data['payment_method'],
                'transaction_id' => $data['transaction_id'],
                'voucher_path' => $voucher,
            ]);
            $next = max(7000, ((int) Ticket::where('draw_id', $draw->id)->lockForUpdate()->max('ticket_number')) + 1);
            $assigned = 0;

            foreach ($prizes->values() as $index => $prize) {
                $amount = $isCombo
                    ? ($index === $prizes->count() - 1 ? round($subtotal - $discount - $assigned, 2) : round((float) $prize->price - ($discount * ((float) $prize->price / $subtotal)), 2))
                    : (float) $prize->price;
                $assigned += $amount;

                $ticket = Ticket::create(['id' => 'ticket-'.Str::uuid(), 'sale_id' => $sale->id, 'draw_id' => $draw->id, 'prize_id' => $prize->id, 'customer_id' => $customer->id, 'ticket_number' => $next + $index, 'purchase_type' => $isCombo ? 'combo' : 'individual', 'total_amount' => $amount, 'status' => $data['status'], 'payment_method' => $data['payment_method'], 'transaction_id' => $data['transaction_id'], 'voucher_path' => $voucher]);
                $ticket->prizes()->attach($prize->id, ['unit_price' => $amount]);
            }
        });

        return redirect()->route('sales.index')->with('success', $isCombo ? 'Venta combo registrada con tickets individuales por premio.' : 'Venta y ticket registrados correctamente.');
    }
}
