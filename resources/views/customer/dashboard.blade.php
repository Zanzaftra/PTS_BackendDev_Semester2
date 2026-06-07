@php
    $useVite = file_exists(public_path('hot')) || file_exists(public_path('build/manifest.json'));
@endphp
<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Dashboard Pelanggan Rindu Water - Lacak pengiriman dan kelola jadwal langganan air mineral.">
    <title>Dashboard Pelanggan &mdash; Rindu Water</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;900&display=swap" rel="stylesheet">

    @if ($useVite)
        @vite(['resources/css/app.css', 'resources/js/customer-dashboard-app.jsx'])
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
        window.__CUSTOMER_DATA__ = {
            customer:  @json($customer),
            transaksi: @json($transaksi),
            langganan: @json($langganan)
        };
    </script>

    <div id="customer-dashboard-root"></div>

    @if (!$useVite)
    <!-- Fallback React Dashboard Compiler -->
    <script type="text/babel">
        function CustomerDashboardFallback() {
            const [activeTab, setActiveTab] = React.useState('tracking');
            const [darkMode, setDarkMode] = React.useState(localStorage.getItem('darkMode') === 'true');
            const [profileDropdown, setProfileDropdown] = React.useState(false);

            const initialData = window.__CUSTOMER_DATA__ || {};

            const customer = initialData.customer || {
                nama_pelanggan: 'Pelanggan',
                email: 'customer@rinduwater.com',
                no_telepon: '0812-3456-7890',
                alamat: 'Alamat belum diisi',
                jenis_pelanggan: 'individu'
            };

            const transaksi = initialData.transaksi || [];
            const langganan = initialData.langganan || [];

            const latestOrder = transaksi[0] || {
                kode_invoice: 'INV-DEMO-001',
                total_bayar: 45000,
                status_transaksi: 'menunggu',
                metode_pembayaran: 'transfer',
                tanggal_transaksi: new Date().toISOString()
            };

            const toggleTheme = () => {
                const nextMode = !darkMode;
                setDarkMode(nextMode);
                localStorage.setItem('darkMode', nextMode);
                document.documentElement.classList.toggle('dark', nextMode);
            };

            const formatRupiah = (number) => {
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(number);
            };

            const handleLogout = async () => {
                try {
                    const response = await fetch('/customer/logout', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        }
                    });
                    const data = await response.json();
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    }
                } catch (err) {
                    window.location.href = '/customer/login';
                }
            };

            const getStatusStep = (status) => {
                switch (status?.toLowerCase()) {
                    case 'menunggu': return 1;
                    case 'dibayar': return 2;
                    case 'diproses': return 2;
                    case 'dikirim': return 3;
                    case 'selesai': return 4;
                    default: return 1;
                }
            };

            const currentStep = getStatusStep(latestOrder.status_transaksi);

            return (
                <div className={`min-h-screen flex flex-col transition-colors duration-500 ${darkMode ? 'dark bg-slate-950 text-slate-100' : 'bg-slate-50 text-slate-800'}`}>
                    
                    {/* TOP HEADER */}
                    <header className="px-8 py-5 flex items-center justify-between border-b border-slate-200 dark:border-white/5 bg-white dark:bg-slate-900/60 backdrop-blur-md sticky top-0 z-50">
                        <div className="flex items-center gap-3">
                            <div className="w-10 h-10 rounded-2xl bg-gradient-to-tr from-sky-500 to-teal-400 flex items-center justify-center text-white font-black text-xl shadow-lg">
                                💧
                            </div>
                            <div>
                                <span className="text-xs font-bold text-sky-500 tracking-wider block uppercase">Portal Hidrasi</span>
                                <span className="text-base font-black text-slate-800 dark:text-white tracking-tight">Rindu Water</span>
                            </div>
                        </div>

                        <div className="flex items-center gap-4">
                            <button 
                                onClick={toggleTheme} 
                                className="p-2.5 rounded-xl border border-slate-250 dark:border-white/10 hover:bg-slate-105 transition"
                            >
                                {darkMode ? '☀️' : '🌙'}
                            </button>

                            <div className="relative">
                                <button 
                                    onClick={() => setProfileDropdown(!profileDropdown)}
                                    className="flex items-center gap-3 p-1.5 pr-4 rounded-xl border border-slate-250 dark:border-white/10 hover:bg-slate-100 dark:hover:bg-white/5 transition"
                                >
                                    <div className="w-8 h-8 rounded-lg bg-gradient-to-tr from-sky-500 to-teal-400 flex items-center justify-center text-white font-bold text-xs uppercase">
                                        {customer.nama_pelanggan.substring(0, 2)}
                                    </div>
                                    <span className="text-xs font-bold hidden sm:inline text-slate-700 dark:text-slate-200">{customer.nama_pelanggan}</span>
                                </button>

                                {profileDropdown && (
                                    <div className="absolute right-0 mt-2 w-56 bg-white dark:bg-slate-900 border border-slate-255 dark:border-white/10 rounded-xl shadow-2xl py-2 z-50 animate-fadeIn">
                                        <div className="px-4 py-2 border-b border-slate-100 dark:border-white/5 space-y-1">
                                            <span className="block text-[10px] font-bold text-slate-400 uppercase tracking-widest">Detail Kontak</span>
                                            <span className="block text-xs font-semibold truncate text-slate-705 dark:text-slate-250">{customer.email}</span>
                                            <span className="block text-[10px] text-slate-400">{customer.no_telepon}</span>
                                        </div>
                                        <button 
                                            onClick={handleLogout}
                                            className="w-full text-left px-4 py-2.5 text-xs text-rose-500 hover:bg-slate-50 dark:hover:bg-white/5 font-bold transition flex items-center gap-2"
                                        >
                                            🔐 Keluar Portal
                                        </button>
                                    </div>
                                )}
                            </div>
                        </div>
                    </header>

                    {/* HERO BANNER */}
                    <div className="bg-gradient-to-r from-sky-650 via-sky-500 to-teal-500 py-10 px-8 text-white relative overflow-hidden shrink-0 shadow-md">
                        <div className="absolute top-0 right-0 w-[300px] h-[300px] bg-white/10 rounded-full blur-[80px] pointer-events-none -z-10"></div>
                        <div className="max-w-6xl mx-auto space-y-3">
                            <span className="px-3 py-1 bg-white/20 rounded-full text-[10px] font-black tracking-widest uppercase">Dashboard Pelanggan</span>
                            <h2 className="text-3xl font-black tracking-tight">Selamat Datang, {customer.nama_pelanggan}!</h2>
                            <p className="text-xs font-light text-white/80 max-w-xl">
                                Pantau status pengiriman air galon Anda, kelola pasokan langganan aktif, dan akses riwayat belanja Anda dengan mudah.
                            </p>
                        </div>
                    </div>

                    {/* BODY CONTENT */}
                    <div className="flex-1 max-w-6xl w-full mx-auto p-8 space-y-8 min-h-0 overflow-y-auto">
                        <div className="flex border-b border-slate-200 dark:border-white/5 gap-6">
                            <button onClick={() => setActiveTab('tracking')} className={`pb-4 text-xs font-extrabold tracking-wider uppercase border-b-2 transition-all ${activeTab === 'tracking' ? 'border-sky-500 text-sky-500 font-black' : 'border-transparent text-slate-400 hover:text-slate-300'}`}>Lacak Pengiriman</button>
                            <button onClick={() => setActiveTab('history')} className={`pb-4 text-xs font-extrabold tracking-wider uppercase border-b-2 transition-all ${activeTab === 'history' ? 'border-sky-500 text-sky-500 font-black' : 'border-transparent text-slate-400 hover:text-slate-300'}`}>Riwayat Pesanan</button>
                            <button onClick={() => setActiveTab('subscription')} className={`pb-4 text-xs font-extrabold tracking-wider uppercase border-b-2 transition-all ${activeTab === 'subscription' ? 'border-sky-500 text-sky-500 font-black' : 'border-transparent text-slate-400 hover:text-slate-300'}`}>Jadwal Langganan</button>
                        </div>

                        {activeTab === 'tracking' && (
                            <div className="space-y-8 animate-fadeIn">
                                <div className="bg-white dark:bg-slate-900/50 rounded-3xl border border-slate-200 dark:border-white/5 p-6 md:p-8 space-y-8 shadow-sm">
                                    <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-6 border-b border-slate-100 dark:border-white/5">
                                        <div>
                                            <span className="text-[10px] font-black text-slate-400 uppercase tracking-widest">Pesanan Terbaru Anda</span>
                                            <h3 className="text-lg font-black text-sky-500 mt-1">{latestOrder.kode_invoice}</h3>
                                        </div>
                                        <div className="text-right">
                                            <span className="text-[10px] font-bold text-slate-400 uppercase tracking-widest block">Status Bayar</span>
                                            <span className={`inline-block px-3 py-1 rounded-full text-xs font-black uppercase mt-1 ${latestOrder.status_transaksi === 'selesai' || latestOrder.status_transaksi === 'dibayar' ? 'bg-emerald-500/10 text-emerald-500' : 'bg-amber-500/10 text-amber-500'}`}>
                                                {latestOrder.status_transaksi}
                                            </span>
                                        </div>
                                    </div>

                                    <div className="space-y-6">
                                        <h4 className="text-xs font-black uppercase tracking-wider text-slate-400">Progres Pengantaran Air</h4>
                                        <div className="grid grid-cols-1 md:grid-cols-4 gap-6 relative">
                                            <div className="absolute top-[22px] left-8 right-8 h-1 bg-slate-100 dark:bg-slate-950 -z-10 rounded hidden md:block">
                                                <div className="h-full bg-gradient-to-r from-sky-500 to-teal-400 rounded transition-all duration-1000" style={{ width: `${(Math.max(0, currentStep - 1) / 3) * 100}%` }}></div>
                                            </div>

                                            <div className="flex md:flex-col items-center gap-4 text-left md:text-center">
                                                <div className={`w-12 h-12 rounded-2xl flex items-center justify-center font-black text-lg shadow-md ${currentStep >= 1 ? 'bg-gradient-to-tr from-sky-500 to-teal-400 text-white' : 'bg-slate-100 dark:bg-slate-950 text-slate-400'}`}>📝</div>
                                                <div>
                                                    <span className="block text-xs font-black text-slate-800 dark:text-white mt-1">Pesanan Diterima</span>
                                                    <span className="block text-[10px] text-slate-450">Pembayaran divalidasi</span>
                                                </div>
                                            </div>

                                            <div className="flex md:flex-col items-center gap-4 text-left md:text-center">
                                                <div className={`w-12 h-12 rounded-2xl flex items-center justify-center font-black text-lg shadow-md ${currentStep >= 2 ? 'bg-gradient-to-tr from-sky-500 to-teal-400 text-white' : 'bg-slate-100 dark:bg-slate-950 text-slate-400'}`}>📦</div>
                                                <div>
                                                    <span className="block text-xs font-black text-slate-800 dark:text-white mt-1">Sedang Diproses</span>
                                                    <span className="block text-[10px] text-slate-450">Air disiapkan di gudang</span>
                                                </div>
                                            </div>

                                            <div className="flex md:flex-col items-center gap-4 text-left md:text-center">
                                                <div className={`w-12 h-12 rounded-2xl flex items-center justify-center font-black text-lg shadow-md ${currentStep >= 3 ? 'bg-gradient-to-tr from-sky-500 to-teal-400 text-white' : 'bg-slate-100 dark:bg-slate-950 text-slate-400'}`}>🛵</div>
                                                <div>
                                                    <span className="block text-xs font-black text-slate-800 dark:text-white mt-1">Dalam Perjalanan</span>
                                                    <span className="block text-[10px] text-slate-450">Kurir sedang mengantar</span>
                                                </div>
                                            </div>

                                            <div className="flex md:flex-col items-center gap-4 text-left md:text-center">
                                                <div className={`w-12 h-12 rounded-2xl flex items-center justify-center font-black text-lg shadow-md ${currentStep >= 4 ? 'bg-gradient-to-tr from-sky-500 to-teal-400 text-white' : 'bg-slate-100 dark:bg-slate-950 text-slate-400'}`}>✅</div>
                                                <div>
                                                    <span className="block text-xs font-black text-slate-800 dark:text-white mt-1">Selesai Diterima</span>
                                                    <span className="block text-[10px] text-slate-450">Terima kasih banyak!</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div className="bg-white dark:bg-slate-900/50 rounded-3xl border border-slate-200 dark:border-white/5 p-6 space-y-4">
                                        <h4 className="text-xs font-black uppercase tracking-wider text-slate-400">📍 Lokasi Pengantaran Air</h4>
                                        <p className="text-sm font-bold text-slate-700 dark:text-slate-250 leading-relaxed">{customer.alamat}</p>
                                        <span className="inline-block text-[10px] font-semibold text-slate-400 bg-slate-100 dark:bg-slate-950 px-2.5 py-1 rounded-lg">Kontak: {customer.no_telepon}</span>
                                    </div>

                                    <div className="bg-white dark:bg-slate-900/50 rounded-3xl border border-slate-200 dark:border-white/5 p-6 flex flex-col justify-between">
                                        <div className="space-y-2">
                                            <h4 className="text-xs font-black uppercase tracking-wider text-slate-400">💡 Pesan Air Tambahan</h4>
                                            <p className="text-[11px] text-slate-400 font-light leading-relaxed">Kehabisan stok air sebelum jadwal reguler Anda? Pesan langsung satu-kali sekarang juga.</p>
                                        </div>
                                        <a href="/customer/beli" className="w-full mt-4 py-3 bg-gradient-to-r from-sky-500 to-teal-400 text-white text-center rounded-xl text-xs font-bold uppercase tracking-wider transition-all block shadow-md">Pesan Air Sekarang</a>
                                    </div>
                                </div>
                            </div>
                        )}

                        {activeTab === 'history' && (
                            <div className="bg-white dark:bg-slate-900/50 rounded-3xl border border-slate-200 dark:border-white/5 p-6 space-y-6 shadow-sm animate-fadeIn">
                                <h3 className="text-sm font-black text-slate-800 dark:text-white uppercase tracking-wider">Riwayat Pembelanjaan Anda</h3>
                                {transaksi.length === 0 ? (
                                    <div className="py-12 text-center space-y-3">
                                        <span className="text-4xl">📭</span>
                                        <p className="text-xs text-slate-400 font-light">Belum ada riwayat pesanan yang terdaftar.</p>
                                    </div>
                                ) : (
                                    <div className="overflow-x-auto">
                                        <table className="w-full text-left text-xs">
                                            <thead>
                                                <tr className="border-b border-slate-200 dark:border-white/5 text-slate-400 font-bold uppercase tracking-wider">
                                                    <th className="py-4">Invoice</th>
                                                    <th className="py-4">Tanggal Pesanan</th>
                                                    <th className="py-4">Metode Bayar</th>
                                                    <th className="py-4">Total Bayar</th>
                                                    <th className="py-4">Status Pengiriman</th>
                                                </tr>
                                            </thead>
                                            <tbody className="divide-y divide-slate-100 dark:divide-white/5 font-medium">
                                                {transaksi.map(t => (
                                                    <tr key={t.id_transaksi} className="hover:bg-slate-50 dark:hover:bg-white/5 transition">
                                                        <td className="py-4 font-bold text-sky-500">{t.kode_invoice}</td>
                                                        <td className="py-4">{new Date(t.tanggal_transaksi || t.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}</td>
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
                                )}
                            </div>
                        )}

                        {activeTab === 'subscription' && (
                            <div className="space-y-6 animate-fadeIn">
                                <div className="bg-white dark:bg-slate-900/50 rounded-3xl border border-slate-200 dark:border-white/5 p-6 space-y-6 shadow-sm">
                                    <h3 className="text-sm font-black text-slate-800 dark:text-white uppercase tracking-wider">Frekuensi & Jadwal Pasokan Langganan</h3>
                                    {langganan.length === 0 ? (
                                        <div className="py-12 text-center space-y-3">
                                            <span className="text-4xl">💧</span>
                                            <p className="text-xs text-slate-400 font-light">Anda belum terdaftar dalam paket langganan reguler.</p>
                                            <a href="/customer/beli" className="inline-block text-xs font-bold text-sky-500 hover:underline">Pesan Langganan Mingguan / Bulanan Sekarang</a>
                                        </div>
                                    ) : (
                                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            {langganan.map(l => (
                                                <div key={l.id_langganan} className="p-6 bg-slate-50 dark:bg-slate-950/40 rounded-2xl border border-slate-200 dark:border-white/5 space-y-4">
                                                    <div className="flex justify-between items-center">
                                                        <span className="px-2.5 py-1 bg-sky-500/10 text-sky-500 text-[10px] font-black tracking-wider rounded-lg uppercase">PAKET {l.periode_pengantaran}</span>
                                                        <span className="w-2.5 h-2.5 rounded-full bg-emerald-500" title="Status Aktif"></span>
                                                    </div>
                                                    <div className="space-y-1">
                                                        <span className="block text-[10px] font-bold text-slate-400 uppercase tracking-widest">Kuantitas Pasokan</span>
                                                        <span className="block text-xl font-black text-slate-800 dark:text-white">{l.jumlah_pesanan} Galon / Antar</span>
                                                    </div>
                                                    <div className="pt-3 border-t border-slate-200 dark:border-white/5 grid grid-cols-2 gap-4 text-xs font-bold text-slate-450">
                                                        <div>
                                                            <span className="block text-[9px] uppercase tracking-wider">Mulai Aktif</span>
                                                            <span className="block text-slate-700 dark:text-slate-300 mt-1">{new Date(l.tanggal_mulai).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}</span>
                                                        </div>
                                                        <div>
                                                            <span className="block text-[9px] uppercase tracking-wider">Berakhir Pada</span>
                                                            <span className="block text-slate-700 dark:text-slate-300 mt-1">{new Date(l.tanggal_berakhir).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            ))}
                                        </div>
                                    )}
                                </div>
                            </div>
                        )}
                    </div>
                </div>
            );
        }

        const root = ReactDOM.createRoot(document.getElementById('customer-dashboard-root'));
        root.render(<CustomerDashboardFallback />);
    </script>
    @endif
</body>
</html>
