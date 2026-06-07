@php
    $useVite = file_exists(public_path('hot')) || file_exists(public_path('build/manifest.json'));
@endphp
<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Dashboard Admin Rindu Water - Kelola stok, kurir, langganan, dan transaksi.">
    <title>Dashboard Admin &mdash; Rindu Water</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;900&display=swap" rel="stylesheet">

    @if ($useVite)
        @vite(['resources/css/app.css', 'resources/js/dashboard-app.jsx'])
    @else
        <!-- Fallback CDN Libraries for Instant Out-of-the-Box Execution -->
        <script src="https://unpkg.com/react@18/umd/react.production.min.js" crossorigin></script>
        <script src="https://unpkg.com/react-dom@18/umd/react-dom.production.min.js" crossorigin></script>
        <script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                darkMode: 'class',
                theme: {
                    extend: {
                        colors: {
                            brand: {
                                50: '#f0f7ff',
                                100: '#e0f2fe',
                                200: '#bae6fd',
                                300: '#7dd3fc',
                                400: '#38bdf8',
                                500: '#0ea5e9',
                                600: '#2563eb',
                                700: '#1d4ed8',
                                800: '#1e40af',
                                900: '#1e3a8a',
                            }
                        }
                    }
                }
            }
        </script>
    @endif

    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body { margin: 0; padding: 0; font-family: 'Inter', system-ui, sans-serif; background: #0f172a; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeIn { animation: fadeIn 0.3s ease forwards; }
    </style>
</head>
<body>
    {{-- Pass Laravel data to React before the app mounts --}}
    <script>
        window.__RINDU_DATA__ = {
            stats:     @json($stats),
            gudang:    @json($gudang),
            kurir:     @json($kurir),
            langganan: @json($langganan),
            transaksi: @json($transaksi),
            pelanggan: @json($pelanggan)
        };
    </script>

    <div id="dashboard-root"></div>

    @if (!$useVite)
    <!-- Fallback React Dashboard Compiler -->
    <script type="text/babel">
        function DashboardFallback() {
            const [activeTab, setActiveTab] = React.useState('overview');
            const [searchQuery, setSearchQuery] = React.useState('');
            const [darkMode, setDarkMode] = React.useState(localStorage.getItem('darkMode') === 'true');
            const [adminDropdown, setAdminDropdown] = React.useState(false);

            const initialData = window.__RINDU_DATA__ || {};

            const stats = initialData.stats || {
                gudang_aktif: 1,
                stok_total: 2500,
                kurir_aktif: 4,
                total_transaksi: 142,
                total_pendapatan: 12850000,
                langganan_aktif: 48
            };

            const listGudang = initialData.gudang && initialData.gudang.length > 0 ? initialData.gudang : [
                { id_gudang: 1, nama_gudang: 'Gudang Utama Jagakarsa', lokasi: 'Jakarta Selatan', kapasitas_total: 5000, stok_saat_ini: 2500, status_gudang: 'aktif' }
            ];

            const listKurir = initialData.kurir && initialData.kurir.length > 0 ? initialData.kurir : [
                { id_kurir: 1, nama_kurir: 'Andi Pratama', no_hp: '0812-4422-9900', kendaraan: 'Motor Box', plat_nomor: 'B 3012 SHZ', status_kurir: 'aktif' },
                { id_kurir: 2, nama_kurir: 'Budi Santoso', no_hp: '0856-1188-4422', kendaraan: 'Pickup Suzuki', plat_nomor: 'F 8920 CC', status_kurir: 'aktif' },
                { id_kurir: 3, nama_kurir: 'Rian Wijaya', no_hp: '0878-9900-2211', kendaraan: 'Tossa Roda Tiga', plat_nomor: 'A 2911 PL', status_kurir: 'aktif' }
            ];

            const listLangganan = initialData.langganan && initialData.langganan.length > 0 ? initialData.langganan : [
                { id_langganan: 1, nama_pelanggan: 'Zaki Zulfikar', email: 'zaki@example.com', no_telepon: '081234567890', periode_pengantaran: 'mingguan', jumlah_pesanan: 3, status_langganan: 'aktif' },
                { id_langganan: 2, nama_pelanggan: 'PT Angkasa Makmur', email: 'info@angkasamaju.co.id', no_telepon: '021-880044', periode_pengantaran: 'bulanan', jumlah_pesanan: 20, status_langganan: 'aktif' }
            ];

            const listTransaksi = initialData.transaksi && initialData.transaksi.length > 0 ? initialData.transaksi : [
                { id_transaksi: 1, nama_pelanggan: 'Zaki Zulfikar', kode_invoice: 'INV-20260524-ZKI', metode_pembayaran: 'transfer', total_bayar: 150000, status_transaksi: 'selesai', tanggal_transaksi: '24 May 2026' },
                { id_transaksi: 2, nama_pelanggan: 'Indah Kusuma', kode_invoice: 'INV-20260524-IND', metode_pembayaran: 'e-wallet', total_bayar: 24000, status_transaksi: 'dibayar', tanggal_transaksi: '24 May 2026' },
                { id_transaksi: 3, nama_pelanggan: 'Bambang Tri', kode_invoice: 'INV-20260524-BBG', metode_pembayaran: 'tunai', total_bayar: 50000, status_transaksi: 'menunggu', tanggal_transaksi: '24 May 2026' }
            ];

            const listPelanggan = initialData.pelanggan && initialData.pelanggan.length > 0 ? initialData.pelanggan : [
                { id_pelanggan: 1, nama_pelanggan: 'Zaki Zulfikar', email: 'zaki@example.com', no_telepon: '081234567890', alamat: 'Jl. Raya Jagakarsa No. 12', jenis_pelanggan: 'individu', status_pelanggan: 'aktif' },
                { id_pelanggan: 2, nama_pelanggan: 'PT Angkasa Makmur', email: 'info@angkasamaju.co.id', no_telepon: '021-880044', alamat: 'Kawasan Industri Pulogadung Blok C', jenis_pelanggan: 'lembaga', status_pelanggan: 'aktif' }
            ];

            const toggleTheme = () => {
                const nextMode = !darkMode;
                setDarkMode(nextMode);
                localStorage.setItem('darkMode', nextMode);
                document.documentElement.classList.toggle('dark', nextMode);
            };

            const formatRupiah = (number) => {
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(number);
            };

            const handleLogout = () => {
                window.location.href = '/admin/login';
            };

            // Filter lists based on search
            const filteredTransaksi = listTransaksi.filter(t => 
                (t.nama_pelanggan || '').toLowerCase().includes(searchQuery.toLowerCase()) ||
                (t.kode_invoice || '').toLowerCase().includes(searchQuery.toLowerCase())
            );

            const filteredGudang = listGudang.filter(g => 
                (g.nama_gudang || '').toLowerCase().includes(searchQuery.toLowerCase()) ||
                (g.lokasi || '').toLowerCase().includes(searchQuery.toLowerCase())
            );

            const filteredKurir = listKurir.filter(k => 
                (k.nama_kurir || '').toLowerCase().includes(searchQuery.toLowerCase()) ||
                (k.kendaraan || '').toLowerCase().includes(searchQuery.toLowerCase())
            );

            const filteredLangganan = listLangganan.filter(l => 
                (l.nama_pelanggan || '').toLowerCase().includes(searchQuery.toLowerCase()) ||
                (l.email || '').toLowerCase().includes(searchQuery.toLowerCase())
            );

            const filteredPelanggan = listPelanggan.filter(p => 
                (p.nama_pelanggan || '').toLowerCase().includes(searchQuery.toLowerCase()) ||
                (p.email || '').toLowerCase().includes(searchQuery.toLowerCase()) ||
                (p.alamat || '').toLowerCase().includes(searchQuery.toLowerCase())
            );

            return (
                <div className={`min-h-screen flex transition-colors duration-500 ${darkMode ? 'dark bg-slate-950 text-slate-100' : 'bg-slate-50 text-slate-800'}`}>
                    {/* LEFT SIDEBAR NAVIGATION */}
                    <aside className="w-64 bg-slate-900 text-slate-400 p-6 flex flex-col justify-between border-r border-white/5 shrink-0 hidden md:flex">
                        <div className="space-y-8">
                            <div className="flex items-center gap-3">
                                <div className="w-9 h-9 rounded-xl bg-gradient-to-tr from-blue-600 to-sky-400 flex items-center justify-center text-white shadow-md">
                                    <svg xmlns="http://www.w3.org/2000/svg" className="h-5.5 w-5.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2.5">
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                    </svg>
                                </div>
                                <span className="text-lg font-black text-white tracking-tight">Rindu Water</span>
                            </div>

                            <nav className="space-y-1.5">
                                <span className="block text-[9px] font-bold text-slate-500 uppercase tracking-widest mb-3">Administrasi Menu</span>
                                
                                <button 
                                    onClick={() => { setActiveTab('overview'); setSearchQuery(''); }}
                                    className={`w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-bold transition-all ${activeTab === 'overview' ? 'bg-blue-600 text-white shadow-md' : 'hover:text-white hover:bg-white/5'}`}
                                >
                                    <span>📊</span> <span>Ikhtisar Data</span>
                                </button>

                                <button 
                                    onClick={() => { setActiveTab('transaksi'); setSearchQuery(''); }}
                                    className={`w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-bold transition-all ${activeTab === 'transaksi' ? 'bg-blue-600 text-white shadow-md' : 'hover:text-white hover:bg-white/5'}`}
                                >
                                    <span>💸</span> <span>Daftar Transaksi</span>
                                </button>

                                <button 
                                    onClick={() => { setActiveTab('pelanggan'); setSearchQuery(''); }}
                                    className={`w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-bold transition-all ${activeTab === 'pelanggan' ? 'bg-blue-600 text-white shadow-md' : 'hover:text-white hover:bg-white/5'}`}
                                >
                                    <span>👥</span> <span>Kelola Pelanggan</span>
                                </button>

                                <button 
                                    onClick={() => { setActiveTab('gudang'); setSearchQuery(''); }}
                                    className={`w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-bold transition-all ${activeTab === 'gudang' ? 'bg-blue-600 text-white shadow-md' : 'hover:text-white hover:bg-white/5'}`}
                                >
                                    <span>🏬</span> <span>Stok Gudang</span>
                                </button>

                                <button 
                                    onClick={() => { setActiveTab('kurir'); setSearchQuery(''); }}
                                    className={`w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-bold transition-all ${activeTab === 'kurir' ? 'bg-blue-600 text-white shadow-md' : 'hover:text-white hover:bg-white/5'}`}
                                >
                                    <span>🛵</span> <span>Kelola Kurir</span>
                                </button>

                                <button 
                                    onClick={() => { setActiveTab('langganan'); setSearchQuery(''); }}
                                    className={`w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-bold transition-all ${activeTab === 'langganan' ? 'bg-blue-600 text-white shadow-md' : 'hover:text-white hover:bg-white/5'}`}
                                >
                                    <span>💧</span> <span>Jadwal Langganan</span>
                                </button>
                            </nav>
                        </div>

                        <div className="pt-6 border-t border-white/5">
                            <button 
                                onClick={handleLogout}
                                className="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-bold text-rose-400 hover:text-rose-300 hover:bg-rose-500/10 transition-all"
                            >
                                <span>🔐</span>
                                <span>Keluar Portal</span>
                            </button>
                        </div>
                    </aside>

                    {/* MAIN DASHBOARD CONTENT AREA */}
                    <main className="flex-1 flex flex-col min-w-0">
                        {/* TOPBAR HEADER */}
                        <header className="px-8 py-5 flex items-center justify-between border-b border-slate-200 dark:border-white/5 relative z-20">
                            <h1 className="text-xl font-black tracking-tight text-slate-800 dark:text-white uppercase">
                                {activeTab === 'overview' && 'Ikhtisar Portal'}
                                {activeTab === 'transaksi' && 'Daftar Transaksi'}
                                {activeTab === 'pelanggan' && 'Kelola Pelanggan'}
                                {activeTab === 'gudang' && 'Manajemen Gudang'}
                                {activeTab === 'kurir' && 'Manajemen Armada'}
                                {activeTab === 'langganan' && 'Paket Langganan'}
                            </h1>

                            <div className="flex items-center gap-4">
                                <button 
                                    onClick={toggleTheme} 
                                    className="p-2.5 rounded-xl border border-slate-250 dark:border-white/10 hover:bg-slate-100 dark:hover:bg-white/5 transition"
                                >
                                    {darkMode ? '☀️' : '🌙'}
                                </button>

                                <div className="relative">
                                    <button 
                                        onClick={() => setAdminDropdown(!adminDropdown)}
                                        className="flex items-center gap-3 p-1.5 pr-4 rounded-xl border border-slate-250 dark:border-white/10 hover:bg-slate-100 dark:hover:bg-white/5 transition"
                                    >
                                        <div className="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center text-white font-black text-xs">
                                            AD
                                        </div>
                                        <span className="text-xs font-bold hidden sm:inline text-slate-700 dark:text-slate-200">Administrator</span>
                                    </button>

                                    {adminDropdown && (
                                        <div className="absolute right-0 mt-2 w-48 bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/10 rounded-xl shadow-xl py-2 z-50">
                                            <button 
                                                onClick={handleLogout}
                                                className="w-full text-left px-4 py-2.5 text-xs text-rose-500 hover:bg-slate-50 dark:hover:bg-white/5 font-bold transition"
                                            >
                                                Log Out Portal
                                            </button>
                                        </div>
                                    )}
                                </div>
                            </div>
                        </header>

                        {/* DASHBOARD BODY */}
                        <div className="p-8 space-y-8 overflow-y-auto max-h-[calc(100vh-80px)]">
                            {/* SEARCH & FILTERS BAR */}
                            <div className="flex flex-col sm:flex-row gap-4 justify-between items-center bg-white dark:bg-slate-900/55 p-4 rounded-2xl border border-slate-200 dark:border-white/5">
                                <div className="relative w-full sm:max-w-md">
                                    <span className="absolute left-4 top-1/2 -translate-y-1/2 text-sm opacity-55">🔍</span>
                                    <input 
                                        type="text" 
                                        value={searchQuery}
                                        onChange={(e) => setSearchQuery(e.target.value)}
                                        placeholder={`Cari di tab ${activeTab}...`} 
                                        className="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-950/60 border border-slate-250 dark:border-white/10 rounded-xl text-xs focus:outline-none focus:border-blue-500 transition"
                                    />
                                </div>
                                <div className="flex flex-wrap gap-1">
                                    {['overview', 'transaksi', 'pelanggan', 'gudang', 'kurir', 'langganan'].map(tab => (
                                        <button 
                                            key={tab}
                                            onClick={() => { setActiveTab(tab); setSearchQuery(''); }}
                                            className={`px-3 py-1.5 rounded-lg text-[9px] font-extrabold tracking-wider uppercase border transition ${activeTab === tab ? 'bg-blue-600 border-blue-600 text-white' : 'border-slate-200 dark:border-white/5'}`}
                                        >
                                            {tab === 'overview' ? 'Ikhtisar' : tab}
                                        </button>
                                    ))}
                                </div>
                            </div>

                            {/* TAB CONTENT: OVERVIEW */}
                            {activeTab === 'overview' && (
                                <div className="space-y-8 animate-fadeIn">
                                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                                        <div className="bg-white dark:bg-slate-900/50 p-6 rounded-3xl border border-slate-200 dark:border-white/5 space-y-3 shadow-sm hover:scale-[1.01] transition-all">
                                            <div className="flex justify-between items-center">
                                                <span className="text-xs font-bold text-slate-400 uppercase tracking-widest">Pendapatan</span>
                                                <span className="p-2 bg-emerald-500/10 text-emerald-500 rounded-xl text-lg">💰</span>
                                            </div>
                                            <h3 className="text-2xl font-black text-slate-800 dark:text-white tracking-tight">{formatRupiah(stats.total_pendapatan)}</h3>
                                            <p className="text-[10px] text-slate-400">Total penjualan terbayar</p>
                                        </div>

                                        <div className="bg-white dark:bg-slate-900/50 p-6 rounded-3xl border border-slate-200 dark:border-white/5 space-y-3 shadow-sm hover:scale-[1.01] transition-all">
                                            <div className="flex justify-between items-center">
                                                <span className="text-xs font-bold text-slate-400 uppercase tracking-widest">Transaksi</span>
                                                <span className="p-2 bg-blue-500/10 text-blue-500 rounded-xl text-lg">📦</span>
                                            </div>
                                            <h3 className="text-2xl font-black text-slate-800 dark:text-white tracking-tight">{stats.total_transaksi} Order</h3>
                                            <p className="text-[10px] text-slate-400">Terdaftar di database</p>
                                        </div>

                                        <div className="bg-white dark:bg-slate-900/50 p-6 rounded-3xl border border-slate-200 dark:border-white/5 space-y-3 shadow-sm hover:scale-[1.01] transition-all">
                                            <div className="flex justify-between items-center">
                                                <span className="text-xs font-bold text-slate-400 uppercase tracking-widest">Stok Gudang</span>
                                                <span className="p-2 bg-sky-500/10 text-sky-500 rounded-xl text-lg">🏬</span>
                                            </div>
                                            <h3 className="text-2xl font-black text-slate-800 dark:text-white tracking-tight">{stats.stok_total} Galon</h3>
                                            <p className="text-[10px] text-slate-400">Dari {stats.gudang_aktif} unit aktif</p>
                                        </div>

                                        <div className="bg-white dark:bg-slate-900/50 p-6 rounded-3xl border border-slate-200 dark:border-white/5 space-y-3 shadow-sm hover:scale-[1.01] transition-all">
                                            <div className="flex justify-between items-center">
                                                <span className="text-xs font-bold text-slate-400 uppercase tracking-widest">Langganan</span>
                                                <span className="p-2 bg-indigo-500/10 text-indigo-500 rounded-xl text-lg">💧</span>
                                            </div>
                                            <h3 className="text-2xl font-black text-slate-800 dark:text-white tracking-tight">{stats.langganan_aktif} Paket</h3>
                                            <p className="text-[10px] text-slate-400">Jadwal pasokan aktif</p>
                                        </div>
                                    </div>

                                    <div className="bg-white dark:bg-slate-900/50 rounded-3xl border border-slate-200 dark:border-white/5 p-6 space-y-6 shadow-sm">
                                        <div className="flex justify-between items-center">
                                            <h3 className="text-sm font-black text-slate-800 dark:text-white uppercase tracking-wider">Antrian Transaksi Terakhir</h3>
                                            <button onClick={() => setActiveTab('transaksi')} className="text-xs font-bold text-blue-500 hover:underline">Lihat Semua →</button>
                                        </div>
                                        <div className="overflow-x-auto">
                                            <table className="w-full text-left text-xs">
                                                <thead>
                                                    <tr className="border-b border-slate-200 dark:border-white/5 text-slate-400 font-bold uppercase tracking-wider">
                                                        <th className="py-4">Invoice</th>
                                                        <th className="py-4">Pelanggan</th>
                                                        <th className="py-4">Metode</th>
                                                        <th className="py-4">Total</th>
                                                        <th className="py-4">Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody className="divide-y divide-slate-100 dark:divide-white/5 font-medium">
                                                    {filteredTransaksi.slice(0, 5).map(t => (
                                                        <tr key={t.id_transaksi} className="hover:bg-slate-50 dark:hover:bg-white/5 transition">
                                                            <td className="py-4 font-bold text-blue-500">{t.kode_invoice}</td>
                                                            <td className="py-4">{t.nama_pelanggan}</td>
                                                            <td className="py-4 uppercase">{t.metode_pembayaran}</td>
                                                            <td className="py-4 font-bold">{formatRupiah(t.total_bayar)}</td>
                                                            <td className="py-4">
                                                                <span className={`px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase ${t.status_transaksi === 'selesai' || t.status_transaksi === 'dibayar' ? 'bg-emerald-500/10 text-emerald-500' : 'bg-amber-500/10 text-amber-500'}`}>
                                                                    {t.status_transaksi}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    ))}
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            )}

                            {/* TAB CONTENT: TRANSAKSI */}
                            {activeTab === 'transaksi' && (
                                <div className="bg-white dark:bg-slate-900/50 rounded-3xl border border-slate-200 dark:border-white/5 p-6 space-y-6 shadow-sm animate-fadeIn">
                                    <h3 className="text-sm font-black text-slate-800 dark:text-white uppercase tracking-wider">Manajemen Semua Pembayaran & Transaksi</h3>
                                    <div className="overflow-x-auto">
                                        <table className="w-full text-left text-xs">
                                            <thead>
                                                <tr className="border-b border-slate-200 dark:border-white/5 text-slate-400 font-bold uppercase tracking-wider">
                                                    <th className="py-4">Invoice</th>
                                                    <th className="py-4">Nama Pelanggan</th>
                                                    <th className="py-4">Tanggal Pesanan</th>
                                                    <th className="py-4">Metode</th>
                                                    <th className="py-4">Total Bayar</th>
                                                    <th className="py-4">Status Pengiriman</th>
                                                    <th className="py-4">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody className="divide-y divide-slate-100 dark:divide-white/5 font-medium">
                                                {filteredTransaksi.map(t => (
                                                    <tr key={t.id_transaksi} className="hover:bg-slate-50 dark:hover:bg-white/5 transition">
                                                        <td className="py-4 font-bold text-blue-500">{t.kode_invoice}</td>
                                                        <td className="py-4">{t.nama_pelanggan}</td>
                                                        <td className="py-4">{new Date(t.tanggal_transaksi || t.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}</td>
                                                        <td className="py-4 uppercase">{t.metode_pembayaran}</td>
                                                        <td className="py-4 font-bold">{formatRupiah(t.total_bayar)}</td>
                                                        <td className="py-4">
                                                            <span className={`px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase ${t.status_transaksi === 'selesai' || t.status_transaksi === 'dibayar' ? 'bg-emerald-500/10 text-emerald-500' : 'bg-amber-500/10 text-amber-500'}`}>
                                                                {t.status_transaksi}
                                                            </span>
                                                        </td>
                                                        <td className="py-4">
                                                            <select 
                                                                value={t.status_transaksi}
                                                                onChange={(e) => {
                                                                    alert(`Status transaksi ${t.kode_invoice} berhasil diperbarui menjadi ${e.target.value}!`);
                                                                    t.status_transaksi = e.target.value;
                                                                    setActiveTab('transaksi'); // refresh UI
                                                                }}
                                                                className="px-2 py-1 bg-slate-100 dark:bg-slate-950 border border-slate-200 dark:border-white/5 rounded-lg text-[10px] font-bold focus:outline-none"
                                                            >
                                                                <option value="menunggu">Menunggu</option>
                                                                <option value="dibayar">Dibayar</option>
                                                                <option value="diproses">Diproses</option>
                                                                <option value="dikirim">Dikirim</option>
                                                                <option value="selesai">Selesai</option>
                                                                <option value="batal">Batal</option>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                ))}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            )}

                            {/* TAB CONTENT: PELANGGAN */}
                            {activeTab === 'pelanggan' && (
                                <div className="bg-white dark:bg-slate-900/50 rounded-3xl border border-slate-200 dark:border-white/5 p-6 space-y-6 shadow-sm animate-fadeIn">
                                    <h3 className="text-sm font-black text-slate-800 dark:text-white uppercase tracking-wider">Manajemen Pelanggan Terdaftar</h3>
                                    <div className="overflow-x-auto">
                                        <table className="w-full text-left text-xs">
                                            <thead>
                                                <tr className="border-b border-slate-200 dark:border-white/5 text-slate-400 font-bold uppercase tracking-wider">
                                                    <th className="py-4">ID</th>
                                                    <th className="py-4">Nama Pelanggan</th>
                                                    <th className="py-4">Tipe</th>
                                                    <th className="py-4">Kontak</th>
                                                    <th className="py-4">Alamat Rumah/Lembaga</th>
                                                    <th className="py-4">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody className="divide-y divide-slate-100 dark:divide-white/5 font-medium">
                                                {filteredPelanggan.map(p => (
                                                    <tr key={p.id_pelanggan} className="hover:bg-slate-50 dark:hover:bg-white/5 transition">
                                                        <td className="py-4 text-slate-400 font-bold">#{p.id_pelanggan}</td>
                                                        <td className="py-4 font-bold">{p.nama_pelanggan}</td>
                                                        <td className="py-4">
                                                            <span className={`px-2 py-0.5 rounded text-[9px] font-bold uppercase ${p.jenis_pelanggan === 'lembaga' ? 'bg-indigo-500/10 text-indigo-500' : 'bg-blue-500/10 text-blue-500'}`}>
                                                                {p.jenis_pelanggan}
                                                            </span>
                                                        </td>
                                                        <td className="py-4">
                                                            <div className="space-y-0.5">
                                                                <span className="block font-semibold">{p.email}</span>
                                                                <span className="block text-[10px] text-slate-450">{p.no_telepon}</span>
                                                            </div>
                                                        </td>
                                                        <td className="py-4 truncate max-w-[200px]">{p.alamat}</td>
                                                        <td className="py-4">
                                                            <span className="px-2.5 py-1 bg-emerald-500/10 text-emerald-500 text-[10px] font-bold rounded-lg uppercase">
                                                                {p.status_pelanggan || 'aktif'}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                ))}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            )}

                            {/* TAB CONTENT: GUDANG */}
                            {activeTab === 'gudang' && (
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-6 animate-fadeIn">
                                    {filteredGudang.map(g => {
                                        const pct = Math.min(100, Math.round((g.stok_saat_ini / g.kapasitas_total) * 100));
                                        return (
                                            <div key={g.id_gudang} className="bg-white dark:bg-slate-900/50 p-6 rounded-3xl border border-slate-200 dark:border-white/5 space-y-4">
                                                <div className="flex justify-between items-start">
                                                    <div>
                                                        <span className="px-2 py-0.5 rounded text-[8px] bg-blue-500/10 text-blue-500 font-black uppercase tracking-wider">UNIT {g.id_gudang}</span>
                                                        <h3 className="text-base font-black text-slate-800 dark:text-white mt-1">{g.nama_gudang}</h3>
                                                        <p className="text-[10px] text-slate-400">{g.lokasi}</p>
                                                    </div>
                                                    <span className="px-2.5 py-1 bg-emerald-500/10 text-emerald-500 text-[10px] font-bold rounded-lg uppercase">{g.status_gudang}</span>
                                                </div>
                                                <div className="space-y-2">
                                                    <div className="flex justify-between text-xs font-bold text-slate-400">
                                                        <span>Kapasitas ({pct}%)</span>
                                                        <span>{g.stok_saat_ini} / {g.kapasitas_total} Galon</span>
                                                    </div>
                                                    <div className="w-full h-2 bg-slate-100 dark:bg-slate-950 rounded-full overflow-hidden">
                                                        <div className="h-full bg-blue-600 rounded-full" style={{ width: `${pct}%` }}></div>
                                                    </div>
                                                </div>
                                            </div>
                                        );
                                    })}
                                </div>
                            )}

                            {/* TAB CONTENT: KURIR */}
                            {activeTab === 'kurir' && (
                                <div className="grid grid-cols-1 md:grid-cols-3 gap-6 animate-fadeIn">
                                    {filteredKurir.map(k => (
                                        <div key={k.id_kurir} className="bg-white dark:bg-slate-900/50 p-6 rounded-3xl border border-slate-200 dark:border-white/5 text-center space-y-4 hover:scale-[1.01] transition-all">
                                            <div className="w-16 h-16 rounded-2xl bg-blue-600/10 text-blue-500 flex items-center justify-center text-3xl mx-auto font-black">
                                                🛵
                                            </div>
                                            <div>
                                                <h3 className="text-sm font-black text-slate-800 dark:text-white">{k.nama_kurir}</h3>
                                                <p className="text-[10px] text-slate-400 mt-1">{k.no_hp}</p>
                                            </div>
                                            <div className="pt-2 border-t border-slate-100 dark:border-white/5 space-y-1">
                                                <span className="block text-[10px] font-bold text-slate-700 dark:text-slate-300">{k.kendaraan}</span>
                                                <span className="block text-[9px] font-bold text-slate-400 tracking-wider">{k.plat_nomor}</span>
                                            </div>
                                            <span className="inline-block px-2.5 py-1 bg-emerald-500/10 text-emerald-500 text-[9px] font-bold rounded-lg uppercase">{k.status_kurir}</span>
                                        </div>
                                    ))}
                                </div>
                            )}

                            {/* TAB CONTENT: LANGGANAN */}
                            {activeTab === 'langganan' && (
                                <div className="bg-white dark:bg-slate-900/50 rounded-3xl border border-slate-200 dark:border-white/5 p-6 space-y-6 shadow-sm animate-fadeIn">
                                    <h3 className="text-sm font-black text-slate-800 dark:text-white uppercase tracking-wider">Pelanggan Aktif Pasokan Air</h3>
                                    <div className="overflow-x-auto">
                                        <table className="w-full text-left text-xs">
                                            <thead>
                                                <tr className="border-b border-slate-200 dark:border-white/5 text-slate-400 font-bold uppercase tracking-wider">
                                                    <th className="py-4">Pelanggan</th>
                                                    <th className="py-4">Kontak</th>
                                                    <th className="py-4">Frekuensi</th>
                                                    <th className="py-4">Jumlah Pasokan</th>
                                                    <th className="py-4">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody className="divide-y divide-slate-100 dark:divide-white/5 font-medium">
                                                {filteredLangganan.map(l => (
                                                    <tr key={l.id_langganan} className="hover:bg-slate-50 dark:hover:bg-white/5 transition">
                                                        <td className="py-4 font-bold">{l.nama_pelanggan}</td>
                                                        <td className="py-4">
                                                            <div className="space-y-0.5">
                                                                <span className="block font-semibold">{l.email}</span>
                                                                <span className="block text-[10px] text-slate-400">{l.no_telepon}</span>
                                                            </div>
                                                        </td>
                                                        <td className="py-4">
                                                            <span className="px-2.5 py-1 rounded-lg text-[9px] font-bold uppercase bg-blue-500/10 text-blue-500">
                                                                {l.periode_pengantaran}
                                                            </span>
                                                        </td>
                                                        <td className="py-4 font-bold text-slate-700 dark:text-slate-350">{l.jumlah_pesanan} Galon / Antar</td>
                                                        <td className="py-4">
                                                            <span className="px-2.5 py-1 rounded-lg text-[9px] font-bold uppercase bg-emerald-500/10 text-emerald-500">
                                                                {l.status_langganan || 'aktif'}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                ))}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            )}
                        </div>
                    </main>
                </div>
            );
        }

        const root = ReactDOM.createRoot(document.getElementById('dashboard-root'));
        root.render(<DashboardFallback />);
    </script>
    @endif
</body>
</html>
