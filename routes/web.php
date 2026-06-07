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

if (!function_exists('mock_customer_user')) {
    function mock_customer_user(string $email): ?array
    {
        return session('mock_customer_users.' . $email);
    }
}

if (!function_exists('store_mock_customer_user')) {
    function store_mock_customer_user(array $user): void
    {
        session()->put('mock_customer_users.' . $user['email'], $user);
    }
}

if (!function_exists('mock_admin_user')) {
    function mock_admin_user(string $email): ?array
    {
        return session('mock_admin_users.' . $email);
    }
}

if (!function_exists('store_mock_admin_user')) {
    function store_mock_admin_user(array $user): void
    {
        session()->put('mock_admin_users.' . $user['email'], $user);
    }
}

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

    // 1. Check demo credentials
    if ($credentials['email'] === 'admin@rinduwater.com' && $credentials['password'] === 'admin123') {
        session(['admin_logged_in' => true, 'admin_email' => $credentials['email']]);
        return response()->json([
            'success' => true,
            'message' => 'Login berhasil!',
            'redirect' => route('admin.dashboard')
        ]);
    }

    $sessionUser = mock_admin_user($credentials['email']);
    if ($sessionUser && (Hash::check($credentials['password'], $sessionUser['password']) || $credentials['password'] === $sessionUser['password'])) {
        session([
            'admin_logged_in' => true,
            'admin_id' => $sessionUser['id_admin'] ?? null,
            'admin_name' => $sessionUser['nama_admin'] ?? $sessionUser['name'] ?? $credentials['email'],
            'admin_email' => $sessionUser['email']
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Login berhasil!',
            'redirect' => route('admin.dashboard')
        ]);
    }

    // 2. Check Database if migrated
    try {
        if (Schema::hasTable('admin')) {
            $adminUser = \App\Models\admin::where('email', $credentials['email'])->first();
            if ($adminUser) {
                if (Hash::check($credentials['password'], $adminUser->password) || $credentials['password'] === $adminUser->password) {
                    session([
                        'admin_logged_in' => true, 
                        'admin_id' => $adminUser->id_admin,
                        'admin_name' => $adminUser->nama_admin,
                        'admin_email' => $adminUser->email
                    ]);
                    return response()->json([
                        'success' => true,
                        'message' => 'Login berhasil!',
                        'redirect' => route('admin.dashboard')
                    ]);
                }
            }
        }
    } catch (\Exception $e) {
        // Fail silently
    }

    return response()->json([
        'success' => false,
        'message' => 'Email atau password salah.'
    ], 422);
});

Route::get('/admin/register', function () {
    return view('register');
})->name('admin.register');

Route::post('/admin/register', function (Illuminate\Http\Request $request) {
    $validated = $request->validate([
        'nama_admin' => 'required|string|max:255',
        'email' => 'required|email|max:100',
        'password' => 'required|string|min:6|confirmed',
        'kode_perusahaan' => 'required|string|in:PRIMA'
    ]);

    try {
        if (Schema::hasTable('admin')) {
            if (\App\Models\admin::where('email', $validated['email'])->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email sudah terdaftar.'
                ], 422);
            }
            \App\Models\admin::create([
                'nama_admin' => $validated['nama_admin'],
                'username' => 'admin-' . strtolower(str_replace(' ', '', $validated['nama_admin'])) . '-' . rand(100, 999),
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'no_hp' => null,
                'kode_perusahaan' => strtoupper($validated['kode_perusahaan']),
                'role' => 'staff',
                'status_admin' => 'aktif'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pendaftaran admin berhasil! Silakan masuk.',
                'redirect' => route('admin.login')
            ]);
        }
    } catch (\Exception $e) {
        // Fallback for mocked preview if DB fails
    }

    store_mock_admin_user([
        'id_admin' => null,
        'nama_admin' => $validated['nama_admin'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
    ]);

    return response()->json([
        'success' => true,
        'is_mocked' => true,
        'message' => 'Registrasi admin berhasil disimpan untuk login sementara. Silakan login!',
        'redirect' => route('admin.login')
    ]);
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
    $pelanggan = collect();

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
            $transaksi = \App\Models\transaksi::orderBy('created_at', 'desc')->get();
        }
        if (Schema::hasTable('langganan')) {
            $stats['langganan_aktif'] = \App\Models\langganan::where('status_langganan', 'aktif')->count();
            $langganan = \App\Models\langganan::all();
        }
        if (Schema::hasTable('pelanggan')) {
            $pelanggan = \App\Models\pelanggan::all();
        }
    } catch (\Exception $e) {
        // Fallback silently if DB is unconfigured
    }

    return view('dashboard', compact('stats', 'gudang', 'kurir', 'langganan', 'transaksi', 'pelanggan'));
})->name('admin.dashboard');

// ================= CUSTOMER PORTAL ROUTES =================

Route::get('/customer/login', function () {
    return view('customer.login');
})->name('customer.login');

Route::post('/customer/login', function (Illuminate\Http\Request $request) {
    $validated = $request->validate([
        'email' => 'required|email',
        'password' => 'required|min:6'
    ]);

    $sessionUser = mock_customer_user($validated['email']);
    if ($sessionUser && (Hash::check($validated['password'], $sessionUser['password']) || $validated['password'] === $sessionUser['password'])) {
        session([
            'customer_logged_in' => true,
            'customer_id' => $sessionUser['id_pelanggan'] ?? null,
            'customer_name' => $sessionUser['nama_pelanggan'] ?? $sessionUser['name'] ?? $validated['email'],
            'customer_email' => $sessionUser['email']
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Login berhasil!',
            'redirect' => route('customer.dashboard')
        ]);
    }

    try {
        if (Schema::hasTable('pelanggan')) {
            $pelanggan = \App\Models\pelanggan::where('email', $validated['email'])->first();
            if ($pelanggan) {
                if ($pelanggan->password && (Hash::check($validated['password'], $pelanggan->password) || $validated['password'] === $pelanggan->password)) {
                    session([
                        'customer_logged_in' => true,
                        'customer_id' => $pelanggan->id_pelanggan,
                        'customer_name' => $pelanggan->nama_pelanggan,
                        'customer_email' => $pelanggan->email
                    ]);
                    return response()->json([
                        'success' => true,
                        'message' => 'Login berhasil!',
                        'redirect' => route('customer.dashboard')
                    ]);
                }
            }
        }
    } catch (\Exception $e) {
        // Fail silently
    }

    // Demo fallback for preview
    if (($validated['email'] === 'zaki@example.com' || $validated['email'] === 'customer@rinduwater.com') && $validated['password'] === 'customer123') {
        session([
            'customer_logged_in' => true,
            'customer_id' => 1,
            'customer_name' => 'Zaki Zulfikar',
            'customer_email' => $validated['email']
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Login demo berhasil!',
            'redirect' => route('customer.dashboard')
        ]);
    }

    return response()->json([
        'success' => false,
        'message' => 'Email atau password salah.'
    ], 422);
});

Route::get('/customer/register', function () {
    return view('customer.register');
})->name('customer.register');

Route::post('/customer/register', function (Illuminate\Http\Request $request) {
    $validated = $request->validate([
        'nama_pelanggan' => 'required|string|max:255',
        'email' => 'required|email|max:100',
        'password' => 'required|string|min:6|confirmed'
    ]);

    try {
        if (Schema::hasTable('pelanggan')) {
            if (\App\Models\pelanggan::where('email', $validated['email'])->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email sudah terdaftar.'
                ], 422);
            }
            if (\App\Models\pelanggan::where('nama_pelanggan', $validated['nama_pelanggan'])->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nama pelanggan sudah digunakan.'
                ], 422);
            }

            \App\Models\pelanggan::create([
                'nama_pelanggan' => $validated['nama_pelanggan'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'no_telepon' => '0000000000',
                'alamat' => 'Alamat belum diisi',
                'jenis_pelanggan' => 'individu',
                'nama_lembaga' => null,
                'penanggung_jawab' => $validated['nama_pelanggan'],
                'tanggal_daftar' => now(),
                'status_pelanggan' => 'aktif'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pendaftaran berhasil! Silakan masuk.',
                'redirect' => route('customer.login')
            ]);
        }
    } catch (\Exception $e) {
        // Fallback for mocked preview if DB fails
    }

    store_mock_customer_user([
        'id_pelanggan' => null,
        'nama_pelanggan' => $validated['nama_pelanggan'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
    ]);

    return response()->json([
        'success' => true,
        'is_mocked' => true,
        'message' => 'Registrasi pelanggan berhasil disimpan untuk login sementara. Silakan login!',
        'redirect' => route('customer.login')
    ]);
});

Route::get('/customer/dashboard', function () {
    if (!session('customer_logged_in')) {
        return redirect()->route('customer.login');
    }

    $customerId = session('customer_id');
    $customerEmail = session('customer_email');

    $customer = null;
    $transaksi = collect();
    $langganan = collect();

    try {
        if (Schema::hasTable('pelanggan')) {
            $customer = \App\Models\pelanggan::find($customerId) ?? \App\Models\pelanggan::where('email', $customerEmail)->first();
        }
        if ($customer) {
            $customerId = $customer->id_pelanggan;
            if (Schema::hasTable('transaksi')) {
                $transaksi = \App\Models\transaksi::where('id_pelanggan', $customerId)->orderBy('created_at', 'desc')->get();
            }
            if (Schema::hasTable('langganan')) {
                $langganan = \App\Models\langganan::where('id_pelanggan', $customerId)->get();
            }
        }
    } catch (\Exception $e) {
        // Fallback silently if DB is unconfigured
    }

    if (!$customer) {
        $customer = (object) [
            'id_pelanggan' => session('customer_id'),
            'nama_pelanggan' => session('customer_name') ?: 'Pelanggan',
            'email' => session('customer_email') ?: '',
            'no_telepon' => '0812-0000-0000',
            'alamat' => 'Alamat belum diisi',
            'jenis_pelanggan' => 'individu',
        ];
    }

    $stats = [
        'total_pesanan' => $transaksi ? $transaksi->count() : 0,
        'total_belanja' => $transaksi ? $transaksi->sum('total_bayar') : 0,
        'langganan_aktif' => $langganan ? $langganan->where('status_langganan', 'aktif')->count() : 0,
        'riwayat_terbaru' => $transaksi ? $transaksi->take(5) : collect(),
    ];

    return view('customer.dashboard', compact('customer', 'transaksi', 'langganan', 'stats'));
})->name('customer.dashboard');

Route::get('/customer/beli', function () {
    if (!session('customer_logged_in')) {
        return redirect()->route('customer.login');
    }

    return view('customer.beli', [
        'customer' => (object) [
            'nama_pelanggan' => session('customer_name') ?: 'Pelanggan',
            'email' => session('customer_email') ?: '',
            'no_telepon' => '',
            'alamat' => '',
        ],
    ]);
})->name('customer.beli');

Route::post('/customer/logout', function () {
    session()->forget(['customer_logged_in', 'customer_id', 'customer_name', 'customer_email']);
    return response()->json([
        'success' => true,
        'redirect' => route('customer.login')
    ]);
})->name('customer.logout');


// ================= ADMIN JSON API ENDPOINTS =================

// Update transaction status
Route::post('/admin/api/transaksi/{id}/status', function (Illuminate\Http\Request $request, $id) {
    $validated = $request->validate([
        'status' => 'required|in:menunggu,dibayar,diproses,dikirim,selesai,batal'
    ]);

    try {
        if (Schema::hasTable('transaksi')) {
            $t = \App\Models\transaksi::find($id);
            if ($t) {
                $t->status_transaksi = $validated['status'];
                $t->save();
                return response()->json(['success' => true, 'message' => 'Status transaksi berhasil diperbarui.', 'data' => $t]);
            }
            return response()->json(['success' => false, 'message' => 'Transaksi tidak ditemukan.'], 404);
        }
    } catch (\Exception $e) {}

    // Mocked response for offline dev
    return response()->json(['success' => true, 'is_mocked' => true, 'message' => 'Status (simulasi) diperbarui.', 'status' => $validated['status']]);
});

// Update customer status
Route::post('/admin/api/pelanggan/{id}/status', function (Illuminate\Http\Request $request, $id) {
    $validated = $request->validate([
        'status' => 'required|in:aktif,tidak aktif'
    ]);

    try {
        if (Schema::hasTable('pelanggan')) {
            $p = \App\Models\pelanggan::find($id);
            if ($p) {
                $p->status_pelanggan = $validated['status'];
                $p->save();
                return response()->json(['success' => true, 'message' => 'Status pelanggan berhasil diperbarui.', 'data' => $p]);
            }
            return response()->json(['success' => false, 'message' => 'Pelanggan tidak ditemukan.'], 404);
        }
    } catch (\Exception $e) {}

    return response()->json(['success' => true, 'is_mocked' => true, 'message' => 'Status pelanggan (simulasi) diperbarui.', 'status' => $validated['status']]);
});

// Create new gudang
Route::post('/admin/api/gudang', function (Illuminate\Http\Request $request) {
    $validated = $request->validate([
        'nama_gudang'     => 'required|string|max:255',
        'lokasi'          => 'required|string|max:255',
        'kapasitas_total' => 'required|integer|min:1',
        'stok_saat_ini'   => 'required|integer|min:0',
        'status_gudang'   => 'required|in:aktif,penuh,maintenance',
    ]);

    try {
        if (Schema::hasTable('gudang')) {
            $g = \App\Models\gudang::create($validated);
            return response()->json(['success' => true, 'message' => 'Gudang berhasil ditambahkan.', 'data' => $g]);
        }
    } catch (\Exception $e) {}

    // Mocked fallback
    $mockId = rand(10, 999);
    return response()->json([
        'success' => true, 'is_mocked' => true, 'message' => 'Gudang (simulasi) berhasil ditambahkan.',
        'data' => array_merge(['id_gudang' => $mockId], $validated)
    ]);
});

// Update existing gudang stock/capacity
Route::post('/admin/api/gudang/{id}/update', function (Illuminate\Http\Request $request, $id) {
    $validated = $request->validate([
        'stok_saat_ini'   => 'sometimes|integer|min:0',
        'kapasitas_total' => 'sometimes|integer|min:1',
        'status_gudang'   => 'sometimes|in:aktif,penuh,maintenance',
    ]);

    try {
        if (Schema::hasTable('gudang')) {
            $g = \App\Models\gudang::find($id);
            if ($g) {
                $g->fill($validated)->save();
                return response()->json(['success' => true, 'message' => 'Gudang berhasil diperbarui.', 'data' => $g]);
            }
            return response()->json(['success' => false, 'message' => 'Gudang tidak ditemukan.'], 404);
        }
    } catch (\Exception $e) {}

    return response()->json(['success' => true, 'is_mocked' => true, 'message' => 'Gudang (simulasi) diperbarui.']);
});

// Create new courier
Route::post('/admin/api/kurir', function (Illuminate\Http\Request $request) {
    $validated = $request->validate([
        'nama_kurir'   => 'required|string|max:255',
        'no_hp'        => 'required|string|max:30',
        'alamat'       => 'nullable|string',
        'kendaraan'    => 'required|string|max:100',
        'plat_nomor'   => 'required|string|max:20',
        'status_kurir' => 'required|in:aktif,nonaktif',
        'catatan'      => 'nullable|string',
    ]);

    try {
        if (Schema::hasTable('kurir')) {
            $k = \App\Models\kurir::create($validated);
            return response()->json(['success' => true, 'message' => 'Kurir berhasil ditambahkan.', 'data' => $k]);
        }
    } catch (\Exception $e) {}

    $mockId = rand(10, 999);
    return response()->json([
        'success' => true, 'is_mocked' => true, 'message' => 'Kurir (simulasi) berhasil ditambahkan.',
        'data' => array_merge(['id_kurir' => $mockId], $validated)
    ]);
});

// Update existing kurir status/details
Route::post('/admin/api/kurir/{id}/update', function (Illuminate\Http\Request $request, $id) {
    $validated = $request->validate([
        'status_kurir' => 'sometimes|in:aktif,nonaktif',
        'kendaraan'    => 'sometimes|string|max:100',
        'plat_nomor'   => 'sometimes|string|max:20',
        'catatan'      => 'sometimes|nullable|string',
    ]);

    try {
        if (Schema::hasTable('kurir')) {
            $k = \App\Models\kurir::find($id);
            if ($k) {
                $k->fill($validated)->save();
                return response()->json(['success' => true, 'message' => 'Kurir berhasil diperbarui.', 'data' => $k]);
            }
            return response()->json(['success' => false, 'message' => 'Kurir tidak ditemukan.'], 404);
        }
    } catch (\Exception $e) {}

    return response()->json(['success' => true, 'is_mocked' => true, 'message' => 'Kurir (simulasi) diperbarui.']);
});

// Update subscription status
Route::post('/admin/api/langganan/{id}/status', function (Illuminate\Http\Request $request, $id) {
    $validated = $request->validate([
        'status' => 'required|in:aktif,nonaktif,selesai'
    ]);

    try {
        if (Schema::hasTable('langganan')) {
            $l = \App\Models\langganan::find($id);
            if ($l) {
                $l->status_langganan = $validated['status'];
                $l->save();
                return response()->json(['success' => true, 'message' => 'Status langganan berhasil diperbarui.', 'data' => $l]);
            }
            return response()->json(['success' => false, 'message' => 'Langganan tidak ditemukan.'], 404);
        }
    } catch (\Exception $e) {}

    return response()->json(['success' => true, 'is_mocked' => true, 'message' => 'Status langganan (simulasi) diperbarui.', 'status' => $validated['status']]);
});
