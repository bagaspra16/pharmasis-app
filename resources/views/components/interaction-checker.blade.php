<div x-data="interactionChecker()" class="w-full max-w-4xl mx-auto">
    {{-- Main Container Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">

        {{-- Header & Input Area --}}
        <div class="p-6 md:p-8 border-b border-slate-100 bg-slate-50/50">
            <h2 class="text-2xl font-brand font-extrabold text-slate-800 tracking-tight mb-2">Check Interactions</h2>
            <p class="text-slate-500 text-sm mb-6">Add 2 to 10 medicines to see how they might interact with each other.
            </p>

            {{-- Selected Drugs Chips --}}
            <div class="flex flex-wrap gap-2 mb-4" x-show="selectedDrugs.length > 0">
                <template x-for="(drug, index) in selectedDrugs" :key="drug.id">
                    <div
                        class="flex items-center gap-2 px-3 py-1.5 bg-primary/10 text-primary-dark rounded-full text-sm font-semibold transition-all group">
                        <span x-text="drug.name"></span>
                        <button @click="removeDrug(index)"
                            class="w-4 h-4 rounded-full hover:bg-primary/20 flex items-center justify-center transition-colors focus:outline-none">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </template>
            </div>

            {{-- Search Input (Dropdown Autocomplete) --}}
            <div class="relative" x-show="selectedDrugs.length < 10" @click.outside="open = false; query = ''">
                <div class="relative">
                    <input type="text" x-model="query" @input.debounce.300ms="fetchSuggestions()"
                        @focus="if(query.length >= 2) open = true" @keydown.arrow-down.prevent="focusNext()"
                        @keydown.arrow-up.prevent="focusPrev()" @keydown.enter.prevent="selectFocused()"
                        placeholder="Type a medicine name..."
                        class="w-full pl-11 pr-4 py-3 text-sm border-2 border-slate-200 rounded-xl bg-white hover:border-slate-300 focus:outline-none focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm"
                        autocomplete="off" />
                    <svg class="absolute left-4 top-3.5 w-4 h-4 text-slate-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>

                    <div x-show="isSearching" class="absolute right-4 top-3.5">
                        <svg class="animate-spin w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                    </div>
                </div>

                {{-- Autocomplete Dropdown --}}
                <div x-show="open && suggestions.length > 0" x-cloak
                    class="absolute top-full left-0 right-0 mt-2 bg-white rounded-xl shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden z-[100] transform origin-top transition-all duration-200 max-h-80 overflow-y-auto">
                    <template x-for="(drug, idx) in suggestions" :key="drug.id">
                        <button @click="addDrug(drug)" :class="focusedIdx === idx ? 'bg-slate-50' : 'hover:bg-slate-50'"
                            class="w-full flex items-center gap-3 px-4 py-3 transition-colors text-left border-b border-slate-50 last:border-0 focus:outline-none">
                            <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center flex-shrink-0">
                                <span class="text-xs font-bold text-slate-500"
                                    x-text="drug.alpha_index || drug.name?.charAt(0) || '?'"></span>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-slate-800 truncate" x-text="drug.name"></p>
                                <p class="text-xs text-slate-500 truncate"
                                    x-text="[drug.generic_name, drug.drug_class].filter(Boolean).join(' · ')"></p>
                            </div>
                        </button>
                    </template>
                </div>
                <div x-show="open && suggestions.length === 0 && !isSearching && query.length >= 2" x-cloak
                    class="absolute top-full left-0 right-0 mt-2 bg-white rounded-xl shadow-2xl border border-slate-100 px-4 py-6 text-center z-[100] text-sm text-slate-500">
                    No medicine found for "<span class="font-medium" x-text="query"></span>".
                </div>
            </div>

            <div x-show="selectedDrugs.length === 10" x-cloak
                class="mt-2 text-sm text-amber-600 flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Maximum of 10 medicines reached.
            </div>

            {{-- Action Button --}}
            <div class="mt-6 flex justify-end">
                <button @click="checkInteractions()" :disabled="selectedDrugs.length < 2 || isChecking"
                    :class="selectedDrugs.length < 2 ? 'opacity-50 cursor-not-allowed bg-slate-300' : 'bg-primary hover:bg-primary-dark shadow-md hover:shadow-lg'"
                    class="px-6 py-2.5 text-white font-semibold flex items-center gap-2 rounded-xl transition-all duration-300">
                    <span x-show="!isChecking">Check Interactions</span>
                    <span x-show="isChecking">Checking...</span>
                    <svg x-show="isChecking" class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z">
                        </path>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Results Area --}}
        <div x-show="hasChecked" x-cloak class="p-6 md:p-8 bg-white transition-all">

            {{-- Loading Skeleton --}}
            <div x-show="isChecking" class="space-y-4 animate-pulse">
                <div class="h-20 bg-slate-100 rounded-xl w-full mb-6"></div>
                <div class="h-12 bg-slate-100 rounded-xl w-full"></div>
                <div class="h-12 bg-slate-100 rounded-xl w-full"></div>
                <div class="h-12 bg-slate-100 rounded-xl w-full"></div>
            </div>

            {{-- Final Results --}}
            <div x-show="!isChecking && results">
                {{-- Summary Card --}}
                <div :class="getRiskColor(results?.overall_risk, 'bg')"
                    class="p-5 rounded-2xl mb-8 border transition-colors duration-500 flex items-start gap-4">
                    <div :class="getRiskColor(results?.overall_risk, 'icon-bg')"
                        class="w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg :class="getRiskColor(results?.overall_risk, 'icon-text')" class="w-6 h-6" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-800 text-lg sm:text-lg mb-1"
                            x-text="getRiskTitle(results?.overall_risk)"></h3>
                        <div class="text-sm text-slate-600 leading-relaxed">
                            <p x-show="results?.coverage_status === 'full'">We found complete interaction data for
                                all pairs.</p>
                            <p x-show="results?.coverage_status === 'partial'">Some interaction data was unavailable,
                                so our AI provided careful inferences for missing pairs.</p>

                            {{-- Dynamic AI Explanation for 'none' coverage --}}
                            <div x-show="results?.coverage_status === 'none'">
                                <p class="mb-2">No direct database records found. The following is an AI-generated
                                    inference based on pharmacological drug classes:</p>
                                <p class="font-medium text-slate-700 p-3 bg-slate-50/80 rounded-lg border border-slate-100"
                                    x-text="results?.pairs[0]?.summary"></p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Pairwise Details Accordion --}}
                <div class="space-y-3">
                    <h4 class="font-brand font-bold text-slate-800 text-lg mb-4">Pairwise Breakdown</h4>

                    <template x-for="(pair, idx) in results?.pairs" :key="idx">
                        <div x-data="{ expanded: false }"
                            class="border border-slate-100 rounded-xl overflow-hidden bg-white shadow-sm hover:shadow transition-shadow">
                            {{-- Accordion Header --}}
                            <button @click="expanded = !expanded"
                                class="w-full px-5 py-4 flex items-center justify-between text-left focus:outline-none">
                                <div class="flex items-center gap-3">
                                    <span :class="getRiskColor(pair.risk_level, 'badge')"
                                        class="px-2.5 py-1 text-xs font-bold uppercase tracking-wide rounded-md">
                                        <span x-text="pair.risk_level"></span>
                                    </span>
                                    <p class="font-semibold text-slate-800 text-sm">
                                        <span x-text="pair.drug_a"></span> <span
                                            class="text-slate-400 font-normal mx-1">+</span> <span
                                            x-text="pair.drug_b"></span>
                                    </p>
                                </div>
                                <svg class="w-4 h-4 text-slate-400 transform transition-transform duration-200"
                                    :class="expanded ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            {{-- Accordion Body --}}
                            <div x-show="expanded" x-collapse x-cloak>
                                <div class="px-5 pb-5 pt-1 border-t border-slate-50">
                                    <p class="text-sm text-slate-600 leading-relaxed mb-3" x-text="pair.summary"></p>

                                    {{-- Source / Confidence chip --}}
                                    <div class="mt-4 flex gap-2">
                                        <span x-show="pair.coverage === 'database'"
                                            class="inline-flex items-center gap-1 px-2 py-1 rounded bg-teal-50 text-teal-700 text-xs font-medium">
                                            <svg class="w-3 H-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            High Confidence (Database)
                                        </span>
                                        <span x-show="pair.coverage === 'ai_inferred'"
                                            class="inline-flex items-center gap-1 px-2 py-1 rounded bg-amber-50 text-amber-700 text-xs font-medium">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                                </path>
                                            </svg>
                                            AI-Inferred (Limited Evidence)
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Disclaimer --}}
                <div x-show="results?.disclaimer_required"
                    class="mt-8 pt-6 border-t border-slate-100 flex items-start gap-3">
                    <svg class="w-5 h-5 text-slate-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        <strong>Informational purposes only.</strong> This interaction check is primarily educational
                        and uses an AI summarizer/inferencer. It does not replace professional medical advice.
                        Algorithms and databases may miss potential hazards. Always consult a licensed healthcare
                        professional or pharmacist before starting or adjusting medications.
                    </p>
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
                if (this.query.length < 2) {
                    this.suggestions = [];
                    this.open = false;
                    return;
                }
                this.isSearching = true;
                try {
                    // Reuse existing search endpoint
                    const res = await fetch(`/api/v1/search/instant?q=${encodeURIComponent(this.query)}`);
                    const data = await res.json();

                    // Filter out already selected drugs
                    const selectedIds = this.selectedDrugs.map(d => String(d.id));
                    this.suggestions = (data.data || []).filter(d => !selectedIds.includes(String(d.id)));

                    this.open = true;
                    this.focusedIdx = -1;
                } catch (e) {
                    this.suggestions = [];
                }
                this.isSearching = false;
            },

            focusNext() { this.focusedIdx = Math.min(this.focusedIdx + 1, this.suggestions.length - 1); },
            focusPrev() { this.focusedIdx = Math.max(this.focusedIdx - 1, -1); },

            selectFocused() {
                if (this.focusedIdx >= 0 && this.suggestions[this.focusedIdx]) {
                    this.addDrug(this.suggestions[this.focusedIdx]);
                }
            },

            addDrug(drug) {
                if (this.selectedDrugs.length >= 10) return;
                // Ensure no duplicates
                if (!this.selectedDrugs.some(d => String(d.id) === String(drug.id))) {
                    this.selectedDrugs.push(drug);
                }
                this.query = '';
                this.open = false;
                this.suggestions = [];
                this.hasChecked = false; // reset results if drug combination changes
                // Focus back to input
                this.$nextTick(() => { this.$el.querySelector('input').focus(); });
            },

            removeDrug(index) {
                this.selectedDrugs.splice(index, 1);
                this.hasChecked = false;
            },

            async checkInteractions() {
                if (this.selectedDrugs.length < 2) return;

                this.isChecking = true;
                this.hasChecked = true;
                this.results = null;

                try {
                    const payload = {
                        drug_ids: this.selectedDrugs.map(d => String(d.id))
                    };
                    const res = await fetch('/api/v1/interactions/check', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(payload)
                    });

                    const data = await res.json();
                    if (data.success) {
                        this.results = data.data;
                    } else {
                        alert('Error: ' + data.message);
                        this.hasChecked = false;
                    }
                } catch (e) {
                    alert('Something went wrong checking interactions.');
                    this.hasChecked = false;
                }

                this.isChecking = false;
            },

            getRiskTitle(level) {
                if (level === 'major') return 'Major Interactions Found';
                if (level === 'moderate') return 'Moderate Interactions Found';
                if (level === 'minor') return 'Minor Interactions Found';
                return 'No Significant Interactions Detected';
            },

            getRiskColor(level, part) {
                const colors = {
                    'major': {
                        'bg': 'bg-red-50/50 border-red-100',
                        'icon-bg': 'bg-red-100',
                        'icon-text': 'text-red-500',
                        'badge': 'bg-red-100 text-red-700'
                    },
                    'moderate': {
                        'bg': 'bg-orange-50/50 border-orange-100',
                        'icon-bg': 'bg-orange-100',
                        'icon-text': 'text-orange-500',
                        'badge': 'bg-orange-100 text-orange-700'
                    },
                    'minor': {
                        'bg': 'bg-yellow-50/50 border-yellow-100',
                        'icon-bg': 'bg-yellow-100',
                        'icon-text': 'text-yellow-600',
                        'badge': 'bg-yellow-100 text-yellow-700'
                    },
                    'unknown': {
                        'bg': 'bg-slate-50 border-slate-200',
                        'icon-bg': 'bg-slate-200',
                        'icon-text': 'text-slate-500',
                        'badge': 'bg-slate-100 text-slate-600'
                    }
                };

                const schema = colors[level] || colors['unknown'];
                return schema[part];
            }
        };
    }
</script>
@endpush