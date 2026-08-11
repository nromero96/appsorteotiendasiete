<?php

namespace App\Http\Controllers;

use App\Models\Draw;
use App\Models\Prize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PrizeController extends Controller
{
    public function create(Draw $draw)
    {
        return view('prizes.form', ['draw' => $draw, 'prize' => new Prize]);
    }

    public function store(Request $request, Draw $draw)
    {
        $data = $request->validate(['name' => 'required|max:255', 'description' => 'nullable', 'price' => 'required|numeric|min:0', 'active' => 'nullable|boolean', 'image' => 'nullable|image|max:5120']);
        if ($request->hasFile('image')) $data['image_path'] = $request->file('image')->store('prizes', 'public');
        $data['id'] = 'prize-'.Str::uuid(); $data['draw_id'] = $draw->id; $data['active'] = $request->boolean('active');
        Prize::create($data);
        return redirect()->route('draws.show', $draw)->with('success', 'Premio agregado.');
    }

    public function destroy(Draw $draw, Prize $prize)
    {
        abort_unless($prize->draw_id === $draw->id, 404);

        if ($prize->tickets()->exists()) {
            return back()->with('error', 'No se puede eliminar un premio que ya tiene tickets registrados.');
        }

        if ($prize->image_path) {
            Storage::disk('public')->delete($prize->image_path);
        }

        $prize->delete();
        return back()->with('success', 'Premio eliminado.');
    }

    public function edit(Draw $draw, Prize $prize)
    {
        abort_unless($prize->draw_id === $draw->id, 404);

        return view('prizes.form', compact('draw', 'prize'));
    }

    public function update(Request $request, Draw $draw, Prize $prize)
    {
        abort_unless($prize->draw_id === $draw->id, 404);

        $data = $request->validate([
            'name' => 'required|max:255',
            'description' => 'nullable',
            'price' => 'required|numeric|min:0',
            'active' => 'nullable|boolean',
            'image' => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('image')) {
            if ($prize->image_path) {
                Storage::disk('public')->delete($prize->image_path);
            }
            $data['image_path'] = $request->file('image')->store('prizes', 'public');
        }

        $data['active'] = $request->boolean('active');
        $prize->update($data);

        return redirect()->route('draws.show', $draw)->with('success', 'Premio actualizado.');
    }
}
