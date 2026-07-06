{{-- HERO — Cek Sehat AI (Bahasa Indonesia · tema teal asli · interaktif + kiosk) --}}
<div x-data="mediCheckHeroId()" x-init="init()" id="ceksehat-root">

    <section class="relative min-h-screen flex items-center justify-center overflow-hidden bg-slate-50"
        id="ceksehat-section" x-data="socialGrid()" @mousemove="handleMouseMove" x-ref="container">

        {{-- Infinite Grid Layer 1 --}}
        <div class="absolute inset-0 z-0 opacity-10 pointer-events-none">
            <svg class="w-full h-full text-slate-400">
                <defs>
                    <pattern id="grid-id-base" width="40" height="40" patternUnits="userSpaceOnUse" :x="gridOffsetX"
                        :y="gridOffsetY">
                        <path d="M 40 0 L 0 0 0 40" fill="none" stroke="currentColor" stroke-width="1" />
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#grid-id-base)" />
            </svg>
        </div>

        {{-- Infinite Grid Layer 2: mouse highlight --}}
        <div class="absolute inset-0 z-0 opacity-50 pointer-events-none transition-opacity duration-300"
            :style="`mask-image: radial-gradient(350px circle at ${mouseX}px ${mouseY}px, black, transparent); -webkit-mask-image: radial-gradient(350px circle at ${mouseX}px ${mouseY}px, black, transparent);`">
            <svg class="w-full h-full text-primary">
                <defs>
                    <pattern id="grid-id-active" width="40" height="40" patternUnits="userSpaceOnUse" :x="gridOffsetX"
                        :y="gridOffsetY">
                        <path d="M 40 0 L 0 0 0 40" fill="none" stroke="currentColor" stroke-width="1.5" />
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#grid-id-active)" />
            </svg>
        </div>

        {{-- Ambient blobs --}}
        <div class="absolute inset-0 pointer-events-none z-0">
            <div class="absolute right-[-10%] top-[-10%] w-[40%] h-[40%] rounded-full bg-teal-400/20 blur-[120px]">
            </div>
            <div class="absolute left-[10%] bottom-[-10%] w-[30%] h-[30%] rounded-full bg-emerald-400/20 blur-[100px]">
            </div>
            <div class="absolute left-[-10%] top-[30%] w-[30%] h-[30%] rounded-full bg-primary/15 blur-[120px]"></div>
        </div>

        {{-- Content --}}
        <div class="relative z-10 max-w-3xl mx-auto px-4 py-10 md:py-16 text-center w-full">

            {{-- Badge --}}
            <div class="inline-flex items-center gap-2 text-[11px] font-medium px-5 py-2 rounded-full mb-7 glass uppercase tracking-[0.14em]"
                style="color:#0d4f52;">
                <span class="relative flex h-2 w-2">
                    <span
                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
                Analisis Gejala dengan AI
                <span class="w-px h-3 bg-slate-300/60 mx-0.5"></span>
                <span class="font-display normal-case tracking-normal text-primary text-base leading-none">Cek
                    Sehat</span>
            </div>

            {{-- Headline --}}
            <h1 class="font-display text-5xl sm:text-6xl lg:text-7xl text-ink-900 mb-5 leading-[1.02] tracking-tight">
                Kenali
                <span class="italic"
                    style="background: linear-gradient(135deg,#3EAEB1 10%,#1a7a7d 90%); -webkit-background-clip:text; -webkit-text-fill-color:transparent; padding-right: 0.05em;">kesehatan</span>
                Anda.
            </h1>
            <p class="text-sm md:text-base text-ink-500 mb-4 max-w-lg mx-auto leading-relaxed">
                Ceritakan yang Anda rasakan dengan mengetik atau bicara, lalu dapatkan analisis kondisi, rekomendasi
                obat, dan info faskes terdekat dalam hitungan detik.
            </p>

            {{-- Cara pakai — 3 langkah sederhana --}}
            <div class="flex flex-wrap items-center justify-center gap-2 mb-8 text-[11px] font-medium">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full glass-soft text-primary-dark">
                    <span
                        class="w-4 h-4 rounded-full bg-primary/15 text-primary text-[9px] font-bold flex items-center justify-center">1</span>
                    Ceritakan gejala
                </span>
                <svg class="w-3 h-3 text-ink-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full glass-soft text-primary-dark">
                    <span
                        class="w-4 h-4 rounded-full bg-primary/15 text-primary text-[9px] font-bold flex items-center justify-center">2</span>
                    AI menganalisis
                </span>
                <svg class="w-3 h-3 text-ink-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full glass-soft text-primary-dark">
                    <span
                        class="w-4 h-4 rounded-full bg-primary/15 text-primary text-[9px] font-bold flex items-center justify-center">3</span>
                    Terima saran
                </span>
            </div>

            {{-- Input Switcher --}}
            <div class="w-full max-w-2xl mx-auto flex flex-col items-center mt-4 mb-8 relative z-20">

                {{-- Tabs --}}
                <div class="inline-flex flex-wrap justify-center p-1.5 rounded-3xl md:rounded-full mb-6"
                    style="background: rgba(255,255,255,0.72); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.9); box-shadow: 0 4px 20px rgba(62,174,177,0.12), 0 1px 0 rgba(255,255,255,0.9) inset;">
                    <button @click="activeTab = 'text'"
                        :class="activeTab === 'text' ? 'text-white shadow-lg' : 'text-slate-500 hover:text-slate-700 hover:bg-white/60'"
                        :style="activeTab === 'text' ? 'background: linear-gradient(135deg,#0d9488,#0f766e); box-shadow: 0 4px 12px rgba(13,148,136,0.4);' : ''"
                        class="px-5 py-2 rounded-full text-xs font-semibold transition-all duration-300 flex items-center gap-1.5">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Ketik
                    </button>
                    <button @click="activeTab = 'voice'"
                        :class="activeTab === 'voice' ? 'text-white shadow-lg' : 'text-slate-500 hover:text-slate-700 hover:bg-white/60'"
                        :style="activeTab === 'voice' ? 'background: linear-gradient(135deg,#3EAEB1,#2d8a8d); box-shadow: 0 4px 12px rgba(62,174,177,0.4);' : ''"
                        class="px-5 py-2 rounded-full text-xs font-semibold transition-all duration-300 flex items-center gap-1.5">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 1a3 3 0 00-3 3v8a3 3 0 006 0V4a3 3 0 00-3-3z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 10v2a7 7 0 01-14 0v-2" />
                        </svg>
                        Suara
                    </button>
                    <button @click="activeTab = 'search'"
                        :class="activeTab === 'search' ? 'text-white shadow-lg' : 'text-slate-500 hover:text-slate-700 hover:bg-white/60'"
                        :style="activeTab === 'search' ? 'background: linear-gradient(135deg,#3EAEB1,#61BACA); box-shadow: 0 4px 12px rgba(62,174,177,0.4);' : ''"
                        class="px-5 py-2 rounded-full text-xs font-semibold transition-all duration-300 flex items-center gap-1.5">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Cari Obat
                    </button>
                </div>

                {{-- Input Area --}}
                <div class="relative w-full min-h-[14rem] flex justify-center">

                    {{-- 1. Voice --}}
                    <div x-show="activeTab === 'voice'" x-transition:enter="transition ease-out duration-300 delay-100"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                        class="absolute inset-0 flex flex-col items-center justify-start">

                        <button @click="toggleRecording()" :disabled="analyzing"
                            :class="recording ? 'bg-red-500 shadow-red-400/40 scale-110' : 'bg-primary shadow-primary/40 hover:scale-105'"
                            class="relative w-24 h-24 sm:w-28 sm:h-28 rounded-full text-white shadow-xl transition-all duration-300 flex items-center justify-center group focus:outline-none">
                            <template x-if="recording">
                                <div>
                                    <span class="absolute inset-0 rounded-full bg-red-400/30 animate-ping"></span>
                                    <span class="absolute -inset-3 rounded-full bg-red-400/15 animate-pulse"></span>
                                </div>
                            </template>
                            <svg x-show="!recording && !analyzing" class="w-10 h-10 sm:w-12 sm:h-12 drop-shadow-md"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M12 1a3 3 0 00-3 3v8a3 3 0 006 0V4a3 3 0 00-3-3z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M19 10v2a7 7 0 01-14 0v-2" />
                                <line x1="12" y1="19" x2="12" y2="23" stroke-width="1.8" stroke-linecap="round" />
                                <line x1="8" y1="23" x2="16" y2="23" stroke-width="1.8" stroke-linecap="round" />
                            </svg>
                            <svg x-show="recording" x-cloak class="w-10 h-10 sm:w-12 sm:h-12 drop-shadow-md"
                                fill="currentColor" viewBox="0 0 24 24">
                                <rect x="6" y="6" width="12" height="12" rx="2" />
                            </svg>
                            <svg x-show="analyzing" x-cloak class="w-10 h-10 sm:w-12 sm:h-12 animate-spin" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4" />
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                            </svg>
                        </button>
                        <p class="text-xs text-slate-500 mt-4 font-medium"
                            x-text="recording ? 'Sedang merekam — ketuk untuk berhenti' : (analyzing ? 'Menganalisis gejala...' : 'Ketuk untuk bicara — bahasa apa saja')">
                        </p>

                        <div x-show="recording" x-cloak class="mt-4 mx-auto w-full max-w-sm">
                            <div class="rounded-xl px-4 py-3"
                                style="background: rgba(255,255,255,0.82); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.9); box-shadow: 0 4px 20px rgba(62,174,177,0.12);">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-red-500 animate-ping"></span>
                                        <span
                                            class="text-[10px] text-red-500 font-bold tracking-widest uppercase">Merekam</span>
                                    </span>
                                    <span class="text-[10px] text-slate-400">Transkripsi suara aktif</span>
                                </div>
                                <canvas x-ref="waveCanvas" width="320" height="48"
                                    class="w-full h-12 rounded-lg bg-slate-50/60"></canvas>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Text --}}
                    <div x-show="activeTab === 'text'" x-cloak
                        x-transition:enter="transition ease-out duration-300 delay-100"
                        x-transition:enter-start="opacity-0 translate-y-4"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-4"
                        class="absolute inset-x-0 top-0 flex flex-col items-center px-4 w-full max-w-2xl mx-auto">
                        <div class="w-full rounded-3xl p-3"
                            style="background: rgba(255,255,255,0.85); backdrop-filter: blur(24px); border: 1px solid rgba(255,255,255,0.95); box-shadow: 0 8px 32px rgba(62,174,177,0.15), 0 1px 0 rgba(255,255,255,0.9) inset;">
                            <div class="flex flex-col">
                                <textarea x-ref="symptomTextarea" x-model="symptoms"
                                    @keydown.enter="handleTextareaEnter($event)" @input="autoResizeTextarea()"
                                    placeholder="Ceritakan gejala Anda secara detail... Contoh: Saya batuk kering dan demam 38°C sejak 2 hari lalu, disertai sakit kepala dan lemas."
                                    class="flex-1 bg-transparent text-slate-900 placeholder-slate-400/80 font-medium text-base px-5 py-4 rounded-3xl focus:outline-none focus:ring-0 resize-none border-none min-h-[120px] max-h-[280px] [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar-thumb]:bg-teal-600/20 [&::-webkit-scrollbar-thumb]:rounded-full hover:[&::-webkit-scrollbar-thumb]:bg-teal-600/40"
                                    rows="3"></textarea>
                                <div
                                    class="flex items-center justify-between gap-3 pt-3 px-3 border-t border-slate-200/50 mt-1">
                                    <span class="text-xs text-slate-400 hidden sm:inline flex-1">Ceritakan detail
                                        penyakit Anda... Tekan Enter untuk menganalisis.</span>
                                    <button @click="analyzeText()" :disabled="analyzing || !symptoms.trim()"
                                        class="flex-shrink-0 ml-auto text-white font-bold h-10 w-10 sm:h-11 sm:w-11 rounded-full flex items-center justify-center transition-all disabled:opacity-40 disabled:cursor-not-allowed hover:scale-105"
                                        style="background: linear-gradient(135deg,#0d9488,#0f766e); box-shadow: 0 4px 16px rgba(13,148,136,0.35);">
                                        <svg x-show="!analyzing" class="w-4 h-4 sm:w-5 sm:h-5" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M5 12h14M12 5l7 7-7 7" />
                                        </svg>
                                        <svg x-show="analyzing" class="w-4 h-4 sm:w-5 sm:h-5 animate-spin" fill="none"
                                            viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 3. Search --}}
                    <div x-show="activeTab === 'search'" x-cloak
                        x-transition:enter="transition ease-out duration-300 delay-100"
                        x-transition:enter-start="opacity-0 translate-y-4"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-4"
                        class="absolute inset-x-0 top-0 flex flex-col items-center px-4 w-full max-w-lg mx-auto"
                        x-data="heroSearchId()">
                        <div class="w-full max-w-2xl rounded-full p-2 relative z-30 flex items-center gap-2"
                            style="background: rgba(255,255,255,0.82); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.9); box-shadow: 0 4px 24px rgba(62,174,177,0.12), 0 1px 0 rgba(255,255,255,0.9) inset;">
                            <div class="flex-1 flex items-center px-4" @keydown.escape="open=false; results=[]"
                                @click.outside="open=false">
                                <svg class="w-5 h-5 text-slate-400 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <input type="text" x-model="query" @input.debounce.300ms="fetch()"
                                    @focus="if(query.length>=2) open=true" @keydown.arrow-down.prevent="focusNext()"
                                    @keydown.arrow-up.prevent="focusPrev()" @keydown.enter.prevent="go()"
                                    placeholder="Cari berdasarkan nama obat..."
                                    class="w-full bg-transparent text-slate-900 placeholder-slate-400 text-sm font-medium focus:outline-none"
                                    autocomplete="off" />
                            </div>
                            <a :href="`/search?q=${encodeURIComponent(query)}`"
                                class="flex-shrink-0 text-white font-bold text-sm px-6 py-3 rounded-full transition-all flex items-center justify-center"
                                style="background: linear-gradient(135deg,#3EAEB1,#2d8a8d); box-shadow: 0 4px 16px rgba(62,174,177,0.35);">Cari</a>
                            <div x-show="open && results.length > 0" x-cloak
                                class="absolute left-0 right-0 top-[110%] bg-white rounded-2xl shadow-xl border border-slate-100 overflow-y-auto max-h-72 z-50 [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]">
                                <template x-for="(drug, idx) in results" :key="drug.id">
                                    <a :href="drug.is_fda ? `/drugs/fda/${drug.slug}` : `/drugs/${drug.id}`"
                                        :class="focusedIdx===idx ? 'bg-primary/10' : 'hover:bg-slate-50'"
                                        class="flex items-center gap-3 px-4 py-3 transition-colors text-left border-b border-slate-50 last:border-0">
                                        <div
                                            class="w-8 h-8 rounded-lg bg-teal-500/10 flex items-center justify-center flex-shrink-0">
                                            <span class="text-sm font-bold text-teal-600"
                                                x-text="drug.name?.charAt(0)||'?'"></span></div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-semibold text-slate-900 truncate" x-text="drug.name">
                                            </p>
                                            <p class="text-[10px] text-slate-500 truncate"
                                                x-text="[drug.generic_name,drug.drug_class].filter(Boolean).join(' · ')">
                                            </p>
                                        </div>
                                    </a>
                                </template>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Error --}}
            <div x-show="error" x-cloak
                class="mt-4 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl max-w-lg mx-auto"
                x-text="error"></div>

            {{-- Processing Panel --}}
            <div x-show="showProcessing" x-cloak class="mt-8 max-w-lg mx-auto text-left" id="processing-panel-id">
                <div class="rounded-2xl overflow-hidden"
                    style="background: rgba(255,255,255,0.82); backdrop-filter: blur(24px); border: 1px solid rgba(255,255,255,0.92); box-shadow: 0 8px 40px rgba(62,174,177,0.14), 0 1px 0 rgba(255,255,255,0.9) inset;">
                    <div class="px-5 py-3.5 flex items-center gap-2"
                        style="border-bottom: 1px solid rgba(62,174,177,0.1); background: linear-gradient(90deg, rgba(62,174,177,0.06), transparent);">
                        <svg class="w-4 h-4 text-primary animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-20" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" />
                            <path class="opacity-80" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                        </svg>
                        <span class="text-xs font-bold text-slate-700 uppercase tracking-widest">Menjalankan Analisis
                            AI...</span>
                    </div>
                    <div class="px-5 py-4 space-y-3">
                        <template x-for="(step, idx) in steps" :key="step.id">
                            <div class="flex items-start gap-3 transition-all duration-300"
                                :class="completedSteps.includes(step.id) ? 'opacity-100' : (currentStep === idx ? 'opacity-100' : 'opacity-35')">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5 transition-all duration-300"
                                    :class="completedSteps.includes(step.id) ? 'bg-emerald-100' : (currentStep === idx ? 'bg-primary/15' : 'bg-slate-100')">
                                    <template x-if="completedSteps.includes(step.id)">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                    </template>
                                    <template x-if="!completedSteps.includes(step.id) && currentStep === idx">
                                        <svg class="w-3.5 h-3.5 text-primary animate-spin" fill="none"
                                            viewBox="0 0 24 24">
                                            <circle class="opacity-20" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="3" />
                                            <path class="opacity-80" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                                        </svg>
                                    </template>
                                    <template x-if="!completedSteps.includes(step.id) && currentStep !== idx">
                                        <span class="text-sm" x-text="step.icon"></span>
                                    </template>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-semibold leading-snug"
                                        :class="completedSteps.includes(step.id) ? 'text-emerald-700' : (currentStep === idx ? 'text-slate-800' : 'text-slate-400')"
                                        x-text="step.label"></p>
                                    <p x-show="currentStep === idx && !completedSteps.includes(step.id)"
                                        class="text-[10px] text-primary mt-0.5 leading-relaxed" x-text="step.detail">
                                    </p>
                                </div>
                            </div>
                        </template>
                    </div>
                    <div class="px-5 py-2.5"
                        style="border-top: 1px solid rgba(62,174,177,0.1); background: rgba(248,254,254,0.6);">
                        <p class="text-[10px] text-slate-400 text-center">Mohon tunggu, proses analisis membutuhkan
                            waktu ~20-30 detik...</p>
                    </div>
                </div>
            </div>

        </div>{{-- /inner content div --}}
    </section>{{-- /ceksehat-section --}}


    {{-- ═══════════════════════════════════════════════════════════════════════ --}}
    {{-- ════════════ HALAMAN OUTPUT TERPISAH — HASIL ANALISIS ══════════════ --}}
    {{-- ═══════════════════════════════════════════════════════════════════════ --}}
    <section x-show="showResults" x-cloak id="medicheck-output-page"
        x-transition:enter="transition ease-out duration-700" x-transition:enter-start="opacity-0 translate-y-6"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-effect="if(showResults) setTimeout(() => { $el.scrollIntoView({ behavior: 'smooth', block: 'start' }) }, 150)"
        class="relative min-h-screen pt-12 pb-24 border-t border-slate-200"
        style="background: linear-gradient(180deg, #F0FDF4 0%, #FFFFFF 15%, #F8FAFC 100%);">

        {{-- ── Output Header Bar ── --}}
        <div class="max-w-5xl mx-auto px-4 pt-4 pb-2 flex items-center justify-between">
            {{-- Left: Pharmasis Logo + Label --}}
            <div class="flex items-center gap-2.5">
                <a href="/" class="flex items-center gap-2 group">
                    <img src="/images/icon.png" alt="Pharmasis"
                        class="w-8 h-8 rounded-xl object-contain"
                        style="box-shadow: 0 3px 10px rgba(13,148,136,0.25);" />
                    <div class="leading-tight">
                        <p class="text-sm font-bold text-slate-800 group-hover:text-primary transition-colors">Pharmasis</p>
                        <p class="text-[10px] text-slate-400 font-medium">Cek Sehat AI</p>
                    </div>
                </a>
                <span class="w-px h-7 bg-slate-200 mx-1"></span>
                <span class="text-[11px] font-semibold text-primary bg-primary/10 px-2.5 py-1 rounded-full">Laporan Kesehatan</span>
            </div>
            {{-- Right: Reset Button --}}
            <button @click="resetKiosk()"
                class="flex items-center gap-1.5 text-[11px] font-semibold px-4 py-2 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 transition-all active:scale-95">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Analisis Baru
            </button>
        </div>

        <div class="max-w-5xl mx-auto px-4 py-8 space-y-6">

            {{-- ── Transcription Card ── --}}
            <div x-show="transcription" class="rounded-2xl p-5"
                style="background: rgba(255,255,255,0.92); backdrop-filter: blur(20px); border: 1px solid rgba(62,174,177,0.12); box-shadow: 0 4px 24px rgba(62,174,177,0.08);">
                <p class="text-[11px] font-bold text-primary uppercase tracking-widest mb-2 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    Yang Anda ceritakan:
                </p>
                <p class="text-base text-slate-700 italic leading-relaxed"
                    x-text='"«\u00a0" + transcription + "\u00a0»"'></p>
            </div>

            {{-- ════════════════ ZONA ATAS: KONDISI & SVG TUBUH ════════════════ --}}
            <div id="mc-kondisi" class="glass-card rounded-3xl overflow-hidden animate-fade-in" x-show="hasJourney">

                {{-- Header --}}
                <div class="px-6 py-4 flex items-center gap-3"
                    :style="`border-bottom: 1px solid rgba(62,174,177,0.1); background: linear-gradient(90deg, ${themeAccent()}10, transparent);`">
                    <span class="w-10 h-10 rounded-2xl flex items-center justify-center flex-shrink-0"
                        :style="`background: ${themeAccent()}20; color: ${themeAccent()};`">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </span>
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">Kondisi Tubuh Anda</h3>
                        <p class="text-xs text-slate-400">Klik area yang ditandai pada peta tubuh untuk melihat detail
                        </p>
                    </div>
                </div>

                <div class="p-6">
                    <div class="flex flex-col lg:flex-row items-start gap-8">

                        {{-- ── SVG Peta Tubuh Interaktif ── --}}
                        <div class="flex-shrink-0 w-full lg:w-auto flex flex-col items-center" x-data="{
                            hoveredArea: null,
                            tooltipX: 0,
                            tooltipY: 0,
                            getAreas() { return this.journey?.zona_atas?.human_mapping_widget?.highlighted_areas || []; }
                         }">

                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Peta Tubuh
                            </p>

                            <div class="relative" style="width: 160px; height: 360px;">

                                {{-- Tooltip --}}
                                <div x-show="hoveredArea" x-cloak class="absolute z-30 pointer-events-none"
                                    :style="`left: ${tooltipX}px; top: ${Math.max(0, tooltipY - 76)}px; transform: translateX(-50%);`">
                                    <div class="text-white text-xs rounded-xl px-3 py-2 shadow-2xl text-center whitespace-nowrap"
                                        :style="`background: ${hoveredArea?.color_code || '#0f172a'};`">
                                        <p class="font-bold text-[11px] tracking-wide"
                                            x-text="bodyPartLabel(hoveredArea?.part)"></p>
                                        <p class="text-[10px] opacity-85 mt-0.5" x-text="hoveredArea?.status"></p>
                                    </div>
                                    <div class="w-2.5 h-2.5 transform rotate-45 mx-auto -mt-1.5 shadow-lg"
                                        :style="`background: ${hoveredArea?.color_code || '#0f172a'};`"></div>
                                </div>

                                {{-- SVG BODY DETAIL --}}
                                <svg viewBox="0 0 100 240" class="w-full h-full" xmlns="http://www.w3.org/2000/svg"
                                    style="filter: drop-shadow(0 6px 18px rgba(62,174,177,0.18));">
                                    <defs>
                                        <style>
                                            @keyframes bodyAreaPulse {

                                                0%,
                                                100% {
                                                    opacity: 0.32;
                                                    transform-box: fill-box;
                                                    transform: scale(1);
                                                }

                                                50% {
                                                    opacity: 0.58;
                                                    transform-box: fill-box;
                                                    transform: scale(1.06);
                                                }
                                            }

                                            .area-pulse {
                                                animation: bodyAreaPulse 2.2s ease-in-out infinite;
                                                transform-origin: center;
                                            }

                                            .body-hit {
                                                cursor: pointer;
                                            }

                                            .body-hit:hover ellipse,
                                            .body-hit:hover circle {
                                                filter: brightness(1.15);
                                            }
                                        </style>
                                    </defs>

                                    {{-- ── BASE SILHOUETTE ── --}}
                                    <g fill="#d9f0f1" stroke="#92cdd0" stroke-width="0.75" stroke-linejoin="round">

                                        {{-- HEAD --}}
                                        <ellipse cx="50" cy="17" rx="14" ry="15.5" />

                                        {{-- EARS --}}
                                        <ellipse cx="36.5" cy="17.5" rx="2.2" ry="3.5" fill="#d9f0f1" stroke="#92cdd0"
                                            stroke-width="0.65" />
                                        <ellipse cx="63.5" cy="17.5" rx="2.2" ry="3.5" fill="#d9f0f1" stroke="#92cdd0"
                                            stroke-width="0.65" />

                                        {{-- FACE DETAILS (subtle) --}}
                                        <ellipse cx="44.5" cy="14" rx="2.4" ry="1.8" fill="#b5d9dc" stroke="none" />
                                        <ellipse cx="55.5" cy="14" rx="2.4" ry="1.8" fill="#b5d9dc" stroke="none" />
                                        <path d="M48,19.5 Q50,21.5 52,19.5" fill="none" stroke="#92cdd0"
                                            stroke-width="0.55" />
                                        <path d="M49.3,16.5 L50,18.5 L50.7,16.5" fill="none" stroke="#92cdd0"
                                            stroke-width="0.45" />

                                        {{-- NECK --}}
                                        <path
                                            d="M44,31 Q43.5,35 43.5,41 L56.5,41 Q56.5,35 56,31 Q53,34.5 50,34.5 Q47,34.5 44,31Z" />

                                        {{-- LEFT SHOULDER (viewer's right = anatomical left) --}}
                                        <path d="M43.5,41 L37,44 L22,49 Q17,51.5 15.5,58 L22,62 Q26,55 36,51 L44,49Z" />

                                        {{-- RIGHT SHOULDER (viewer's left = anatomical right) --}}
                                        <path d="M56.5,41 L63,44 L78,49 Q83,51.5 84.5,58 L78,62 Q74,55 64,51 L56,49Z" />

                                        {{-- CHEST / UPPER TORSO --}}
                                        <path d="M22,62 L78,62 L75.5,100 L24.5,100Z" />
                                        {{-- Sternum --}}
                                        <line x1="50" y1="63" x2="50" y2="99" stroke="#92cdd0" stroke-width="0.35"
                                            fill="none" />
                                        {{-- Rib arcs --}}
                                        <path d="M35,68 Q50,70 65,68" fill="none" stroke="#92cdd0"
                                            stroke-width="0.35" />
                                        <path d="M32,76 Q50,78 68,76" fill="none" stroke="#92cdd0"
                                            stroke-width="0.35" />
                                        <path d="M30,84 Q50,86 70,84" fill="none" stroke="#92cdd0"
                                            stroke-width="0.35" />
                                        <path d="M29,92 Q50,94 71,92" fill="none" stroke="#92cdd0"
                                            stroke-width="0.35" />

                                        {{-- ABDOMEN --}}
                                        <path d="M24.5,100 L75.5,100 L72,130 L28,130Z" />
                                        {{-- Navel --}}
                                        <circle cx="50" cy="113" r="1.6" fill="none" stroke="#92cdd0"
                                            stroke-width="0.6" />

                                        {{-- PELVIS / HIPS --}}
                                        <path d="M28,130 L72,130 L76,150 L24,150Z" />

                                        {{-- LEFT UPPER ARM --}}
                                        <path d="M15.5,58 L8,63 L6,97 L17,99 L23,64Z" />

                                        {{-- RIGHT UPPER ARM --}}
                                        <path d="M84.5,58 L92,63 L94,97 L83,99 L77,64Z" />

                                        {{-- LEFT ELBOW --}}
                                        <ellipse cx="11.5" cy="99.5" rx="5.8" ry="4.2" />

                                        {{-- RIGHT ELBOW --}}
                                        <ellipse cx="88.5" cy="99.5" rx="5.8" ry="4.2" />

                                        {{-- LEFT FOREARM --}}
                                        <path d="M5.8,102 L6,105 L10,137 L19,137 L20,105 L18,102Z" />

                                        {{-- RIGHT FOREARM --}}
                                        <path d="M94.2,102 L94,105 L90,137 L81,137 L80,105 L82,102Z" />

                                        {{-- LEFT WRIST --}}
                                        <ellipse cx="14.5" cy="138" rx="5.2" ry="3.2" />

                                        {{-- RIGHT WRIST --}}
                                        <ellipse cx="85.5" cy="138" rx="5.2" ry="3.2" />

                                        {{-- LEFT HAND --}}
                                        <path
                                            d="M7.5,141 Q6.5,147 7,153 Q9.5,159 14.5,159 Q19.5,159 21,153 Q21.5,147 20.5,141Z" />
                                        <line x1="8.5" y1="147" x2="7.5" y2="155" stroke="#92cdd0" stroke-width="0.5" />
                                        <line x1="11.5" y1="146" x2="10.5" y2="156" stroke="#92cdd0"
                                            stroke-width="0.5" />
                                        <line x1="14.5" y1="145" x2="14.5" y2="157" stroke="#92cdd0"
                                            stroke-width="0.5" />
                                        <line x1="17.5" y1="146" x2="18.5" y2="156" stroke="#92cdd0"
                                            stroke-width="0.5" />
                                        <line x1="20.5" y1="147" x2="21.5" y2="155" stroke="#92cdd0"
                                            stroke-width="0.5" />

                                        {{-- RIGHT HAND --}}
                                        <path
                                            d="M92.5,141 Q93.5,147 93,153 Q90.5,159 85.5,159 Q80.5,159 79,153 Q78.5,147 79.5,141Z" />
                                        <line x1="91.5" y1="147" x2="92.5" y2="155" stroke="#92cdd0"
                                            stroke-width="0.5" />
                                        <line x1="88.5" y1="146" x2="89.5" y2="156" stroke="#92cdd0"
                                            stroke-width="0.5" />
                                        <line x1="85.5" y1="145" x2="85.5" y2="157" stroke="#92cdd0"
                                            stroke-width="0.5" />
                                        <line x1="82.5" y1="146" x2="81.5" y2="156" stroke="#92cdd0"
                                            stroke-width="0.5" />
                                        <line x1="79.5" y1="147" x2="78.5" y2="155" stroke="#92cdd0"
                                            stroke-width="0.5" />

                                        {{-- LEFT THIGH --}}
                                        <path d="M24,152 L46,152 L43.5,192 L21.5,192Z" />

                                        {{-- RIGHT THIGH --}}
                                        <path d="M54,152 L76,152 L78.5,192 L56.5,192Z" />

                                        {{-- LEFT KNEE --}}
                                        <ellipse cx="32.5" cy="195" rx="12" ry="5.5" />

                                        {{-- RIGHT KNEE --}}
                                        <ellipse cx="67.5" cy="195" rx="12" ry="5.5" />

                                        {{-- LEFT LOWER LEG / CALF --}}
                                        <path d="M20.5,199 L44.5,199 L42,228 L23,228Z" />

                                        {{-- RIGHT LOWER LEG / CALF --}}
                                        <path d="M55.5,199 L79.5,199 L77,228 L57,228Z" />

                                        {{-- LEFT ANKLE --}}
                                        <ellipse cx="32.5" cy="229.5" rx="9.5" ry="4" />

                                        {{-- RIGHT ANKLE --}}
                                        <ellipse cx="67.5" cy="229.5" rx="9.5" ry="4" />

                                        {{-- LEFT FOOT --}}
                                        <path d="M21,232 L44,232 L48,237 L17,237 L16,234Z" />

                                        {{-- RIGHT FOOT --}}
                                        <path d="M56,232 L79,232 L84,234 L83,237 L52,237Z" />

                                    </g>

                                    {{-- ── HIGHLIGHT OVERLAYS (dinamis dari journey data) ── --}}
                                    <template x-for="(area, idx) in getAreas()" :key="idx">
                                        <g class="body-hit"
                                            @mouseenter="hoveredArea = area; tooltipX = $event.offsetX; tooltipY = $event.offsetY"
                                            @mousemove="tooltipX = $event.offsetX; tooltipY = $event.offsetY"
                                            @mouseleave="hoveredArea = null"
                                            @click="openDrawer(bodyPartLabel(area.part), { mechanism: area.status, action_for_doctor: area.detail || '', reference_source: 'Analisis Cek Sehat AI' })">

                                            {{-- Outer aura ring (pulse) --}}
                                            <ellipse :cx="bodyPos(area.part).cx" :cy="bodyPos(area.part).cy"
                                                :rx="bodyPos(area.part).rx + 6" :ry="bodyPos(area.part).ry + 6"
                                                :fill="area.color_code || '#F59E0B'" fill-opacity="0.12" stroke="none"
                                                class="area-pulse" />

                                            {{-- Main highlight area --}}
                                            <ellipse :cx="bodyPos(area.part).cx" :cy="bodyPos(area.part).cy"
                                                :rx="bodyPos(area.part).rx" :ry="bodyPos(area.part).ry"
                                                :fill="area.color_code || '#F59E0B'" fill-opacity="0.42"
                                                :stroke="area.color_code || '#F59E0B'" stroke-width="1.5" />

                                            {{-- Center indicator dot --}}
                                            <circle :cx="bodyPos(area.part).cx" :cy="bodyPos(area.part).cy" r="3.2"
                                                :fill="area.color_code || '#F59E0B'" opacity="0.95" />
                                            <circle :cx="bodyPos(area.part).cx" :cy="bodyPos(area.part).cy" r="1.5"
                                                fill="#ffffff" />

                                            {{-- Pulsing ring around dot --}}
                                            <circle :cx="bodyPos(area.part).cx" :cy="bodyPos(area.part).cy" r="6.5"
                                                :stroke="area.color_code || '#F59E0B'" stroke-width="1.2" fill="none"
                                                class="area-pulse" />

                                        </g>
                                    </template>

                                </svg>
                            </div>

                            {{-- Area Labels Klik --}}
                            <div class="flex flex-wrap justify-center gap-1.5 mt-4 max-w-[180px]">
                                <template x-for="(area, idx) in getAreas()" :key="idx">
                                    <button type="button"
                                        @click="openDrawer(bodyPartLabel(area.part), { mechanism: area.status, action_for_doctor: area.detail || '', reference_source: 'Analisis Cek Sehat AI' })"
                                        class="text-[11px] font-semibold px-2.5 py-1 rounded-full transition-all hover:opacity-75 active:scale-95"
                                        :style="`background: ${area.color_code}22; color: ${area.color_code}; border: 1.5px solid ${area.color_code}55;`"
                                        x-text="bodyPartLabel(area.part)">
                                    </button>
                                </template>
                            </div>
                        </div>

                        {{-- ── Right: Empathy + Energy + Diagnosis ── --}}
                        <div class="flex-1 min-w-0 space-y-5">

                            {{-- Theme badge --}}
                            <div class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-widest px-3 py-1.5 rounded-full"
                                :style="`background:${themeAccent()}18; color:${themeAccent()};`">
                                <span class="w-2 h-2 rounded-full animate-pulse"
                                    :style="`background:${themeAccent()}`"></span>
                                <span x-text="journey?.status_page_theme || 'Laporan Kesehatan'"></span>
                            </div>

                            {{-- Empathy summary --}}
                            <p class="text-lg md:text-xl text-ink-900 leading-relaxed font-medium"
                                x-text="journey?.zona_atas?.ai_empathy_summary"></p>

                            {{-- ── Energy Score Widget ── --}}
                            <div x-show="energyScore !== null" class="rounded-2xl p-4"
                                style="background: rgba(255,255,255,0.75); border: 1px solid rgba(62,174,177,0.12); box-shadow: 0 2px 12px rgba(62,174,177,0.06);">
                                <div class="flex items-center gap-4 mb-3">
                                    {{-- Circular progress --}}
                                    <div class="relative flex-shrink-0 w-20 h-20">
                                        <svg class="w-20 h-20 -rotate-90" viewBox="0 0 80 80">
                                            <circle cx="40" cy="40" r="32" fill="none" stroke="#e2e8f0"
                                                stroke-width="7" />
                                            <circle cx="40" cy="40" r="32" fill="none" stroke-width="7"
                                                stroke-linecap="round" :stroke="energyColor()"
                                                :stroke-dasharray="201.06"
                                                :stroke-dashoffset="201.06 - (201.06 * (energyScore || 0) / 100)"
                                                style="transition: stroke-dashoffset 1.8s cubic-bezier(0.4,0,0.2,1);" />
                                        </svg>
                                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                                            <span class="text-xl font-black leading-none"
                                                :style="`color: ${energyColor()}`" x-text="energyScore + '%'"></span>
                                            <span class="text-[9px] text-slate-400 font-medium mt-0.5">energi</span>
                                        </div>
                                    </div>
                                    {{-- Text info --}}
                                    <div class="flex-1">
                                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Energi
                                            Tubuh</p>
                                        <p class="text-sm font-semibold mt-0.5" :style="`color: ${energyColor()}`"
                                            x-text="energyScore >= 70 ? 'Kondisi cukup baik' : (energyScore >= 40 ? 'Butuh perhatian lebih' : 'Perlu istirahat segera')">
                                        </p>
                                        <p class="text-[11px] text-slate-400 mt-0.5">Berdasarkan gejala yang dilaporkan
                                        </p>
                                    </div>
                                </div>
                                {{-- Linear bar --}}
                                <div class="h-2.5 rounded-full bg-slate-100 overflow-hidden">
                                    <div class="h-full rounded-full"
                                        :style="`width: ${energyScore}%; background: linear-gradient(90deg, ${energyColor()}, ${energyColor()}bb); transition: width 1.8s cubic-bezier(0.4,0,0.2,1);`">
                                    </div>
                                </div>
                                <div class="flex justify-between mt-1">
                                    <span class="text-[9px] text-slate-300">Kritis (0%)</span>
                                    <span class="text-[9px] text-slate-300">Optimal (100%)</span>
                                </div>
                            </div>

                            {{-- ── Kondisi / Diagnosis List ── --}}
                            <div x-show="result?.step1?.conditions?.length">
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2.5">Kemungkinan
                                    Kondisi</p>
                                <div class="space-y-2">
                                    <template x-for="cond in (result?.step1?.conditions || []).slice(0,3)"
                                        :key="cond.name">
                                        <div
                                            class="flex items-start gap-3 p-3 rounded-2xl bg-white/80 border border-slate-100 hover:border-slate-200 transition-colors">
                                            <div class="w-1.5 h-full min-h-[2rem] rounded-full flex-shrink-0 mt-0.5"
                                                :class="(cond.likelihood === 'High' || cond.likelihood === 'Tinggi') ? 'bg-red-400' : ((cond.likelihood === 'Moderate' || cond.likelihood === 'Sedang') ? 'bg-amber-400' : 'bg-emerald-400')">
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2 flex-wrap mb-0.5">
                                                    <span
                                                        class="text-[10px] font-black uppercase tracking-wider px-1.5 py-0.5 rounded"
                                                        :class="(cond.likelihood === 'High' || cond.likelihood === 'Tinggi') ? 'bg-red-100 text-red-700' : ((cond.likelihood === 'Moderate' || cond.likelihood === 'Sedang') ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700')"
                                                        x-text="likelihoodLabel(cond.likelihood)"></span>
                                                    <p class="text-sm font-bold text-slate-800" x-text="cond.name"></p>
                                                </div>
                                                <p class="text-xs text-slate-500 leading-snug line-clamp-2"
                                                    x-text="cond.description"></p>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>{{-- /mc-kondisi --}}

            {{-- ════════════════ ZONA TENGAH: RENCANA PEMULIHAN ════════════════ --}}
            <div id="mc-rencana" class="glass-card rounded-3xl p-6 animate-fade-in"
                x-show="journey?.zona_tengah?.interactive_timeline_checklist?.length">
                <div class="flex items-center justify-between mb-5 flex-wrap gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-2xl bg-primary/15 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-800">Langkah Pemulihan Anda</h3>
                            <p class="text-xs text-slate-400">Ketuk item saat sudah Anda lakukan</p>
                        </div>
                    </div>
                    {{-- Circular progress checklist --}}
                    <div class="relative flex-shrink-0 w-14 h-14">
                        <svg class="w-14 h-14 -rotate-90" viewBox="0 0 56 56">
                            <circle cx="28" cy="28" r="22" fill="none" stroke="#e2e8f0" stroke-width="5.5" />
                            <circle cx="28" cy="28" r="22" fill="none" stroke="#10B981" stroke-width="5.5"
                                stroke-linecap="round" :stroke-dasharray="138.23"
                                :stroke-dashoffset="138.23 - (138.23 * checklistProgress / 100)"
                                style="transition: stroke-dashoffset 0.5s ease;" />
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-xs font-black text-emerald-600" x-text="checklistProgress + '%'"></span>
                        </div>
                    </div>
                </div>
                <p class="text-sm text-slate-500 mb-4 leading-relaxed">Ikuti langkah-langkah ini secara berurutan.
                    Selesaikan dari atas ke bawah untuk pemulihan optimal.</p>
                <div class="space-y-2.5">
                    <template x-for="(item, idx) in (journey?.zona_tengah?.interactive_timeline_checklist || [])"
                        :key="idx">
                        <button type="button" @click="toggleChecklist(idx)"
                            class="w-full flex items-start gap-3 p-4 rounded-2xl border text-left transition-all group"
                            :class="item.is_done ? 'bg-emerald-50 border-emerald-200 shadow-sm' : 'bg-white/60 border-slate-100 hover:border-primary/30 hover:bg-white hover:shadow-sm'">
                            <span
                                class="w-7 h-7 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5 transition-all"
                                :class="item.is_done ? 'bg-emerald-500 shadow-md shadow-emerald-200' : 'bg-slate-100 border border-slate-200 group-hover:border-primary/40'">
                                <svg x-show="item.is_done" class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                <span x-show="!item.is_done" class="text-xs font-bold text-slate-400"
                                    x-text="idx + 1"></span>
                            </span>
                            <div class="flex-1 min-w-0">
                                <span
                                    class="inline-block text-[11px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full mb-1"
                                    :class="item.is_done ? 'bg-emerald-100 text-emerald-700' : 'bg-primary/10 text-primary'"
                                    x-text="item.time"></span>
                                <p class="text-sm leading-snug font-medium"
                                    :class="item.is_done ? 'text-emerald-700 line-through opacity-60' : 'text-slate-800'"
                                    x-text="item.task"></p>
                            </div>
                        </button>
                    </template>
                </div>
            </div>{{-- /mc-rencana --}}

            {{-- ════════════════ ZONA BAWAH: OBAT & KEAMANAN ════════════════ --}}
            <div id="mc-obat" class="glass-card rounded-3xl overflow-hidden animate-fade-in">

                <div class="px-6 py-4 flex items-center gap-3"
                    style="border-bottom: 1px solid rgba(62,174,177,0.1); background: linear-gradient(90deg,rgba(62,174,177,0.06),transparent);">
                    <span
                        class="w-10 h-10 bg-primary/15 rounded-2xl flex items-center justify-center text-primary flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                        </svg>
                    </span>
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">Obat & Keamanan</h3>
                        <p class="text-xs text-slate-400">Ketuk nama obat untuk melihat detail lengkap</p>
                    </div>
                </div>

                <div class="p-6 space-y-6">

                    {{-- Safety Alerts --}}
                    <template x-for="(alert, ai) in (journey?.zona_bawah?.safety_alerts || [])" :key="ai">
                        <button type="button" @click="openDrawer(alert.title, alert.drawer_click_details)"
                            class="w-full text-left rounded-2xl p-4 border-2 transition-all hover:scale-[1.01] active:scale-100"
                            :class="alert.level === 'CRITICAL' ? 'bg-red-50 border-red-300' : (alert.level === 'HIGH_ALERT' ? 'bg-amber-50 border-amber-300' : 'bg-slate-50 border-slate-200')">
                            <div class="flex items-start gap-3">
                                <span
                                    class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0 font-black text-white text-lg"
                                    :class="alert.level === 'CRITICAL' ? 'bg-red-500' : (alert.level === 'HIGH_ALERT' ? 'bg-amber-500' : 'bg-slate-400')">!</span>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap mb-0.5">
                                        <span
                                            class="text-[11px] font-black uppercase tracking-wider px-2 py-0.5 rounded"
                                            :class="alert.level === 'CRITICAL' ? 'bg-red-200 text-red-800' : (alert.level === 'HIGH_ALERT' ? 'bg-amber-200 text-amber-800' : 'bg-slate-200 text-slate-700')"
                                            x-text="alertLevelLabel(alert.level)"></span>
                                        <p class="text-base font-bold"
                                            :class="alert.level === 'CRITICAL' ? 'text-red-800' : 'text-slate-800'"
                                            x-text="alert.title"></p>
                                    </div>
                                    <p class="text-sm text-slate-600 leading-relaxed" x-text="alert.short_warning"></p>
                                    <p class="text-xs font-semibold text-primary mt-1.5">Ketuk untuk detail mekanisme →
                                    </p>
                                </div>
                            </div>
                        </button>
                    </template>

                    {{-- OTC medications --}}
                    <div x-show="journey?.zona_bawah?.medication_recommendations?.over_the_counter?.length">
                        <p
                            class="text-sm font-bold text-emerald-600 uppercase tracking-wider mb-3 flex items-center gap-2">
                            <span
                                class="w-5 h-5 rounded-md bg-emerald-100 flex items-center justify-center text-xs">🟢</span>
                            Obat Bebas & Suplemen
                        </p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <template
                                x-for="(d, di) in (journey?.zona_bawah?.medication_recommendations?.over_the_counter || [])"
                                :key="di">
                                <button type="button"
                                    @click="openDrawer(d.name, { ...d.drawer_click_details, __drug: d })"
                                    class="text-left bg-white rounded-2xl border border-slate-100 p-4 shadow-sm hover:shadow-md hover:border-emerald-200 transition-all group">
                                    <div class="flex items-center gap-2 flex-wrap mb-1">
                                        <p class="text-base font-bold text-slate-900" x-text="d.name"></p>
                                        <span
                                            class="text-[10px] bg-emerald-100 text-emerald-700 px-1.5 py-0.5 rounded-full font-bold uppercase">Bebas</span>
                                    </div>
                                    <p class="text-xs text-slate-500 mb-1.5" x-text="d.generic"></p>
                                    <p class="text-sm font-semibold text-slate-700" x-text="d.dosage"></p>
                                    <p class="text-sm text-slate-500 mt-1 leading-snug" x-text="d.purpose"></p>
                                    <p
                                        class="text-xs font-semibold text-primary mt-2 group-hover:translate-x-0.5 transition-transform">
                                        Ketuk untuk detail →</p>
                                </button>
                            </template>
                        </div>
                    </div>

                    {{-- Prescription medications --}}
                    <div x-show="journey?.zona_bawah?.medication_recommendations?.prescription_only?.length">
                        <p
                            class="text-sm font-bold text-violet-600 uppercase tracking-wider mb-3 flex items-center gap-2">
                            <span
                                class="w-5 h-5 rounded-md bg-violet-100 flex items-center justify-center text-xs">🟣</span>
                            Obat Resep Dokter
                        </p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <template
                                x-for="(d, di) in (journey?.zona_bawah?.medication_recommendations?.prescription_only || [])"
                                :key="di">
                                <button type="button"
                                    @click="openDrawer(d.name, { ...d.drawer_click_details, __drug: d })"
                                    class="text-left bg-white rounded-2xl border border-slate-100 p-4 shadow-sm hover:shadow-md hover:border-violet-200 transition-all group">
                                    <div class="flex items-center gap-2 flex-wrap mb-1">
                                        <p class="text-base font-bold text-slate-900" x-text="d.name"></p>
                                        <span
                                            class="text-[10px] bg-violet-100 text-violet-700 px-1.5 py-0.5 rounded-full font-bold uppercase">Resep</span>
                                    </div>
                                    <p class="text-xs text-slate-500 mb-1.5" x-text="d.generic"></p>
                                    <p class="text-sm font-semibold text-slate-700" x-text="d.dosage"></p>
                                    <p class="text-sm text-slate-500 mt-1 leading-snug" x-text="d.purpose"></p>
                                    <p
                                        class="text-xs font-semibold text-primary mt-2 group-hover:translate-x-0.5 transition-transform">
                                        Ketuk untuk detail →</p>
                                </button>
                            </template>
                        </div>
                    </div>

                    {{-- Classic drug fallback (jika tidak ada journey) --}}
                    <div x-show="!hasJourney && result?.step2?.drugs?.length">
                        <p class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-3">Rekomendasi Obat</p>
                        <div class="space-y-3">
                            <template x-for="(d, di) in (result?.step2?.drugs || [])" :key="d.name">
                                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden"
                                    :data-drug-name="d.name" x-data="{ open: di === 0 }">
                                    <button type="button" @click="open = !open"
                                        class="w-full px-4 py-3 flex items-start justify-between gap-3 text-left hover:bg-slate-50/60 transition-colors">
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 flex-wrap mb-0.5">
                                                <p class="text-sm font-bold text-slate-900 drug-name-label"
                                                    x-text="d.name"></p>
                                                <span x-show="d.otc"
                                                    class="text-[9px] bg-emerald-100 text-emerald-700 px-1.5 py-0.5 rounded-full font-bold uppercase">Obat
                                                    Bebas</span>
                                                <span x-show="!d.otc"
                                                    class="text-[9px] bg-violet-100 text-violet-700 px-1.5 py-0.5 rounded-full font-bold uppercase">Perlu
                                                    Resep</span>
                                            </div>
                                            <p class="text-xs text-slate-500" x-text="d.generic"></p>
                                        </div>
                                        <svg class="w-4 h-4 text-slate-400 flex-shrink-0 mt-1 transition-transform duration-300"
                                            :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                    <div x-show="open" x-collapse>
                                        <div
                                            class="px-4 py-3 grid grid-cols-2 gap-x-4 gap-y-2.5 border-t border-slate-50">
                                            <div x-show="d.dose">
                                                <p
                                                    class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">
                                                    Dosis</p>
                                                <p class="text-xs font-semibold text-slate-800" x-text="d.dose"></p>
                                            </div>
                                            <div x-show="d.frequency">
                                                <p
                                                    class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">
                                                    Frekuensi</p>
                                                <p class="text-xs font-semibold text-slate-800" x-text="d.frequency">
                                                </p>
                                            </div>
                                        </div>
                                        <div class="px-4 pb-3 space-y-2">
                                            <div x-show="d.purpose" class="bg-blue-50 rounded-xl px-3 py-2">
                                                <p
                                                    class="text-[9px] font-bold text-blue-500 uppercase tracking-wider mb-0.5">
                                                    Kegunaan</p>
                                                <p class="text-xs text-blue-900 leading-relaxed" x-text="d.purpose"></p>
                                            </div>
                                            <div x-show="d.how_to_take" class="bg-emerald-50 rounded-xl px-3 py-2">
                                                <p
                                                    class="text-[9px] font-bold text-emerald-600 uppercase tracking-wider mb-0.5">
                                                    Cara Pakai</p>
                                                <p class="text-xs text-emerald-900 leading-relaxed"
                                                    x-text="d.how_to_take"></p>
                                            </div>
                                            <div x-show="d.side_effects?.length"
                                                class="bg-amber-50 rounded-xl px-3 py-2">
                                                <p
                                                    class="text-[9px] font-bold text-amber-600 uppercase tracking-wider mb-1">
                                                    Efek Samping</p>
                                                <div class="flex flex-wrap gap-1"><template
                                                        x-for="se in (d.side_effects||[])" :key="se"><span
                                                            class="text-[10px] bg-amber-100 text-amber-800 px-2 py-0.5 rounded-full"
                                                            x-text="se"></span></template></div>
                                            </div>
                                            <div x-show="d.contraindications?.length"
                                                class="bg-red-50 rounded-xl px-3 py-2">
                                                <p
                                                    class="text-[9px] font-bold text-red-600 uppercase tracking-wider mb-1">
                                                    Tidak Boleh Digunakan Bila</p>
                                                <div class="flex flex-wrap gap-1"><template
                                                        x-for="ci in (d.contraindications||[])" :key="ci"><span
                                                            class="text-[10px] bg-red-100 text-red-700 px-2 py-0.5 rounded-full"
                                                            x-text="ci"></span></template></div>
                                            </div>
                                            <div class="flex items-center justify-between pt-1">
                                                <div x-show="d.reference_source" class="flex items-center gap-1.5">
                                                    <svg class="w-3 h-3 text-slate-400" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                    <span class="text-[10px] text-slate-400"
                                                        x-text="'Sumber: ' + d.reference_source"></span>
                                                </div>
                                                <a :href="d.db_url || `/search?q=${encodeURIComponent(d.name)}`"
                                                    target="_blank"
                                                    class="flex-shrink-0 flex items-center gap-1 text-[10px] font-semibold px-2.5 py-1.5 rounded-lg transition-colors"
                                                    :class="d.db_found ? 'bg-primary/10 text-primary hover:bg-primary/20' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                                    </svg>
                                                    <span x-text="d.db_found ? 'Lihat Detail' : 'Cari'"></span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Pharmacist notes --}}
                    <div x-show="result?.step2?.pharmacist_notes"
                        class="px-4 py-3 bg-amber-50 border border-amber-200 rounded-2xl">
                        <p class="text-xs font-bold text-amber-700 uppercase tracking-wider mb-0.5">Catatan Apoteker</p>
                        <p class="text-sm text-amber-800 leading-relaxed" x-text="result?.step2?.pharmacist_notes"></p>
                    </div>

                    {{-- Drug interactions --}}
                    <div x-show="result?.step3?.interactions?.length">
                        <p class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-2.5">Interaksi Antar Obat
                        </p>
                        <div class="space-y-2">
                            <template x-for="i in (result?.step3?.interactions || [])" :key="i.drug_a + i.drug_b">
                                <div class="flex items-start gap-2 p-3 rounded-xl"
                                    :class="(i.severity==='Avoid'||i.severity==='Hindari') ? 'bg-red-50' : ((i.severity==='Caution'||i.severity==='Hati-hati') ? 'bg-amber-50' : 'bg-slate-50')">
                                    <span class="text-xs font-bold px-1.5 py-0.5 rounded flex-shrink-0 mt-0.5"
                                        :class="(i.severity==='Avoid'||i.severity==='Hindari') ? 'bg-red-200 text-red-800' : ((i.severity==='Caution'||i.severity==='Hati-hati') ? 'bg-amber-200 text-amber-800' : 'bg-slate-200 text-slate-700')"
                                        x-text="severityLabel(i.severity)"></span>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-semibold text-slate-800"
                                            x-text="`${i.drug_a} + ${i.drug_b}`"></p>
                                        <p class="text-xs text-slate-500" x-text="i.effect"></p>
                                        <p x-show="i.substitute" class="text-xs text-emerald-600 mt-0.5"
                                            x-text="`→ Alternatif: ${i.substitute}`"></p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Rencana Pemulihan Klasik (fallback) --}}
                    <div x-show="!hasJourney && result?.step4"
                        class="rounded-2xl overflow-hidden border border-slate-100">
                        <div class="px-5 py-3.5 flex items-center gap-2"
                            style="background: linear-gradient(90deg,rgba(62,174,177,0.06),transparent);">
                            <span
                                class="w-7 h-7 bg-primary/15 rounded-xl flex items-center justify-center text-xs font-bold text-primary flex-shrink-0">4</span>
                            <h3 class="text-sm font-bold text-slate-800">Rencana Pemulihan</h3>
                        </div>
                        <div class="p-4 space-y-3">
                            <div x-show="result?.step4?.recovery_timeline"
                                class="flex items-start gap-3 bg-primary/5 rounded-xl px-4 py-3">
                                <div>
                                    <p class="text-[9px] font-bold text-primary uppercase tracking-wider mb-0.5">
                                        Perkiraan Waktu Pemulihan</p>
                                    <p class="text-xs text-slate-800 font-medium"
                                        x-text="result?.step4?.recovery_timeline"></p>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div x-show="result?.step4?.lifestyle_tips?.length"
                                    class="bg-emerald-50 rounded-xl p-3">
                                    <p class="text-[9px] font-bold text-emerald-600 uppercase tracking-wider mb-1.5">
                                        Gaya Hidup</p>
                                    <template x-for="tip in (result?.step4?.lifestyle_tips||[])" :key="tip">
                                        <p class="text-xs text-emerald-900 mb-0.5">• <span x-text="tip"></span></p>
                                    </template>
                                </div>
                                <div x-show="result?.step4?.diet_recommendations?.length"
                                    class="bg-teal-50 rounded-xl p-3">
                                    <p class="text-[9px] font-bold text-teal-600 uppercase tracking-wider mb-1.5">
                                        Anjuran Makanan</p>
                                    <template x-for="d in (result?.step4?.diet_recommendations||[])" :key="d">
                                        <p class="text-xs text-teal-900 mb-0.5">• <span x-text="d"></span></p>
                                    </template>
                                </div>
                            </div>
                            <div x-show="result?.step4?.warning_signs?.length"
                                class="bg-red-50 border border-red-100 rounded-xl p-3">
                                <p class="text-[9px] font-bold text-red-600 uppercase tracking-wider mb-1.5">Segera ke
                                    Dokter Bila</p>
                                <template x-for="w in (result?.step4?.warning_signs||[])" :key="w">
                                    <p class="text-xs text-red-800 mb-0.5">• <span x-text="w"></span></p>
                                </template>
                            </div>
                            <div x-show="result?.step4?.disclaimer" class="pt-2 border-t border-slate-100">
                                <p class="text-[10px] text-slate-400 italic leading-relaxed"
                                    x-text="result?.step4?.disclaimer"></p>
                            </div>
                        </div>
                    </div>

                    {{-- Clinical PDF export --}}
                    <div x-show="journey?.zona_bawah?.clinical_pdf_export"
                        class="rounded-2xl p-4 border border-dashed border-slate-200 bg-slate-50/60">
                        <div class="flex items-start gap-3 flex-wrap">
                            <div class="flex-1 min-w-[12rem]">
                                <p class="text-sm font-bold text-slate-700 mb-1">Untuk dibawa ke dokter</p>
                                <p class="text-xs text-slate-500 mb-1">Rangkuman rekam medis formal (istilah medis +
                                    kode ICD-10).</p>
                                <p class="text-xs text-slate-400">Dugaan ICD-10: <span
                                        class="font-mono font-semibold text-slate-600"
                                        x-text="journey?.zona_bawah?.clinical_pdf_export?.suspected_icd10"></span></p>
                            </div>
                            <button @click="exportClinicalPdf()"
                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full text-sm font-bold text-white transition-all hover:scale-105"
                                style="background: linear-gradient(135deg,#0d4f52,#1a7a7d);">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                PDF Klinis
                            </button>
                        </div>
                    </div>

                </div>
            </div>{{-- /mc-obat --}}

            {{-- ════════════════ FASKES TERDEKAT ════════════════ --}}
            <div id="mc-faskes" class="glass-card rounded-3xl overflow-hidden animate-fade-in"
                x-show="nearbyLoading || nearbyProviders">
                <div class="px-6 py-4 flex items-center justify-between"
                    style="border-bottom: 1px solid rgba(62,174,177,0.1); background: linear-gradient(90deg,rgba(62,174,177,0.06),transparent);">
                    <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <span
                            class="w-9 h-9 bg-primary/15 rounded-2xl flex items-center justify-center text-lg flex-shrink-0">📍</span>
                        Fasilitas Kesehatan Terdekat
                    </h3>
                    <div x-show="nearbyLoading" class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-primary animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-20" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" />
                            <path class="opacity-80" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                        </svg>
                        <span class="text-[10px] text-primary font-medium">Mencari...</span>
                    </div>
                </div>
                <div class="p-5">
                    <template x-if="nearbyLoading">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <template x-for="n in [1,2,3,4]" :key="n">
                                <div class="bg-white rounded-xl p-3 border border-slate-100 animate-pulse">
                                    <div class="h-3 bg-slate-200 rounded w-3/4 mb-2"></div>
                                    <div class="h-2.5 bg-slate-100 rounded w-full mb-1"></div>
                                    <div class="h-2.5 bg-slate-100 rounded w-2/3 mb-3"></div>
                                    <div class="h-6 bg-slate-100 rounded w-1/3"></div>
                                </div>
                            </template>
                        </div>
                    </template>
                    <template x-if="!nearbyLoading && nearbyProviders">
                        <div>
                            <div x-show="nearbyProviders.providers?.length"
                                class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <template x-for="provider in (nearbyProviders.providers||[])" :key="provider.name">
                                    <div
                                        class="bg-white rounded-xl p-3 border border-slate-100 shadow-sm hover:shadow-md transition-shadow">
                                        <div class="flex items-start justify-between mb-1.5">
                                            <p class="text-sm font-bold text-slate-800 leading-tight"
                                                x-text="provider.name"></p>
                                            <span
                                                class="text-[9px] bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded font-bold uppercase truncate max-w-[80px] ml-2 flex-shrink-0"
                                                x-text="provider.type"></span>
                                        </div>
                                        <p class="text-xs text-slate-500 mb-2 line-clamp-2" x-text="provider.address">
                                        </p>
                                        <div class="flex gap-1.5 flex-wrap">
                                            <a x-show="provider.contact"
                                                :href="provider.contact && provider.contact.includes('http') ? provider.contact : 'tel:' + provider.contact"
                                                target="_blank"
                                                class="text-[10px] font-bold text-primary bg-primary/10 px-2 py-1 rounded inline-block hover:bg-primary/20 transition-colors"
                                                x-text="provider.contact && provider.contact.includes('http') ? 'Situs ↗' : 'Telepon'"></a>
                                            <a :href="provider.maps_url" target="_blank"
                                                class="text-[10px] font-bold text-slate-600 bg-slate-100 px-2 py-1 rounded inline-block hover:bg-slate-200 transition-colors">Peta
                                                ↗</a>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            <div x-show="nearbyProviders.emergency_numbers"
                                class="bg-red-50 border border-red-100 rounded-xl p-3 flex items-start gap-3 mt-3">
                                <div
                                    class="w-6 h-6 rounded bg-red-100 text-red-600 flex items-center justify-center font-bold text-xs flex-shrink-0 mt-0.5">
                                    !</div>
                                <div>
                                    <p class="text-[9px] font-bold text-red-600 uppercase tracking-wider mb-0.5">Nomor
                                        Darurat Setempat</p>
                                    <p class="text-sm text-red-800 font-bold"
                                        x-text="nearbyProviders.emergency_numbers"></p>
                                </div>
                            </div>
                            <a :href="nearbyProviders.maps_search_url" target="_blank"
                                class="mt-3 flex items-center justify-center gap-2 w-full py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 transition-colors text-sm font-semibold text-slate-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Cari Lebih Banyak di Google Maps ↗
                            </a>
                        </div>
                    </template>
                </div>
            </div>{{-- /mc-faskes --}}

            {{-- Watermark --}}
            <div class="text-center pt-4">
                <div class="flex items-center justify-center gap-2 mb-1">
                    <div class="w-5 h-5 rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg,#0d9488,#0f766e);">
                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9V8h2v8zm4 0h-2V8h2v8z" />
                        </svg>
                    </div>
                    <span class="text-xs font-bold text-slate-500">Pharmasis <span class="text-primary">Cek Sehat</span></span>
                </div>
                <p class="text-[11px] text-slate-300 mt-0.5">Laporan ini hanya untuk edukasi. Selalu konsultasikan dengan tenaga kesehatan profesional.</p>
            </div>

            {{-- Action Bar --}}
            <div class="pb-10 flex flex-wrap items-center justify-center gap-3" id="ceksehat-actions">
                <button @click="copyResult()"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full text-xs font-semibold transition-all hover:scale-105 active:scale-95"
                    style="background: rgba(255,255,255,0.88); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.9); box-shadow: 0 4px 20px rgba(62,174,177,0.12); color: #16323a;">
                    <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    <span x-text="copyFeedback || 'Salin Laporan'"></span>
                </button>
                <button @click="shareResult()"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full text-xs font-semibold transition-all hover:scale-105 active:scale-95"
                    style="background: linear-gradient(135deg,#3EAEB1,#2d8a8d); box-shadow: 0 4px 16px rgba(62,174,177,0.35); color: #fff;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                    </svg>
                    Bagikan
                </button>
                <button @click="savePdf()"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full text-xs font-semibold transition-all hover:scale-105 active:scale-95"
                    style="background: rgba(255,255,255,0.88); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.9); box-shadow: 0 4px 20px rgba(62,174,177,0.12); color: #16323a;">
                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Simpan PDF
                </button>
                <button @click="resetKiosk()"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full text-xs font-semibold transition-all hover:scale-105 active:scale-95 bg-slate-100 text-slate-600 hover:bg-slate-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Mulai Lagi
                </button>
            </div>

        </div>{{-- /max-w-5xl --}}

        {{-- ══════════════ BOTTOM-SHEET DRAWER ══════════════ --}}
        <div x-show="drawerOpen" x-cloak x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[110] flex items-end sm:items-center justify-center"
            style="background: rgba(11,31,36,0.5); backdrop-filter: blur(4px);" @click.self="closeDrawer()">
            <div class="bg-white w-full sm:max-w-lg sm:rounded-3xl rounded-t-3xl max-h-[85vh] overflow-y-auto shadow-2xl"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="translate-y-full sm:translate-y-4 sm:scale-95 opacity-0"
                x-transition:enter-end="translate-y-0 sm:scale-100 opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="translate-y-0 opacity-100"
                x-transition:leave-end="translate-y-full opacity-0">
                <div class="sticky top-0 bg-white pt-3 pb-3 px-6 border-b border-slate-100 z-10">
                    <div class="w-10 h-1.5 rounded-full bg-slate-200 mx-auto mb-3 sm:hidden"></div>
                    <div class="flex items-center justify-between gap-3">
                        <h3 class="text-xl font-bold text-ink-900" x-text="drawerTitle"></h3>
                        <button @click="closeDrawer()"
                            class="w-9 h-9 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center flex-shrink-0 transition-colors">
                            <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="p-6 space-y-4">
                    <template x-if="drawerData?.__drug">
                        <div class="flex flex-wrap gap-2">
                            <span x-show="drawerData.__drug.drug_class"
                                class="text-sm bg-slate-100 text-slate-600 px-3 py-1 rounded-full font-medium"
                                x-text="drawerData.__drug.drug_class"></span>
                            <span x-show="drawerData.__drug.dosage"
                                class="text-sm bg-primary/10 text-primary px-3 py-1 rounded-full font-semibold"
                                x-text="drawerData.__drug.dosage"></span>
                        </div>
                    </template>
                    <div x-show="drawerData?.mechanism">
                        <p class="text-xs font-bold text-indigo-500 uppercase tracking-wider mb-1">Mekanisme / Cara
                            Kerja</p>
                        <p class="text-base text-slate-700 leading-relaxed" x-text="drawerData?.mechanism"></p>
                    </div>
                    <div x-show="drawerData?.action_for_doctor">
                        <p class="text-xs font-bold text-teal-600 uppercase tracking-wider mb-1">Tindakan untuk Dokter
                        </p>
                        <p class="text-base text-slate-700 leading-relaxed" x-text="drawerData?.action_for_doctor"></p>
                    </div>
                    <div x-show="drawerData?.how_to_take" class="bg-emerald-50 rounded-2xl px-4 py-3">
                        <p class="text-xs font-bold text-emerald-600 uppercase tracking-wider mb-1">Cara Pakai</p>
                        <p class="text-base text-emerald-900 leading-relaxed" x-text="drawerData?.how_to_take"></p>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div x-show="drawerData?.duration" class="bg-slate-50 rounded-xl px-3 py-2">
                            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Durasi</p>
                            <p class="text-sm font-semibold text-slate-800" x-text="drawerData?.duration"></p>
                        </div>
                        <div x-show="drawerData?.route" class="bg-slate-50 rounded-xl px-3 py-2">
                            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Cara
                                Pemberian</p>
                            <p class="text-sm font-semibold text-slate-800 capitalize" x-text="drawerData?.route"></p>
                        </div>
                    </div>
                    <div x-show="drawerData?.side_effects?.length" class="bg-amber-50 rounded-2xl px-4 py-3">
                        <p class="text-xs font-bold text-amber-600 uppercase tracking-wider mb-1.5">Efek Samping</p>
                        <div class="flex flex-wrap gap-1.5">
                            <template x-for="se in (drawerData?.side_effects || [])" :key="se"><span
                                    class="text-sm bg-amber-100 text-amber-800 px-2.5 py-1 rounded-full"
                                    x-text="se"></span></template>
                        </div>
                    </div>
                    <div x-show="drawerData?.contraindications?.length" class="bg-red-50 rounded-2xl px-4 py-3">
                        <p class="text-xs font-bold text-red-600 uppercase tracking-wider mb-1.5">Tidak Boleh Digunakan
                            Bila</p>
                        <div class="flex flex-wrap gap-1.5">
                            <template x-for="ci in (drawerData?.contraindications || [])" :key="ci"><span
                                    class="text-sm bg-red-100 text-red-700 px-2.5 py-1 rounded-full"
                                    x-text="ci"></span></template>
                        </div>
                    </div>
                    <div x-show="drawerData?.__drug?.db_url" class="pt-2">
                        <a :href="drawerData?.__drug?.db_url" target="_blank"
                            class="inline-flex items-center gap-2 text-sm font-semibold text-primary bg-primary/10 px-4 py-2.5 rounded-full hover:bg-primary/20 transition-colors">
                            Lihat halaman obat lengkap
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                    <p x-show="drawerData?.reference_source" class="text-xs text-slate-400 pt-1"
                        x-text="'Sumber: ' + drawerData?.reference_source"></p>
                </div>
            </div>
        </div>{{-- /drawer --}}

        {{-- ══════════════ KIOSK POPUP ══════════════ --}}
        <div x-show="showKioskPopup" x-cloak x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" class="fixed inset-0 z-[100] flex items-center justify-center p-4"
            style="background: rgba(11,31,36,0.45); backdrop-filter: blur(6px);">
            <div class="glass-card rounded-2xl w-full max-w-sm p-6 text-center"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-90"
                x-transition:enter-end="opacity-100 scale-100">
                <div class="relative w-20 h-20 mx-auto mb-4">
                    <svg class="w-20 h-20 -rotate-90" viewBox="0 0 100 100">
                        <circle cx="50" cy="50" r="44" fill="none" stroke="#e2f0f0" stroke-width="8" />
                        <circle cx="50" cy="50" r="44" fill="none" stroke="#3EAEB1" stroke-width="8"
                            stroke-linecap="round" :stroke-dasharray="276.46"
                            :stroke-dashoffset="276.46 - (276.46 * kioskCountdown / kioskDuration)"
                            style="transition: stroke-dashoffset 1s linear;" />
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-2xl font-bold text-primary" x-text="kioskCountdown"></span>
                    </div>
                </div>
                <h3 class="text-lg font-bold text-ink-900 mb-1">Sudah selesai membaca?</h3>
                <p class="text-sm text-ink-500 mb-5 leading-relaxed">
                    Halaman akan otomatis kembali ke awal dalam <span class="font-bold text-primary"
                        x-text="kioskCountdown"></span> detik
                    agar siap untuk pengguna berikutnya.
                </p>
                <div class="flex gap-3">
                    <button @click="snoozeKiosk()"
                        class="flex-1 px-4 py-3 rounded-full text-sm font-bold bg-slate-100 text-slate-700 hover:bg-slate-200 transition-colors">Tunggu</button>
                    <button @click="resetKiosk()"
                        class="flex-1 px-4 py-3 rounded-full text-sm font-bold text-white transition-all"
                        style="background: linear-gradient(135deg,#3EAEB1,#2d8a8d); box-shadow: 0 4px 16px rgba(62,174,177,0.35);">Cukup</button>
                </div>
                <p class="text-[10px] text-ink-400 mt-4">"Tunggu" memberi 1 menit tambahan · "Cukup" langsung mulai dari
                    awal</p>
            </div>
        </div>{{-- /kiosk --}}

    </section>{{-- /medicheck-output-page --}}

</div>{{-- /ceksehat-root --}}