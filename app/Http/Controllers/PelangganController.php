<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PelangganController extends Controller
{
    public function index():view
    {$pelanggan = \App\Models\pelanggan::all();
    return view('pelanggan.index', compact('pelanggan'));

    }
    public function create():view
    {
        return view('pelanggan.create');
    }
    public function store (Request $request)
    {
        $validated = $request->validate([
            'jenis_pelanggan' => 'required|string|max:255',
            'nama_pelanggan' => 'required|string|max:255',
            'nama_lembaga' => 'nullable|string|max:255',
            'penanggung_jawab' => 'nullable|string|max:255',
            'alamat' => 'required|string',
            'no_telepon' => 'required|string|max:15',
            'email' => 'required|email|max:100|unique:pelanggans',
            'tanggal_daftar' => 'required|date',
            'status_pelanggan' => 'required|string',
        ]);

        \App\Models\pelanggan::create($request ->All());

        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil ditambahkan.');
    }
    function edit(string $id):view
    {
        $pelanggan = \App\Models\pelanggan::findOrFail($id);
        return view('pelanggan.edit', compact('pelanggan'));
    }
    function update (Request $request,string $id)
    {
        $validated = $request->validate([
            'jenis_pelanggan' => 'required|string|max:255',
            'nama_pelanggan' => 'required|string|max:255',
            'nama_lembaga' => 'nullable|string|max:255',
            'penanggung_jawab' => 'nullable|string|max:255',
            'alamat' => 'required|string',
            'no_telepon' => 'required|string|max:15',
            'email' => 'required|email|max:100|unique:pelanggans,email,'.$id,
            'tanggal_daftar' => 'required|date',
            'status_pelanggan' => 'required|string',
        ]);

        $pelanggan = \App\Models\pelanggan::findOrFail($id);
        $pelanggan->update($request ->All());

        return redirect()->route('pelanggan.index')->with('success', 'data berhasil diubah.');
    }
    public function destroy(string $id)
    {
        $pelanggan = \App\Models\pelanggan::findOrFail($id); 
        $pelanggan->delete();
        return redirect()->route('pelanggan.index')->with('success', 'data berhasil dihapus.');
    }
}
