<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GudangController extends Controller
{
    public function index():view
    {$gudang = \App\Models\gudang::all();
    return view('gudang.index', compact('gudang'));

    }
    public function create():view
    {
        return view('gudang.create');
    }
    public function store (Request $request)
    {
        $validated = $request->validate([
            'nama_gudang' => 'required|string|max:255',
            'lokasi' => 'required|string|max:255',
            'kapasitas_total' => 'required|numeric|min:0',
            'stok_saat_ini' => 'required|numeric|min:0',
            'status_gudang' => 'required|string',
        ]);

        \App\Models\gudang::create($request ->All());

        return redirect()->route('gudang.index')->with('success', 'Gudang berhasil ditambahkan.');
    }
    function edit(string $id):view
    {
        $gudang = \App\Models\gudang::findOrFail($id);
        return view('gudang.edit', compact('gudang'));
    }
    function update (Request $request,string $id)
    {
        $validated = $request->validate([
            'nama_gudang' => 'required|string|max:255',
            'lokasi' => 'required|string|max:255',
            'kapasitas_total' => 'required|numeric|min:0',
            'stok_saat_ini' => 'required|numeric|min:0',
            'status_gudang' => 'required|string',
        ]);

        $gudang = \App\Models\gudang::findOrFail($id);
        $gudang->update($request ->All());

        return redirect()->route('gudang.index')->with('success', 'data berhasil diubah.');
    }
    public function destroy(string $id)
    {
        $gudang = \App\Models\gudang::findOrFail($id); 
        $gudang->delete();
        return redirect()->route('gudang.index')->with('success', 'data berhasil dihapus.');
    }
}
