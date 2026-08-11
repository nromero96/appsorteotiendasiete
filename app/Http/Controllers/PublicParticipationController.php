<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Draw;
use App\Models\Sale;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PublicParticipationController extends Controller
{
    public function create(Draw $draw)
    {
        if ($draw->isTicketRegistrationClosed()) {
            return redirect()->route('public.next-draw')->with('error', 'El registro para este sorteo ya está cerrado.');
        }

        $draw->load(['prizes' => fn ($query) => $query->where('active', true)]);

        if ($draw->prizes->isEmpty()) {
            return redirect()->route('public.next-draw')->with('error', 'Este sorteo aún no tiene premios disponibles.');
        }

        return view('public.participate', compact('draw'));
    }

    public function store(Request $request, Draw $draw)
    {
        if ($draw->isTicketRegistrationClosed()) {
            return redirect()->route('public.next-draw')->with('error', 'El registro para este sorteo ya está cerrado.');
        }

        $data = $request->validate([
            'option' => ['required', 'string', 'max:100'],
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'document_number' => ['required', 'string', 'max:50'],
            'payment_method' => ['required', 'in:Yape,Plin,Transferencia,Compra'],
            'transaction_id' => ['nullable', 'string', 'max:150'],
            'voucher' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
        ]);

        $activePrizes = $draw->prizes()->where('active', true)->get();
        $isCombo = $data['option'] === 'combo';

        if ($isCombo) {
            abort_unless($activePrizes->count() > 1, 422);
            $prizes = $activePrizes;
        } else {
            abort_unless(Str::startsWith($data['option'], 'prize:'), 422);
            $prize = $activePrizes->firstWhere('id', Str::after($data['option'], 'prize:'));
            abort_unless($prize, 422);
            $prizes = collect([$prize]);
        }

        $voucher = $request->file('voucher')->store('vouchers', 'public');
        $subtotal = (float) $prizes->sum('price');
        $discount = $isCombo ? min(max((float) $draw->combo_discount, 0), $subtotal) : 0;

        $sale = DB::transaction(function () use ($draw, $data, $voucher, $prizes, $subtotal, $discount, $isCombo) {
            $customer = Customer::create([
                'full_name' => $data['full_name'],
                'phone' => $data['phone'],
                'document_number' => $data['document_number'],
            ]);

            $sale = Sale::create([
                'id' => 'sale-'.Str::uuid(),
                'draw_id' => $draw->id,
                'customer_id' => $customer->id,
                'purchase_type' => $isCombo ? 'combo' : 'individual',
                'subtotal_amount' => $subtotal,
                'discount_amount' => $discount,
                'total_amount' => round($subtotal - $discount, 2),
                'status' => 'pendiente',
                'payment_method' => $data['payment_method'],
                'transaction_id' => $data['transaction_id'] ?? null,
                'voucher_path' => $voucher,
            ]);

            $nextTicketNumber = max(7000, ((int) Ticket::where('draw_id', $draw->id)->lockForUpdate()->max('ticket_number')) + 1);
            $assigned = 0;

            foreach ($prizes->values() as $index => $prize) {
                $amount = $isCombo
                    ? ($index === $prizes->count() - 1
                        ? round($subtotal - $discount - $assigned, 2)
                        : round((float) $prize->price - ($discount * ((float) $prize->price / $subtotal)), 2))
                    : (float) $prize->price;

                $assigned += $amount;
                $ticket = Ticket::create([
                    'id' => 'ticket-'.Str::uuid(),
                    'sale_id' => $sale->id,
                    'draw_id' => $draw->id,
                    'prize_id' => $prize->id,
                    'customer_id' => $customer->id,
                    'ticket_number' => $nextTicketNumber + $index,
                    'purchase_type' => $isCombo ? 'combo' : 'individual',
                    'total_amount' => $amount,
                    'status' => 'pendiente',
                    'payment_method' => $data['payment_method'],
                    'transaction_id' => $data['transaction_id'] ?? null,
                    'voucher_path' => $voucher,
                ]);
                $ticket->prizes()->attach($prize->id, ['unit_price' => $amount]);
            }

            return $sale;
        });

        $request->session()->put('public_participation_sale', $sale->id);

        return redirect()->route('public.participation.confirmation', $sale);
    }

    public function confirmation(Request $request, Sale $sale)
    {
        abort_unless($request->session()->get('public_participation_sale') === $sale->id, 403);

        $sale->load(['draw', 'tickets.prize']);

        return view('public.participation-confirmation', compact('sale'));
    }
}
