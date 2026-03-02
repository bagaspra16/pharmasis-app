@extends('layouts.app')

@section('title', 'Pharmasis — Know Your Medicine')
@section('meta_description', 'Search 16,000+ medicines. Find drug information, warnings, dosage, and side effects in
plain language. Powered by AI for easy understanding.')

@push('head')
<style>
    /* ── Scoped Mothwing: hero title ONLY ── */
    .hero-title {
        font-family: 'Mothwing', sans-serif;
        letter-spacing: 0.03em;
    }

    /* ── CSS Animated Gradient (stable, smooth, cross-browser) ── */
    @keyframes gradientShift {
        0% {
            background-position: 0% 30%;
        }

        25% {
            background-position: 60% 70%;
        }

        50% {
            background-position: 100% 40%;
        }

        75% {
            background-position: 40% 90%;
        }

        100% {
            background-position: 0% 30%;
        }
    }

    .hero-gradient {
        background: linear-gradient(-55deg,
                #b2f0f0 0%,
                #d4f7f7 12%,
                #e8fffb 22%,
                #c8ede8 30%,
                #a8dfe0 40%,
                #d0f3f4 52%,
                #e4fbfd 62%,
                #b8edd0 72%,
                #cef5f0 82%,
                #b2f0f0 100%);
        background-size: 400% 400%;
        animation: gradientShift 14s ease infinite;
    }

    /* ── Floating orb ── */
    @keyframes orbFloat {

        0%,
        100% {
            transform: translate(0, 0) scale(1);
        }

        33% {
            transform: translate(30px, -40px) scale(1.08);
        }

        66% {
            transform: translate(-20px, 30px) scale(0.95);
        }
    }

    @keyframes orbFloat2 {

        0%,
        100% {
            transform: translate(0, 0) scale(1);
        }

        40% {
            transform: translate(-40px, 20px) scale(1.05);
        }

        70% {
            transform: translate(25px, -30px) scale(0.92);
        }
    }

    .orb-1 {
        animation: orbFloat 20s ease-in-out infinite;
    }

    .orb-2 {
        animation: orbFloat2 16s ease-in-out infinite;
    }

    .orb-3 {
        animation: orbFloat 24s ease-in-out infinite reverse;
    }

    /* ── Hero search frosted card ── */
    .hero-search-card {
        background: rgba(255, 255, 255, 0.55);
        backdrop-filter: blur(18px);
        -webkit-backdrop-filter: blur(18px);
        border: 1px solid rgba(255, 255, 255, 0.85);
        box-shadow:
            0 4px 24px rgba(62, 174, 177, 0.12),
            0 1px 0 rgba(255, 255, 255, 0.9) inset;
        transition: box-shadow 0.3s ease;
    }

    .hero-search-card:focus-within {
        box-shadow:
            0 8px 40px rgba(62, 174, 177, 0.2),
            0 1px 0 rgba(255, 255, 255, 0.9) inset;
    }

    /* ── Drug card ── */
    .drug-card {
        transition: transform 0.22s cubic-bezier(.22, 1, .36, 1), box-shadow 0.22s ease;
    }

    .drug-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 40px rgba(62, 174, 177, 0.14);
    }

    /* ── Alpha pill ── */
    .alpha-pill {
        transition: background 0.18s, color 0.18s, transform 0.18s;
    }

    .alpha-pill:hover {
        transform: scale(1.12);
    }
</style>
@endpush

@section('content')

{{-- ── DB Offline Banner ── --}}
@if(!empty($dbOffline))
<div class="bg-amber-50 border-b border-amber-200" x-data="{ show: true }" x-show="show">
    <div class="max-w-7xl mx-auto px-4 py-2.5 flex items-center gap-3">
        <svg class="w-4 h-4 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7" />
        </svg>
        <p class="text-xs text-amber-800 flex-1"><strong>Database temporarily unavailable.</strong> Showing results from
            OpenFDA.</p>
        <button onclick="window.location.reload()"
            class="text-xs text-amber-700 border border-amber-200 px-2.5 py-1 rounded-lg hover:bg-amber-100 transition-colors">Retry</button>
        <button @click="show=false" class="text-amber-400 hover:text-amber-700 ml-1">✕</button>
    </div>
</div>
@endif

{{-- ════════════════════════════════════════ HERO ════════════════════════════════════════ --}}
<section class="relative min-h-[92vh] flex items-center justify-center overflow-hidden">

    {{-- CSS Animated Gradient --}}
    <div class="hero-gradient absolute inset-0"></div>

    {{-- Floating ambient orbs --}}
    <div
        class="orb-1 absolute top-16 left-[15%]  w-72 h-72 bg-teal-300/40  rounded-full blur-[72px] pointer-events-none">
    </div>
    <div
        class="orb-2 absolute top-32 right-[12%] w-96 h-96 bg-cyan-200/35  rounded-full blur-[80px] pointer-events-none">
    </div>
    <div
        class="orb-3 absolute bottom-20 left-1/3  w-64 h-64 bg-emerald-200/30 rounded-full blur-[64px] pointer-events-none">
    </div>

    {{-- Mesh texture overlay --}}
    <div class="absolute inset-0 pointer-events-none"
        style="background-image:radial-gradient(circle,rgba(62,174,177,0.07) 1px,transparent 1px);background-size:28px 28px;">
    </div>

    {{-- Content --}}
    <div class="relative z-10 max-w-3xl mx-auto px-4 py-20 text-center w-full" x-data="heroSearch()">

        {{-- Badge --}}
        <div
            class="inline-flex items-center gap-2 bg-white/60 text-slate-600 text-xs font-medium px-4 py-1.5 rounded-full mb-7 border border-white/80 backdrop-blur-sm shadow-sm">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse flex-shrink-0"></span>
            16,000+ medicines in our database
        </div>

        {{-- Headline: Mothwing font scoped here, black text --}}
        <h1 class="hero-title text-5xl sm:text-6xl lg:text-7xl text-slate-900 mb-4 leading-[1.05]">
            Know Your<br><span class="text-primary">Medicine.</span>
        </h1>
        <p class="text-sm text-slate-600 mb-10 max-w-md mx-auto leading-relaxed">
            Clear, reliable drug information in plain language.<br>Search by name, generic, or drug class.
        </p>

        {{-- ── Search Card ── --}}
        <div class="hero-search-card rounded-2xl p-2 max-w-2xl mx-auto" @keydown.escape="open=false; results=[]"
            @click.outside="open=false">
            <div class="flex items-center gap-2">
                {{-- Icon --}}
                <div class="pl-3 flex-shrink-0">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                {{-- Input --}}
                <input type="text" x-model="query" @input.debounce.300ms="fetch()"
                    @focus="if(query.length>=2) open=true" @keydown.arrow-down.prevent="focusNext()"
                    @keydown.arrow-up.prevent="focusPrev()" @keydown.enter.prevent="go()"
                    placeholder="Search medicines, e.g. Aspirin, Metformin..."
                    class="flex-1 bg-transparent text-slate-900 placeholder-slate-400 font-medium text-sm py-3.5 focus:outline-none"
                    autocomplete="off" id="hero-search" />
                {{-- Spinner --}}
                <div x-show="loading" class="pr-2 flex-shrink-0">
                    <svg class="animate-spin w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                    </svg>
                </div>
                {{-- Search Button --}}
                <a :href="`/search?q=${encodeURIComponent(query)}`"
                    class="flex-shrink-0 bg-primary text-white font-semibold text-sm px-5 py-3 rounded-xl hover:bg-primary-dark transition-colors shadow-sm">
                    Search
                </a>
            </div>

            {{-- Dropdown Results --}}
            <div x-show="open && results.length > 0" x-cloak
                class="mt-1 border-t border-slate-200 divide-y divide-slate-100 overflow-hidden rounded-b-xl">
                <template x-for="(drug, idx) in results" :key="drug.id">
                    <a :href="drug.is_fda ? `/drugs/fda/${drug.slug}` : `/drugs/${drug.id}`"
                        :class="focusedIdx===idx ? 'bg-primary/10' : 'hover:bg-slate-50'"
                        class="flex items-center gap-3 px-3 py-2.5 transition-colors text-left">
                        <div class="w-8 h-8 rounded-lg bg-primary/15 flex items-center justify-center flex-shrink-0">
                            <span class="text-xs font-bold text-primary"
                                x-text="drug.alpha_index||drug.name?.charAt(0)||'?'"></span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-slate-900 truncate" x-text="drug.name"></p>
                            <p class="text-xs text-slate-500 truncate"
                                x-text="[drug.generic_name,drug.drug_class].filter(Boolean).join(' · ')"></p>
                        </div>
                        <svg class="w-3.5 h-3.5 text-slate-300 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </template>
                <a :href="`/search?q=${encodeURIComponent(query)}`"
                    class="flex items-center gap-2 px-3 py-2.5 text-xs text-primary font-semibold hover:bg-primary/5 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 10h16M4 14h10" />
                    </svg>
                    See all results for "<span x-text="query"></span>"
                </a>
            </div>

            {{-- No results state --}}
            <div x-show="open && results.length===0 && !loading && query.length>=2" x-cloak
                class="mt-1 border-t border-slate-200 py-4 text-center">
                <p class="text-sm text-slate-500">No medicine found. Try a different spelling.</p>
            </div>
        </div>

        {{-- Quick links --}}
        <p class="text-slate-600 text-xs mt-5 tracking-wide">
            Try:&nbsp;
            <a href="/search?q=aspirin"
                class="text-primary hover:text-primary-dark font-semibold transition-colors">Aspirin</a>,&nbsp;
            <a href="/search?q=metformin"
                class="text-primary hover:text-primary-dark font-semibold transition-colors">Metformin</a>,&nbsp;
            <a href="/search?q=ibuprofen"
                class="text-primary hover:text-primary-dark font-semibold transition-colors">Ibuprofen</a>
        </p>
    </div>

    {{-- Wave divider --}}
    <div class="absolute bottom-0 left-0 right-0 z-[2] pointer-events-none">
        <svg viewBox="0 0 1440 60" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none"
            class="w-full h-auto">
            <path d="M0 0C240 50 480 60 720 36C960 12 1200 50 1440 30V60H0V0Z" fill="#f8fafc" />
        </svg>
    </div>
</section>

{{-- ════════════════════════════════════════ FEATURED ════════════════════════════════════════ --}}
<section class="max-w-7xl mx-auto px-4 py-16">

    {{-- Section header --}}
    <div class="flex items-end justify-between mb-8">
        <div>
            <p class="text-xs font-semibold text-primary uppercase tracking-widest mb-1">Database</p>
            <h2 class="text-2xl font-heading text-slate-800">
                {{ !empty($fdaMode) ? 'Popular Medicines via OpenFDA' : 'Popular Medicines' }}
            </h2>
        </div>
        <a href="{{ route('drugs.search') }}"
            class="flex items-center gap-1.5 text-sm text-primary font-medium hover:text-primary-dark transition-colors group">
            Browse all
            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </a>
    </div>

    {{-- Cards grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($featured as $drug)
        @php
        $isDto = $drug instanceof \App\DTOs\DrugDTO;
        $uses = $isDto ? $drug->uses : \App\Models\Drug::cleanField($drug->uses);
        $preview = $uses ? Str::limit($uses, 85) : 'Tap to learn more about this medicine.';
        $href = $isDto ? route('drugs.show_fda', $drug->slug) : route('drugs.show', $drug->id);
        $initials = strtoupper(substr($drug->name ?? '?', 0, 2));
        @endphp
        <a href="{{ $href }}"
            class="drug-card group bg-white rounded-2xl p-5 border border-slate-100 block shadow-sm hover:border-primary/20">
            {{-- Top row --}}
            <div class="flex items-start justify-between mb-3.5">
                <div
                    class="w-10 h-10 bg-gradient-to-br from-primary/20 to-secondary/30 rounded-xl flex items-center justify-center flex-shrink-0">
                    <span class="font-bold text-primary text-sm leading-none">{{ $initials }}</span>
                </div>
                @if($drug->drug_class)
                <span
                    class="text-xs bg-slate-100 text-slate-500 font-medium px-2 py-0.5 rounded-full max-w-[120px] truncate">{{
                    Str::limit($drug->drug_class, 18) }}</span>
                @endif
            </div>
            {{-- Name --}}
            <h3
                class="font-semibold text-slate-800 text-sm leading-snug mb-0.5 group-hover:text-primary transition-colors line-clamp-1">
                {{ $drug->name }}</h3>
            @if($drug->generic_name)
            <p class="text-xs text-slate-400 italic mb-2 truncate">{{ $drug->generic_name }}</p>
            @endif
            <p class="text-xs text-slate-500 leading-relaxed line-clamp-3">{{ $preview }}</p>
            {{-- Footer --}}
            <div class="flex items-center justify-between mt-3.5 pt-3 border-t border-slate-50">
                @if(!empty($drug->is_fda))
                <span class="text-xs text-blue-500 bg-blue-50 px-1.5 py-0.5 rounded font-medium">FDA</span>
                @elseif(!empty($drug->translated))
                <span class="text-xs text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded font-medium">✓ Verified</span>
                @else
                <span class="text-xs text-slate-300">—</span>
                @endif
                <span
                    class="text-xs text-primary font-medium group-hover:translate-x-1 transition-transform inline-block">Details
                    →</span>
            </div>
        </a>
        @endforeach
    </div>

</section>

{{-- ════════════════════════════════════════ ALPHABET ════════════════════════════════════════ --}}
@if(!empty($alphaIndex))
<section class="bg-white border-y border-slate-100 py-10">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-end gap-3 mb-5">
            <h2 class="text-xl font-heading text-slate-800">Browse by Letter</h2>
            <span class="text-xs text-slate-400 pb-0.5">{{ count($alphaIndex) }} sections</span>
        </div>
        <div class="flex flex-wrap gap-2">
            @foreach($alphaIndex as $alpha)
            <a href="{{ route('drugs.search', ['alpha' => $alpha]) }}"
                class="alpha-pill w-10 h-10 flex items-center justify-center rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-primary hover:text-white hover:border-primary">
                {{ strtoupper($alpha) }}
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ════════════════════════════════════════ FEATURES ════════════════════════════════════════ --}}
<section class="max-w-7xl mx-auto px-4 py-16">
    <div class="text-center mb-10">
        <p class="text-xs font-semibold text-primary uppercase tracking-widest mb-2">Why Pharmasis</p>
        <h2 class="text-2xl font-heading text-slate-800">Everything you need to know about your medicine</h2>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach([
        ['icon'=>'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z','title'=>'Smart Search','desc'=>'Search across 16,000+
        medicines by trade name, generic, or drug class — in seconds.'],
        ['icon'=>'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0
        117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4
        0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z','title'=>'AI Plain Language','desc'=>'Complex medical text
        simplified by AI into everyday language you can actually understand.'],
        ['icon'=>'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003
        9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z','title'=>'Safety
        First','desc'=>'Warnings, drug interactions, and before-taking checklists — so you can make informed
        decisions.'],
        ] as $f)
        <div
            class="bg-white rounded-2xl border border-slate-100 p-7 shadow-sm hover:shadow-md hover:border-primary/20 transition-all group">
            <div
                class="w-11 h-11 bg-primary/8 rounded-xl flex items-center justify-center mb-4 group-hover:bg-primary/15 transition-colors">
                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $f['icon'] }}" />
                </svg>
            </div>
            <h3 class="font-heading text-slate-800 mb-2 text-lg">{{ $f['title'] }}</h3>
            <p class="text-sm text-slate-500 leading-relaxed">{{ $f['desc'] }}</p>
        </div>
        @endforeach
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
                const U = k => this.minigl.Uniform;
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

        /* ── Boot ── */
        document.addEventListener('DOMContentLoaded', () => {
            const canvas = document.getElementById('gradient-canvas');
            if (!canvas) return;
            try {
                // Pharmasis brand gradient: deep teal → teal → cyan → light teal → white → teal
                const g = new Gradient(canvas, ['#0d4f52', '#1a7a7d', '#3EAEB1', '#61BACA', '#9FD8E1', '#1a7a7d']);
                g.start();
            } catch (e) {
                // WebGL not available — fall back to CSS gradient
                canvas.style.display = 'none';
                canvas.parentElement.style.background = 'linear-gradient(135deg,#0d4f52 0%,#1a7a7d 40%,#3EAEB1 100%)';
            }
        });
    })();

    /* ── Alpine hero search ── */
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
@endpush