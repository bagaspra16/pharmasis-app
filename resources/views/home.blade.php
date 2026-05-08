@extends('layouts.app')

@section('title', 'Pharmasis — Know Your Medicine')
@section('meta_description', (!empty($dbOffline) || !empty($fdaMode))
    ? 'Search 67,000+ medicines via OpenFDA. Find drug information, warnings, dosage, and side effects in plain language. Powered by AI for easy understanding.'
    : 'Search 16,000+ medicines. Find drug information, warnings, dosage, and side effects in plain language. Powered by AI for easy understanding.')

@push('head')
<style>
    /* Hero composite display type — Instrument Serif italic accent + Geist tracking */
    .display-italic {
        font-family: 'Instrument Serif', serif;
        font-style: italic;
        font-weight: 400;
        letter-spacing: -0.01em;
    }
    .display-roman {
        font-family: 'Instrument Serif', serif;
        font-weight: 400;
        letter-spacing: -0.015em;
    }

    /* Drug card lift */
    .drug-card {
        transition: transform 320ms cubic-bezier(.22,1,.36,1), box-shadow 320ms ease;
    }
    .drug-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 18px 44px rgba(11,31,36,0.10);
    }

    /* Alpha pill */
    .alpha-pill {
        transition: background 220ms ease, color 220ms ease, transform 220ms ease, border-color 220ms ease;
    }
    .alpha-pill:hover {
        transform: scale(1.08);
    }

    /* Performance: defer rendering of off-screen sections */
    .cv-auto {
        content-visibility: auto;
        contain-intrinsic-size: 1px 480px;
    }

    @media (prefers-reduced-motion: reduce) {
        .drug-card, .alpha-pill { transition: none; }
        .drug-card:hover, .alpha-pill:hover { transform: none; }
    }
</style>
@endpush

@section('content')

{{-- ── DB Offline Banner ── --}}
@if(!empty($dbOffline))
<div class="border-b border-amber-200/60" style="background: rgba(254,243,199,0.65); backdrop-filter: blur(10px);"
    x-data="{ show: true }" x-init="setTimeout(() => show = false, 8000)" x-show="show" x-transition.opacity>
    <div class="max-w-7xl mx-auto px-4 py-2.5 flex items-center gap-3">
        <svg class="w-4 h-4 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7" />
        </svg>
        <p class="text-xs text-amber-800 flex-1">
            <strong>Using OpenFDA data source.</strong> Our local database is temporarily unavailable, so results are being loaded from OpenFDA.
        </p>
        <button onclick="window.location.reload()"
            class="text-xs text-amber-700 border border-amber-200 px-2.5 py-1 rounded-lg hover:bg-amber-100 transition-colors">Retry</button>
        <button @click="show=false" class="text-amber-400 hover:text-amber-700 ml-1">✕</button>
    </div>
</div>
@endif

@include('partials.hero-medicheck')

{{-- ── Local Storage AI History ── --}}
@include('partials.medicheck-history')

{{-- ════════════════════════════════════════ FEATURED ════════════════════════════════════════ --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 cv-auto">

    {{-- Section header --}}
    <div class="flex items-end justify-between mb-10 flex-wrap gap-4">
        <div>
            <div class="flex items-center gap-2 mb-3">
                <span class="w-1 h-4 rounded-full" style="background: linear-gradient(to bottom,#3EAEB1,#61BACA);"></span>
                <p class="text-[11px] font-semibold text-primary uppercase tracking-[0.18em]">Database</p>
            </div>
            <h2 class="display-roman text-3xl md:text-4xl text-ink-900 leading-tight">
                {{ !empty($fdaMode) ? 'Popular medicines' : 'Popular medicines' }}
                <span class="display-italic text-primary">today</span>
            </h2>
            <p class="text-sm text-ink-500 mt-2 max-w-md">
                {{ !empty($fdaMode) ? 'Curated from the live OpenFDA registry — open and trusted by clinicians.' : 'A small slice of our verified library — searched the most this week.' }}
            </p>
        </div>
        <a href="{{ route('drugs.search') }}" class="glass-soft rounded-full pl-5 pr-4 py-2.5 text-sm font-medium text-primary-dark hover:text-primary group inline-flex items-center gap-2 transition-colors">
            Browse all
            <svg class="w-4 h-4 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </a>
    </div>

    {{-- Cards grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        @foreach($featured as $drug)
        @php
        $isDto = $drug instanceof \App\DTOs\DrugDTO;
        $uses = $isDto ? $drug->uses : \App\Models\Drug::cleanField($drug->uses);
        $preview = $uses ? Str::limit($uses, 85) : 'Tap to learn more about this medicine.';
        $href = $isDto ? route('drugs.show_fda', $drug->slug) : route('drugs.show', $drug->id);
        $initials = strtoupper(substr($drug->name ?? '?', 0, 2));
        @endphp
        <a href="{{ $href }}" class="drug-card glass-card glass-hover group block rounded-2xl p-5">
            <div class="flex items-start justify-between mb-4">
                <div class="w-11 h-11 rounded-2xl flex items-center justify-center flex-shrink-0"
                    style="background: linear-gradient(135deg,rgba(62,174,177,0.18),rgba(97,186,202,0.30));">
                    <span class="font-semibold text-primary-dark text-sm leading-none tracking-tight">{{ $initials }}</span>
                </div>
                @if($drug->drug_class)
                <span class="text-[10px] font-medium px-2.5 py-1 rounded-full max-w-[120px] truncate"
                    style="background:rgba(62,174,177,0.08); color:#2d8a8d; border:1px solid rgba(62,174,177,0.15);">{{ Str::limit($drug->drug_class, 18) }}</span>
                @endif
            </div>
            <h3 class="font-medium text-ink-900 text-sm leading-snug mb-1 group-hover:text-primary transition-colors line-clamp-1 tracking-tight">
                {{ $drug->name }}</h3>
            @if($drug->generic_name)
            <p class="display-italic text-xs text-ink-400 mb-2.5 truncate">{{ $drug->generic_name }}</p>
            @endif
            <p class="text-xs text-ink-500 leading-relaxed line-clamp-3">{{ $preview }}</p>
            <div class="flex items-center justify-between mt-4 pt-3" style="border-top:1px solid rgba(62,174,177,0.10);">
                @if(!empty($drug->is_fda))
                <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full" style="background:rgba(59,130,246,0.10);color:#3b82f6;">FDA</span>
                @elseif(!empty($drug->translated))
                <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full" style="background:rgba(16,185,129,0.10);color:#059669;">✓ Verified</span>
                @else
                <span class="text-xs text-ink-300">—</span>
                @endif
                <span class="text-xs font-medium text-primary group-hover:translate-x-0.5 transition-transform inline-block">Details →</span>
            </div>
        </a>
        @endforeach
    </div>

</section>

{{-- ════════════════════════════════════════ WHY PHARMASIS ════════════════════════════════════════ --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 cv-auto">
    <div class="text-center mb-14">
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-[11px] font-semibold mb-5 glass-soft text-primary-dark uppercase tracking-[0.18em]">
            Why Pharmasis
        </div>
        <h2 class="display-roman text-3xl md:text-4xl lg:text-5xl text-ink-900 leading-tight max-w-3xl mx-auto">
            Everything you need to know
            <span class="display-italic text-primary">about your medicine.</span>
        </h2>
        <p class="text-sm md:text-base text-ink-500 mt-4 max-w-xl mx-auto leading-relaxed">
            Trusted by thousands. Powered by AI. Built for clarity.
        </p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach([
        ['icon'=>'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z','title'=>'Smart Search','desc'=>(!empty($dbOffline) || !empty($fdaMode))
            ? 'Search across 67,000+ medicines via OpenFDA by trade name, generic, or drug class — in seconds.'
            : 'Search across 16,000+ medicines by trade name, generic, or drug class — in seconds.','accent'=>'#3EAEB1'],
        ['icon'=>'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z','title'=>'AI Plain Language','desc'=>'Complex medical text simplified by AI into everyday language you can actually understand.','accent'=>'#6366f1'],
        ['icon'=>'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z','title'=>'Safety First','desc'=>'Warning, drug interactions, and before-taking checklists, so you can make informed decisions.','accent'=>'#059669'],
        ] as $f)
        <div class="drug-card glass-card glass-hover group rounded-2xl p-7 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-28 h-28 rounded-full opacity-[0.10] pointer-events-none"
                style="background: radial-gradient(circle, {{ $f['accent'] }}, transparent 70%); transform: translate(30%,-30%);"></div>
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center mb-5 transition-transform group-hover:scale-105 duration-500"
                style="background: linear-gradient(135deg, {{ $f['accent'] }}22, {{ $f['accent'] }}38); border:1px solid {{ $f['accent'] }}25;">
                <svg class="w-5 h-5" style="color:{{ $f['accent'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $f['icon'] }}" />
                </svg>
            </div>
            <h3 class="display-roman text-ink-900 mb-2 text-2xl leading-tight">{{ $f['title'] }}</h3>
            <p class="text-sm text-ink-500 leading-relaxed">{{ $f['desc'] }}</p>
            <div class="mt-6 pt-4" style="border-top:1px solid rgba(62,174,177,0.10);">
            </div>
        </div>
        @endforeach
    </div>
     <div class="max-w-7xl mx-auto mt-5">

        {{-- Bento grid: left large (dark) + right column --}}
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">

            {{-- ── LEFT: Dark pipeline card (spans 3 cols) ── --}}
            <div class="lg:col-span-3 rounded-3xl p-8 relative overflow-hidden flex flex-col justify-between min-h-[420px]"
                style="background:linear-gradient(145deg,#061e20 0%,#0d4f52 55%,#1a7a7d 100%); border:1px solid">
                {{-- Background glow orb --}}
                <div class="absolute -top-20 -right-20 w-64 h-64 rounded-full pointer-events-none"
                    style="background:radial-gradient(circle,rgba(62,174,177,0.25),transparent 70%);"></div>
                <div class="absolute bottom-0 left-0 w-48 h-48 rounded-full pointer-events-none"
                    style="background:radial-gradient(circle,rgba(97,186,202,0.15),transparent 70%); transform:translate(-30%,30%);"></div>

                {{-- Header --}}
                <div class="relative mb-8">
                    <p class="text-teal-300/60 text-[11px] font-bold uppercase tracking-widest mb-2">8 clinical steps · ~20s</p>
                    <h2 class="text-white text-2xl md:text-3xl font-heading leading-snug">
                        From symptoms<br>to a full clinical report.
                    </h2>
                </div>

                {{-- Pipeline steps list --}}
                <div class="relative space-y-0">
                    {{-- Vertical line --}}
                    <div class="absolute left-[15px] top-2 bottom-2 w-px" style="background:linear-gradient(to bottom,rgba(62,174,177,0.5),rgba(62,174,177,0.05));"></div>

                    @foreach([
                        ['01','Parsing symptom context & severity markers','Tokenizing · NLP entity extraction · urgency scoring'],
                        ['02','Clinical pattern recognition','Differential diagnosis · ICD-10 mapping · confidence scoring'],
                        ['03','Drug matching & dosage calculation','Formulary cross-reference · OTC vs Rx · weight-adjusted dosage'],
                        ['04','Drug interaction safety check','Cytochrome P450 scan · contraindication matrix · substitutes'],
                        ['05','Safety guard validation','Hallucination filtering · clinical plausibility · disclaimer injection'],
                        ['06','Recovery plan & lifestyle report','Timeline estimation · lifestyle optimisation · emergency flags'],
                        ['07','Locating nearby healthcare providers','IP geo-location · facility mapping · contact extraction'],
                        ['08','Analysis complete — rendering results','Structured JSON validated · auto-swap applied · report ready'],
                    ] as [$n, $label, $detail])
                    <div class="flex items-start gap-4 py-2.5 group">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 z-10 transition-all duration-200 group-hover:scale-110"
                            style="background:rgba(62,174,177,0.18); border:1px solid rgba(62,174,177,0.30);">
                            <span class="text-[10px] font-black text-teal-300">{{ $n }}</span>
                        </div>
                        <div class="pt-1 min-w-0">
                            <p class="text-sm font-semibold text-white/90 leading-tight truncate">{{ $label }}</p>
                            <p class="text-[10px] text-teal-300/50 mt-0.5 truncate">{{ $detail }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- ── RIGHT: Two stacked mini cards ── --}}
            <div class="lg:col-span-2 flex flex-col gap-4">

                {{-- Mini card 1: Input modes --}}
                <div class="rounded-3xl p-7 flex-1 relative overflow-hidden"
                    style="background:rgba(255,255,255,0.88); backdrop-filter:blur(24px); border:1px solid rgba(255,255,255,0.96); box-shadow:0 4px 24px rgba(62,174,177,0.10), 0 1px 0 rgba(255,255,255,0.9) inset;">
                    <div class="absolute top-0 right-0 w-32 h-32 pointer-events-none"
                        style="background:radial-gradient(circle at top right,rgba(62,174,177,0.12),transparent 70%);"></div>
                    <div class="w-11 h-11 rounded-2xl flex items-center justify-center mb-5"
                        style="background:linear-gradient(135deg,rgba(62,174,177,0.15),rgba(97,186,202,0.25)); border:1px solid rgba(62,174,177,0.15);">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 1a3 3 0 00-3 3v8a3 3 0 006 0V4a3 3 0 00-3-3z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 10v2a7 7 0 01-14 0v-2"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 mb-1.5">Voice & Text Input</h3>
                    <p class="text-sm text-slate-500 leading-relaxed mb-4">Describe symptoms by speaking or typing — in English or Indonesian. Powered by OpenAI Whisper for multilingual transcription.</p>
                    <div class="flex gap-2 flex-wrap">
                        <span class="inline-flex items-center gap-1 text-[10px] font-semibold px-2.5 py-1 rounded-full" style="background:rgba(62,174,177,0.10);color:#2d8a8d;">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>Voice
                        </span>
                        <span class="text-[10px] font-semibold px-2.5 py-1 rounded-full" style="background:rgba(62,174,177,0.10);color:#2d8a8d;">Text</span>
                        <span class="text-[10px] font-semibold px-2.5 py-1 rounded-full" style="background:rgba(62,174,177,0.10);color:#2d8a8d;">EN / ID</span>
                        <span class="text-[10px] font-semibold px-2.5 py-1 rounded-full" style="background:rgba(62,174,177,0.10);color:#2d8a8d;">Whisper AI</span>
                    </div>
                </div>

                {{-- Mini card 2: What you get --}}
                <div class="rounded-3xl p-7 flex-1 relative overflow-hidden"
                    style="background:rgba(255,255,255,0.88); backdrop-filter:blur(24px); border:1px solid rgba(255,255,255,0.96); box-shadow:0 4px 24px rgba(62,174,177,0.10), 0 1px 0 rgba(255,255,255,0.9) inset;">
                    <div class="absolute top-0 right-0 w-32 h-32 pointer-events-none"
                        style="background:radial-gradient(circle at top right,rgba(5,150,105,0.08),transparent 70%);"></div>
                    <div class="w-11 h-11 rounded-2xl flex items-center justify-center mb-5"
                        style="background:linear-gradient(135deg,rgba(5,150,105,0.12),rgba(5,150,105,0.22)); border:1px solid rgba(5,150,105,0.15);">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 mb-1.5">What You Receive</h3>
                    <ul class="space-y-1.5 text-sm text-slate-500">
                        <li class="flex items-center gap-2"><span class="w-1 h-1 rounded-full bg-emerald-400 flex-shrink-0"></span>Differential diagnosis with likelihood scores</li>
                        <li class="flex items-center gap-2"><span class="w-1 h-1 rounded-full bg-emerald-400 flex-shrink-0"></span>Drug recommendations & dosage guide</li>
                        <li class="flex items-center gap-2"><span class="w-1 h-1 rounded-full bg-emerald-400 flex-shrink-0"></span>Drug interaction & P450 safety check</li>
                        <li class="flex items-center gap-2"><span class="w-1 h-1 rounded-full bg-emerald-400 flex-shrink-0"></span>Recovery plan & lifestyle tips</li>
                        <li class="flex items-center gap-2"><span class="w-1 h-1 rounded-full bg-emerald-400 flex-shrink-0"></span>Nearby healthcare providers</li>
                    </ul>
                </div>

            </div>
        </div>

        {{-- Disclaimer --}}
        <p class="text-center text-[11px] text-slate-400 mt-6">
            <svg class="w-3 h-3 inline mr-1 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
            For educational purposes only. Always consult a licensed healthcare professional before taking any medication.
        </p>

    </div>
</section>

{{-- ════════════════════════════════════════ CTA STRIP ════════════════════════════════════════ --}}
<section class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-6 py-15 cv-auto">
    <div class="relative overflow-hidden rounded-3xl px-8 py-14 md:px-14 md:py-16 text-center"
        style="background: linear-gradient(135deg, rgba(13,79,82,0.96) 0%, rgba(26,122,125,0.94) 50%, rgba(62,174,177,0.92) 100%);">
        {{-- Soft floating orbs --}}
        <div class="absolute -top-20 -right-20 w-72 h-72 rounded-full opacity-30 pointer-events-none"
            style="background: radial-gradient(circle, rgba(159,216,225,0.6), transparent 70%);"></div>
        <div class="absolute -bottom-24 -left-16 w-80 h-80 rounded-full opacity-25 pointer-events-none"
            style="background: radial-gradient(circle, rgba(97,186,202,0.6), transparent 70%);"></div>

        <div class="relative max-w-2xl mx-auto">
            <p class="display-italic text-teal-200/80 text-lg mb-3">— start with a question</p>
            <h2 class="display-roman text-white text-3xl md:text-5xl leading-tight mb-5">
                Know your <span class="display-italic">medicine</span>
                before you take it.
            </h2>
            <p class="text-sm md:text-base text-white/70 leading-relaxed mb-8 max-w-lg mx-auto">
                Search a drug, scan an interaction, or describe how you feel — Pharmasis turns medical jargon into clear answers.
            </p>
            <div class="flex flex-wrap items-center justify-center gap-3">
                <a href="{{ route('drugs.search') }}"
                    class="inline-flex items-center gap-2 px-6 py-3 rounded-full text-sm font-semibold transition-all"
                    style="background: rgba(255,255,255,0.96); color:#0d4f52; box-shadow: 0 6px 24px rgba(0,0,0,0.15);"
                    onmouseover="this.style.transform='translateY(-1px)';this.style.boxShadow='0 10px 30px rgba(0,0,0,0.22)';"
                    onmouseout="this.style.transform='';this.style.boxShadow='0 6px 24px rgba(0,0,0,0.15)';">
                    Browse Medicines
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </a>
                <a href="{{ route('interactions.index') }}"
                    class="inline-flex items-center gap-2 px-6 py-3 rounded-full text-sm font-semibold text-white transition-all"
                    style="background: rgba(255,255,255,0.10); border:1px solid rgba(255,255,255,0.25); backdrop-filter: blur(10px);"
                    onmouseover="this.style.background='rgba(255,255,255,0.18)';"
                    onmouseout="this.style.background='rgba(255,255,255,0.10)';">
                    Check Interactions
                </a>
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
    /* ═══════════════════════════════════════════════════════════
       WebGL Gradient Wave — ported from designali-in/gradient-wave
       ═══════════════════════════════════════════════════════════ */
    (function () {
        function normalizeColor(hex) { return [((hex >> 16) & 255) / 255, ((hex >> 8) & 255) / 255, ((255) & hex) / 255]; }

        class MiniGl {
            constructor(canvas) {
                this.canvas = canvas; const gl = canvas.getContext('webgl', { antialias: true }); if (!gl) return; this.gl = gl; this.meshes = [];
                const ctx = this.gl, self = this;
                this.Uniform = class {
                    constructor(e) { Object.assign(this, e); const m = { float: '1f', int: '1i', vec2: '2fv', vec3: '3fv', vec4: '4fv', mat4: 'Matrix4fv' }; this.typeFn = m[this.type] || '1f'; }
                    update(loc) { if (this.value === undefined || loc === null) return; const isM = this.typeFn.indexOf('Matrix') === 0; const fn = 'uniform' + this.typeFn; if (isM) ctx[fn](loc, this.transpose || false, this.value); else ctx[fn](loc, this.value); }
                    getDeclaration(name, type, len) { if (this.excludeFrom === type) return ''; if (this.type === 'array') return this.value[0].getDeclaration(name, type, this.value.length) + `\nconst int ${name}_length = ${this.value.length};`; if (this.type === 'struct') { let n = name.replace('u_', ''); n = n.charAt(0).toUpperCase() + n.slice(1); const f = Object.entries(this.value).map(([k, v]) => v.getDeclaration(k, type).replace(/^uniform/, '')).join(''); return `uniform struct ${n}\n{\n${f}\n} ${name}${len ? `[${len}]` : ''};`; } return `uniform ${this.type} ${name}${len ? `[${len}]` : ''};`; }
                };
                this.Attribute = class {
                    constructor(e) { this.buffer = ctx.createBuffer(); Object.assign(this, e); } update() { if (this.values) { ctx.bindBuffer(this.target, this.buffer); ctx.bufferData(this.target, this.values, ctx.STATIC_DRAW); } }
                    attach(e, prog) { const n = ctx.getAttribLocation(prog, e); if (this.target === ctx.ARRAY_BUFFER) { ctx.bindBuffer(this.target, this.buffer); ctx.enableVertexAttribArray(n); ctx.vertexAttribPointer(n, this.size, this.type || ctx.FLOAT, this.normalized || false, 0, 0); } return n; }
                    use(n) { ctx.bindBuffer(this.target, this.buffer); if (this.target === ctx.ARRAY_BUFFER) { ctx.enableVertexAttribArray(n); ctx.vertexAttribPointer(n, this.size, this.type || ctx.FLOAT, this.normalized || false, 0, 0); } }
                };
                this.Material = class {
                    constructor(vert, frag, uniforms = {}) {
                        const mat = this; function getShader(type, src) { const s = ctx.createShader(type); ctx.shaderSource(s, src); ctx.compileShader(s); return s; }
                        function decl(u, type) { return Object.entries(u).map(([k, v]) => v.getDeclaration(k, type)).join('\n'); }
                        mat.uniforms = uniforms; mat.uniformInstances = [];
                        const pre = 'precision highp float;';
                        const vs = `${pre}\nattribute vec4 position;\nattribute vec2 uv;\nattribute vec2 uvNorm;\n${decl(self.commonUniforms, 'vertex')}\n${decl(uniforms, 'vertex')}\n${vert}`;
                        const fs = `${pre}\n${decl(self.commonUniforms, 'fragment')}\n${decl(uniforms, 'fragment')}\n${frag}`;
                        mat.program = ctx.createProgram(); ctx.attachShader(mat.program, getShader(ctx.VERTEX_SHADER, vs)); ctx.attachShader(mat.program, getShader(ctx.FRAGMENT_SHADER, fs)); ctx.linkProgram(mat.program);
                        ctx.useProgram(mat.program); mat.attachUniforms(undefined, self.commonUniforms); mat.attachUniforms(undefined, mat.uniforms);
                    }
                    attachUniforms(name, uniforms) { if (name === undefined) { Object.entries(uniforms).forEach(([n, u]) => this.attachUniforms(n, u)); } else if (uniforms.type === 'array') { uniforms.value.forEach((u, i) => this.attachUniforms(`${name}[${i}]`, u)); } else if (uniforms.type === 'struct') { Object.entries(uniforms.value).forEach(([u, i]) => this.attachUniforms(`${name}.${u}`, i)); } else { this.uniformInstances.push({ uniform: uniforms, location: ctx.getUniformLocation(this.program, name) }); } }
                };
                this.PlaneGeometry = class {
                    constructor() { this.attributes = { position: new self.Attribute({ target: ctx.ARRAY_BUFFER, size: 3 }), uv: new self.Attribute({ target: ctx.ARRAY_BUFFER, size: 2 }), uvNorm: new self.Attribute({ target: ctx.ARRAY_BUFFER, size: 2 }), index: new self.Attribute({ target: ctx.ELEMENT_ARRAY_BUFFER, size: 3, type: ctx.UNSIGNED_SHORT }) }; }
                    setTopology(xs = 1, ys = 1) { this.xSegCount = xs; this.ySegCount = ys; this.vertexCount = (xs + 1) * (ys + 1); const qc = xs * ys * 2; this.attributes.uv.values = new Float32Array(2 * this.vertexCount); this.attributes.uvNorm.values = new Float32Array(2 * this.vertexCount); this.attributes.index.values = new Uint16Array(3 * qc); for (let y = 0; y <= ys; y++)for (let x = 0; x <= xs; x++) { const i = y * (xs + 1) + x; this.attributes.uv.values[2 * i] = x / xs; this.attributes.uv.values[2 * i + 1] = 1 - y / ys; this.attributes.uvNorm.values[2 * i] = (x / xs) * 2 - 1; this.attributes.uvNorm.values[2 * i + 1] = 1 - (y / ys) * 2; if (x < xs && y < ys) { const s = y * xs + x; this.attributes.index.values[6 * s] = i; this.attributes.index.values[6 * s + 1] = i + 1 + xs; this.attributes.index.values[6 * s + 2] = i + 1; this.attributes.index.values[6 * s + 3] = i + 1; this.attributes.index.values[6 * s + 4] = i + 1 + xs; this.attributes.index.values[6 * s + 5] = i + 2 + xs; } } this.attributes.uv.update(); this.attributes.uvNorm.update(); this.attributes.index.update(); }
                    setSize(w = 1, h = 1) { this.width = w; this.height = h; this.attributes.position.values = new Float32Array(3 * this.vertexCount); const ox = w / -2, oy = h / -2, sw = w / this.xSegCount, sh = h / this.ySegCount; for (let y = 0; y <= this.ySegCount; y++)for (let x = 0; x <= this.xSegCount; x++) { const idx = y * (this.xSegCount + 1) + x; this.attributes.position.values[3 * idx] = ox + x * sw; this.attributes.position.values[3 * idx + 1] = -(oy + y * sh); this.attributes.position.values[3 * idx + 2] = 0; } this.attributes.position.update(); }
                };
                this.Mesh = class {
                    constructor(geo, mat) { this.geometry = geo; this.material = mat; this.attributeInstances = []; Object.entries(this.geometry.attributes).forEach(([e, a]) => this.attributeInstances.push({ attribute: a, location: a.attach(e, this.material.program) })); self.meshes.push(this); }
                    draw() { ctx.useProgram(this.material.program); this.material.uniformInstances.forEach(({ uniform, location }) => uniform.update(location)); this.attributeInstances.forEach(({ attribute, location }) => attribute.use(location)); ctx.drawElements(ctx.TRIANGLES, this.geometry.attributes.index.values.length, ctx.UNSIGNED_SHORT, 0); }
                };
                const I = [1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1];
                this.commonUniforms = { projectionMatrix: new this.Uniform({ type: 'mat4', value: I }), modelViewMatrix: new this.Uniform({ type: 'mat4', value: I }), resolution: new this.Uniform({ type: 'vec2', value: [1, 1] }), aspectRatio: new this.Uniform({ type: 'float', value: 1 }) };
            }
            setSize(w = 640, h = 480) { this.width = w; this.height = h; this.canvas.width = w; this.canvas.height = h; this.gl.viewport(0, 0, w, h); this.commonUniforms.resolution.value = [w, h]; this.commonUniforms.aspectRatio.value = w / h; }
            setOrthographicCamera() { this.commonUniforms.projectionMatrix.value = [2 / this.width, 0, 0, 0, 0, 2 / this.height, 0, 0, 0, 0, -0.001, 0, 0, 0, 0, 1]; }
            render() { this.gl.clearColor(0, 0, 0, 0); this.gl.clearDepth(1); this.meshes.forEach(m => m.draw()); }
        }

        class Gradient {
            constructor(canvas, colors) { this.canvas = canvas; this.colors = colors; this.time = 0; this.last = 0; this.isPlaying = false; this.minigl = new MiniGl(canvas); this.init(); }
            init() {
                const sc = this.colors.map(hex => normalizeColor(parseInt(hex.replace('#', '0x'), 16)));
                const uniforms = {
                    u_time: new this.minigl.Uniform({ value: 0 }),
                    u_shadow_power: new this.minigl.Uniform({ value: 6 }),
                    u_darken_top: new this.minigl.Uniform({ value: 0 }),
                    u_active_colors: new this.minigl.Uniform({ value: [1, 1, 1, 1], type: 'vec4' }),
                    u_global: new this.minigl.Uniform({ value: { noiseFreq: new this.minigl.Uniform({ value: [0.00014, 0.00029], type: 'vec2' }), noiseSpeed: new this.minigl.Uniform({ value: 0.000005 }) }, type: 'struct' }),
                    u_vertDeform: new this.minigl.Uniform({ value: { incline: new this.minigl.Uniform({ value: 0.5 }), offsetTop: new this.minigl.Uniform({ value: -0.5 }), offsetBottom: new this.minigl.Uniform({ value: -0.5 }), noiseFreq: new this.minigl.Uniform({ value: [3, 4], type: 'vec2' }), noiseAmp: new this.minigl.Uniform({ value: 280 }), noiseSpeed: new this.minigl.Uniform({ value: 10 }), noiseFlow: new this.minigl.Uniform({ value: 5 }), noiseSeed: new this.minigl.Uniform({ value: 5 }) }, type: 'struct', excludeFrom: 'fragment' }),
                    u_baseColor: new this.minigl.Uniform({ value: sc[0], type: 'vec3', excludeFrom: 'fragment' }),
                    u_waveLayers: new this.minigl.Uniform({ value: [], excludeFrom: 'fragment', type: 'array' }),
                };
                for (let i = 1; i < sc.length; i++) { uniforms.u_waveLayers.value.push(new this.minigl.Uniform({ value: { color: new this.minigl.Uniform({ value: sc[i], type: 'vec3' }), noiseFreq: new this.minigl.Uniform({ value: [2 + i / sc.length, 3 + i / sc.length], type: 'vec2' }), noiseSpeed: new this.minigl.Uniform({ value: 11 + 0.3 * i }), noiseFlow: new this.minigl.Uniform({ value: 6.5 + 0.3 * i }), noiseSeed: new this.minigl.Uniform({ value: 5 + 10 * i }), noiseFloor: new this.minigl.Uniform({ value: 0.1 }), noiseCeil: new this.minigl.Uniform({ value: 0.63 + 0.07 * i }) }, type: 'struct' })); }

                const vert = `
vec3 mod289(vec3 x){return x-floor(x*(1./289.))*289.;}
vec4 mod289(vec4 x){return x-floor(x*(1./289.))*289.;}
vec4 permute(vec4 x){return mod289(((x*34.)+1.)*x);}
vec4 taylorInvSqrt(vec4 r){return 1.79284291400159-0.85373472095314*r;}
float snoise(vec3 v){
  const vec2 C=vec2(1./6.,1./3.);const vec4 D=vec4(0.,.5,1.,2.);
  vec3 i=floor(v+dot(v,C.yyy));vec3 x0=v-i+dot(i,C.xxx);
  vec3 g=step(x0.yzx,x0.xyz);vec3 l=1.-g;
  vec3 i1=min(g.xyz,l.zxy);vec3 i2=max(g.xyz,l.zxy);
  vec3 x1=x0-i1+C.xxx;vec3 x2=x0-i2+C.yyy;vec3 x3=x0-D.yyy;
  i=mod289(i);
  vec4 p=permute(permute(permute(i.z+vec4(0.,i1.z,i2.z,1.))+i.y+vec4(0.,i1.y,i2.y,1.))+i.x+vec4(0.,i1.x,i2.x,1.));
  float n_=0.142857142857;vec3 ns=n_*D.wyz-D.xzx;
  vec4 j=p-49.*floor(p*ns.z*ns.z);vec4 x_=floor(j*ns.z);vec4 y_=floor(j-7.*x_);
  vec4 x=x_*ns.x+ns.yyyy;vec4 y=y_*ns.x+ns.yyyy;vec4 h=1.-abs(x)-abs(y);
  vec4 b0=vec4(x.xy,y.xy);vec4 b1=vec4(x.zw,y.zw);
  vec4 s0=floor(b0)*2.+1.;vec4 s1=floor(b1)*2.+1.;vec4 sh=-step(h,vec4(0.));
  vec4 a0=b0.xzyw+s0.xzyw*sh.xxyy;vec4 a1=b1.xzyw+s1.xzyw*sh.zzww;
  vec3 p0=vec3(a0.xy,h.x);vec3 p1=vec3(a0.zw,h.y);vec3 p2=vec3(a1.xy,h.z);vec3 p3=vec3(a1.zw,h.w);
  vec4 norm=taylorInvSqrt(vec4(dot(p0,p0),dot(p1,p1),dot(p2,p2),dot(p3,p3)));
  p0*=norm.x;p1*=norm.y;p2*=norm.z;p3*=norm.w;
  vec4 m=max(0.6-vec4(dot(x0,x0),dot(x1,x1),dot(x2,x2),dot(x3,x3)),0.);m=m*m;
  return 42.*dot(m*m,vec4(dot(p0,x0),dot(p1,x1),dot(p2,x2),dot(p3,x3)));
}
vec3 blendNormal(vec3 base,vec3 blend,float op){return blend*op+base*(1.-op);}
varying vec3 v_color;
void main(){
  float time=u_time*u_global.noiseSpeed;
  vec2 noiseCoord=resolution*uvNorm*u_global.noiseFreq;
  float tilt=resolution.y/2.*uvNorm.y;
  float incline=resolution.x*uvNorm.x/2.*u_vertDeform.incline;
  float offset=resolution.x/2.*u_vertDeform.incline*mix(u_vertDeform.offsetBottom,u_vertDeform.offsetTop,uv.y);
  float noise=snoise(vec3(noiseCoord.x*u_vertDeform.noiseFreq.x+time*u_vertDeform.noiseFlow,noiseCoord.y*u_vertDeform.noiseFreq.y,time*u_vertDeform.noiseSpeed+u_vertDeform.noiseSeed))*u_vertDeform.noiseAmp;
  noise*=1.-pow(abs(uvNorm.y),2.);noise=max(0.,noise);
  vec3 pos=vec3(position.x,position.y+tilt+incline+noise-offset,position.z);
  v_color=u_baseColor;
  for(int i=0;i<u_waveLayers_length;i++){
    if(u_active_colors[i+1]==1.){
      WaveLayers layer=u_waveLayers[i];
      float ln=smoothstep(layer.noiseFloor,layer.noiseCeil,snoise(vec3(noiseCoord.x*layer.noiseFreq.x+time*layer.noiseFlow,noiseCoord.y*layer.noiseFreq.y,time*layer.noiseSpeed+layer.noiseSeed))/2.+0.5);
      v_color=blendNormal(v_color,layer.color,pow(ln,4.));
    }
  }
  gl_Position=projectionMatrix*modelViewMatrix*vec4(pos,1.);
}`;

                const frag = `
varying vec3 v_color;
void main(){
  vec3 color=v_color;
  if(u_darken_top==1.){vec2 st=gl_FragCoord.xy/resolution.xy;color.g-=pow(st.y+sin(-12.)*st.x,u_shadow_power)*0.4;}
  gl_FragColor=vec4(color,1.);
}`;

                const material = new this.minigl.Material(vert, frag, uniforms);
                const geometry = new this.minigl.PlaneGeometry();
                this.mesh = new this.minigl.Mesh(geometry, material);
                this.resize();
                window.addEventListener('resize', () => this.resize());
            }
            resize() { const w = this.canvas.parentElement.offsetWidth, h = this.canvas.parentElement.offsetHeight; this.minigl.setSize(w, h); this.minigl.setOrthographicCamera(); this.mesh.geometry.setTopology(Math.ceil(w * 0.02), Math.ceil(h * 0.05)); this.mesh.geometry.setSize(w, h); }
            animate = (ts) => { if (!this.isPlaying) return; this.time += Math.min(ts - this.last, 1e3 / 15); this.last = ts; this.mesh.material.uniforms.u_time.value = this.time; this.minigl.render(); this.animationId = requestAnimationFrame(this.animate); };
            start() { this.isPlaying = true; this.animationId = requestAnimationFrame(this.animate); }
            stop() { this.isPlaying = false; if (this.animationId) cancelAnimationFrame(this.animationId); }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const canvas = document.getElementById('gradient-canvas');
            if (!canvas) return;
            try {
                const g = new Gradient(canvas, ['#0d4f52', '#1a7a7d', '#3EAEB1', '#61BACA', '#9FD8E1', '#1a7a7d']);
                g.start();
            } catch (e) {
                canvas.style.display = 'none';
                canvas.parentElement.style.background = 'linear-gradient(135deg,#0d4f52 0%,#1a7a7d 40%,#3EAEB1 100%)';
            }
        });
    })();

    /* ── Alpine hero search (preserved API) ── */
    function heroSearch() {
        return {
            query: '', results: [], open: false, loading: false, focusedIdx: -1,
            async fetch() {
                if (this.query.length < 2) { this.results = []; this.open = false; return; }
                this.loading = true;
                try { const r = await window.fetch(`/api/v1/search/instant?q=${encodeURIComponent(this.query)}`); const d = await r.json(); this.results = d.data || []; this.open = true; this.focusedIdx = -1; } catch (e) { this.results = []; }
                this.loading = false;
            },
            focusNext() { this.focusedIdx = Math.min(this.focusedIdx + 1, this.results.length - 1); },
            focusPrev() { this.focusedIdx = Math.max(this.focusedIdx - 1, -1); },
            go() {
                if (this.focusedIdx >= 0 && this.results[this.focusedIdx]) { const d = this.results[this.focusedIdx]; window.location.href = d.is_fda ? `/drugs/fda/${d.slug}` : `/drugs/${d.id}`; }
                else if (this.query) { window.location.href = `/search?q=${encodeURIComponent(this.query)}`; }
            }
        };
    }
</script>
@include('partials.hero-medicheck-scripts')
@endpush
