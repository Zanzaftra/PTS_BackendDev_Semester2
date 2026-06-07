<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kangen Water - Kemurnian Hidrasi & Keseimbangan Optimal</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Tailwind Play CDN as Fallback + Configuration -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        heading: ['Outfit', 'sans-serif'],
                    },
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
    
    <!-- AlpineJS for micro-interactions -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            transition: background-color 0.5s ease, color 0.3s ease;
        }
        h1, h2, h3, h4, .font-heading {
            font-family: 'Outfit', sans-serif;
        }
        .glass-panel {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.04);
        }
        .dark .glass-panel {
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.2);
        }
        .ph-glow {
            filter: drop-shadow(0 0 25px currentColor);
        }
        
        /* Floating Bubbles CSS */
        @keyframes float-bubble {
            0% { transform: translateY(105vh) translateX(0) scale(0.5); opacity: 0; }
            10% { opacity: 0.4; }
            90% { opacity: 0.4; }
            100% { transform: translateY(-10vh) translateX(100px) scale(1.3); opacity: 0; }
        }
        .bubble-particle {
            position: absolute;
            background: linear-gradient(180deg, rgba(14, 165, 233, 0.15) 0%, rgba(37, 99, 235, 0.03) 100%);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            pointer-events: none;
            z-index: 0;
        }
    </style>
</head>
<body class="min-h-screen relative overflow-x-hidden transition-colors duration-500 bg-sky-50 text-slate-800 dark:bg-slate-950 dark:text-slate-100" 
      x-data="{ 
          checkoutProduct: '', 
          checkoutPrice: 0,
          checkoutQuantity: 1,
          checkoutType: 'one-off',
          checkoutPeriod: 'harian',
          selectedProductCapacity: '',
          successModal: false,
          orderResult: {},
          trackInvoice: '',
          trackingData: null,
          showAdminPanel: false,
          
          /* Checkout Wizard State */
          wizardStep: 1,
          
          /* pH Health Simulator State */
          phVal: '9.0',
          
          /* Testimonials State */
          activeTestimonial: 0,
          testimonials: [
              { name: 'Dr. Hendra Wijaya', role: 'Praktisi Kesehatan & Gizi', text: 'Kangen Water sangat direkomendasikan untuk menyeimbangkan pH tubuh yang seringkali terlalu asam akibat pola makan modern. Hidrasi mikronya mempercepat detoksifikasi.', rating: 5, avatar: 'https://images.unsplash.com/photo-1622253692010-333f2da6031d?auto=format&fit=crop&q=80&w=120' },
              { name: 'Santi Rahayu', role: 'Ibu Rumah Tangga (Pelanggan Bulanan)', text: 'Sejak langganan Kangen Water Bulanan, energi seluruh keluarga meningkat. Rasa airnya sangat ringan, segar, dan anak-anak jadi lebih rajin minum air putih.', rating: 5, avatar: 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=120' },
              { name: 'PT Angkasa Makmur', role: 'Institutional Hydration Partner', text: 'Layanan langganan mingguan sangat praktis dan tepat waktu. Staf kami merasa segar bugar sepanjang hari, dan pengiriman dispenser rutin sangat membantu manajemen kantor.', rating: 5, avatar: 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=crop&q=80&w=120' }
          ],

          initCheckout(id, name, price, capacity) {
              this.checkoutProduct = id;
              this.checkoutPrice = price;
              this.selectedProductCapacity = capacity;
              this.wizardStep = 2; // Jump straight to Product Details wizard step
              document.getElementById('order-wizard-section').scrollIntoView({ behavior: 'smooth' });
          },

          calculateTotal() {
              return this.checkoutPrice * this.checkoutQuantity;
          },

          formatRupiah(number) {
              return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(number);
          },
          
          toggleTheme() {
              this.darkMode = !this.darkMode;
              localStorage.setItem('darkMode', this.darkMode);
          }
      }">

    <!-- Floating Background Water Bubbles Particles -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none -z-10 select-none">
        <div class="bubble-particle" style="width: 80px; height: 80px; left: 10%; animation: float-bubble 15s linear infinite; animation-delay: 0s;"></div>
        <div class="bubble-particle" style="width: 50px; height: 50px; left: 25%; animation: float-bubble 12s linear infinite; animation-delay: 2s;"></div>
        <div class="bubble-particle" style="width: 120px; height: 120px; left: 40%; animation: float-bubble 20s linear infinite; animation-delay: 5s;"></div>
        <div class="bubble-particle" style="width: 70px; height: 70px; left: 60%; animation: float-bubble 14s linear infinite; animation-delay: 1s;"></div>
        <div class="bubble-particle" style="width: 90px; height: 90px; left: 75%; animation: float-bubble 18s linear infinite; animation-delay: 7s;"></div>
        <div class="bubble-particle" style="width: 40px; height: 40px; left: 90%; animation: float-bubble 10s linear infinite; animation-delay: 3s;"></div>
    </div>

    <!-- Navigation Header -->
    <nav class="sticky top-0 z-40 glass-panel shadow-sm border-b transition-colors duration-500">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
            <a href="#" class="flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-blue-600 to-sky-400 flex items-center justify-center shadow-lg shadow-blue-500/20 group-hover:scale-105 transition-transform">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg>
                </div>
                <span class="text-xl font-black tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-sky-500 dark:from-blue-400 dark:to-sky-300">Kangen Water</span>
            </a>
            
            <div class="hidden md:flex items-center gap-8 font-semibold text-slate-600 dark:text-slate-300 text-sm tracking-wide">
                <a href="#benefits" class="hover:text-blue-600 dark:hover:text-blue-400 transition">Keunggulan</a>
                <a href="#simulator" class="hover:text-blue-600 dark:hover:text-blue-400 transition">Lab pH</a>
                <a href="#products" class="hover:text-blue-600 dark:hover:text-blue-400 transition">Produk</a>
                <a href="#subscriptions" class="hover:text-blue-600 dark:hover:text-blue-400 transition">Langganan</a>
                <a href="#tracking" class="hover:text-blue-600 dark:hover:text-blue-400 transition">Pelacakan</a>
            </div>

            <div class="flex items-center gap-4">
                <!-- Theme Toggle Button -->
                <button @click="toggleTheme()" 
                        class="p-2 rounded-xl bg-white/60 border border-slate-200 dark:bg-slate-800 dark:border-slate-700 text-slate-600 dark:text-slate-300 shadow-sm hover:scale-105 hover:bg-white dark:hover:bg-slate-700 transition" 
                        title="Ubah Tema">
                    <svg x-show="!darkMode" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                    <svg x-show="darkMode" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m12.728 12.728l.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
                    </svg>
                </button>
                <!-- Login Admin Portal Button -->
                <a href="/admin/login" class="px-4 py-2.5 rounded-xl text-xs font-extrabold tracking-wider uppercase border-2 border-blue-500/30 text-blue-600 dark:text-blue-400 hover:bg-blue-600 hover:text-white hover:border-blue-600 dark:hover:bg-blue-500 dark:hover:border-blue-500 dark:hover:text-white shadow-sm transition-all duration-300 flex items-center gap-2" title="Masuk ke Portal Admin">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Login Admin
                </a>

                <!-- Portal Pelanggan Buttons -->
                <a href="/customer/login" class="px-4 py-2.5 rounded-xl text-xs font-extrabold tracking-wider uppercase border-2 border-sky-500/30 text-sky-600 dark:text-sky-400 hover:bg-sky-600 hover:text-white hover:border-sky-600 dark:hover:bg-sky-500 dark:hover:border-sky-500 dark:hover:text-white shadow-sm transition-all duration-300 flex items-center gap-2" title="Masuk ke Portal Pelanggan">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                    Login Pelanggan
                </a>

                <a href="/customer/register" class="px-4 py-2.5 rounded-xl text-xs font-extrabold tracking-wider uppercase border border-sky-500 bg-sky-500 text-white hover:bg-sky-600 hover:border-sky-600 shadow-sm transition-all duration-300 flex items-center gap-2" title="Daftar Akun Pelanggan">
                    Daftar
                </a>

                <a href="#order-wizard-section" class="px-5 py-2.5 bg-gradient-to-r from-blue-600 to-sky-500 hover:from-blue-700 hover:to-sky-600 text-white rounded-xl text-xs font-extrabold tracking-wider uppercase shadow-md shadow-blue-500/10 hover:shadow-blue-500/30 transition-all duration-300">Pesan Sekarang</a>
            </div>
        </div>
    </nav>

    <!-- Promo Bar Banner -->
    <div class="bg-gradient-to-r from-blue-600 via-sky-500 to-teal-500 py-2.5 px-6 text-center text-white text-xs font-bold tracking-wider relative overflow-hidden flex items-center justify-center gap-2">
        <span class="inline-block bg-white/25 px-2 py-0.5 rounded text-[10px]">PROMO</span>
        <span>Diskon 15% Untuk Paket Langganan Mingguan Selama Bulan Ini!</span>
    </div>

    <!-- Hero Section -->
    <header class="max-w-7xl mx-auto px-6 pt-12 pb-24 grid grid-cols-1 lg:grid-cols-12 gap-16 items-center relative">
        <div class="lg:col-span-7 flex flex-col items-start text-left space-y-6">
            <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-blue-50 border border-blue-100 text-blue-600 dark:bg-blue-950/40 dark:border-blue-900/50 dark:text-blue-400 font-extrabold text-[10px] tracking-widest uppercase">
                <span class="w-2.5 h-2.5 rounded-full bg-blue-600 dark:bg-blue-400 animate-pulse"></span>
                Kemurnian Alami Tanpa Kompromi
            </span>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-black tracking-tight leading-[1.1] text-slate-900 dark:text-white">
                Kemurnian Hidrasi,<br>
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-teal-500 dark:from-blue-400 dark:to-teal-400">Seimbangkan Tubuh</span><br>
                Secara Optimal
            </h1>
            <p class="text-lg text-slate-600 dark:text-slate-350 max-w-xl leading-relaxed font-light">
                Kangen Water memadukan air berkualitas tinggi dengan proses ionisasi canggih menghasilkan air minum alkali tinggi kaya antioksidan dan hidrogen aktif yang mempercepat hidrasi tubuh Anda.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 w-full sm:w-auto pt-4">
                <a href="#products" class="px-8 py-4 bg-gradient-to-r from-blue-600 to-sky-500 hover:from-blue-700 hover:to-sky-600 text-white text-base font-bold rounded-2xl shadow-xl shadow-blue-500/20 hover:shadow-blue-500/30 transition-all text-center">Jelajahi Produk</a>
                <a href="https://wa.me/6281234567890" target="_blank" class="px-8 py-4 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 rounded-2xl text-base font-bold shadow-md hover:shadow-lg transition-all text-center flex items-center justify-center gap-2">
                    <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.514 2.266 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.502-5.724-1.457L0 24zm6.59-4.846c1.6.95 3.188 1.449 4.825 1.451 5.436 0 9.86-4.37 9.864-9.799.002-2.63-1.023-5.101-2.885-6.963-1.864-1.863-4.341-2.887-6.974-2.888-5.437 0-9.863 4.37-9.868 9.8-.001 1.802.488 3.568 1.417 5.116l-.955 3.49 3.58-.939z"></path>
                    </svg>
                    WhatsApp Chat
                </a>
            </div>
            
            <!-- Quick Features -->
            <div class="grid grid-cols-3 gap-8 pt-8 w-full border-t border-slate-200 dark:border-slate-800">
                <div>
                    <span class="block text-2xl lg:text-3xl font-extrabold text-blue-600 dark:text-blue-400">8.5 - 9.5</span>
                    <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400 tracking-widest uppercase">pH Alkaline</span>
                </div>
                <div>
                    <span class="block text-2xl lg:text-3xl font-extrabold text-teal-500 dark:text-teal-400">100%</span>
                    <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400 tracking-widest uppercase">Bebas Bahan Kimia</span>
                </div>
                <div>
                    <span class="block text-2xl lg:text-3xl font-extrabold text-sky-500 dark:text-sky-400">Micro</span>
                    <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400 tracking-widest uppercase">Hydration Cell</span>
                </div>
            </div>
        </div>

        <div class="lg:col-span-5 relative flex justify-center">
            <div class="absolute inset-0 bg-white/40 dark:bg-slate-800/40 rounded-[3rem] border border-white/50 dark:border-slate-700/50 -rotate-3 -z-10 shadow-2xl"></div>
            <div class="relative w-full max-w-[420px] aspect-[4/5] rounded-[3.2rem] overflow-hidden shadow-2xl border border-white/80 dark:border-slate-700/80 transition-transform duration-500 hover:scale-[1.02]">
                <img src="{{ asset('images/hero_water.png') }}" alt="Kangen Water Premium Bottle" class="w-full h-full object-cover">
                <!-- Floating Glassmorphic Tag -->
                <div class="absolute bottom-6 left-6 right-6 glass-panel p-4 rounded-2xl flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-500 flex items-center justify-center text-white shrink-0 shadow-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div>
                        <span class="block text-[10px] font-extrabold text-slate-400 dark:text-slate-400 uppercase tracking-widest">Kombinasi Terbaik</span>
                        <span class="block text-xs font-black text-slate-800 dark:text-white">Hidrogen Aktif & Antioksidan</span>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Key Benefits Section -->
    <section id="benefits" class="max-w-7xl mx-auto px-6 py-24 border-t border-slate-200 dark:border-slate-900">
        <div class="text-center max-w-2xl mx-auto mb-16 space-y-4">
            <span class="text-blue-600 dark:text-blue-400 font-bold text-xs tracking-widest uppercase">Mengapa Kangen Water?</span>
            <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 dark:text-white">Standar Hidrasi Menyeluruh</h2>
            <p class="text-slate-600 dark:text-slate-350 font-light">Proses pemurnian dan ionisasi canggih kami menghadirkan manfaat kesehatan melampaui air mineral biasa.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Benefit 1 -->
            <div class="glass-panel p-8 rounded-3xl shadow-lg hover:-translate-y-2 transition-all duration-300">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center mb-6 shadow-inner">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-3">pH Seimbang (Alkaline)</h3>
                <p class="text-sm text-slate-600 dark:text-slate-350 leading-relaxed font-light">Dengan pH alkaline 8.5 - 9.5 membantu menetralisir kelebihan asam tubuh akibat stres maupun makanan kurang sehat.</p>
            </div>

            <!-- Benefit 2 -->
            <div class="glass-panel p-8 rounded-3xl shadow-lg hover:-translate-y-2 transition-all duration-300">
                <div class="w-12 h-12 rounded-2xl bg-teal-50 dark:bg-teal-900/30 text-teal-600 dark:text-teal-400 flex items-center justify-center mb-6 shadow-inner">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-3">Antioksidan Sangat Tinggi</h3>
                <p class="text-sm text-slate-600 dark:text-slate-350 leading-relaxed font-light">ORP negatif yang tinggi menangkal molekul radikal bebas secara aktif, melindungi sel-sel tubuh dan menjaga vitalitas.</p>
            </div>

            <!-- Benefit 3 -->
            <div class="glass-panel p-8 rounded-3xl shadow-lg hover:-translate-y-2 transition-all duration-300">
                <div class="w-12 h-12 rounded-2xl bg-sky-50 dark:bg-sky-900/30 text-sky-600 dark:text-sky-400 flex items-center justify-center mb-6 shadow-inner">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 8H18.5" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-3">Struktur Klaster Mikro</h3>
                <p class="text-sm text-slate-600 dark:text-slate-350 leading-relaxed font-light">Gugus molekul air berukuran jauh lebih kecil dibanding air biasa, mempermudah dan mempercepat penyerapan cairan ke sel.</p>
            </div>

            <!-- Benefit 4 -->
            <div class="glass-panel p-8 rounded-3xl shadow-lg hover:-translate-y-2 transition-all duration-300">
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 flex items-center justify-center mb-6 shadow-inner">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-3">Higienis & Bebas BPA</h3>
                <p class="text-sm text-slate-600 dark:text-slate-350 leading-relaxed font-light">Diproduksi dengan protokol sterilisasi medis dan dikemas di wadah bersertifikat ramah lingkungan bebas zat racun.</p>
            </div>
        </div>
    </section>

    <!-- NEW FEATURE: Interactive Alkaline pH Simulator Section -->
    <section id="simulator" class="max-w-7xl mx-auto px-6 py-20 border-t border-slate-200 dark:border-slate-900">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 items-center">
            <div class="lg:col-span-5 space-y-6">
                <span class="text-blue-600 dark:text-blue-400 font-bold text-xs tracking-widest uppercase">Eksplorasi Tingkat Hidrasi</span>
                <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 dark:text-white">Alkaline pH Health Simulator</h2>
                <p class="text-slate-600 dark:text-slate-350 font-light leading-relaxed">
                    Setiap tingkatan pH air alkali memiliki peranan dan khasiat hidrasi unik yang disesuaikan untuk proses pemulihan serta gaya hidup harian Anda. Geser tingkat pH di laboratorium virtual kami di samping untuk memahami manfaatnya!
                </p>
                <div class="p-6 bg-blue-50/50 dark:bg-slate-900/50 rounded-2xl border border-blue-100/50 dark:border-slate-850/50">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest block mb-2">Mengapa pH air penting?</span>
                    <span class="text-sm font-light text-slate-600 dark:text-slate-300">Skala pH (Potential of Hydrogen) menentukan keasaman air. Sel tubuh kita beroperasi optimal pada tingkat alkalinitas tertentu untuk menyerap mineral esensial.</span>
                </div>
            </div>
            
            <div class="lg:col-span-7">
                <div class="glass-panel p-8 md:p-10 rounded-[2.5rem] shadow-2xl border relative overflow-hidden transition-all duration-300">
                    <!-- Top Lab Header -->
                    <div class="flex items-center justify-between pb-6 border-b border-slate-200/60 dark:border-slate-800/60 mb-8">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-blue-600 animate-ping"></span>
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Virtual Lab Kangen Ionizer</span>
                        </div>
                        <span class="text-xs font-semibold text-blue-600 dark:text-blue-400" x-text="'Nilai Aktif: pH ' + phVal"></span>
                    </div>

                    <!-- Interactive pH Value Display -->
                    <div class="text-center py-6">
                        <div class="inline-block transition-all duration-500 duration-300 p-8 rounded-full mb-6 font-heading font-black text-6xl text-white shadow-xl ph-glow" 
                             :style="phVal === '8.5' ? 'color: #818cf8; background: linear-gradient(135deg, #a5b4fc 0%, #6366f1 100%)' : (phVal === '9.0' ? 'color: #3b82f6; background: linear-gradient(135deg, #60a5fa 0%, #2563eb 100%)' : 'color: #a855f7; background: linear-gradient(135deg, #c084fc 0%, #7e22ce 100%)')"
                             x-text="phVal">
                        </div>
                    </div>

                    <!-- Slider Control -->
                    <div class="space-y-4 max-w-md mx-auto mb-10">
                        <div class="flex justify-between text-xs font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-widest px-2">
                            <span>pH 8.5</span>
                            <span>pH 9.0</span>
                            <span>pH 9.5</span>
                        </div>
                        <input type="range" min="8.5" max="9.5" step="0.5" x-model="phVal" 
                               class="w-full h-2 bg-slate-200 dark:bg-slate-800 rounded-lg appearance-none cursor-pointer accent-blue-600 dark:accent-blue-500 focus:outline-none">
                    </div>

                    <!-- Dynamic Simulator Information Content Display -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-6 border-t border-slate-200/60 dark:border-slate-800/60">
                        <!-- Left Column: Medical & Health Benefits -->
                        <div class="space-y-3">
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest">Manfaat Utama Tubuh</h4>
                            <div class="space-y-3">
                                <template x-if="phVal === '8.5'">
                                    <div class="space-y-2">
                                        <span class="block text-base font-extrabold text-slate-800 dark:text-white">Tahap Awal & Adaptasi</span>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 font-light leading-relaxed">
                                            Sangat direkomendasikan untuk minggu pertama konsumsi air alkali. Membantu tubuh Anda menyesuaikan diri secara bertahap terhadap peningkatan pH tanpa menimbulkan reaksi kejut.
                                        </p>
                                    </div>
                                </template>
                                <template x-if="phVal === '9.0'">
                                    <div class="space-y-2">
                                        <span class="block text-base font-extrabold text-slate-800 dark:text-white">Harian & Metabolisme Seimbang</span>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 font-light leading-relaxed">
                                            Tingkat ideal untuk konsumsi rutin harian. Mengoptimalkan metabolisme organ, menetralkan tumpukan asam laktat setelah berolahraga, dan menjaga energi konstan sepanjang hari.
                                        </p>
                                    </div>
                                </template>
                                <template x-if="phVal === '9.5'">
                                    <div class="space-y-2">
                                        <span class="block text-base font-extrabold text-slate-800 dark:text-white">Detoksifikasi & Terapi Maksimal</span>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 font-light leading-relaxed">
                                            Kaya akan kandungan hidrogen aktif super melimpah. Membantu pemulihan kelelahan fisik ekstrem secara cepat dan membuang akumulasi logam berat serta zat radikal bebas berbahaya.
                                        </p>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Right Column: Culinary & Domestic Recommendations -->
                        <div class="space-y-3">
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest">Rekomendasi Kuliner & Dapur</h4>
                            <div class="space-y-3">
                                <template x-if="phVal === '8.5'">
                                    <div class="space-y-2">
                                        <span class="block text-base font-extrabold text-slate-800 dark:text-white">Nasi Pulen & Teh Wangi</span>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 font-light leading-relaxed">
                                            Membuat molekul beras menyerap cairan secara merata saat dimasak, menghasilkan tekstur nasi yang empuk dan harum alami. Juga mempercepat ekstraksi daun teh herbal.
                                        </p>
                                    </div>
                                </template>
                                <template x-if="phVal === '9.0'">
                                    <div class="space-y-2">
                                        <span class="block text-base font-extrabold text-slate-800 dark:text-white">Ekstraksi Kopi & Sayur Soup</span>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 font-light leading-relaxed">
                                            Sangat baik untuk menyeduh bubuk kopi arabika guna mengeluarkan aroma buah alaminya tanpa memicu keasaman lambung. Mempercepat proses rebusan sup kaldu yang gurih.
                                        </p>
                                    </div>
                                </template>
                                <template x-if="phVal === '9.5'">
                                    <div class="space-y-2">
                                        <span class="block text-base font-extrabold text-slate-800 dark:text-white">Cuci Pestisida & Rebus Hijau</span>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 font-light leading-relaxed">
                                            Efektif melarutkan sisa bahan kimia pestisida membandel pada kulit sayur dan buah. Menjaga zat hijau daun sayuran rebusan agar tetap berkilau dan bergizi tinggi.
                                        </p>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Products Catalog Section -->
    <section id="products" class="max-w-7xl mx-auto px-6 py-20 border-t border-slate-200 dark:border-slate-900">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-6">
            <div class="space-y-4 max-w-xl">
                <span class="text-blue-600 dark:text-blue-400 font-bold text-xs tracking-widest uppercase">Kemasan Premium Sehat</span>
                <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 dark:text-white">Katalog Produk Air Kangen</h2>
                <p class="text-slate-600 dark:text-slate-350 font-light">Tersedia berbagai varian kemasan steril yang disesuaikan untuk kebutuhan personal, keluarga, maupun korporasi.</p>
            </div>
            <div>
                <span class="inline-flex gap-1.5 px-4 py-2 glass-panel rounded-2xl text-xs font-semibold text-slate-500 border">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    Stok Selalu Diperbarui Secara Real-Time
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            @if($produk->isEmpty())
                <!-- Varian 1: Galon 19L -->
                <div class="glass-panel rounded-[2rem] overflow-hidden flex flex-col justify-between group transition-transform duration-300 hover:scale-[1.02] border">
                    <div class="p-6">
                        <div class="w-full aspect-[4/3] rounded-2xl bg-gradient-to-b from-blue-50 to-sky-100/50 dark:from-slate-900 dark:to-slate-800/50 mb-6 flex items-center justify-center relative overflow-hidden">
                            <span class="absolute top-4 left-4 px-3 py-1 rounded-full bg-emerald-500 text-white font-bold text-[9px] tracking-wider uppercase shadow-sm">TERSEDIA</span>
                            <span class="absolute top-4 right-4 px-3 py-1 rounded-full bg-blue-600 text-white font-bold text-[9px] tracking-wider uppercase">GALON</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-blue-400 group-hover:scale-110 transition duration-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-extrabold text-slate-800 dark:text-white mb-1">Kangen Ultra Galon</h3>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-3">Kapasitas: 19 Liter</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-light line-clamp-2">Volume besar ekonomis, ideal menyuplai kebugaran harian keluarga serumah maupun staf kantor Anda.</p>
                    </div>
                    <div class="p-6 pt-0 border-t border-slate-100/50 dark:border-slate-800/30">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-xs font-semibold text-slate-400">Harga Satuan</span>
                            <span class="text-xl font-black text-blue-600 dark:text-blue-400">Rp 50.000</span>
                        </div>
                        <button @click="initCheckout('1', 'Kangen Ultra Galon', 50000, '19L')" class="w-full py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-xl text-xs font-bold tracking-wide shadow-md transition duration-300">Pesan Sekarang</button>
                    </div>
                </div>

                <!-- Varian 2: Botol 1500ml -->
                <div class="glass-panel rounded-[2rem] overflow-hidden flex flex-col justify-between group transition-transform duration-300 hover:scale-[1.02] border">
                    <div class="p-6">
                        <div class="w-full aspect-[4/3] rounded-2xl bg-gradient-to-b from-blue-50 to-sky-100/50 dark:from-slate-900 dark:to-slate-800/50 mb-6 flex items-center justify-center relative overflow-hidden">
                            <span class="absolute top-4 left-4 px-3 py-1 rounded-full bg-emerald-500 text-white font-bold text-[9px] tracking-wider uppercase shadow-sm">TERSEDIA</span>
                            <span class="absolute top-4 right-4 px-3 py-1 rounded-full bg-teal-600 text-white font-bold text-[9px] tracking-wider uppercase">BOTOL</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-teal-400 group-hover:scale-110 transition duration-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-extrabold text-slate-800 dark:text-white mb-1">Kangen Fresh Bottle</h3>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-3">Kapasitas: 1500 ml</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-light line-clamp-2">Ukuran besar portabel yang pas ditaruh di dalam mobil atau saat bepergian jarak jauh.</p>
                    </div>
                    <div class="p-6 pt-0 border-t border-slate-100/50 dark:border-slate-800/30">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-xs font-semibold text-slate-400">Harga Satuan</span>
                            <span class="text-xl font-black text-blue-600 dark:text-blue-400">Rp 12.000</span>
                        </div>
                        <button @click="initCheckout('2', 'Kangen Fresh Bottle', 12000, '1500ml')" class="w-full py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-xl text-xs font-bold tracking-wide shadow-md transition duration-300">Pesan Sekarang</button>
                    </div>
                </div>

                <!-- Varian 3: Botol 600ml -->
                <div class="glass-panel rounded-[2rem] overflow-hidden flex flex-col justify-between group transition-transform duration-300 hover:scale-[1.02] border">
                    <div class="p-6">
                        <div class="w-full aspect-[4/3] rounded-2xl bg-gradient-to-b from-blue-50 to-sky-100/50 dark:from-slate-900 dark:to-slate-800/50 mb-6 flex items-center justify-center relative overflow-hidden">
                            <span class="absolute top-4 left-4 px-3 py-1 rounded-full bg-emerald-500 text-white font-bold text-[9px] tracking-wider uppercase shadow-sm">TERSEDIA</span>
                            <span class="absolute top-4 right-4 px-3 py-1 rounded-full bg-indigo-600 text-white font-bold text-[9px] tracking-wider uppercase">BOTOL</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-indigo-400 group-hover:scale-110 transition duration-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m12.728 12.728l.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-extrabold text-slate-800 dark:text-white mb-1">Kangen Active Bottle</h3>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-3">Kapasitas: 600 ml</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-light line-clamp-2">Sangat pas digenggam untuk menemani rutinitas olahraga, gym, maupun bepergian ke kantor.</p>
                    </div>
                    <div class="p-6 pt-0 border-t border-slate-100/50 dark:border-slate-800/30">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-xs font-semibold text-slate-400">Harga Satuan</span>
                            <span class="text-xl font-black text-blue-600 dark:text-blue-400">Rp 6.000</span>
                        </div>
                        <button @click="initCheckout('3', 'Kangen Active Bottle', 6000, '600ml')" class="w-full py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-xl text-xs font-bold tracking-wide shadow-md transition duration-300">Pesan Sekarang</button>
                    </div>
                </div>

                <!-- Varian 4: Gelas 220ml -->
                <div class="glass-panel rounded-[2rem] overflow-hidden flex flex-col justify-between group transition-transform duration-300 hover:scale-[1.02] border">
                    <div class="p-6">
                        <div class="w-full aspect-[4/3] rounded-2xl bg-gradient-to-b from-blue-50 to-sky-100/50 dark:from-slate-900 dark:to-slate-800/50 mb-6 flex items-center justify-center relative overflow-hidden">
                            <span class="absolute top-4 left-4 px-3 py-1 rounded-full bg-emerald-500 text-white font-bold text-[9px] tracking-wider uppercase shadow-sm">TERSEDIA</span>
                            <span class="absolute top-4 right-4 px-3 py-1 rounded-full bg-sky-600 text-white font-bold text-[9px] tracking-wider uppercase">GELAS</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-sky-400 group-hover:scale-110 transition duration-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5a2 2 0 10-2 2h2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-extrabold text-slate-800 dark:text-white mb-1">Kangen Hydrate Cup</h3>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-3">Kapasitas: 220 ml</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-light line-clamp-2">Sangat higienis dan praktis disajikan bagi para tamu undangan saat rapat maupun perhelatan formal.</p>
                    </div>
                    <div class="p-6 pt-0 border-t border-slate-100/50 dark:border-slate-800/30">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-xs font-semibold text-slate-400">Harga Satuan</span>
                            <span class="text-xl font-black text-blue-600 dark:text-blue-400">Rp 2.000</span>
                        </div>
                        <button @click="initCheckout('4', 'Kangen Hydrate Cup', 2000, '220ml')" class="w-full py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-xl text-xs font-bold tracking-wide shadow-md transition duration-300">Pesan Sekarang</button>
                    </div>
                </div>
            @else
                <!-- Load Dynamic Products from database table produk_air -->
                @foreach($produk as $item)
                    <div class="glass-panel rounded-[2rem] overflow-hidden flex flex-col justify-between group transition-transform duration-300 hover:scale-[1.02] border">
                        <div class="p-6">
                            <div class="w-full aspect-[4/3] rounded-2xl bg-gradient-to-b from-blue-50 to-sky-100/50 dark:from-slate-900 dark:to-slate-800/50 mb-6 flex items-center justify-center relative overflow-hidden">
                                @if($item->stok > 0 && $item->status_produk === 'tersedia')
                                    <span class="absolute top-4 left-4 px-3 py-1 rounded-full bg-emerald-500 text-white font-bold text-[9px] tracking-wider uppercase shadow-sm">TERSEDIA</span>
                                @else
                                    <span class="absolute top-4 left-4 px-3 py-1 rounded-full bg-rose-500 text-white font-bold text-[9px] tracking-wider uppercase shadow-sm">HABIS</span>
                                @endif
                                <span class="absolute top-4 right-4 px-3 py-1 rounded-full bg-blue-600 text-white font-bold text-[9px] tracking-wider uppercase">{{ strtoupper($item->jenis_kemasan) }}</span>
                                
                                @if($item->foto_produk)
                                    <img src="{{ asset('storage/' . $item->foto_produk) }}" alt="{{ $item->nama_produk }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-blue-400 group-hover:scale-110 transition duration-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                    </svg>
                                @endif
                            </div>
                            <h3 class="text-xl font-extrabold text-slate-800 dark:text-white mb-1 leading-tight">{{ $item->nama_produk }}</h3>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-3">Kapasitas: {{ $item->kapasitas }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-light line-clamp-2">{{ $item->deskripsi ?? 'Nikmati kesegaran air alkali kaya mineral aktif penyeimbang hidrasi tubuh Anda.' }}</p>
                        </div>
                        <div class="p-6 pt-0 border-t border-slate-100/50 dark:border-slate-800/30">
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-xs font-semibold text-slate-400">Harga Satuan</span>
                                <span class="text-xl font-black text-blue-600 dark:text-blue-400">Rp {{ number_format($item->harga, 0, ',', '.') }}</span>
                            </div>
                            <button @click="initCheckout('{{ $item->id_produk }}', '{{ $item->nama_produk }}', {{ $item->harga }}, '{{ $item->kapasitas }}')" 
                                    class="w-full py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-xl text-xs font-bold tracking-wide shadow-md transition duration-300"
                                    {{ ($item->stok > 0 && $item->status_produk === 'tersedia') ? '' : 'disabled' }}>
                                {{ ($item->stok > 0 && $item->status_produk === 'tersedia') ? 'Pesan Sekarang' : 'Stok Habis' }}
                            </button>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </section>

    <!-- Subscription Plans Section -->
    <section id="subscriptions" class="max-w-7xl mx-auto px-6 py-24 border-t border-slate-200 dark:border-slate-900 bg-blue-500/5 dark:bg-slate-900/10 rounded-[3rem] my-12">
        <div class="text-center max-w-2xl mx-auto mb-20 space-y-4">
            <span class="text-teal-600 dark:text-teal-400 font-bold text-xs tracking-widest uppercase">Konsistensi Hidrasi</span>
            <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 dark:text-white">Layanan Langganan Premium</h2>
            <p class="text-slate-600 dark:text-slate-350 font-light">Kami mengantarkan kesegaran air alkali Kangen Water secara berkala langsung ke lokasi Anda dengan penawaran tarif hemat bersahabat.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-stretch">
            <!-- Tier 1: Harian -->
            <div class="glass-panel p-8 rounded-[2.5rem] flex flex-col justify-between relative overflow-hidden group border">
                <div class="space-y-6">
                    <span class="inline-block px-4 py-1.5 rounded-full bg-blue-50 dark:bg-blue-950/30 text-blue-600 dark:text-blue-400 font-bold text-[10px] uppercase tracking-widest">Harian</span>
                    <h3 class="text-2xl font-black text-slate-800 dark:text-white leading-none">Paket Daily Hydration</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed font-light">Pasokan harian terjaga segar untuk melengkapi jadwal padat Anda agar vitalitas tubuh senantiasa optimal.</p>
                    <ul class="space-y-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                        <li class="flex items-center gap-3 text-sm text-slate-600 dark:text-slate-350">
                            <span class="w-5 h-5 rounded-full bg-emerald-50 dark:bg-emerald-950/30 text-emerald-500 flex items-center justify-center shrink-0 text-xs">✓</span>
                            Pengiriman botol dingin setiap pagi
                        </li>
                        <li class="flex items-center gap-3 text-sm text-slate-600 dark:text-slate-350">
                            <span class="w-5 h-5 rounded-full bg-emerald-50 dark:bg-emerald-950/30 text-emerald-500 flex items-center justify-center shrink-0 text-xs">✓</span>
                            Bebas biaya kirim se-kota
                        </li>
                        <li class="flex items-center gap-3 text-sm text-slate-600 dark:text-slate-350">
                            <span class="w-5 h-5 rounded-full bg-emerald-50 dark:bg-emerald-950/30 text-emerald-500 flex items-center justify-center shrink-0 text-xs">✓</span>
                            Kemasan tersegel steril
                        </li>
                    </ul>
                </div>
                <div class="pt-8 space-y-4">
                    <div class="flex items-baseline gap-1">
                        <span class="text-slate-400 text-[10px] font-bold uppercase">Mulai Dari</span>
                        <span class="text-3xl font-black text-slate-900 dark:text-white">Rp 10.000</span>
                        <span class="text-slate-400 text-xs font-semibold">/ hari</span>
                    </div>
                    <a href="#order-wizard-section" @click="checkoutType = 'subscription'; checkoutPeriod = 'harian'; wizardStep = 2" class="block w-full py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-xl text-center text-xs font-bold tracking-wider uppercase transition shadow-md">Pilih Paket</a>
                </div>
            </div>

            <!-- Tier 2: Mingguan -->
            <div class="glass-panel p-8 rounded-[2.5rem] border-2 border-blue-500 dark:border-blue-400 flex flex-col justify-between relative overflow-hidden group lg:scale-105 shadow-2xl">
                <div class="absolute top-0 right-0 bg-blue-500 text-white font-extrabold text-[9px] tracking-widest uppercase px-6 py-2 rounded-bl-3xl shadow-sm">PILIHAN TERBAIK</div>
                <div class="space-y-6">
                    <span class="inline-block px-4 py-1.5 rounded-full bg-teal-50 dark:bg-teal-950/30 text-teal-600 dark:text-teal-400 font-bold text-[10px] uppercase tracking-widest">Mingguan</span>
                    <h3 class="text-2xl font-black text-slate-800 dark:text-white leading-none">Paket Family Hydration</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed font-light">Kebutuhan ideal pasokan mingguan galon untuk menopang kebugaran seluruh anggota keluarga tercinta Anda.</p>
                    <ul class="space-y-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                        <li class="flex items-center gap-3 text-sm text-slate-600 dark:text-slate-350">
                            <span class="w-5 h-5 rounded-full bg-emerald-50 dark:bg-emerald-950/30 text-emerald-500 flex items-center justify-center shrink-0 text-xs">✓</span>
                            Pengiriman terjadwal 2x seminggu
                        </li>
                        <li class="flex items-center gap-3 text-sm text-slate-600 dark:text-slate-350">
                            <span class="w-5 h-5 rounded-full bg-emerald-50 dark:bg-emerald-950/30 text-emerald-500 flex items-center justify-center shrink-0 text-xs">✓</span>
                            Jadwal antar fleksibel
                            <span class="text-[9px] font-bold text-teal-600 dark:text-teal-400 bg-teal-50 dark:bg-teal-950/40 px-2 py-0.5 rounded-full uppercase ml-1">Bebas Ganti</span>
                        </li>
                        <li class="flex items-center gap-3 text-sm text-slate-600 dark:text-slate-350">
                            <span class="w-5 h-5 rounded-full bg-emerald-50 dark:bg-emerald-950/30 text-emerald-500 flex items-center justify-center shrink-0 text-xs">✓</span>
                            Tarif diskon tambahan 15%
                        </li>
                    </ul>
                </div>
                <div class="pt-8 space-y-4">
                    <div class="flex items-baseline gap-1">
                        <span class="text-slate-400 text-[10px] font-bold uppercase">Mulai Dari</span>
                        <span class="text-3xl font-black text-blue-600 dark:text-blue-400">Rp 45.000</span>
                        <span class="text-slate-400 text-xs font-semibold">/ minggu</span>
                    </div>
                    <a href="#order-wizard-section" @click="checkoutType = 'subscription'; checkoutPeriod = 'mingguan'; wizardStep = 2" class="block w-full py-4 bg-gradient-to-r from-blue-600 to-sky-500 hover:from-blue-700 hover:to-sky-600 text-white rounded-xl text-center text-xs font-extrabold tracking-wider uppercase transition shadow-lg shadow-blue-500/10 hover:scale-[1.01]">Pilih Paket</a>
                </div>
            </div>

            <!-- Tier 3: Bulanan -->
            <div class="glass-panel p-8 rounded-[2.5rem] flex flex-col justify-between relative overflow-hidden group border">
                <div class="space-y-6">
                    <span class="inline-block px-4 py-1.5 rounded-full bg-indigo-50 dark:bg-indigo-950/30 text-indigo-600 dark:text-indigo-400 font-bold text-[10px] uppercase tracking-widest">Bulanan</span>
                    <h3 class="text-2xl font-black text-slate-800 dark:text-white leading-none">Paket Office Hydration</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed font-light">Paling direkomendasikan menyuplai hidrasi sehat skala korporat, restoran, klinik medis, maupun gedung perkantoran.</p>
                    <ul class="space-y-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                        <li class="flex items-center gap-3 text-sm text-slate-600 dark:text-slate-350">
                            <span class="w-5 h-5 rounded-full bg-emerald-50 dark:bg-emerald-950/30 text-emerald-500 flex items-center justify-center shrink-0 text-xs">✓</span>
                            Prioritas jadwal antaran pagi
                        </li>
                        <li class="flex items-center gap-3 text-sm text-slate-600 dark:text-slate-350">
                            <span class="w-5 h-5 rounded-full bg-emerald-50 dark:bg-emerald-950/30 text-emerald-500 flex items-center justify-center shrink-0 text-xs">✓</span>
                            Layanan cuci dispenser gratis berkala
                        </li>
                        <li class="flex items-center gap-3 text-sm text-slate-600 dark:text-slate-350">
                            <span class="w-5 h-5 rounded-full bg-emerald-50 dark:bg-emerald-950/30 text-emerald-500 flex items-center justify-center shrink-0 text-xs">✓</span>
                            Rincian invoice laporan bulanan rapi
                        </li>
                    </ul>
                </div>
                <div class="pt-8 space-y-4">
                    <div class="flex items-baseline gap-1">
                        <span class="text-slate-400 text-[10px] font-bold uppercase">Mulai Dari</span>
                        <span class="text-3xl font-black text-slate-900 dark:text-white">Rp 180.000</span>
                        <span class="text-slate-400 text-xs font-semibold">/ bulan</span>
                    </div>
                    <a href="#order-wizard-section" @click="checkoutType = 'subscription'; checkoutPeriod = 'bulanan'; wizardStep = 2" class="block w-full py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-xl text-center text-xs font-bold tracking-wider uppercase transition shadow-md">Pilih Paket</a>
                </div>
            </div>
        </div>
    </section>

    <!-- NEW FEATURE: Testimonials Slider Section -->
    <section class="max-w-4xl mx-auto px-6 py-20">
        <div class="text-center max-w-xl mx-auto mb-12 space-y-3">
            <span class="text-blue-600 dark:text-blue-400 font-bold text-xs tracking-widest uppercase">Ulasan Pengguna</span>
            <h2 class="text-3xl font-black text-slate-800 dark:text-white">Mengapa Mereka Setia Minum Kangen?</h2>
        </div>

        <div class="glass-panel p-8 md:p-12 rounded-[2.5rem] shadow-xl border relative overflow-hidden" 
             x-init="setInterval(() => { activeTestimonial = (activeTestimonial + 1) % testimonials.length }, 8000)">
            
            <div class="relative min-h-[160px] flex items-center justify-center">
                <!-- Loop Testimonials -->
                <template x-for="(t, idx) in testimonials" :key="idx">
                    <div x-show="activeTestimonial === idx" 
                         class="space-y-6 text-center animate-fadeIn" 
                         x-transition:enter="transition ease-out duration-500"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100">
                        
                        <!-- Stars -->
                        <div class="flex justify-center gap-1">
                            <template x-for="star in Array.from({length: t.rating})">
                                <span class="text-yellow-400 text-lg">★</span>
                            </template>
                        </div>
                        
                        <!-- Review Quote -->
                        <p class="text-lg md:text-xl font-light italic leading-relaxed text-slate-600 dark:text-slate-300" x-text="'“' + t.text + '”'"></p>
                        
                        <!-- Bio Details -->
                        <div class="flex items-center justify-center gap-4 pt-4">
                            <img :src="t.avatar" :alt="t.name" class="w-12 h-12 rounded-full object-cover border-2 border-blue-500 shadow-md">
                            <div class="text-left">
                                <span class="block text-sm font-extrabold text-slate-800 dark:text-white" x-text="t.name"></span>
                                <span class="block text-xs text-slate-400" x-text="t.role"></span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Dots Indicator Navigation -->
            <div class="flex justify-center gap-2 mt-8">
                <template x-for="(t, idx) in testimonials" :key="idx">
                    <button @click="activeTestimonial = idx" 
                            :class="activeTestimonial === idx ? 'bg-blue-600 dark:bg-blue-400 w-6' : 'bg-slate-200 dark:bg-slate-800 w-2'" 
                            class="h-2 rounded-full transition-all duration-300"></button>
                </template>
            </div>
        </div>
    </section>

    <!-- Interactive 3-Step Checkout Wizard Section -->
    <section id="order-wizard-section" class="max-w-4xl mx-auto px-6 py-20 border-t border-slate-200 dark:border-slate-900">
        <div class="glass-panel p-8 md:p-12 rounded-[2.5rem] shadow-2xl border relative overflow-hidden" 
             x-data="{ 
                customerType: 'individu',
                submitting: false,
                errorMessage: '',

                validateStep1() {
                    const nama = document.querySelector('input[name=nama_pelanggan]').value;
                    const penanggung = document.querySelector('input[name=penanggung_jawab]').value;
                    const telp = document.querySelector('input[name=no_telepon]').value;
                    const email = document.querySelector('input[name=email]').value;
                    const alamat = document.querySelector('textarea[name=alamat]').value;
                    
                    if(this.customerType === 'lembaga') {
                        const lembaga = document.querySelector('input[name=nama_lembaga]').value;
                        if(!lembaga.trim()) return false;
                    }

                    return nama.trim() && penanggung.trim() && telp.trim() && email.trim() && alamat.trim();
                },

                validateStep2() {
                    return this.checkoutProduct !== '';
                },

                submitOrder() {
                    this.submitting = true;
                    this.errorMessage = '';
                    
                    const form = document.getElementById('wizard-checkout-form');
                    const formData = new FormData(form);

                    formData.append('purchase_type', this.checkoutType);
                    if (this.checkoutType === 'subscription') {
                        formData.append('periode_pengantaran', this.checkoutPeriod);
                    }

                    fetch('/checkout', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        this.submitting = false;
                        if (data.success) {
                            this.orderResult = data;
                            this.successModal = true;
                            // Reset form & states
                            form.reset();
                            this.checkoutProduct = '';
                            this.checkoutPrice = 0;
                            this.checkoutQuantity = 1;
                            this.wizardStep = 1;
                        } else {
                            this.errorMessage = data.message || 'Terjadi gangguan. Periksa kembali entri data Anda.';
                        }
                    })
                    .catch(error => {
                        this.submitting = false;
                        this.errorMessage = 'Koneksi internet bermasalah. Pengiriman formulir gagal.';
                    });
                }
             }">
            
            <div class="text-center max-w-xl mx-auto mb-12 space-y-3">
                <span class="text-blue-600 dark:text-blue-400 font-bold text-xs tracking-widest uppercase">Proses Pemesanan Modern</span>
                <h2 class="text-3xl font-black text-slate-800 dark:text-white">Formulir Pembelian Air Sehat</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-light">Proses checkout terbagi menjadi 3 langkah mudah untuk kenyamanan ekstra Anda.</p>
            </div>

            <!-- Wizard Progress Steps Indicator -->
            <div class="relative flex justify-between items-center max-w-md mx-auto mb-12">
                <div class="absolute top-1/2 left-0 right-0 h-0.5 bg-slate-200 dark:bg-slate-800 -translate-y-1/2 -z-10"></div>
                <div class="absolute top-1/2 left-0 h-0.5 bg-blue-500 dark:bg-blue-400 -translate-y-1/2 -z-10 transition-all duration-500"
                     :style="wizardStep === 1 ? 'width: 0%' : (wizardStep === 2 ? 'width: 50%' : 'width: 100%')"></div>

                <!-- Step 1: Profil -->
                <button type="button" @click="if(wizardStep > 1) wizardStep = 1" 
                        class="w-9 h-9 rounded-full font-bold text-xs flex items-center justify-center transition-all duration-300"
                        :class="wizardStep >= 1 ? 'bg-blue-500 text-white shadow-lg shadow-blue-500/20' : 'bg-slate-200 dark:bg-slate-800 text-slate-500'">
                    1
                </button>
                <!-- Step 2: Produk -->
                <button type="button" @click="if(validateStep1() && wizardStep > 2) wizardStep = 2" 
                        class="w-9 h-9 rounded-full font-bold text-xs flex items-center justify-center transition-all duration-300"
                        :class="wizardStep >= 2 ? 'bg-blue-500 text-white shadow-lg shadow-blue-500/20' : 'bg-slate-200 dark:bg-slate-800 text-slate-500'">
                    2
                </button>
                <!-- Step 3: Bayar -->
                <button type="button" @click="if(validateStep1() && validateStep2()) wizardStep = 3" 
                        class="w-9 h-9 rounded-full font-bold text-xs flex items-center justify-center transition-all duration-300"
                        :class="wizardStep >= 3 ? 'bg-blue-500 text-white shadow-lg shadow-blue-500/20' : 'bg-slate-200 dark:bg-slate-800 text-slate-500'">
                    3
                </button>
            </div>

            <!-- Error Banner -->
            <div x-show="errorMessage" class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-100 text-rose-600 text-sm font-semibold flex items-center gap-3" x-transition>
                <span class="w-5 h-5 rounded-full bg-rose-500 text-white flex items-center justify-center text-xs shrink-0">!</span>
                <span x-text="errorMessage"></span>
            </div>

            <form id="wizard-checkout-form" @submit.prevent="submitOrder()" class="space-y-8">
                
                <!-- STEP 1: Profil Pelanggan -->
                <div x-show="wizardStep === 1" class="space-y-6 animate-fadeIn">
                    <div class="space-y-3">
                        <label class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">Kategori Pemesan</label>
                        <div class="grid grid-cols-2 gap-4 p-1.5 bg-slate-100 dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800">
                            <button type="button" @click="customerType = 'individu'" 
                                    :class="customerType === 'individu' ? 'bg-white dark:bg-slate-800 text-blue-600 dark:text-blue-400 shadow-sm' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200'"
                                    class="py-3 rounded-xl text-sm font-bold tracking-wide transition-all">
                                Individu (Pribadi)
                            </button>
                            <button type="button" @click="customerType = 'lembaga'" 
                                    :class="customerType === 'lembaga' ? 'bg-white dark:bg-slate-800 text-blue-600 dark:text-blue-400 shadow-sm' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200'"
                                    class="py-3 rounded-xl text-sm font-bold tracking-wide transition-all">
                                Lembaga (Bisnis/Kantor)
                            </button>
                        </div>
                        <input type="hidden" name="jenis_pelanggan" :value="customerType">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-350">Nama Pelanggan / Kontak Utama <span class="text-rose-500">*</span></label>
                            <input type="text" name="nama_pelanggan" required placeholder="Contoh: Zaki Zulfikar" 
                                   class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:border-blue-500 shadow-inner">
                        </div>

                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-350">Penanggung Jawab / Penerima <span class="text-rose-500">*</span></label>
                            <input type="text" name="penanggung_jawab" required placeholder="Nama penerima paket" 
                                   class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:border-blue-500 shadow-inner">
                        </div>

                        <div class="col-span-1 md:col-span-2 space-y-2" x-show="customerType === 'lembaga'" x-transition>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-350">Nama Perusahaan / Lembaga <span class="text-rose-500">*</span></label>
                            <input type="text" name="nama_lembaga" :required="customerType === 'lembaga'" placeholder="Contoh: PT Angkasa Makmur" 
                                   class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:border-blue-500 shadow-inner">
                        </div>

                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-350">Nomor Telepon / WhatsApp <span class="text-rose-500">*</span></label>
                            <input type="tel" name="no_telepon" required placeholder="Contoh: 081234567890" 
                                   class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:border-blue-500 shadow-inner">
                        </div>

                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-350">Alamat Email <span class="text-rose-500">*</span></label>
                            <input type="email" name="email" required placeholder="Contoh: zaki@example.com" 
                                   class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:border-blue-500 shadow-inner">
                        </div>

                        <div class="col-span-1 md:col-span-2 space-y-2">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-350">Alamat Pengiriman Lengkap <span class="text-rose-500">*</span></label>
                            <textarea name="alamat" required rows="3" placeholder="Tuliskan nama jalan, nomor rumah, RT/RW, kelurahan, kecamatan" 
                                      class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:border-blue-500 shadow-inner resize-none"></textarea>
                        </div>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="button" @click="if(validateStep1()) wizardStep = 2" 
                                class="px-6 py-3.5 bg-blue-500 hover:bg-blue-600 text-white rounded-xl text-xs font-extrabold tracking-wider uppercase transition shadow-md">
                            Selanjutnya
                        </button>
                    </div>
                </div>

                <!-- STEP 2: Produk & Paket -->
                <div x-show="wizardStep === 2" class="space-y-6 animate-fadeIn" x-transition>
                    <div class="p-6 md:p-8 bg-sky-500/5 dark:bg-slate-900/40 rounded-3xl border border-sky-100/50 dark:border-slate-800/40 grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-6">
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-350">Varian Produk Air <span class="text-rose-500">*</span></label>
                                <select name="id_produk" required x-model="checkoutProduct" 
                                        @change="
                                            const selectedOpt = $el.options[$el.selectedIndex];
                                            checkoutPrice = parseInt(selectedOpt.getAttribute('data-price') || 0);
                                            selectedProductCapacity = selectedOpt.getAttribute('data-capacity') || '';
                                        "
                                        class="w-full px-4 py-3.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:border-blue-500 transition">
                                    <option value="" data-price="0">-- Pilih Produk Air --</option>
                                    @if($produk->isEmpty())
                                        <option value="1" data-price="50000" data-capacity="19L">Kangen Ultra Galon 19L (Rp 50.000)</option>
                                        <option value="2" data-price="12000" data-capacity="1500ml">Kangen Fresh Bottle 1500ml (Rp 12.000)</option>
                                        <option value="3" data-price="6000" data-capacity="600ml">Kangen Active Bottle 600ml (Rp 6.000)</option>
                                        <option value="4" data-price="2000" data-capacity="220ml">Kangen Hydrate Cup 220ml (Rp 2.000)</option>
                                    @else
                                        @foreach($produk as $item)
                                            <option value="{{ $item->id_produk }}" data-price="{{ $item->harga }}" data-capacity="{{ $item->kapasitas }}">{{ $item->nama_produk }} (Rp {{ number_format($item->harga, 0, ',', '.') }})</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-350">Jumlah Unit <span class="text-rose-500">*</span></label>
                                    <input type="number" name="jumlah" required min="1" x-model.number="checkoutQuantity"
                                           class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:border-blue-500 shadow-inner">
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-350">Metode Transaksi</label>
                                    <div class="grid grid-cols-2 gap-2">
                                        <button type="button" @click="checkoutType = 'one-off'" 
                                                :class="checkoutType === 'one-off' ? 'bg-blue-500 text-white shadow-sm' : 'bg-white dark:bg-slate-800 text-slate-500 border border-slate-200 dark:border-slate-700'"
                                                class="py-3.5 rounded-xl text-[10px] font-extrabold uppercase transition-all duration-300">
                                            Sekali Beli
                                        </button>
                                        <button type="button" @click="checkoutType = 'subscription'" 
                                                :class="checkoutType === 'subscription' ? 'bg-blue-500 text-white shadow-sm' : 'bg-white dark:bg-slate-800 text-slate-500 border border-slate-200 dark:border-slate-700'"
                                                class="py-3.5 rounded-xl text-[10px] font-extrabold uppercase transition-all duration-300">
                                            Langganan
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Dynamic Subscription Period Field -->
                            <div class="space-y-2 animate-fadeIn" x-show="checkoutType === 'subscription'" x-transition>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-350">Periode Antar Langganan</label>
                                <select x-model="checkoutPeriod" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:border-blue-500 transition">
                                    <option value="harian">Harian (Diantar Setiap Pagi)</option>
                                    <option value="mingguan">Mingguan (Diantar 2x Seminggu)</option>
                                    <option value="bulanan">Bulanan (Diantar Setiap Bulan)</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex flex-col justify-between border-t md:border-t-0 md:border-l border-slate-200 dark:border-slate-800 pt-6 md:pt-0 md:pl-8 space-y-6">
                            <div class="space-y-4">
                                <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Detail Ringkasan Tagihan</h4>
                                <div class="space-y-3 font-semibold text-slate-500 dark:text-slate-400 text-xs">
                                    <div class="flex justify-between">
                                        <span>Tipe Transaksi</span>
                                        <span class="text-slate-800 dark:text-white font-bold" x-text="checkoutType === 'subscription' ? 'Langganan (' + checkoutPeriod.toUpperCase() + ')' : 'Sekali Beli'"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Kapasitas Kemasan</span>
                                        <span class="text-slate-800 dark:text-white font-bold" x-text="selectedProductCapacity || '-'"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Tarif Satuan</span>
                                        <span class="text-slate-800 dark:text-white font-bold" x-text="formatRupiah(checkoutPrice)"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Jumlah Unit</span>
                                        <span class="text-slate-800 dark:text-white font-bold" x-text="checkoutQuantity + ' Unit'"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="p-5 bg-white dark:bg-slate-800 rounded-2xl border border-sky-100 dark:border-slate-700 shadow-md shadow-sky-900/5">
                                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Total Pembayaran</span>
                                <span class="block text-3xl font-black text-blue-600 dark:text-blue-400" x-text="formatRupiah(calculateTotal())"></span>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between pt-4">
                        <button type="button" @click="wizardStep = 1" 
                                class="px-6 py-3.5 bg-slate-200 hover:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-extrabold tracking-wider uppercase transition">
                            Sebelumnya
                        </button>
                        <button type="button" @click="if(validateStep2()) wizardStep = 3" 
                                class="px-6 py-3.5 bg-blue-500 hover:bg-blue-600 text-white rounded-xl text-xs font-extrabold tracking-wider uppercase transition shadow-md">
                            Selanjutnya
                        </button>
                    </div>
                </div>

                <!-- STEP 3: Pembayaran & Catatan -->
                <div x-show="wizardStep === 3" class="space-y-6 animate-fadeIn" x-transition>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-350">Metode Pembayaran <span class="text-rose-500">*</span></label>
                            <select name="metode_pembayaran" required 
                                    class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:border-blue-500 transition">
                                <option value="transfer">Transfer Bank Mandiri / BCA</option>
                                <option value="e-wallet">E-Wallet QRIS (Gopay / OVO)</option>
                                <option value="tunai">Tunai / Bayar di Tempat (COD)</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-350">Catatan Tambahan Pengiriman (Opsional)</label>
                            <input type="text" name="catatan" placeholder="Contoh: Taruh galon di teras rumah depan pagar" 
                                   class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:border-blue-500 shadow-inner">
                        </div>
                    </div>

                    <!-- Payment Guide Glassmorphic Panel -->
                    <div class="p-6 bg-emerald-500/5 dark:bg-emerald-950/10 rounded-2xl border border-emerald-500/20 space-y-2">
                        <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 block tracking-wide">💡 Panduan Pembayaran Aman</span>
                        <p class="text-xs text-slate-600 dark:text-slate-350 font-light leading-relaxed">
                            Setelah Anda menekan tombol konfirmasi di bawah ini, nomor invoice resmi Anda akan diterbitkan. Layanan admin Kangen Water kami akan segera menghubungi nomor WhatsApp Anda dalam kurun waktu 10 menit untuk memandu penyelesaian pembayaran via transfer bank atau kode QRIS.
                        </p>
                    </div>

                    <div class="flex justify-between pt-4 gap-4">
                        <button type="button" @click="wizardStep = 2" 
                                class="px-6 py-3.5 bg-slate-200 hover:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-extrabold tracking-wider uppercase transition">
                            Sebelumnya
                        </button>
                        <button type="submit" :disabled="submitting" 
                                class="flex-1 py-3.5 bg-gradient-to-r from-blue-600 to-sky-500 hover:from-blue-700 hover:to-sky-600 text-white rounded-xl text-xs font-extrabold tracking-wider uppercase transition shadow-lg shadow-blue-500/10 flex items-center justify-center gap-3">
                            <template x-if="submitting">
                                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </template>
                            <span x-text="submitting ? 'Mengirim...' : 'Konfirmasi & Selesaikan Pesanan'"></span>
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </section>

    <!-- Upgraded Courier Tracking Section with Animated Route Simulator -->
    <section id="tracking" class="max-w-4xl mx-auto px-6 py-20 border-t border-slate-200 dark:border-slate-900">
        <div class="text-center max-w-2xl mx-auto mb-12 space-y-4">
            <span class="text-blue-600 dark:text-blue-400 font-bold text-xs tracking-widest uppercase">Status Pengantaran</span>
            <h2 class="text-3xl font-black text-slate-800 dark:text-white">Pelacakan Pesanan Kangen</h2>
            <p class="text-slate-600 dark:text-slate-350 font-light">Ketikkan nomor invoice Anda untuk menelusuri pergerakan armada pengantaran kurir Kangen Water.</p>
        </div>

        <div class="glass-panel p-8 rounded-[2.5rem] shadow-xl border space-y-8"
             x-data="{
                truckProgress: 0,
                intervalId: null,

                searchInvoice() {
                    if(!trackInvoice.trim()) return;
                    
                    // Clear existing interval
                    if(this.intervalId) clearInterval(this.intervalId);

                    const hash = trackInvoice.split('').reduce((acc, char) => acc + char.charCodeAt(0), 0);
                    const stages = ['dijadwalkan', 'dalam perjalanan', 'terkirim'];
                    const chosenStage = stages[hash % 3];

                    const mockCouriers = [
                        { name: 'Andi Pratama', hp: '0812-4422-9900', vehicle: 'Motor Box', plat: 'B 3012 SHZ' },
                        { name: 'Budi Santoso', hp: '0856-1188-4422', vehicle: 'Pickup Suzuki', plat: 'F 8920 CC' },
                        { name: 'Rian Wijaya', hp: '0878-9900-2211', vehicle: 'Tossa Roda Tiga', plat: 'A 2911 PL' }
                    ];
                    const chosenCourier = mockCouriers[hash % mockCouriers.length];

                    this.trackingData = {
                        invoice: trackInvoice.toUpperCase(),
                        status: chosenStage,
                        courier: chosenCourier,
                        date: new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }),
                        destination: 'Alamat Pengiriman Utama Terdaftar'
                    };

                    // Animate the Delivery Truck Progress based on delivery status
                    this.truckProgress = 0;
                    let maxProgress = 100;
                    if (chosenStage === 'dijadwalkan') maxProgress = 25;
                    if (chosenStage === 'dalam perjalanan') maxProgress = 65;
                    
                    this.intervalId = setInterval(() => {
                        if(this.truckProgress < maxProgress) {
                            this.truckProgress += 1;
                        } else {
                            clearInterval(this.intervalId);
                        }
                    }, 25);
                }
             }">
            
            <div class="flex flex-col sm:flex-row gap-4">
                <input type="text" x-model="trackInvoice" placeholder="Masukkan nomor invoice (Contoh: INV-20260524-XXXXX)" 
                       @keydown.enter="searchInvoice()"
                       class="flex-1 px-5 py-4 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-sm focus:outline-none focus:border-blue-500 shadow-inner">
                <button @click="searchInvoice()" 
                        class="px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl text-xs font-extrabold uppercase tracking-wider shadow-md">
                    Lacak Pesanan
                </button>
            </div>

            <!-- Tracking Timeline Visual Display -->
            <div x-show="trackingData" class="pt-6 border-t border-slate-150 dark:border-slate-800 space-y-8 animate-fadeIn" x-transition>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6 bg-sky-500/5 rounded-2xl border border-sky-100/50 dark:border-slate-800/40">
                    <div class="space-y-1">
                        <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest">Nomor Invoice Resmi</span>
                        <span class="block text-lg font-black text-slate-800 dark:text-white" x-text="trackingData?.invoice"></span>
                    </div>
                    <div class="space-y-1">
                        <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest">Tanggal Pemesanan</span>
                        <span class="block text-base font-bold text-slate-700 dark:text-slate-350" x-text="trackingData?.date"></span>
                    </div>
                </div>

                <!-- Upgraded Animated Route Map Simulator Container -->
                <div class="space-y-4">
                    <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Simulasi Peta Rute Pengiriman Kurir</h4>
                    <div class="h-44 w-full bg-slate-100 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 rounded-3xl relative overflow-hidden flex items-center justify-center p-6 shadow-inner">
                        <!-- Background Grid lines -->
                        <div class="absolute inset-0 bg-[linear-gradient(rgba(14,165,233,0.03)_1px,transparent_1px),linear-gradient(90deg,rgba(14,165,233,0.03)_1px,transparent_1px)] bg-[size:20px_20px]"></div>
                        
                        <!-- SVG Delivery Path -->
                        <svg class="w-full h-full" viewBox="0 0 400 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <!-- Route Line -->
                            <path d="M 20 50 C 100 20, 150 80, 250 30 C 310 -10, 350 70, 380 50" stroke="#cbd5e1" stroke-width="4" stroke-linecap="round" class="dark:stroke-slate-800"/>
                            <!-- Colored Active Line based on progress -->
                            <path d="M 20 50 C 100 20, 150 80, 250 30 C 310 -10, 350 70, 380 50" stroke="#3b82f6" stroke-width="4" stroke-linecap="round" 
                                  stroke-dasharray="400" :stroke-dashoffset="400 - (400 * (truckProgress / 100))" class="transition-all duration-300"/>
                            
                            <!-- Keypoints Checkpoints -->
                            <!-- Point 1: Pabrik (x:20, y:50) -->
                            <circle cx="20" cy="50" r="6" fill="#3b82f6" stroke="#fff" stroke-width="2"/>
                            <text x="20" y="70" font-size="8" fill="#94a3b8" font-weight="bold" text-anchor="middle">Pabrik</text>
                            
                            <!-- Point 2: Gudang (x:150, y:57) -->
                            <circle cx="150" cy="57" r="6" :fill="truckProgress >= 40 ? '#3b82f6' : '#cbd5e1'" stroke="#fff" stroke-width="2" class="transition-colors"/>
                            <text x="150" y="75" font-size="8" fill="#94a3b8" font-weight="bold" text-anchor="middle">Sortir</text>
                            
                            <!-- Point 3: Kota (x:270, y:18) -->
                            <circle cx="270" cy="18" r="6" :fill="truckProgress >= 70 ? '#3b82f6' : '#cbd5e1'" stroke="#fff" stroke-width="2" class="transition-colors"/>
                            <text x="270" y="35" font-size="8" fill="#94a3b8" font-weight="bold" text-anchor="middle">Kota</text>
                            
                            <!-- Point 4: Anda (x:380, y:50) -->
                            <circle cx="380" cy="50" r="6" :fill="truckProgress >= 100 ? '#10b981' : '#cbd5e1'" stroke="#fff" stroke-width="2" class="transition-colors"/>
                            <text x="380" y="70" font-size="8" fill="#94a3b8" font-weight="bold" text-anchor="middle">Tujuan</text>

                            <!-- Moving Vehicle Icon Wrapper -->
                            <g :style="'transform: translate(' + (truckProgress * 3.6) + 'px, ' + (Math.sin(truckProgress * 0.1) * 15 + 15) + 'px)'" class="transition-transform duration-300 duration-100 ease-out">
                                <!-- Luminous outer glow bubble for vehicle -->
                                <circle cx="10" cy="20" r="10" fill="rgba(59, 130, 246, 0.25)" class="animate-pulse"/>
                                <!-- Active Droplet Icon -->
                                <path d="M10 14.5 C9 16.5, 7.5 18, 7.5 19 C7.5 20.4, 8.6 21.5, 10 21.5 C11.4 21.5, 12.5 20.4, 12.5 19 C12.5 18, 11 16.5, 10 14.5 Z" fill="#3b82f6"/>
                            </g>
                        </svg>
                    </div>
                </div>

                <!-- Courier Information Card -->
                <div class="p-6 bg-slate-50 dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center shadow-md">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Kurir Pengirim</span>
                            <span class="block text-base font-black text-slate-850 dark:text-white" x-text="trackingData?.courier?.name"></span>
                            <span class="block text-xs text-slate-500" x-text="trackingData?.courier?.hp"></span>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 md:flex md:items-center gap-6 w-full md:w-auto border-t md:border-t-0 pt-4 md:pt-0 border-slate-200 dark:border-slate-800">
                        <div class="space-y-1">
                            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest">Kendaraan Armada</span>
                            <span class="block text-sm font-bold text-slate-700 dark:text-slate-350" x-text="trackingData?.courier?.vehicle"></span>
                        </div>
                        <div class="space-y-1">
                            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest">Plat Nomor</span>
                            <span class="block text-xs font-bold text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800 border px-2.5 py-1 rounded-lg uppercase shadow-sm" x-text="trackingData?.courier?.plat"></span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-400 py-16 border-t border-slate-800 transition-colors duration-500">
        <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-12 gap-12">
            <div class="md:col-span-6 space-y-6">
                <a href="#" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-blue-600 to-sky-400 flex items-center justify-center shadow-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                        </svg>
                    </div>
                    <span class="text-xl font-extrabold tracking-tight text-white">Kangen Water</span>
                </a>
                <p class="text-sm font-light max-w-md leading-relaxed">
                    Air mineral alkali premium dengan ionisasi mikro molekul canggih kaya antioksidan aktif bebas dari kontaminasi zat kimia berbahaya, dirancang khusus untuk memulihkan vitalitas hidrasi optimal sel-sel tubuh Anda.
                </p>
                <div class="flex gap-4">
                    <div class="w-9 h-9 rounded-full bg-slate-800 hover:bg-slate-700 transition flex items-center justify-center cursor-pointer text-xs font-bold text-white">IG</div>
                    <div class="w-9 h-9 rounded-full bg-slate-800 hover:bg-slate-700 transition flex items-center justify-center cursor-pointer text-xs font-bold text-white">WA</div>
                    <div class="w-9 h-9 rounded-full bg-slate-800 hover:bg-slate-700 transition flex items-center justify-center cursor-pointer text-xs font-bold text-white">FB</div>
                </div>
            </div>

            <div class="md:col-span-3 space-y-4">
                <h4 class="text-white font-extrabold text-xs tracking-widest uppercase">Pilihan Layanan</h4>
                <ul class="space-y-3 text-sm font-medium">
                    <li><a href="#products" class="hover:text-blue-400 transition">Galon 19 Liter</a></li>
                    <li><a href="#products" class="hover:text-blue-400 transition">Kemasan Botol 1500ml</a></li>
                    <li><a href="#products" class="hover:text-blue-400 transition">Kemasan Botol 600ml</a></li>
                    <li><a href="#subscriptions" class="hover:text-blue-400 transition">Paket Langganan Air</a></li>
                </ul>
            </div>

            <div class="md:col-span-3 space-y-4">
                <h4 class="text-white font-extrabold text-xs tracking-widest uppercase">Hubungi Kantor</h4>
                <p class="text-sm font-light leading-relaxed">
                    Jl. Indah Lestari No. 24, Kav 10-12<br>
                    Kota Jakarta Selatan, DKI Jakarta
                </p>
                <p class="text-sm font-bold text-white">Telepon: (021) 8800-4422</p>
                <p class="text-sm font-bold text-white">WhatsApp: 0812-3456-7890</p>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-6 pt-12 mt-12 border-t border-slate-800/80 flex flex-col sm:flex-row items-center justify-between gap-6">
            <span class="text-[11px] font-semibold text-slate-500">© 2026 Kangen Water. Seluruh Hak Cipta Dilindungi.</span>
            
            <div class="flex items-center gap-3">
                <!-- Direct Admin Login Link -->
                <a href="/admin/login" class="px-4 py-2 border border-blue-500/20 hover:border-blue-500/50 bg-blue-500/5 hover:bg-blue-500/10 rounded-xl text-xs font-bold text-blue-400 hover:text-blue-300 transition-all flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                    Portal Admin
                </a>

                <!-- Direct Customer Login & Register Link -->
                <a href="/customer/login" class="px-4 py-2 border border-sky-500/20 hover:border-sky-500/50 bg-sky-500/5 hover:bg-sky-500/10 rounded-xl text-xs font-bold text-sky-400 hover:text-sky-300 transition-all flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Portal Pelanggan
                </a>

                <a href="/customer/register" class="px-4 py-2 border border-slate-700 hover:border-slate-500 bg-slate-800/20 rounded-xl text-xs font-bold text-slate-405 hover:text-slate-300 transition-all">
                    Daftar Pelanggan
                </a>
                
                <!-- Admin Preview Toggle Switch -->
                <button @click="showAdminPanel = !showAdminPanel" 
                        class="px-4 py-2 border border-slate-800 hover:border-slate-600 rounded-xl text-xs font-bold text-slate-500 hover:text-slate-300 transition-all flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                    </svg>
                    <span x-text="showAdminPanel ? 'Sembunyikan Panel Admin' : 'Tampilkan Panel Admin (Developer)'"></span>
                </button>
            </div>
        </div>
    </footer>

    <!-- SUCCESS CHECKOUT DIALOG MODAL -->
    <div x-show="successModal" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-6 bg-slate-950/80 backdrop-blur-sm" x-transition>
        <div class="bg-white rounded-[2.5rem] border border-sky-100 shadow-2xl p-8 max-w-lg w-full text-center space-y-6 relative overflow-hidden" @click.away="successModal = false">
            <div class="w-20 h-20 bg-emerald-50 text-emerald-500 rounded-full flex items-center justify-center mx-auto shadow-inner">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
            </div>

            <div class="space-y-2">
                <h3 class="text-2xl font-black text-slate-800">Transaksi Berhasil Didaftar!</h3>
                <p class="text-xs text-slate-500 font-light" x-text="orderResult.message"></p>
            </div>

            <div class="p-6 bg-slate-50 rounded-2xl border border-slate-200/50 space-y-3 font-semibold text-slate-600 text-sm text-left">
                <div class="flex justify-between">
                    <span>Kode Invoice</span>
                    <span class="text-slate-850 font-black" x-text="orderResult.invoice"></span>
                </div>
                <div class="flex justify-between">
                    <span>Total Pembayaran</span>
                    <span class="text-blue-600 font-black" x-text="formatRupiah(orderResult.total)"></span>
                </div>
                <div class="flex justify-between">
                    <span>Metode Bayar</span>
                    <span class="text-slate-800 uppercase" x-text="orderResult.metode"></span>
                </div>
            </div>

            <div class="pt-2 space-y-4">
                <p class="text-[10px] text-slate-400 font-medium">Mohon simpan nomor invoice di atas untuk mengecek status armada kurir Kangen Water pada panel pelacakan.</p>
                <div class="flex gap-4">
                    <button @click="successModal = false" class="flex-1 py-3.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs uppercase tracking-wider transition">Tutup</button>
                    <button @click="
                        successModal = false;
                        trackInvoice = orderResult.invoice;
                        document.getElementById('tracking').scrollIntoView({ behavior: 'smooth' });
                        setTimeout(() => { document.querySelector('#tracking button').click() }, 500);
                    " class="flex-1 py-3.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs uppercase tracking-wider shadow-md transition">Lacak Kurir</button>
                </div>
            </div>
        </div>
    </div>

    <!-- SECRET ADMIN PREVIEW METRICS DRAWER -->
    <div x-show="showAdminPanel" class="fixed bottom-6 right-6 z-40 max-w-md w-full glass-dark text-white rounded-[2rem] border border-white/10 shadow-2xl p-6 space-y-6 animate-fadeIn" x-transition>
        <div class="flex items-center justify-between pb-3 border-b border-white/10">
            <div class="flex items-center gap-2.5">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                <span class="text-[10px] font-bold tracking-widest uppercase text-slate-300">Live Database Overview (PHP Artisan)</span>
            </div>
            <button @click="showAdminPanel = false" class="text-slate-400 hover:text-white text-xs font-bold">×</button>
        </div>

        <div class="space-y-4">
            <p class="text-[10px] text-slate-400 font-light leading-relaxed">
                Rangkuman metrik yang ditarik secara dinamis dari database Anda (`gudang`, `kurir`, `transaksi`, `langganan`) via web controller. Nilai tiruan aktif jika migrasi belum diisi.
            </p>
            
            <div class="grid grid-cols-2 gap-4">
                <div class="p-4 bg-white/5 rounded-xl border border-white/5 space-y-1">
                    <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider">Unit Gudang Aktif</span>
                    <span class="block text-lg font-black text-blue-400">{{ $stats['gudang_aktif'] }} Unit</span>
                </div>
                <div class="p-4 bg-white/5 rounded-xl border border-white/5 space-y-1">
                    <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider">Kapasitas Stok Gudang</span>
                    <span class="block text-lg font-black text-sky-400">{{ number_format($stats['stok_total'], 0, ',', '.') }} Pcs</span>
                </div>
                <div class="p-4 bg-white/5 rounded-xl border border-white/5 space-y-1">
                    <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider">Armada Kurir Aktif</span>
                    <span class="block text-lg font-black text-teal-400">{{ $stats['kurir_aktif'] }} Personil</span>
                </div>
                <div class="p-4 bg-white/5 rounded-xl border border-white/5 space-y-1">
                    <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider">Paket Langganan Aktif</span>
                    <span class="block text-lg font-black text-indigo-400">{{ $stats['langganan_aktif'] }} Paket</span>
                </div>
            </div>

            <!-- Total Revenue Stat -->
            <div class="p-4 bg-white/5 rounded-xl border border-white/5 flex items-center justify-between">
                <div>
                    <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider">Akumulasi Omset Penjualan</span>
                    <span class="block text-[10px] text-slate-400 font-light">Jumlah Transaksi: {{ $stats['total_transaksi'] }}</span>
                </div>
                <span class="text-lg font-black text-emerald-400">Rp {{ number_format($stats['total_pendapatan'], 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="pt-2 text-[9px] text-slate-500 font-semibold text-center flex justify-center gap-3 border-t border-white/5">
            <span>Server: local</span>
            <span>Framework: Laravel 11</span>
            <span>CSS: Tailwind v4</span>
        </div>
    </div>

</body>
</html>
