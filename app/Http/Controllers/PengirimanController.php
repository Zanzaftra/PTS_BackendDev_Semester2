<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PengirimanController extends Controller
{
    public function index()
    {$pengiriman = \App\Models\pengiriman::all();
    return view('pengiriman.index', compact('pengiriman'));

    }
    public function create()
    {
        return view('pengiriman.create');
    }
    public function store (Request $request)
    {
        $validated = $request->validate([
            'id_transaksi' => 'required|integer',
            'id_kurir' => 'nullable|integer',
            'alamat_tujuan' => 'required|string',
            'tanggal_pengiriman' => 'required|date',
            'status_pengiriman' => 'required|string',
            'foto_bukti_pengiriman' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'catatan_kurir' => 'nullable|string',
        ]);

        \App\Models\pengiriman::create($validated);

        return redirect()->route('pengiriman.index')->with('success', 'Pengiriman berhasil ditambahkan.');
    }
    function edit(string $id)
    {
        $pengiriman = \App\Models\pengiriman::findOrFail($id);
        return view('pengiriman.edit', compact('pengiriman'));
    }
    function update (Request $request,string $id)
    {
        $validated = $request->validate([
            'alamat_tujuan' => 'required|string',
            'tanggal_pengiriman' => 'required|date',
            'status_pengiriman' => 'required|string',
            'foto_bukti_pengiriman' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'catatan_kurir' => 'nullable|string',
        ]);

        $pengiriman = \App\Models\pengiriman::findOrFail($id);
        $pengiriman->update($validated);

        return redirect()->route('pengiriman.index')->with('success', 'data berhasil diubah.');
    }
    public function destroy(string $id)
    {
        $pengiriman = \App\Models\pengiriman::findOrFail($id); 
        $pengiriman->delete();
        return redirect()->route('pengiriman.index')->with('success', 'data berhasil dihapus.');
    }
}
