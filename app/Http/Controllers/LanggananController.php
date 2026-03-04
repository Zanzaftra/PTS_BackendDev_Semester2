<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LanggananController extends Controller
{
    public function index():view
    {$langganan = \App\Models\langganan::all();
    return view('langganan.index', compact('langganan'));

    }
    public function create():view
    {
        return view('langganan.create');
    }
    public function store (Request $request)
    {
        $validated = $request->validate([
            'periode_pengantaran' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_berakhir' => 'required|date',
            'jumlah_pesanan' => 'required|integer|min:1',
            'status_langganan' => 'required|string',
        ]);

        \App\Models\langganan::create($request ->All());

        return redirect()->route('langganan.index')->with('success', 'Langganan berhasil ditambahkan.');
    }
    function edit(string $id):view
    {
        $langganan = \App\Models\langganan::findOrFail($id);
        return view('langganan.edit', compact('langganan'));
    }
    function update (Request $request,string $id)
    {
        $validated = $request->validate([
            'periode_pengantaran' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_berakhir' => 'required|date',
            'jumlah_pesanan' => 'required|integer|min:1',
            'status_langganan' => 'required|string',
        ]);

        $langganan = \App\Models\langganan::findOrFail($id);
        $langganan->update($request ->All());

        return redirect()->route('langganan.index')->with('success', 'data berhasil diubah.');
    }
    public function destroy(string $id)
    {
        $langganan = \App\Models\langganan::findOrFail($id); 
        $langganan->delete();
        return redirect()->route('langganan.index')->with('success', 'data berhasil dihapus.');
    }
}
