<!DOCTYPE html>
<html lang="id" class="scroll-smooth" style="overscroll-behavior: none;">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#3EAEB1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- SEO & Meta Tags Optimization Maksimal -->
    @hasSection('title')
        <title>@yield('title') | Pharmasis</title>
        <meta name="title" content="@yield('title') | Pharmasis">
        <meta property="og:title" content="@yield('title') | Pharmasis">
        <meta property="twitter:title" content="@yield('title') | Pharmasis">
    @else
        <title>Pharmasis | Cek Kesehatan & Direktori Obat Terpercaya</title>
        <meta name="title" content="Pharmasis | Cek Kesehatan & Direktori Obat Terpercaya">
        <meta property="og:title" content="Pharmasis | Cek Kesehatan & Direktori Obat Terpercaya">
        <meta property="twitter:title" content="Pharmasis | Cek Kesehatan & Direktori Obat Terpercaya">
    @endif

    <meta name="description" content="@yield('meta_description', 'Cek gejala penyakit, cari informasi obat terlengkap, dan dapatkan panduan kesehatan terpercaya dengan Pharmasis. Solusi pintar untuk kesehatan Anda dan keluarga.')">
    <meta name="keywords" content="Pharmasis, cek kesehatan online, cek gejala penyakit, direktori obat, informasi obat, panduan medis, asisten kesehatan">
    <meta name="author" content="Pharmasis">
    <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
    <meta name="language" content="id">
    
    <!-- Open Graph / Facebook / WhatsApp -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:description" content="@yield('meta_description', 'Cek gejala penyakit, cari informasi obat terlengkap, dan dapatkan panduan kesehatan terpercaya dengan Pharmasis.')">
    <meta property="og:image" content="{{ asset('images/icon.png') }}">
    <meta property="og:site_name" content="Pharmasis">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:description" content="@yield('meta_description', 'Cek gejala penyakit, cari informasi obat terlengkap, dan dapatkan panduan kesehatan terpercaya dengan Pharmasis.')">
    <meta property="twitter:image" content="{{ asset('images/icon.png') }}">

    <!-- Canonical URL untuk menghindari Duplicate Content -->
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3EAEB1',
                        secondary: '#61BACA',
                        soft: '#9FD8E1',
                        card: '#9CD1CE',
                        'primary-dark': '#2d8a8d',
                        'primary-light': '#e8f7f8',
                        ink: {
                            900: '#0b1f24',
                            800: '#16323a',
                            700: '#27474f',
                            500: '#5c7178',
                            400: '#8b9ba0',
                            300: '#b6c1c5',
                        },
                    },
                    fontFamily: {
                        sans: ['Geist', 'Inter', 'system-ui', 'sans-serif'],
                        body: ['Geist', 'Inter', 'system-ui', 'sans-serif'],
                        display: ['"Instrument Serif"', 'ui-serif', 'Georgia', 'serif'],
                        heading: ['Geist', 'Inter', 'system-ui', 'sans-serif'],
                        brand: ['"Instrument Serif"', 'ui-serif', 'Georgia', 'serif'],
                        mono: ['"Geist Mono"', 'ui-monospace', 'SFMono-Regular', 'monospace'],
                        mothwing: ['Mothwing', 'serif'],
                    },
                    letterSpacing: {
                        'tightish': '-0.012em',
                        'tighter-2': '-0.025em',
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.3s ease-in-out',
                        'slide-down': 'slideDown 0.2s ease-out',
                        'pulse-soft': 'pulseSoft 2s ease-in-out infinite',
                    },
                    keyframes: {
                        fadeIn: { '0%': { opacity: 0, transform: 'translateY(4px)' }, '100%': { opacity: 1, transform: 'translateY(0)' } },
                        slideDown: { '0%': { opacity: 0, transform: 'translateY(-8px)' }, '100%': { opacity: 1, transform: 'translateY(0)' } },
                        pulseSoft: { '0%,100%': { opacity: 1 }, '50%': { opacity: 0.6 } },
                    }
                }
            }
        }
    </script>
    {{-- Custom App Icon --}}
    <link rel="icon" href="{{ asset('images/icon.png') }}">

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Markdown renderer for AI output -->
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

    {{-- Google Fonts: Geist (body/UI) + Instrument Serif (display) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Geist:wght@300;400;500;600;700;800&family=Geist+Mono:wght@400;500;600&family=Instrument+Serif:ital,wght@0,400;1,400&display=swap"
        rel="stylesheet">

    <style>
        @font-face {
            font-family: 'Mothwing';
            src: url('{{ asset('fonts/mothwing-demo.otf') }}') format('opentype');
            font-weight: normal;
            font-style: normal;
            font-display: swap;
        }

        [x-cloak] { display: none !important; }

        /* ── Base typographic refinements ── */
        html { font-feature-settings: "ss01", "cv11", "cv02"; }
        body {
            font-family: 'Geist', 'Inter', system-ui, sans-serif;
            font-feature-settings: "ss01", "cv11";
            letter-spacing: -0.005em;
            color: #16323a;
        }
        .font-display { font-family: 'Instrument Serif', ui-serif, Georgia, serif; letter-spacing: -0.01em; }
        .font-body    { font-family: 'Geist', 'Inter', system-ui, sans-serif; }
        .font-mono    { font-family: 'Geist Mono', ui-monospace, SFMono-Regular, monospace; }
        .display-italic { font-family: 'Instrument Serif', ui-serif, Georgia, serif; font-style: italic; letter-spacing: -0.005em; }

        /* ── Design Tokens ── */
        :root {
            --primary: #3EAEB1;
            --primary-dark: #2d8a8d;
            --primary-light: #e8f7f8;

            /* Glass system — softer, smoother, modern not loud */
            --glass-bg-strong: rgba(255,255,255,0.74);
            --glass-bg: rgba(255,255,255,0.62);
            --glass-bg-soft: rgba(255,255,255,0.48);
            --glass-border: rgba(255,255,255,0.7);
            --glass-border-soft: rgba(255,255,255,0.55);
            --glass-ring: 0 1px 0 rgba(255,255,255,0.85) inset;
            --glass-shadow-sm: 0 4px 18px rgba(11,31,36,0.06);
            --glass-shadow-md: 0 10px 36px rgba(11,31,36,0.08);
            --glass-shadow-lg: 0 24px 64px rgba(11,31,36,0.12);
            --glass-tint: rgba(62,174,177,0.05);
        }

        /* ── Scrollbar ── */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(to bottom, rgba(62,174,177,0.55), rgba(159,216,225,0.6));
            border-radius: 8px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(to bottom, rgba(62,174,177,0.8), rgba(159,216,225,0.85));
        }

        /* ── Glass Utilities ── */
        .glass {
            background: var(--glass-bg);
            backdrop-filter: blur(20px) saturate(140%);
            -webkit-backdrop-filter: blur(20px) saturate(140%);
            border: 1px solid var(--glass-border);
            box-shadow: var(--glass-ring);
        }
        .glass-strong {
            background: var(--glass-bg-strong);
            backdrop-filter: blur(24px) saturate(150%);
            -webkit-backdrop-filter: blur(24px) saturate(150%);
            border: 1px solid var(--glass-border);
            box-shadow: var(--glass-shadow-sm), var(--glass-ring);
        }
        .glass-card {
            background: var(--glass-bg-strong);
            backdrop-filter: blur(22px) saturate(145%);
            -webkit-backdrop-filter: blur(22px) saturate(145%);
            border: 1px solid var(--glass-border);
            box-shadow: var(--glass-shadow-sm), var(--glass-ring);
            border-radius: 1.25rem;
        }
        .glass-soft {
            background: var(--glass-bg-soft);
            backdrop-filter: blur(16px) saturate(135%);
            -webkit-backdrop-filter: blur(16px) saturate(135%);
            border: 1px solid var(--glass-border-soft);
        }
        .glass-dark {
            background: rgba(11,40,42,0.62);
            backdrop-filter: blur(24px) saturate(140%);
            -webkit-backdrop-filter: blur(24px) saturate(140%);
            border: 1px solid rgba(62,174,177,0.22);
        }
        .glass-hover {
            transition: transform 0.3s cubic-bezier(.22,1,.36,1), box-shadow 0.3s ease, border-color 0.3s ease;
        }
        .glass-hover:hover {
            transform: translateY(-3px);
            box-shadow: var(--glass-shadow-md), var(--glass-ring);
            border-color: rgba(62,174,177,0.32);
        }

        /* ── Skeleton ── */
        .skeleton {
            background: linear-gradient(90deg,#e2f7f8 25%,#f0fafa 50%,#e2f7f8 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
        }
        @keyframes shimmer {
            0%   { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        /* ── Risk colors ── */
        .risk-minor    { --risk-color: #10B981; }
        .risk-moderate { --risk-color: #F59E0B; }
        .risk-major    { --risk-color: #EF4444; }

        /* ── Marquee ── */
        @keyframes marquee          { from { transform: translateX(0); } to { transform: translateX(calc(-100% - 1rem)); } }
        @keyframes marquee-vertical { from { transform: translateY(0); } to { transform: translateY(calc(-100% - 1rem)); } }
        .animate-marquee          { animation: marquee 40s linear infinite; }
        .animate-marquee-fast     { animation: marquee 18s linear infinite; }
        .animate-marquee-vertical { animation: marquee-vertical 40s linear infinite; }
        .group:hover .pause-on-hover { animation-play-state: paused; }

        /* ── Gradient hero utility ── */
        .gradient-hero { background: linear-gradient(135deg,#0d4f52 0%,#1a7a7d 40%,#3EAEB1 100%); }

        /* ── AI Markdown ── */
        .ai-markdown { font-size: 0.9rem; line-height: 1.65; }
        .ai-markdown p  { margin-bottom: 0.4rem; }
        .ai-markdown ul { list-style-type: disc; padding-left: 1.25rem; margin-bottom: 0.4rem; }
        .ai-markdown li { margin-bottom: 0.18rem; }
        .ai-markdown strong { font-weight: 600; }

        /* ──────────────────────────────────────────────────────────
           Sticky → Floating Glass Navbar (smooth morph on scroll)
           ────────────────────────────────────────────────────────── */
        .nav-shell {
            position: relative;
            z-index: 50;
            padding: 0;
            flex-shrink: 0;
            transition:
                padding 420ms cubic-bezier(.22,1,.36,1),
                background 380ms ease;
            background: linear-gradient(to bottom, rgba(248,254,254,0.0), rgba(248,254,254,0.0));
        }
        .nav-shell.is-floating { padding: 14px 16px 0; }
        .nav-shell.is-floating::before {
            content: '';
            position: absolute; inset: 0;
            background: linear-gradient(to bottom, rgba(248,254,254,0.55), rgba(248,254,254,0));
            pointer-events: none;
            opacity: 0;
            transition: opacity 320ms ease;
        }
        .nav-shell.is-floating::before { opacity: 1; }

        .nav-bar {
            position: relative;
            margin: 0 auto;
            max-width: 80rem;
            transition:
                max-width 420ms cubic-bezier(.22,1,.36,1),
                border-radius 420ms cubic-bezier(.22,1,.36,1),
                background 380ms ease,
                box-shadow 380ms ease,
                border-color 380ms ease,
                padding 420ms cubic-bezier(.22,1,.36,1),
                backdrop-filter 380ms ease;
            background: transparent;
            backdrop-filter: none;
            -webkit-backdrop-filter: none;
            border: none;
            border-radius: 0;
            box-shadow: none;
            padding: 0 24px;
        }
        .nav-shell.is-floating .nav-bar {
            max-width: 76rem;
            border-radius: 999px;
            background: rgba(255,255,255,0.65);
            backdrop-filter: blur(32px) saturate(180%);
            -webkit-backdrop-filter: blur(32px) saturate(180%);
            border: 1px solid rgba(255,255,255,0.85);
            box-shadow:
                0 1px 0 rgba(255,255,255,0.9) inset,
                0 20px 48px rgba(11,31,36,0.12),
                0 6px 18px rgba(62,174,177,0.14);
            padding: 0 12px 0 18px;
        }

        @media (max-width: 767px) {
            .nav-shell.is-floating .nav-bar {
                border-radius: 22px;
                max-width: calc(100% - 0px);
            }
        }

        /* Nav link with refined underline */
        .nav-link-modern {
            position: relative;
            transition: color 0.2s;
        }
        .nav-link-modern::after {
            content: '';
            position: absolute;
            bottom: -3px; left: 50%;
            transform: translateX(-50%) scaleX(0);
            width: 60%; height: 2px;
            background: linear-gradient(90deg, #3EAEB1, #61BACA);
            border-radius: 2px;
            transition: transform 0.25s cubic-bezier(.22,1,.36,1);
        }
        .nav-link-modern:hover::after { transform: translateX(-50%) scaleX(1); }

        /* CTA gradient button */
        .btn-gradient {
            background: linear-gradient(135deg,#3EAEB1,#2d8a8d);
            box-shadow: 0 6px 18px rgba(62,174,177,0.32), 0 1px 0 rgba(255,255,255,0.4) inset;
            color: #fff;
            transition: transform 0.25s cubic-bezier(.22,1,.36,1), box-shadow 0.25s ease;
        }
        .btn-gradient:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 26px rgba(62,174,177,0.4), 0 1px 0 rgba(255,255,255,0.4) inset;
        }

        /* Reduced motion respect */
        @media (prefers-reduced-motion: reduce) {
            .nav-bar, .nav-shell { transition: none !important; }
            .glass-hover { transition: none !important; }
        }

        /* ── Hard scroll lock ── */
        body {
            overscroll-behavior: none;
        }
        #scroll-wrapper {
            overscroll-behavior: none;
            -webkit-overflow-scrolling: auto;
        }

    </style>

    @stack('head')
    <!-- Structured Data (JSON-LD) untuk Maksimalisasi SEO (Rich Snippets) -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "WebSite",
      "name": "Pharmasis",
      "alternateName": "Cek Sehat AI",
      "url": "{{ url('/') }}",
      "potentialAction": {
        "@type": "SearchAction",
        "target": "{{ url('/search') }}?q={search_term_string}",
        "query-input": "required name=search_term_string"
      }
    }
    </script>
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "MedicalOrganization",
      "name": "Pharmasis",
      "url": "{{ url('/') }}",
      "logo": "{{ asset('images/icon.png') }}",
      "description": "Asisten kesehatan AI pintar untuk cek gejala penyakit, analisis kondisi medis, dan direktori obat terlengkap dengan penjelasan mudah dipahami."
    }
    </script>
</head>

<body class="font-body bg-[#f7fcfc] text-ink-800 antialiased selection:bg-primary/20 selection:text-ink-900 flex flex-col h-screen overflow-hidden">

    {{-- Ambient page-wide glass tint backdrop --}}
    <div aria-hidden="true" class="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute -top-40 -left-40 w-[40rem] h-[40rem] rounded-full opacity-30"
             style="background: radial-gradient(closest-side, rgba(62,174,177,0.22), transparent 70%);"></div>
        <div class="absolute -bottom-40 -right-32 w-[36rem] h-[36rem] rounded-full opacity-25"
             style="background: radial-gradient(closest-side, rgba(159,216,225,0.28), transparent 70%);"></div>
    </div>

    {{-- ════════════════════════════════════════ NAVBAR ════════════════════════════════════════ --}}
    <div class="nav-shell" id="nav-shell">
        <nav class="nav-bar" x-data="Object.assign(navSearch(), { mobileOpen: false })">
            <div class="flex items-center justify-between h-16 gap-4">

                {{-- Logo --}}
                <a href="{{ route('home') }}" class="flex-shrink-0 flex items-center gap-2.5 group">
                    <div class="relative">
                        <div class="absolute inset-0 bg-primary/30 rounded-xl blur-md group-hover:blur-lg transition-all duration-300"></div>
                        <img src="{{ asset('images/icon.png') }}" alt="Pharmasis Logo"
                            class="relative w-9 h-9 object-contain group-hover:scale-105 transition-transform duration-300">
                    </div>
                    <span class="text-[1.55rem] leading-none font-display tracking-tight text-ink-900">
                        Pharmasis
                    </span>
                </a>

                {{-- Nav Search --}}
                <div class="flex-1 max-w-xl relative hidden md:block"
                    @keydown.escape="open = false; query = ''; results = []">
                    <div class="relative">
                        <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-ink-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" x-model="query" @input.debounce.300ms="fetchInstant()"
                            @focus="if(query.length >= 2) open = true" @keydown.arrow-down.prevent="focusNext()"
                            @keydown.arrow-up.prevent="focusPrev()" @keydown.enter.prevent="selectFocused()"
                            placeholder="Search medicines, generics, drug classes…"
                            class="w-full pl-10 pr-9 py-2.5 text-sm rounded-full transition-all duration-300
                                   bg-white/55 hover:bg-white/75 focus:bg-white
                                   border border-white/60
                                   focus:outline-none focus:ring-4 focus:ring-primary/12 focus:border-primary/55
                                   placeholder:text-ink-400 text-ink-800"
                            style="backdrop-filter: blur(14px) saturate(140%); -webkit-backdrop-filter: blur(14px) saturate(140%);"
                            autocomplete="off" />
                        <div x-show="loading" class="absolute right-3 top-1/2 -translate-y-1/2">
                            <svg class="animate-spin w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                            </svg>
                        </div>
                    </div>

                    {{-- Dropdown --}}
                    <div x-show="open && results.length > 0" x-cloak @click.outside="open = false; results = []"
                        class="absolute top-full left-0 right-0 mt-2 rounded-2xl overflow-hidden z-50 animate-slide-down glass-strong">
                        <template x-for="(drug, idx) in results" :key="drug.id">
                            <a :href="drug.is_fda ? `/drugs/fda/${drug.slug}` : `/drugs/${drug.id}`"
                                :class="focusedIdx === idx ? 'bg-primary/10' : 'hover:bg-white/40'"
                                class="flex items-center gap-3 px-4 py-3 transition-colors border-b border-white/30 last:border-0">
                                <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                                    <span class="text-xs font-bold text-primary"
                                        x-text="drug.alpha_index || drug.name?.charAt(0) || '?'"></span>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-ink-800 truncate" x-text="drug.name"></p>
                                    <p class="text-xs text-ink-400 truncate"
                                        x-text="[drug.generic_name, drug.drug_class].filter(Boolean).join(' · ')"></p>
                                </div>
                            </a>
                        </template>
                        <a :href="`/search?q=${encodeURIComponent(query)}`"
                            class="flex items-center gap-2 px-4 py-2.5 text-sm text-primary font-medium hover:bg-primary/10 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            See all results for “<span x-text="query"></span>”
                        </a>
                    </div>
                    <div x-show="open && results.length === 0 && !loading && query.length >= 2" x-cloak
                        class="absolute top-full left-0 right-0 mt-2 rounded-2xl px-4 py-6 text-center z-50 glass-strong">
                        <p class="text-sm text-ink-500">No medicine found for “<span class="font-medium text-ink-800" x-text="query"></span>”</p>
                    </div>
                </div>

                {{-- Desktop Nav Links --}}
                <div class="hidden sm:flex items-center gap-1 sm:gap-2 text-sm font-medium">
                    <a href="{{ route('home') }}"
                        class="nav-link-modern px-4 py-2 text-ink-700 hover:text-primary rounded-full transition-all">Home</a>
                    <a href="{{ route('interactions.index') }}"
                        class="nav-link-modern px-4 py-2 text-ink-700 hover:text-primary rounded-full transition-all">Interactions</a>
                    <a href="{{ route('drugs.search') }}"
                        class="btn-gradient px-5 py-2.5 rounded-full font-semibold text-sm">Browse</a>
                </div>

                {{-- Mobile: Hamburger Button --}}
                <button @click="mobileOpen = !mobileOpen"
                    class="sm:hidden flex items-center justify-center w-9 h-9 rounded-full text-ink-700 hover:bg-white/70 transition-colors focus:outline-none border border-white/60"
                    style="backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);"
                    :aria-expanded="mobileOpen" aria-label="Toggle menu">
                    <svg x-show="!mobileOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                    <svg x-show="mobileOpen" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            {{-- Mobile Dropdown Menu --}}
            <div x-show="mobileOpen" x-cloak x-collapse class="sm:hidden">
                {{-- Mobile Search --}}
                <div class="px-1 pt-3 pb-2 relative" @keydown.escape="open = false; query = ''; results = []">
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-ink-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <input type="text" x-model="query" @input.debounce.300ms="fetchInstant()"
                            @focus="if(query.length >= 2) open = true"
                            @keydown.enter.prevent="if(query) window.location.href=`/search?q=${encodeURIComponent(query)}`"
                            placeholder="Search medicines…"
                            class="w-full pl-9 pr-4 py-2.5 text-sm rounded-2xl bg-white/70 border border-white/60 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary placeholder:text-ink-400 transition-all"
                            style="backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px);"
                            autocomplete="off" />
                    </div>
                    {{-- Mobile Search Dropdown --}}
                    <div x-show="open && results.length > 0" x-cloak @click.outside="open = false; results = []"
                        class="absolute left-0 right-0 top-full mt-1 rounded-2xl overflow-hidden z-50 max-h-64 overflow-y-auto glass-strong">
                        <template x-for="(drug, idx) in results" :key="drug.id">
                            <a :href="drug.is_fda ? `/drugs/fda/${drug.slug}` : `/drugs/${drug.id}`"
                                @click="mobileOpen = false"
                                class="flex items-center gap-3 px-4 py-3 hover:bg-white/40 transition-colors border-b border-white/30 last:border-0">
                                <div class="w-7 h-7 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                                    <span class="text-xs font-bold text-primary" x-text="drug.name?.charAt(0) || '?'"></span>
                                </div>
                                <p class="text-sm font-semibold text-ink-800 truncate" x-text="drug.name"></p>
                            </a>
                        </template>
                    </div>
                </div>

                {{-- Mobile Nav Links --}}
                <div class="px-1 pb-4 pt-2 space-y-1">
                    <a href="{{ route('home') }}" @click="mobileOpen = false"
                        class="flex items-center gap-3 px-4 py-3 text-sm font-semibold text-ink-700 hover:text-primary hover:bg-white/60 rounded-xl transition-all">
                        <svg class="w-4 h-4 text-ink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Home
                    </a>
                    <a href="{{ route('interactions.index') }}" @click="mobileOpen = false"
                        class="flex items-center gap-3 px-4 py-3 text-sm font-semibold text-ink-700 hover:text-primary hover:bg-white/60 rounded-xl transition-all">
                        <svg class="w-4 h-4 text-ink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        Interactions
                    </a>
                    <a href="{{ route('drugs.search') }}" @click="mobileOpen = false"
                        class="btn-gradient flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl">
                        <svg class="w-4 h-4 text-white/85" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                        </svg>
                        Browse Medicines
                    </a>
                </div>
            </div>
        </nav>
    </div>


    {{-- Scrollable Content Area --}}
    <div class="flex-1 overflow-y-auto relative" id="scroll-wrapper">

    {{-- Page Content --}}
    <main>
        @yield('content')
    </main>

    {{-- ════════════════════════════════════════ FOOTER ════════════════════════════════════════ --}}
    <footer class="mt-24 relative overflow-hidden" style="background: linear-gradient(135deg,#061e20 0%,#0d4f52 60%,#1a7a7d 100%);">
        {{-- Glass grid overlay --}}
        <div class="absolute inset-0 opacity-[0.06] pointer-events-none" style="background-image: repeating-linear-gradient(0deg,transparent,transparent 39px,rgba(159,216,225,0.7) 39px,rgba(159,216,225,0.7) 40px),repeating-linear-gradient(90deg,transparent,transparent 39px,rgba(159,216,225,0.7) 39px,rgba(159,216,225,0.7) 40px);"></div>
        {{-- Glow orbs --}}
        <div class="absolute top-0 right-0 w-80 h-80 rounded-full opacity-15 pointer-events-none" style="background: radial-gradient(circle, #3EAEB1, transparent 70%); transform: translate(30%,-40%);"></div>
        <div class="absolute bottom-0 left-0 w-72 h-72 rounded-full opacity-10 pointer-events-none" style="background: radial-gradient(circle, #9FD8E1, transparent 70%); transform: translate(-30%,40%);"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 pt-16 pb-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12 mb-12">
                <div>
                    <div class="flex items-center gap-2.5 mb-5">
                        <div class="relative">
                            <div class="absolute inset-0 bg-teal-400/30 rounded-xl blur-md"></div>
                            <img src="{{ asset('images/icon.png') }}" alt="Pharmasis Logo" class="relative w-9 h-9 object-contain opacity-95">
                        </div>
                        <span class="text-[1.7rem] leading-none font-display tracking-tight"
                              style="background: linear-gradient(90deg,#9FD8E1,#61BACA); -webkit-background-clip:text; -webkit-text-fill-color:transparent;">
                            Pharmasis
                        </span>
                    </div>
                    <p class="text-sm text-teal-100/75 leading-relaxed max-w-xs">
                        <span class="display-italic text-teal-50/90">Know your medicine.</span>
                        Accurate, easy-to-understand medicine information — when you need it.
                    </p>
                    {{-- Stat pills --}}
                    <div class="flex gap-2 mt-6 flex-wrap">
                        <span class="text-[11px] font-semibold px-3 py-1 rounded-full glass-dark text-teal-100">16,000+ Medicines</span>
                        <span class="text-[11px] font-semibold px-3 py-1 rounded-full glass-dark text-teal-100">AI-Powered</span>
                    </div>
                </div>
                <div>
                    <h3 class="text-white/90 mb-5 text-xs font-semibold uppercase tracking-[0.18em]">Quick Links</h3>
                    <ul class="space-y-3 text-sm">
                        <li><a href="{{ route('home') }}" class="text-teal-100/75 hover:text-teal-200 transition-colors flex items-center gap-2"><span class="w-1 h-1 rounded-full bg-primary inline-block"></span>Home</a></li>
                        <li><a href="{{ route('interactions.index') }}" class="text-teal-100/75 hover:text-teal-200 transition-colors flex items-center gap-2"><span class="w-1 h-1 rounded-full bg-primary inline-block"></span>Check Interactions</a></li>
                        <li><a href="{{ route('drugs.search') }}" class="text-teal-100/75 hover:text-teal-200 transition-colors flex items-center gap-2"><span class="w-1 h-1 rounded-full bg-primary inline-block"></span>Browse Medicines</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-white/90 mb-5 text-xs font-semibold uppercase tracking-[0.18em]">Disclaimer</h3>
                    <div class="rounded-2xl p-4 glass-dark">
                        <p class="text-xs text-teal-100/75 leading-relaxed">
                            This platform provides educational information only and does not replace professional medical advice. Always consult a licensed healthcare professional before making medical decisions.
                        </p>
                    </div>
                </div>
            </div>
            <div class="pt-6 flex flex-col sm:flex-row items-center justify-between gap-2" style="border-top:1px solid rgba(62,174,177,0.18);">
                <p class="text-xs text-teal-200/45">© {{ date('Y') }} Pharmasis. For educational purposes only.</p>
                <p class="text-xs text-teal-200/45">Data sourced from WebMD &amp; OpenFDA. Not for medical diagnosis or treatment.</p>
            </div>
        </div>
    </footer>

    </div>{{-- /scroll-wrapper --}}

    <script>
        /* ── Navbar scroll detection inside wrapper ── */
        document.addEventListener('DOMContentLoaded', () => {
            const wrapper = document.getElementById('scroll-wrapper');
            const navShell = document.getElementById('nav-shell');
            if (!wrapper || !navShell) return;
            let ticking = false;
            const threshold = 32;
            function update() {
                navShell.classList.toggle('is-floating', wrapper.scrollTop > threshold);
                ticking = false;
            }
            wrapper.addEventListener('scroll', () => {
                if (ticking) return;
                ticking = true;
                requestAnimationFrame(update);
            });
            update();
        });

        /* ── Nav search Alpine ── */
        function navSearch() {
            return {
                query: '',
                results: [],
                open: false,
                loading: false,
                focusedIdx: -1,
                async fetchInstant() {
                    if (this.query.length < 2) { this.results = []; this.open = false; return; }
                    this.loading = true;
                    try {
                        const res = await fetch(`/api/v1/search/instant?q=${encodeURIComponent(this.query)}`);
                        const data = await res.json();
                        this.results = data.data || [];
                        this.open = true;
                        this.focusedIdx = -1;
                    } catch (e) { this.results = []; }
                    this.loading = false;
                },
                focusNext() { this.focusedIdx = Math.min(this.focusedIdx + 1, this.results.length - 1); },
                focusPrev() { this.focusedIdx = Math.max(this.focusedIdx - 1, -1); },
                selectFocused() {
                    if (this.focusedIdx >= 0 && this.results[this.focusedIdx]) {
                        const d = this.results[this.focusedIdx];
                        window.location.href = d.is_fda ? `/drugs/fda/${d.slug}` : `/drugs/${d.id}`;
                    } else if (this.query) {
                        window.location.href = `/search?q=${encodeURIComponent(this.query)}`;
                    }
                }
            };
        }
    </script>

    @include('partials.cookie-consent')
    @stack('scripts')
</body>

</html>
