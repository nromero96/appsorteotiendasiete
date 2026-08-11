<?php

namespace App\Http\Controllers;

use App\Models\Draw;
use App\Models\Sale;
use App\Models\Ticket;

class DashboardController extends Controller
{
    public function index()
    {
        $upcomingDraws = Draw::query()
            ->withCount(['prizes', 'tickets'])
            ->whereDate('draw_date', '>=', today())
            ->orderBy('draw_date')
            ->orderBy('draw_time')
            ->take(5)
            ->get();

        $nextDraw = $upcomingDraws->first();

        $stats = [
            'draws' => Draw::count(),
            'tickets' => Ticket::count(),
            'approved_sales' => Sale::where('status', 'aprobado')->sum('total_amount'),
            'pending_tickets' => Ticket::where('status', 'pendiente')->count(),
        ];

        $recentTickets = Ticket::query()
            ->with(['customer', 'draw', 'prize'])
            ->latest()
            ->take(6)
            ->get();

        return view('dashboard.index', compact('nextDraw', 'upcomingDraws', 'stats', 'recentTickets'));
    }
}
