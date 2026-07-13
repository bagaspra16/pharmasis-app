{{-- HERO — Cek Sehat AI · Screening Further-Question Flow (Bahasa Indonesia) --}}
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
            <div class="absolute right-[-10%] top-[-10%] w-[40%] h-[40%] rounded-full bg-teal-400/20 blur-[120px]"></div>
            <div class="absolute left-[10%] bottom-[-10%] w-[30%] h-[30%] rounded-full bg-emerald-400/20 blur-[100px]"></div>
            <div class="absolute left-[-10%] top-[30%] w-[30%] h-[30%] rounded-full bg-primary/15 blur-[120px]"></div>
        </div>

        {{-- Content --}}
        <div class="relative z-10 max-w-3xl mx-auto px-4 py-10 md:py-16 text-center w-full">

            {{-- Badge --}}
            <div class="inline-flex items-center gap-2 text-[11px] font-medium px-5 py-2 rounded-full mb-7 glass uppercase tracking-[0.14em]"
                style="color:#0d4f52;">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
                Screening Gejala dengan AI
                <span class="w-px h-3 bg-slate-300/60 mx-0.5"></span>
                <span class="font-display normal-case tracking-normal text-primary text-base leading-none">Cek Sehat</span>
            </div>

            {{-- Headline --}}
            <h1 class="font-display text-5xl sm:text-6xl lg:text-7xl text-ink-900 mb-5 leading-[1.02] tracking-tight">
                Kenali
                <span class="italic"
                    style="background: linear-gradient(135deg,#3EAEB1 10%,#1a7a7d 90%); -webkit-background-clip:text; -webkit-text-fill-color:transparent; padding-right: 0.05em;">kesehatan</span>
                Anda.
            </h1>
            <p class="text-sm md:text-base text-ink-500 mb-4 max-w-lg mx-auto leading-relaxed">
                Ceritakan keluhan Anda, jawab beberapa pertanyaan singkat dari AI, lalu terima kesimpulan yang
                disesuaikan — apakah Anda seorang tenaga kesehatan atau pasien.
            </p>

            {{-- Cara pakai — 3 langkah --}}
            <div class="flex flex-wrap items-center justify-center gap-2 mb-8 text-[11px] font-medium">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full glass-soft text-primary-dark">
                    <span class="w-4 h-4 rounded-full bg-primary/15 text-primary text-[9px] font-bold flex items-center justify-center">1</span>
                    Ceritakan keluhan
                </span>
                <svg class="w-3 h-3 text-ink-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full glass-soft text-primary-dark">
                    <span class="w-4 h-4 rounded-full bg-primary/15 text-primary text-[9px] font-bold flex items-center justify-center">2</span>
                    Jawab screening
                </span>
                <svg class="w-3 h-3 text-ink-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full glass-soft text-primary-dark">
                    <span class="w-4 h-4 rounded-full bg-primary/15 text-primary text-[9px] font-bold flex items-center justify-center">3</span>
                    Terima kesimpulan
                </span>
            </div>

            {{-- ══════════════ INTAKE: first prompt (text / voice / search) ══════════════ --}}
            <div x-show="phase === 'intake'" class="w-full max-w-2xl mx-auto flex flex-col items-center mt-4 mb-8 relative z-20">

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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 1a3 3 0 00-3 3v8a3 3 0 006 0V4a3 3 0 00-3-3z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 10v2a7 7 0 01-14 0v-2" />
                        </svg>
                        Suara
                    </button>
                    <button @click="activeTab = 'search'"
                        :class="activeTab === 'search' ? 'text-white shadow-lg' : 'text-slate-500 hover:text-slate-700 hover:bg-white/60'"
                        :style="activeTab === 'search' ? 'background: linear-gradient(135deg,#3EAEB1,#61BACA); box-shadow: 0 4px 12px rgba(62,174,177,0.4);' : ''"
                        class="px-5 py-2 rounded-full text-xs font-semibold transition-all duration-300 flex items-center gap-1.5">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
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

                        <button @click="toggleRecording()" :disabled="screening"
                            :class="recording ? 'bg-red-500 shadow-red-400/40 scale-110' : 'bg-primary shadow-primary/40 hover:scale-105'"
                            class="relative w-24 h-24 sm:w-28 sm:h-28 rounded-full text-white shadow-xl transition-all duration-300 flex items-center justify-center group focus:outline-none">
                            <template x-if="recording">
                                <div>
                                    <span class="absolute inset-0 rounded-full bg-red-400/30 animate-ping"></span>
                                    <span class="absolute -inset-3 rounded-full bg-red-400/15 animate-pulse"></span>
                                </div>
                            </template>
                            <svg x-show="!recording && !screening" class="w-10 h-10 sm:w-12 sm:h-12 drop-shadow-md"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 1a3 3 0 00-3 3v8a3 3 0 006 0V4a3 3 0 00-3-3z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 10v2a7 7 0 01-14 0v-2" />
                                <line x1="12" y1="19" x2="12" y2="23" stroke-width="1.8" stroke-linecap="round" />
                                <line x1="8" y1="23" x2="16" y2="23" stroke-width="1.8" stroke-linecap="round" />
                            </svg>
                            <svg x-show="recording" x-cloak class="w-10 h-10 sm:w-12 sm:h-12 drop-shadow-md" fill="currentColor" viewBox="0 0 24 24">
                                <rect x="6" y="6" width="12" height="12" rx="2" />
                            </svg>
                            <svg x-show="screening" x-cloak class="w-10 h-10 sm:w-12 sm:h-12 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                            </svg>
                        </button>
                        <p class="text-xs text-slate-500 mt-4 font-medium"
                            x-text="recording ? 'Sedang merekam — ketuk untuk berhenti' : (screening ? 'Menyiapkan pertanyaan...' : 'Ketuk untuk bicara — bahasa apa saja')"></p>

                        <div x-show="recording" x-cloak class="mt-4 mx-auto w-full max-w-sm">
                            <div class="rounded-xl px-4 py-3"
                                style="background: rgba(255,255,255,0.82); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.9); box-shadow: 0 4px 20px rgba(62,174,177,0.12);">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-red-500 animate-ping"></span>
                                        <span class="text-[10px] text-red-500 font-bold tracking-widest uppercase">Merekam</span>
                                    </span>
                                    <span class="text-[10px] text-slate-400">Transkripsi suara aktif</span>
                                </div>
                                <canvas x-ref="waveCanvas" width="320" height="48" class="w-full h-12 rounded-lg bg-slate-50/60"></canvas>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Text --}}
                    <div x-show="activeTab === 'text'" x-cloak
                        x-transition:enter="transition ease-out duration-300 delay-100"
                        x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-4"
                        class="absolute inset-x-0 top-0 flex flex-col items-center px-4 w-full max-w-2xl mx-auto">
                        <div class="w-full rounded-3xl p-3"
                            style="background: rgba(255,255,255,0.85); backdrop-filter: blur(24px); border: 1px solid rgba(255,255,255,0.95); box-shadow: 0 8px 32px rgba(62,174,177,0.15), 0 1px 0 rgba(255,255,255,0.9) inset;">
                            <div class="flex flex-col">
                                <textarea x-ref="symptomTextarea" x-model="symptoms"
                                    @keydown.enter="handleTextareaEnter($event)" @input="autoResizeTextarea()"
                                    placeholder="Ceritakan keluhan Anda secara detail... Contoh: Saya batuk kering dan demam 38°C sejak 2 hari lalu, disertai sakit kepala dan lemas."
                                    class="flex-1 bg-transparent text-slate-900 placeholder-slate-400/80 font-medium text-base px-5 py-4 rounded-3xl focus:outline-none focus:ring-0 resize-none border-none min-h-[120px] max-h-[280px] [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar-thumb]:bg-teal-600/20 [&::-webkit-scrollbar-thumb]:rounded-full hover:[&::-webkit-scrollbar-thumb]:bg-teal-600/40"
                                    rows="3"></textarea>
                                <div class="flex items-center justify-between gap-3 pt-3 px-3 border-t border-slate-200/50 mt-1">
                                    <span class="text-xs text-slate-400 hidden sm:inline flex-1">Ceritakan keluhan Anda... Tekan Enter untuk mulai screening.</span>
                                    <button @click="startScreening()" :disabled="screening || !symptoms.trim()"
                                        class="flex-shrink-0 ml-auto text-white font-bold h-10 w-10 sm:h-11 sm:w-11 rounded-full flex items-center justify-center transition-all disabled:opacity-40 disabled:cursor-not-allowed hover:scale-105"
                                        style="background: linear-gradient(135deg,#0d9488,#0f766e); box-shadow: 0 4px 16px rgba(13,148,136,0.35);">
                                        <svg x-show="!screening" class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 12h14M12 5l7 7-7 7" />
                                        </svg>
                                        <svg x-show="screening" class="w-4 h-4 sm:w-5 sm:h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 3. Search --}}
                    <div x-show="activeTab === 'search'" x-cloak
                        x-transition:enter="transition ease-out duration-300 delay-100"
                        x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-4"
                        class="absolute inset-x-0 top-0 flex flex-col items-center px-4 w-full max-w-lg mx-auto"
                        x-data="heroSearchId()">
                        <div class="w-full max-w-2xl rounded-full p-2 relative z-30 flex items-center gap-2"
                            style="background: rgba(255,255,255,0.82); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.9); box-shadow: 0 4px 24px rgba(62,174,177,0.12), 0 1px 0 rgba(255,255,255,0.9) inset;">
                            <div class="flex-1 flex items-center px-4" @keydown.escape="open=false; results=[]" @click.outside="open=false">
                                <svg class="w-5 h-5 text-slate-400 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
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
                                        <div class="w-8 h-8 rounded-lg bg-teal-500/10 flex items-center justify-center flex-shrink-0">
                                            <span class="text-sm font-bold text-teal-600" x-text="drug.name?.charAt(0)||'?'"></span></div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-semibold text-slate-900 truncate" x-text="drug.name"></p>
                                            <p class="text-[10px] text-slate-500 truncate" x-text="[drug.generic_name,drug.drug_class].filter(Boolean).join(' · ')"></p>
                                        </div>
                                    </a>
                                </template>
                            </div>
                        </div>
                    </div>

                </div>
            </div>{{-- /intake --}}

            {{-- Error --}}
            <div x-show="error" x-cloak
                class="mt-4 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl max-w-lg mx-auto"
                x-text="error"></div>

            {{-- ══════════════ SCREENING PANEL: questions + concluding ══════════════ --}}
            <div id="screening-panel" x-show="phase === 'questions' || phase === 'concluding'" x-cloak
                class="mt-8 max-w-xl mx-auto text-left"
                x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-4"
                x-transition:enter-end="opacity-100 translate-y-0">

                {{-- ── QUESTIONS ── --}}
                <div x-show="phase === 'questions'" class="rounded-3xl overflow-hidden"
                    style="background: rgba(255,255,255,0.9); backdrop-filter: blur(24px); border: 1px solid rgba(255,255,255,0.95); box-shadow: 0 8px 40px rgba(62,174,177,0.16), 0 1px 0 rgba(255,255,255,0.9) inset;">

                    {{-- Progress header --}}
                    <div class="px-6 pt-5 pb-4" style="border-bottom: 1px solid rgba(62,174,177,0.1);">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-[11px] font-bold text-primary uppercase tracking-widest">Screening Gejala</span>
                            <span class="text-[11px] font-semibold text-slate-400"
                                x-text="`Pertanyaan ${currentQ + 1} dari ${questions.length}`"></span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <template x-for="(q, i) in questions" :key="q.id">
                                <div class="h-1.5 flex-1 rounded-full transition-all duration-300"
                                    :class="i < currentQ ? 'bg-emerald-400' : (i === currentQ ? 'bg-primary' : 'bg-slate-200')"></div>
                            </template>
                        </div>
                    </div>

                    {{-- Recap of the initial complaint --}}
                    <div class="px-6 pt-4">
                        <div class="bg-primary/5 rounded-2xl px-4 py-3">
                            <p class="text-[10px] font-bold text-primary uppercase tracking-widest mb-1">Keluhan Anda</p>
                            <p class="text-sm text-slate-700 italic leading-snug line-clamp-3" x-text="firstPrompt"></p>
                        </div>
                    </div>

                    {{-- The current question --}}
                    <div class="px-6 py-5" x-show="currentQuestion">
                        {{-- Standard (non-role) question --}}
                        <template x-if="currentQuestion && !currentQuestion.is_role">
                            <div>
                                <h3 class="text-lg font-bold text-slate-800 leading-snug mb-1" x-text="currentQuestion.question"></h3>
                                <p x-show="currentQuestion.hint" class="text-xs text-slate-400 mb-4" x-text="currentQuestion.hint"></p>

                                {{-- Quick-choice chips --}}
                                <div x-show="currentQuestion.choices && currentQuestion.choices.length" class="flex flex-wrap gap-2 mb-4">
                                    <template x-for="choice in (currentQuestion.choices || [])" :key="choice">
                                        <button type="button" @click="toggleChip(currentQuestion.id, choice)"
                                            class="text-sm font-medium px-4 py-2 rounded-full border transition-all active:scale-95"
                                            :class="chipSelected(currentQuestion.id, choice)
                                                ? 'bg-primary text-white border-primary shadow-md shadow-primary/25'
                                                : 'bg-white text-slate-600 border-slate-200 hover:border-primary/40 hover:bg-primary/5'"
                                            x-text="choice"></button>
                                    </template>
                                    <span x-show="currentQuestion.allow_multiple" class="w-full text-[10px] text-slate-400 mt-0.5">Bisa pilih lebih dari satu</span>
                                </div>

                                {{-- Free text --}}
                                <div x-show="currentQuestion.free_text !== false">
                                    <textarea x-model="answers[currentQuestion.id].text"
                                        placeholder="Tambahkan detail Anda di sini (opsional)..."
                                        rows="2"
                                        class="w-full bg-white text-slate-900 placeholder-slate-400 text-sm px-4 py-3 rounded-2xl border border-slate-200 focus:outline-none focus:border-primary/50 focus:ring-2 focus:ring-primary/10 resize-none"></textarea>
                                </div>

                                {{-- Nav --}}
                                <div class="flex items-center justify-between gap-3 mt-5">
                                    <button type="button" @click="prevQ()" x-show="currentQ > 0"
                                        class="text-sm font-semibold text-slate-500 hover:text-slate-700 px-4 py-2.5 rounded-full hover:bg-slate-100 transition-colors flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                        </svg>
                                        Kembali
                                    </button>
                                    <div x-show="currentQ === 0" class="flex-1"></div>
                                    <button type="button" @click="nextQ()"
                                        class="ml-auto text-sm font-bold text-white px-6 py-2.5 rounded-full transition-all hover:scale-105 active:scale-95 flex items-center gap-1.5"
                                        style="background: linear-gradient(135deg,#0d9488,#0f766e); box-shadow: 0 4px 16px rgba(13,148,136,0.3);">
                                        <span x-text="currentAnswered() ? 'Lanjut' : 'Lewati'"></span>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </template>

                        {{-- Role question (final) --}}
                        <template x-if="currentQuestion && currentQuestion.is_role">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="w-6 h-6 rounded-lg bg-primary/15 text-primary flex items-center justify-center flex-shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </span>
                                    <span class="text-[10px] font-bold text-primary uppercase tracking-widest">Langkah terakhir</span>
                                </div>
                                <h3 class="text-lg font-bold text-slate-800 leading-snug mb-1" x-text="currentQuestion.question"></h3>
                                <p x-show="currentQuestion.hint" class="text-xs text-slate-400 mb-4" x-text="currentQuestion.hint"></p>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-4">
                                    {{-- Doctor card --}}
                                    <button type="button" @click="selectRole(currentQuestion.choices[0], 0)" :disabled="analyzing"
                                        class="text-left rounded-2xl p-5 border-2 border-slate-200 hover:border-primary bg-white hover:bg-primary/5 transition-all active:scale-[0.98] group disabled:opacity-50">
                                        <div class="w-11 h-11 rounded-2xl bg-primary/15 text-primary flex items-center justify-center mb-3 group-hover:scale-105 transition-transform">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7h-3V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2zM12 11v4m-2-2h4" />
                                            </svg>
                                        </div>
                                        <p class="text-sm font-bold text-slate-800 mb-0.5" x-text="currentQuestion.choices[0]"></p>
                                        <p class="text-xs text-slate-500 leading-snug">Kesimpulan klinis mendetail: diagnosis banding, regimen obat, interaksi, dan penatalaksanaan.</p>
                                    </button>
                                    {{-- Patient card --}}
                                    <button type="button" @click="selectRole(currentQuestion.choices[1], 1)" :disabled="analyzing"
                                        class="text-left rounded-2xl p-5 border-2 border-slate-200 hover:border-emerald-400 bg-white hover:bg-emerald-50 transition-all active:scale-[0.98] group disabled:opacity-50">
                                        <div class="w-11 h-11 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center mb-3 group-hover:scale-105 transition-transform">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                            </svg>
                                        </div>
                                        <p class="text-sm font-bold text-slate-800 mb-0.5" x-text="currentQuestion.choices[1]"></p>
                                        <p class="text-xs text-slate-500 leading-snug">Penjelasan yang menenangkan, penanganan awal, pola hidup, dan arahan ke dokter.</p>
                                    </button>
                                </div>

                                <div class="flex items-center justify-start mt-5">
                                    <button type="button" @click="prevQ()"
                                        class="text-sm font-semibold text-slate-500 hover:text-slate-700 px-4 py-2.5 rounded-full hover:bg-slate-100 transition-colors flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                        </svg>
                                        Kembali
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- ── CONCLUDING (processing) ── --}}
                <div x-show="phase === 'concluding'" x-cloak class="rounded-2xl overflow-hidden"
                    style="background: rgba(255,255,255,0.82); backdrop-filter: blur(24px); border: 1px solid rgba(255,255,255,0.92); box-shadow: 0 8px 40px rgba(62,174,177,0.14), 0 1px 0 rgba(255,255,255,0.9) inset;">
                    <div class="px-5 py-3.5 flex items-center gap-2"
                        style="border-bottom: 1px solid rgba(62,174,177,0.1); background: linear-gradient(90deg, rgba(62,174,177,0.06), transparent);">
                        <svg class="w-4 h-4 text-primary animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-20" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" />
                            <path class="opacity-80" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                        </svg>
                        <span class="text-xs font-bold text-slate-700 uppercase tracking-widest">Menyusun kesimpulan...</span>
                    </div>
                    <div class="px-5 py-4 space-y-3">
                        <template x-for="(step, idx) in concludeSteps" :key="step.id">
                            <div class="flex items-start gap-3 transition-all duration-300"
                                :class="completedSteps.includes(step.id) ? 'opacity-100' : (currentStep === idx ? 'opacity-100' : 'opacity-35')">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5 transition-all duration-300"
                                    :class="completedSteps.includes(step.id) ? 'bg-emerald-100' : (currentStep === idx ? 'bg-primary/15' : 'bg-slate-100')">
                                    <template x-if="completedSteps.includes(step.id)">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </template>
                                    <template x-if="!completedSteps.includes(step.id) && currentStep === idx">
                                        <svg class="w-3.5 h-3.5 text-primary animate-spin" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-20" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" />
                                            <path class="opacity-80" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
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
                                        class="text-[10px] text-primary mt-0.5 leading-relaxed" x-text="step.detail"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                    <div class="px-5 py-2.5" style="border-top: 1px solid rgba(62,174,177,0.1); background: rgba(248,254,254,0.6);">
                        <p class="text-[10px] text-slate-400 text-center">Mohon tunggu, sedang menyiapkan kesimpulan yang disesuaikan dengan peran Anda...</p>
                    </div>
                </div>

            </div>{{-- /screening-panel --}}

        </div>{{-- /inner content div --}}
    </section>{{-- /ceksehat-section --}}


    {{-- ═══════════════════════════════════════════════════════════════════════ --}}
    {{-- ════════════════════ HALAMAN OUTPUT — KESIMPULAN ═══════════════════════ --}}
    {{-- ═══════════════════════════════════════════════════════════════════════ --}}
    <section x-show="phase === 'result'" x-cloak id="medicheck-output-page"
        x-transition:enter="transition ease-out duration-700" x-transition:enter-start="opacity-0 translate-y-6"
        x-transition:enter-end="opacity-100 translate-y-0"
        class="relative min-h-screen pt-12 pb-24 border-t border-slate-200"
        style="background: linear-gradient(180deg, #F0FDF4 0%, #FFFFFF 15%, #F8FAFC 100%); outline: none;">

        {{-- ── Output Header Bar ── --}}
        <div class="max-w-5xl mx-auto px-4 pt-4 pb-2 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <a href="/" class="flex items-center gap-2 group">
                    <img src="/images/icon.png" alt="Pharmasis" class="w-8 h-8 rounded-xl object-contain"
                        style="box-shadow: 0 3px 10px rgba(13,148,136,0.25);" />
                    <div class="leading-tight">
                        <p class="text-sm font-bold text-slate-800 group-hover:text-primary transition-colors">Pharmasis</p>
                        <p class="text-[10px] text-slate-400 font-medium">Cek Sehat AI</p>
                    </div>
                </a>
                <span class="w-px h-7 bg-slate-200 mx-1"></span>
                <span class="text-[11px] font-semibold px-2.5 py-1 rounded-full"
                    :class="isDoctor ? 'text-primary bg-primary/10' : 'text-emerald-700 bg-emerald-100'"
                    x-text="isDoctor ? 'Laporan Klinis' : 'Laporan Kesehatan'"></span>
            </div>
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

            {{-- ── Keluhan + Screening recap ── --}}
            <div class="rounded-2xl p-5"
                style="background: rgba(255,255,255,0.92); backdrop-filter: blur(20px); border: 1px solid rgba(62,174,177,0.12); box-shadow: 0 4px 24px rgba(62,174,177,0.08);">
                <p class="text-[11px] font-bold text-primary uppercase tracking-widest mb-2 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    Keluhan yang Anda ceritakan
                </p>
                <p class="text-base text-slate-700 italic leading-relaxed mb-3" x-text='"« " + (result?.symptoms || "") + " »"'></p>
                <div x-show="result?.qa?.length" class="pt-3 border-t border-slate-100 space-y-2">
                    <template x-for="(pair, i) in (result?.qa || [])" :key="i">
                        <div class="text-sm">
                            <p class="font-semibold text-slate-600" x-text="pair.question"></p>
                            <p class="text-slate-500" x-text="'↳ ' + pair.answer"></p>
                        </div>
                    </template>
                </div>
            </div>

            {{-- ════════════════════════════════════════════════════════════════ --}}
            {{-- ════════════════════════ DOCTOR BRANCH ══════════════════════════ --}}
            {{-- ════════════════════════════════════════════════════════════════ --}}
            <template x-if="isDoctor && conclusion">
                <div class="space-y-6">

                    {{-- Differential diagnosis --}}
                    <div x-show="conclusion.differential?.length" class="glass-card rounded-3xl overflow-hidden">
                        <div class="px-6 py-4 flex items-center gap-3"
                            style="border-bottom: 1px solid rgba(62,174,177,0.1); background: linear-gradient(90deg,rgba(62,174,177,0.06),transparent);">
                            <span class="w-10 h-10 bg-primary/15 rounded-2xl flex items-center justify-center text-primary flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </span>
                            <div>
                                <h3 class="text-lg font-bold text-slate-800">Diagnosis Banding</h3>
                                <p class="text-xs text-slate-400">Kemungkinan diferensial berdasarkan hasil screening</p>
                            </div>
                        </div>
                        <div class="p-6 space-y-2">
                            <template x-for="cond in (conclusion.differential || [])" :key="cond.name">
                                <div class="flex items-start gap-3 p-3 rounded-2xl bg-white/80 border border-slate-100">
                                    <div class="w-1.5 min-h-[2rem] rounded-full flex-shrink-0 mt-0.5"
                                        :class="(cond.likelihood === 'High' || cond.likelihood === 'Tinggi') ? 'bg-red-400' : ((cond.likelihood === 'Moderate' || cond.likelihood === 'Sedang') ? 'bg-amber-400' : 'bg-emerald-400')"></div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap mb-0.5">
                                            <span class="text-[10px] font-black uppercase tracking-wider px-1.5 py-0.5 rounded"
                                                :class="(cond.likelihood === 'High' || cond.likelihood === 'Tinggi') ? 'bg-red-100 text-red-700' : ((cond.likelihood === 'Moderate' || cond.likelihood === 'Sedang') ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700')"
                                                x-text="likelihoodLabel(cond.likelihood)"></span>
                                            <p class="text-sm font-bold text-slate-800" x-text="cond.name"></p>
                                        </div>
                                        <p class="text-xs text-slate-500 leading-snug" x-text="cond.rationale"></p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Drug regimen --}}
                    <div x-show="conclusion.drugs?.length" class="glass-card rounded-3xl overflow-hidden">
                        <div class="px-6 py-4 flex items-center gap-3"
                            style="border-bottom: 1px solid rgba(62,174,177,0.1); background: linear-gradient(90deg,rgba(62,174,177,0.06),transparent);">
                            <span class="w-10 h-10 bg-primary/15 rounded-2xl flex items-center justify-center text-primary flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                </svg>
                            </span>
                            <div>
                                <h3 class="text-lg font-bold text-slate-800">Regimen Farmakologi</h3>
                                <p class="text-xs text-slate-400">Ketuk nama obat untuk detail lengkap</p>
                            </div>
                        </div>
                        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <template x-for="(d, di) in (conclusion.drugs || [])" :key="di">
                                <button type="button"
                                    @click="openDrawer(d.name, { mechanism: d.mechanism, how_to_take: '', duration: d.duration, route: d.route, contraindications: d.contraindications, side_effects: [], reference_source: d.notes, __drug: { drug_class: d.drug_class, dosage: [d.dose, d.frequency].filter(Boolean).join(' · '), db_url: d.db_url } })"
                                    class="text-left bg-white rounded-2xl border border-slate-100 p-4 shadow-sm hover:shadow-md hover:border-primary/30 transition-all group">
                                    <div class="flex items-center gap-2 flex-wrap mb-1">
                                        <p class="text-base font-bold text-slate-900" x-text="d.name"></p>
                                        <span x-show="d.drug_class" class="text-[10px] bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded-full font-bold uppercase" x-text="d.drug_class"></span>
                                    </div>
                                    <p class="text-xs text-slate-500 mb-1.5" x-text="d.generic"></p>
                                    <p class="text-sm font-semibold text-slate-700" x-text="[d.dose, d.frequency, d.duration].filter(Boolean).join(' · ')"></p>
                                    <p x-show="d.route" class="text-xs text-slate-400 mt-0.5 capitalize" x-text="'Rute: ' + d.route"></p>
                                    <p class="text-xs font-semibold text-primary mt-2 group-hover:translate-x-0.5 transition-transform">Ketuk untuk detail →</p>
                                </button>
                            </template>
                        </div>
                    </div>

                    {{-- Interactions --}}
                    <div x-show="conclusion.interactions?.length" class="glass-card rounded-3xl p-6">
                        <h3 class="text-lg font-bold text-slate-800 mb-3">Interaksi Obat</h3>
                        <div class="space-y-2">
                            <template x-for="i in (conclusion.interactions || [])" :key="i.drug_a + i.drug_b">
                                <div class="flex items-start gap-2 p-3 rounded-xl"
                                    :class="(i.severity==='Avoid'||i.severity==='Hindari') ? 'bg-red-50' : ((i.severity==='Caution'||i.severity==='Hati-hati') ? 'bg-amber-50' : 'bg-slate-50')">
                                    <span class="text-xs font-bold px-1.5 py-0.5 rounded flex-shrink-0 mt-0.5"
                                        :class="(i.severity==='Avoid'||i.severity==='Hindari') ? 'bg-red-200 text-red-800' : ((i.severity==='Caution'||i.severity==='Hati-hati') ? 'bg-amber-200 text-amber-800' : 'bg-slate-200 text-slate-700')"
                                        x-text="severityLabel(i.severity)"></span>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-slate-800" x-text="`${i.drug_a} + ${i.drug_b}`"></p>
                                        <p class="text-xs text-slate-500" x-text="i.effect"></p>
                                        <p x-show="i.management" class="text-xs text-primary mt-0.5" x-text="'→ Manajemen: ' + i.management"></p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Management + Red flags --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div x-show="conclusion.management?.length" class="glass-card rounded-3xl p-6">
                            <h3 class="text-base font-bold text-slate-800 mb-3">Penatalaksanaan</h3>
                            <ul class="space-y-2">
                                <template x-for="(m, mi) in (conclusion.management || [])" :key="mi">
                                    <li class="flex items-start gap-2 text-sm text-slate-600">
                                        <span class="w-1.5 h-1.5 rounded-full bg-primary flex-shrink-0 mt-1.5"></span>
                                        <span x-text="m"></span>
                                    </li>
                                </template>
                            </ul>
                        </div>
                        <div x-show="conclusion.red_flags?.length" class="rounded-3xl p-6 bg-red-50 border border-red-100">
                            <h3 class="text-base font-bold text-red-700 mb-3 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-lg bg-red-500 text-white flex items-center justify-center font-black">!</span>
                                Red Flags
                            </h3>
                            <ul class="space-y-2">
                                <template x-for="(w, wi) in (conclusion.red_flags || [])" :key="wi">
                                    <li class="flex items-start gap-2 text-sm text-red-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500 flex-shrink-0 mt-1.5"></span>
                                        <span x-text="w"></span>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </div>

                    {{-- Clinical summary + ICD-10 --}}
                    <div x-show="conclusion.clinical_summary || conclusion.icd10" class="glass-card rounded-3xl p-6">
                        <div class="flex items-center justify-between flex-wrap gap-2 mb-2">
                            <h3 class="text-base font-bold text-slate-800">Ringkasan Klinis</h3>
                            <span x-show="conclusion.icd10" class="text-xs font-mono font-semibold text-slate-600 bg-slate-100 px-2.5 py-1 rounded-lg">
                                ICD-10: <span x-text="conclusion.icd10"></span>
                            </span>
                        </div>
                        <p class="text-sm text-slate-600 leading-relaxed" x-text="conclusion.clinical_summary"></p>
                    </div>

                    {{-- Clinical PDF export CTA --}}
                    <div class="rounded-2xl p-4 border border-dashed border-slate-200 bg-slate-50/60 flex items-center justify-between flex-wrap gap-3">
                        <div class="flex-1 min-w-[12rem]">
                            <p class="text-sm font-bold text-slate-700 mb-0.5">Dokumen klinis</p>
                            <p class="text-xs text-slate-500">Ekspor ringkasan formal ini untuk rekam medis atau rujukan.</p>
                        </div>
                        <button @click="savePdf()"
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full text-sm font-bold text-white transition-all hover:scale-105"
                            style="background: linear-gradient(135deg,#0d4f52,#1a7a7d);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            PDF Klinis
                        </button>
                    </div>
                </div>
            </template>

            {{-- ════════════════════════════════════════════════════════════════ --}}
            {{-- ════════════════════════ PATIENT BRANCH ═════════════════════════ --}}
            {{-- ════════════════════════════════════════════════════════════════ --}}
            <template x-if="!isDoctor && conclusion">
                <div class="space-y-6">

                    {{-- Reassurance banner --}}
                    <div x-show="conclusion.reassurance" class="rounded-3xl p-6 relative overflow-hidden"
                        style="background: linear-gradient(135deg, rgba(240,253,250,0.9), rgba(236,253,245,0.9)); border: 1px solid rgba(94,234,212,0.4);">
                        <div class="absolute -top-8 -right-8 w-32 h-32 rounded-full bg-emerald-300/20 blur-2xl pointer-events-none"></div>
                        <div class="flex items-start gap-3 relative">
                            <span class="w-11 h-11 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                            </span>
                            <p class="text-lg text-emerald-900 leading-relaxed font-medium" x-text="conclusion.reassurance"></p>
                        </div>
                    </div>

                    {{-- Assumed condition --}}
                    <div x-show="conclusion.assumed_condition?.name" class="glass-card rounded-3xl p-6">
                        <p class="text-[11px] font-bold text-primary uppercase tracking-widest mb-1.5">Kemungkinan Kondisi</p>
                        <h3 class="text-xl font-bold text-slate-800 mb-2" x-text="conclusion.assumed_condition?.name"></h3>
                        <p class="text-sm text-slate-600 leading-relaxed" x-text="conclusion.assumed_condition?.plain_explanation"></p>
                    </div>

                    {{-- Self-care + Lifestyle --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div x-show="conclusion.self_care?.length" class="glass-card rounded-3xl p-6">
                            <h3 class="text-base font-bold text-slate-800 mb-3 flex items-center gap-2">
                                <span class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </span>
                                Penanganan Awal
                            </h3>
                            <ul class="space-y-2">
                                <template x-for="(s, si) in (conclusion.self_care || [])" :key="si">
                                    <li class="flex items-start gap-2 text-sm text-slate-600">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 flex-shrink-0 mt-1.5"></span>
                                        <span x-text="s"></span>
                                    </li>
                                </template>
                            </ul>
                        </div>
                        <div x-show="conclusion.lifestyle?.length" class="glass-card rounded-3xl p-6">
                            <h3 class="text-base font-bold text-slate-800 mb-3 flex items-center gap-2">
                                <span class="w-8 h-8 rounded-xl bg-teal-100 text-teal-600 flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                </span>
                                Pengaturan Pola Hidup
                            </h3>
                            <ul class="space-y-2">
                                <template x-for="(s, si) in (conclusion.lifestyle || [])" :key="si">
                                    <li class="flex items-start gap-2 text-sm text-slate-600">
                                        <span class="w-1.5 h-1.5 rounded-full bg-teal-400 flex-shrink-0 mt-1.5"></span>
                                        <span x-text="s"></span>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </div>

                    {{-- When to seek care --}}
                    <div x-show="conclusion.when_to_seek_care" class="rounded-3xl p-6 bg-amber-50 border border-amber-200">
                        <h3 class="text-base font-bold text-amber-800 mb-2 flex items-center gap-2">
                            <span class="w-7 h-7 rounded-lg bg-amber-500 text-white flex items-center justify-center font-black">!</span>
                            Kapan Harus ke Fasilitas Kesehatan
                        </h3>
                        <p class="text-sm text-amber-900 leading-relaxed" x-text="conclusion.when_to_seek_care"></p>
                    </div>

                    {{-- Which doctor + What to ask --}}
                    <div class="glass-card rounded-3xl p-6">
                        <div x-show="conclusion.which_doctor" class="mb-4 pb-4 border-b border-slate-100">
                            <p class="text-[11px] font-bold text-primary uppercase tracking-widest mb-1">Sebaiknya Temui</p>
                            <p class="text-lg font-bold text-slate-800" x-text="conclusion.which_doctor"></p>
                        </div>
                        <div x-show="conclusion.what_to_ask_doctor?.length">
                            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2">Yang Perlu Anda Tanyakan ke Dokter</p>
                            <ul class="space-y-2">
                                <template x-for="(q, qi) in (conclusion.what_to_ask_doctor || [])" :key="qi">
                                    <li class="flex items-start gap-2 text-sm text-slate-600">
                                        <span class="w-5 h-5 rounded-full bg-primary/10 text-primary text-[10px] font-bold flex items-center justify-center flex-shrink-0 mt-0.5" x-text="qi + 1"></span>
                                        <span x-text="q"></span>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </div>

                    {{-- Disclaimer --}}
                    <div x-show="conclusion.disclaimer" class="px-2">
                        <p class="text-[11px] text-slate-400 italic leading-relaxed text-center" x-text="conclusion.disclaimer"></p>
                    </div>
                </div>
            </template>

            {{-- ════════════════ FASKES TERDEKAT (patient branch) ════════════════ --}}
            <div id="mc-faskes" class="glass-card rounded-3xl overflow-hidden" x-show="nearbyLoading || nearbyProviders">
                <div class="px-6 py-4 flex items-center justify-between"
                    style="border-bottom: 1px solid rgba(62,174,177,0.1); background: linear-gradient(90deg,rgba(62,174,177,0.06),transparent);">
                    <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <span class="w-9 h-9 bg-primary/15 rounded-2xl flex items-center justify-center text-lg flex-shrink-0">📍</span>
                        Fasilitas Kesehatan Terdekat
                    </h3>
                    <div x-show="nearbyLoading" class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-primary animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-20" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" />
                            <path class="opacity-80" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
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
                            <div x-show="nearbyProviders.providers?.length" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <template x-for="provider in (nearbyProviders.providers||[])" :key="provider.name">
                                    <div class="bg-white rounded-xl p-3 border border-slate-100 shadow-sm hover:shadow-md transition-shadow">
                                        <div class="flex items-start justify-between mb-1.5">
                                            <p class="text-sm font-bold text-slate-800 leading-tight" x-text="provider.name"></p>
                                            <span class="text-[9px] bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded font-bold uppercase truncate max-w-[80px] ml-2 flex-shrink-0" x-text="provider.type"></span>
                                        </div>
                                        <p class="text-xs text-slate-500 mb-2 line-clamp-2" x-text="provider.address"></p>
                                        <div class="flex gap-1.5 flex-wrap">
                                            <a x-show="provider.contact"
                                                :href="provider.contact && provider.contact.includes('http') ? provider.contact : 'tel:' + provider.contact"
                                                target="_blank"
                                                class="text-[10px] font-bold text-primary bg-primary/10 px-2 py-1 rounded inline-block hover:bg-primary/20 transition-colors"
                                                x-text="provider.contact && provider.contact.includes('http') ? 'Situs ↗' : 'Telepon'"></a>
                                            <a :href="provider.maps_url" target="_blank"
                                                class="text-[10px] font-bold text-slate-600 bg-slate-100 px-2 py-1 rounded inline-block hover:bg-slate-200 transition-colors">Peta ↗</a>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            <div x-show="nearbyProviders.emergency_numbers" class="bg-red-50 border border-red-100 rounded-xl p-3 flex items-start gap-3 mt-3">
                                <div class="w-6 h-6 rounded bg-red-100 text-red-600 flex items-center justify-center font-bold text-xs flex-shrink-0 mt-0.5">!</div>
                                <div>
                                    <p class="text-[9px] font-bold text-red-600 uppercase tracking-wider mb-0.5">Nomor Darurat Setempat</p>
                                    <p class="text-sm text-red-800 font-bold" x-text="nearbyProviders.emergency_numbers"></p>
                                </div>
                            </div>
                            <a :href="nearbyProviders.maps_search_url" target="_blank"
                                class="mt-3 flex items-center justify-center gap-2 w-full py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 transition-colors text-sm font-semibold text-slate-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9V8h2v8zm4 0h-2V8h2v8z" />
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    <span x-text="copyFeedback || 'Salin Laporan'"></span>
                </button>
                <button @click="shareResult()"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full text-xs font-semibold transition-all hover:scale-105 active:scale-95"
                    style="background: linear-gradient(135deg,#3EAEB1,#2d8a8d); box-shadow: 0 4px 16px rgba(62,174,177,0.35); color: #fff;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                    </svg>
                    Bagikan
                </button>
                <button @click="savePdf()"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full text-xs font-semibold transition-all hover:scale-105 active:scale-95"
                    style="background: rgba(255,255,255,0.88); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.9); box-shadow: 0 4px 20px rgba(62,174,177,0.12); color: #16323a;">
                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Simpan PDF
                </button>
                <button @click="resetKiosk()"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full text-xs font-semibold transition-all hover:scale-105 active:scale-95 bg-slate-100 text-slate-600 hover:bg-slate-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Mulai Lagi
                </button>
            </div>

        </div>{{-- /max-w-5xl --}}

        {{-- ══════════════ BOTTOM-SHEET DRAWER (drug detail) ══════════════ --}}
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
                x-transition:leave-start="translate-y-0 opacity-100" x-transition:leave-end="translate-y-full opacity-0">
                <div class="sticky top-0 bg-white pt-3 pb-3 px-6 border-b border-slate-100 z-10">
                    <div class="w-10 h-1.5 rounded-full bg-slate-200 mx-auto mb-3 sm:hidden"></div>
                    <div class="flex items-center justify-between gap-3">
                        <h3 class="text-xl font-bold text-ink-900" x-text="drawerTitle"></h3>
                        <button @click="closeDrawer()" class="w-9 h-9 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center flex-shrink-0 transition-colors">
                            <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="p-6 space-y-4">
                    <template x-if="drawerData?.__drug">
                        <div class="flex flex-wrap gap-2">
                            <span x-show="drawerData.__drug.drug_class" class="text-sm bg-slate-100 text-slate-600 px-3 py-1 rounded-full font-medium" x-text="drawerData.__drug.drug_class"></span>
                            <span x-show="drawerData.__drug.dosage" class="text-sm bg-primary/10 text-primary px-3 py-1 rounded-full font-semibold" x-text="drawerData.__drug.dosage"></span>
                        </div>
                    </template>
                    <div x-show="drawerData?.mechanism">
                        <p class="text-xs font-bold text-indigo-500 uppercase tracking-wider mb-1">Mekanisme / Cara Kerja</p>
                        <p class="text-base text-slate-700 leading-relaxed" x-text="drawerData?.mechanism"></p>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div x-show="drawerData?.duration" class="bg-slate-50 rounded-xl px-3 py-2">
                            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Durasi</p>
                            <p class="text-sm font-semibold text-slate-800" x-text="drawerData?.duration"></p>
                        </div>
                        <div x-show="drawerData?.route" class="bg-slate-50 rounded-xl px-3 py-2">
                            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Rute</p>
                            <p class="text-sm font-semibold text-slate-800 capitalize" x-text="drawerData?.route"></p>
                        </div>
                    </div>
                    <div x-show="drawerData?.contraindications?.length" class="bg-red-50 rounded-2xl px-4 py-3">
                        <p class="text-xs font-bold text-red-600 uppercase tracking-wider mb-1.5">Kontraindikasi</p>
                        <div class="flex flex-wrap gap-1.5">
                            <template x-for="ci in (drawerData?.contraindications || [])" :key="ci">
                                <span class="text-sm bg-red-100 text-red-700 px-2.5 py-1 rounded-full" x-text="ci"></span>
                            </template>
                        </div>
                    </div>
                    <div x-show="drawerData?.reference_source">
                        <p class="text-xs font-bold text-teal-600 uppercase tracking-wider mb-1">Catatan</p>
                        <p class="text-base text-slate-700 leading-relaxed" x-text="drawerData?.reference_source"></p>
                    </div>
                    <div x-show="drawerData?.__drug?.db_url" class="pt-2">
                        <a :href="drawerData?.__drug?.db_url" target="_blank"
                            class="inline-flex items-center gap-2 text-sm font-semibold text-primary bg-primary/10 px-4 py-2.5 rounded-full hover:bg-primary/20 transition-colors">
                            Lihat halaman obat lengkap
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
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
                    Halaman akan otomatis kembali ke awal dalam <span class="font-bold text-primary" x-text="kioskCountdown"></span> detik
                    agar siap untuk pengguna berikutnya.
                </p>
                <div class="flex gap-3">
                    <button @click="snoozeKiosk()"
                        class="flex-1 px-4 py-3 rounded-full text-sm font-bold bg-slate-100 text-slate-700 hover:bg-slate-200 transition-colors">Tunggu</button>
                    <button @click="resetKiosk()"
                        class="flex-1 px-4 py-3 rounded-full text-sm font-bold text-white transition-all"
                        style="background: linear-gradient(135deg,#3EAEB1,#2d8a8d); box-shadow: 0 4px 16px rgba(62,174,177,0.35);">Cukup</button>
                </div>
                <p class="text-[10px] text-ink-400 mt-4">"Tunggu" memberi 1 menit tambahan · "Cukup" langsung mulai dari awal</p>
            </div>
        </div>{{-- /kiosk --}}

    </section>{{-- /medicheck-output-page --}}

</div>{{-- /ceksehat-root --}}
