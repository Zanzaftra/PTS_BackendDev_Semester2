<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LaporanPenjualanController extends Controller
{
    public function index():view
    {$laporan_penjualan = \App\Models\laporan_penjualan::all();
    return view('laporan_penjualan.index', compact('laporan_penjualan'));

    }
    public function create():view
    {
        return view('laporan_penjualan.create');
    }
    public function store (Request $request)
    {
        $validated = $request->validate([
            'periode_laporan' => 'required|string|max:255',
            'total_transaksi' => 'required|integer|min:0',
            'total_pendapatan' => 'required|numeric|min:0',
            'produk_terlaris' => 'nullable|string|max:255',
            'tanggal_dibuat' => 'required|date',
        ]);

        \App\Models\laporan_penjualan::create($request ->All());

        return redirect()->route('laporan_penjualan.index')->with('success', 'Laporan Penjualan berhasil ditambahkan.');
    }
    function edit(string $id):view
    {
        $laporan_penjualan = \App\Models\laporan_penjualan::findOrFail($id);
        return view('laporan_penjualan.edit', compact('laporan_penjualan'));
    }
    function update (Request $request,string $id)
    {
        $validated = $request->validate([
            'periode_laporan' => 'required|string|max:255',
            'total_transaksi' => 'required|integer|min:0',
            'total_pendapatan' => 'required|numeric|min:0',
            'produk_terlaris' => 'nullable|string|max:255',
            'tanggal_dibuat' => 'required|date',
        ]);

        $laporan_penjualan = \App\Models\laporan_penjualan::findOrFail($id);
        $laporan_penjualan->update($request ->All());

        return redirect()->route('laporan_penjualan.index')->with('success', 'data berhasil diubah.');
    }
    public function destroy(string $id)
    {
        $laporan_penjualan = \App\Models\laporan_penjualan::findOrFail($id); 
        $laporan_penjualan->delete();
        return redirect()->route('laporan_penjualan.index')->with('success', 'data berhasil dihapus.');
    }
}
