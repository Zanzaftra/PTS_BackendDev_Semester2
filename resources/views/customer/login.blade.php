@php
    $useVite = file_exists(public_path('hot')) || file_exists(public_path('build/manifest.json'));
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Login Portal Pelanggan Rindu Water - Lacak pesanan & jadwal pasokan air mineral Anda.">
    <title>Login Pelanggan &mdash; Rindu Water</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;900&display=swap" rel="stylesheet">

    @if ($useVite)
        @vite(['resources/css/app.css', 'resources/js/customer-login-app.jsx'])
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
            from { opacity: 0; transform: translateY(6px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeIn { animation: fadeIn 0.35s ease forwards; }
    </style>
</head>
<body>
    <div id="customer-login-root"></div>

    @if (!$useVite)
    <!-- Fallback React Component Compiler -->
    <script type="text/babel">
        function CustomerLoginFallback() {
            const [email, setEmail] = React.useState('');
            const [error, setError] = React.useState('');
            const [loading, setLoading] = React.useState(false);
            const [emailError, setEmailError] = React.useState('');

            const validateEmail = (val) => {
                if (!val) {
                    setEmailError('Email wajib diisi.');
                    return false;
                }
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(val)) {
                    setEmailError('Format email tidak valid.');
                    return false;
                }
                setEmailError('');
                return true;
            };

            const handleSubmit = async (e) => {
                e.preventDefault();
                setError('');

                const isEmailValid = validateEmail(email);
                if (!isEmailValid) return;

                setLoading(true);
                try {
                    const response = await fetch('/customer/login', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ email })
                    });

                    const data = await response.json();
                    setLoading(false);

                    if (data.success) {
                        window.location.href = data.redirect;
                    } else {
                        setError(data.message || 'Email tidak terdaftar.');
                    }
                } catch (err) {
                    setLoading(false);
                    setError('Gagal menghubungkan ke server. Silakan coba kembali.');
                }
            };

            return (
                <div className="min-h-screen bg-gradient-to-tr from-slate-950 via-slate-900 to-sky-950 flex items-center justify-center p-6 relative overflow-hidden">
                    <div className="absolute top-0 right-0 w-[40vw] h-[40vw] bg-sky-500/10 rounded-full blur-[100px] pointer-events-none -z-10"></div>
                    <div className="absolute bottom-0 left-0 w-[35vw] h-[35vw] bg-teal-500/10 rounded-full blur-[100px] pointer-events-none -z-10"></div>

                    <div className="w-full max-w-md bg-slate-900/50 backdrop-blur-xl border border-white/10 p-8 md:p-10 rounded-[2.5rem] shadow-2xl space-y-8 relative">
                        <div className="text-center space-y-3">
                            <div className="w-14 h-14 bg-gradient-to-tr from-sky-500 to-teal-400 rounded-2xl flex items-center justify-center mx-auto shadow-lg shadow-sky-500/20">
                                <svg xmlns="http://www.w3.org/2000/svg" className="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2.5">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <h2 className="text-2xl font-black text-white tracking-tight">Portal Pelanggan</h2>
                            <p className="text-xs text-slate-400 font-light font-sans">Masukkan email terdaftar Anda untuk melihat pesanan & status pengantaran air Anda</p>
                        </div>

                        {error && (
                            <div className="p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs font-semibold flex items-center gap-3 animate-fadeIn">
                                <span className="w-5 h-5 rounded-full bg-rose-500 text-white flex items-center justify-center shrink-0 text-[10px] font-bold">!</span>
                                <span>{error}</span>
                            </div>
                        )}

                        <form onSubmit={handleSubmit} className="space-y-6">
                            <div className="space-y-2">
                                <label className="block text-xs font-bold text-slate-400 uppercase tracking-widest font-sans">Alamat Email Pelanggan</label>
                                <div className="relative">
                                    <input 
                                        type="email" 
                                        value={email}
                                        onChange={(e) => {
                                            setEmail(e.target.value);
                                            if(emailError) validateEmail(e.target.value);
                                        }}
                                        onBlur={(e) => validateEmail(e.target.value)}
                                        required
                                        placeholder="nama@email.com" 
                                        className={`w-full px-4 py-3.5 bg-slate-950/40 border ${emailError ? 'border-rose-500/50' : 'border-white/10 focus:border-sky-500'} rounded-xl text-sm focus:outline-none text-white transition shadow-inner font-sans`}
                                    />
                                </div>
                                {emailError && (
                                    <span className="text-[10px] font-semibold text-rose-400 block">{emailError}</span>
                                )}
                            </div>

                            <button 
                                type="submit" 
                                disabled={loading}
                                className="w-full py-4 bg-gradient-to-r from-sky-500 to-teal-400 hover:from-sky-600 hover:to-teal-500 text-white rounded-xl text-xs font-bold tracking-widest uppercase shadow-lg shadow-sky-500/10 hover:shadow-sky-500/20 active:scale-[0.99] hover:scale-[1.01] transition-all flex items-center justify-center gap-3 font-sans"
                            >
                                {loading ? (
                                    <svg className="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                                        <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                ) : null}
                                <span>{loading ? 'Menghubungkan...' : 'Masuk Dashboard Saya'}</span>
                            </button>
                        </form>

                        <div className="p-4 bg-white/5 rounded-xl border border-white/5 text-[11px] text-slate-400 font-light leading-relaxed font-sans">
                            <span className="font-semibold text-slate-200 block mb-1">💡 Uji Coba Demo</span>
                            Gunakan email <span className="font-bold text-sky-400 font-sans">customer@rinduwater.com</span> atau <span className="font-bold text-sky-400 font-sans">zaki@example.com</span> untuk login langsung.
                            <br/><br/>
                            Belum pesan air? Kembali ke <a href="/" className="font-bold text-sky-400 hover:underline">Halaman Utama</a> dan pesan sekarang!
                        </div>
                    </div>
                </div>
            );
        }

        const root = ReactDOM.createRoot(document.getElementById('customer-login-root'));
        root.render(<CustomerLoginFallback />);
    </script>
    @endif
</body>
</html>
