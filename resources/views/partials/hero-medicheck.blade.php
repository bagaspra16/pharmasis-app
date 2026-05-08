{{-- HERO — MediCheck AI --}}
<section class="relative min-h-screen flex items-center justify-center overflow-hidden bg-slate-50" id="hero-section"
    x-data="infiniteGrid()" @mousemove="handleMouseMove" x-ref="container">

    {{-- Infinite Grid Layer 1: Dim background --}}
    <div class="absolute inset-0 z-0 opacity-10 pointer-events-none">
        <svg class="w-full h-full text-slate-400">
            <defs>
                <pattern id="grid-pattern-base" width="40" height="40" patternUnits="userSpaceOnUse" :x="gridOffsetX"
                    :y="gridOffsetY">
                    <path d="M 40 0 L 0 0 0 40" fill="none" stroke="currentColor" stroke-width="1" />
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#grid-pattern-base)" />
        </svg>
    </div>

    {{-- Infinite Grid Layer 2: Mouse-tracking highlight --}}
    <div class="absolute inset-0 z-0 opacity-50 pointer-events-none transition-opacity duration-300"
        :style="`mask-image: radial-gradient(350px circle at ${mouseX}px ${mouseY}px, black, transparent); -webkit-mask-image: radial-gradient(350px circle at ${mouseX}px ${mouseY}px, black, transparent);`">
        <svg class="w-full h-full text-primary">
            <defs>
                <pattern id="grid-pattern-active" width="40" height="40" patternUnits="userSpaceOnUse" :x="gridOffsetX"
                    :y="gridOffsetY">
                    <path d="M 40 0 L 0 0 0 40" fill="none" stroke="currentColor" stroke-width="1.5" />
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#grid-pattern-active)" />
        </svg>
    </div>

    {{-- Ambient Blobs --}}
    <div class="absolute inset-0 pointer-events-none z-0">
        <div class="absolute right-[-10%] top-[-10%] w-[40%] h-[40%] rounded-full bg-teal-400/20 blur-[120px]"></div>
        <div class="absolute left-[10%] bottom-[-10%] w-[30%] h-[30%] rounded-full bg-emerald-400/20 blur-[100px]">
        </div>
        <div class="absolute left-[-10%] top-[30%] w-[30%] h-[30%] rounded-full bg-primary/15 blur-[120px]"></div>
    </div>

    {{-- Content --}}
    <div class="relative z-10 max-w-3xl mx-auto px-4 py-10 md:py-16 text-center w-full" x-data="mediCheckHero()">

        {{-- Badge --}}
        <div class="inline-flex items-center gap-2 text-[11px] font-medium px-5 py-2 rounded-full mb-7 glass uppercase tracking-[0.14em]"
            style="color: #0d4f52;">
            <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
            </span>
            AI-Powered Symptom Analysis
            <span class="w-px h-3 bg-slate-300/60 mx-0.5"></span>
            <span class="font-display normal-case tracking-normal text-primary text-base leading-none">MediCheck</span>
        </div>

        {{-- Headline — Instrument Serif composite display --}}
        <h1 class="font-display text-5xl sm:text-6xl lg:text-7xl text-ink-900 mb-5 leading-[1.02] tracking-tight">
            Know your
            <span class="italic" style="background: linear-gradient(135deg,#3EAEB1 10%,#1a7a7d 90%); -webkit-background-clip:text; -webkit-text-fill-color:transparent; padding-right: 0.05em;">medicine.</span>
        </h1>
        <p class="text-sm md:text-base text-ink-500 mb-8 max-w-lg mx-auto leading-relaxed">
            All-in-one health platform. Voice or type your symptoms to get instant AI analysis, drug recommendations, and safety checks in seconds.
        </p>

        {{-- Unified Input Switcher --}}
        <div x-data="{ activeTab: 'voice' }"
            class="w-full max-w-2xl mx-auto flex flex-col items-center mt-4 mb-8 relative z-20">

            {{-- Tabs Selector --}}
            <div class="inline-flex flex-wrap justify-center p-1.5 rounded-3xl md:rounded-full mb-6"
                style="background: rgba(255,255,255,0.72); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.9); box-shadow: 0 4px 20px rgba(62,174,177,0.12), 0 1px 0 rgba(255,255,255,0.9) inset;">
                <button @click="activeTab = 'voice'"
                    :class="activeTab === 'voice' ? 'text-white shadow-lg' : 'text-slate-500 hover:text-slate-700 hover:bg-white/60'"
                    :style="activeTab === 'voice' ? 'background: linear-gradient(135deg,#3EAEB1,#2d8a8d); box-shadow: 0 4px 12px rgba(62,174,177,0.4);' : ''"
                    class="px-5 py-2 rounded-full text-xs font-semibold transition-all duration-300 flex items-center gap-1.5">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 1a3 3 0 00-3 3v8a3 3 0 006 0V4a3 3 0 00-3-3z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 10v2a7 7 0 01-14 0v-2"/></svg>
                    Voice
                </button>
                <button @click="activeTab = 'text'"
                    :class="activeTab === 'text' ? 'text-white shadow-lg' : 'text-slate-500 hover:text-slate-700 hover:bg-white/60'"
                    :style="activeTab === 'text' ? 'background: linear-gradient(135deg,#6366f1,#4f46e5); box-shadow: 0 4px 12px rgba(99,102,241,0.4);' : ''"
                    class="px-5 py-2 rounded-full text-xs font-semibold transition-all duration-300 flex items-center gap-1.5">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Text
                </button>
                <button @click="activeTab = 'search'"
                    :class="activeTab === 'search' ? 'text-white shadow-lg' : 'text-slate-500 hover:text-slate-700 hover:bg-white/60'"
                    :style="activeTab === 'search' ? 'background: linear-gradient(135deg,#0d9488,#0f766e); box-shadow: 0 4px 12px rgba(13,148,136,0.4);' : ''"
                    class="px-5 py-2 rounded-full text-xs font-semibold transition-all duration-300 flex items-center gap-1.5">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Search
                </button>
            </div>

            {{-- Input Area Container --}}
            <div class="relative w-full min-h-[14rem] flex justify-center">

                {{-- 1. Voice Input --}}
                <div x-show="activeTab === 'voice'" x-transition:enter="transition ease-out duration-300 delay-100"
                    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                    class="absolute inset-0 flex flex-col items-center justify-start">

                    <button @click="toggleRecording()" :disabled="analyzing"
                        :class="recording ? 'bg-red-500 shadow-red-400/40 scale-110' : 'bg-primary shadow-primary/40 hover:scale-105'"
                        class="relative w-24 h-24 sm:w-28 sm:h-28 rounded-full text-white shadow-xl transition-all duration-300 flex items-center justify-center group focus:outline-none"
                        id="voice-btn">
                        {{-- Pulse rings when recording --}}
                        <template x-if="recording">
                            <div>
                                <span class="absolute inset-0 rounded-full bg-red-400/30 animate-ping"></span>
                                <span class="absolute -inset-3 rounded-full bg-red-400/15 animate-pulse"></span>
                            </div>
                        </template>
                        {{-- Mic icon --}}
                        <svg x-show="!recording && !analyzing" class="w-10 h-10 sm:w-12 sm:h-12 drop-shadow-md"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M12 1a3 3 0 00-3 3v8a3 3 0 006 0V4a3 3 0 00-3-3z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M19 10v2a7 7 0 01-14 0v-2" />
                            <line x1="12" y1="19" x2="12" y2="23" stroke-width="1.8" stroke-linecap="round" />
                            <line x1="8" y1="23" x2="16" y2="23" stroke-width="1.8" stroke-linecap="round" />
                        </svg>
                        {{-- Stop icon --}}
                        <svg x-show="recording" x-cloak class="w-10 h-10 sm:w-12 sm:h-12 drop-shadow-md"
                            fill="currentColor" viewBox="0 0 24 24">
                            <rect x="6" y="6" width="12" height="12" rx="2" />
                        </svg>
                        {{-- Spinner --}}
                        <svg x-show="analyzing" x-cloak class="w-10 h-10 sm:w-12 sm:h-12 animate-spin" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                        </svg>
                    </button>
                    <p class="text-xs text-slate-500 mt-4 font-medium"
                        x-text="recording ? '' : (analyzing ? 'Analyzing symptoms...' : 'Tap to speak — any language')">
                    </p>

                    {{-- Waveform visualizer while recording --}}
                    <div x-show="recording" x-cloak class="mt-4 mx-auto w-full max-w-sm">
                        <div class="rounded-xl px-4 py-3" style="background: rgba(255,255,255,0.82); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.9); box-shadow: 0 4px 20px rgba(62,174,177,0.12);">
                            <div class="flex items-center justify-between mb-2">
                                <span class="flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-red-500 animate-ping"></span>
                                    <span
                                        class="text-[10px] text-red-500 font-bold tracking-widest uppercase">Recording</span>
                                </span>
                                <span class="text-[10px] text-slate-400">Whisper transcript enabled</span>
                            </div>
                            <canvas x-ref="waveCanvas" width="320" height="48"
                                class="w-full h-12 rounded-lg bg-slate-50/60"></canvas>
                        </div>
                    </div>
                </div>

                {{-- 2. Text Input --}}
                <div x-show="activeTab === 'text'" x-cloak
                    x-transition:enter="transition ease-out duration-300 delay-100"
                    x-transition:enter-start="opacity-0 translate-y-4"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 translate-y-4"
                    class="absolute inset-x-0 top-0 flex flex-col items-center px-4 w-full max-w-lg mx-auto">

                    <div class="w-full max-w-2xl rounded-3xl p-2"
                        style="background: rgba(255,255,255,0.82); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.9); box-shadow: 0 4px 24px rgba(62,174,177,0.12), 0 1px 0 rgba(255,255,255,0.9) inset;">
                        <div class="flex flex-col">
                            <textarea
                                x-ref="symptomTextarea"
                                x-model="symptoms"
                                @keydown.enter="handleTextareaEnter($event)"
                                @input="autoResizeTextarea()"
                                @x-transition:enter="autoResizeTextarea()"
                                placeholder="Describe your symptoms in detail (e.g. headache, fever for 2 days, sore throat)..."
                                class="flex-1 bg-transparent text-slate-900 placeholder-slate-400 font-medium text-sm px-4 py-3 rounded-3xl focus:outline-none focus:ring-0 resize-none border-none min-h-[52px] max-h-[240px] scrollbar-hide"
                                rows="1"
                                id="symptom-input"></textarea>
                            <div class="flex items-center justify-end gap-2 pt-2 px-1">
                                <div x-data="{ showTooltip: false }" class="relative">
                                    <button
                                        @mouseenter="showTooltip = true"
                                        @mouseleave="showTooltip = false"
                                        @click="analyzeText()"
                                        :disabled="analyzing || !symptoms.trim()"
                                        class="flex-shrink-0 text-white font-bold h-8 w-8 rounded-full flex items-center justify-center transition-all disabled:opacity-40 disabled:cursor-not-allowed"
                                        style="background: linear-gradient(135deg,#6366f1,#4f46e5); box-shadow: 0 4px 16px rgba(99,102,241,0.35);">
                                        <svg x-show="!analyzing" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5l7 7-7 7" />
                                        </svg>
                                        <svg x-show="analyzing" class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                            <rect x="6" y="6" width="12" height="12" rx="2" />
                                        </svg>
                                    </button>
                                    <div x-show="showTooltip" x-transition
                                        class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 bg-slate-800 text-white text-xs rounded whitespace-nowrap z-50">
                                        <span x-text="analyzing ? 'Stop analysis' : 'Send message (Enter)'"></span>
                                        <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-slate-800"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 3. Drug Search --}}
                <div x-show="activeTab === 'search'" x-cloak
                    x-transition:enter="transition ease-out duration-300 delay-100"
                    x-transition:enter-start="opacity-0 translate-y-4"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 translate-y-4"
                    class="absolute inset-x-0 top-0 flex flex-col items-center px-4 w-full max-w-lg mx-auto"
                    x-data="heroSearch()">

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
                                placeholder="Search by medicine name..."
                                class="w-full bg-transparent text-slate-900 placeholder-slate-400 text-sm font-medium focus:outline-none"
                                autocomplete="off" />
                        </div>
                        <a :href="`/search?q=${encodeURIComponent(query)}`"
                            class="flex-shrink-0 text-white font-bold text-sm px-6 py-3 rounded-full transition-all flex items-center justify-center"
                            style="background: linear-gradient(135deg,#0d9488,#0f766e); box-shadow: 0 4px 16px rgba(13,148,136,0.35);">
                            Search
                        </a>

                        {{-- Drug search dropdown --}}
                        <div x-show="open && results.length > 0" x-cloak
                            class="absolute left-0 right-0 top-[110%] bg-white rounded-2xl shadow-xl border border-slate-100 overflow-y-auto max-h-72 z-50 [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]">
                            <template x-for="(drug, idx) in results" :key="drug.id">
                                <a :href="drug.is_fda ? `/drugs/fda/${drug.slug}` : `/drugs/${drug.id}`"
                                    :class="focusedIdx===idx ? 'bg-primary/10' : 'hover:bg-slate-50'"
                                    class="flex items-center gap-3 px-4 py-3 transition-colors text-left border-b border-slate-50 last:border-0">
                                    <div
                                        class="w-8 h-8 rounded-lg bg-teal-500/10 flex items-center justify-center flex-shrink-0">
                                        <span class="text-sm font-bold text-teal-600"
                                            x-text="drug.name?.charAt(0)||'?'"></span>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-semibold text-slate-900 truncate" x-text="drug.name"></p>
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

        {{-- Error display --}}
        <div x-show="error" x-cloak
            class="mt-4 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl max-w-lg mx-auto"
            x-text="error"></div>

        {{-- Agentic Processing Panel --}}
        <div x-show="showProcessing" x-cloak class="mt-8 max-w-lg mx-auto text-left" id="processing-panel">
            <div class="rounded-2xl overflow-hidden"
                style="background: rgba(255,255,255,0.82); backdrop-filter: blur(24px); border: 1px solid rgba(255,255,255,0.92); box-shadow: 0 8px 40px rgba(62,174,177,0.14), 0 1px 0 rgba(255,255,255,0.9) inset;">
                {{-- Header --}}
                <div class="px-5 py-3.5 flex items-center gap-2" style="border-bottom: 1px solid rgba(62,174,177,0.1); background: linear-gradient(90deg, rgba(62,174,177,0.06), transparent);">
                    <svg class="w-4 h-4 text-primary animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-20" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" />
                        <path class="opacity-80" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                    </svg>
                    <span class="text-xs font-bold text-slate-700 uppercase tracking-widest"
                        x-text="lang === 'id' ? 'Menjalankan Pipeline AI 5-Langkah...' : 'Running 5-Model AI Pipeline...'"></span>
                </div>
                {{-- Steps --}}
                <div class="px-5 py-4 space-y-3">
                    <template x-for="(step, idx) in steps" :key="step.id">
                        <div class="flex items-start gap-3 transition-all duration-300"
                            :class="completedSteps.includes(step.id) ? 'opacity-100' : (currentStep === idx ? 'opacity-100' : 'opacity-35')">
                            {{-- Icon / Spinner / Check --}}
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5 transition-all duration-300"
                                :class="completedSteps.includes(step.id) ? 'bg-emerald-100' : (currentStep === idx ? 'bg-primary/15' : 'bg-slate-100')">
                                {{-- Completed check --}}
                                <template x-if="completedSteps.includes(step.id)">
                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                </template>
                                {{-- Active spinner --}}
                                <template x-if="!completedSteps.includes(step.id) && currentStep === idx">
                                    <svg class="w-3.5 h-3.5 text-primary animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-20" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="3" />
                                        <path class="opacity-80" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                                    </svg>
                                </template>
                                {{-- Pending --}}
                                <template x-if="!completedSteps.includes(step.id) && currentStep !== idx">
                                    <span class="text-sm" x-text="step.icon"></span>
                                </template>
                            </div>
                            {{-- Label --}}
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold leading-snug"
                                    :class="completedSteps.includes(step.id) ? 'text-emerald-700' : (currentStep === idx ? 'text-slate-800' : 'text-slate-400')"
                                    x-text="step.label"></p>
                                <p x-show="currentStep === idx && !completedSteps.includes(step.id)"
                                    class="text-[10px] text-primary mt-0.5 leading-relaxed" x-text="step.detail"></p>
                            </div>
                            {{-- Time badge for completed --}}
                            <span x-show="completedSteps.includes(step.id)"
                                class="text-[10px] text-emerald-500 font-mono flex-shrink-0 mt-1"></span>
                        </div>
                    </template>
                </div>
                {{-- Footer hint --}}
                <div class="px-5 py-2.5" style="border-top: 1px solid rgba(62,174,177,0.1); background: rgba(248,254,254,0.6);">
                    <p class="text-[10px] text-slate-400 text-center"
                        x-text="lang === 'id' ? 'Mohon tunggu, proses analisis membutuhkan waktu ~20-30 detik...' : 'Please wait, analysis takes ~20-30 seconds...'">
                    </p>
                </div>
            </div>
        </div>

        {{-- Results Panel --}}
        <div x-show="showResults" x-cloak class="mt-8 text-left" id="medicheck-results">
            {{-- Transcription --}}
            <div x-show="transcription"
                class="rounded-xl p-4 mb-4"
                style="background: rgba(255,255,255,0.80); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.9); box-shadow: 0 4px 20px rgba(62,174,177,0.09);">
                <p class="text-xs font-semibold text-primary uppercase tracking-wider mb-1">You said:</p>
                <p class="text-sm text-slate-700 italic" x-text="transcription"></p>
            </div>

            {{-- Step 1: Initial Diagnosis (Multiple conditions) --}}
            <div x-show="result?.step1?.conditions?.length"
                class="glass-card rounded-xl p-4 mb-4"
                id="step1-card">
                <h3 class="text-sm font-bold text-slate-800 mb-3 flex items-center gap-2">
                    <span
                        class="w-6 h-6 bg-primary/15 rounded-lg flex items-center justify-center text-xs font-bold text-primary">1</span>
                    Initial Analysis & Differential Diagnosis
                </h3>

                <template x-for="cond in (result?.step1?.conditions||[])" :key="cond.name">
                    <div class="mb-3 last:mb-0 p-3 bg-white/50 rounded-lg border border-slate-100">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-xs font-bold px-2 py-0.5 rounded uppercase tracking-wider" :class="cond.likelihood === 'High' ? 'bg-red-100 text-red-700' : 
                                        cond.likelihood === 'Moderate' ? 'bg-amber-100 text-amber-700' : 
                                        'bg-emerald-100 text-emerald-700'" x-text="cond.likelihood"></span>
                            <p class="text-sm font-bold text-slate-800" x-text="cond.name"></p>
                        </div>
                        <p class="text-xs text-slate-600 leading-relaxed" x-text="cond.description"></p>
                    </div>
                </template>

                <div class="mt-3 pt-3 border-t border-slate-200">
                    <p class="text-xs text-slate-500 italic" x-text="result?.step1?.summary"></p>
                </div>
            </div>

            {{-- Step 2: Drug Recommendations --}}
            <div x-show="result?.step2?.drugs"
                class="glass-card rounded-2xl mb-4 overflow-hidden"
                id="step2-card">
                {{-- Header --}}
                <div class="px-5 py-3.5 flex items-center gap-2" style="border-bottom: 1px solid rgba(62,174,177,0.1); background: linear-gradient(90deg,rgba(62,174,177,0.06),transparent);">
                    <span
                        class="w-7 h-7 bg-primary/15 rounded-xl flex items-center justify-center text-xs font-bold text-primary flex-shrink-0">2</span>
                    <div>
                        <h3 class="text-sm font-bold text-slate-800">Drug Recommendations</h3>
                        <p class="text-[10px] text-slate-400"
                            x-text="`${(result?.step2?.drugs||[]).length} medication(s) identified · based on clinical analysis`">
                        </p>
                    </div>
                </div>

                {{-- Pharmacist note --}}
                <div x-show="result?.step2?.pharmacist_notes"
                    class="mx-4 mt-3 px-3 py-2 bg-amber-50 border border-amber-200 rounded-xl">
                    <p class="text-[10px] font-bold text-amber-700 uppercase tracking-wider mb-0.5">Pharmacist Note</p>
                    <p class="text-xs text-amber-800 leading-relaxed" x-text="result?.step2?.pharmacist_notes"></p>
                </div>

                {{-- Drug cards --}}
                <div class="p-4 space-y-4">
                    <template x-for="d in (result?.step2?.drugs||[])" :key="d.name">
                        <div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden"
                            :data-drug-name="d.name">

                            {{-- Card header --}}
                            <div class="px-4 py-3 border-b border-slate-50 flex items-start justify-between gap-3">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap mb-0.5">
                                        <p class="text-sm font-bold text-slate-900 drug-name-label" x-text="d.name"></p>
                                        <span x-show="d.otc"
                                            class="text-[9px] bg-emerald-100 text-emerald-700 px-1.5 py-0.5 rounded-full font-bold uppercase tracking-wide">OTC</span>
                                        <span x-show="!d.otc"
                                            class="text-[9px] bg-violet-100 text-violet-700 px-1.5 py-0.5 rounded-full font-bold uppercase tracking-wide">Rx
                                            Only</span>
                                        <span x-show="d.drug_class"
                                            class="text-[9px] bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full font-medium"
                                            x-text="d.drug_class"></span>
                                    </div>
                                    <p class="text-xs text-slate-500" x-text="d.generic"></p>
                                </div>
                                {{-- Search/View in app --}}
                                <a :href="d.db_url || `/search?q=${encodeURIComponent(d.name)}`" target="_blank"
                                    class="flex-shrink-0 flex items-center gap-1 text-[10px] font-semibold px-2.5 py-1.5 rounded-lg transition-colors"
                                    :class="d.db_found ? 'bg-primary/10 text-primary hover:bg-primary/20' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                    <span x-text="d.db_found ? 'View Details' : 'Search'"></span>
                                </a>
                            </div>

                            {{-- Details grid --}}
                            <div class="px-4 py-3 grid grid-cols-2 gap-x-4 gap-y-2.5">
                                {{-- Dose & Frequency --}}
                                <div>
                                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">
                                        Dosage</p>
                                    <p class="text-xs font-semibold text-slate-800" x-text="d.dose"></p>
                                </div>
                                <div>
                                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">
                                        Frequency</p>
                                    <p class="text-xs font-semibold text-slate-800" x-text="d.frequency"></p>
                                </div>
                                <div x-show="d.duration">
                                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">
                                        Duration</p>
                                    <p class="text-xs font-semibold text-slate-800" x-text="d.duration"></p>
                                </div>
                                <div x-show="d.route">
                                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Route
                                    </p>
                                    <p class="text-xs font-semibold text-slate-800 capitalize" x-text="d.route"></p>
                                </div>
                            </div>

                            {{-- Purpose + Mechanism --}}
                            <div class="px-4 pb-3 space-y-2">
                                <div class="bg-blue-50 rounded-lg px-3 py-2">
                                    <p class="text-[9px] font-bold text-blue-500 uppercase tracking-wider mb-0.5">
                                        Purpose</p>
                                    <p class="text-xs text-blue-900 leading-relaxed" x-text="d.purpose"></p>
                                </div>
                                <div x-show="d.mechanism" class="bg-indigo-50 rounded-lg px-3 py-2">
                                    <p class="text-[9px] font-bold text-indigo-500 uppercase tracking-wider mb-0.5">
                                        Mechanism of Action</p>
                                    <p class="text-xs text-indigo-900 leading-relaxed" x-text="d.mechanism"></p>
                                </div>
                                <div x-show="d.how_to_take" class="bg-emerald-50 rounded-lg px-3 py-2">
                                    <p class="text-[9px] font-bold text-emerald-600 uppercase tracking-wider mb-0.5">How
                                        to Take</p>
                                    <p class="text-xs text-emerald-900 leading-relaxed" x-text="d.how_to_take"></p>
                                </div>

                                {{-- Side effects --}}
                                <div x-show="d.side_effects?.length" class="bg-amber-50 rounded-lg px-3 py-2">
                                    <p class="text-[9px] font-bold text-amber-600 uppercase tracking-wider mb-1">Side
                                        Effects</p>
                                    <div class="flex flex-wrap gap-1">
                                        <template x-for="se in (d.side_effects||[])" :key="se">
                                            <span
                                                class="text-[10px] bg-amber-100 text-amber-800 px-2 py-0.5 rounded-full"
                                                x-text="se"></span>
                                        </template>
                                    </div>
                                </div>

                                {{-- Contraindications --}}
                                <div x-show="d.contraindications?.length" class="bg-red-50 rounded-lg px-3 py-2">
                                    <p class="text-[9px] font-bold text-red-600 uppercase tracking-wider mb-1">
                                        Contraindications</p>
                                    <div class="flex flex-wrap gap-1">
                                        <template x-for="ci in (d.contraindications||[])" :key="ci">
                                            <span class="text-[10px] bg-red-100 text-red-700 px-2 py-0.5 rounded-full"
                                                x-text="ci"></span>
                                        </template>
                                    </div>
                                </div>

                                {{-- Source --}}
                                <div x-show="d.reference_source" class="flex items-center justify-between pt-1">
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <span class="text-[10px] text-slate-400"
                                            x-text="'Source: ' + d.reference_source"></span>
                                    </div>
                                    <a :href="`https://medlineplus.gov/druginfo/meds/${d.generic?.toLowerCase().replace(/\s+/g,'')}.html`"
                                        target="_blank" rel="noopener"
                                        class="text-[10px] text-primary hover:underline font-medium flex items-center gap-1">
                                        MedlinePlus
                                    </a>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>


            {{-- Step 3: Interactions --}}
            <div x-show="result?.step3?.interactions?.length"
                class="rounded-xl p-4 mb-4"
                style="background: rgba(255,255,255,0.80); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.9); box-shadow: 0 4px 20px rgba(62,174,177,0.09);">
                <h3 class="text-sm font-bold text-slate-800 mb-3 flex items-center gap-2">
                    <span
                        class="w-6 h-6 bg-primary/15 rounded-lg flex items-center justify-center text-xs font-bold text-primary">3</span>
                    Drug Interactions
                </h3>
                <template x-for="i in (result?.step3?.interactions||[])" :key="i.drug_a+i.drug_b">
                    <div class="flex items-start gap-2 mb-2 last:mb-0 p-2 rounded-lg"
                        :class="i.severity==='Avoid'?'bg-red-50':i.severity==='Caution'?'bg-amber-50':'bg-slate-50'">
                        <span class="text-xs font-bold px-1.5 py-0.5 rounded"
                            :class="i.severity==='Avoid'?'bg-red-200 text-red-800':i.severity==='Caution'?'bg-amber-200 text-amber-800':'bg-slate-200 text-slate-700'"
                            x-text="i.severity"></span>
                        <div class="flex-1">
                            <p class="text-xs font-semibold text-slate-800" x-text="`${i.drug_a} + ${i.drug_b}`"></p>
                            <p class="text-xs text-slate-500" x-text="i.effect"></p>
                            <p x-show="i.substitute" class="text-xs text-emerald-600 mt-0.5"
                                x-text="`→ Substitute: ${i.substitute}`"></p>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Step 4: Recovery Plan --}}
            <div x-show="result?.step4"
                class="rounded-2xl mb-4 overflow-hidden"
                style="background: rgba(255,255,255,0.80); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.9); box-shadow: 0 4px 20px rgba(62,174,177,0.09);">
                <div class="px-5 py-3.5 flex items-center gap-2" style="border-bottom: 1px solid rgba(62,174,177,0.1); background: linear-gradient(90deg,rgba(62,174,177,0.06),transparent);">
                    <span
                        class="w-7 h-7 bg-primary/15 rounded-xl flex items-center justify-center text-xs font-bold text-primary flex-shrink-0">4</span>
                    <h3 class="text-sm font-bold text-slate-800">Recovery Plan</h3>
                </div>
                <div class="p-4 space-y-4">
                    <div x-show="result?.step4?.recovery_timeline"
                        class="flex items-start gap-3 bg-primary/5 rounded-xl px-4 py-3">
                        <span class="text-lg"></span>
                        <div>
                            <p class="text-[9px] font-bold text-primary uppercase tracking-wider mb-0.5">Estimated
                                Timeline</p>
                            <p class="text-xs text-slate-800 font-medium" x-text="result?.step4?.recovery_timeline"></p>
                        </div>
                    </div>
                    <div x-show="result?.step4?.usage_instructions?.length">
                        <p class="text-[9px] font-bold text-slate-500 uppercase tracking-wider mb-2">Drug Usage
                            Instructions</p>
                        <div class="space-y-3">
                            <template x-for="u in (result?.step4?.usage_instructions||[])" :key="u.drug">
                                <div class="bg-white rounded-xl border border-slate-100 p-3">
                                    <p class="text-xs font-bold text-slate-800 mb-2" x-text="u.drug"></p>
                                    <div x-show="u.step_by_step?.length" class="mb-2">
                                        <template x-for="(s, i) in (u.step_by_step||[])" :key="i">
                                            <div class="flex items-start gap-2 mb-1">
                                                <span
                                                    class="w-4 h-4 rounded-full bg-primary/20 text-primary text-[9px] font-bold flex items-center justify-center flex-shrink-0 mt-0.5"
                                                    x-text="i+1"></span>
                                                <p class="text-xs text-slate-700" x-text="s"></p>
                                            </div>
                                        </template>
                                    </div>
                                    <p x-show="u.instructions && !u.step_by_step?.length"
                                        class="text-xs text-slate-700 mb-2" x-text="u.instructions"></p>
                                    <div class="flex flex-wrap gap-1.5 mt-2">
                                        <span x-show="u.timing"
                                            class="text-[10px] bg-blue-50 text-blue-700 px-2 py-0.5 rounded-full"
                                            x-text="'' + u.timing"></span>
                                        <span x-show="u.with_food === true"
                                            class="text-[10px] bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded-full">With
                                            food</span>
                                        <span x-show="u.with_food === false"
                                            class="text-[10px] bg-slate-50 text-slate-600 px-2 py-0.5 rounded-full">Without
                                            food</span>
                                        <span x-show="u.max_daily_dose"
                                            class="text-[10px] bg-red-50 text-red-600 px-2 py-0.5 rounded-full"
                                            x-text="'Max: ' + u.max_daily_dose"></span>
                                    </div>
                                    <p x-show="u.what_to_avoid" class="text-[10px] text-amber-700 mt-1.5"
                                        x-text="'Avoid: ' + u.what_to_avoid"></p>
                                    <p x-show="u.what_to_watch" class="text-[10px] text-indigo-700 mt-0.5"
                                        x-text="'Watch for: ' + u.what_to_watch"></p>
                                </div>
                            </template>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div x-show="result?.step4?.lifestyle_tips?.length" class="bg-emerald-50 rounded-xl p-3">
                            <p class="text-[9px] font-bold text-emerald-600 uppercase tracking-wider mb-1.5">Lifestyle
                            </p>
                            <template x-for="tip in (result?.step4?.lifestyle_tips||[])" :key="tip">
                                <p class="text-xs text-emerald-900 mb-0.5">• <span x-text="tip"></span></p>
                            </template>
                        </div>
                        <div x-show="result?.step4?.diet_recommendations?.length" class="bg-teal-50 rounded-xl p-3">
                            <p class="text-[9px] font-bold text-teal-600 uppercase tracking-wider mb-1.5">Diet
                                Recommendations</p>
                            <template x-for="d in (result?.step4?.diet_recommendations||[])" :key="d">
                                <p class="text-xs text-teal-900 mb-0.5">• <span x-text="d"></span></p>
                            </template>
                        </div>
                    </div>
                    <div x-show="result?.step4?.warning_signs?.length"
                        class="bg-red-50 border border-red-100 rounded-xl p-3">
                        <p class="text-[9px] font-bold text-red-600 uppercase tracking-wider mb-1.5">Seek Medical Help
                            If</p>
                        <template x-for="w in (result?.step4?.warning_signs||[])" :key="w">
                            <p class="text-xs text-red-800 mb-0.5">• <span x-text="w"></span></p>
                        </template>
                    </div>
                    <div x-show="result?.step4?.follow_up"
                        class="flex items-start gap-2 bg-slate-50 rounded-xl px-3 py-2">
                        <span class="text-base"></span>
                        <div>
                            <p class="text-[9px] font-bold text-slate-500 uppercase tracking-wider">Follow-up</p>
                            <p class="text-xs text-slate-700" x-text="result?.step4?.follow_up"></p>
                        </div>
                    </div>
                    <div x-show="result?.step4?.disclaimer" class="pt-3 border-t border-slate-100">
                        <p class="text-[10px] text-slate-400 italic leading-relaxed" x-text="result?.step4?.disclaimer">
                        </p>
                    </div>
                </div>
            </div>

            {{-- Step 5: Local Healthcare Providers (async loaded) --}}
            <div x-show="showResults && (nearbyLoading || nearbyProviders)"
                class="rounded-2xl mb-4 overflow-hidden"
                style="background: rgba(255,255,255,0.80); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.9); box-shadow: 0 4px 20px rgba(62,174,177,0.09);">
                <div class="px-5 py-3.5 flex items-center justify-between" style="border-bottom: 1px solid rgba(62,174,177,0.1); background: linear-gradient(90deg,rgba(62,174,177,0.06),transparent);">
                    <div class="flex items-center gap-2">
                        <span
                            class="w-7 h-7 bg-primary/15 rounded-xl flex items-center justify-center text-xs font-bold text-primary flex-shrink-0">5</span>
                        <h3 class="text-sm font-bold text-slate-800">Nearby Healthcare Providers</h3>
                    </div>
                    {{-- Loading spinner --}}
                    <div x-show="nearbyLoading" class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-primary animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-20" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" />
                            <path class="opacity-80" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                        </svg>
                        <span class="text-[10px] text-primary font-medium">Locating nearby facilities...</span>
                    </div>
                </div>

                <div class="p-4 space-y-3">
                    {{-- Skeleton while loading --}}
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

                    {{-- Loaded providers grid --}}
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
                                                x-text="provider.contact && provider.contact.includes('http') ? 'Website ↗' : 'Call'"></a>
                                            <a :href="provider.maps_url" target="_blank"
                                                class="text-[10px] font-bold text-slate-600 bg-slate-100 px-2 py-1 rounded inline-block hover:bg-slate-200 transition-colors">Maps
                                                ↗</a>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            {{-- Emergency numbers --}}
                            <div x-show="nearbyProviders.emergency_numbers"
                                class="bg-red-50 border border-red-100 rounded-xl p-3 flex items-start gap-3 mt-3">
                                <div
                                    class="w-6 h-6 rounded bg-red-100 text-red-600 flex items-center justify-center font-bold text-xs flex-shrink-0 mt-0.5">
                                    !</div>
                                <div>
                                    <p class="text-[9px] font-bold text-red-600 uppercase tracking-wider mb-0.5">Local
                                        Emergency Numbers</p>
                                    <p class="text-xs text-red-800 font-bold"
                                        x-text="nearbyProviders.emergency_numbers"></p>
                                </div>
                            </div>

                            {{-- Google Maps fallback button --}}
                            <a :href="nearbyProviders.maps_search_url" target="_blank"
                                class="mt-3 flex items-center justify-center gap-2 w-full py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 transition-colors text-xs font-semibold text-slate-700">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Search More on Google Maps ↗
                            </a>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- Watermark footer --}}
        <div x-show="showResults" x-cloak class="mt-4 text-center">
            <p class="text-[10px] text-slate-400 font-medium">
                Generated by <span class="text-primary font-semibold">Pharmasis</span> — Know Your Medicine
            </p>
            <p class="text-[9px] text-slate-300 mt-0.5">This report is for educational purposes only. Consult a healthcare professional for medical advice.</p>
        </div>

        {{-- Export Action Bar --}}
        <div x-show="showResults" x-cloak
            class="mt-6 mb-8 flex flex-wrap items-center justify-center gap-3"
            id="medicheck-actions">
            <button @click="copyResult()"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full text-xs font-semibold transition-all hover:scale-105 active:scale-95"
                style="background: rgba(255,255,255,0.82); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.9); box-shadow: 0 4px 20px rgba(62,174,177,0.12), 0 1px 0 rgba(255,255,255,0.9) inset; color: #16323a;">
                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                <span x-text="copyFeedback || 'Copy Report'"></span>
            </button>
            <button @click="shareResult()"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full text-xs font-semibold transition-all hover:scale-105 active:scale-95"
                style="background: linear-gradient(135deg,#3EAEB1,#2d8a8d); box-shadow: 0 4px 16px rgba(62,174,177,0.35), 0 1px 0 rgba(255,255,255,0.4) inset; color: #fff;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                Share
            </button>
            <button @click="savePdf()"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full text-xs font-semibold transition-all hover:scale-105 active:scale-95"
                style="background: rgba(255,255,255,0.82); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.9); box-shadow: 0 4px 20px rgba(62,174,177,0.12), 0 1px 0 rgba(255,255,255,0.9) inset; color: #16323a;">
                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Save PDF
            </button>
        </div>
    </div>

    {{-- Watermark layer for PDF/print --}}
    <div id="medicheck-watermark" aria-hidden="true"
        style="position: fixed; inset: 0; pointer-events: none; z-index: 9999; display: none;">
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-30deg); opacity: 0.08; font-size: 4rem; font-weight: 800; color: #3EAEB1; white-space: nowrap; font-family: 'Instrument Serif', serif; letter-spacing: 0.1em;">
            Pharmasis
        </div>
    </div>

</section>

<style>
    @media print {
        /* Hide UI chrome */
        .nav-shell, footer, #medicheck-actions, #processing-panel, .no-print {
            display: none !important;
        }
        /* Clean backgrounds for print */
        #hero-section {
            background: white !important;
            min-height: auto !important;
        }
        #hero-section .absolute, #hero-section .z-0 {
            display: none !important;
        }
        /* Page settings */
        @page {
            margin: 1.5cm;
        }
        /* Footer watermark */
        body::after {
            content: 'Pharmasis — Know Your Medicine';
            position: fixed;
            bottom: 1cm;
            right: 1cm;
            font-size: 8pt;
            color: #3EAEB1;
            opacity: 0.6;
            font-family: 'Geist', sans-serif;
        }
    }

    /* Hide scrollbar for textarea */
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }
    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>