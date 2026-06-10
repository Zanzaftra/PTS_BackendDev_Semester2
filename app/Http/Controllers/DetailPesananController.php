<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DetailPesananController extends Controller
{
    public function index()
    {$detail_pesanan = \App\Models\detail_pesanan::all();
    return view('detail_pesanan.index', compact('detail_pesanan'));

    }
    public function create()
    {
        return view('detail_pesanan.create');
    }
    public function store (Request $request)
    {
        $validated = $request->validate([
            'id_transaksi' => 'required|integer',
            'id_produk' => 'required|integer',
            'jumlah' => 'required|integer|min:1',
            'harga_satuan' => 'required|numeric|min:0',
        ]);

        \App\Models\detail_pesanan::create($validated);

        return redirect()->route('detail_pesanan.index')->with('success', 'Detail pesanan berhasil ditambahkan.');
    }
    function edit(string $id)
    {
        $detail_pesanan = \App\Models\detail_pesanan::findOrFail($id);
        return view('detail_pesanan.edit', compact('detail_pesanan'));
    }
    function update (Request $request,string $id)
    {
        $validated = $request->validate([
            'jumlah' => 'required|integer|min:1',
            'harga_satuan' => 'required|numeric|min:0',
        ]);

        $detail_pesanan = \App\Models\detail_pesanan::findOrFail($id);
        $detail_pesanan->update($validated);

        return redirect()->route('detail_pesanan.index')->with('success', 'data berhasil diubah.');
    }
    public function destroy(string $id)
    {
        $detail_pesanan = \App\Models\detail_pesanan::findOrFail($id); 
        $detail_pesanan->delete();
        return redirect()->route('detail_pesanan.index')->with('success', 'data berhasil dihapus.');
    }
}
