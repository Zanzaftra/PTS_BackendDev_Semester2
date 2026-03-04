<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProdukAirController extends Controller
{
    public function index():view
    {$produk_air = \App\Models\produk_air::all();
    return view('produk_air.index', compact('produk_air'));

    }
    public function create():view
    {
        return view('produk_air.create');
    }
    public function store (Request $request)
    {
        $validated = $request->validate([
            'nama_produk' => 'required|string|max:255',
            'jenis_kemasan' => 'required|string|max:255',
            'kapasitas' => 'required|numeric|min:0',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'status_produk' => 'required|string',
            'tanggal_ditambahkan' => 'required|date',
            'foto_produk' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'deskripsi' => 'nullable|string',
        ]);

        \App\Models\produk_air::create($request ->All());

        return redirect()->route('produk_air.index')->with('success', 'Produk Air berhasil ditambahkan.');
    }
    function edit(string $id):view
    {
        $produk_air = \App\Models\produk_air::findOrFail($id);
        return view('produk_air.edit', compact('produk_air'));
    }
    function update (Request $request,string $id)
    {
        $validated = $request->validate([
            'nama_produk' => 'required|string|max:255',
            'jenis_kemasan' => 'required|string|max:255',
            'kapasitas' => 'required|numeric|min:0',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'status_produk' => 'required|string',
            'tanggal_ditambahkan' => 'required|date',
            'foto_produk' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'deskripsi' => 'nullable|string',
        ]);

        $produk_air = \App\Models\produk_air::findOrFail($id);
        $produk_air->update($request ->All());

        return redirect()->route('produk_air.index')->with('success', 'data berhasil diubah.');
    }
    public function destroy(string $id)
    {
        $produk_air = \App\Models\produk_air::findOrFail($id); 
        $produk_air->delete();
        return redirect()->route('produk_air.index')->with('success', 'data berhasil dihapus.');
    }
}
