<?php

namespace App\Http\Controllers;

use App\Models\Draw;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DrawController extends Controller
{
    public function index()
    {
        return view('draws.index', ['draws' => Draw::withCount(['prizes', 'tickets'])->latest('draw_date')->get()]);
    }

    public function show(Draw $draw)
    {
        $draw->load('prizes', 'tickets.customer', 'tickets.prize');
        return view('draws.show', compact('draw'));
    }

    public function report(Draw $draw)
    {
        $sales = $draw->sales()
            ->with(['customer', 'tickets.prize'])
            ->latest()
            ->paginate(15);

        $totals = [
            'sales_count' => $draw->sales()->count(),
            'approved' => $draw->sales()->where('status', 'aprobado')->sum('total_amount'),
            'pending' => $draw->sales()->where('status', 'pendiente')->sum('total_amount'),
            'discounts' => $draw->sales()->sum('discount_amount'),
        ];

        return view('draws.report', compact('draw', 'sales', 'totals'));
    }

    public function create() { return view('draws.form', ['draw' => new Draw, 'prizes' => []]); }

    public function store(Request $request)
    {
        $data = $request->validate(['title' => 'required|string|max:255', 'draw_date' => 'nullable|date', 'draw_time' => 'nullable', 'transmission' => 'nullable|string|max:255', 'combo_discount' => 'nullable|numeric|min:0', 'flyer' => 'nullable|image|max:5120']);
        if ($request->hasFile('flyer')) {
            $data['flyer_path'] = $request->file('flyer')->store('flyers', 'public');
        }
        unset($data['flyer']);
        $draw = Draw::create(['id' => 'draw-'.Str::uuid(), ...$data]);
        return redirect()->route('draws.show', $draw)->with('success', 'Sorteo creado.');
    }

    public function edit(Draw $draw) { return view('draws.form', ['draw' => $draw, 'prizes' => $draw->prizes]); }

    public function update(Request $request, Draw $draw)
    {
        $data = $request->validate(['title' => 'required|string|max:255', 'draw_date' => 'nullable|date', 'draw_time' => 'nullable', 'transmission' => 'nullable|string|max:255', 'combo_discount' => 'nullable|numeric|min:0', 'flyer' => 'nullable|image|max:5120']);
        if ($request->hasFile('flyer')) {
            if ($draw->flyer_path) {
                Storage::disk('public')->delete($draw->flyer_path);
            }
            $data['flyer_path'] = $request->file('flyer')->store('flyers', 'public');
        }
        unset($data['flyer']);
        $draw->update($data);
        return redirect()->route('draws.show', $draw)->with('success', 'Sorteo actualizado.');
    }
}
