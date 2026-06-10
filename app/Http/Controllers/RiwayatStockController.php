<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RiwayatStockController extends Controller
{
    public function index()
    {$riwayat_stock = \App\Models\riwayat_stock::all();
    return view('riwayat_stock.index', compact('riwayat_stock'));

    }
    public function create()
    {
        return view('riwayat_stock.create');
    }
    public function store (Request $request)
    {
        $validated = $request->validate([
            'id_produk' => 'required|integer',
            'jenis_perubahan' => 'required|string|max:255',
            'jumlah' => 'required|integer|min:0',
            'tanggal_perubahan' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        \App\Models\riwayat_stock::create($validated);

        return redirect()->route('riwayat_stock.index')->with('success', 'Riwayat Stock berhasil ditambahkan.');
    }
    function edit(string $id)
    {
        $riwayat_stock = \App\Models\riwayat_stock::findOrFail($id);
        return view('riwayat_stock.edit', compact('riwayat_stock'));
    }
    function update (Request $request,string $id)
    {
        $validated = $request->validate([
            'jenis_perubahan' => 'required|string|max:255',
            'jumlah' => 'required|integer|min:0',
            'tanggal_perubahan' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        $riwayat_stock = \App\Models\riwayat_stock::findOrFail($id);
        $riwayat_stock->update($validated);

        return redirect()->route('riwayat_stock.index')->with('success', 'data berhasil diubah.');
    }
    public function destroy(string $id)
    {
        $riwayat_stock = \App\Models\riwayat_stock::findOrFail($id); 
        $riwayat_stock->delete();
        return redirect()->route('riwayat_stock.index')->with('success', 'data berhasil dihapus.');
    }
}
