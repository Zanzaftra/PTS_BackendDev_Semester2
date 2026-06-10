<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KurirController extends Controller
{
    public function index()
    {$kurir = \App\Models\kurir::all();
    return view('kurir.index', compact('kurir'));

    }
    public function create()
    {
        return view('kurir.create');
    }
    public function store (Request $request)
    {
        $validated = $request->validate([
            'nama_kurir' => 'required|string|max:255',
            'no_hp' => 'required|string|max:15',
            'alamat' => 'required|string',
            'status_kurir' => 'required|string',
            'kendaraan' => 'required|string|max:255',
            'plat_nomor' => 'required|string|max:20',
            'catatan' => 'nullable|string',
        ]);

        \App\Models\kurir::create($validated);

        return redirect()->route('kurir.index')->with('success', 'Kurir berhasil ditambahkan.');
    }
    function edit(string $id)
    {
        $kurir = \App\Models\kurir::findOrFail($id);
        return view('kurir.edit', compact('kurir'));
    }
    function update (Request $request,string $id)
    {
        $validated = $request->validate([
            'nama_kurir' => 'required|string|max:255',
            'no_hp' => 'required|string|max:15',
            'alamat' => 'required|string',
            'status_kurir' => 'required|string',
            'kendaraan' => 'required|string|max:255',
            'plat_nomor' => 'required|string|max:20',
            'catatan' => 'nullable|string',
        ]);

        $kurir = \App\Models\kurir::findOrFail($id);
        $kurir->update($validated);

        return redirect()->route('kurir.index')->with('success', 'data berhasil diubah.');
    }
    public function destroy(string $id)
    {
        $kurir = \App\Models\kurir::findOrFail($id); 
        $kurir->delete();
        return redirect()->route('kurir.index')->with('success', 'data berhasil dihapus.');
    }
}
