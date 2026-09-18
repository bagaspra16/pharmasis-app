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
    <!-- Pharmasis Multilingual Localization Engine (i18n) Inline Blade Partial -->
    @include('partials.i18n-script')

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

        /* ── Transparent White Glassmorphic Scrollbar ── */
        * {
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.6) rgba(255, 255, 255, 0.08);
        }
        ::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }
        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.08);
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.52);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.85);
            border-radius: 9999px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.14), inset 0 0 0 1px rgba(255, 255, 255, 0.7), inset 0 1px 2px rgba(255, 255, 255, 0.9);
            transition: background 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.82);
            border-color: #ffffff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.22), inset 0 0 0 1px rgba(255, 255, 255, 0.95), inset 0 1px 3px rgba(255, 255, 255, 1);
        }
        ::-webkit-scrollbar-thumb:active {
            background: rgba(255, 255, 255, 0.95);
            border-color: #ffffff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.28);
        }
        ::-webkit-scrollbar-corner {
            background: transparent;
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

        /* ── High-Visibility iOS Dark Spinner Animation ── */
        .ios-spinner {
            position: relative;
            width: 36px;
            height: 36px;
            display: inline-block;
        }
        .ios-spinner div {
            position: absolute;
            left: 43.5%;
            top: 35%;
            width: 13%;
            height: 30%;
            background: #090d16;
            border-radius: 50px;
            opacity: 0.12;
            animation: iosSpinnerFade 0.9s linear infinite;
            transform-origin: 50% 195%;
            box-shadow: 0 0.5px 1px rgba(0,0,0,0.4);
        }
        .ios-spinner div:nth-child(1)  { transform: rotate(0deg); animation-delay: -0.825s; }
        .ios-spinner div:nth-child(2)  { transform: rotate(30deg); animation-delay: -0.75s; }
        .ios-spinner div:nth-child(3)  { transform: rotate(60deg); animation-delay: -0.675s; }
        .ios-spinner div:nth-child(4)  { transform: rotate(90deg); animation-delay: -0.6s; }
        .ios-spinner div:nth-child(5)  { transform: rotate(120deg); animation-delay: -0.525s; }
        .ios-spinner div:nth-child(6)  { transform: rotate(150deg); animation-delay: -0.45s; }
        .ios-spinner div:nth-child(7)  { transform: rotate(180deg); animation-delay: -0.375s; }
        .ios-spinner div:nth-child(8)  { transform: rotate(210deg); animation-delay: -0.3s; }
        .ios-spinner div:nth-child(9)  { transform: rotate(240deg); animation-delay: -0.225s; }
        .ios-spinner div:nth-child(10) { transform: rotate(270deg); animation-delay: -0.15s; }
        .ios-spinner div:nth-child(11) { transform: rotate(300deg); animation-delay: -0.075s; }
        .ios-spinner div:nth-child(12) { transform: rotate(330deg); animation-delay: 0s; }

        @keyframes iosSpinnerFade {
            0% { opacity: 1; filter: drop-shadow(0 0 1px rgba(9,13,22,0.8)); }
            100% { opacity: 0.12; }
        }

        /* ── Antigravity Signature Colorful AI Styles ── */
        @keyframes antigravityShimmer {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .badge-antigravity-ai {
            position: relative;
            background: linear-gradient(135deg, rgba(6,182,212,0.12) 0%, rgba(99,102,241,0.14) 35%, rgba(236,72,153,0.14) 70%, rgba(245,158,11,0.12) 100%);
            background-size: 200% 200%;
            animation: antigravityShimmer 5s ease infinite;
            border: 1px solid rgba(99,102,241,0.28);
            box-shadow: 0 2px 10px -2px rgba(99,102,241,0.16), inset 0 1px 0 rgba(255,255,255,0.7);
            color: #312e81;
        }

        .badge-antigravity-ai-processing {
            position: relative;
            background: linear-gradient(135deg, rgba(14,165,233,0.16) 0%, rgba(139,92,246,0.2) 50%, rgba(244,63,94,0.16) 100%);
            background-size: 200% 200%;
            animation: antigravityShimmer 2.2s ease infinite;
            border: 1px solid rgba(139,92,246,0.38);
            box-shadow: 0 0 14px rgba(139,92,246,0.22);
            color: #4338ca;
        }

        .text-antigravity-gradient {
            background: linear-gradient(135deg, #0284c7 0%, #6366f1 35%, #d946ef 70%, #f59e0b 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* ──────────────────────────────────────────────────────────
           Sticky → Floating Glass Navbar (Ultra-Smooth Morph)
           ────────────────────────────────────────────────────────── */
        .nav-shell {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 50;
            padding: 0;
            pointer-events: none;
            transition: transform 500ms cubic-bezier(0.16, 1, 0.3, 1),
                        padding 500ms cubic-bezier(0.16, 1, 0.3, 1);
            background: transparent;
            will-change: transform, padding;
        }
        .nav-shell.is-floating {
            padding: 10px 16px 0;
            transform: translateY(2px);
        }

        .nav-bar {
            position: relative;
            margin: 0 auto;
            max-width: 80rem;
            pointer-events: auto;
            border-radius: 1rem;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(16px) saturate(160%);
            -webkit-backdrop-filter: blur(16px) saturate(160%);
            border: 1px solid transparent;
            box-shadow: none;
            padding: 0 24px;
            will-change: max-width, border-radius, background-color, border-color, box-shadow, padding;
            transition:
                max-width 500ms cubic-bezier(0.16, 1, 0.3, 1),
                border-radius 500ms cubic-bezier(0.16, 1, 0.3, 1),
                background-color 450ms cubic-bezier(0.16, 1, 0.3, 1),
                border-color 450ms cubic-bezier(0.16, 1, 0.3, 1),
                box-shadow 450ms cubic-bezier(0.16, 1, 0.3, 1),
                padding 500ms cubic-bezier(0.16, 1, 0.3, 1);
        }
        .nav-shell.is-floating .nav-bar {
            max-width: 72rem;
            border-radius: 9999px;
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(24px) saturate(180%);
            -webkit-backdrop-filter: blur(24px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.95);
            box-shadow:
                0 14px 40px -6px rgba(11, 31, 36, 0.11),
                0 2px 10px -2px rgba(11, 31, 36, 0.04),
                0 1px 0 rgba(255, 255, 255, 0.95) inset;
            padding: 0 16px 0 22px;
        }

        @media (max-width: 767px) {
            .nav-shell.is-floating {
                padding: 8px 10px 0;
            }
            .nav-shell.is-floating .nav-bar {
                border-radius: 1.25rem;
                max-width: 100%;
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
                <div class="flex-1 max-w-xl relative hidden md:block group"
                    @keydown.escape="open = false; query = ''; results = []">
                    <div class="relative">
                        <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-[18px] h-[18px] text-slate-400 group-focus-within:text-primary transition-colors duration-200 pointer-events-none z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" x-model="query" @input.debounce.300ms="fetchInstant()"
                            @focus="if(query.length >= 2) open = true" @keydown.arrow-down.prevent="focusNext()"
                            @keydown.arrow-up.prevent="focusPrev()" @keydown.enter.prevent="selectFocused()"
                            placeholder="Search medicines, generics, drug classes…"
                            class="w-full pl-10 pr-9 py-2.5 text-sm rounded-full transition-all duration-300
                                   bg-white/85 hover:bg-white focus:bg-white
                                   border border-slate-300/90 hover:border-slate-400 focus:border-primary
                                   focus:outline-none focus:ring-4 focus:ring-primary/15
                                   shadow-[0_4px_14px_rgba(11,31,36,0.07)] hover:shadow-[0_6px_20px_rgba(11,31,36,0.10)]
                                   placeholder:text-ink-400 text-ink-800"
                            style="backdrop-filter: blur(14px) saturate(140%); -webkit-backdrop-filter: blur(14px) saturate(140%);"
                            autocomplete="off" />
                        <button x-show="query.length > 0 && !loading" x-cloak @click="query = ''; results = []; open = false" type="button"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-0.5 rounded-full transition-colors z-10" aria-label="Clear search">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        <div x-show="loading" class="absolute right-3 top-1/2 -translate-y-1/2 z-10">
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

                {{-- Desktop Nav Links & Language Switcher --}}
                <div class="hidden sm:flex items-center gap-2 text-sm font-medium">
                    {{-- Language Selector Dropdown (Desktop - In Home Position) --}}
                    <div class="relative" x-data="{
                        langOpen: false,
                        currentLang: window.PharmasisI18n ? window.PharmasisI18n.getLanguage() : 'en',
                        get currentMeta() {
                            return (window.PharmasisI18n && window.PharmasisI18n.getLangMeta(this.currentLang)) || { code: 'en', name: 'English', native: 'English' };
                        },
                        selectLang(code) {
                            this.langOpen = false;
                            if (window.PharmasisI18n) {
                                window.PharmasisI18n.setLanguage(code);
                            }
                        },
                        init() {
                            window.addEventListener('pharmasis:languageChanged', (e) => {
                                this.currentLang = e.detail.lang;
                            });
                        }
                    }">
                        <button @click="langOpen = !langOpen" @click.outside="langOpen = false" type="button"
                            class="flex items-center gap-2 px-3.5 py-2 rounded-full text-xs font-semibold text-ink-800 bg-white/85 hover:bg-white border border-slate-200/90 shadow-[0_2px_8px_rgba(11,31,36,0.06)] hover:shadow-[0_4px_14px_rgba(11,31,36,0.12)] transition-all focus:outline-none"
                            style="backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);"
                            title="Multi-Language Switcher">
                            <svg class="w-4 h-4 text-teal-700 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                            </svg>
                            <span class="tracking-wider uppercase font-mono text-xs font-bold text-teal-900" x-text="currentMeta.code"></span>
                            <span class="text-xs font-semibold text-slate-600 hidden lg:inline" x-text="'· ' + currentMeta.native"></span>
                            <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': langOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        {{-- Dropdown Panel --}}
                        <div x-show="langOpen" x-cloak
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                            x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                            class="absolute left-0 mt-2 w-60 rounded-2xl p-1.5 z-50 glass-strong shadow-2xl border border-white/90 max-h-80 overflow-y-auto">
                            <div class="px-3 py-2 border-b border-slate-100/70 mb-1 flex items-center justify-between">
                                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Multi-Language</p>
                                <span class="text-[9px] font-mono bg-teal-50 text-teal-700 px-1.5 py-0.5 rounded font-bold">11 Languages</span>
                            </div>
                            <div class="space-y-0.5">
                                <template x-for="lang in (window.PharmasisI18n ? window.PharmasisI18n.languages : [])" :key="lang.code">
                                    <button type="button" @click="selectLang(lang.code)"
                                        class="w-full flex items-center justify-between px-2.5 py-2 rounded-xl text-xs transition-colors"
                                        :class="currentLang === lang.code ? 'bg-primary/15 text-primary-dark font-bold' : 'hover:bg-white/60 text-ink-700 font-medium'">
                                        <div class="flex items-center gap-2.5 min-w-0">
                                            <span class="w-6 h-5 rounded flex items-center justify-center text-[10px] font-bold font-mono uppercase bg-slate-100 text-slate-700 border border-slate-200/80" x-text="lang.code"></span>
                                            <div class="text-left truncate">
                                                <p class="truncate" x-text="lang.native"></p>
                                                <p class="text-[10px] opacity-60 truncate" x-text="lang.name"></p>
                                            </div>
                                        </div>
                                        <svg x-show="currentLang === lang.code" class="w-4 h-4 text-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('interactions.index') }}" data-i18n="nav_interactions"
                        class="nav-link-modern px-3.5 py-2 text-ink-700 hover:text-primary rounded-full transition-all">Interactions</a>
                    <a href="{{ route('drugs.search') }}" data-i18n="nav_browse"
                        class="btn-gradient px-4 py-2 rounded-full font-semibold text-xs sm:text-sm">Browse</a>
                </div>

                {{-- Mobile: Hamburger & Lang Capsule --}}
                <div class="sm:hidden flex items-center gap-2">
                    {{-- Mobile Language Selector --}}
                    <div class="relative" x-data="{
                        mOpen: false,
                        currentLang: window.PharmasisI18n ? window.PharmasisI18n.getLanguage() : 'en',
                        get currentMeta() {
                            return (window.PharmasisI18n && window.PharmasisI18n.getLangMeta(this.currentLang)) || { code: 'en', name: 'English', native: 'English' };
                        },
                        selectLang(code) {
                            this.mOpen = false;
                            if (window.PharmasisI18n) window.PharmasisI18n.setLanguage(code);
                        },
                        init() {
                            window.addEventListener('pharmasis:languageChanged', (e) => { this.currentLang = e.detail.lang; });
                        }
                    }">
                        <button @click="mOpen = !mOpen" @click.outside="mOpen = false" type="button"
                            class="flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold text-ink-700 bg-white/80 border border-slate-200 shadow-sm focus:outline-none"
                            style="backdrop-filter: blur(10px);">
                            <svg class="w-3.5 h-3.5 text-teal-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                            </svg>
                            <span class="uppercase text-[11px] font-mono font-bold text-teal-900" x-text="currentMeta.code"></span>
                        </button>
                        <div x-show="mOpen" x-cloak
                            class="absolute right-0 mt-2 w-52 rounded-2xl p-1.5 z-50 glass-strong shadow-2xl border border-white/90 max-h-72 overflow-y-auto">
                            <template x-for="lang in (window.PharmasisI18n ? window.PharmasisI18n.languages : [])" :key="lang.code">
                                <button type="button" @click="selectLang(lang.code)"
                                    class="w-full flex items-center justify-between px-2.5 py-2 rounded-xl text-xs transition-colors"
                                    :class="currentLang === lang.code ? 'bg-primary/15 text-primary-dark font-bold' : 'hover:bg-white/60 text-ink-700 font-medium'">
                                    <div class="flex items-center gap-2 truncate">
                                        <span class="w-5 h-4 rounded flex items-center justify-center text-[9px] font-bold font-mono uppercase bg-slate-100 text-slate-700 border border-slate-200/80" x-text="lang.code"></span>
                                        <span class="truncate" x-text="lang.native"></span>
                                    </div>
                                    <svg x-show="currentLang === lang.code" class="w-3.5 h-3.5 text-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                    </svg>
                                </button>
                            </template>
                        </div>
                    </div>

                    <button @click="mobileOpen = !mobileOpen"
                        class="flex items-center justify-center w-9 h-9 rounded-full text-ink-700 hover:bg-white/70 transition-colors focus:outline-none border border-white/60"
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
            </div>

            {{-- Mobile Dropdown Menu --}}
            <div x-show="mobileOpen" x-cloak x-collapse class="sm:hidden">
                {{-- Mobile Search --}}
                <div class="px-1 pt-3 pb-2 relative" @keydown.escape="open = false; query = ''; results = []">
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500 pointer-events-none z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <input type="text" x-model="query" @input.debounce.300ms="fetchInstant()"
                            @focus="if(query.length >= 2) open = true"
                            @keydown.enter.prevent="if(query) window.location.href=`/search?q=${encodeURIComponent(query)}`"
                            placeholder="Search medicines…"
                            data-i18n-placeholder="nav_search_mobile"
                            class="w-full pl-9 pr-4 py-2.5 text-sm rounded-2xl bg-white/85 hover:bg-white focus:bg-white border border-slate-300/90 hover:border-slate-400 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 placeholder:text-ink-400 transition-all shadow-[0_4px_14px_rgba(11,31,36,0.07)]"
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
                    <a href="{{ route('interactions.index') }}" @click="mobileOpen = false"
                        class="flex items-center gap-3 px-4 py-3 text-sm font-semibold text-ink-700 hover:text-primary hover:bg-white/60 rounded-xl transition-all">
                        <svg class="w-4 h-4 text-ink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        <span data-i18n="nav_interactions">Interactions</span>
                    </a>
                    <a href="{{ route('drugs.search') }}" @click="mobileOpen = false"
                        class="btn-gradient flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl">
                        <svg class="w-4 h-4 text-white/85" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                        </svg>
                        <span data-i18n="nav_browse">Browse Medicines</span>
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
            const threshold = 16;
            function update() {
                const isScrolled = wrapper.scrollTop > threshold;
                if (navShell.classList.contains('is-floating') !== isScrolled) {
                    navShell.classList.toggle('is-floating', isScrolled);
                }
                ticking = false;
            }
            wrapper.addEventListener('scroll', () => {
                if (ticking) return;
                ticking = true;
                requestAnimationFrame(update);
            }, { passive: true });
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

    {{-- ════════════════════════════════════════ MINIMALIST BARE iOS SPINNER PRELOADER ════════════════════════════════════════ --}}
    <div id="pharmasis-lang-preloader" class="hidden fixed inset-0 z-[99999] flex flex-col items-center justify-center gap-3 select-none"
        style="background: rgba(255,255,255,0.45); backdrop-filter: blur(20px) saturate(180%); -webkit-backdrop-filter: blur(20px) saturate(180%);">
        
        {{-- Compact Bare Minimalist Dark iOS spinner --}}
        <div class="ios-spinner" style="transform: scale(1.05); transform-origin: center;">
            <div></div><div></div><div></div><div></div>
            <div></div><div></div><div></div><div></div>
            <div></div><div></div><div></div><div></div>
        </div>
        {{-- Language switching text label --}}
        <p id="pharmasis-preloader-text" class="text-xs font-semibold text-ink-600 tracking-wide mt-1" style="opacity:0.7;">Changing language...</p>
    </div>

    @include('partials.cookie-consent')
    @stack('scripts')
</body>

</html>
