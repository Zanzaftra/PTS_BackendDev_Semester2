import React, { useState } from 'react';

export default function Login() {
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [showPassword, setShowPassword] = useState(false);
    const [error, setError] = useState('');
    const [loading, setLoading] = useState(false);
    
    // Client-side validation states
    const [emailError, setEmailError] = useState('');
    const [passwordError, setPasswordError] = useState('');

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
        
        const isEmailValid = validateEmail(email);
        const isPasswordValid = validatePassword(password);

        if (!isEmailValid || !isPasswordValid) return;

        setLoading(true);
        try {
            const response = await fetch('/admin/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ email, password })
            });

            const data = await response.json();
            setLoading(false);

            if (data.success) {
                // Successful login! Smooth redirect
                window.location.href = data.redirect;
            } else {
                setError(data.message || 'Email atau password salah.');
            }
        } catch (err) {
            setLoading(false);
            setError('Gagal menghubungkan ke server. Silakan coba kembali.');
        }
    };

    return (
        <div className="min-h-screen bg-gradient-to-tr from-slate-950 via-slate-900 to-brand-950 flex items-center justify-center p-6 relative overflow-hidden">
            {/* Ambient background water blobs */}
            <div className="absolute top-0 right-0 w-[40vw] h-[40vw] bg-blue-500/10 rounded-full blur-[100px] pointer-events-none -z-10"></div>
            <div className="absolute bottom-0 left-0 w-[35vw] h-[35vw] bg-brand-500/10 rounded-full blur-[100px] pointer-events-none -z-10"></div>

            <div className="w-full max-w-md bg-slate-900/50 backdrop-blur-xl border border-white/10 p-8 md:p-10 rounded-[2.5rem] shadow-2xl space-y-8 relative">
                
                {/* Logo and title */}
                <div className="text-center space-y-3">
                    <div className="w-14 h-14 bg-gradient-to-tr from-blue-600 to-sky-400 rounded-2xl flex items-center justify-center mx-auto shadow-lg shadow-blue-500/20">
                        <svg xmlns="http://www.w3.org/2000/svg" className="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                        </svg>
                    </div>
                    <h2 className="text-2xl font-black text-white tracking-tight">Login Portal Admin</h2>
                    <p className="text-xs text-slate-400 font-light">Masukkan kredensial Anda untuk mengakses Dashboard Rindu Water</p>
                </div>

                {/* Main Error Banner */}
                {error && (
                    <div className="p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs font-semibold flex items-center gap-3 animate-fadeIn">
                        <span className="w-5 h-5 rounded-full bg-rose-500 text-white flex items-center justify-center shrink-0 text-[10px] font-bold">!</span>
                        <span>{error}</span>
                    </div>
                )}

                <form onSubmit={handleSubmit} className="space-y-6">
                    {/* Email Input */}
                    <div className="space-y-2">
                        <label className="block text-xs font-bold text-slate-400 uppercase tracking-widest">Alamat Email</label>
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
                                placeholder="nama@rinduwater.com" 
                                className={`w-full px-4 py-3.5 bg-slate-950/40 border ${emailError ? 'border-rose-500/50' : 'border-white/10 focus:border-blue-500'} rounded-xl text-sm focus:outline-none text-white transition shadow-inner`}
                            />
                        </div>
                        {emailError && (
                            <span className="text-[10px] font-semibold text-rose-400 block">{emailError}</span>
                        )}
                    </div>

                    {/* Password Input */}
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
                                className={`w-full pl-4 pr-12 py-3.5 bg-slate-950/40 border ${passwordError ? 'border-rose-500/50' : 'border-white/10 focus:border-blue-500'} rounded-xl text-sm focus:outline-none text-white transition shadow-inner`}
                            />
                            {/* Toggle Show/Hide Password Eye Icon */}
                            <button 
                                type="button" 
                                onClick={() => setShowPassword(!showPassword)}
                                className="absolute right-4 top-1/2 -translate-y-1/2 text-slate-450 hover:text-white transition"
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
                        {passwordError && (
                            <span className="text-[10px] font-semibold text-rose-400 block">{passwordError}</span>
                        )}
                    </div>

                    {/* Submit Button */}
                    <button 
                        type="submit" 
                        disabled={loading}
                        className="w-full py-4 bg-gradient-to-r from-blue-600 to-sky-500 hover:from-blue-700 hover:to-sky-600 text-white rounded-xl text-xs font-bold tracking-widest uppercase shadow-lg shadow-blue-500/10 hover:shadow-blue-500/20 active:scale-[0.99] hover:scale-[1.01] transition-all flex items-center justify-center gap-3"
                    >
                        {loading ? (
                            <svg className="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                                <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        ) : null}
                        <span>{loading ? 'Menghubungkan...' : 'Masuk Dashboard'}</span>
                    </button>
                </form>

                {/* Registration Link */}
                <div className="text-center">
                    <p className="text-xs text-slate-400 font-light">
                        Belum memiliki akun?{' '}
                        <a href="/admin/register" className="font-bold text-blue-400 hover:text-blue-300 transition pl-1">
                            Daftar Portal
                        </a>
                    </p>
                </div>

                {/* Helpful Hint Box */}
                <div className="p-4 bg-white/5 rounded-xl border border-white/5 text-[11px] text-slate-400 font-light leading-relaxed">
                    <span className="font-semibold text-slate-200 block mb-1">💡 Informasi Login</span>
                    Gunakan akun admin yang Anda daftarkan. Jika belum memiliki akun, silakan daftar terlebih dahulu melalui halaman daftar.
                </div>
            </div>
        </div>
    );
}
