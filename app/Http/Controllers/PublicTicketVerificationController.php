<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class PublicTicketVerificationController extends Controller
{
    public function index(Request $request)
    {
        $tickets = collect();
        $searched = $request->filled('document_number');

        if ($searched) {
            $data = $request->validate([
                'document_number' => ['required', 'string', 'max:50'],
            ]);

            $tickets = Ticket::query()
                ->with(['draw', 'prize', 'customer'])
                ->whereHas('customer', fn ($customer) => $customer->where('document_number', $data['document_number']))
                ->whereIn('status', ['pendiente', 'aprobado'])
                ->latest()
                ->get();
        }

        return view('public.ticket-verification', compact('tickets', 'searched'));
    }
}
