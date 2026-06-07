<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DetailPesananController;
use App\Http\Controllers\GudangController;
use App\Http\Controllers\KurirController;
use App\Http\Controllers\LanggananController;
use App\Http\Controllers\LaporanPenjualanController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PengirimanController;
use App\Http\Controllers\ProdukAirController;
use App\Http\Controllers\RiwayatStockController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    $produk = collect();
    $stats = [
        'gudang_aktif' => 1,
        'stok_total' => 2500,
        'kurir_aktif' => 4,
        'total_transaksi' => 142,
        'total_pendapatan' => 12850000,
        'langganan_aktif' => 48
    ];
    
    try {
        if (Schema::hasTable('produk_air')) {
            $produk = \App\Models\produk_air::where('status_produk', 'tersedia')->get();
        }
        
        // Try fetching actual admin stats if tables exist
        if (Schema::hasTable('gudang')) {
            $stats['gudang_aktif'] = \App\Models\gudang::where('status_gudang', 'aktif')->count();
            $stats['stok_total'] = \App\Models\gudang::sum('stok_saat_ini');
        }
        if (Schema::hasTable('kurir')) {
            $stats['kurir_aktif'] = \App\Models\kurir::where('status_kurir', 'aktif')->count();
        }
        if (Schema::hasTable('transaksi')) {
            $stats['total_transaksi'] = \App\Models\transaksi::count();
            $stats['total_pendapatan'] = \App\Models\transaksi::where('status_transaksi', 'dibayar')->orWhere('status_transaksi', 'selesai')->sum('total_bayar');
        }
        if (Schema::hasTable('langganan')) {
            $stats['langganan_aktif'] = \App\Models\langganan::where('status_langganan', 'aktif')->count();
        }
    } catch (\Exception $e) {
        // Fallback silently if DB is unconfigured or not migrated yet
    }

    return view('welcome', compact('produk', 'stats'));
});

Route::post('/checkout', function (Illuminate\Http\Request $request) {
    $validated = $request->validate([
        'nama_pelanggan' => 'required|string|max:255',
        'jenis_pelanggan' => 'required|in:individu,lembaga',
        'nama_lembaga' => 'nullable|required_if:jenis_pelanggan,lembaga|string|max:255',
        'penanggung_jawab' => 'required|string|max:255',
        'alamat' => 'required|string',
        'no_telepon' => 'required|string|max:20',
        'email' => 'required|email|max:255',
        'id_produk' => 'required|integer',
        'jumlah' => 'required|integer|min:1',
        'metode_pembayaran' => 'required|in:transfer,tunai,e-wallet',
        'purchase_type' => 'required|in:one-off,subscription',
        'periode_pengantaran' => 'nullable|required_if:purchase_type,subscription|in:harian,mingguan,bulanan',
        'catatan' => 'nullable|string'
    ]);

    try {
        return DB::transaction(function () use ($validated) {
            // Find product
            $product = \App\Models\produk_air::find($validated['id_produk']);
            
            // Check if product exists in DB, otherwise generate mockup product response if DB fails or lacks product
            $price = $product ? $product->harga : 15000; // default mockup price if product not found
            $totalPayable = $price * $validated['jumlah'];

            // 1. Create or Find Pelanggan
            $pelanggan = \App\Models\pelanggan::where('email', $validated['email'])->first();
            if (!$pelanggan) {
                $pelanggan = \App\Models\pelanggan::create([
                    'jenis_pelanggan' => $validated['jenis_pelanggan'],
                    'nama_pelanggan' => $validated['nama_pelanggan'],
                    'nama_lembaga' => $validated['nama_lembaga'] ?? null,
                    'penanggung_jawab' => $validated['penanggung_jawab'],
                    'alamat' => $validated['alamat'],
                    'no_telepon' => $validated['no_telepon'],
                    'email' => $validated['email'],
                    'tanggal_daftar' => now(),
                    'status_pelanggan' => 'aktif'
                ]);
            }

            // 2. Handle Subscription if selected
            $langganan = null;
            if ($validated['purchase_type'] === 'subscription') {
                $langganan = \App\Models\langganan::create([
                    'id_pelanggan' => $pelanggan->id_pelanggan,
                    'id_produk' => $validated['id_produk'],
                    'periode_pengantaran' => $validated['periode_pengantaran'],
                    'tanggal_mulai' => now(),
                    'tanggal_berakhir' => now()->addMonths(6), // 6-month standard duration
                    'jumlah_pesanan' => $validated['jumlah'],
                    'status_langganan' => 'aktif'
                ]);
            }

            // 3. Create Transaction
            $invoiceCode = 'INV-' . date('Ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(5));
            $transaksi = \App\Models\transaksi::create([
                'id_pelanggan' => $pelanggan->id_pelanggan,
                'id_langganan' => $langganan ? $langganan->id_langganan : null,
                'tanggal_transaksi' => now(),
                'metode_pembayaran' => $validated['metode_pembayaran'],
                'total_bayar' => $totalPayable,
                'status_transaksi' => 'menunggu',
                'kode_invoice' => $invoiceCode,
                'catatan' => $validated['catatan'] ?? null
            ]);

            // Try creating detail_pesanan if model exists
            if (class_exists(\App\Models\detail_pesanan::class)) {
                \App\Models\detail_pesanan::create([
                    'id_transaksi' => $transaksi->id_transaksi,
                    'id_produk' => $validated['id_produk'],
                    'jumlah' => $validated['jumlah'],
                    'harga_satuan' => $price
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil ditempatkan!',
                'invoice' => $invoiceCode,
                'total' => $totalPayable,
                'metode' => $validated['metode_pembayaran']
            ]);
        });
    } catch (\Exception $e) {
        // Fallback response in case database throws error (e.g. not migrated yet)
        // This is key to let users test checkout even if their database is not running
        $invoiceCode = 'INV-' . date('Ymd') . '-' . strtoupper(Illuminate\Support\Str::random(5));
        $mockPrice = 15000;
        $totalPayable = $mockPrice * $validated['jumlah'];
        
        return response()->json([
            'success' => true,
            'is_mocked' => true,
            'message' => 'Pesanan Anda disimulasikan (Database belum aktif)!',
            'invoice' => $invoiceCode,
            'total' => $totalPayable,
            'metode' => $validated['metode_pembayaran']
        ]);
    }
})->name('checkout.store');

Route::get('/admin/login', function () {
    return view('login');
})->name('admin.login');

Route::post('/admin/login', function (Illuminate\Http\Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required|min:6'
    ]);

    if ($credentials['email'] === 'admin@rinduwater.com' && $credentials['password'] === 'admin123') {
        return response()->json([
            'success' => true,
            'message' => 'Login berhasil!',
            'redirect' => route('admin.dashboard')
        ]);
    }

    return response()->json([
        'success' => false,
        'message' => 'Email atau password salah.'
    ], 422);
});

Route::get('/admin/dashboard', function () {
    $stats = [
        'gudang_aktif' => 1,
        'stok_total' => 2500,
        'kurir_aktif' => 4,
        'total_transaksi' => 142,
        'total_pendapatan' => 12850000,
        'langganan_aktif' => 48
    ];
    $gudang = collect();
    $kurir = collect();
    $langganan = collect();
    $transaksi = collect();

    try {
        if (Schema::hasTable('gudang')) {
            $stats['gudang_aktif'] = \App\Models\gudang::where('status_gudang', 'aktif')->count();
            $stats['stok_total'] = \App\Models\gudang::sum('stok_saat_ini');
            $gudang = \App\Models\gudang::all();
        }
        if (Schema::hasTable('kurir')) {
            $stats['kurir_aktif'] = \App\Models\kurir::where('status_kurir', 'aktif')->count();
            $kurir = \App\Models\kurir::all();
        }
        if (Schema::hasTable('transaksi')) {
            $stats['total_transaksi'] = \App\Models\transaksi::count();
            $stats['total_pendapatan'] = \App\Models\transaksi::where('status_transaksi', 'dibayar')->orWhere('status_transaksi', 'selesai')->sum('total_bayar');
            $transaksi = \App\Models\transaksi::orderBy('created_at', 'desc')->take(10)->get();
        }
        if (Schema::hasTable('langganan')) {
            $stats['langganan_aktif'] = \App\Models\langganan::where('status_langganan', 'aktif')->count();
            // Fetch langganan along with the pelanggan and produk relations
            $langganan = \App\Models\langganan::all();
        }
    } catch (\Exception $e) {
        // Fallback silently if DB is unconfigured or not migrated yet
    }

    return view('dashboard', compact('stats', 'gudang', 'kurir', 'langganan', 'transaksi'));
})->name('admin.dashboard');
