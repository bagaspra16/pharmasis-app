<div x-data="medicheckHistory()" @medicheck-history-updated.window="loadHistory()" x-init="loadHistory()"
    class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 w-full"
    style="border-top: 1px solid rgba(62,174,177,0.12); margin-top: 3rem;"
    x-show="history.length > 0" x-cloak>

    <div class="flex items-center justify-between mb-8 flex-wrap gap-4">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="w-1 h-4 rounded-full" style="background: linear-gradient(to bottom,#3EAEB1,#61BACA);"></span>
                <p class="text-[11px] font-semibold text-primary uppercase tracking-[0.18em]">History</p>
            </div>
            <h2 class="font-display text-3xl md:text-4xl text-ink-900 leading-tight tracking-tight">
                MediCheck <span class="italic text-primary">consultation</span> history
            </h2>
            <p class="text-sm text-ink-400 mt-2 ml-0">Past consultations are stored locally on your device.</p>
        </div>
        <button @click="showClearConfirm = true"
            class="text-xs font-medium px-4 py-2 rounded-full transition-all"
            style="color:#ef4444; background:rgba(239,68,68,0.06); border:1px solid rgba(239,68,68,0.15);"
            onmouseover="this.style.background='rgba(239,68,68,0.12)'"
            onmouseout="this.style.background='rgba(239,68,68,0.06)'">
            Clear History
        </button>
    </div>

    {{-- Horizontal Scrollable List --}}
    <div class="flex overflow-x-auto pb-4 gap-4 snap-x hide-scrollbar">
        <template x-for="item in history" :key="item.id">
            <div @click="openModal(item)"
                class="snap-start flex-shrink-0 w-72 rounded-2xl p-4 cursor-pointer group glass-card glass-hover">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[10px] font-medium px-2.5 py-1 rounded-full uppercase tracking-wider"
                        style="background:rgba(62,174,177,0.1); color:#2d8a8d; border:1px solid rgba(62,174,177,0.15);"
                        x-text="formatDate(item.date)"></span>
                    <span class="text-slate-300 group-hover:text-primary transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </span>
                </div>
                <p class="display-italic text-sm text-ink-500 mb-3 line-clamp-2" x-text="`&ldquo;${item.preview}&rdquo;`"></p>
                <div class="space-y-1.5">
                    <template x-for="cond in item.conditions" :key="cond">
                        <div class="flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-primary/50"></span>
                            <span class="text-xs font-medium text-ink-700 truncate tracking-tight" x-text="cond"></span>
                        </div>
                    </template>
                </div>
            </div>
        </template>
    </div>

    {{-- Clear History Confirmation Modal --}}
    <div x-show="showClearConfirm" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6"
        aria-modal="true">
        {{-- Backdrop --}}
        <div x-show="showClearConfirm" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="showClearConfirm = false"
            class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm"></div>

        {{-- Modal Content --}}
        <div x-show="showClearConfirm" x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="relative w-full max-w-sm rounded-2xl overflow-hidden"
            style="background:rgba(255,255,255,0.97); backdrop-filter:blur(32px); border:1px solid rgba(255,255,255,0.9); box-shadow:0 24px 80px rgba(220,38,38,0.15), 0 1px 0 rgba(255,255,255,0.9) inset;">

            {{-- Header --}}
            <div class="px-5 py-4 flex items-center gap-3 border-b border-slate-100">
                <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                <h3 class="text-base font-bold text-slate-800">Clear History</h3>
            </div>

            {{-- Body --}}
            <div class="p-5">
                <p class="text-sm text-slate-700 mb-3">
                    Delete all <span class="font-semibold" x-text="history.length"></span> consultation(s)?
                </p>
                <p class="text-xs text-slate-500">
                    This can't be undone
                </p>
            </div>

            {{-- Footer --}}
            <div class="px-5 py-4 flex items-center justify-end gap-2 border-t border-slate-100">
                <button @click="showClearConfirm = false"
                    class="px-4 py-2 text-sm font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">
                    Cancel
                </button>
                <button @click="confirmClearHistory()"
                    class="px-4 py-2 text-sm font-medium text-white bg-red-500 hover:bg-red-600 rounded-lg transition-colors">
                    Delete
                </button>
            </div>
        </div>
    </div>

    {{-- Modal Popup --}}
    <div x-show="selected" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6"
        aria-modal="true">
        {{-- Backdrop --}}
        <div x-show="selected" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="selected = null"
            class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm"></div>

        {{-- Modal Content --}}
        <div x-show="selected" x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="relative w-full max-w-4xl rounded-2xl overflow-hidden max-h-[90vh] flex flex-col"
            style="background:rgba(248,254,254,0.97); backdrop-filter:blur(32px); border:1px solid rgba(255,255,255,0.9); box-shadow:0 24px 80px rgba(13,79,82,0.2), 0 1px 0 rgba(255,255,255,0.9) inset;">

            {{-- Header --}}
            <div class="px-6 py-4 flex items-center justify-between sticky top-0 z-10"
                style="background: linear-gradient(135deg,rgba(13,79,82,0.95),rgba(26,122,125,0.95)); border-bottom:1px solid rgba(62,174,177,0.3);">
                <div>
                    <h3 class="text-lg font-bold text-white">Consultation Details</h3>
                    <p class="text-xs text-teal-200/70 mt-0.5" x-text="selected ? formatDate(selected.date) : ''"></p>
                </div>
                <div class="flex items-center gap-2">
                    <button @click="copySelected()"
                        class="text-[10px] font-semibold text-white/80 hover:text-white bg-white/10 hover:bg-white/20 px-3 py-1.5 rounded-full transition-all flex items-center gap-1.5">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        <span x-text="copyFeedback || 'Copy'"></span>
                    </button>
                    <button @click="shareSelected()"
                        class="text-[10px] font-semibold text-white/80 hover:text-white bg-white/10 hover:bg-white/20 px-3 py-1.5 rounded-full transition-all flex items-center gap-1.5">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                        Share
                    </button>
                    <button @click="printSelected()"
                        class="text-[10px] font-semibold text-white/80 hover:text-white bg-white/10 hover:bg-white/20 px-3 py-1.5 rounded-full transition-all flex items-center gap-1.5">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        PDF
                    </button>
                    <button @click="selected = null"
                        class="w-8 h-8 flex items-center justify-center rounded-full text-white/70 hover:text-white transition-colors"
                        style="background:rgba(255,255,255,0.12); border:1px solid rgba(255,255,255,0.2);">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Scrollable Body --}}
            <div class="p-6 overflow-y-auto flex-1 custom-scrollbar" style="background:rgba(248,254,254,0.98);">
                <template x-if="selected">
                    <div class="space-y-4">
                        {{-- Symptoms --}}
                        <div class="rounded-xl p-4"
                            style="background:rgba(255,255,255,0.85); border:1px solid rgba(62,174,177,0.12); box-shadow:0 2px 12px rgba(62,174,177,0.06);">
                            <p class="text-[10px] font-bold text-primary uppercase tracking-wider mb-2">Symptoms</p>
                            <p class="text-sm text-slate-700 italic leading-relaxed"
                                x-text="`&quot;${selected.result.symptoms}&quot;`"></p>
                        </div>

                        {{-- Conditions --}}
                        <div class="rounded-xl p-4"
                            style="background:rgba(255,255,255,0.85); border:1px solid rgba(62,174,177,0.12); box-shadow:0 2px 12px rgba(62,174,177,0.06);">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-3">Differential
                                Diagnosis</p>
                            <div class="space-y-3">
                                <template x-for="cond in (selected.result.step1?.conditions||[])" :key="cond.name">
                                    <div class="p-3 bg-slate-50 rounded-lg">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded uppercase" :class="cond.likelihood === 'High' ? 'bg-red-100 text-red-700' : 
                                                          cond.likelihood === 'Moderate' ? 'bg-amber-100 text-amber-700' : 
                                                          'bg-emerald-100 text-emerald-700'"
                                                x-text="cond.likelihood"></span>
                                            <p class="text-sm font-bold text-slate-800" x-text="cond.name"></p>
                                        </div>
                                        <p class="text-xs text-slate-600" x-text="cond.description"></p>
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- Drugs --}}
                        <div class="rounded-xl p-4"
                            style="background:rgba(255,255,255,0.85); border:1px solid rgba(62,174,177,0.12); box-shadow:0 2px 12px rgba(62,174,177,0.06);">
                            <p class="text-[10px] font-bold text-primary uppercase tracking-wider mb-3">Drug Recommendations</p>
                            <div class="space-y-4">
                                <template x-for="drug in (selected.result.step2?.drugs||[])" :key="drug.name">
                                    <div class="p-4 bg-white border border-slate-100 rounded-lg shadow-sm">
                                        {{-- Header --}}
                                        <div class="flex items-start justify-between gap-3 mb-3 pb-3 border-b border-slate-100">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2 flex-wrap mb-1">
                                                    <p class="text-sm font-bold text-slate-900" x-text="drug.name"></p>
                                                    <span x-show="drug.otc"
                                                        class="text-[9px] bg-emerald-100 text-emerald-700 px-1.5 py-0.5 rounded-full font-bold uppercase tracking-wide">OTC</span>
                                                    <span x-show="!drug.otc"
                                                        class="text-[9px] bg-violet-100 text-violet-700 px-1.5 py-0.5 rounded-full font-bold uppercase tracking-wide">Rx Only</span>
                                                    <span x-show="drug.drug_class"
                                                        class="text-[9px] bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full font-medium"
                                                        x-text="drug.drug_class"></span>
                                                </div>
                                                <p class="text-xs text-slate-500" x-text="drug.generic"></p>
                                            </div>
                                            <a :href="drug.db_url || `/search?q=${encodeURIComponent(drug.name)}`"
                                                target="_blank"
                                                class="flex-shrink-0 flex items-center gap-1 text-[10px] font-semibold px-2.5 py-1.5 rounded-lg transition-colors"
                                                :class="drug.db_found ? 'bg-primary/10 text-primary hover:bg-primary/20' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                                </svg>
                                                <span x-text="drug.db_found ? 'View Details' : 'Search'"></span>
                                            </a>
                                        </div>

                                        {{-- Dosage grid --}}
                                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 mb-3">
                                            <div>
                                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Dosage</p>
                                                <p class="text-xs font-semibold text-slate-800" x-text="drug.dose"></p>
                                            </div>
                                            <div>
                                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Frequency</p>
                                                <p class="text-xs font-semibold text-slate-800" x-text="drug.frequency"></p>
                                            </div>
                                            <div x-show="drug.duration">
                                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Duration</p>
                                                <p class="text-xs font-semibold text-slate-800" x-text="drug.duration"></p>
                                            </div>
                                            <div x-show="drug.route">
                                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Route</p>
                                                <p class="text-xs font-semibold text-slate-800 capitalize" x-text="drug.route"></p>
                                            </div>
                                        </div>

                                        {{-- Purpose + Mechanism + How to Take --}}
                                        <div class="space-y-2 mb-3">
                                            <div class="bg-blue-50 rounded-lg px-3 py-2">
                                                <p class="text-[9px] font-bold text-blue-500 uppercase tracking-wider mb-0.5">Purpose</p>
                                                <p class="text-xs text-blue-900 leading-relaxed" x-text="drug.purpose"></p>
                                            </div>
                                            <div x-show="drug.mechanism" class="bg-indigo-50 rounded-lg px-3 py-2">
                                                <p class="text-[9px] font-bold text-indigo-500 uppercase tracking-wider mb-0.5">Mechanism of Action</p>
                                                <p class="text-xs text-indigo-900 leading-relaxed" x-text="drug.mechanism"></p>
                                            </div>
                                            <div x-show="drug.how_to_take" class="bg-emerald-50 rounded-lg px-3 py-2">
                                                <p class="text-[9px] font-bold text-emerald-600 uppercase tracking-wider mb-0.5">How to Take</p>
                                                <p class="text-xs text-emerald-900 leading-relaxed" x-text="drug.how_to_take"></p>
                                            </div>
                                        </div>

                                        {{-- Side effects --}}
                                        <div x-show="drug.side_effects?.length" class="bg-amber-50 rounded-lg px-3 py-2 mb-2">
                                            <p class="text-[9px] font-bold text-amber-600 uppercase tracking-wider mb-1">Side Effects</p>
                                            <div class="flex flex-wrap gap-1">
                                                <template x-for="se in (drug.side_effects||[])" :key="se">
                                                    <span class="text-[10px] bg-amber-100 text-amber-800 px-2 py-0.5 rounded-full" x-text="se"></span>
                                                </template>
                                            </div>
                                        </div>

                                        {{-- Contraindications --}}
                                        <div x-show="drug.contraindications?.length" class="bg-red-50 rounded-lg px-3 py-2">
                                            <p class="text-[9px] font-bold text-red-600 uppercase tracking-wider mb-1">Contraindications</p>
                                            <div class="flex flex-wrap gap-1">
                                                <template x-for="ci in (drug.contraindications||[])" :key="ci">
                                                    <span class="text-[10px] bg-red-100 text-red-700 px-2 py-0.5 rounded-full" x-text="ci"></span>
                                                </template>
                                            </div>
                                        </div>

                                        {{-- Source --}}
                                        <div x-show="drug.reference_source" class="flex items-center gap-1.5 mt-2 pt-2 border-t border-slate-100">
                                            <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <span class="text-[10px] text-slate-400" x-text="'Source: ' + drug.reference_source"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            {{-- Pharmacist note --}}
                            <div x-show="selected.result.step2?.pharmacist_notes" class="mt-3 px-3 py-2 bg-amber-50 border border-amber-200 rounded-xl">
                                <p class="text-[10px] font-bold text-amber-700 uppercase tracking-wider mb-0.5">Pharmacist Note</p>
                                <p class="text-xs text-amber-800 leading-relaxed" x-text="selected.result.step2.pharmacist_notes"></p>
                            </div>
                        </div>

                        {{-- Drug Interactions --}}
                        <div x-show="selected.result.step3?.interactions?.length" class="rounded-xl p-4"
                            style="background:rgba(255,255,255,0.85); border:1px solid rgba(62,174,177,0.12); box-shadow:0 2px 12px rgba(62,174,177,0.06);">
                            <p class="text-[10px] font-bold text-primary uppercase tracking-wider mb-3">Drug Interactions</p>
                            <div class="space-y-3">
                                <template x-for="inter in (selected.result.step3?.interactions||[])" :key="inter.drug_a + inter.drug_b">
                                    <div class="p-3 rounded-lg"
                                        :class="inter.severity === 'Avoid' ? 'bg-red-50 border border-red-100' : inter.severity === 'Caution' ? 'bg-amber-50 border border-amber-100' : 'bg-slate-50 border border-slate-100'">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded uppercase"
                                                :class="inter.severity === 'Avoid' ? 'bg-red-200 text-red-800' : inter.severity === 'Caution' ? 'bg-amber-200 text-amber-800' : 'bg-slate-200 text-slate-700'"
                                                x-text="inter.severity"></span>
                                            <p class="text-sm font-semibold text-slate-800" x-text="inter.drug_a + ' + ' + inter.drug_b"></p>
                                        </div>
                                        <p class="text-xs text-slate-600" x-text="inter.effect"></p>
                                        <div x-show="inter.substitute" class="mt-2 pt-2 border-t border-slate-200">
                                            <p class="text-xs text-emerald-700 font-semibold">Substitute: <span x-text="inter.substitute"></span></p>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- Recovery Plan --}}
                        <div class="rounded-xl p-4"
                            style="background:rgba(255,255,255,0.85); border:1px solid rgba(62,174,177,0.12); box-shadow:0 2px 12px rgba(62,174,177,0.06);">
                            <p class="text-[10px] font-bold text-primary uppercase tracking-wider mb-3">Recovery Plan</p>

                            <div class="space-y-4">
                                <div x-show="selected.result.step4?.recovery_timeline"
                                    class="flex items-start gap-2 bg-blue-50/50 p-3 rounded-lg">
                                    <span class="text-sm">📅</span>
                                    <div>
                                        <p class="text-[10px] font-bold text-blue-800 uppercase">Timeline</p>
                                        <p class="text-xs text-blue-900"
                                            x-text="selected.result.step4?.recovery_timeline"></p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div x-show="selected.result.step4?.lifestyle_tips?.length"
                                        class="bg-emerald-50/50 p-3 rounded-lg">
                                        <p class="text-[10px] font-bold text-emerald-800 uppercase mb-1.5">Lifestyle</p>
                                        <template x-for="tip in (selected.result.step4?.lifestyle_tips||[])">
                                            <p class="text-xs text-emerald-900 mb-0.5">• <span x-text="tip"></span></p>
                                        </template>
                                    </div>
                                    <div x-show="selected.result.step4?.diet_recommendations?.length"
                                        class="bg-teal-50/50 p-3 rounded-lg">
                                        <p class="text-[10px] font-bold text-teal-800 uppercase mb-1.5">Diet</p>
                                        <template x-for="diet in (selected.result.step4?.diet_recommendations||[])">
                                            <p class="text-xs text-teal-900 mb-0.5">• <span x-text="diet"></span></p>
                                        </template>
                                    </div>
                                </div>

                                <div x-show="selected.result.step4?.warning_signs?.length"
                                    class="bg-red-50 border border-red-100 rounded-lg p-3">
                                    <p class="text-[10px] font-bold text-red-700 uppercase mb-1.5">⚠️ Seek Medical Help If</p>
                                    <template x-for="warning in (selected.result.step4?.warning_signs||[])">
                                        <p class="text-xs text-red-900 mb-0.5">• <span x-text="warning"></span></p>
                                    </template>
                                </div>

                                <div x-show="selected.result.step4?.follow_up"
                                    class="bg-slate-50 rounded-lg p-3">
                                    <p class="text-[10px] font-bold text-slate-700 uppercase mb-1">Follow-up</p>
                                    <p class="text-xs text-slate-800" x-text="selected.result.step4.follow_up"></p>
                                </div>

                                <div x-show="selected.result.step4?.disclaimer"
                                    class="bg-slate-100/50 rounded-lg p-3 border border-slate-200">
                                    <p class="text-[9px] font-bold text-slate-500 uppercase mb-1">Disclaimer</p>
                                    <p class="text-xs text-slate-600 italic" x-text="selected.result.step4.disclaimer"></p>
                                </div>
                            </div>
                        </div>

                        {{-- Step 5: Local Healthcare Providers --}}
                        <div x-show="selected.nearbyProviders || selected.nearbyLocation" class="rounded-xl p-4"
                            style="background:rgba(255,255,255,0.85); border:1px solid rgba(62,174,177,0.12); box-shadow:0 2px 12px rgba(62,174,177,0.06);">
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Nearby Healthcare Providers</p>
                                <span x-show="selected.nearbyLocation" class="text-[9px] bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-medium truncate max-w-[160px]" x-text="selected.nearbyLocation"></span>
                            </div>
                            <div x-show="selected.nearbyProviders?.providers?.length" class="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-3">
                                <template x-for="provider in (selected.nearbyProviders?.providers||[])" :key="provider.name">
                                    <div class="p-3 bg-slate-50 border border-slate-100 rounded-lg">
                                        <div class="flex items-start justify-between mb-1">
                                            <p class="text-xs font-bold text-slate-800 leading-tight" x-text="provider.name"></p>
                                            <span class="text-[9px] bg-white border border-slate-200 text-slate-500 px-1.5 py-0.5 rounded font-bold uppercase ml-2 flex-shrink-0" x-text="provider.type"></span>
                                        </div>
                                        <p class="text-[11px] text-slate-500 mb-2" x-text="provider.address"></p>
                                        <div class="flex gap-1.5">
                                            <a x-show="provider.contact" :href="provider.contact && provider.contact.includes('http') ? provider.contact : 'tel:' + provider.contact" target="_blank" class="text-[10px] font-bold text-primary bg-primary/10 px-2 py-0.5 rounded hover:bg-primary/20" x-text="provider.contact && provider.contact.includes('http') ? 'Website ↗' : 'Call'"></a>
                                            <a x-show="provider.maps_url" :href="provider.maps_url" target="_blank" class="text-[10px] font-bold text-slate-500 bg-slate-200 px-2 py-0.5 rounded hover:bg-slate-300">Maps ↗</a>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            <div x-show="selected.nearbyProviders?.emergency_numbers" class="bg-red-50 border border-red-100 rounded-lg p-2.5 flex items-center gap-2 mb-2">
                                <span class="w-4 h-4 rounded bg-red-100 text-red-600 flex items-center justify-center font-bold text-[9px] flex-shrink-0">!</span>
                                <p class="text-[10px] text-red-800 font-bold" x-text="selected.nearbyProviders?.emergency_numbers"></p>
                            </div>
                            <a :href="selected.nearbyProviders?.maps_search_url || ('https://www.google.com/maps/search/' + encodeURIComponent('hospital clinic near ' + (selected.nearbyLocation || '')))" target="_blank" class="flex items-center justify-center gap-1.5 w-full py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 transition-colors text-[10px] font-semibold text-slate-600">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Search on Google Maps ↗
                            </a>
                        </div>

                        {{-- Watermark --}}
                        <div class="pt-4 mt-2 border-t border-slate-100 text-center">
                            <p class="text-[10px] text-slate-400 font-medium">
                                Generated by <span class="text-primary font-semibold">Pharmasis</span> — Know Your Medicine
                            </p>
                            <p class="text-[9px] text-slate-300 mt-0.5">This report is for educational purposes only. Consult a healthcare professional for medical advice.</p>
                        </div>

                    </div>
                </template>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('medicheckHistory', () => ({
            history: [],
            selected: null,
            copyFeedback: '',
            showClearConfirm: false,

            loadHistory() {
                try {
                    this.history = JSON.parse(localStorage.getItem('medicheckHistory') || '[]');
                } catch (e) {
                    this.history = [];
                }
            },

            confirmClearHistory() {
                localStorage.removeItem('medicheckHistory');
                this.history = [];
                this.selected = null;
                this.showClearConfirm = false;
            },

            openModal(item) {
                this.selected = item;
                document.body.style.overflow = 'hidden';
            },

            closeModal() {
                this.selected = null;
                document.body.style.overflow = '';
            },

            formatDate(isoString) {
                if (!isoString) return '';
                const d = new Date(isoString);
                return d.toLocaleDateString('id-ID', {
                    day: 'numeric', month: 'short', year: 'numeric',
                    hour: '2-digit', minute: '2-digit'
                });
            },

            /* ── Build plain-text report from history item ── */
            buildHistoryReport(item) {
                if (!item || !item.result) return '';
                const r = item.result;
                const watermark = '\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n  Generated by Pharmasis — Know Your Medicine\n  https://pharmasis.biz.id\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n';
                let text = '\n';
                text += '        PHARMASIS MEDCHECK REPORT\n';
                text += '\n';
                text += `Date: ${new Date(item.date).toLocaleString()}\n`;
                text += `Symptoms: ${r.symptoms || 'N/A'}\n\n`;

                if (r.step1?.conditions?.length) {
                    text += 'DIFFERENTIAL DIAGNOSIS\n';
                    r.step1.conditions.forEach(c => {
                        text += `[${c.likelihood}] ${c.name}\n  ${c.description}\n\n`;
                    });
                    if (r.step1.summary) text += `Summary: ${r.step1.summary}\n\n`;
                }

                if (r.step2?.drugs?.length) {
                    text += 'DRUG RECOMMENDATIONS\n';
                    r.step2.drugs.forEach(d => {
                        text += `• ${d.name} (${d.generic})\n`;
                        text += `  Class: ${d.drug_class || 'N/A'} | OTC: ${d.otc ? 'Yes' : 'Rx Only'}\n`;
                        text += `  Dosage: ${d.dose || 'N/A'} | Frequency: ${d.frequency || 'N/A'}\n`;
                        text += `  Purpose: ${d.purpose || 'N/A'}\n`;
                        if (d.mechanism) text += `  Mechanism: ${d.mechanism}\n`;
                        if (d.how_to_take) text += `  How to Take: ${d.how_to_take}\n`;
                        if (d.side_effects?.length) text += `  Side Effects: ${d.side_effects.join(', ')}\n`;
                        if (d.contraindications?.length) text += `  Contraindications: ${d.contraindications.join(', ')}\n`;
                        text += '\n';
                    });
                    if (r.step2.pharmacist_notes) text += `Pharmacist Note: ${r.step2.pharmacist_notes}\n\n`;
                }

                if (r.step3?.interactions?.length) {
                    text += 'DRUG INTERACTIONS\n';
                    r.step3.interactions.forEach(i => {
                        text += `[${i.severity}] ${i.drug_a} + ${i.drug_b}\n  Effect: ${i.effect}\n`;
                        if (i.substitute) text += `  Substitute: ${i.substitute}\n`;
                        text += '\n';
                    });
                }

                if (r.step4) {
                    text += 'RECOVERY PLAN\n';
                    if (r.step4.recovery_timeline) text += `Timeline: ${r.step4.recovery_timeline}\n`;
                    if (r.step4.lifestyle_tips?.length) text += `Lifestyle: ${r.step4.lifestyle_tips.join('; ')}\n`;
                    if (r.step4.diet_recommendations?.length) text += `Diet: ${r.step4.diet_recommendations.join('; ')}\n`;
                    if (r.step4.warning_signs?.length) text += `Warning Signs: ${r.step4.warning_signs.join('; ')}\n`;
                    if (r.step4.follow_up) text += `Follow-up: ${r.step4.follow_up}\n`;
                    if (r.step4.disclaimer) text += `Disclaimer: ${r.step4.disclaimer}\n`;
                    text += '\n';
                }

                text += watermark;
                return text;
            },

            /* ── Copy selected history item ── */
            async copySelected() {
                if (!this.selected) return;
                const text = this.buildHistoryReport(this.selected);
                try {
                    await navigator.clipboard.writeText(text);
                    this.copyFeedback = 'Copied!';
                    setTimeout(() => this.copyFeedback = '', 2000);
                } catch (e) {
                    const ta = document.createElement('textarea');
                    ta.value = text;
                    document.body.appendChild(ta);
                    ta.select();
                    document.execCommand('copy');
                    document.body.removeChild(ta);
                    this.copyFeedback = 'Copied!';
                    setTimeout(() => this.copyFeedback = '', 2000);
                }
            },

            /* ── Share selected history item ── */
            async shareSelected() {
                if (!this.selected) return;
                const text = this.buildHistoryReport(this.selected);
                const shareData = {
                    title: 'Pharmasis MediCheck Report',
                    text: text,
                };
                if (navigator.canShare && navigator.canShare(shareData)) {
                    try {
                        await navigator.share(shareData);
                    } catch (e) {
                        if (e.name !== 'AbortError') {
                            await navigator.clipboard.writeText(text);
                            this.copyFeedback = 'Shared!';
                            setTimeout(() => this.copyFeedback = '', 2000);
                        }
                    }
                } else {
                    await navigator.clipboard.writeText(text);
                    this.copyFeedback = 'Copied for sharing!';
                    setTimeout(() => this.copyFeedback = '', 2000);
                }
            },

            /* ── Build clean HTML for PDF from history ── */
            buildHistoryPrintHTML(item) {
                if (!item || !item.result) return '';
                const r = item.result;
                const dateStr = new Date(item.date).toLocaleString();
                const symptoms = r.symptoms || 'N/A';
                const loc = item.nearbyLocation || null;
                const nearby = item.nearbyProviders || null;
                let html = '';

                html += '<div style="text-align:center;margin-bottom:2rem;border-bottom:2px solid #3EAEB1;padding-bottom:1rem;">';
                html += '<h1 style="font-size:1.6rem;font-weight:800;color:#0d4f52;margin:0;font-family:system-ui,sans-serif;">Pharmasis MediCheck Report</h1>';
                html += '<p style="font-size:0.75rem;color:#64748b;margin:0.5rem 0 0;">Generated on ' + dateStr + '</p>';
                html += '</div>';

                html += '<div style="margin-bottom:1.5rem;background:#f8fafc;border:1px solid #e2e8f0;border-radius:0.5rem;padding:1rem;">';
                html += '<p style="font-size:0.7rem;font-weight:700;color:#3EAEB1;text-transform:uppercase;letter-spacing:0.05em;margin:0 0 0.5rem;">Symptoms</p>';
                html += '<p style="font-size:0.9rem;color:#334155;margin:0;font-style:italic;">"' + this._esc(symptoms) + '"</p>';
                html += '</div>';

                if (r.step1?.conditions?.length) {
                    html += '<h2 style="font-size:1rem;font-weight:700;color:#0f172a;margin:1.5rem 0 0.75rem;border-left:3px solid #3EAEB1;padding-left:0.5rem;">Differential Diagnosis</h2>';
                    r.step1.conditions.forEach(c => {
                        const badgeColor = c.likelihood === 'High' ? '#dc2626' : c.likelihood === 'Moderate' ? '#d97706' : '#059669';
                        const badgeBg = c.likelihood === 'High' ? '#fee2e2' : c.likelihood === 'Moderate' ? '#fef3c7' : '#d1fae5';
                        html += '<div style="margin-bottom:0.75rem;border:1px solid #e2e8f0;border-radius:0.5rem;padding:0.75rem;background:#fff;">';
                        html += '<div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.25rem;">';
                        html += '<span style="font-size:0.6rem;font-weight:700;padding:0.15rem 0.4rem;border-radius:0.25rem;text-transform:uppercase;background:' + badgeBg + ';color:' + badgeColor + ';">' + this._esc(c.likelihood) + '</span>';
                        html += '<span style="font-size:0.85rem;font-weight:700;color:#1e293b;">' + this._esc(c.name) + '</span>';
                        html += '</div>';
                        html += '<p style="font-size:0.8rem;color:#475569;margin:0;">' + this._esc(c.description) + '</p>';
                        html += '</div>';
                    });
                    if (r.step1.summary) html += '<p style="font-size:0.8rem;color:#64748b;font-style:italic;margin:0.5rem 0 0;">' + this._esc(r.step1.summary) + '</p>';
                }

                if (r.step2?.drugs?.length) {
                    html += '<h2 style="font-size:1rem;font-weight:700;color:#0f172a;margin:1.5rem 0 0.75rem;border-left:3px solid #3EAEB1;padding-left:0.5rem;">Drug Recommendations</h2>';
                    r.step2.drugs.forEach(d => {
                        html += '<div style="margin-bottom:1rem;border:1px solid #e2e8f0;border-radius:0.5rem;padding:1rem;background:#fff;page-break-inside:avoid;">';
                        html += '<div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.5rem;flex-wrap:wrap;">';
                        html += '<span style="font-size:0.9rem;font-weight:700;color:#0f172a;">' + this._esc(d.name) + '</span>';
                        html += '<span style="font-size:0.6rem;padding:0.15rem 0.4rem;border-radius:0.25rem;background:#f1f5f9;color:#475569;">' + this._esc(d.generic) + '</span>';
                        html += '<span style="font-size:0.6rem;padding:0.15rem 0.4rem;border-radius:0.25rem;background:' + (d.otc ? '#d1fae5;color:#065f46' : '#ede9fe;color:#5b21b6') + ';">' + (d.otc ? 'OTC' : 'Rx Only') + '</span>';
                        if (d.drug_class) html += '<span style="font-size:0.6rem;padding:0.15rem 0.4rem;border-radius:0.25rem;background:#f1f5f9;color:#475569;">' + this._esc(d.drug_class) + '</span>';
                        html += '</div>';
                        html += '<table style="width:100%;border-collapse:collapse;font-size:0.8rem;margin-bottom:0.5rem;">';
                        if (d.dose) html += '<tr><td style="padding:0.25rem 0;color:#64748b;width:30%;">Dosage</td><td style="padding:0.25rem 0;color:#334155;font-weight:600;">' + this._esc(d.dose) + '</td></tr>';
                        if (d.frequency) html += '<tr><td style="padding:0.25rem 0;color:#64748b;">Frequency</td><td style="padding:0.25rem 0;color:#334155;font-weight:600;">' + this._esc(d.frequency) + '</td></tr>';
                        if (d.duration) html += '<tr><td style="padding:0.25rem 0;color:#64748b;">Duration</td><td style="padding:0.25rem 0;color:#334155;font-weight:600;">' + this._esc(d.duration) + '</td></tr>';
                        if (d.route) html += '<tr><td style="padding:0.25rem 0;color:#64748b;">Route</td><td style="padding:0.25rem 0;color:#334155;font-weight:600;">' + this._esc(d.route) + '</td></tr>';
                        html += '</table>';
                        if (d.purpose) html += '<div style="background:#eff6ff;border-radius:0.375rem;padding:0.5rem;margin-bottom:0.5rem;"><p style="font-size:0.65rem;font-weight:700;color:#3b82f6;text-transform:uppercase;margin:0 0 0.25rem;">Purpose</p><p style="font-size:0.8rem;color:#1e3a8a;margin:0;">' + this._esc(d.purpose) + '</p></div>';
                        if (d.mechanism) html += '<div style="background:#eef2ff;border-radius:0.375rem;padding:0.5rem;margin-bottom:0.5rem;"><p style="font-size:0.65rem;font-weight:700;color:#6366f1;text-transform:uppercase;margin:0 0 0.25rem;">Mechanism</p><p style="font-size:0.8rem;color:#3730a3;margin:0;">' + this._esc(d.mechanism) + '</p></div>';
                        if (d.how_to_take) html += '<div style="background:#ecfdf5;border-radius:0.375rem;padding:0.5rem;margin-bottom:0.5rem;"><p style="font-size:0.65rem;font-weight:700;color:#059669;text-transform:uppercase;margin:0 0 0.25rem;">How to Take</p><p style="font-size:0.8rem;color:#064e3b;margin:0;">' + this._esc(d.how_to_take) + '</p></div>';
                        if (d.side_effects?.length) html += '<p style="font-size:0.75rem;color:#b45309;margin:0;"><strong>Side Effects:</strong> ' + d.side_effects.map(s => this._esc(s)).join(', ') + '</p>';
                        if (d.contraindications?.length) html += '<p style="font-size:0.75rem;color:#dc2626;margin:0.25rem 0 0;"><strong>Contraindications:</strong> ' + d.contraindications.map(s => this._esc(s)).join(', ') + '</p>';
                        html += '</div>';
                    });
                    if (r.step2.pharmacist_notes) html += '<div style="background:#fffbeb;border:1px solid #fcd34d;border-radius:0.5rem;padding:0.75rem;margin-bottom:1rem;"><p style="font-size:0.7rem;font-weight:700;color:#b45309;text-transform:uppercase;margin:0 0 0.25rem;">Pharmacist Note</p><p style="font-size:0.8rem;color:#78350f;margin:0;">' + this._esc(r.step2.pharmacist_notes) + '</p></div>';
                }

                if (r.step3?.interactions?.length) {
                    html += '<h2 style="font-size:1rem;font-weight:700;color:#0f172a;margin:1.5rem 0 0.75rem;border-left:3px solid #3EAEB1;padding-left:0.5rem;">Drug Interactions</h2>';
                    r.step3.interactions.forEach(i => {
                        const bg = i.severity === 'Avoid' ? '#fef2f2' : i.severity === 'Caution' ? '#fffbeb' : '#f8fafc';
                        const border = i.severity === 'Avoid' ? '#fecaca' : i.severity === 'Caution' ? '#fcd34d' : '#e2e8f0';
                        const badgeColor = i.severity === 'Avoid' ? '#991b1b' : i.severity === 'Caution' ? '#92400e' : '#334155';
                        const badgeBg = i.severity === 'Avoid' ? '#fecaca' : i.severity === 'Caution' ? '#fde68a' : '#e2e8f0';
                        html += '<div style="margin-bottom:0.5rem;border:1px solid ' + border + ';border-radius:0.5rem;padding:0.75rem;background:' + bg + ';">';
                        html += '<span style="font-size:0.65rem;font-weight:700;padding:0.15rem 0.4rem;border-radius:0.25rem;background:' + badgeBg + ';color:' + badgeColor + ';">' + this._esc(i.severity) + '</span>';
                        html += '<p style="font-size:0.85rem;font-weight:600;color:#1e293b;margin:0.25rem 0;">' + this._esc(i.drug_a) + ' + ' + this._esc(i.drug_b) + '</p>';
                        html += '<p style="font-size:0.8rem;color:#475569;margin:0;">' + this._esc(i.effect) + '</p>';
                        if (i.substitute) html += '<p style="font-size:0.8rem;color:#059669;margin:0.25rem 0 0;">Substitute: ' + this._esc(i.substitute) + '</p>';
                        html += '</div>';
                    });
                }

                if (r.step4) {
                    html += '<h2 style="font-size:1rem;font-weight:700;color:#0f172a;margin:1.5rem 0 0.75rem;border-left:3px solid #3EAEB1;padding-left:0.5rem;">Recovery Plan</h2>';
                    if (r.step4.recovery_timeline) html += '<div style="display:flex;align-items:flex-start;gap:0.75rem;background:#f0fdfa;border-radius:0.5rem;padding:0.75rem;margin-bottom:0.75rem;"><span style="font-size:1.1rem;"></span><div><p style="font-size:0.65rem;font-weight:700;color:#0f766e;text-transform:uppercase;margin:0 0 0.25rem;">Timeline</p><p style="font-size:0.85rem;color:#134e4a;margin:0;font-weight:600;">' + this._esc(r.step4.recovery_timeline) + '</p></div></div>';
                    html += '<div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;margin-bottom:0.75rem;">';
                    if (r.step4.lifestyle_tips?.length) {
                        html += '<div style="background:#ecfdf5;border-radius:0.5rem;padding:0.75rem;">';
                        html += '<p style="font-size:0.65rem;font-weight:700;color:#059669;text-transform:uppercase;margin:0 0 0.5rem;">Lifestyle</p>';
                        r.step4.lifestyle_tips.forEach(t => html += '<p style="font-size:0.8rem;color:#064e3b;margin:0 0 0.25rem;">• ' + this._esc(t) + '</p>');
                        html += '</div>';
                    }
                    if (r.step4.diet_recommendations?.length) {
                        html += '<div style="background:#f0fdfa;border-radius:0.5rem;padding:0.75rem;">';
                        html += '<p style="font-size:0.65rem;font-weight:700;color:#0f766e;text-transform:uppercase;margin:0 0 0.5rem;">Diet</p>';
                        r.step4.diet_recommendations.forEach(d => html += '<p style="font-size:0.8rem;color:#134e4a;margin:0 0 0.25rem;">• ' + this._esc(d) + '</p>');
                        html += '</div>';
                    }
                    html += '</div>';
                    if (r.step4.warning_signs?.length) {
                        html += '<div style="background:#fef2f2;border:1px solid #fecaca;border-radius:0.5rem;padding:0.75rem;margin-bottom:0.75rem;">';
                        html += '<p style="font-size:0.7rem;font-weight:700;color:#dc2626;text-transform:uppercase;margin:0 0 0.5rem;">Seek Medical Help If</p>';
                        r.step4.warning_signs.forEach(w => html += '<p style="font-size:0.8rem;color:#991b1b;margin:0 0 0.25rem;">• ' + this._esc(w) + '</p>');
                        html += '</div>';
                    }
                    if (r.step4.follow_up) html += '<div style="background:#f8fafc;border-radius:0.5rem;padding:0.75rem;margin-bottom:0.5rem;"><p style="font-size:0.65rem;font-weight:700;color:#64748b;text-transform:uppercase;margin:0 0 0.25rem;">Follow-up</p><p style="font-size:0.8rem;color:#334155;margin:0;">' + this._esc(r.step4.follow_up) + '</p></div>';
                    if (r.step4.disclaimer) html += '<p style="font-size:0.75rem;color:#94a3b8;font-style:italic;margin:1rem 0 0;border-top:1px solid #e2e8f0;padding-top:0.75rem;">' + this._esc(r.step4.disclaimer) + '</p>';
                }

                if (nearby?.providers?.length) {
                    html += '<h2 style="font-size:1rem;font-weight:700;color:#0f172a;margin:1.5rem 0 0.75rem;border-left:3px solid #3EAEB1;padding-left:0.5rem;">Nearby Healthcare Providers</h2>';
                    html += '<div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;">';
                    nearby.providers.forEach(p => {
                        html += '<div style="border:1px solid #e2e8f0;border-radius:0.5rem;padding:0.75rem;background:#fff;">';
                        html += '<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.25rem;">';
                        html += '<p style="font-size:0.85rem;font-weight:700;color:#0f172a;margin:0;">' + this._esc(p.name) + '</p>';
                        html += '<span style="font-size:0.6rem;padding:0.1rem 0.35rem;border-radius:0.25rem;background:#f1f5f9;color:#475569;font-weight:600;">' + this._esc(p.type) + '</span>';
                        html += '</div>';
                        html += '<p style="font-size:0.75rem;color:#475569;margin:0 0 0.5rem;">' + this._esc(p.address) + '</p>';
                        html += '</div>';
                    });
                    html += '</div>';
                    if (nearby.emergency_numbers) html += '<div style="background:#fef2f2;border:1px solid #fecaca;border-radius:0.5rem;padding:0.75rem;margin-top:0.75rem;display:flex;align-items:center;gap:0.5rem;"><span style="width:1.25rem;height:1.25rem;border-radius:0.25rem;background:#fee2e2;color:#dc2626;display:flex;align-items:center;justify-content:center;font-size:0.6rem;font-weight:700;flex-shrink:0;">!</span><p style="font-size:0.8rem;color:#991b1b;font-weight:700;margin:0;">' + this._esc(nearby.emergency_numbers) + '</p></div>';
                }

                html += '<div style="margin-top:2rem;padding-top:1rem;border-top:2px solid #3EAEB1;text-align:center;">';
                html += '<p style="font-size:0.75rem;color:#3EAEB1;font-weight:600;margin:0;">Pharmasis — Know Your Medicine</p>';
                html += '<p style="font-size:0.65rem;color:#94a3b8;margin:0.25rem 0 0;">This report is for educational purposes only. Consult a healthcare professional for medical advice.</p>';
                html += '</div>';

                return html;
            },

            _esc(str) {
                if (!str) return '';
                return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
            },

            /* ── Print selected history item as PDF ── */
            printSelected() {
                if (!this.selected) return;
                const html = this.buildHistoryPrintHTML(this.selected);
                if (!html) return;
                const printWin = window.open('', '_blank');
                printWin.document.write(`
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <title>Pharmasis MediCheck Report</title>
                        <meta charset="utf-8">
                        <style>
                            @page { margin: 1.5cm; size: A4; }
                            body { margin: 0; padding: 0; font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; color: #334155; background: #fff; line-height: 1.5; }
                            .watermark { position: fixed; inset: 0; pointer-events: none; z-index: 0; }
                            .watermark > div { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-30deg); opacity: 0.06; font-size: 5rem; font-weight: 800; color: #3EAEB1; white-space: nowrap; letter-spacing: 0.1em; }
                            .content { position: relative; z-index: 1; }
                            @media print { .no-print { display: none !important; } }
                        </style>
                    </head>
                    <body>
                        <div class="watermark"><div>Pharmasis</div></div>
                        <div class="content" style="padding: 1.5cm;">${html}</div>
                        <div class="no-print" style="text-align:center;padding: 2rem;">
                            <button onclick="window.print()" style="padding: 0.6rem 2rem; border-radius: 999px; border: none; background: linear-gradient(135deg,#3EAEB1,#2d8a8d); color: #fff; font-weight: 700; font-size: 0.9rem; cursor: pointer; box-shadow: 0 4px 16px rgba(62,174,177,0.35);">Print / Save as PDF</button>
                        </div>
                    </body>
                    </html>
                `);
                printWin.document.close();
                printWin.focus();
                setTimeout(() => printWin.print(), 400);
            },

            init() {
                this.$watch('selected', value => {
                    if (!value) document.body.style.overflow = '';
                });
                this.$watch('showClearConfirm', value => {
                    if (value) {
                        document.body.style.overflow = 'hidden';
                    } else {
                        document.body.style.overflow = '';
                    }
                });
            }
        }));
    });
</script>

<style>
    .hide-scrollbar::-webkit-scrollbar { display: none; }
    .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    .custom-scrollbar::-webkit-scrollbar { width: 5px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: linear-gradient(to bottom, #3EAEB1, #9FD8E1);
        border-radius: 10px;
    }
</style>