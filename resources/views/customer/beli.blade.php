@php
    $useVite = file_exists(public_path('hot')) || file_exists(public_path('build/manifest.json'));
@endphp
<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>Beli Air &mdash; Rindu Water</title>
    @if ($useVite)
        @vite(['resources/css/app.css'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 antialiased">
    <main class="min-h-screen px-4 py-8 md:px-8">
        <div class="mx-auto flex max-w-6xl flex-col gap-8">
            <header class="rounded-[32px] border border-white/10 bg-white/8 p-6 shadow-[0_20px_60px_-20px_rgba(56,189,248,0.35)] backdrop-blur-xl md:p-8">
                <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                    <div class="space-y-3">
                        <p class="text-[10px] font-black uppercase tracking-[0.35em] text-sky-300">Portal Pelanggan</p>
                        <h1 class="text-3xl font-black tracking-tight text-white md:text-4xl">Beli Air Mineral dengan Tampilan yang Lebih Modern</h1>
                        <p class="max-w-2xl text-sm text-slate-300">Pesan air galon, pilih paket langganan, dan lihat riwayat pembelian Anda dari satu tempat yang lebih rapi dan nyaman dipakai.</p>
                    </div>
                    <a href="/customer/dashboard" class="inline-flex items-center justify-center rounded-2xl border border-white/10 bg-slate-900/80 px-4 py-3 text-xs font-black uppercase tracking-[0.25em] text-slate-100 transition hover:bg-slate-800">Kembali ke dashboard</a>
                </div>
            </header>

            <section class="grid gap-8 lg:grid-cols-[1.05fr_0.95fr]">
                <article class="rounded-[32px] border border-white/10 bg-white/6 p-6 shadow-[0_25px_70px_-25px_rgba(15,23,42,0.7)] backdrop-blur-2xl md:p-8">
                    <div class="space-y-6">
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-[0.35em] text-emerald-300">Fitur Pembelian</p>
                            <h2 class="mt-2 text-2xl font-black text-white">Pesan air sesuai kebutuhan rumah, kantor, atau usaha Anda</h2>
                        </div>
                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="rounded-3xl border border-sky-400/20 bg-gradient-to-br from-sky-400/12 to-cyan-400/5 p-4">
                                <p class="text-[10px] uppercase tracking-[0.35em] text-sky-100">Pilih Produk</p>
                                <p class="mt-2 text-xl font-black text-white">Gallon 19L & Botol 600ml</p>
                            </div>
                            <div class="rounded-3xl border border-emerald-400/20 bg-gradient-to-br from-emerald-400/12 to-lime-400/5 p-4">
                                <p class="text-[10px] uppercase tracking-[0.35em] text-emerald-100">Metode Bayar</p>
                                <p class="mt-2 text-xl font-black text-white">Transfer / Tunai / E-wallet</p>
                            </div>
                        </div>
                        <div class="grid gap-3">
                            <div class="rounded-3xl border border-white/10 bg-slate-900/85 p-4 text-sm text-slate-200">✨ Pilih mode <strong>pesan sekali</strong> atau <strong>langganan rutin</strong>.</div>
                            <div class="rounded-3xl border border-white/10 bg-slate-900/85 p-4 text-sm text-slate-200">📍 Alamat dan kontak langsung tersimpan untuk pengiriman lebih cepat.</div>
                            <div class="rounded-3xl border border-white/10 bg-slate-900/85 p-4 text-sm text-slate-200">🧾 Riwayat pembelian otomatis muncul di dashboard setelah pesanan berhasil dibuat.</div>
                        </div>
                    </div>
                </article>

                <article class="rounded-[32px] border border-white/10 bg-gradient-to-br from-sky-500/10 via-slate-900 to-emerald-500/10 p-6 shadow-[0_25px_70px_-25px_rgba(56,189,248,0.35)] backdrop-blur-2xl md:p-8">
                    <div class="mb-6">
                        <p class="text-[10px] font-black uppercase tracking-[0.35em] text-sky-200">Form Pemesanan</p>
                        <h2 class="mt-2 text-xl font-black text-white">Isi detail pesanan Anda dengan cepat</h2>
                    </div>
                    <form id="customer-buy-form" class="space-y-5 text-sm text-slate-100">
                        <input type="hidden" name="jenis_pelanggan" value="individu" />
                        <div class="grid gap-3 rounded-3xl border border-white/10 bg-slate-950/70 p-4 md:grid-cols-2">
                            <button type="button" data-mode="one-off" class="mode-btn rounded-2xl border border-sky-400/20 bg-sky-400/10 px-4 py-3 text-left text-xs font-black uppercase tracking-[0.25em] text-sky-100">Pesan Sekali</button>
                            <button type="button" data-mode="subscription" class="mode-btn rounded-2xl border border-white/10 bg-slate-900/80 px-4 py-3 text-left text-xs font-black uppercase tracking-[0.25em] text-slate-200">Langganan Rutin</button>
                        </div>
                        <input type="hidden" name="purchase_type" id="purchase_type" value="one-off" />
                        <div class="grid gap-4 md:grid-cols-2">
                            <label class="space-y-2 text-xs font-semibold uppercase tracking-[0.25em] text-slate-300">
                                Nama pelanggan
                                <input type="text" name="nama_pelanggan" value="{{ $customer->nama_pelanggan ?? '' }}" required class="w-full rounded-2xl border border-white/10 bg-slate-900/80 px-4 py-3 text-sm text-white placeholder:text-slate-400 focus:border-sky-400 focus:outline-none" />
                            </label>
                            <label class="space-y-2 text-xs font-semibold uppercase tracking-[0.25em] text-slate-300">
                                Jenis pelanggan
                                <select name="jenis_pelanggan_select" id="jenis_pelanggan_select" class="w-full rounded-2xl border border-white/10 bg-slate-900/80 px-4 py-3 text-sm text-white focus:border-sky-400 focus:outline-none">
                                    <option value="individu">Individu</option>
                                    <option value="lembaga">Lembaga</option>
                                </select>
                            </label>
                            <label class="space-y-2 text-xs font-semibold uppercase tracking-[0.25em] text-slate-300">
                                Penanggung jawab
                                <input type="text" name="penanggung_jawab" value="{{ $customer->nama_pelanggan ?? '' }}" required class="w-full rounded-2xl border border-white/10 bg-slate-900/80 px-4 py-3 text-sm text-white placeholder:text-slate-400 focus:border-sky-400 focus:outline-none" />
                            </label>
                            <label class="space-y-2 text-xs font-semibold uppercase tracking-[0.25em] text-slate-300">
                                Nama lembaga
                                <input type="text" name="nama_lembaga" id="nama_lembaga" placeholder="Opsional" class="w-full rounded-2xl border border-white/10 bg-slate-900/80 px-4 py-3 text-sm text-white placeholder:text-slate-400 focus:border-sky-400 focus:outline-none" />
                            </label>
                            <label class="space-y-2 text-xs font-semibold uppercase tracking-[0.25em] text-slate-300 md:col-span-2">
                                Alamat lengkap
                                <textarea name="alamat" rows="3" required class="w-full rounded-2xl border border-white/10 bg-slate-900/80 px-4 py-3 text-sm text-white placeholder:text-slate-400 focus:border-sky-400 focus:outline-none">{{ $customer->alamat ?? '' }}</textarea>
                            </label>
                            <label class="space-y-2 text-xs font-semibold uppercase tracking-[0.25em] text-slate-300">
                                No. telepon
                                <input type="text" name="no_telepon" value="{{ $customer->no_telepon ?? '' }}" required class="w-full rounded-2xl border border-white/10 bg-slate-900/80 px-4 py-3 text-sm text-white placeholder:text-slate-400 focus:border-sky-400 focus:outline-none" />
                            </label>
                            <label class="space-y-2 text-xs font-semibold uppercase tracking-[0.25em] text-slate-300">
                                Email
                                <input type="email" name="email" value="{{ $customer->email ?? '' }}" required class="w-full rounded-2xl border border-white/10 bg-slate-900/80 px-4 py-3 text-sm text-white placeholder:text-slate-400 focus:border-sky-400 focus:outline-none" />
                            </label>
                            <label class="space-y-2 text-xs font-semibold uppercase tracking-[0.25em] text-slate-300">
                                Produk
                                <select name="id_produk" class="w-full rounded-2xl border border-white/10 bg-slate-900/80 px-4 py-3 text-sm text-white focus:border-sky-400 focus:outline-none">
                                    <option value="1">Gallon 19L</option>
                                    <option value="2">Botol 600ml</option>
                                </select>
                            </label>
                            <label class="space-y-2 text-xs font-semibold uppercase tracking-[0.25em] text-slate-300">
                                Jumlah
                                <input type="number" name="jumlah" min="1" value="1" required class="w-full rounded-2xl border border-white/10 bg-slate-900/80 px-4 py-3 text-sm text-white focus:border-sky-400 focus:outline-none" />
                            </label>
                            <label class="space-y-2 text-xs font-semibold uppercase tracking-[0.25em] text-slate-300">
                                Metode pembayaran
                                <select name="metode_pembayaran" class="w-full rounded-2xl border border-white/10 bg-slate-900/80 px-4 py-3 text-sm text-white focus:border-sky-400 focus:outline-none">
                                    <option value="transfer">Transfer</option>
                                    <option value="tunai">Tunai</option>
                                    <option value="e-wallet">E-wallet</option>
                                </select>
                            </label>
                            <label id="periode-wrap" class="hidden space-y-2 text-xs font-semibold uppercase tracking-[0.25em] text-slate-300">
                                Periode langganan
                                <select name="periode_pengantaran" class="w-full rounded-2xl border border-white/10 bg-slate-900/80 px-4 py-3 text-sm text-white focus:border-sky-400 focus:outline-none">
                                    <option value="mingguan">Mingguan</option>
                                    <option value="bulanan">Bulanan</option>
                                </select>
                            </label>
                            <label class="space-y-2 text-xs font-semibold uppercase tracking-[0.25em] text-slate-300 md:col-span-2">
                                Catatan (opsional)
                                <textarea name="catatan" rows="2" class="w-full rounded-2xl border border-white/10 bg-slate-900/80 px-4 py-3 text-sm text-white placeholder:text-slate-400 focus:border-sky-400 focus:outline-none" placeholder="Contoh: kirim sebelum pukul 18.00"></textarea>
                            </label>
                        </div>

                        <button type="submit" class="w-full rounded-2xl bg-gradient-to-r from-sky-500 to-emerald-400 px-4 py-3 text-xs font-black uppercase tracking-[0.35em] text-white shadow-lg shadow-sky-500/20 transition hover:scale-[1.01] hover:shadow-emerald-400/20">Buat Pesanan</button>
                        <p id="buy-feedback" class="hidden rounded-2xl border border-white/10 bg-slate-900/80 p-3 text-xs text-slate-300"></p>
                    </form>
                </article>
            </section>
        </div>
    </main>

    <script>
        const form = document.getElementById('customer-buy-form');
        const purchaseTypeInput = document.getElementById('purchase_type');
        const periodeWrap = document.getElementById('periode-wrap');
        const jenisPelangganSelect = document.getElementById('jenis_pelanggan_select');
        const namaLembagaInput = document.getElementById('nama_lembaga');

        const setMode = (mode) => {
            purchaseTypeInput.value = mode;
            document.querySelectorAll('.mode-btn').forEach((button) => {
                const active = button.dataset.mode === mode;
                button.className = `mode-btn rounded-2xl border px-4 py-3 text-left text-xs font-black uppercase tracking-[0.25em] transition ${active ? 'border-sky-400/40 bg-sky-400/15 text-sky-100' : 'border-white/10 bg-slate-900/80 text-slate-200'}`;
            });
            periodeWrap.classList.toggle('hidden', mode !== 'subscription');
        };

        document.querySelectorAll('.mode-btn').forEach((button) => {
            button.addEventListener('click', () => setMode(button.dataset.mode));
        });

        jenisPelangganSelect?.addEventListener('change', () => {
            const isLembaga = jenisPelangganSelect.value === 'lembaga';
            namaLembagaInput.required = isLembaga;
            namaLembagaInput.placeholder = isLembaga ? 'Nama lembaga / kantor' : 'Opsional';
        });

        setMode('one-off');

        form?.addEventListener('submit', async function (event) {
            event.preventDefault();
            const feedback = document.getElementById('buy-feedback');
            const data = new FormData(form);
            data.set('jenis_pelanggan', jenisPelangganSelect?.value || 'individu');

            feedback.classList.remove('hidden');
            feedback.textContent = 'Mengirim pesanan...';

            try {
                const response = await fetch('/checkout', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: data
                });
                const result = await response.json();
                feedback.textContent = result.message || 'Pesanan berhasil dibuat.';
                feedback.className = 'rounded-2xl border border-emerald-400/20 bg-emerald-400/10 p-3 text-xs text-emerald-100';
                form.reset();
                setMode('one-off');
            } catch (error) {
                feedback.textContent = 'Gagal mengirim pesanan, silakan coba lagi.';
                feedback.className = 'rounded-2xl border border-rose-400/20 bg-rose-400/10 p-3 text-xs text-rose-100';
            }
        });
    </script>
</body>
</html>
