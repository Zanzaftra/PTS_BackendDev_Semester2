<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    public function index():view
    {$transaksi = \App\Models\transaksi::all();
    return view('transaksi.index', compact('transaksi'));

    }
    public function create():view
    {
        return view('transaksi.create');
    }
    public function store (Request $request)
    {
        $validated = $request->validate([
            'tanggal_transaksi' => 'required|date',
            'metode_pembayaran' => 'required|string|max:255',
            'total_bayar' => 'required|numeric|min:0',
            'status_transaksi' => 'required|string',
            'kode_invoice' => 'required|string|max:255|unique:transaksis',
            'catatan' => 'nullable|string',
        ]);

        \App\Models\transaksi::create($request ->All());

        return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil ditambahkan.');
    }
    function edit(string $id):view
    {
        $transaksi = \App\Models\transaksi::findOrFail($id);
        return view('transaksi.edit', compact('transaksi'));
    }
    function update (Request $request,string $id)
    {
        $validated = $request->validate([
            'tanggal_transaksi' => 'required|date',
            'metode_pembayaran' => 'required|string|max:255',
            'total_bayar' => 'required|numeric|min:0',
            'status_transaksi' => 'required|string',
            'kode_invoice' => 'required|string|max:255|unique:transaksis,kode_invoice,'.$id,
            'catatan' => 'nullable|string',
        ]);

        $transaksi = \App\Models\transaksi::findOrFail($id);
        $transaksi->update($request ->All());

        return redirect()->route('transaksi.index')->with('success', 'data berhasil diubah.');
    }
    public function destroy(string $id)
    {
        $transaksi = \App\Models\transaksi::findOrFail($id); 
        $transaksi->delete();
        return redirect()->route('transaksi.index')->with('success', 'data berhasil dihapus.');
    }
}
