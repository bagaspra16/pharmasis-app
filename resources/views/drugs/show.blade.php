@extends('layouts.app')

@section('title', $drug->name . ' — Pharmasis')
@section('meta_description', 'Drug information for ' . $drug->name . ($drug->generic_name ? ' (' . $drug->generic_name .
')' : '') . '. Uses, warnings, dosage, side effects and more.')

@section('content')

@php
$cleanUses = \App\Models\Drug::cleanField($drug->uses);
$cleanWarnings = \App\Models\Drug::cleanField($drug->warnings);
$cleanDosage = \App\Models\Drug::cleanField($drug->dosage);
$cleanSideEffects = \App\Models\Drug::cleanField($drug->side_effects);
$cleanInteractions = \App\Models\Drug::cleanField($drug->interactions);
$beforeItems = \App\Models\Drug::cleanBeforeTaking($drug->before_taking);
$riskLevel = $drug->risk_level;
$riskColors = ['minor' => 'text-emerald-600 bg-emerald-50 border-emerald-200', 'moderate' => 'text-amber-600 bg-amber-50
border-amber-200', 'major' => 'text-red-600 bg-red-50 border-red-200'];
$riskColor = $riskColors[$riskLevel] ?? $riskColors['minor'];
@endphp

<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex flex-col lg:flex-row gap-8">

        {{-- ── Main Content Column ── --}}
        <div class="flex-1 min-w-0">

            {{-- Breadcrumb --}}
            <nav class="flex items-center gap-2 text-sm text-slate-400 mb-4">
                <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Home</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <a href="{{ route('drugs.search') }}" class="hover:text-primary transition-colors">Medicines</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-slate-600 truncate max-w-xs">{{ $drug->name }}</span>
            </nav>

            @if(!empty($fdaMode))
            <div class="mb-4 flex items-center gap-2 bg-blue-50 border border-blue-100 rounded-xl px-4 py-3">
                <svg class="w-4 h-4 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-xs text-blue-700">
                    <strong>OpenFDA Data Source</strong> — Local database is offline. This information is sourced
                    directly from the
                    <a href="https://open.fda.gov" target="_blank" class="underline">U.S. Food & Drug
                        Administration</a>.
                </p>
            </div>
            @endif

            {{-- Hero Identity --}}
            <div class="bg-white rounded-2xl border border-slate-100 p-6 sm:p-8 mb-6 shadow-sm">
                <div class="flex items-start gap-5">
                    <div
                        class="w-16 h-16 bg-gradient-to-br from-primary to-secondary rounded-2xl flex items-center justify-center flex-shrink-0 shadow-md">
                        <span class="text-2xl font-extrabold text-white">{{ strtoupper(substr($drug->alpha_index ??
                            $drug->name ?? '?', 0, 2)) }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-2">
                            @if($drug->alpha_index)
                            <span class="text-xs bg-primary/10 text-primary font-semibold px-2.5 py-1 rounded-full">{{
                                strtoupper($drug->alpha_index) }}</span>
                            @endif
                            @if($drug->translated)
                            <span
                                class="text-xs bg-green-100 text-green-700 font-medium px-2.5 py-1 rounded-full flex items-center gap-1">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                                Verified
                            </span>
                            @endif
                            <span
                                class="text-xs border {{ $riskColor }} px-2.5 py-1 rounded-full font-semibold capitalize">⚠
                                {{ $riskLevel }} risk</span>
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 leading-tight">{{ $drug->name }}
                        </h1>
                        @if($drug->generic_name)
                        <p class="text-slate-500 mt-1">Generic: <span class="font-medium text-slate-700">{{
                                $drug->generic_name }}</span></p>
                        @endif
                        @if($drug->drug_class)
                        <p class="text-slate-500 text-sm mt-0.5">Class: <a
                                href="{{ route('drugs.search', ['drug_class' => $drug->drug_class]) }}"
                                class="text-primary hover:underline font-medium">{{ $drug->drug_class }}</a></p>
                        @endif
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex flex-wrap gap-3 mt-6 pt-5 border-t border-slate-100" x-data="bookmarks()">
                    <button @click="toggle('{{ $drug->id }}', '{{ addslashes($drug->name) }}')"
                        class="flex items-center gap-2 px-4 py-2 rounded-xl border transition-all text-sm font-medium"
                        :class="isBookmarked('{{ $drug->id }}') ? 'bg-primary text-white border-primary' : 'border-slate-200 text-slate-600 hover:border-primary hover:text-primary'">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z" />
                        </svg>
                        <span x-text="isBookmarked('{{ $drug->id }}') ? 'Bookmarked' : 'Bookmark'"></span>
                    </button>
                    @if($drug->url)
                    <a href="{{ $drug->url }}" target="_blank" rel="noopener"
                        class="flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-200 text-slate-600 hover:border-primary hover:text-primary transition-all text-sm font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                        Source ({{ $drug->source ?? 'WebMD' }})
                    </a>
                    @endif
                </div>
            </div>

            {{-- Content Sections --}}
            <div class="space-y-4" x-data="{ openSections: ['uses'] }">

                @php
                $sections = [
                ['id' => 'uses', 'label' => 'Uses & Indications', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0
                012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'content' => $cleanUses,
                'field' => 'uses'],
                ['id' => 'warnings', 'label' => 'Warnings & Precautions','icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54
                0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
                'content' => $cleanWarnings, 'field' => 'warnings'],
                ['id' => 'dosage', 'label' => 'Dosage & Administration','icon' => 'M19.428 15.428a2 2 0
                00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8
                4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782
                0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z', 'content' => $cleanDosage, 'field' => 'dosage'],
                ['id' => 'side_effects', 'label' => 'Side Effects', 'icon' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18
                0 9 9 0 0118 0z', 'content' => $cleanSideEffects, 'field' => 'side_effects'],
                ['id' => 'interactions', 'label' => 'Drug Interactions', 'icon' => 'M4 4v5h.582m15.356 2A8.001 8.001 0
                004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15', 'content' =>
                $cleanInteractions, 'field' => 'interactions'],
                ];
                @endphp

                @foreach($sections as $section)
                @if($section['content'])
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden"
                    x-data="{ open: {{ in_array($section['id'], ['uses']) ? 'true' : 'false' }}, aiText: '', aiLoading: false, aiError: '' }">
                    <button @click="open = !open"
                        class="w-full flex items-center justify-between px-6 py-5 text-left hover:bg-slate-50 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-primary/10 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="{{ $section['icon'] }}" />
                                </svg>
                            </div>
                            <span class="font-semibold text-slate-800">{{ $section['label'] }}</span>
                        </div>
                        <svg class="w-5 h-5 text-slate-400 transition-transform duration-200"
                            :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="open" x-collapse class="border-t border-slate-100">
                        <div class="px-6 py-5">
                            <p class="text-slate-700 leading-relaxed text-sm">{{ $section['content'] }}</p>

                            {{-- AI Simplifier --}}
                            <div class="mt-4 pt-4 border-t border-slate-50">
                                <div x-show="!aiText && !aiLoading">
                                    <button
                                        @click="simplify('{{ $drug->id }}', '{{ $section['field'] }}', `{{ addslashes($section['content']) }}`)"
                                        class="inline-flex items-center gap-2 text-xs text-primary hover:text-primary-dark font-medium transition-colors group">
                                        <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                        </svg>
                                        Explain in simple terms (AI)
                                    </button>
                                </div>

                                {{-- Loading --}}
                                <div x-show="aiLoading" class="flex items-center gap-2 text-xs text-slate-500">
                                    <svg class="animate-spin w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4" />
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                                    </svg>
                                    AI is simplifying this...
                                </div>

                                {{-- Error --}}
                                <div x-show="aiError" class="text-xs text-red-500 mt-1" x-text="aiError"></div>

                                {{-- AI Result --}}
                                <div x-show="aiText" x-cloak
                                    class="mt-3 bg-primary-light rounded-xl p-4 border border-primary/20">
                                    <div class="flex items-center gap-2 mb-2">
                                        <svg class="w-4 h-4 text-primary flex-shrink-0" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                        </svg>
                                        <span class="text-xs font-semibold text-primary">AI Plain Language
                                            Explanation</span>
                                        <span class="text-xs text-slate-400 ml-auto">Educational use only</span>
                                    </div>
                                    <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-line"
                                        x-text="aiText"></p>
                                    <button @click="aiText = ''"
                                        class="text-xs text-slate-400 hover:text-slate-600 mt-2 transition-colors">Hide</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                @endforeach

            </div>{{-- end sections --}}

            {{-- Before Taking Checklist --}}
            @if(count($beforeItems) > 0)
            <div class="mt-4 bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden"
                x-data="{ open: false, checkedItems: {}, showWarning: false }">
                <button @click="open = !open"
                    class="w-full flex items-center justify-between px-6 py-5 hover:bg-slate-50 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                        </div>
                        <span class="font-semibold text-slate-800">Before Taking Checklist</span>
                        <span class="text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full font-medium">{{
                            count($beforeItems) }} items</span>
                    </div>
                    <svg class="w-5 h-5 text-slate-400 transition-transform" :class="open ? 'rotate-180' : ''"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" class="border-t border-slate-100 px-6 py-5">
                    <p class="text-sm text-slate-500 mb-4">Check the conditions that apply to you before taking this
                        medication:</p>
                    <div class="space-y-3">
                        @foreach($beforeItems as $idx => $item)
                        <label class="flex items-start gap-3 cursor-pointer group">
                            <input type="checkbox"
                                @change="checkedItems[{{ $idx }}] = $event.target.checked; showWarning = Object.values(checkedItems).some(Boolean)"
                                class="mt-0.5 w-4 h-4 text-amber-500 rounded border-slate-300 focus:ring-amber-400">
                            <span
                                class="text-sm text-slate-700 leading-relaxed group-hover:text-slate-900 transition-colors">{{
                                $item }}</span>
                        </label>
                        @endforeach
                    </div>
                    <div x-show="showWarning" x-cloak class="mt-4 p-4 bg-amber-50 border border-amber-200 rounded-xl">
                        <div class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            <div>
                                <p class="text-sm font-semibold text-amber-800">Talk to your doctor</p>
                                <p class="text-xs text-amber-700 mt-0.5">One or more conditions apply to you. Please
                                    consult a healthcare professional before taking this medication.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

        </div>{{-- end main column --}}

        {{-- ── Sidebar (Sticky Quick Summary) ── --}}
        <aside class="w-full lg:w-80 flex-shrink-0">
            <div class="sticky top-20 space-y-4">

                {{-- Quick Safety Snapshot --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                    <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        Quick Safety Summary
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center py-2 border-b border-slate-50">
                            <span class="text-xs text-slate-500">Risk Level</span>
                            <span
                                class="text-xs font-semibold px-2.5 py-1 rounded-full capitalize border {{ $riskColor }}">{{
                                $riskLevel }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-slate-50">
                            <span class="text-xs text-slate-500">Drug Class</span>
                            <span class="text-xs font-medium text-slate-700 text-right max-w-[60%]">{{ $drug->drug_class
                                ?: 'Not specified' }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-slate-50">
                            <span class="text-xs text-slate-500">Generic Name</span>
                            <span class="text-xs font-medium text-slate-700 text-right max-w-[60%]">{{
                                $drug->generic_name ?: 'Not specified' }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-slate-50">
                            <span class="text-xs text-slate-500">Data Source</span>
                            <span class="text-xs font-medium text-slate-700 uppercase">{{ $drug->source ?: 'Unknown'
                                }}</span>
                        </div>
                        @if($drug->updated_at && is_object($drug->updated_at) && method_exists($drug->updated_at,
                        'format'))
                        <div class="flex justify-between items-center py-2">
                            <span class="text-xs text-slate-500">Last Updated</span>
                            <span class="text-xs font-medium text-slate-700">{{ $drug->updated_at->format('M Y')
                                }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Sections Navigator --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                    <h3 class="font-bold text-slate-800 mb-3 text-sm">On This Page</h3>
                    <ul class="space-y-1.5">
                        @if($cleanUses) <li><a href="#"
                                class="text-sm text-slate-600 hover:text-primary transition-colors">Uses &
                                Indications</a></li>@endif
                        @if($cleanWarnings) <li><a href="#"
                                class="text-sm text-slate-600 hover:text-primary transition-colors">Warnings &
                                Precautions</a></li>@endif
                        @if(count($beforeItems))<li><a href="#"
                                class="text-sm text-amber-600 hover:text-amber-700 transition-colors font-medium">Before
                                Taking Checklist</a></li>@endif
                        @if($cleanDosage) <li><a href="#"
                                class="text-sm text-slate-600 hover:text-primary transition-colors">Dosage &
                                Administration</a></li>@endif
                        @if($cleanSideEffects) <li><a href="#"
                                class="text-sm text-slate-600 hover:text-primary transition-colors">Side Effects</a>
                        </li>@endif
                        @if($cleanInteractions) <li><a href="#"
                                class="text-sm text-slate-600 hover:text-primary transition-colors">Drug
                                Interactions</a></li>@endif
                    </ul>
                </div>

                {{-- Disclaimer --}}
                <div class="bg-amber-50 rounded-2xl border border-amber-200 p-4">
                    <p class="text-xs text-amber-800 leading-relaxed">
                        <strong>⚠ Educational information only.</strong> This does not replace professional medical
                        advice, diagnosis, or treatment. Always consult a qualified healthcare provider.
                    </p>
                </div>
            </div>
        </aside>

    </div>
</div>

@endsection

@push('scripts')
<script>
    function simplify(drugId, field, text) {
        return async function () {
            this.aiLoading = true;
            this.aiError = '';
            try {
                const res = await fetch('/api/v1/ai/simplify', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify({ drug_id: drugId, field: field, text: text.substring(0, 2000) })
                });
                const data = await res.json();
                if (data.success) { this.aiText = data.text; }
                else { this.aiError = data.error || 'Failed to simplify. Try again.'; }
            } catch (e) { this.aiError = 'Network error. Please try again.'; }
            this.aiLoading = false;
        }
    }

    function bookmarks() {
        return {
            getAll() { try { return JSON.parse(localStorage.getItem('pharmasis_bookmarks') || '{}'); } catch (e) { return {}; } },
            isBookmarked(id) { return !!this.getAll()[id]; },
            toggle(id, name) {
                const bm = this.getAll();
                if (bm[id]) { delete bm[id]; } else { bm[id] = { id, name, savedAt: Date.now() }; }
                localStorage.setItem('pharmasis_bookmarks', JSON.stringify(bm));
                this.$forceUpdate && this.$forceUpdate();
            }
        }
    }
</script>
@endpush