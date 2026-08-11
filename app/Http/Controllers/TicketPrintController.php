<?php

namespace App\Http\Controllers;

use App\Models\Draw;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketPrintController extends Controller
{
    public function index(Request $request, Draw $draw)
    {
        $selectedPrize = null;
        $query = $draw->tickets()
            ->where('status', 'aprobado')
            ->with(['customer', 'prize']);

        if ($request->filled('prize_id')) {
            $selectedPrize = $draw->prizes()->whereKey($request->input('prize_id'))->firstOrFail();
            $query->where('prize_id', $selectedPrize->id);
        }

        $tickets = $query
            ->orderBy('ticket_number')
            ->get();

        return view('tickets.print', compact('draw', 'tickets', 'selectedPrize'));
    }

    public function single(Draw $draw, Ticket $ticket)
    {
        abort_unless($ticket->draw_id === $draw->id && $ticket->status === 'aprobado', 404);
        $ticket->load(['customer', 'prize']);

        return view('tickets.print', [
            'draw' => $draw,
            'tickets' => collect([$ticket]),
            'selectedPrize' => $ticket->prize,
        ]);
    }
}
