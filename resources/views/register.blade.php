@php
    $useVite = file_exists(public_path('hot')) || file_exists(public_path('build/manifest.json'));
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Daftar Akun Portal Admin Rindu Water - Registrasi pengelola baru.">
    <title>Daftar Admin &mdash; Rindu Water</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;900&display=swap" rel="stylesheet">

    @if ($useVite)
        @vite(['resources/css/app.css', 'resources/js/register-app.jsx'])
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
    <div id="register-root"></div>

    @if (!$useVite)
    <!-- Fallback React Component Compiler -->
    <script type="text/babel">
        function RegisterFallback() {
            const [namaAdmin, setNamaAdmin] = React.useState('');
            const [username, setUsername] = React.useState('');
            const [email, setEmail] = React.useState('');
            const [password, setPassword] = React.useState('');
            const [noHp, setNoHp] = React.useState('');
            const [showPassword, setShowPassword] = React.useState(false);
            
            const [error, setError] = React.useState('');
            const [success, setSuccess] = React.useState('');
            const [loading, setLoading] = React.useState(false);

            const [namaError, setNamaError] = React.useState('');
            const [userError, setUserError] = React.useState('');
            const [emailError, setEmailError] = React.useState('');
            const [passwordError, setPasswordError] = React.useState('');

            const validateNama = (val) => {
                if (!val) {
                    setNamaError('Nama lengkap wajib diisi.');
                    return false;
                }
                setNamaError('');
                return true;
            };

            const validateUsername = (val) => {
                if (!val) {
                    setUserError('Username wajib diisi.');
                    return false;
                }
                if (val.length < 3) {
                    setUserError('Username minimal 3 karakter.');
                    return false;
                }
                setUserError('');
                return true;
            };

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

            const validatePassword = (val) => {
                if (!val) {
                    setPasswordError('Password wajib diisi.');
                    return false;
                }
                if (val.length < 6) {
                    setPasswordError('Password minimal 6 karakter.');
                    return false;
                }
                setPasswordError('');
                return true;
            };

            const handleSubmit = async (e) => {
                e.preventDefault();
                setError('');
                setSuccess('');

                const isNamaValid = validateNama(namaAdmin);
                const isUserValid = validateUsername(username);
                const isEmailValid = validateEmail(email);
                const isPasswordValid = validatePassword(password);

                if (!isNamaValid || !isUserValid || !isEmailValid || !isPasswordValid) return;

                setLoading(true);
                try {
                    const response = await fetch('/admin/register', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            nama_admin: namaAdmin,
                            username,
                            email,
                            password,
                            no_hp: noHp
                        })
                    });

                    const data = await response.json();
                    setLoading(false);

                    if (data.success) {
                        setSuccess(data.message);
                        setTimeout(() => {
                            window.location.href = data.redirect;
                        }, 2000);
                    } else {
                        setError(data.message || 'Registrasi gagal.');
                    }
                } catch (err) {
                    setLoading(false);
                    setError('Gagal menghubungkan ke server. Silakan coba kembali.');
                }
            };

            return (
                <div className="min-h-screen bg-gradient-to-tr from-slate-950 via-slate-900 to-blue-950 flex items-center justify-center p-6 relative overflow-hidden">
                    <div className="absolute top-0 right-0 w-[40vw] h-[40vw] bg-blue-500/10 rounded-full blur-[100px] pointer-events-none -z-10"></div>
                    <div className="absolute bottom-0 left-0 w-[35vw] h-[35vw] bg-sky-500/10 rounded-full blur-[100px] pointer-events-none -z-10"></div>

                    <div className="w-full max-w-lg bg-slate-900/50 backdrop-blur-xl border border-white/10 p-8 md:p-10 rounded-[2.5rem] shadow-2xl space-y-6 relative animate-fadeIn">
                        <div className="text-center space-y-3">
                            <div className="w-14 h-14 bg-gradient-to-tr from-blue-600 to-sky-400 rounded-2xl flex items-center justify-center mx-auto shadow-lg shadow-blue-500/20">
                                <svg xmlns="http://www.w3.org/2000/svg" className="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2.5">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                </svg>
                            </div>
                            <h2 className="text-2xl font-black text-white tracking-tight">Daftar Akun Admin</h2>
                            <p className="text-xs text-slate-400 font-light">Lengkapi formulir di bawah untuk mendaftarkan akun baru</p>
                        </div>

                        {error && (
                            <div className="p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs font-semibold flex items-center gap-3 animate-fadeIn">
                                <span className="w-5 h-5 rounded-full bg-rose-500 text-white flex items-center justify-center shrink-0 text-[10px] font-bold">!</span>
                                <span>{error}</span>
                            </div>
                        )}

                        {success && (
                            <div className="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-semibold flex items-center gap-3 animate-fadeIn">
                                <span className="w-5 h-5 rounded-full bg-emerald-500 text-white flex items-center justify-center shrink-0 text-xs">✓</span>
                                <span>{success}</span>
                            </div>
                        )}

                        <form onSubmit={handleSubmit} className="space-y-4">
                            <div className="space-y-2">
                                <label className="block text-xs font-bold text-slate-400 uppercase tracking-widest">Nama Lengkap</label>
                                <input 
                                    type="text" 
                                    value={namaAdmin}
                                    onChange={(e) => {
                                        setNamaAdmin(e.target.value);
                                        if(namaError) validateNama(e.target.value);
                                    }}
                                    onBlur={(e) => validateNama(e.target.value)}
                                    required
                                    placeholder="Zaki Zulfikar" 
                                    className={`w-full px-4 py-3 bg-slate-950/40 border ${namaError ? 'border-rose-500/50' : 'border-white/10 focus:border-blue-500'} rounded-xl text-sm focus:outline-none text-white transition shadow-inner`}
                                />
                                {namaError && <span className="text-[10px] font-semibold text-rose-400 block">{namaError}</span>}
                            </div>

                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div className="space-y-2">
                                    <label className="block text-xs font-bold text-slate-400 uppercase tracking-widest">Username</label>
                                    <input 
                                        type="text" 
                                        value={username}
                                        onChange={(e) => {
                                            setUsername(e.target.value);
                                            if(userError) validateUsername(e.target.value);
                                        }}
                                        onBlur={(e) => validateUsername(e.target.value)}
                                        required
                                        placeholder="zaki_admin" 
                                        className={`w-full px-4 py-3 bg-slate-950/40 border ${userError ? 'border-rose-500/50' : 'border-white/10 focus:border-blue-500'} rounded-xl text-sm focus:outline-none text-white transition shadow-inner`}
                                    />
                                    {userError && <span className="text-[10px] font-semibold text-rose-400 block">{userError}</span>}
                                </div>

                                <div className="space-y-2">
                                    <label className="block text-xs font-bold text-slate-400 uppercase tracking-widest">Nomor HP</label>
                                    <input 
                                        type="text" 
                                        value={noHp}
                                        onChange={(e) => setNoHp(e.target.value)}
                                        placeholder="08123456789" 
                                        className="w-full px-4 py-3 bg-slate-950/40 border border-white/10 focus:border-blue-500 rounded-xl text-sm focus:outline-none text-white transition shadow-inner"
                                    />
                                </div>
                            </div>

                            <div className="space-y-2">
                                <label className="block text-xs font-bold text-slate-400 uppercase tracking-widest">Alamat Email</label>
                                <input 
                                    type="email" 
                                    value={email}
                                    onChange={(e) => {
                                        setEmail(e.target.value);
                                        if(emailError) validateEmail(e.target.value);
                                    }}
                                    onBlur={(e) => validateEmail(e.target.value)}
                                    required
                                    placeholder="zaki@rinduwater.com" 
                                    className={`w-full px-4 py-3 bg-slate-950/40 border ${emailError ? 'border-rose-500/50' : 'border-white/10 focus:border-blue-500'} rounded-xl text-sm focus:outline-none text-white transition shadow-inner`}
                                />
                                {emailError && <span className="text-[10px] font-semibold text-rose-400 block">{emailError}</span>}
                            </div>

                            <div className="space-y-2">
                                <label className="block text-xs font-bold text-slate-400 uppercase tracking-widest">Kata Sandi</label>
                                <div className="relative">
                                    <input 
                                        type={showPassword ? 'text' : 'password'} 
                                        value={password}
                                        onChange={(e) => {
                                            setPassword(e.target.value);
                                            if(passwordError) validatePassword(e.target.value);
                                        }}
                                        onBlur={(e) => validatePassword(e.target.value)}
                                        required
                                        placeholder="••••••••" 
                                        className={`w-full pl-4 pr-12 py-3 bg-slate-950/40 border ${passwordError ? 'border-rose-500/50' : 'border-white/10 focus:border-blue-500'} rounded-xl text-sm focus:outline-none text-white transition shadow-inner`}
                                    />
                                    <button 
                                        type="button" 
                                        onClick={() => setShowPassword(!showPassword)}
                                        className="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-white transition"
                                    >
                                        {showPassword ? (
                                            <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                            </svg>
                                        ) : (
                                            <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        )}
                                    </button>
                                </div>
                                {passwordError && <span className="text-[10px] font-semibold text-rose-400 block">{passwordError}</span>}
                            </div>

                            <button 
                                type="submit" 
                                disabled={loading}
                                className="w-full mt-2 py-3.5 bg-gradient-to-r from-blue-600 to-sky-500 hover:from-blue-700 hover:to-sky-600 text-white rounded-xl text-xs font-bold tracking-widest uppercase shadow-lg shadow-blue-500/10 hover:shadow-blue-500/20 active:scale-[0.99] hover:scale-[1.01] transition-all flex items-center justify-center gap-3"
                            >
                                {loading ? (
                                    <svg className="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                                        <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                ) : null}
                                <span>{loading ? 'Mendaftarkan...' : 'Daftar Sekarang'}</span>
                            </button>
                        </form>

                        <div className="text-center pt-2">
                            <p className="text-xs text-slate-400 font-light">
                                Sudah memiliki akun?{' '}
                                <a href="/admin/login" className="font-bold text-blue-400 hover:text-blue-300 transition pl-1">
                                    Masuk Portal
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            );
        }

        const root = ReactDOM.createRoot(document.getElementById('register-root'));
        root.render(<RegisterFallback />);
    </script>
    @endif
</body>
</html>
