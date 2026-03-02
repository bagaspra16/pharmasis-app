@extends('layouts.app')

@section('title', 'Drug Interaction Checker')

@section('content')

{{-- Page Header --}}
<div class="bg-slate-50 mt-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex items-center gap-3">
        <div>
            <h1 class="text-xl font-sans font-bold text-slate-900 leading-tight">Drug Interaction <span
                    class="text-primary">Intelligence Check</span></h1>
            <p class="text-xs text-slate-400 mt-0.5">Add 2–10 medicines · AI-powered risk analysis</p>
        </div>
    </div>
</div>

{{-- Main Interaction UI --}}
<div class="bg-slate-50 min-h-screen" x-data="interactionChecker()">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-8 items-start">

            {{-- LEFT: Input Panel (sticky) --}}
            <div class="lg:col-span-2 lg:sticky lg:top-24 space-y-6">

                {{-- Drug Selector Input --}}
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-3">
                        Search & Add Medicines
                    </label>

                    <div class="relative" @click.outside="open = false; query = ''"
                        @keydown.escape.window="open = false">
                        <div class="relative">
                            <input type="text" x-model="query" @input.debounce.300ms="fetchSuggestions()"
                                @focus="if(query.length >= 2) open = true" @keydown.arrow-down.prevent="focusNext()"
                                @keydown.arrow-up.prevent="focusPrev()" @keydown.enter.prevent="selectFocused()"
                                :disabled="selectedDrugs.length >= 10" placeholder="Search medicines..."
                                class="w-full pl-11 pr-10 py-3.5 text-sm bg-white border-2 border-slate-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-primary/10 focus:border-primary hover:border-slate-300 transition-all shadow-sm placeholder:text-slate-400"
                                autocomplete="off" />
                            <svg class="absolute left-4 top-3.5 w-5 h-5 text-slate-400 pointer-events-none" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <div x-show="isSearching" class="absolute right-4 top-3.5">
                                <svg class="animate-spin w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                            </div>
                        </div>

                        {{-- Autocomplete Dropdown --}}
                        <div x-show="open && suggestions.length > 0" x-cloak
                            class="absolute top-full left-0 right-0 mt-2 bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden z-50 max-h-72 overflow-y-auto">
                            <template x-for="(drug, idx) in suggestions" :key="drug.id">
                                <button @click="addDrug(drug)"
                                    :class="focusedIdx === idx ? 'bg-primary/5' : 'hover:bg-slate-50'"
                                    class="w-full flex items-center gap-3 px-4 py-3 text-left border-b border-slate-50 last:border-0 transition-colors focus:outline-none">
                                    <div
                                        class="w-9 h-9 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0 border border-primary/10">
                                        <span class="text-xs font-bold text-primary uppercase"
                                            x-text="drug.alpha_index || drug.name?.charAt(0) || '?'"></span>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-semibold text-slate-800 truncate" x-text="drug.name"></p>
                                        <p class="text-xs text-slate-400 truncate"
                                            x-text="[drug.generic_name, drug.drug_class].filter(Boolean).join(' · ')">
                                        </p>
                                    </div>
                                    <svg class="w-4 h-4 text-primary/60 flex-shrink-0" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4"></path>
                                    </svg>
                                </button>
                            </template>
                        </div>
                        <div x-show="open && suggestions.length === 0 && !isSearching && query.length >= 2" x-cloak
                            class="absolute top-full left-0 right-0 mt-2 bg-white rounded-2xl shadow-xl border border-slate-100 px-4 py-6 text-center z-50 text-sm text-slate-500">
                            No match for "<span class="font-semibold text-slate-700" x-text="query"></span>"
                        </div>
                    </div>
                </div>

                {{-- Selected Drugs List --}}
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest">
                            Selected Medicines
                            <span class="ml-1.5 px-1.5 py-0.5 bg-primary/10 text-primary rounded-full text-xs"
                                x-text="selectedDrugs.length + '/10'"></span>
                        </label>
                        <button x-show="selectedDrugs.length > 0" @click="selectedDrugs = []; hasChecked = false;"
                            class="text-xs text-slate-400 hover:text-rose-500 transition-colors font-medium">Clear
                            all</button>
                    </div>

                    {{-- Empty state --}}
                    <div x-show="selectedDrugs.length === 0"
                        class="border-2 border-dashed border-slate-200 rounded-2xl p-8 text-center bg-white">
                        <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4"></path>
                            </svg>
                        </div>
                        <p class="text-sm font-semibold text-slate-400">No medicines selected</p>
                        <p class="text-xs text-slate-300 mt-1">Search above to add at least 2</p>
                    </div>

                    {{-- Chips stacked vertically --}}
                    <div class="space-y-2" x-show="selectedDrugs.length > 0">
                        <template x-for="(drug, index) in selectedDrugs" :key="drug.id">
                            <div
                                class="flex items-center gap-3 px-4 py-3 bg-white border border-slate-100 rounded-2xl shadow-sm group hover:border-primary/20 transition-colors">
                                <div
                                    class="w-8 h-8 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
                                    <span class="text-xs font-bold text-primary uppercase"
                                        x-text="drug.alpha_index || drug.name?.charAt(0) || '?'"></span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-slate-800 truncate" x-text="drug.name"></p>
                                    <p class="text-xs text-slate-400 truncate"
                                        x-text="drug.drug_class || drug.generic_name || 'Medicine'"></p>
                                </div>
                                <button @click="removeDrug(index)"
                                    class="w-7 h-7 rounded-full hover:bg-rose-50 flex items-center justify-center transition-colors focus:outline-none flex-shrink-0 opacity-40 group-hover:opacity-100">
                                    <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Check Button --}}
                <button @click="checkInteractions()" :disabled="selectedDrugs.length < 2 || isChecking"
                    :class="selectedDrugs.length < 2 ? 'opacity-50 cursor-not-allowed bg-slate-300 text-slate-500' : 'bg-primary hover:bg-teal-600 text-white shadow-lg shadow-primary/25 hover:shadow-xl hover:shadow-primary/30 hover:-translate-y-0.5'"
                    class="w-full py-4 font-bold font-brand text-base rounded-2xl transition-all duration-300 flex items-center justify-center gap-3">
                    <template x-if="!isChecking">
                        <span class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                </path>
                            </svg>
                            Analyze Interactions
                        </span>
                    </template>
                    <template x-if="isChecking">
                        <span class="flex items-center gap-2 text-white">
                            <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            Analyzing...
                        </span>
                    </template>
                </button>

                {{-- How-it-works note --}}
                <div class="rounded-2xl bg-slate-100/60 p-4 space-y-2.5">
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">How it works</p>
                    <div class="flex items-start gap-2.5 text-xs text-slate-500">
                        <div
                            class="w-5 h-5 bg-teal-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                            <span class="text-teal-600 font-bold text-xs">1</span>
                        </div>
                        Search our drug database for each pair
                    </div>
                    <div class="flex items-start gap-2.5 text-xs text-slate-500">
                        <div
                            class="w-5 h-5 bg-teal-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                            <span class="text-teal-600 font-bold text-xs">2</span>
                        </div>
                        AI summarizes any found interactions clearly
                    </div>
                    <div class="flex items-start gap-2.5 text-xs text-slate-500">
                        <div
                            class="w-5 h-5 bg-amber-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                            <span class="text-amber-600 font-bold text-xs">3</span>
                        </div>
                        Missing data → conservative AI inference by drug class
                    </div>
                </div>
            </div>

            {{-- RIGHT: Results Panel --}}
            <div class="lg:col-span-3 space-y-6">

                {{-- Default Empty State --}}
                <div x-show="!hasChecked" class="flex flex-col items-center justify-center py-24 text-center">
                    <div class="w-24 h-24 bg-slate-100 rounded-3xl flex items-center justify-center mb-6">
                        <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-brand font-bold text-slate-700 mb-2">Ready to analyze</h3>
                    <p class="text-sm text-slate-400 max-w-xs leading-relaxed">Select at least 2 medicines on the left,
                        then click <strong>Analyze Interactions</strong> to see results here.</p>
                </div>

                {{-- Skeleton --}}
                <div x-show="hasChecked && isChecking" class="space-y-4 animate-pulse">
                    <div class="h-28 bg-slate-200 rounded-2xl"></div>
                    <div class="h-16 bg-slate-100 rounded-2xl"></div>
                    <div class="h-16 bg-slate-100 rounded-2xl"></div>
                    <div class="h-16 bg-slate-100 rounded-2xl"></div>
                </div>

                {{-- Real Results --}}
                <div x-show="hasChecked && !isChecking && results" class="space-y-5">

                    {{-- Overall Risk Banner --}}
                    <div :class="getRiskColor(results?.overall_risk, 'banner')"
                        class="rounded-2xl p-5 flex items-center gap-5 transition-colors duration-500">
                        <div :class="getRiskColor(results?.overall_risk, 'icon-bg')"
                            class="w-14 h-14 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg :class="getRiskColor(results?.overall_risk, 'icon-text')" class="w-7 h-7" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span :class="getRiskColor(results?.overall_risk, 'badge')"
                                    class="text-xs font-bold uppercase tracking-widest px-2 py-0.5 rounded-full"
                                    x-text="(results?.overall_risk || 'unknown').toUpperCase()"></span>
                                <span class="text-xs text-slate-500">Overall Risk</span>
                            </div>
                            <p class="font-mothwing text-slate-900 text-2xl"
                                x-text="getRiskTitle(results?.overall_risk)"></p>
                            <p class="text-sm text-slate-600 mt-0.5">
                                <span x-show="results?.coverage_status === 'full'">Complete interaction data found in
                                    our database.</span>
                                <span x-show="results?.coverage_status === 'partial'">Partial data found. AI filled the
                                    gaps conservatively.</span>
                                <span x-show="results?.coverage_status === 'none'">No direct records found. All results
                                    are AI-inferred by drug class.</span>
                            </p>
                        </div>
                    </div>

                    {{-- Pairwise Breakdown --}}
                    <div>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-3">Pairwise Breakdown
                        </p>
                        <div class="space-y-2">
                            <template x-for="(pair, idx) in results?.pairs" :key="idx">
                                <div x-data="{ expanded: false }"
                                    class="bg-white rounded-2xl border border-slate-100 overflow-hidden transition-shadow hover:shadow-md">
                                    <button @click="expanded = !expanded"
                                        class="w-full px-5 py-4 flex items-center justify-between text-left focus:outline-none gap-3">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div :class="getRiskColor(pair.risk_level, 'dot')"
                                                class="w-2.5 h-2.5 rounded-full flex-shrink-0"></div>
                                            <div class="min-w-0">
                                                <p class="text-sm font-bold text-slate-800 truncate">
                                                    <span x-text="pair.drug_a"></span>
                                                    <span class="text-slate-300 font-light mx-2">×</span>
                                                    <span x-text="pair.drug_b"></span>
                                                </p>
                                                <p :class="getRiskColor(pair.risk_level, 'level-text')"
                                                    class="text-xs font-semibold capitalize"
                                                    x-text="pair.risk_level + ' risk'"></p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2 flex-shrink-0">
                                            <span x-show="pair.coverage === 'ai_inferred'"
                                                class="hidden sm:inline-flex items-center gap-1 px-2 py-0.5 bg-amber-50 text-amber-600 text-xs font-semibold rounded-full whitespace-nowrap">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                                    </path>
                                                </svg>
                                                AI inferred
                                            </span>
                                            <span x-show="pair.coverage === 'database'"
                                                class="hidden sm:inline-flex items-center gap-1 px-2 py-0.5 bg-teal-50 text-teal-600 text-xs font-semibold rounded-full whitespace-nowrap">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                Database
                                            </span>
                                            <svg class="w-4 h-4 text-slate-300 transition-transform duration-200"
                                                :class="expanded ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </div>
                                    </button>

                                    <div x-show="expanded" x-collapse x-cloak
                                        :class="getRiskColor(pair.risk_level, 'body-bg')"
                                        class="border-t border-slate-100">
                                        <div class="px-5 py-4">
                                            <p class="text-sm text-slate-700 leading-relaxed" x-text="pair.summary"></p>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Disclaimer Strip --}}
                    <div class="flex items-start gap-3 pt-4 border-t border-slate-200">
                        <svg class="w-4 h-4 text-slate-300 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-xs text-slate-400 leading-relaxed">
                            <strong class="text-slate-500">Informational only.</strong> This check is for educational
                            purposes and does not replace advice from a licensed pharmacist or physician. AI inferences
                            are estimates — always verify with a healthcare professional.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function interactionChecker() {
        return {
            query: '',
            suggestions: [],
            open: false,
            isSearching: false,
            focusedIdx: -1,
            selectedDrugs: [],
            isChecking: false,
            hasChecked: false,
            results: null,

            async fetchSuggestions() {
                if (this.query.length < 2) { this.suggestions = []; this.open = false; return; }
                this.isSearching = true;
                try {
                    const res = await fetch(`/api/v1/search/instant?q=${encodeURIComponent(this.query)}`);
                    const data = await res.json();
                    const selectedIds = this.selectedDrugs.map(d => String(d.id));
                    this.suggestions = (data.data || []).filter(d => !selectedIds.includes(String(d.id)));
                    this.open = true;
                    this.focusedIdx = -1;
                } catch (e) { this.suggestions = []; }
                this.isSearching = false;
            },

            focusNext() { this.focusedIdx = Math.min(this.focusedIdx + 1, this.suggestions.length - 1); },
            focusPrev() { this.focusedIdx = Math.max(this.focusedIdx - 1, -1); },
            selectFocused() {
                if (this.focusedIdx >= 0 && this.suggestions[this.focusedIdx]) this.addDrug(this.suggestions[this.focusedIdx]);
            },

            addDrug(drug) {
                if (this.selectedDrugs.length >= 10) return;
                if (!this.selectedDrugs.some(d => String(d.id) === String(drug.id))) this.selectedDrugs.push(drug);
                this.query = ''; this.open = false; this.suggestions = []; this.hasChecked = false;
                this.$nextTick(() => { this.$el.querySelector('input')?.focus(); });
            },

            removeDrug(index) { this.selectedDrugs.splice(index, 1); this.hasChecked = false; },

            async checkInteractions() {
                if (this.selectedDrugs.length < 2) return;
                this.isChecking = true; this.hasChecked = true; this.results = null;
                try {
                    const res = await fetch('/api/v1/interactions/check', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                        },
                        body: JSON.stringify({ drug_ids: this.selectedDrugs.map(d => String(d.id)) })
                    });
                    const data = await res.json();
                    if (data.success) { this.results = data.data; }
                    else { alert('Error: ' + (data.message || 'Unknown error.')); this.hasChecked = false; }
                } catch (e) { alert('Failed to connect. Please try again.'); this.hasChecked = false; }
                this.isChecking = false;
            },

            getRiskTitle(level) {
                const titles = { major: 'Major Interactions Found', moderate: 'Moderate Interactions Found', minor: 'Minor Interactions Found', unknown: 'No Significant Interactions Detected' };
                return titles[level] || titles.unknown;
            },

            getRiskColor(level, part) {
                const map = {
                    major: { banner: 'bg-red-50 border border-red-100', 'icon-bg': 'bg-red-100', 'icon-text': 'text-red-500', badge: 'bg-red-100 text-red-700', dot: 'bg-red-400', 'level-text': 'text-red-500', 'body-bg': 'bg-red-50/30' },
                    moderate: { banner: 'bg-orange-50 border border-orange-100', 'icon-bg': 'bg-orange-100', 'icon-text': 'text-orange-500', badge: 'bg-orange-100 text-orange-700', dot: 'bg-orange-400', 'level-text': 'text-orange-500', 'body-bg': 'bg-orange-50/30' },
                    minor: { banner: 'bg-yellow-50 border border-yellow-100', 'icon-bg': 'bg-yellow-100', 'icon-text': 'text-yellow-600', badge: 'bg-yellow-100 text-yellow-700', dot: 'bg-yellow-400', 'level-text': 'text-yellow-600', 'body-bg': 'bg-yellow-50/30' },
                    unknown: { banner: 'bg-slate-100 border border-slate-200', 'icon-bg': 'bg-slate-200', 'icon-text': 'text-slate-500', badge: 'bg-slate-200 text-slate-600', dot: 'bg-slate-300', 'level-text': 'text-slate-400', 'body-bg': 'bg-slate-50/50' }
                };
                return (map[level] || map.unknown)[part] || '';
            }
        };
    }
</script>
@endpush
@endsection