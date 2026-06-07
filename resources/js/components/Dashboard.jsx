import React, { useState } from 'react';

export default function Dashboard({ initialData }) {
    const [activeTab, setActiveTab] = useState('overview');
    const [searchQuery, setSearchQuery] = useState('');
    const [darkMode, setDarkMode] = useState(localStorage.getItem('darkMode') === 'true');
    const [adminDropdown, setAdminDropdown] = useState(false);

    // Initial database data passed from Laravel Backend
    const stats = initialData?.stats || {
        gudang_aktif: 1,
        stok_total: 2500,
        kurir_aktif: 4,
        total_transaksi: 142,
        total_pendapatan: 12850000,
        langganan_aktif: 48
    };

    const listGudang = initialData?.gudang || [
        { id_gudang: 1, nama_gudang: 'Gudang Utama Jagakarsa', lokasi: 'Jakarta Selatan', kapasitas_total: 5000, stok_saat_ini: 2500, status_gudang: 'aktif' }
    ];

    const listKurir = initialData?.kurir || [
        { id_kurir: 1, nama_kurir: 'Andi Pratama', no_hp: '0812-4422-9900', kendaraan: 'Motor Box', plat_nomor: 'B 3012 SHZ', status_kurir: 'aktif' },
        { id_kurir: 2, nama_kurir: 'Budi Santoso', no_hp: '0856-1188-4422', kendaraan: 'Pickup Suzuki', plat_nomor: 'F 8920 CC', status_kurir: 'aktif' },
        { id_kurir: 3, nama_kurir: 'Rian Wijaya', no_hp: '0878-9900-2211', kendaraan: 'Tossa Roda Tiga', plat_nomor: 'A 2911 PL', status_kurir: 'aktif' }
    ];

    const listLangganan = initialData?.langganan || [
        { id_langganan: 1, nama_pelanggan: 'Zaki Zulfikar', email: 'zaki@example.com', no_telepon: '081234567890', periode_pengantaran: 'mingguan', jumlah_pesanan: 3, status_langganan: 'aktif' },
        { id_langganan: 2, nama_pelanggan: 'PT Angkasa Makmur', email: 'info@angkasamaju.co.id', no_telepon: '021-880044', periode_pengantaran: 'bulanan', jumlah_pesanan: 20, status_langganan: 'aktif' }
    ];

    const listTransaksi = initialData?.transaksi || [
        { id_transaksi: 1, nama_pelanggan: 'Zaki Zulfikar', kode_invoice: 'INV-20260524-ZKI', metode_pembayaran: 'transfer', total_bayar: 150000, status_transaksi: 'selesai', tanggal_transaksi: '24 May 2026' },
        { id_transaksi: 2, nama_pelanggan: 'Indah Kusuma', kode_invoice: 'INV-20260524-IND', metode_pembayaran: 'e-wallet', total_bayar: 24000, status_transaksi: 'dibayar', tanggal_transaksi: '24 May 2026' },
        { id_transaksi: 3, nama_pelanggan: 'Bambang Tri', kode_invoice: 'INV-20260524-BBG', metode_pembayaran: 'tunai', total_bayar: 50000, status_transaksi: 'menunggu', tanggal_transaksi: '24 May 2026' }
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
        // Log out admin and redirect
        window.location.href = '/admin/login';
    };

    return (
        <div className={`min-h-screen flex transition-colors duration-500 ${darkMode ? 'dark bg-slate-950 text-slate-100' : 'bg-slate-50 text-slate-800'}`}>
            
            {/* 1. LEFT SIDEBAR NAVIGATION */}
            <aside className="w-64 bg-slate-900 text-slate-400 p-6 flex flex-col justify-between border-r border-white/5 shrink-0 hidden md:flex">
                <div className="space-y-10">
                    <div className="flex items-center gap-3">
                        <div className="w-9 h-9 rounded-xl bg-gradient-to-tr from-blue-600 to-sky-400 flex items-center justify-center text-white shadow-md">
                            <svg xmlns="http://www.w3.org/2000/svg" className="h-5.5 w-5.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2.5">
                                <path strokeLinecap="round" strokeLinejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                            </svg>
                        </div>
                        <span className="text-lg font-black text-white tracking-tight">Rindu Water</span>
                    </div>

                    <nav className="space-y-2">
                        <span className="block text-[9px] font-bold text-slate-500 uppercase tracking-widest mb-4">Administrasi Menu</span>
                        
                        {/* Overview Tab Button */}
                        <button 
                            onClick={() => { setActiveTab('overview'); setSearchQuery(''); }}
                            className={`w-full flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-bold transition-all ${activeTab === 'overview' ? 'bg-blue-600 text-white shadow-md shadow-blue-500/10' : 'hover:text-white hover:bg-white/5'}`}
                        >
                            <span className="text-base">📊</span>
                            <span>Ikhtisar Data</span>
                        </button>

                        {/* Gudang Tab Button */}
                        <button 
                            onClick={() => { setActiveTab('gudang'); setSearchQuery(''); }}
                            className={`w-full flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-bold transition-all ${activeTab === 'gudang' ? 'bg-blue-600 text-white shadow-md shadow-blue-500/10' : 'hover:text-white hover:bg-white/5'}`}
                        >
                            <span className="text-base">🏬</span>
                            <span>Stok Gudang</span>
                        </button>

                        {/* Kurir Tab Button */}
                        <button 
                            onClick={() => { setActiveTab('kurir'); setSearchQuery(''); }}
                            className={`w-full flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-bold transition-all ${activeTab === 'kurir' ? 'bg-blue-600 text-white shadow-md shadow-blue-500/10' : 'hover:text-white hover:bg-white/5'}`}
                        >
                            <span className="text-base">🛵</span>
                            <span>Kelola Kurir</span>
                        </button>

                        {/* Langganan Tab Button */}
                        <button 
                            onClick={() => { setActiveTab('langganan'); setSearchQuery(''); }}
                            className={`w-full flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-bold transition-all ${activeTab === 'langganan' ? 'bg-blue-600 text-white shadow-md shadow-blue-500/10' : 'hover:text-white hover:bg-white/5'}`}
                        >
                            <span className="text-base">💧</span>
                            <span>Langganan Air</span>
                        </button>
                    </nav>
                </div>

                <div className="pt-6 border-t border-white/5 space-y-4">
                    <div className="flex items-center gap-3">
                        <div className="w-8 h-8 rounded-full bg-slate-800 border border-white/10 flex items-center justify-center font-bold text-xs text-white">A</div>
                        <div>
                            <span className="block text-xs font-bold text-white leading-none">Super Admin</span>
                            <span class="text-[9px] text-slate-500">admin@rinduwater.com</span>
                        </div>
                    </div>
                    <button 
                        onClick={handleLogout} 
                        className="w-full py-2.5 bg-white/5 hover:bg-rose-500/10 text-rose-400 hover:text-rose-300 rounded-xl text-[10px] font-bold tracking-widest uppercase transition"
                    >
                        Keluar Portal
                    </button>
                </div>
            </aside>

            {/* 2. MAIN APPLICATION CONTENT AREA */}
            <main className="flex-1 flex flex-col min-w-0">
                {/* TOP NAVBAR */}
                <header className="py-4 px-6 md:px-8 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between gap-4">
                    <div className="flex items-center gap-4 flex-1">
                        {/* Search bar inside header */}
                        <div className="relative max-w-sm w-full">
                            <input 
                                type="text"
                                value={searchQuery}
                                onChange={(e) => setSearchQuery(e.target.value)}
                                placeholder="Cari data pelanggan, invoice, atau kurir..." 
                                className="w-full pl-10 pr-4 py-2 bg-white dark:bg-slate-900 border border-slate-250 dark:border-slate-800 rounded-xl text-xs focus:outline-none focus:border-blue-500 transition shadow-inner dark:text-white"
                            />
                            <span className="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs">🔍</span>
                        </div>
                    </div>

                    <div className="flex items-center gap-4">
                        {/* Dark Mode toggle switch in header */}
                        <button 
                            onClick={toggleTheme} 
                            className="p-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-350 shadow-sm hover:scale-105 transition"
                        >
                            {darkMode ? '☀️' : '🌙'}
                        </button>

                        <div className="relative">
                            <button 
                                onClick={() => setAdminDropdown(!adminDropdown)}
                                className="flex items-center gap-2.5 px-3 py-1.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-semibold shadow-sm"
                            >
                                <span className="w-2 h-2 rounded-full bg-emerald-500"></span>
                                <span>Super Admin</span>
                            </button>
                            
                            {adminDropdown && (
                                <div className="absolute right-0 mt-2 w-48 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xl p-2 space-y-1 z-15 text-xs">
                                    <button onClick={handleLogout} className="w-full text-left px-3 py-2 text-rose-500 hover:bg-rose-500/10 rounded-lg font-bold">Keluar Portal</button>
                                </div>
                            )}
                        </div>
                    </div>
                </header>

                {/* DYNAMIC SCROLL CONTAINER */}
                <div className="flex-1 p-6 md:p-8 overflow-y-auto space-y-8">
                    
                    {/* TAB OVERVIEW */}
                    {activeTab === 'overview' && (
                        <div className="space-y-8 animate-fadeIn">
                            <div className="space-y-2">
                                <h2 className="text-2xl font-black tracking-tight text-slate-900 dark:text-white">Selamat Datang di Portal Admin 📊</h2>
                                <p className="text-xs text-slate-500 dark:text-slate-400 font-light">Berikut adalah ringkasan perkembangan bisnis Rindu Water Anda hari ini.</p>
                            </div>

                            {/* Stat Grid List */}
                            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                                <div className="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 rounded-3xl shadow-sm space-y-3 hover:scale-[1.01] transition-all">
                                    <div className="flex items-center justify-between">
                                        <span className="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">Akumulasi Pendapatan</span>
                                        <span className="text-xl">💰</span>
                                    </div>
                                    <span className="block text-2xl font-black text-blue-600 dark:text-blue-400">{formatRupiah(stats.total_pendapatan)}</span>
                                </div>

                                <div className="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 rounded-3xl shadow-sm space-y-3 hover:scale-[1.01] transition-all">
                                    <div className="flex items-center justify-between">
                                        <span className="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">Jumlah Transaksi</span>
                                        <span className="text-xl">💳</span>
                                    </div>
                                    <span className="block text-2xl font-black text-slate-850 dark:text-white">{stats.total_transaksi} Kali</span>
                                </div>

                                <div className="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 rounded-3xl shadow-sm space-y-3 hover:scale-[1.01] transition-all">
                                    <div className="flex items-center justify-between">
                                        <span className="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">Langganan Aktif</span>
                                        <span className="text-xl">💧</span>
                                    </div>
                                    <span className="block text-2xl font-black text-teal-500 dark:text-teal-400">{stats.langganan_aktif} Paket</span>
                                </div>

                                <div className="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 rounded-3xl shadow-sm space-y-3 hover:scale-[1.01] transition-all">
                                    <div className="flex items-center justify-between">
                                        <span className="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">Volume Stok Total</span>
                                        <span className="text-xl">🏬</span>
                                    </div>
                                    <span className="block text-2xl font-black text-indigo-500 dark:text-indigo-400">{stats.stok_total} Pcs</span>
                                </div>
                            </div>

                            {/* Recent Transactions Table */}
                            <div className="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden">
                                <div className="px-6 py-5 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center gap-4">
                                    <h3 className="text-sm font-black text-slate-800 dark:text-white">10 Transaksi Terakhir</h3>
                                    <span className="text-[10px] font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wide">Pembaruan Berjalan</span>
                                </div>
                                <div className="overflow-x-auto">
                                    <table className="w-full text-left text-xs text-slate-600 dark:text-slate-400">
                                        <thead className="bg-slate-50 dark:bg-slate-800/40 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                                            <tr>
                                                <th className="py-4.5 px-6">Pelanggan</th>
                                                <th className="py-4.5 px-6">Kode Invoice</th>
                                                <th className="py-4.5 px-6">Metode</th>
                                                <th className="py-4.5 px-6">Total Bayar</th>
                                                <th className="py-4.5 px-6">Status</th>
                                                <th className="py-4.5 px-6">Tanggal</th>
                                            </tr>
                                        </thead>
                                        <tbody className="divide-y divide-slate-150 dark:divide-slate-800">
                                            {listTransaksi.map((tr) => (
                                                <tr key={tr.id_transaksi} className="hover:bg-slate-50/50 dark:hover:bg-slate-800/20">
                                                    <td className="py-4.5 px-6 font-extrabold text-slate-850 dark:text-white">{tr.nama_pelanggan}</td>
                                                    <td className="py-4.5 px-6 font-semibold uppercase">{tr.kode_invoice}</td>
                                                    <td className="py-4.5 px-6 uppercase">{tr.metode_pembayaran}</td>
                                                    <td className="py-4.5 px-6 font-extrabold text-blue-600 dark:text-blue-400">{formatRupiah(tr.total_bayar)}</td>
                                                    <td className="py-4.5 px-6">
                                                        <span className={`inline-block px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase ${tr.status_transaksi === 'selesai' ? 'bg-emerald-500/10 text-emerald-500' : (tr.status_transaksi === 'dibayar' ? 'bg-blue-500/10 text-blue-500' : 'bg-amber-500/10 text-amber-500')}`}>
                                                            {tr.status_transaksi}
                                                        </span>
                                                    </td>
                                                    <td className="py-4.5 px-6 font-light">{tr.tanggal_transaksi}</td>
                                                </tr>
                                            ))}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    )}

                    {/* TAB GUDANG */}
                    {activeTab === 'gudang' && (
                        <div className="space-y-8 animate-fadeIn">
                            <div className="flex flex-col sm:flex-row sm:items-end justify-between gap-6">
                                <div className="space-y-2">
                                    <h2 className="text-2xl font-black tracking-tight text-slate-900 dark:text-white">Inventori Stok Gudang 🏬</h2>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 font-light">Pantau penempatan stok air dan kapasitas sisa gudang secara real-time.</p>
                                </div>
                                <button className="px-5 py-3 bg-blue-600 hover:bg-blue-750 text-white rounded-xl text-xs font-bold tracking-wide transition shadow-md shrink-0">Tambah Gudang</button>
                            </div>

                            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                {listGudang.filter(g => g.nama_gudang.toLowerCase().includes(searchQuery.toLowerCase())).map((gd) => {
                                    const percentage = Math.round((gd.stok_saat_ini / gd.kapasitas_total) * 100);
                                    return (
                                        <div key={gd.id_gudang} className="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-6 hover:scale-[1.01] transition-all">
                                            <div className="flex justify-between items-start">
                                                <div>
                                                    <h3 className="text-base font-extrabold text-slate-800 dark:text-white leading-tight">{gd.nama_gudang}</h3>
                                                    <span className="text-[10px] text-slate-400 font-bold uppercase tracking-wider">{gd.lokasi}</span>
                                                </div>
                                                <span className={`px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase ${gd.status_gudang === 'aktif' ? 'bg-emerald-500/10 text-emerald-500' : 'bg-rose-500/10 text-rose-500'}`}>
                                                    {gd.status_gudang}
                                                </span>
                                            </div>

                                            {/* Stock progress capacity bar */}
                                            <div className="space-y-2">
                                                <div class="flex justify-between text-xs font-semibold text-slate-500">
                                                    <span>Kapasitas Terisi</span>
                                                    <span>{percentage}% ({gd.stok_saat_ini} / {gd.kapasitas_total} Pcs)</span>
                                                </div>
                                                <div className="w-full h-2 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                                                    <div className="h-full bg-blue-500 rounded-full transition-all duration-1000" style={{ width: `${percentage}%` }}></div>
                                                </div>
                                            </div>

                                            <div className="grid grid-cols-2 gap-4 text-xs border-t border-slate-100 dark:border-slate-800 pt-4">
                                                <div>
                                                    <span className="block text-[9px] font-bold text-slate-400 uppercase tracking-widest">Kapasitas Sisa</span>
                                                    <span className="block text-sm font-extrabold text-slate-850 dark:text-white">{gd.kapasitas_total - gd.stok_saat_ini} Pcs</span>
                                                </div>
                                                <div>
                                                    <span className="block text-[9px] font-bold text-slate-400 uppercase tracking-widest">Terakhir Input</span>
                                                    <span className="block text-sm font-extrabold text-slate-850 dark:text-white">Hari Ini</span>
                                                </div>
                                            </div>
                                        </div>
                                    );
                                })}
                            </div>
                        </div>
                    )}

                    {/* TAB KURIR */}
                    {activeTab === 'kurir' && (
                        <div className="space-y-8 animate-fadeIn">
                            <div className="flex flex-col sm:flex-row sm:items-end justify-between gap-6">
                                <div className="space-y-2">
                                    <h2 className="text-2xl font-black tracking-tight text-slate-900 dark:text-white">Armada Kurir Pengantar 🛵</h2>
                                    <p className="text-xs text-slate-500 dark:text-slate-400 font-light">Status aktifitas tugas kurir pendistribusian air mineral Rindu Water.</p>
                                </div>
                                <button className="px-5 py-3 bg-blue-600 hover:bg-blue-750 text-white rounded-xl text-xs font-bold tracking-wide transition shadow-md shrink-0">Daftarkan Kurir</button>
                            </div>

                            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                {listKurir.filter(k => k.nama_kurir.toLowerCase().includes(searchQuery.toLowerCase())).map((kr) => (
                                    <div key={kr.id_kurir} className="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm flex flex-col justify-between space-y-6 hover:scale-[1.01] transition-all">
                                        <div className="flex justify-between items-start">
                                            <div className="flex items-center gap-4">
                                                <div className="w-12 h-12 rounded-2xl bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center font-extrabold text-base shadow-sm">👤</div>
                                                <div>
                                                    <h3 className="text-base font-extrabold text-slate-800 dark:text-white leading-tight">{kr.nama_kurir}</h3>
                                                    <span className="text-[10px] text-slate-400 font-bold uppercase tracking-wider">{kr.no_hp}</span>
                                                </div>
                                            </div>
                                            <span className={`px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase ${kr.status_kurir === 'aktif' ? 'bg-emerald-500/10 text-emerald-500' : 'bg-slate-500/10 text-slate-500'}`}>
                                                {kr.status_kurir}
                                            </span>
                                        </div>

                                        <div className="grid grid-cols-2 gap-4 text-xs border-t border-slate-100 dark:border-slate-800 pt-4 bg-slate-50/50 dark:bg-slate-900/30 p-4 rounded-2xl border">
                                            <div>
                                                <span className="block text-[8px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Kendaraan</span>
                                                <span className="block text-xs font-bold text-slate-700 dark:text-slate-350">{kr.kendaraan}</span>
                                            </div>
                                            <div>
                                                <span className="block text-[8px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Plat Nomor</span>
                                                <span className="block text-xs font-bold text-slate-700 dark:text-slate-200 font-mono uppercase bg-white dark:bg-slate-850 px-2 py-0.5 rounded border border-slate-150 inline-block">{kr.plat_nomor}</span>
                                            </div>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    )}

                    {/* TAB LANGGANAN */}
                    {activeTab === 'langganan' && (
                        <div className="space-y-8 animate-fadeIn">
                            <div className="space-y-2">
                                <h2 className="text-2xl font-black tracking-tight text-slate-900 dark:text-white">Pelanggan Langganan Aktif 💧</h2>
                                <p className="text-xs text-slate-500 dark:text-slate-400 font-light">Data keanggotaan paket distribusi berkala Rindu Water.</p>
                            </div>

                            <div className="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden">
                                <div className="px-6 py-5 border-b border-slate-200 dark:border-slate-800">
                                    <h3 className="text-sm font-black text-slate-800 dark:text-white">Daftar Langganan Aktif</h3>
                                </div>
                                <div className="overflow-x-auto">
                                    <table className="w-full text-left text-xs text-slate-600 dark:text-slate-400">
                                        <thead className="bg-slate-50 dark:bg-slate-800/40 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                                            <tr>
                                                <th className="py-4.5 px-6">Pelanggan</th>
                                                <th className="py-4.5 px-6">Nomor Telp</th>
                                                <th className="py-4.5 px-6">Tipe Antaran</th>
                                                <th className="py-4.5 px-6">Jumlah</th>
                                                <th className="py-4.5 px-6">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody className="divide-y divide-slate-150 dark:divide-slate-800">
                                            {listLangganan.filter(l => l.nama_pelanggan.toLowerCase().includes(searchQuery.toLowerCase())).map((lg) => (
                                                <tr key={lg.id_langganan} className="hover:bg-slate-50/50 dark:hover:bg-slate-800/20">
                                                    <td className="py-4.5 px-6">
                                                        <div>
                                                            <span className="block font-extrabold text-slate-850 dark:text-white">{lg.nama_pelanggan}</span>
                                                            <span className="block text-[10px] text-slate-400">{lg.email}</span>
                                                        </div>
                                                    </td>
                                                    <td className="py-4.5 px-6">{lg.no_telepon}</td>
                                                    <td className="py-4.5 px-6 uppercase font-bold text-blue-600 dark:text-blue-400">{lg.periode_pengantaran}</td>
                                                    <td className="py-4.5 px-6 font-extrabold">{lg.jumlah_pesanan} Unit</td>
                                                    <td className="py-4.5 px-6">
                                                        <span className="inline-block px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase bg-emerald-500/10 text-emerald-500">
                                                            {lg.status_langganan}
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

                </div>
            </main>
        </div>
    );
}
