<?php

namespace App\Http\Controllers;

use App\Models\Draw;

class PublicDrawController extends Controller
{
    public function __invoke()
    {
        $nextDraw = Draw::query()
            ->with(['prizes' => fn ($query) => $query->where('active', true)])
            ->whereDate('draw_date', '>=', today())
            ->orderBy('draw_date')
            ->orderBy('draw_time')
            ->first();

        return view('public.next-draw', compact('nextDraw'));
    }
}
