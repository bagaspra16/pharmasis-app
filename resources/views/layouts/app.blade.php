<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Pharmasis') — Know Your Medicine</title>
    <meta name="description"
        content="@yield('meta_description', 'Pharmasis provides clear, reliable, and easy-to-understand medicine safety information. Search 16,000+ drugs with AI-powered plain-language explanations.')">

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
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'system-ui', 'sans-serif'],
                        heading: ['Syne', 'sans-serif'],
                        brand: ['Syne', 'sans-serif'],
                        mothwing: ['Mothwing', 'serif'],
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

    {{-- Google Fonts: Plus Jakarta Sans (body) + Syne (brand/heading) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Syne:wght@700;800&display=swap"
        rel="stylesheet">

    <style>
        @font-face {
            font-family: 'Mothwing';
            src: url('{{ asset(' fonts/mothwing-demo.otf') }}') format('opentype');
            font-weight: normal;
            font-style: normal;
            font-display: swap;
        }

        [x-cloak] {
            display: none !important;
        }

        .gradient-hero {
            background: linear-gradient(135deg, #0d4f52 0%, #1a7a7d 40%, #3EAEB1 100%);
        }

        .glass {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        .card-hover {
            transition: all 0.25s ease;
        }

        .card-hover:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 32px rgba(62, 174, 177, 0.18);
        }

        .risk-minor {
            --risk-color: #10B981;
        }

        .risk-moderate {
            --risk-color: #F59E0B;
        }

        .risk-major {
            --risk-color: #EF4444;
        }

        .skeleton {
            background: linear-gradient(90deg, #e2e8f0 25%, #f1f5f9 50%, #e2e8f0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
        }

        @keyframes shimmer {
            0% {
                background-position: 200% 0;
            }

            100% {
                background-position: -200% 0;
            }
        }

        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: #9FD8E1;
            border-radius: 3px;
        }

        /* ── Marquee ── */
        @keyframes marquee {
            from {
                transform: translateX(0);
            }

            to {
                transform: translateX(calc(-100% - 1rem));
            }
        }

        @keyframes marquee-vertical {
            from {
                transform: translateY(0);
            }

            to {
                transform: translateY(calc(-100% - 1rem));
            }
        }

        .animate-marquee {
            animation: marquee 40s linear infinite;
        }

        .animate-marquee-fast {
            animation: marquee 18s linear infinite;
        }

        .animate-marquee-vertical {
            animation: marquee-vertical 40s linear infinite;
        }

        .group:hover .pause-on-hover {
            animation-play-state: paused;
        }

        /* ── Glass ── */
        .glass-card {
            background: rgba(255, 255, 255, 0.07);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }

        /* ── AI Markdown Styling ── */
        .ai-markdown {
            font-size: 0.9rem;
            line-height: 1.6;
        }

        .ai-markdown p {
            margin-bottom: 0.35rem;
        }

        .ai-markdown ul {
            list-style-type: disc;
            padding-left: 1.25rem;
            margin-bottom: 0.35rem;
        }

        .ai-markdown li {
            margin-bottom: 0.15rem;
        }

        .ai-markdown strong {
            font-weight: 600;
        }
    </style>

    @stack('head')
</head>

<body class="font-sans bg-slate-50 text-slate-800 antialiased">


    {{-- Navigation --}}
    <nav class="bg-white/80 backdrop-blur-md border-b border-slate-200/50 sticky top-0 z-50 shadow-sm transition-all duration-300"
        x-data="Object.assign(navSearch(), { mobileOpen: false })">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 gap-4">
                {{-- Logo --}}
                <a href="{{ route('home') }}" class="flex-shrink-0 flex items-center gap-2 group">
                    <img src="{{ asset('images/icon.png') }}" alt="Pharmasis Logo"
                        class="w-8 h-8 object-contain drop-shadow-[0_2px_4px_rgba(62,174,177,0.3)] group-hover:scale-105 transition-transform duration-300">
                    <span
                        class="text-2xl text-primary font-heading tracking-wide transform translate-y-0.5">Pharmasis</span>
                </a>

                {{-- Nav Search (hidden on mobile / home page, shown on md+) --}}
                <div class="flex-1 max-w-xl relative hidden md:block"
                    @keydown.escape="open = false; query = ''; results = []">
                    <div class="relative">
                        <input type="text" x-model="query" @input.debounce.300ms="fetchInstant()"
                            @focus="if(query.length >= 2) open = true" @keydown.arrow-down.prevent="focusNext()"
                            @keydown.arrow-up.prevent="focusPrev()" @keydown.enter.prevent="selectFocused()"
                            placeholder="Search medicines, generics, drug classes..."
                            class="w-full pl-10 pr-4 py-2.5 text-sm border-2 border-slate-100/80 rounded-full bg-slate-50/50 hover:bg-white focus:bg-white focus:outline-none focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all duration-300 shadow-sm"
                            autocomplete="off" />
                        <div x-show="loading" class="absolute right-3 top-2.5">
                            <svg class="animate-spin w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4" />
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                            </svg>
                        </div>
                    </div>
                    {{-- Dropdown --}}
                    <div x-show="open && results.length > 0" x-cloak @click.outside="open = false; results = []"
                        class="absolute top-full left-0 right-0 mt-1 bg-white rounded-xl shadow-xl border border-slate-100 overflow-hidden z-50 animate-slide-down">
                        <template x-for="(drug, idx) in results" :key="drug.id">
                            <a :href="drug.is_fda ? `/drugs/fda/${drug.slug}` : `/drugs/${drug.id}`"
                                :class="focusedIdx === idx ? 'bg-primary-light' : 'hover:bg-slate-50'"
                                class="flex items-center gap-3 px-4 py-3 transition-colors border-b border-slate-50 last:border-0">
                                <div
                                    class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                                    <span class="text-xs font-bold text-primary"
                                        x-text="drug.alpha_index || drug.name?.charAt(0) || '?'"></span>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-slate-800 truncate" x-text="drug.name"></p>
                                    <p class="text-xs text-slate-500 truncate"
                                        x-text="[drug.generic_name, drug.drug_class].filter(Boolean).join(' · ')"></p>
                                </div>
                            </a>
                        </template>
                        <a :href="`/search?q=${encodeURIComponent(query)}`"
                            class="flex items-center gap-2 px-4 py-2.5 text-sm text-primary font-medium hover:bg-primary-light transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            See all results for "<span x-text="query"></span>"
                        </a>
                    </div>
                    <div x-show="open && results.length === 0 && !loading && query.length >= 2" x-cloak
                        class="absolute top-full left-0 right-0 mt-1 bg-white rounded-xl shadow-xl border border-slate-100 px-4 py-6 text-center z-50">
                        <p class="text-sm text-slate-500">No medicine found for "<span class="font-medium"
                                x-text="query"></span>"</p>
                    </div>
                </div>

                {{-- Desktop Nav Links --}}
                <div class="hidden sm:flex items-center gap-1 sm:gap-2 text-sm font-semibold">
                    <a href="{{ route('home') }}"
                        class="px-3 sm:px-4 py-2 text-slate-600 hover:text-primary hover:bg-primary/5 rounded-full transition-all">Home</a>
                    <a href="{{ route('interactions.index') }}"
                        class="px-3 sm:px-4 py-2 text-slate-600 hover:text-primary hover:bg-primary/5 rounded-full transition-all">Interactions</a>
                    <a href="{{ route('drugs.search') }}"
                        class="px-4 sm:px-5 py-2 text-white bg-primary hover:bg-teal-600 shadow-sm shadow-primary/30 rounded-full transition-all">Browse</a>
                </div>

                {{-- Mobile: Hamburger Button --}}
                <button @click="mobileOpen = !mobileOpen"
                    class="sm:hidden flex items-center justify-center w-9 h-9 rounded-lg text-slate-600 hover:bg-slate-100 transition-colors focus:outline-none"
                    :aria-expanded="mobileOpen" aria-label="Toggle menu">
                    <svg x-show="!mobileOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                    <svg x-show="mobileOpen" x-cloak class="w-5 h-5" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Mobile Dropdown Menu --}}
        <div x-show="mobileOpen" x-cloak x-collapse class="sm:hidden border-t border-slate-100 bg-white">
            {{-- Mobile Search --}}
            <div class="px-4 pt-3 pb-2 relative" @keydown.escape="open = false; query = ''; results = []">
                <div class="relative">
                    <input type="text" x-model="query" @input.debounce.300ms="fetchInstant()"
                        @focus="if(query.length >= 2) open = true"
                        @keydown.enter.prevent="if(query) window.location.href=`/search?q=${encodeURIComponent(query)}`"
                        placeholder="Search medicines..."
                        class="w-full pl-9 pr-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                        autocomplete="off" />
                    <svg class="absolute left-3 top-2.5 w-4 h-4 text-slate-400 pointer-events-none" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                {{-- Mobile Search Dropdown --}}
                <div x-show="open && results.length > 0" x-cloak @click.outside="open = false; results = []"
                    class="absolute left-4 right-4 top-full bg-white rounded-xl shadow-xl border border-slate-100 overflow-hidden z-50 max-h-64 overflow-y-auto">
                    <template x-for="(drug, idx) in results" :key="drug.id">
                        <a :href="drug.is_fda ? `/drugs/fda/${drug.slug}` : `/drugs/${drug.id}`"
                            @click="mobileOpen = false"
                            class="flex items-center gap-3 px-4 py-3 hover:bg-slate-50 transition-colors border-b border-slate-50 last:border-0">
                            <div
                                class="w-7 h-7 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                                <span class="text-xs font-bold text-primary"
                                    x-text="drug.name?.charAt(0) || '?'"></span>
                            </div>
                            <p class="text-sm font-semibold text-slate-800 truncate" x-text="drug.name"></p>
                        </a>
                    </template>
                </div>
            </div>

            {{-- Mobile Nav Links --}}
            <div class="px-4 pb-4 space-y-1">
                <a href="{{ route('home') }}" @click="mobileOpen = false"
                    class="flex items-center gap-3 px-4 py-3 text-sm font-semibold text-slate-700 hover:text-primary hover:bg-primary/5 rounded-xl transition-all">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                        </path>
                    </svg>
                    Home
                </a>
                <a href="{{ route('interactions.index') }}" @click="mobileOpen = false"
                    class="flex items-center gap-3 px-4 py-3 text-sm font-semibold text-slate-700 hover:text-primary hover:bg-primary/5 rounded-xl transition-all">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                        </path>
                    </svg>
                    Interactions
                </a>
                <a href="{{ route('drugs.search') }}" @click="mobileOpen = false"
                    class="flex items-center gap-3 px-4 py-3 text-sm font-semibold text-white bg-primary hover:bg-teal-600 rounded-xl transition-all shadow-sm shadow-primary/20">
                    <svg class="w-4 h-4 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                    </svg>
                    Browse Medicines
                </a>
            </div>
        </div>
    </nav>


    {{-- Page Content --}}
    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-slate-900 text-slate-300 mt-16">
        <div class="max-w-7xl mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <img src="{{ asset('images/icon.png') }}" alt="Pharmasis Logo"
                            class="w-8 h-8 object-contain opacity-80 backdrop-grayscale">
                        <span class="text-2xl font-heading text-slate-100 tracking-wide">Pharmasis</span>
                    </div>
                    <p class="text-sm text-slate-400 leading-relaxed">Know your medicine. Accurate, easy-to-understand
                        medicine information — when you need it.</p>
                </div>
                <div>
                    <h3 class="font-semibold text-white mb-3">Quick Links</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('home') }}" class="hover:text-primary transition-colors">Home</a></li>
                        <li><a href="{{ route('interactions.index') }}"
                                class="hover:text-primary transition-colors">Check Interactions</a></li>
                        <li><a href="{{ route('drugs.search') }}" class="hover:text-primary transition-colors">Browse
                                Medicines</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-semibold text-white mb-3">Medical Disclaimer</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">This platform provides educational information
                        only and does not replace professional medical advice. Always consult a licensed healthcare
                        professional before making medical decisions.</p>
                </div>
            </div>
            <div class="border-t border-slate-700 pt-6 flex flex-col sm:flex-row items-center justify-between gap-2">
                <p class="text-xs text-slate-500">© {{ date('Y') }} Pharmasis. For educational purposes only.</p>
                <p class="text-xs text-slate-500">Data sourced from WebMD. Not for medical diagnosis or treatment.</p>
            </div>
        </div>
    </footer>

    <script>
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

    @stack('scripts')
</body>

</html>