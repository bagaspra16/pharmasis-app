@extends('layouts.app')

@section('title', $query ? "Search: {$query} — Pharmasis" : 'Browse Medicines — Pharmasis')
@section('meta_description', 'Browse and search medicines on Pharmasis. Find drug information by name, generic name, or
drug class.')

@push('head')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* ── Select2 Pharmasis Theme ─────────────────────────────────── */
    .select2-container {
        width: 100% !important;
        max-width: 100% !important;
    }

    .select2-container .select2-selection--single {
        height: 38px;
        border: 1px solid #e2e8f0;
        border-radius: 0.75rem;
        background: #fff;
        display: flex;
        align-items: center;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #475569;
        font-size: 0.8125rem;
        line-height: 38px;
        padding-left: 12px;
        padding-right: 28px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #94a3b8;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
        right: 8px;
        width: 20px;
    }

    .select2-container--default.select2-container--focus .select2-selection--single,
    .select2-container--default.select2-container--open .select2-selection--single {
        border-color: #3EAEB1;
        box-shadow: 0 0 0 2px rgba(62, 174, 177, 0.18);
        outline: none;
    }

    /* Dropdown panel — constrained to parent width, no auto-expand */
    .select2-dropdown {
        border: 1px solid #e2e8f0;
        border-radius: 0.75rem;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.09);
        overflow: hidden;
        font-size: 0.8125rem;
        z-index: 9999;
    }

    .select2-container--default .select2-search--dropdown {
        padding: 8px 8px 4px;
    }

    .select2-container--default .select2-search--dropdown .select2-search__field {
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
        padding: 5px 10px;
        font-size: 0.8125rem;
        width: 100%;
        box-sizing: border-box;
        outline: none;
    }

    .select2-container--default .select2-search--dropdown .select2-search__field:focus {
        border-color: #3EAEB1;
        box-shadow: 0 0 0 2px rgba(62, 174, 177, 0.15);
    }

    .select2-results__options {
        max-height: 200px;
        overflow-y: auto;
    }

    .select2-results__option {
        font-size: 0.8125rem;
        padding: 7px 12px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #e8f7f8;
        color: #2d8a8d;
    }

    .select2-container--default .select2-results__option[aria-selected=true] {
        background-color: #3EAEB1;
        color: #fff;
    }

    .select2-container--default .select2-results__option--selected {
        background-color: #3EAEB1;
        color: #fff;
    }

    /* Clear button */
    .select2-container--default .select2-selection--single .select2-selection__clear {
        color: #94a3b8;
        font-size: 1rem;
        margin-right: 18px;
        font-weight: 300;
    }

    .select2-container--default .select2-selection--single .select2-selection__clear:hover {
        color: #ef4444;
    }
</style>
@endpush

@section('content')

@if(!empty($dbOffline))
<div class="bg-amber-50 border-b border-amber-200 mb-4">
    <div class="max-w-7xl mx-auto px-4 py-3 flex items-center gap-3">
        <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
        </svg>
        <p class="text-sm text-amber-800">
            <strong>Database is temporarily unavailable.</strong>
            Search results cannot be loaded right now. Please try again shortly.
        </p>
        <button onclick="window.location.reload()"
            class="ml-auto text-xs text-amber-700 border border-amber-300 px-3 py-1 rounded-lg hover:bg-amber-100 transition-colors flex-shrink-0">Retry</button>
    </div>
</div>
@endif

<div class="max-w-7xl mx-auto px-4 py-8">

    {{-- Page Header --}}
    <div class="mb-5">
        <h1 class="text-2xl font-bold text-slate-800">
            @if($query)
            Results for "<span class="text-primary">{{ $query }}</span>"
            <span class="text-base font-normal text-slate-400 ml-1">({{ number_format($results->total()) }}
                found)</span>
            @else
            Browse Medicines
            <span class="text-base font-normal text-slate-400 ml-1">({{ number_format($results->total()) }}
                total)</span>
            @endif
        </h1>

        {{-- Active filter chips --}}
        @php $hasFilters = !empty($filters['alpha']) || !empty($filters['drug_class']) ||
        !empty($filters['translated']); @endphp
        @if($hasFilters)
        <div class="flex flex-wrap gap-2 mt-3">
            @if(!empty($filters['alpha']))
            <a href="{{ route('drugs.search', array_merge(request()->except('alpha'), ['q' => $query])) }}"
                class="inline-flex items-center gap-1 bg-primary/10 text-primary text-xs font-medium px-3 py-1 rounded-full hover:bg-primary/20 transition-colors">
                Letter: <strong>{{ strtoupper($filters['alpha']) }}</strong>
                <span class="ml-1 opacity-60 hover:opacity-100">✕</span>
            </a>
            @endif
            @if(!empty($filters['drug_class']))
            <a href="{{ route('drugs.search', array_merge(request()->except('drug_class'), ['q' => $query])) }}"
                class="inline-flex items-center gap-1 bg-primary/10 text-primary text-xs font-medium px-3 py-1 rounded-full hover:bg-primary/20 transition-colors max-w-[220px] truncate">
                Class: <strong class="truncate">{{ $filters['drug_class'] }}</strong>
                <span class="ml-1 flex-shrink-0 opacity-60 hover:opacity-100">✕</span>
            </a>
            @endif
            @if(!empty($filters['translated']))
            <a href="{{ route('drugs.search', array_merge(request()->except('translated'), ['q' => $query])) }}"
                class="inline-flex items-center gap-1 bg-primary/10 text-primary text-xs font-medium px-3 py-1 rounded-full hover:bg-primary/20 transition-colors">
                ✓ Verified only <span class="ml-1 opacity-60">✕</span>
            </a>
            @endif
            <a href="{{ route('drugs.search', ['q' => $query]) }}"
                class="inline-flex items-center gap-1 text-xs text-slate-400 hover:text-red-500 transition-colors px-1">
                Clear all filters
            </a>
        </div>
        @endif
    </div>

    <div class="flex flex-col lg:flex-row gap-6">

        {{-- ── Filter Sidebar ── --}}
        <aside class="w-full lg:w-64 xl:w-72 flex-shrink-0">
            <form method="GET" action="{{ route('drugs.search') }}" id="filterForm">

                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    {{-- Sidebar header --}}
                    <div class="flex items-center justify-between px-4 py-3.5 border-b border-slate-100 bg-slate-50/60">
                        <h3 class="text-sm font-bold text-slate-700 flex items-center gap-2">
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z" />
                            </svg>
                            Filter
                        </h3>
                        @if($hasFilters)
                        <a href="{{ route('drugs.search', ['q' => $query]) }}"
                            class="text-xs text-red-400 hover:text-red-600 font-medium transition-colors">Reset</a>
                        @endif
                    </div>

                    <div class="p-4 space-y-4">

                        {{-- Keyword --}}
                        <div>
                            <label
                                class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">Keyword</label>
                            <div class="relative">
                                <input type="text" name="q" id="searchInput" value="{{ $query }}"
                                    placeholder="Medicine name..." autocomplete="off"
                                    class="w-full pl-8 pr-3 py-2 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/25 focus:border-primary transition-all bg-slate-50 focus:bg-white">
                                <svg class="absolute left-2.5 top-2.5 w-3.5 h-3.5 text-slate-400 pointer-events-none"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>

                        {{-- Letter / Alpha --}}
                        <div>
                            <label
                                class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">Letter</label>
                            <div class="w-full">
                                <select name="alpha" id="alphaSelect">
                                    <option value="">All Letters</option>
                                    @foreach($alphaIndex as $alpha)
                                    <option value="{{ $alpha }}" {{ ($filters['alpha'] ?? '' )===$alpha ? 'selected'
                                        : '' }}>
                                        {{ strtoupper($alpha) }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Drug Class --}}
                        <div>
                            <label
                                class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">Drug
                                Class</label>
                            <div class="w-full">
                                <select name="drug_class" id="classSelect">
                                    <option value="">All Classes</option>
                                    @foreach($classes as $class)
                                    <option value="{{ $class }}" {{ trim($filters['drug_class'] ?? '' )===trim($class)
                                        ? 'selected' : '' }}>
                                        {{ $class }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Sort --}}
                        <div>
                            <label
                                class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">Sort
                                By</label>
                            <div class="w-full">
                                <select name="order" id="orderSelect">
                                    <option value="" {{ empty($filters['order']) ? 'selected' : '' }}>Best Match
                                    </option>
                                    <option value="alpha" {{ ($filters['order'] ?? '' )==='alpha' ? 'selected' : '' }}>A
                                        – Z</option>
                                    <option value="newest" {{ ($filters['order'] ?? '' )==='newest' ? 'selected' : ''
                                        }}>Recently Updated</option>
                                </select>
                            </div>
                        </div>

                        {{-- Verified Toggle --}}
                        <label
                            class="flex items-center gap-3 cursor-pointer group py-2.5 px-3 rounded-xl border border-slate-100 hover:bg-slate-50 hover:border-slate-200 transition-all">
                            <div class="relative flex-shrink-0">
                                <input type="checkbox" id="verifiedToggle" name="translated" value="1" {{
                                    !empty($filters['translated']) ? 'checked' : '' }} class="sr-only peer">
                                <div
                                    class="w-9 h-5 bg-slate-200 rounded-full peer peer-checked:bg-primary after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:border after:border-slate-300 after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:after:translate-x-4 peer-checked:after:border-white">
                                </div>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-semibold text-slate-700">Verified only</p>
                                <p class="text-xs text-slate-400 leading-tight">Reviewed & translated</p>
                            </div>
                        </label>

                        {{-- Apply button --}}
                        <button type="submit"
                            class="w-full bg-primary hover:bg-primary-dark text-white text-sm font-semibold py-2.5 rounded-xl transition-colors flex items-center justify-center gap-2 shadow-sm shadow-primary/20">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Apply Filters
                        </button>

                    </div>
                </div>
            </form>
        </aside>

        {{-- ── Results Area ── --}}
        <div class="flex-1 min-w-0">

            @if($results->isEmpty())

            @if(!empty($fdaMode) && !empty($fdaResults))
            {{-- ── OpenFDA Fallback Results ── --}}
            <div class="mb-4 flex items-center gap-2">
                <span
                    class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-700 text-xs font-semibold px-3 py-1.5 rounded-full border border-blue-100">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Showing results from OpenFDA (local DB offline)
                </span>
                <span class="text-xs text-slate-400">{{ count($fdaResults) }} results</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3">
                @foreach($fdaResults as $fda)
                @php $initials = strtoupper(substr($fda['alpha_index'] ?? $fda['name'] ?? '?', 0, 2)); @endphp
                <a href="{{ route('drugs.show_fda', $fda['slug']) }}"
                    class="group flex flex-col bg-white rounded-xl border border-blue-100 p-4 card-hover cursor-pointer">
                    <div class="flex items-center gap-3 mb-3">
                        <div
                            class="w-9 h-9 bg-gradient-to-br from-blue-100 to-blue-200 rounded-xl flex items-center justify-center flex-shrink-0">
                            <span class="text-xs font-extrabold text-blue-600 leading-none">{{ $initials }}</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3
                                class="text-sm font-semibold text-slate-800 group-hover:text-blue-600 transition-colors line-clamp-1">
                                {{ $fda['name'] }}
                            </h3>
                            @if(!empty($fda['generic_name']))
                            <p class="text-xs text-slate-400 truncate italic mt-0.5">{{ $fda['generic_name'] }}</p>
                            @endif
                        </div>
                    </div>
                    @if(!empty($fda['drug_class']))
                    <div class="mb-2">
                        <span
                            class="inline-block text-xs bg-blue-50 text-blue-700 font-medium px-2 py-0.5 rounded-md max-w-full truncate">{{
                            $fda['drug_class'] }}</span>
                    </div>
                    @endif
                    @if(!empty($fda['uses']))
                    <p class="text-xs text-slate-500 leading-relaxed line-clamp-2 flex-1">{{ Str::limit($fda['uses'],
                        100) }}</p>
                    @endif
                    <div class="flex items-center justify-between mt-3 pt-2.5 border-t border-slate-50">
                        <span class="text-xs font-medium text-blue-500 bg-blue-50 px-1.5 py-0.5 rounded">FDA</span>
                        <span
                            class="text-xs text-blue-600 font-medium group-hover:translate-x-0.5 transition-transform">Details
                            →</span>
                    </div>
                </a>
                @endforeach
            </div>

            @else
            {{-- True empty state --}}
            <div class="text-center py-24 bg-white rounded-2xl border border-slate-100 shadow-sm">
                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-slate-600">No medicines found</h3>
                <p class="text-slate-400 mt-1 text-sm">Try different keywords or adjust the filters</p>
                <div class="flex gap-3 justify-center mt-5">
                    <a href="{{ route('drugs.search') }}"
                        class="px-5 py-2 bg-primary text-white rounded-xl text-sm font-semibold hover:bg-primary-dark transition-colors">Browse
                        All</a>
                </div>
            </div>

            @endif {{-- end fdaMode --}}

            @else

            {{-- Results meta row --}}
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm text-slate-500">
                    Showing
                    <span class="font-semibold text-slate-700">{{ $results->firstItem() }}–{{ $results->lastItem()
                        }}</span>
                    of
                    <span class="font-semibold text-slate-700">{{ number_format($results->total()) }}</span>
                </p>
                <span class="text-xs text-slate-400 bg-white border border-slate-100 px-2.5 py-1 rounded-lg">
                    Page {{ $results->currentPage() }} / {{ $results->lastPage() }}
                </span>
            </div>

            {{-- Drug Cards Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3">
                @foreach($results as $drug)
                @php
                $preview = \App\Models\Drug::cleanField($drug->uses);
                $initials = strtoupper(substr($drug->alpha_index ?? $drug->name ?? '?', 0, 2));
                @endphp
                <a href="{{ route('drugs.show', $drug->id) }}"
                    class="group flex flex-col bg-white rounded-xl border border-slate-100 p-4 card-hover cursor-pointer">

                    {{-- Card top: avatar + badges --}}
                    <div class="flex items-center gap-3 mb-3">
                        <div
                            class="w-9 h-9 bg-gradient-to-br from-primary/20 to-secondary/30 rounded-xl flex items-center justify-center flex-shrink-0">
                            <span class="text-xs font-extrabold text-primary leading-none">{{ $initials }}</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3
                                class="text-sm font-semibold text-slate-800 group-hover:text-primary transition-colors leading-snug line-clamp-1">
                                {{ $drug->name }}
                            </h3>
                            @if($drug->generic_name)
                            <p class="text-xs text-slate-400 truncate mt-0.5 italic">{{ $drug->generic_name }}</p>
                            @endif
                        </div>
                    </div>

                    {{-- Drug class badge --}}
                    @if($drug->drug_class)
                    <div class="mb-2">
                        <span
                            class="inline-block text-xs bg-primary/8 text-primary-dark font-medium px-2 py-0.5 rounded-md max-w-full truncate">
                            {{ $drug->drug_class }}
                        </span>
                    </div>
                    @endif

                    {{-- Uses preview --}}
                    @if($preview)
                    <p class="text-xs text-slate-500 leading-relaxed line-clamp-2 flex-1">{{ Str::limit($preview, 100)
                        }}</p>
                    @else
                    <p class="text-xs text-slate-300 italic flex-1">No description available</p>
                    @endif

                    {{-- Card footer --}}
                    <div class="flex items-center justify-between mt-3 pt-2.5 border-t border-slate-50">
                        <div class="flex items-center gap-1.5">
                            @if($drug->translated)
                            <span class="text-xs font-medium text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded">✓
                                Verified</span>
                            @else
                            <span class="text-xs text-slate-300">{{ strtoupper($drug->source ?? 'unknown') }}</span>
                            @endif
                        </div>
                        <span
                            class="text-xs text-primary font-medium group-hover:translate-x-0.5 transition-transform">Details
                            →</span>
                    </div>
                </a>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($results->hasPages())
            <nav class="mt-7 flex items-center justify-center gap-1" aria-label="Pagination">

                {{-- First --}}
                @if(!$results->onFirstPage())
                <a href="{{ $results->appends(request()->query())->url(1) }}"
                    class="w-9 h-9 flex items-center justify-center rounded-lg border border-slate-200 text-slate-500 text-sm hover:bg-slate-50 transition-colors"
                    title="First">«</a>
                @endif

                {{-- Prev --}}
                @if($results->onFirstPage())
                <span
                    class="px-3 py-2 rounded-lg border border-slate-100 text-slate-300 text-sm cursor-not-allowed select-none">‹</span>
                @else
                <a href="{{ $results->appends(request()->query())->previousPageUrl() }}"
                    class="px-3 py-2 rounded-lg border border-slate-200 text-slate-600 text-sm hover:bg-slate-50 transition-colors">‹
                    Prev</a>
                @endif

                {{-- Page window --}}
                @php $start = max(1, $results->currentPage()-2); $end = min($results->lastPage(),
                $results->currentPage()+2); @endphp
                @if($start > 1)<span class="px-1 text-slate-300 text-sm">…</span>@endif
                @for($p = $start; $p <= $end; $p++) <a href="{{ $results->appends(request()->query())->url($p) }}"
                    class="w-9 h-9 flex items-center justify-center rounded-lg text-sm font-medium transition-colors
                          {{ $p === $results->currentPage() ? 'bg-primary text-white shadow-sm' : 'border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
                    {{ $p }}
                    </a>
                    @endfor
                    @if($end < $results->lastPage())<span class="px-1 text-slate-300 text-sm">…</span>@endif

                        {{-- Next --}}
                        @if($results->hasMorePages())
                        <a href="{{ $results->appends(request()->query())->nextPageUrl() }}"
                            class="px-3 py-2 rounded-lg border border-slate-200 text-slate-600 text-sm hover:bg-slate-50 transition-colors">Next
                            ›</a>
                        @else
                        <span
                            class="px-3 py-2 rounded-lg border border-slate-100 text-slate-300 text-sm cursor-not-allowed select-none">›</span>
                        @endif

                        {{-- Last --}}
                        @if($results->hasMorePages())
                        <a href="{{ $results->appends(request()->query())->url($results->lastPage()) }}"
                            class="w-9 h-9 flex items-center justify-center rounded-lg border border-slate-200 text-slate-500 text-sm hover:bg-slate-50 transition-colors"
                            title="Last">»</a>
                        @endif

            </nav>
            @endif

            @endif {{-- end isEmpty --}}

        </div>{{-- end results --}}
    </div>
</div>

@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(function () {
        // Shared Select2 config — dropdownParent keeps dropdown inside sidebar, width resolves to the element
        const sidebarEl = $('#filterForm');

        $('#alphaSelect').select2({
            placeholder: 'All Letters',
            allowClear: true,
            width: 'resolve',
            minimumResultsForSearch: 8,
            dropdownParent: sidebarEl,
        });

        $('#classSelect').select2({
            placeholder: 'All Classes',
            allowClear: true,
            width: 'resolve',
            minimumResultsForSearch: 1,
            dropdownParent: sidebarEl,
        });

        $('#orderSelect').select2({
            placeholder: 'Sort By',
            allowClear: false,
            width: 'resolve',
            minimumResultsForSearch: Infinity,
            dropdownParent: sidebarEl,
        });

        // Auto-submit on select/clear
        $('#alphaSelect, #classSelect, #orderSelect').on('select2:select select2:clear', function () {
            setTimeout(() => { $('#filterForm').submit(); }, 80);
        });

        // Toggle auto-submit
        $('#verifiedToggle').on('change', function () {
            $('#filterForm').submit();
        });

        // Keyword: submit on Enter
        $('#searchInput').on('keydown', function (e) {
            if (e.key === 'Enter') { e.preventDefa('#filterForm').submit(); }
        });
    });
</script>
@endpush