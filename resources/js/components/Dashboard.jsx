import React, { useState } from 'react';

// ─────────────────────────────────────────────
// Toast notification mini-component
// ─────────────────────────────────────────────
function Toast({ message, type, onClose }) {
    const base = 'fixed bottom-6 right-6 z-[999] px-5 py-3.5 rounded-2xl shadow-2xl flex items-center gap-3 text-sm font-bold transition-all animate-fadeIn';
    const colors = {
        success: 'bg-emerald-500 text-white',
        error:   'bg-rose-500 text-white',
        info:    'bg-blue-500 text-white',
    };
    return (
        <div className={`${base} ${colors[type] || colors.info}`}>
            <span>{type === 'success' ? '✅' : type === 'error' ? '❌' : 'ℹ️'}</span>
            <span>{message}</span>
            <button onClick={onClose} className="ml-2 opacity-75 hover:opacity-100 text-lg leading-none">×</button>
        </div>
    );
}

// ─────────────────────────────────────────────
// Modal wrapper
// ─────────────────────────────────────────────
function Modal({ title, children, onClose }) {
    return (
        <div className="fixed inset-0 z-[900] flex items-center justify-center" style={{ background: 'rgba(0,0,0,0.65)' }}>
            <div className="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl w-full max-w-lg mx-4 p-7 relative animate-fadeIn border border-slate-200 dark:border-white/10">
                <div className="flex items-center justify-between mb-6">
                    <h2 className="text-base font-black text-slate-800 dark:text-white uppercase tracking-wide">{title}</h2>
                    <button onClick={onClose} className="p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-white/10 transition text-slate-500 dark:text-slate-400 text-xl leading-none">×</button>
                </div>
                {children}
            </div>
        </div>
    );
}

// ─────────────────────────────────────────────
// Status badge helper
// ─────────────────────────────────────────────
function StatusBadge({ status }) {
    const map = {
        'aktif':    'bg-emerald-500/10 text-emerald-500',
        'selesai':  'bg-emerald-500/10 text-emerald-500',
        'dibayar':  'bg-emerald-500/10 text-emerald-500',
        'nonaktif': 'bg-slate-400/10 text-slate-400',
        'tidak aktif': 'bg-slate-400/10 text-slate-400',
        'menunggu': 'bg-amber-500/10 text-amber-500',
        'diproses': 'bg-blue-500/10 text-blue-500',
        'dikirim':  'bg-indigo-500/10 text-indigo-500',
        'batal':    'bg-rose-500/10 text-rose-500',
        'penuh':    'bg-orange-500/10 text-orange-500',
        'maintenance': 'bg-purple-500/10 text-purple-500',
    };
    return (
        <span className={`px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase ${map[status] || 'bg-slate-200 text-slate-500'}`}>
            {status}
        </span>
    );
}

// ─────────────────────────────────────────────
// CSRF Token helper
// ─────────────────────────────────────────────
function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content || '';
}

// ─────────────────────────────────────────────
// API helper
// ─────────────────────────────────────────────
async function apiPost(url, body) {
    const res = await fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN':  csrfToken(),
            'Accept':        'application/json',
        },
        body: JSON.stringify(body),
    });
    return res.json();
}

// ─────────────────────────────────────────────
// MAIN DASHBOARD COMPONENT
// ─────────────────────────────────────────────
export default function Dashboard({ initialData }) {
    const [activeTab, setActiveTab] = useState('overview');
    const [searchQuery, setSearchQuery] = useState('');
    const [darkMode, setDarkMode] = useState(localStorage.getItem('darkMode') === 'true');
    const [adminDropdown, setAdminDropdown] = useState(false);

    // Toast state
    const [toast, setToast] = useState(null);
    const showToast = (message, type = 'success') => {
        setToast({ message, type });
        setTimeout(() => setToast(null), 4000);
    };

    // ── Seed data from backend with fallback ──────────────────────
    const [stats, setStats] = useState(initialData?.stats || {
        gudang_aktif: 1, stok_total: 2500, kurir_aktif: 4,
        total_transaksi: 142, total_pendapatan: 12850000, langganan_aktif: 48
    });

    const [listGudang, setListGudang] = useState(initialData?.gudang?.length
        ? initialData.gudang
        : [{ id_gudang: 1, nama_gudang: 'Gudang Utama Jagakarsa', lokasi: 'Jakarta Selatan', kapasitas_total: 5000, stok_saat_ini: 2500, status_gudang: 'aktif' }]);

    const [listKurir, setListKurir] = useState(initialData?.kurir?.length
        ? initialData.kurir
        : [
            { id_kurir: 1, nama_kurir: 'Andi Pratama',  no_hp: '0812-4422-9900', kendaraan: 'Motor Box',      plat_nomor: 'B 3012 SHZ', status_kurir: 'aktif' },
            { id_kurir: 2, nama_kurir: 'Budi Santoso',  no_hp: '0856-1188-4422', kendaraan: 'Pickup Suzuki',  plat_nomor: 'F 8920 CC',  status_kurir: 'aktif' },
            { id_kurir: 3, nama_kurir: 'Rian Wijaya',   no_hp: '0878-9900-2211', kendaraan: 'Tossa Roda Tiga',plat_nomor: 'A 2911 PL',  status_kurir: 'aktif' },
        ]);

    const [listLangganan, setListLangganan] = useState(initialData?.langganan?.length
        ? initialData.langganan
        : [
            { id_langganan: 1, nama_pelanggan: 'Zaki Zulfikar', email: 'zaki@example.com', no_telepon: '081234567890', periode_pengantaran: 'mingguan', jumlah_pesanan: 3,  status_langganan: 'aktif', tanggal_mulai: '2026-05-01', tanggal_berakhir: '2026-11-01' },
            { id_langganan: 2, nama_pelanggan: 'PT Angkasa Makmur', email: 'info@angkasa.co.id', no_telepon: '021-880044', periode_pengantaran: 'bulanan', jumlah_pesanan: 20, status_langganan: 'aktif', tanggal_mulai: '2026-04-01', tanggal_berakhir: '2026-10-01' },
        ]);

    const [listTransaksi, setListTransaksi] = useState(initialData?.transaksi?.length
        ? initialData.transaksi
        : [
            { id_transaksi: 1, nama_pelanggan: 'Zaki Zulfikar', kode_invoice: 'INV-20260524-ZKI', metode_pembayaran: 'transfer', total_bayar: 150000, status_transaksi: 'selesai', tanggal_transaksi: '2026-05-24' },
            { id_transaksi: 2, nama_pelanggan: 'Indah Kusuma',   kode_invoice: 'INV-20260524-IND', metode_pembayaran: 'e-wallet', total_bayar: 24000,  status_transaksi: 'dibayar', tanggal_transaksi: '2026-05-24' },
            { id_transaksi: 3, nama_pelanggan: 'Bambang Tri',    kode_invoice: 'INV-20260524-BBG', metode_pembayaran: 'tunai',    total_bayar: 50000,  status_transaksi: 'menunggu', tanggal_transaksi: '2026-05-24' },
        ]);

    const [listPelanggan, setListPelanggan] = useState(initialData?.pelanggan?.length
        ? initialData.pelanggan
        : [
            { id_pelanggan: 1, nama_pelanggan: 'Zaki Zulfikar', email: 'zaki@example.com', no_telepon: '081234567890', alamat: 'Jl. Raya Jagakarsa No. 12', jenis_pelanggan: 'individu', status_pelanggan: 'aktif' },
            { id_pelanggan: 2, nama_pelanggan: 'PT Angkasa Makmur', email: 'info@angkasa.co.id', no_telepon: '021-880044', alamat: 'Kawasan Industri Pulogadung Blok C', jenis_pelanggan: 'lembaga', status_pelanggan: 'aktif' },
        ]);

    // ── Modal states ──────────────────────────────────────────────
    const [showAddGudang, setShowAddGudang] = useState(false);
    const [showAddKurir,  setShowAddKurir]  = useState(false);
    const [editingGudang, setEditingGudang] = useState(null); // gudang object being stock-edited

    const [gudangForm, setGudangForm] = useState({ nama_gudang: '', lokasi: '', kapasitas_total: '', stok_saat_ini: '', status_gudang: 'aktif' });
    const [kurirForm,  setKurirForm]  = useState({ nama_kurir: '', no_hp: '', alamat: '', kendaraan: '', plat_nomor: '', status_kurir: 'aktif', catatan: '' });
    const [stockForm,  setStockForm]  = useState({ stok_saat_ini: '', status_gudang: 'aktif' });
    const [loading,    setLoading]    = useState(false);

    // ── Theme toggle ──────────────────────────────────────────────
    const toggleTheme = () => {
        const nextMode = !darkMode;
        setDarkMode(nextMode);
        localStorage.setItem('darkMode', nextMode);
        document.documentElement.classList.toggle('dark', nextMode);
    };

    const formatRupiah = (n) =>
        new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(n);

    const handleLogout = () => { window.location.href = '/admin/login'; };

    // ── Filters ───────────────────────────────────────────────────
    const q = searchQuery.toLowerCase();
    const filteredTransaksi  = listTransaksi.filter(t =>
        (t.nama_pelanggan||'').toLowerCase().includes(q) || (t.kode_invoice||'').toLowerCase().includes(q) || (t.metode_pembayaran||'').toLowerCase().includes(q));
    const filteredGudang     = listGudang.filter(g =>
        (g.nama_gudang||'').toLowerCase().includes(q) || (g.lokasi||'').toLowerCase().includes(q));
    const filteredKurir      = listKurir.filter(k =>
        (k.nama_kurir||'').toLowerCase().includes(q) || (k.kendaraan||'').toLowerCase().includes(q));
    const filteredLangganan  = listLangganan.filter(l =>
        (l.nama_pelanggan||'').toLowerCase().includes(q) || (l.email||'').toLowerCase().includes(q));
    const filteredPelanggan  = listPelanggan.filter(p =>
        (p.nama_pelanggan||'').toLowerCase().includes(q) || (p.email||'').toLowerCase().includes(q) || (p.alamat||'').toLowerCase().includes(q));

    // ── HANDLERS ──────────────────────────────────────────────────

    // Update transaksi status
    const handleTransaksiStatus = async (id, newStatus) => {
        setListTransaksi(prev => prev.map(t =>
            t.id_transaksi === id ? { ...t, status_transaksi: newStatus } : t));
        try {
            const res = await apiPost(`/admin/api/transaksi/${id}/status`, { status: newStatus });
            showToast(res.message || 'Status transaksi diperbarui!', res.success ? 'success' : 'error');
        } catch {
            showToast('Status diperbarui (offline mode).', 'info');
        }
    };

    // Update pelanggan status
    const handlePelangganStatus = async (id, newStatus) => {
        setListPelanggan(prev => prev.map(p =>
            p.id_pelanggan === id ? { ...p, status_pelanggan: newStatus } : p));
        try {
            const res = await apiPost(`/admin/api/pelanggan/${id}/status`, { status: newStatus });
            showToast(res.message || 'Status pelanggan diperbarui!', res.success ? 'success' : 'error');
        } catch {
            showToast('Status diperbarui (offline mode).', 'info');
        }
    };

    // Update langganan status
    const handleLanggananStatus = async (id, newStatus) => {
        setListLangganan(prev => prev.map(l =>
            l.id_langganan === id ? { ...l, status_langganan: newStatus } : l));
        try {
            const res = await apiPost(`/admin/api/langganan/${id}/status`, { status: newStatus });
            showToast(res.message || 'Status langganan diperbarui!', res.success ? 'success' : 'error');
        } catch {
            showToast('Status diperbarui (offline mode).', 'info');
        }
    };

    // Toggle kurir status
    const handleKurirToggle = async (id) => {
        const kurir = listKurir.find(k => k.id_kurir === id);
        if (!kurir) return;
        const newStatus = kurir.status_kurir === 'aktif' ? 'nonaktif' : 'aktif';
        setListKurir(prev => prev.map(k =>
            k.id_kurir === id ? { ...k, status_kurir: newStatus } : k));
        try {
            const res = await apiPost(`/admin/api/kurir/${id}/update`, { status_kurir: newStatus });
            showToast(res.message || `Kurir ${newStatus === 'aktif' ? 'diaktifkan' : 'dinonaktifkan'}!`, res.success ? 'success' : 'error');
        } catch {
            showToast('Status kurir diperbarui (offline mode).', 'info');
        }
    };

    // Submit add gudang
    const handleAddGudang = async (e) => {
        e.preventDefault();
        setLoading(true);
        try {
            const res = await apiPost('/admin/api/gudang', {
                ...gudangForm,
                kapasitas_total: parseInt(gudangForm.kapasitas_total, 10),
                stok_saat_ini:   parseInt(gudangForm.stok_saat_ini, 10),
            });
            if (res.success && res.data) {
                setListGudang(prev => [...prev, res.data]);
                showToast(res.message || 'Gudang berhasil ditambahkan!', 'success');
            } else {
                showToast(res.message || 'Gagal menambahkan gudang.', 'error');
            }
        } catch {
            const mockId = Date.now();
            setListGudang(prev => [...prev, { ...gudangForm, id_gudang: mockId, kapasitas_total: parseInt(gudangForm.kapasitas_total, 10), stok_saat_ini: parseInt(gudangForm.stok_saat_ini, 10) }]);
            showToast('Gudang ditambahkan (offline mode).', 'info');
        }
        setGudangForm({ nama_gudang: '', lokasi: '', kapasitas_total: '', stok_saat_ini: '', status_gudang: 'aktif' });
        setShowAddGudang(false);
        setLoading(false);
    };

    // Submit update gudang stock
    const handleUpdateStock = async (e) => {
        e.preventDefault();
        if (!editingGudang) return;
        setLoading(true);
        const updated = { stok_saat_ini: parseInt(stockForm.stok_saat_ini, 10), status_gudang: stockForm.status_gudang };
        try {
            const res = await apiPost(`/admin/api/gudang/${editingGudang.id_gudang}/update`, updated);
            if (res.success) {
                setListGudang(prev => prev.map(g =>
                    g.id_gudang === editingGudang.id_gudang ? { ...g, ...updated } : g));
                showToast(res.message || 'Stok gudang diperbarui!', 'success');
            } else {
                showToast(res.message || 'Gagal memperbarui gudang.', 'error');
            }
        } catch {
            setListGudang(prev => prev.map(g =>
                g.id_gudang === editingGudang.id_gudang ? { ...g, ...updated } : g));
            showToast('Stok diperbarui (offline mode).', 'info');
        }
        setEditingGudang(null);
        setLoading(false);
    };

    // Submit add kurir
    const handleAddKurir = async (e) => {
        e.preventDefault();
        setLoading(true);
        try {
            const res = await apiPost('/admin/api/kurir', kurirForm);
            if (res.success && res.data) {
                setListKurir(prev => [...prev, res.data]);
                showToast(res.message || 'Kurir berhasil ditambahkan!', 'success');
            } else {
                showToast(res.message || 'Gagal menambahkan kurir.', 'error');
            }
        } catch {
            const mockId = Date.now();
            setListKurir(prev => [...prev, { ...kurirForm, id_kurir: mockId }]);
            showToast('Kurir ditambahkan (offline mode).', 'info');
        }
        setKurirForm({ nama_kurir: '', no_hp: '', alamat: '', kendaraan: '', plat_nomor: '', status_kurir: 'aktif', catatan: '' });
        setShowAddKurir(false);
        setLoading(false);
    };

    // ── Input style helper ────────────────────────────────────────
    const inputCls = 'w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-white/10 rounded-xl text-xs focus:outline-none focus:border-blue-500 transition text-slate-800 dark:text-slate-200';
    const labelCls = 'block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1';
    const btnPrimary = 'px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition disabled:opacity-50';
    const btnSecondary = 'px-4 py-2.5 bg-slate-100 dark:bg-white/5 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-bold hover:bg-slate-200 dark:hover:bg-white/10 transition';

    // ─────────────────────────────────────────────────────────────
    return (
        <div className={`min-h-screen flex transition-colors duration-500 ${darkMode ? 'dark bg-slate-950 text-slate-100' : 'bg-slate-50 text-slate-800'}`}>

            {/* TOAST */}
            {toast && <Toast message={toast.message} type={toast.type} onClose={() => setToast(null)} />}

            {/* MODAL: Add Gudang */}
            {showAddGudang && (
                <Modal title="➕ Tambah Gudang Baru" onClose={() => setShowAddGudang(false)}>
                    <form onSubmit={handleAddGudang} className="space-y-4">
                        <div className="grid grid-cols-2 gap-4">
                            <div>
                                <label className={labelCls}>Nama Gudang</label>
                                <input className={inputCls} value={gudangForm.nama_gudang} onChange={e => setGudangForm(f => ({ ...f, nama_gudang: e.target.value }))} placeholder="ex: Gudang Selatan 2" required />
                            </div>
                            <div>
                                <label className={labelCls}>Lokasi</label>
                                <input className={inputCls} value={gudangForm.lokasi} onChange={e => setGudangForm(f => ({ ...f, lokasi: e.target.value }))} placeholder="ex: Jakarta Barat" required />
                            </div>
                            <div>
                                <label className={labelCls}>Kapasitas Total (Galon)</label>
                                <input type="number" min="1" className={inputCls} value={gudangForm.kapasitas_total} onChange={e => setGudangForm(f => ({ ...f, kapasitas_total: e.target.value }))} placeholder="ex: 3000" required />
                            </div>
                            <div>
                                <label className={labelCls}>Stok Saat Ini (Galon)</label>
                                <input type="number" min="0" className={inputCls} value={gudangForm.stok_saat_ini} onChange={e => setGudangForm(f => ({ ...f, stok_saat_ini: e.target.value }))} placeholder="ex: 1200" required />
                            </div>
                        </div>
                        <div>
                            <label className={labelCls}>Status Gudang</label>
                            <select className={inputCls} value={gudangForm.status_gudang} onChange={e => setGudangForm(f => ({ ...f, status_gudang: e.target.value }))}>
                                <option value="aktif">Aktif</option>
                                <option value="penuh">Penuh</option>
                                <option value="maintenance">Maintenance</option>
                            </select>
                        </div>
                        <div className="flex gap-3 pt-2">
                            <button type="submit" className={btnPrimary} disabled={loading}>
                                {loading ? 'Menyimpan...' : 'Simpan Gudang'}
                            </button>
                            <button type="button" className={btnSecondary} onClick={() => setShowAddGudang(false)}>Batal</button>
                        </div>
                    </form>
                </Modal>
            )}

            {/* MODAL: Edit Stok Gudang */}
            {editingGudang && (
                <Modal title={`📦 Update Stok — ${editingGudang.nama_gudang}`} onClose={() => setEditingGudang(null)}>
                    <form onSubmit={handleUpdateStock} className="space-y-4">
                        <div className="grid grid-cols-2 gap-4">
                            <div>
                                <label className={labelCls}>Stok Saat Ini (Galon)</label>
                                <input type="number" min="0" className={inputCls} value={stockForm.stok_saat_ini} onChange={e => setStockForm(f => ({ ...f, stok_saat_ini: e.target.value }))} required />
                            </div>
                            <div>
                                <label className={labelCls}>Status</label>
                                <select className={inputCls} value={stockForm.status_gudang} onChange={e => setStockForm(f => ({ ...f, status_gudang: e.target.value }))}>
                                    <option value="aktif">Aktif</option>
                                    <option value="penuh">Penuh</option>
                                    <option value="maintenance">Maintenance</option>
                                </select>
                            </div>
                        </div>
                        <div className="flex gap-3 pt-2">
                            <button type="submit" className={btnPrimary} disabled={loading}>
                                {loading ? 'Menyimpan...' : 'Perbarui Stok'}
                            </button>
                            <button type="button" className={btnSecondary} onClick={() => setEditingGudang(null)}>Batal</button>
                        </div>
                    </form>
                </Modal>
            )}

            {/* MODAL: Add Kurir */}
            {showAddKurir && (
                <Modal title="🛵 Tambah Kurir Baru" onClose={() => setShowAddKurir(false)}>
                    <form onSubmit={handleAddKurir} className="space-y-4">
                        <div className="grid grid-cols-2 gap-4">
                            <div>
                                <label className={labelCls}>Nama Kurir</label>
                                <input className={inputCls} value={kurirForm.nama_kurir} onChange={e => setKurirForm(f => ({ ...f, nama_kurir: e.target.value }))} placeholder="ex: Doni Saputra" required />
                            </div>
                            <div>
                                <label className={labelCls}>No. HP</label>
                                <input className={inputCls} value={kurirForm.no_hp} onChange={e => setKurirForm(f => ({ ...f, no_hp: e.target.value }))} placeholder="ex: 0812-3456-7890" required />
                            </div>
                            <div>
                                <label className={labelCls}>Kendaraan</label>
                                <input className={inputCls} value={kurirForm.kendaraan} onChange={e => setKurirForm(f => ({ ...f, kendaraan: e.target.value }))} placeholder="ex: Motor Box Honda" required />
                            </div>
                            <div>
                                <label className={labelCls}>Plat Nomor</label>
                                <input className={inputCls} value={kurirForm.plat_nomor} onChange={e => setKurirForm(f => ({ ...f, plat_nomor: e.target.value }))} placeholder="ex: B 4521 RWD" required />
                            </div>
                        </div>
                        <div>
                            <label className={labelCls}>Alamat (opsional)</label>
                            <input className={inputCls} value={kurirForm.alamat} onChange={e => setKurirForm(f => ({ ...f, alamat: e.target.value }))} placeholder="ex: Jl. Mangga No. 5, Depok" />
                        </div>
                        <div>
                            <label className={labelCls}>Status Awal</label>
                            <select className={inputCls} value={kurirForm.status_kurir} onChange={e => setKurirForm(f => ({ ...f, status_kurir: e.target.value }))}>
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
                        </div>
                        <div>
                            <label className={labelCls}>Catatan (opsional)</label>
                            <textarea className={inputCls} rows="2" value={kurirForm.catatan} onChange={e => setKurirForm(f => ({ ...f, catatan: e.target.value }))} placeholder="Catatan tambahan..." />
                        </div>
                        <div className="flex gap-3 pt-2">
                            <button type="submit" className={btnPrimary} disabled={loading}>
                                {loading ? 'Menyimpan...' : 'Simpan Kurir'}
                            </button>
                            <button type="button" className={btnSecondary} onClick={() => setShowAddKurir(false)}>Batal</button>
                        </div>
                    </form>
                </Modal>
            )}

            {/* ── LEFT SIDEBAR NAVIGATION ──────────────────────────── */}
            <aside className="w-64 bg-slate-900 text-slate-400 p-6 flex flex-col justify-between border-r border-white/5 shrink-0 hidden md:flex">
                <div className="space-y-8">
                    <div className="flex items-center gap-3">
                        <div className="w-9 h-9 rounded-xl bg-gradient-to-tr from-blue-600 to-sky-400 flex items-center justify-center text-white shadow-md">
                            <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2.5">
                                <path strokeLinecap="round" strokeLinejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                            </svg>
                        </div>
                        <span className="text-lg font-black text-white tracking-tight">Rindu Water</span>
                    </div>

                    <nav className="space-y-1.5">
                        <span className="block text-[9px] font-bold text-slate-500 uppercase tracking-widest mb-3">Administrasi Menu</span>
                        {[
                            { id: 'overview',  icon: '📊', label: 'Ikhtisar Data' },
                            { id: 'transaksi', icon: '💸', label: 'Data Transaksi' },
                            { id: 'pelanggan', icon: '👥', label: 'Kelola Pelanggan' },
                            { id: 'gudang',    icon: '🏬', label: 'Stok Gudang' },
                            { id: 'kurir',     icon: '🛵', label: 'Kelola Kurir' },
                            { id: 'langganan', icon: '💧', label: 'Jadwal Langganan' },
                        ].map(({ id, icon, label }) => (
                            <button
                                key={id}
                                onClick={() => { setActiveTab(id); setSearchQuery(''); }}
                                className={`w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-bold transition-all ${activeTab === id ? 'bg-blue-600 text-white shadow-md' : 'hover:text-white hover:bg-white/5'}`}
                            >
                                <span>{icon}</span> <span>{label}</span>
                            </button>
                        ))}
                    </nav>
                </div>

                <div className="pt-6 border-t border-white/5">
                    <button
                        onClick={handleLogout}
                        className="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-bold text-rose-400 hover:text-rose-300 hover:bg-rose-500/10 transition-all"
                    >
                        <span>🔐</span> <span>Keluar Portal</span>
                    </button>
                </div>
            </aside>

            {/* ── MAIN CONTENT ─────────────────────────────────────── */}
            <main className="flex-1 flex flex-col min-w-0">
                {/* TOPBAR */}
                <header className="px-8 py-5 flex items-center justify-between border-b border-slate-200 dark:border-white/5 relative z-20 bg-white dark:bg-slate-950/20 backdrop-blur-md">
                    <h1 className="text-lg font-black tracking-tight text-slate-800 dark:text-white uppercase">
                        {activeTab === 'overview'  && 'Ikhtisar Portal'}
                        {activeTab === 'transaksi' && 'Data Transaksi'}
                        {activeTab === 'pelanggan' && 'Kelola Pelanggan'}
                        {activeTab === 'gudang'    && 'Stok Gudang'}
                        {activeTab === 'kurir'     && 'Kelola Kurir'}
                        {activeTab === 'langganan' && 'Jadwal Langganan'}
                    </h1>

                    <div className="flex items-center gap-4">
                        <button onClick={toggleTheme} className="p-2.5 rounded-xl border border-slate-200 dark:border-white/10 hover:bg-slate-100 dark:hover:bg-white/5 transition">
                            {darkMode ? '☀️' : '🌙'}
                        </button>

                        <div className="relative">
                            <button
                                onClick={() => setAdminDropdown(!adminDropdown)}
                                className="flex items-center gap-3 p-1.5 pr-4 rounded-xl border border-slate-200 dark:border-white/10 hover:bg-slate-100 dark:hover:bg-white/5 transition"
                            >
                                <div className="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center text-white font-black text-xs">AD</div>
                                <span className="text-xs font-bold hidden sm:inline text-slate-700 dark:text-slate-200">Administrator</span>
                            </button>

                            {adminDropdown && (
                                <div className="absolute right-0 mt-2 w-48 bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/10 rounded-xl shadow-xl py-2 z-50 animate-fadeIn">
                                    <button onClick={handleLogout} className="w-full text-left px-4 py-2.5 text-xs text-rose-500 hover:bg-slate-50 dark:hover:bg-white/5 font-bold transition">
                                        Log Out Portal
                                    </button>
                                </div>
                            )}
                        </div>
                    </div>
                </header>

                {/* DASHBOARD BODY */}
                <div className="p-8 space-y-8 overflow-y-auto max-h-[calc(100vh-80px)]">

                    {/* SEARCH + TAB PILLS */}
                    <div className="flex flex-col sm:flex-row gap-4 justify-between items-center bg-white dark:bg-slate-900/55 p-4 rounded-2xl border border-slate-200 dark:border-white/5">
                        <div className="relative w-full sm:max-w-md">
                            <span className="absolute left-4 top-1/2 -translate-y-1/2 text-sm opacity-55">🔍</span>
                            <input
                                type="text"
                                value={searchQuery}
                                onChange={e => setSearchQuery(e.target.value)}
                                placeholder={`Cari di tab ${activeTab}...`}
                                className="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-white/10 rounded-xl text-xs focus:outline-none focus:border-blue-500 transition text-slate-800 dark:text-slate-200"
                            />
                        </div>
                        <div className="flex flex-wrap gap-1">
                            {['overview','transaksi','pelanggan','gudang','kurir','langganan'].map(tab => (
                                <button
                                    key={tab}
                                    onClick={() => { setActiveTab(tab); setSearchQuery(''); }}
                                    className={`px-3 py-1.5 rounded-lg text-[9px] font-extrabold tracking-wider uppercase border transition ${activeTab === tab ? 'bg-blue-600 border-blue-600 text-white' : 'border-slate-200 dark:border-white/5 text-slate-600 dark:text-slate-400'}`}
                                >
                                    {tab === 'overview' ? 'Ikhtisar' : tab}
                                </button>
                            ))}
                        </div>
                    </div>

                    {/* ── TAB: OVERVIEW ─────────────────────────────────── */}
                    {activeTab === 'overview' && (
                        <div className="space-y-8 animate-fadeIn">
                            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-5">
                                {[
                                    { label: 'Pendapatan',     value: formatRupiah(stats.total_pendapatan),   icon: '💰', color: 'emerald' },
                                    { label: 'Transaksi',      value: `${stats.total_transaksi} Order`,       icon: '📦', color: 'blue'    },
                                    { label: 'Stok Gudang',    value: `${stats.stok_total} Galon`,            icon: '🏬', color: 'sky'     },
                                    { label: 'Gudang Aktif',   value: `${stats.gudang_aktif} Unit`,           icon: '🏗️', color: 'indigo'  },
                                    { label: 'Kurir Aktif',    value: `${stats.kurir_aktif} Orang`,           icon: '🛵', color: 'violet'  },
                                    { label: 'Langganan',      value: `${stats.langganan_aktif} Paket`,       icon: '💧', color: 'cyan'    },
                                ].map(({ label, value, icon, color }) => (
                                    <div key={label} className="bg-white dark:bg-slate-900/50 p-5 rounded-2xl border border-slate-200 dark:border-white/5 space-y-2 shadow-sm hover:scale-[1.01] transition-all xl:col-span-1 sm:col-span-1">
                                        <div className="flex justify-between items-center">
                                            <span className="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{label}</span>
                                            <span className={`p-1.5 bg-${color}-500/10 text-${color}-500 rounded-lg text-base`}>{icon}</span>
                                        </div>
                                        <h3 className="text-lg font-black text-slate-800 dark:text-white tracking-tight">{value}</h3>
                                    </div>
                                ))}
                            </div>

                            {/* Recent Transaksi */}
                            <div className="bg-white dark:bg-slate-900/50 rounded-3xl border border-slate-200 dark:border-white/5 p-6 space-y-5 shadow-sm">
                                <div className="flex justify-between items-center">
                                    <h3 className="text-sm font-black text-slate-800 dark:text-white uppercase tracking-wider">Transaksi Terbaru</h3>
                                    <button onClick={() => setActiveTab('transaksi')} className="text-xs font-bold text-blue-500 hover:underline">Lihat Semua →</button>
                                </div>
                                <div className="overflow-x-auto">
                                    <table className="w-full text-left text-xs">
                                        <thead>
                                            <tr className="border-b border-slate-200 dark:border-white/5 text-slate-400 font-bold uppercase tracking-wider">
                                                <th className="py-3">Invoice</th>
                                                <th className="py-3">Pelanggan</th>
                                                <th className="py-3">Metode</th>
                                                <th className="py-3">Total</th>
                                                <th className="py-3">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody className="divide-y divide-slate-100 dark:divide-white/5 font-medium">
                                            {filteredTransaksi.slice(0, 5).map(t => (
                                                <tr key={t.id_transaksi} className="hover:bg-slate-50 dark:hover:bg-white/5 transition">
                                                    <td className="py-3 font-bold text-blue-500">{t.kode_invoice}</td>
                                                    <td className="py-3">{t.nama_pelanggan}</td>
                                                    <td className="py-3 uppercase">{t.metode_pembayaran}</td>
                                                    <td className="py-3 font-bold">{formatRupiah(t.total_bayar)}</td>
                                                    <td className="py-3"><StatusBadge status={t.status_transaksi} /></td>
                                                </tr>
                                            ))}
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {/* Quick Links Grid */}
                            <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                                {[
                                    { tab: 'gudang',    icon: '🏬', label: 'Tambah Stok Gudang',    desc: `${listGudang.length} gudang terdaftar` },
                                    { tab: 'kurir',     icon: '🛵', label: 'Manajemen Kurir',        desc: `${listKurir.length} kurir terdaftar` },
                                    { tab: 'pelanggan', icon: '👥', label: 'Kelola Pelanggan',       desc: `${listPelanggan.length} pelanggan` },
                                    { tab: 'langganan', icon: '💧', label: 'Jadwal Langganan',       desc: `${listLangganan.length} jadwal aktif` },
                                ].map(({ tab, icon, label, desc }) => (
                                    <button
                                        key={tab}
                                        onClick={() => setActiveTab(tab)}
                                        className="bg-white dark:bg-slate-900/50 p-5 rounded-2xl border border-slate-200 dark:border-white/5 text-left hover:border-blue-400 dark:hover:border-blue-600 hover:shadow-md transition-all group"
                                    >
                                        <span className="text-2xl mb-2 block">{icon}</span>
                                        <h4 className="text-xs font-black text-slate-800 dark:text-white group-hover:text-blue-500 transition">{label}</h4>
                                        <p className="text-[10px] text-slate-400 mt-0.5">{desc}</p>
                                    </button>
                                ))}
                            </div>
                        </div>
                    )}

                    {/* ── TAB: TRANSAKSI ────────────────────────────────── */}
                    {activeTab === 'transaksi' && (
                        <div className="bg-white dark:bg-slate-900/50 rounded-3xl border border-slate-200 dark:border-white/5 p-6 space-y-5 shadow-sm animate-fadeIn">
                            <div className="flex justify-between items-center">
                                <h3 className="text-sm font-black text-slate-800 dark:text-white uppercase tracking-wider">Semua Transaksi & Pembayaran</h3>
                                <span className="text-[10px] font-bold text-slate-400">{filteredTransaksi.length} transaksi</span>
                            </div>
                            <div className="overflow-x-auto">
                                <table className="w-full text-left text-xs">
                                    <thead>
                                        <tr className="border-b border-slate-200 dark:border-white/5 text-slate-400 font-bold uppercase tracking-wider">
                                            <th className="py-3">Invoice</th>
                                            <th className="py-3">Pelanggan</th>
                                            <th className="py-3">Tanggal</th>
                                            <th className="py-3">Metode</th>
                                            <th className="py-3">Total</th>
                                            <th className="py-3">Status</th>
                                            <th className="py-3">Ubah Status</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-slate-100 dark:divide-white/5 font-medium">
                                        {filteredTransaksi.map(t => (
                                            <tr key={t.id_transaksi} className="hover:bg-slate-50 dark:hover:bg-white/5 transition">
                                                <td className="py-3 font-bold text-blue-500">{t.kode_invoice}</td>
                                                <td className="py-3">{t.nama_pelanggan}</td>
                                                <td className="py-3">{new Date(t.tanggal_transaksi || t.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })}</td>
                                                <td className="py-3 uppercase">{t.metode_pembayaran}</td>
                                                <td className="py-3 font-bold">{formatRupiah(t.total_bayar)}</td>
                                                <td className="py-3"><StatusBadge status={t.status_transaksi} /></td>
                                                <td className="py-3">
                                                    <select
                                                        value={t.status_transaksi}
                                                        onChange={e => handleTransaksiStatus(t.id_transaksi, e.target.value)}
                                                        className="px-2 py-1.5 bg-slate-100 dark:bg-slate-950 border border-slate-200 dark:border-white/10 rounded-lg text-[10px] font-bold focus:outline-none cursor-pointer"
                                                    >
                                                        {['menunggu','dibayar','diproses','dikirim','selesai','batal'].map(s => (
                                                            <option key={s} value={s}>{s.charAt(0).toUpperCase() + s.slice(1)}</option>
                                                        ))}
                                                    </select>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                                {filteredTransaksi.length === 0 && (
                                    <p className="py-12 text-center text-slate-400 text-xs font-medium">Tidak ada transaksi ditemukan.</p>
                                )}
                            </div>
                        </div>
                    )}

                    {/* ── TAB: PELANGGAN ────────────────────────────────── */}
                    {activeTab === 'pelanggan' && (
                        <div className="bg-white dark:bg-slate-900/50 rounded-3xl border border-slate-200 dark:border-white/5 p-6 space-y-5 shadow-sm animate-fadeIn">
                            <div className="flex justify-between items-center">
                                <h3 className="text-sm font-black text-slate-800 dark:text-white uppercase tracking-wider">Manajemen Pelanggan Terdaftar</h3>
                                <span className="text-[10px] font-bold text-slate-400">{filteredPelanggan.length} pelanggan</span>
                            </div>
                            <div className="overflow-x-auto">
                                <table className="w-full text-left text-xs">
                                    <thead>
                                        <tr className="border-b border-slate-200 dark:border-white/5 text-slate-400 font-bold uppercase tracking-wider">
                                            <th className="py-3">ID</th>
                                            <th className="py-3">Nama</th>
                                            <th className="py-3">Tipe</th>
                                            <th className="py-3">Kontak</th>
                                            <th className="py-3">Alamat</th>
                                            <th className="py-3">Status</th>
                                            <th className="py-3">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-slate-100 dark:divide-white/5 font-medium">
                                        {filteredPelanggan.map(p => (
                                            <tr key={p.id_pelanggan} className="hover:bg-slate-50 dark:hover:bg-white/5 transition">
                                                <td className="py-3 text-slate-400 font-bold">#{p.id_pelanggan}</td>
                                                <td className="py-3 font-bold">{p.nama_pelanggan}</td>
                                                <td className="py-3">
                                                    <span className={`px-2 py-0.5 rounded text-[9px] font-bold uppercase ${p.jenis_pelanggan === 'lembaga' ? 'bg-indigo-500/10 text-indigo-500' : 'bg-blue-500/10 text-blue-500'}`}>
                                                        {p.jenis_pelanggan}
                                                    </span>
                                                </td>
                                                <td className="py-3">
                                                    <span className="block font-semibold">{p.email}</span>
                                                    <span className="block text-[10px] text-slate-400">{p.no_telepon}</span>
                                                </td>
                                                <td className="py-3 truncate max-w-[160px] text-slate-600 dark:text-slate-400">{p.alamat}</td>
                                                <td className="py-3"><StatusBadge status={p.status_pelanggan || 'aktif'} /></td>
                                                <td className="py-3">
                                                    <button
                                                        onClick={() => handlePelangganStatus(p.id_pelanggan, p.status_pelanggan === 'aktif' ? 'tidak aktif' : 'aktif')}
                                                        className={`px-3 py-1.5 rounded-lg text-[10px] font-bold transition ${p.status_pelanggan === 'aktif'
                                                            ? 'bg-rose-500/10 text-rose-500 hover:bg-rose-500/20'
                                                            : 'bg-emerald-500/10 text-emerald-500 hover:bg-emerald-500/20'}`}
                                                    >
                                                        {p.status_pelanggan === 'aktif' ? 'Nonaktifkan' : 'Aktifkan'}
                                                    </button>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                                {filteredPelanggan.length === 0 && (
                                    <p className="py-12 text-center text-slate-400 text-xs font-medium">Tidak ada pelanggan ditemukan.</p>
                                )}
                            </div>
                        </div>
                    )}

                    {/* ── TAB: GUDANG ───────────────────────────────────── */}
                    {activeTab === 'gudang' && (
                        <div className="space-y-6 animate-fadeIn">
                            <div className="flex justify-between items-center">
                                <h3 className="text-sm font-black text-slate-800 dark:text-white uppercase tracking-wider">
                                    Manajemen Stok Gudang <span className="text-slate-400 font-normal normal-case text-xs ml-2">({filteredGudang.length} gudang)</span>
                                </h3>
                                <button
                                    onClick={() => setShowAddGudang(true)}
                                    className="flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-md"
                                >
                                    <span>+</span> Tambah Gudang
                                </button>
                            </div>

                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {filteredGudang.map(g => {
                                    const pct = Math.min(100, g.kapasitas_total > 0 ? Math.round((g.stok_saat_ini / g.kapasitas_total) * 100) : 0);
                                    const barColor = pct > 85 ? 'bg-rose-500' : pct > 60 ? 'bg-amber-500' : 'bg-blue-600';
                                    return (
                                        <div key={g.id_gudang} className="bg-white dark:bg-slate-900/50 p-6 rounded-3xl border border-slate-200 dark:border-white/5 space-y-4 hover:shadow-md transition-all">
                                            <div className="flex justify-between items-start">
                                                <div>
                                                    <span className="px-2 py-0.5 rounded text-[8px] bg-blue-500/10 text-blue-500 font-black uppercase tracking-wider">UNIT {g.id_gudang}</span>
                                                    <h3 className="text-base font-black text-slate-800 dark:text-white mt-1">{g.nama_gudang}</h3>
                                                    <p className="text-[10px] text-slate-400">📍 {g.lokasi}</p>
                                                </div>
                                                <StatusBadge status={g.status_gudang} />
                                            </div>
                                            <div className="space-y-2">
                                                <div className="flex justify-between text-xs font-bold text-slate-500">
                                                    <span>Kapasitas ({pct}%)</span>
                                                    <span>{g.stok_saat_ini?.toLocaleString('id-ID')} / {g.kapasitas_total?.toLocaleString('id-ID')} Galon</span>
                                                </div>
                                                <div className="w-full h-2.5 bg-slate-100 dark:bg-slate-950 rounded-full overflow-hidden">
                                                    <div className={`h-full ${barColor} rounded-full transition-all duration-700`} style={{ width: `${pct}%` }} />
                                                </div>
                                            </div>
                                            <button
                                                onClick={() => {
                                                    setEditingGudang(g);
                                                    setStockForm({ stok_saat_ini: g.stok_saat_ini, status_gudang: g.status_gudang });
                                                }}
                                                className="w-full py-2 rounded-xl bg-blue-500/10 text-blue-500 hover:bg-blue-500/20 text-[10px] font-bold transition"
                                            >
                                                ✏️ Update Stok & Status
                                            </button>
                                        </div>
                                    );
                                })}

                                {filteredGudang.length === 0 && (
                                    <div className="col-span-2 py-16 text-center text-slate-400 text-xs">
                                        <span className="text-4xl mb-3 block">🏬</span>
                                        Belum ada gudang terdaftar.
                                    </div>
                                )}
                            </div>
                        </div>
                    )}

                    {/* ── TAB: KURIR ────────────────────────────────────── */}
                    {activeTab === 'kurir' && (
                        <div className="space-y-6 animate-fadeIn">
                            <div className="flex justify-between items-center">
                                <h3 className="text-sm font-black text-slate-800 dark:text-white uppercase tracking-wider">
                                    Armada Kurir <span className="text-slate-400 font-normal normal-case text-xs ml-2">({filteredKurir.length} kurir)</span>
                                </h3>
                                <button
                                    onClick={() => setShowAddKurir(true)}
                                    className="flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-md"
                                >
                                    <span>+</span> Tambah Kurir
                                </button>
                            </div>

                            <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                                {filteredKurir.map(k => (
                                    <div key={k.id_kurir} className="bg-white dark:bg-slate-900/50 p-6 rounded-3xl border border-slate-200 dark:border-white/5 text-center space-y-4 hover:shadow-md transition-all">
                                        <div className={`w-16 h-16 rounded-2xl flex items-center justify-center text-3xl mx-auto ${k.status_kurir === 'aktif' ? 'bg-blue-600/10' : 'bg-slate-400/10'}`}>
                                            🛵
                                        </div>
                                        <div>
                                            <h3 className="text-sm font-black text-slate-800 dark:text-white">{k.nama_kurir}</h3>
                                            <p className="text-[10px] text-slate-400 mt-0.5">📞 {k.no_hp}</p>
                                        </div>
                                        <div className="pt-2 border-t border-slate-100 dark:border-white/5 space-y-1">
                                            <span className="block text-[10px] font-bold text-slate-700 dark:text-slate-300">🚗 {k.kendaraan}</span>
                                            <span className="block text-[9px] font-bold text-slate-400 tracking-wider uppercase">{k.plat_nomor}</span>
                                        </div>
                                        <div className="space-y-2">
                                            <StatusBadge status={k.status_kurir} />
                                            <button
                                                onClick={() => handleKurirToggle(k.id_kurir)}
                                                className={`w-full py-2 rounded-xl text-[10px] font-bold transition mt-1 ${k.status_kurir === 'aktif'
                                                    ? 'bg-rose-500/10 text-rose-500 hover:bg-rose-500/20'
                                                    : 'bg-emerald-500/10 text-emerald-500 hover:bg-emerald-500/20'}`}
                                            >
                                                {k.status_kurir === 'aktif' ? '⛔ Nonaktifkan' : '✅ Aktifkan'}
                                            </button>
                                        </div>
                                    </div>
                                ))}

                                {filteredKurir.length === 0 && (
                                    <div className="col-span-3 py-16 text-center text-slate-400 text-xs">
                                        <span className="text-4xl mb-3 block">🛵</span>
                                        Belum ada kurir terdaftar.
                                    </div>
                                )}
                            </div>
                        </div>
                    )}

                    {/* ── TAB: LANGGANAN ────────────────────────────────── */}
                    {activeTab === 'langganan' && (
                        <div className="bg-white dark:bg-slate-900/50 rounded-3xl border border-slate-200 dark:border-white/5 p-6 space-y-5 shadow-sm animate-fadeIn">
                            <div className="flex justify-between items-center">
                                <h3 className="text-sm font-black text-slate-800 dark:text-white uppercase tracking-wider">Jadwal Langganan Pelanggan</h3>
                                <span className="text-[10px] font-bold text-slate-400">{filteredLangganan.length} jadwal</span>
                            </div>
                            <div className="overflow-x-auto">
                                <table className="w-full text-left text-xs">
                                    <thead>
                                        <tr className="border-b border-slate-200 dark:border-white/5 text-slate-400 font-bold uppercase tracking-wider">
                                            <th className="py-3">Pelanggan</th>
                                            <th className="py-3">Kontak</th>
                                            <th className="py-3">Frekuensi</th>
                                            <th className="py-3">Jumlah</th>
                                            <th className="py-3">Periode</th>
                                            <th className="py-3">Status</th>
                                            <th className="py-3">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-slate-100 dark:divide-white/5 font-medium">
                                        {filteredLangganan.map(l => (
                                            <tr key={l.id_langganan} className="hover:bg-slate-50 dark:hover:bg-white/5 transition">
                                                <td className="py-3 font-bold">{l.nama_pelanggan}</td>
                                                <td className="py-3">
                                                    <span className="block font-semibold">{l.email}</span>
                                                    <span className="block text-[10px] text-slate-400">{l.no_telepon}</span>
                                                </td>
                                                <td className="py-3">
                                                    <span className="px-2.5 py-1 rounded-lg text-[9px] font-bold uppercase bg-blue-500/10 text-blue-500">
                                                        {l.periode_pengantaran}
                                                    </span>
                                                </td>
                                                <td className="py-3 font-bold">{l.jumlah_pesanan} Galon / Antar</td>
                                                <td className="py-3 text-[10px] text-slate-400">
                                                    <span className="block">{l.tanggal_mulai ? new Date(l.tanggal_mulai).toLocaleDateString('id-ID', { day:'numeric', month:'short', year:'numeric' }) : '-'}</span>
                                                    <span className="block">s/d {l.tanggal_berakhir ? new Date(l.tanggal_berakhir).toLocaleDateString('id-ID', { day:'numeric', month:'short', year:'numeric' }) : '-'}</span>
                                                </td>
                                                <td className="py-3"><StatusBadge status={l.status_langganan || 'aktif'} /></td>
                                                <td className="py-3">
                                                    <select
                                                        value={l.status_langganan || 'aktif'}
                                                        onChange={e => handleLanggananStatus(l.id_langganan, e.target.value)}
                                                        className="px-2 py-1.5 bg-slate-100 dark:bg-slate-950 border border-slate-200 dark:border-white/10 rounded-lg text-[10px] font-bold focus:outline-none cursor-pointer"
                                                    >
                                                        {['aktif','nonaktif','selesai'].map(s => (
                                                            <option key={s} value={s}>{s.charAt(0).toUpperCase() + s.slice(1)}</option>
                                                        ))}
                                                    </select>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                                {filteredLangganan.length === 0 && (
                                    <p className="py-12 text-center text-slate-400 text-xs font-medium">Belum ada jadwal langganan terdaftar. Jadwal akan otomatis muncul saat pelanggan berlangganan.</p>
                                )}
                            </div>
                        </div>
                    )}

                </div>
            </main>
        </div>
    );
}
