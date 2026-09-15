@extends('layouts.app')

@section('title', 'AI-Powered Symptom Screening & Medicine Directory')
@section('meta_description', (!empty($dbOffline) || !empty($fdaMode))
    ? 'Pharmasis helps you understand medical symptoms and drug information clearly. AI symptom screening, 67,000+ medicines, and three intelligent classification methods built for patients and clinicians.'
    : 'Pharmasis helps you understand medical symptoms and drug information clearly. AI symptom screening, 16,000+ medicines, and three intelligent classification methods built for patients and clinicians.')

@push('head')
<style>
    /* ── Typography & Base Styles ── */
    .display-italic {
        font-family: 'Instrument Serif', serif;
        font-style: italic;
        font-weight: 400;
        letter-spacing: -0.01em;
    }
    .display-roman {
        font-family: 'Instrument Serif', serif;
        font-style: italic;
        font-weight: 400;
        letter-spacing: -0.015em;
    }

    /* ── Ultra Glassmorphism Base System ── */
    .glass-panel-ultra {
        background: rgba(255, 255, 255, 0.78);
        backdrop-filter: blur(28px);
        -webkit-backdrop-filter: blur(28px);
        border: 1px solid rgba(255, 255, 255, 0.92);
        box-shadow: 0 14px 40px rgba(15, 118, 110, 0.06), 0 1px 0 rgba(255, 255, 255, 0.95) inset;
        border-radius: 1.5rem;
        transition: transform 280ms cubic-bezier(.22,1,.36,1), box-shadow 280ms ease, border-color 280ms ease, background-color 280ms ease;
    }
    .glass-panel-ultra:hover {
        background: rgba(255, 255, 255, 0.92);
        box-shadow: 0 20px 48px rgba(15, 118, 110, 0.10), 0 1px 0 rgba(255, 255, 255, 1) inset;
    }

    /* ── Clean Clinical Inquiries Cards (No Top Stripe, Unified Aesthetic) ── */
    .clean-clinical-card {
        background: rgba(255, 255, 255, 0.84);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(226, 232, 240, 0.85);
        box-shadow: 0 8px 24px rgba(15, 118, 110, 0.04);
        border-radius: 1.15rem;
        transition: all 260ms cubic-bezier(.22,1,.36,1);
    }
    .clean-clinical-card:hover {
        transform: translateY(-3px);
        background: #ffffff;
        border-color: rgba(13, 148, 136, 0.4);
        box-shadow: 0 16px 36px rgba(15, 118, 110, 0.09);
    }
    .clean-clinical-card:active {
        transform: scale(0.985);
    }

    /* ── Medicine Continuous Auto-Scroll Marquee (Pause on Hover) ── */
    .med-marquee-container {
        position: relative;
        width: 100%;
        overflow: hidden;
        mask-image: linear-gradient(to right, transparent, black 3%, black 97%, transparent);
        -webkit-mask-image: linear-gradient(to right, transparent, black 3%, black 97%, transparent);
        padding: 0.75rem 0 1.5rem 0;
    }
    .med-marquee-track {
        display: flex;
        gap: 1.25rem;
        width: max-content;
        animation: medMarqueeScroll 48s linear infinite;
    }
    .med-marquee-track:hover {
        animation-play-state: paused;
    }
    @keyframes medMarqueeScroll {
        0% { transform: translateX(0); }
        100% { transform: translateX(-50%); }
    }

    .med-card {
        width: 320px;
        flex-shrink: 0;
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.92);
        box-shadow: 0 10px 30px rgba(11, 31, 36, 0.05);
        border-radius: 1.25rem;
        transition: transform 260ms ease, box-shadow 260ms ease, background-color 260ms ease;
    }
    .med-card:hover {
        transform: translateY(-4px);
        background: #ffffff;
        box-shadow: 0 18px 40px rgba(11, 31, 36, 0.10);
    }

    /* ── Timeline Architecture Model ── */
    .timeline-track-line {
        position: absolute;
        top: 28px;
        left: 36px;
        right: 36px;
        height: 2px;
        background: linear-gradient(to right, #0d9488, #3b82f6, #059669);
        opacity: 0.25;
        z-index: 0;
    }
    .timeline-node {
        position: relative;
        z-index: 1;
    }
    .timeline-pill-btn {
        background: rgba(255, 255, 255, 0.70);
        backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.85);
        transition: all 0.26s ease;
    }
    .timeline-pill-btn.active {
        background: #ffffff !important;
        border-color: #0d9488 !important;
        box-shadow: 0 8px 24px rgba(13, 148, 136, 0.12), 0 1px 0 #ffffff inset !important;
    }

    /* ── Dual-Perspective Big Output Glass Cards ── */
    .glass-output-card {
        background: rgba(255, 255, 255, 0.82);
        backdrop-filter: blur(30px);
        -webkit-backdrop-filter: blur(30px);
        border: 1px solid rgba(255, 255, 255, 0.95);
        box-shadow: 0 18px 44px rgba(15, 118, 110, 0.08), 0 1px 0 rgba(255, 255, 255, 1) inset;
        border-radius: 1.5rem;
        transition: transform 280ms ease, box-shadow 280ms ease, border-color 280ms ease;
    }
    .glass-output-card:hover {
        transform: translateY(-2px);
        border-color: rgba(13, 148, 136, 0.35);
        box-shadow: 0 24px 54px rgba(15, 118, 110, 0.12);
    }

    /* ── Reveal Animation ── */
    .reveal {
        opacity: 0;
        transform: translateY(20px);
        transition: opacity 0.6s ease, transform 0.6s cubic-bezier(.22,1,.36,1);
    }
    .reveal.visible {
        opacity: 1;
        transform: translateY(0);
    }

    @media (prefers-reduced-motion: reduce) {
        .clean-clinical-card, .med-card, .glass-panel-ultra, .glass-output-card, .reveal {
            transition: none !important;
            transform: none !important;
        }
        .med-marquee-track {
            animation: none !important;
            overflow-x: auto;
        }
    }
</style>
@endpush

@section('content')

{{-- ── DB Offline Notice ── --}}
@if(!empty($dbOffline))
<div class="border-b border-amber-200/60" style="background: rgba(254,243,199,0.7); backdrop-filter: blur(10px);"
    x-data="{ show: true }" x-init="setTimeout(() => show = false, 8000)" x-show="show" x-transition.opacity>
    <div class="max-w-7xl mx-auto px-4 py-2.5 flex items-center gap-3">
        <svg class="w-4 h-4 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7" />
        </svg>
        <p class="text-xs text-amber-800 flex-1">
            <strong>OpenFDA Data Mode.</strong> Results are currently retrieved directly from the OpenFDA clinical registry.
        </p>
        <button onclick="window.location.reload()"
            class="text-xs text-amber-700 border border-amber-200 px-2.5 py-1 rounded-lg hover:bg-amber-100 transition-colors">Reload</button>
        <button @click="show=false" class="text-amber-400 hover:text-amber-700 ml-1">✕</button>
    </div>
</div>
@endif

{{-- ══════════════════════ HERO SECTION ══════════════════════ --}}
@include('partials.hero-medicheck-id')

{{-- ══════════════════════ CLINICAL INQUIRIES OVERVIEW (RANDOMIZED & SEVERITY TRIAGED) ══════════════════════ --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20" id="section-conditions">

    <div class="text-center mb-10 reveal" data-delay="0">
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-[11px] font-semibold mb-4 glass-soft text-teal-800 uppercase tracking-[0.18em]">
            Clinical Inquiries Overview
        </div>
        <h2 class="display-roman text-3xl md:text-4xl lg:text-5xl text-ink-900 leading-tight max-w-2xl mx-auto">
            Frequently discussed
            <span class="display-italic text-primary">symptoms and clinical conditions</span>
        </h2>
        <p class="text-sm md:text-base text-slate-500 mt-3 max-w-xl mx-auto leading-relaxed">
            Select any clinical scenario below to populate the MediCheck triage engine with structured symptom parameters.
        </p>
    </div>

    @php
    $allClinicalTopics = [
        // ACUTE TRIAGE
        [
            'severity_label' => 'Acute Triage',
            'badge_color' => '#dc2626',
            'badge_bg' => '#fef2f2',
            'specialty' => 'Tropical Medicine',
            'title' => 'Dengue Viral Infection',
            'symptom' => 'Abrupt onset of high fever, retro-orbital eye pain, severe myalgia, arthralgia, and petechial rash.',
            'query' => 'I have sudden high fever, retro-orbital eye pain, joint ache, and small red spots on skin'
        ],
        [
            'severity_label' => 'Acute Triage',
            'badge_color' => '#dc2626',
            'badge_bg' => '#fef2f2',
            'specialty' => 'Infectious Disease',
            'title' => 'Enteric Fever (Typhoid)',
            'symptom' => 'Step-ladder fever pattern exceeding 5 days with abdominal distension, coated tongue, and severe malaise.',
            'query' => 'I have persistent step-ladder fever for over five days with severe abdominal cramps and fatigue'
        ],
        [
            'severity_label' => 'Acute Triage',
            'badge_color' => '#dc2626',
            'badge_bg' => '#fef2f2',
            'specialty' => 'Pulmonology',
            'title' => 'Bronchial Asthma Attack',
            'symptom' => 'Expiratory wheezing, paroxysmal nocturnal dyspnea, and progressive chest tightness exacerbated by cold exposure.',
            'query' => 'I have recurrent wheezing, chest tightness, and shortness of breath triggered by cold air'
        ],
        [
            'severity_label' => 'Acute Triage',
            'badge_color' => '#dc2626',
            'badge_bg' => '#fef2f2',
            'specialty' => 'Neurology',
            'title' => 'Severe Migraine with Aura',
            'symptom' => 'Unilateral pulsating headache of severe intensity accompanied by photophobia, visual disturbances, and nausea.',
            'query' => 'I have a pulsating headache on one side of my head with nausea and light sensitivity'
        ],
        [
            'severity_label' => 'Acute Triage',
            'badge_color' => '#dc2626',
            'badge_bg' => '#fef2f2',
            'specialty' => 'Rheumatology',
            'title' => 'Acute Gouty Arthritis Flare',
            'symptom' => 'Sudden nocturnal onset of severe joint erythema, swelling, and extreme tenderness at first metatarsophalangeal joint.',
            'query' => 'Sudden severe throbbing pain, heat, and swelling in my big toe joint that started during the night'
        ],
        [
            'severity_label' => 'Acute Triage',
            'badge_color' => '#dc2626',
            'badge_bg' => '#fef2f2',
            'specialty' => 'Gastroenterology',
            'title' => 'Acute Infectious Gastroenteritis',
            'symptom' => 'Frequent watery bowel movements, nausea, hyperactive bowel sounds, and low-grade dehydration signs.',
            'query' => 'Frequent watery diarrhea, nausea, low-grade fever, and stomach cramps since yesterday'
        ],

        // MODERATE EVALUATION
        [
            'severity_label' => 'Moderate Eval',
            'badge_color' => '#d97706',
            'badge_bg' => '#fffbeb',
            'specialty' => 'Cardiovascular',
            'title' => 'Hypertension & Morning Cephalea',
            'symptom' => 'Elevated blood pressure readings with occipital morning headache, dizziness upon standing, and neck stiffness.',
            'query' => 'I have elevated blood pressure readings accompanied by frequent morning headaches and neck stiffness'
        ],
        [
            'severity_label' => 'Moderate Eval',
            'badge_color' => '#d97706',
            'badge_bg' => '#fffbeb',
            'specialty' => 'Endocrinology',
            'title' => 'Type 2 Diabetes & Hyperglycemia',
            'symptom' => 'Polydipsia, polyuria, unexplained fatigue, and persistent fasting blood glucose above 126 mg/dL.',
            'query' => 'I am experiencing high blood sugar readings, increased thirst, and frequent urination'
        ],
        [
            'severity_label' => 'Moderate Eval',
            'badge_color' => '#d97706',
            'badge_bg' => '#fffbeb',
            'specialty' => 'Nephrology & Urology',
            'title' => 'Acute Urinary Tract Infection',
            'symptom' => 'Dysuria, urinary urgency, increased frequency, suprapubic discomfort, and turbid urine output.',
            'query' => 'I experience painful burning urination, cloudy urine, and frequent urge to urinate'
        ],
        [
            'severity_label' => 'Moderate Eval',
            'badge_color' => '#d97706',
            'badge_bg' => '#fffbeb',
            'specialty' => 'Hematology',
            'title' => 'Iron Deficiency Anemia',
            'symptom' => 'Chronic generalized weakness, conjunctival pallor, exertional palpitation, and impaired concentration.',
            'query' => 'I have severe ongoing fatigue, pale conjunctiva, dizziness upon standing, and cold extremities'
        ],
        [
            'severity_label' => 'Moderate Eval',
            'badge_color' => '#d97706',
            'badge_bg' => '#fffbeb',
            'specialty' => 'Endocrinology',
            'title' => 'Hypothyroidism Syndrome',
            'symptom' => 'Unexplained weight gain, cold intolerance, constipation, lethargy, and dry skin texture.',
            'query' => 'I have unexplained weight gain, chronic fatigue, cold sensitivity, and dry skin'
        ],
        [
            'severity_label' => 'Moderate Eval',
            'badge_color' => '#d97706',
            'badge_bg' => '#fffbeb',
            'specialty' => 'Neurology',
            'title' => 'Peripheral Neuropathic Paresthesia',
            'symptom' => 'Bilateral burning or tingling sensation in feet, reduced vibration perception, and stocking distribution numbness.',
            'query' => 'Tingling, burning sensations and pins-and-needles numbness in both of my feet'
        ],

        // ROUTINE CARE
        [
            'severity_label' => 'Routine Care',
            'badge_color' => '#059669',
            'badge_bg' => '#ecfdf5',
            'specialty' => 'Gastroenterology',
            'title' => 'Gastroesophageal Reflux (GERD)',
            'symptom' => 'Postprandial retrosternal burning pain, mild acid regurgitation, and epigastric fullness after meals.',
            'query' => 'I feel a burning sensation in my chest and throat after meals with acid regurgitation'
        ],
        [
            'severity_label' => 'Routine Care',
            'badge_color' => '#059669',
            'badge_bg' => '#ecfdf5',
            'specialty' => 'Psychiatry',
            'title' => 'Generalized Anxiety Symptoms',
            'symptom' => 'Restlessness, mild somatic tension, difficulty relaxing, and occasional autonomic arousal.',
            'query' => 'I feel persistent restlessness, muscle tension, racing thoughts, and rapid heart rate'
        ],
        [
            'severity_label' => 'Routine Care',
            'badge_color' => '#059669',
            'badge_bg' => '#ecfdf5',
            'specialty' => 'Musculoskeletal',
            'title' => 'Mechanical Lumbar Strain',
            'symptom' => 'Localized lower back stiffness and muscle tenderness exacerbated by prolonged sitting or bending.',
            'query' => 'I have localized lower back stiffness that sharpens when bending forward or lifting objects'
        ],
        [
            'severity_label' => 'Routine Care',
            'badge_color' => '#059669',
            'badge_bg' => '#ecfdf5',
            'specialty' => 'Dermatology',
            'title' => 'Atopic Dermatitis & Eczema',
            'symptom' => 'Pruritic dry skin patches and localized erythema predominantly in flexural creases.',
            'query' => 'I have intensely itchy, dry, and red inflamed skin patches in the creases of my elbows and knees'
        ],
        [
            'severity_label' => 'Routine Care',
            'badge_color' => '#059669',
            'badge_bg' => '#ecfdf5',
            'specialty' => 'Otolaryngology',
            'title' => 'Allergic Rhinitis & Sinonasal Congestion',
            'symptom' => 'Clear watery rhinorrhea, paroxysmal sneezing, nasal mucosal itching, and morning congestion.',
            'query' => 'Frequent sneezing fits, clear runny nose, and itchy watery eyes in the morning'
        ],
        [
            'severity_label' => 'Routine Care',
            'badge_color' => '#059669',
            'badge_bg' => '#ecfdf5',
            'specialty' => 'Sleep Medicine',
            'title' => 'Sleep Initiation Insomnia',
            'symptom' => 'Difficulty initiating sleep exceeding 30 minutes, unrefreshing rest, and daytime lethargy.',
            'query' => 'Trouble falling asleep at night with racing thoughts and daytime grogginess'
        ],
    ];

    // Randomize topics on each load to keep content fresh and dynamic
    $displayedTopics = collect($allClinicalTopics)->shuffle()->take(12);
    @endphp

    {{-- Clean Unified Grid (No Top Stripe) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 reveal" data-delay="100">
        @foreach($displayedTopics as $topic)
        <button
            onclick="pharmasisFillCondition({{ json_encode($topic['query']) }})"
            class="clean-clinical-card text-left p-4 group flex flex-col justify-between"
            aria-label="Screen for {{ $topic['title'] }}">
            <div>
                <div class="flex items-center justify-between gap-2 mb-2.5">
                    <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded"
                        style="color: {{ $topic['badge_color'] }}; background: {{ $topic['badge_bg'] }};">
                        {{ $topic['severity_label'] }}
                    </span>
                    <span class="text-[10px] font-medium text-slate-400">
                        {{ $topic['specialty'] }}
                    </span>
                </div>
                <h3 class="text-xs font-bold text-slate-900 leading-snug mb-1.5 group-hover:text-teal-800 transition-colors">
                    {{ $topic['title'] }}
                </h3>
                <p class="text-[11px] text-slate-500 leading-relaxed line-clamp-3">
                    {{ $topic['symptom'] }}
                </p>
            </div>
            <div class="mt-3 pt-2 border-t border-slate-100 flex items-center justify-between text-[10px] font-semibold text-slate-400 group-hover:text-teal-700 transition-colors">
                <span>Clinical Triage</span>
                <span>Select →</span>
            </div>
        </button>
        @endforeach
    </div>

    <p class="text-center text-xs text-slate-400 mt-8 reveal" data-delay="200">
        Clinical screening is intended for educational orientation. Always consult a licensed medical professional for formal clinical diagnosis.
    </p>

</section>

{{-- ══════════════════════ CONTINUOUS MEDICINE CATALOG CAROUSEL ══════════════════════ --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 pb-24" id="section-medicines"
    x-data="{
        quickModalOpen: false,
        selectedDrug: null,
        openDetailModal(drug) {
            this.selectedDrug = drug;
            this.quickModalOpen = true;
        }
    }">

    {{-- Header --}}
    <div class="flex items-end justify-between mb-6 flex-wrap gap-4 reveal" data-delay="0">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <p class="text-[11px] font-bold text-teal-800 uppercase tracking-[0.18em]">Active Medicine Repository</p>
            </div>
            <h2 class="display-roman text-3xl md:text-4xl text-ink-900 leading-tight">
                Continuous pharmacological
                <span class="display-italic text-primary">catalog stream</span>
            </h2>
            <p class="text-sm text-slate-500 mt-1 max-w-lg">
                Hover anywhere over the catalog to pause and inspect formulations in detail.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <span class="text-[11px] font-medium text-slate-400 bg-white/80 border border-slate-200 px-3 py-1.5 rounded-full shadow-sm">
                Auto-scrolling · Hover to pause
            </span>
            <a href="{{ route('drugs.search') }}"
                class="px-5 py-2 rounded-full text-xs font-semibold bg-teal-700 hover:bg-teal-800 text-white shadow-sm inline-flex items-center gap-1.5 transition-colors">
                <span>Browse Full Directory</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </div>

    {{-- Infinite Smooth Auto-Scroll Track (Glassmorphism Cards) --}}
    <div class="med-marquee-container reveal" data-delay="100">
        <div class="med-marquee-track">
            @php
            $drugList = array_merge($featured->all(), $featured->all());
            @endphp

            @foreach($drugList as $drug)
            @php
            $isDto = $drug instanceof \App\DTOs\DrugDTO;
            $name = $drug->name ?? 'Unknown Medicine';
            $generic = $drug->generic_name ?? '';
            $drugClass = $drug->drug_class ?? 'Therapeutic Agent';
            $rawUses = $isDto ? $drug->uses : \App\Models\Drug::cleanField($drug->uses);
            $usesText = !empty($rawUses) ? $rawUses : 'Clinical monograph details available upon inspection.';
            $sideEffects = $isDto ? ($drug->side_effects ?? '') : \App\Models\Drug::cleanField($drug->side_effects ?? '');
            $href = $isDto ? route('drugs.show_fda', $drug->slug ?? Str::slug($name)) : route('drugs.show', $drug->id);
            $initials = strtoupper(substr($name, 0, 2));

            $drugJson = [
                'id' => $drug->id ?? null,
                'name' => $name,
                'generic' => $generic,
                'class' => $drugClass,
                'uses' => $usesText,
                'side_effects' => $sideEffects,
                'url' => $href,
                'is_fda' => !empty($drug->is_fda),
                'translated' => !empty($drug->translated)
            ];
            @endphp

            <div class="med-card p-5 flex flex-col justify-between">
                <div>
                    {{-- Header --}}
                    <div class="flex items-start justify-between gap-2 mb-3">
                        <span class="text-[10px] font-bold text-teal-800 bg-teal-50 px-2.5 py-1 rounded-md border border-teal-100">
                            {{ $initials }} · RX
                        </span>
                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded bg-slate-100/90 text-slate-700 max-w-[170px] truncate border border-slate-200">
                            {{ $drugClass }}
                        </span>
                    </div>

                    {{-- Name --}}
                    <h3 class="font-bold text-slate-900 text-sm leading-snug mb-1 line-clamp-1 hover:text-teal-700 transition-colors">
                        <a href="{{ $href }}">{{ $name }}</a>
                    </h3>
                    <p class="text-xs text-slate-400 italic mb-2.5 truncate">
                        {{ $generic ?: 'Standard Formulation' }}
                    </p>

                    {{-- Description --}}
                    <p class="text-xs text-slate-600 leading-relaxed line-clamp-3 mb-4">
                        {{ Str::limit($usesText, 105) }}
                    </p>
                </div>

                {{-- Card Actions --}}
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                    <button
                        @click="openDetailModal({{ json_encode($drugJson) }})"
                        type="button"
                        class="text-xs font-semibold text-teal-800 hover:text-teal-900 hover:bg-teal-50 px-2.5 py-1.5 rounded-lg transition-colors">
                        Quick Info
                    </button>

                    <a href="{{ $href }}" class="text-xs font-bold text-slate-700 hover:text-teal-700 inline-flex items-center gap-1 transition-colors">
                        <span>Monograph</span>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Quick View Modal (Alpine) --}}
    <div
        x-show="quickModalOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform scale-95"
        x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
        style="display: none;">

        <div @click.away="quickModalOpen = false" class="bg-white/95 backdrop-blur-2xl rounded-3xl max-w-xl w-full p-6 md:p-8 shadow-2xl border border-white/80 relative overflow-hidden">
            <template x-if="selectedDrug">
                <div>
                    <div class="flex items-start justify-between gap-4 mb-4">
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-teal-800 bg-teal-50 px-2.5 py-1 rounded-md border border-teal-100" x-text="selectedDrug.class"></span>
                            <h3 class="text-xl md:text-2xl font-bold text-slate-900 mt-2" x-text="selectedDrug.name"></h3>
                            <p class="text-xs text-slate-500 italic mt-0.5" x-text="selectedDrug.generic ? 'Generic: ' + selectedDrug.generic : 'Standard Formulation'"></p>
                        </div>
                        <button @click="quickModalOpen = false" class="text-slate-400 hover:text-slate-700 p-1.5 rounded-full hover:bg-slate-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="space-y-4 my-6 text-xs leading-relaxed max-h-[60vh] overflow-y-auto pr-1">
                        <div class="p-4 rounded-xl bg-slate-50/80 border border-slate-200">
                            <h4 class="font-bold text-slate-900 uppercase tracking-wide text-[10px] mb-1.5 text-teal-900">Therapeutic Indications</h4>
                            <p class="text-slate-700" x-text="selectedDrug.uses"></p>
                        </div>

                        <div class="p-4 rounded-xl bg-amber-50/70 border border-amber-200" x-show="selectedDrug.side_effects">
                            <h4 class="font-bold uppercase tracking-wide text-[10px] mb-1.5 text-amber-900">Notable Adverse Effects & Precautions</h4>
                            <p class="text-amber-800" x-text="selectedDrug.side_effects || 'Refer to formal prescribing package insert for complete contraindication matrix.'"></p>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <button
                            @click="pharmasisFillCondition('Provide comprehensive clinical details for ' + selectedDrug.name + ' (' + (selectedDrug.generic || '') + ')'); quickModalOpen = false;"
                            class="px-4 py-2 rounded-xl text-xs font-semibold bg-teal-50 text-teal-800 hover:bg-teal-100 transition-colors">
                            Analyze with MediCheck
                        </button>
                        <a :href="selectedDrug.url" class="px-5 py-2 rounded-xl text-xs font-semibold bg-teal-700 text-white hover:bg-teal-800 transition-colors inline-flex items-center gap-1.5">
                            <span>Open Full Monograph</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>
                    </div>
                </div>
            </template>
        </div>
    </div>

</section>

{{-- ══════════════════════ MEDICORE ARCHITECTURE & INTERACTIVE TIMELINE ══════════════════════ --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 overflow-hidden" id="section-how"
    x-data="{
        activeMode: 0,
        activeStage: 0,
        modes: [
            {
                id: 'medi_facts',
                code: 'FACTS_PIPELINE',
                title: 'MediFacts',
                badge: 'Knowledge Engine',
                badgeColor: '#0369a1',
                badgeBg: '#f0f9ff',
                summary: 'Deterministic retrieval of validated pharmacological monographs.',
                stages: [
                    { num: '01', title: 'Intent Identification', subtitle: 'Pharmacological Query', desc: 'Identifies specific drug active ingredients, drug classes, or anatomical systems without generating diagnostic assumptions.' },
                    { num: '02', title: 'Monograph Retrieval', subtitle: 'FDA & Registry Cross-Match', desc: 'Queries authoritative drug databases for hepatic clearance, standard dosing intervals, and molecular mechanism of action.' },
                    { num: '03', title: 'Safety Stratification', subtitle: 'Contraindication Verification', desc: 'Evaluates severe black box warnings, renal adjustment requirements (eGFR thresholds), and potential drug-drug interactions.' },
                    { num: '04', title: 'Structured Knowledge', subtitle: 'Dual-Perspectives', desc: 'Synthesizes plain-language patient education summaries alongside formal clinician pharmacological profiles.' }
                ],
                sampleInput: 'What is the precise cellular mechanism of Metformin and what renal parameters require dosage titration?',
                outputs: [
                    {
                        role: 'Patient Educational Brief',
                        title: 'Plain-Language Therapeutic Orientation',
                        summary: 'Metformin belongs to the biguanide class. It lowers blood sugar by decreasing hepatic glucose output and increasing muscular insulin sensitivity.',
                        points: [
                            'Primary Action: Reduces liver glucose output without causing hypoglycemic spikes',
                            'Standard Administration: Taken with meals to minimize gastrointestinal discomfort',
                            'Safety Note: Requires periodic kidney function checks to verify medication clearance'
                        ]
                    },
                    {
                        role: 'Clinician Pharmacological Monograph',
                        title: 'Cellular Mechanism & Dosing Parameters',
                        summary: 'Mitochondrial complex I inhibition and AMP-activated protein kinase (AMPK) stimulation leading to hepatic gluconeogenesis suppression.',
                        points: [
                            'Renal Parameters: Obtain baseline eGFR prior to therapy initiation',
                            'Titration Protocol: eGFR 30-44 mL/min requires maximum daily dose reduction to 1,000 mg',
                            'Absolute Contraindications: eGFR <30 mL/min or acute conditions with tissue hypoxia risk'
                        ]
                    }
                ]
            },
            {
                id: 'medi_check',
                code: 'CHECK_PIPELINE',
                title: 'MediCheck',
                badge: 'Symptom Triage',
                badgeColor: '#0f766e',
                badgeBg: '#f0fdfa',
                summary: 'Adaptive clinical screening with targeted follow-up probing and triage.',
                stages: [
                    { num: '01', title: 'Chief Complaint Intake', subtitle: 'Semantic Parsing', desc: 'Extracts symptom duration, anatomical localization, severity indicators, and immediate emergency red flag markers.' },
                    { num: '02', title: 'Dynamic Probing', subtitle: 'Adaptive Questionnaire', desc: 'Generates 3 to 5 targeted follow-up questions to distinguish differential possibilities and evaluate aggravating triggers.' },
                    { num: '03', title: 'Clinical Reasoning', subtitle: 'Etiology Stratification', desc: 'Maps symptom clusters against diagnostic criteria (ICD-10 aligned) to establish primary vs secondary differential hypotheses.' },
                    { num: '04', title: 'Triage Synthesis', subtitle: 'Actionable Guidance', desc: 'Delivers clear guidance on when to seek immediate emergency care versus scheduled outpatient consultation.' }
                ],
                sampleInput: 'I have had continuous retrosternal chest discomfort and shortness of breath for two days after moderate physical exertion.',
                outputs: [
                    {
                        role: 'Patient Clinical Guidance',
                        title: 'Immediate Action & Red Flag Orientation',
                        summary: 'Your reported symptoms indicate exertional chest tightness that warrants formal medical evaluation. Rest immediately and avoid physical exertion.',
                        points: [
                            'Emergency Warning: Seek urgent emergency care if discomfort spreads to jaw, left shoulder, or triggers cold sweats',
                            'Self-Monitoring: Observe whether resting relieved the tightness within 10 minutes',
                            'Recommended Facility: Hospital Emergency Department or Urgent Cardiology Clinic'
                        ]
                    },
                    {
                        role: 'Clinician Differential Matrix',
                        title: 'ICD-10 Aligned Triage Assessment',
                        summary: 'Stratified clinical differentials for exertional chest pressure with dyspnea. Immediate cardiac vs non-cardiac differentiation indicated.',
                        points: [
                            'Primary Differential: Angina Pectoris / Rule out NACS (ICD-10 I20.9 / I21.9)',
                            'Secondary Hypotheses: Gastroesophageal spasm (K21.9), Musculoskeletal costochondritis (M94.0)',
                            'Recommended Workup: 12-lead ECG, High-sensitivity Troponin series, Transthoracic Echocardiogram'
                        ]
                    }
                ]
            },
            {
                id: 'medi_combo',
                code: 'COMBO_PIPELINE',
                title: 'MediCombo',
                badge: 'Integrated Evaluation',
                badgeColor: '#b45309',
                badgeBg: '#fffbeb',
                summary: 'Dual-track analysis linking active symptoms with ongoing drug regimens.',
                stages: [
                    { num: '01', title: 'Dual Entity Extraction', subtitle: 'Symptom + Regimen', desc: 'Simultaneously captures active bodily symptoms alongside specific medication regimens, dosages, and timeline of initiation.' },
                    { num: '02', title: 'Pharmacovigilance', subtitle: 'ADR Correlation', desc: 'Cross-checks known Adverse Drug Reaction (ADR) frequencies and timelines against reported symptom onset dates.' },
                    { num: '03', title: 'Dual-Track Analysis', subtitle: 'Drug Effect vs Organic Pathology', desc: 'Differentiates whether symptoms are direct pharmacological side effects or a separate emergent medical condition.' },
                    { num: '04', title: 'Consultation Brief', subtitle: 'Integrated Report', desc: 'Produces a cohesive report for the patient and healthcare provider detailing potential dosage modifications or alternatives.' }
                ],
                sampleInput: 'I started taking Amlodipine 10mg ten days ago and now notice bilateral swelling around my ankles. Is this caused by the pill?',
                outputs: [
                    {
                        role: 'Patient Medication Safety Report',
                        title: 'Adverse Effect Verification',
                        summary: 'Bilateral ankle swelling (peripheral edema) is a documented, non-allergic pharmacological side effect of calcium channel blockers such as Amlodipine.',
                        points: [
                            'Underlying Cause: Selective dilation of precapillary blood vessels leading to fluid accumulation',
                            'Important Precaution: Do not discontinue or alter your prescribed dose abruptly without your doctor',
                            'Next Action: Consult your prescribing physician to discuss dose adjustment or combination therapy'
                        ]
                    },
                    {
                        role: 'Clinician Pharmacovigilance Summary',
                        title: 'Therapeutic Review & Management Strategy',
                        summary: 'Dihydropyridine-induced peripheral edema secondary to preferential precapillary arteriolar vasodilation without true generalized fluid retention.',
                        points: [
                            'Pharmacological Mechanism: Hydrostatic capillary pressure elevation rather than secondary renal sodium retention',
                            'Management Options: Titrate dose to 5mg daily or co-administer ACE inhibitor / ARB to promote postcapillary venodilation',
                            'Differential Exclusion: Evaluate JVP, pulmonary auscultation, and serum creatinine to exclude cardiac/renal decompensation'
                        ]
                    }
                ]
            }
        ]
    }">

    {{-- Section Header --}}
    <div class="text-center mb-12 reveal" data-delay="0">
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-[11px] font-semibold mb-4 glass-soft text-teal-800 uppercase tracking-[0.18em]">
            MediCore Architecture
        </div>
        <h2 class="display-roman text-3xl md:text-4xl lg:text-5xl text-ink-900 leading-tight max-w-3xl mx-auto">
            Interactive clinical reasoning
            <span class="display-italic text-primary">timeline architecture</span>
        </h2>
        <p class="text-sm md:text-base text-slate-500 mt-3 max-w-2xl mx-auto leading-relaxed">
            Explore how MediCore systematically parses, verifies, and synthesizes clinical inputs across each operational stage.
        </p>

        {{-- Mode Selector Tabs (Glassmorphism) --}}
        <div class="flex items-center justify-center gap-3 mt-8 flex-wrap">
            <template x-for="(m, mIdx) in modes" :key="m.id">
                <button
                    @click="activeMode = mIdx; activeStage = 0"
                    :class="activeMode === mIdx ? 'active' : ''"
                    class="timeline-pill-btn px-6 py-3 rounded-2xl text-xs font-bold transition-all duration-300 inline-flex items-center gap-2.5">
                    <span class="w-2 h-2 rounded-full" :style="`background: ${m.badgeColor}`"></span>
                    <span class="text-slate-900" x-text="m.title"></span>
                    <span class="text-[9px] font-semibold px-2 py-0.5 rounded-full uppercase"
                        :style="`background: ${m.badgeBg}; color: ${m.badgeColor};`"
                        x-text="m.badge"></span>
                </button>
            </template>
        </div>
    </div>

    {{-- Main Interactive Timeline Container --}}
    <div class="glass-panel-ultra p-6 md:p-10 reveal" data-delay="100">

        {{-- Mode Overview Header --}}
        <div class="flex flex-wrap items-center justify-between gap-4 pb-6 mb-8 border-b border-slate-200/80">
            <div>
                <span class="text-[10px] font-mono font-bold tracking-wider uppercase px-2.5 py-1 rounded bg-slate-100 text-slate-700"
                    x-text="modes[activeMode].code"></span>
                <h3 class="text-xl md:text-2xl font-bold text-slate-900 mt-2" x-text="modes[activeMode].title + ' Processing Workflow'"></h3>
            </div>
            <p class="text-xs md:text-sm text-slate-600 max-w-md" x-text="modes[activeMode].summary"></p>
        </div>

        {{-- Horizontal 4-Stage Timeline Pipeline --}}
        <div class="relative mb-10">
            <div class="timeline-track-line hidden lg:block"></div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 relative z-10">
                <template x-for="(stage, sIdx) in modes[activeMode].stages" :key="sIdx">
                    <div
                        @click="activeStage = sIdx"
                        :class="activeStage === sIdx ? 'bg-white shadow-md border-teal-500 scale-[1.02]' : 'bg-white/70 border-slate-200/80 hover:bg-white/90'"
                        class="timeline-node p-5 rounded-2xl border transition-all duration-300 cursor-pointer flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <span class="w-8 h-8 rounded-xl flex items-center justify-center font-bold text-xs"
                                    :style="activeStage === sIdx ? 'background: #0d9488; color: #ffffff;' : 'background: #f1f5f9; color: #475569;'"
                                    x-text="stage.num"></span>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-teal-700" x-text="'Stage ' + (sIdx + 1)"></span>
                            </div>
                            <h4 class="text-xs md:text-sm font-bold text-slate-900 mb-1" x-text="stage.title"></h4>
                            <p class="text-[11px] text-teal-800 font-medium mb-2" x-text="stage.subtitle"></p>
                            <p class="text-[11px] text-slate-500 leading-relaxed" x-text="stage.desc"></p>
                        </div>

                        <div class="mt-4 pt-2 border-t border-slate-100 flex items-center justify-between text-[10px] font-semibold"
                            :style="activeStage === sIdx ? 'color: #0d9488;' : 'color: #94a3b8;'">
                            <span x-text="activeStage === sIdx ? 'Active Stage' : 'Click to inspect'"></span>
                            <span x-show="activeStage === sIdx">●</span>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- Representative Intake Simulation (Glass Box) --}}
        <div class="p-5 rounded-2xl bg-white/80 border border-slate-200/80 mb-8">
            <div class="flex items-center justify-between mb-1.5">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Representative Intake Query</span>
                <span class="text-[10px] font-semibold text-teal-700">Live Simulation</span>
            </div>
            <p class="text-xs md:text-sm text-slate-900 font-medium italic" x-text="'“' + modes[activeMode].sampleInput + '”'"></p>
        </div>

        {{-- 2 BIG DUAL-PERSPECTIVE ULTRA GLASSMORPHISM OUTPUT CARDS --}}
        <div>
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider text-teal-800">
                    Dual-Perspective Clinical Synthesis Output
                </h4>
                <span class="text-[11px] font-medium text-slate-400">Structured Clinical Report</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <template x-for="(out, oIdx) in modes[activeMode].outputs" :key="oIdx">
                    <div class="glass-output-card p-6 md:p-8 flex flex-col justify-between">
                        <div>
                            {{-- Header --}}
                            <div class="flex items-center justify-between gap-3 pb-4 mb-4 border-b border-slate-100">
                                <span class="text-[11px] font-bold uppercase tracking-wider px-3 py-1 rounded-full bg-teal-50 text-teal-800 border border-teal-200"
                                    x-text="out.role"></span>
                                <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Protocol Output</span>
                            </div>

                            {{-- Title & Summary --}}
                            <h5 class="text-base md:text-lg font-bold text-slate-900 mb-2" x-text="out.title"></h5>
                            <p class="text-xs md:text-[13px] text-slate-600 leading-relaxed mb-5" x-text="out.summary"></p>

                            {{-- Key Action Points --}}
                            <div class="space-y-2.5 pt-2">
                                <template x-for="(pt, pIdx) in out.points" :key="pIdx">
                                    <div class="p-3 rounded-xl bg-white/75 border border-slate-200/70 flex items-start gap-2.5 text-xs text-slate-700 leading-snug">
                                        <span class="w-1.5 h-1.5 rounded-full bg-teal-600 mt-1.5 flex-shrink-0"></span>
                                        <span x-text="pt"></span>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-[11px] font-semibold text-teal-800">
                            <span>Verified Clinical Format</span>
                            <span>Pharmasis Engine</span>
                        </div>
                    </div>
                </template>
            </div>
        </div>

    </div>

</section>

{{-- ══════════════════════ CORE MISSION & VALUES (UNIFIED SINGLE GLASS PANEL) ══════════════════════ --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20" id="section-goals">

    <div class="relative rounded-3xl overflow-hidden p-8 sm:p-12 lg:p-16 border border-white/90 reveal"
        data-delay="0"
        style="background: rgba(255, 255, 255, 0.75); backdrop-filter: blur(28px); -webkit-backdrop-filter: blur(28px); box-shadow: 0 24px 60px rgba(15, 118, 110, 0.08), 0 1px 0 rgba(255, 255, 255, 1) inset;">

        {{-- Subtle ambient glow --}}
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 w-80 h-80 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10">
            {{-- Top Header & Overview --}}
            <div class="max-w-3xl mb-12">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-[11px] font-semibold mb-4 glass-soft text-teal-800 uppercase tracking-[0.16em]">
                    Mission & Clinical Integrity
                </div>
                <h2 class="display-roman text-3xl sm:text-4xl lg:text-5xl text-ink-900 leading-tight mb-4">
                    Making healthcare knowledge
                    <span class="display-italic text-primary">transparent, verified, and accessible.</span>
                </h2>
                <p class="text-base sm:text-lg text-slate-600 leading-relaxed">
                    Pharmasis bridges the gap between complex pharmacology and everyday decision-making by delivering structured drug information, intelligent symptom screening, and dual-perspective clinical reports with complete clarity.
                </p>
            </div>

            {{-- 3 Key Pillars (Simple, clean typography layout - no nested cards) --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 pt-8 border-t border-slate-200/80">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-teal-700 block mb-2">01 / Verified Evidence</span>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Standardized Drug Directory</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Comprehensive pharmaceutical data curated directly from authorized medical registries, providing clear indications, dosage guidance, and contraindication profiles.
                    </p>
                </div>

                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-teal-700 block mb-2">02 / Objective Triage</span>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Context-Aware Screening</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Interactive symptom analysis evaluates risk factors and warning signs, helping patients identify whether routine home care or immediate medical attention is necessary.
                    </p>
                </div>

                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-teal-700 block mb-2">03 / Dual Format</span>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Patient & Clinician Alignment</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Evaluations generate both straightforward explanations for individual understanding and structured clinical summaries ready to share during medical consultations.
                    </p>
                </div>
            </div>

            {{-- Clean Metrics Bar --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-6 pt-10 mt-10 border-t border-slate-200/60">
                <div>
                    <span class="text-2xl sm:text-3xl font-bold text-slate-900">16,000+</span>
                    <p class="text-xs text-slate-500 mt-1">Verified Medications</p>
                </div>
                <div>
                    <span class="text-2xl sm:text-3xl font-bold text-slate-900">3 Modes</span>
                    <p class="text-xs text-slate-500 mt-1">Specialized AI Engines</p>
                </div>
                <div>
                    <span class="text-2xl sm:text-3xl font-bold text-slate-900">Dual View</span>
                    <p class="text-xs text-slate-500 mt-1">Patient & Clinical Output</p>
                </div>
                <div>
                    <span class="text-2xl sm:text-3xl font-bold text-slate-900">100% Free</span>
                    <p class="text-xs text-slate-500 mt-1">Open Health Access</p>
                </div>
            </div>
        </div>

    </div>

</section>

{{-- ══════════════════════ AI HISTORY MODAL ══════════════════════ --}}
@include('partials.medicheck-history')

{{-- ══════════════════════ CTA CALLOUT (INTERACTIVE MOUSE-TRACKING GRID) ══════════════════════ --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-20" id="section-cta">
    <div class="relative overflow-hidden rounded-3xl p-10 md:p-20 text-center text-white"
        style="background: linear-gradient(135deg, #04181a 0%, #08383b 45%, #0f5256 80%, #156b70 100%); box-shadow: 0 24px 64px rgba(4, 24, 26, 0.35);"
        x-data="socialGrid()" @mousemove="handleMouseMove" x-ref="container">

        {{-- Interactive Grid Layer 1: Base grid --}}
        <div class="absolute inset-0 z-0 opacity-15 pointer-events-none">
            <svg class="w-full h-full text-teal-400">
                <defs>
                    <pattern id="cta-grid-base" width="40" height="40" patternUnits="userSpaceOnUse" :x="gridOffsetX" :y="gridOffsetY">
                        <path d="M 40 0 L 0 0 0 40" fill="none" stroke="currentColor" stroke-width="1" />
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#cta-grid-base)" />
            </svg>
        </div>

        {{-- Interactive Grid Layer 2: Mouse tracking spotlight --}}
        <div class="absolute inset-0 z-0 opacity-60 pointer-events-none transition-opacity duration-300"
            :style="`mask-image: radial-gradient(380px circle at ${mouseX}px ${mouseY}px, black, transparent); -webkit-mask-image: radial-gradient(380px circle at ${mouseX}px ${mouseY}px, black, transparent);`">
            <svg class="w-full h-full text-teal-200">
                <defs>
                    <pattern id="cta-grid-active" width="40" height="40" patternUnits="userSpaceOnUse" :x="gridOffsetX" :y="gridOffsetY">
                        <path d="M 40 0 L 0 0 0 40" fill="none" stroke="currentColor" stroke-width="1.5" />
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#cta-grid-active)" />
            </svg>
        </div>

        {{-- Ambient Blobs --}}
        <div class="absolute inset-0 pointer-events-none z-0">
            <div class="absolute -top-20 -right-20 w-80 h-80 rounded-full bg-teal-400/20 blur-[100px]"></div>
            <div class="absolute -bottom-20 -left-20 w-80 h-80 rounded-full bg-emerald-400/20 blur-[100px]"></div>
        </div>

        {{-- Card Content --}}
        <div class="relative z-10 max-w-3xl mx-auto reveal" data-delay="0">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-[11px] font-bold uppercase tracking-widest text-teal-300 bg-white/10 border border-white/15 backdrop-blur-md mb-6">
                Healthcare Intelligence Platform
            </span>

            <h2 class="display-roman text-3xl sm:text-4xl md:text-5xl lg:text-6xl leading-tight mb-5">
                Begin your clinical screening with
                <span class="display-italic text-teal-200">Pharmasis today</span>
            </h2>

            <p class="text-sm md:text-base text-teal-100/85 max-w-xl mx-auto mb-10 leading-relaxed">
                Describe what you are experiencing in plain language or inspect verified medicine monographs with structured safety parameters.
            </p>

            {{-- Single Spectacular Action Button --}}
            <div class="flex items-center justify-center">
                <a href="{{ route('home') }}#ceksehat-section"
                    class="inline-flex items-center gap-3 px-9 py-4 rounded-full text-sm font-bold bg-white text-teal-950 hover:bg-slate-50 transition-all duration-300 shadow-[0_12px_36px_rgba(0,0,0,0.25)] hover:shadow-[0_20px_48px_rgba(0,0,0,0.35)] hover:scale-105 active:scale-95 group">
                    <span>Start MediCheck Screening</span>
                    <svg class="w-4 h-4 text-teal-700 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </a>
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
@include('partials.hero-medicheck-id-scripts')

<script>
// ── Pre-fill symptom text and scroll smoothly ──
function pharmasisFillCondition(query) {
    const section = document.getElementById('ceksehat-section');
    if (section) {
        section.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    setTimeout(() => {
        const root = document.getElementById('ceksehat-root');
        if (root && root._x_dataStack) {
            const data = root._x_dataStack[0];
            if (data) {
                data.symptoms = query;
                data.activeTab = 'text';
                const ta = root.querySelector('[x-ref="symptomTextarea"]') || root.querySelector('textarea');
                if (ta) {
                    ta.focus();
                    ta.value = query;
                }
            }
        }
        const textareas = document.querySelectorAll('textarea[placeholder*="symptom"], textarea[placeholder*="Describe"], textarea[placeholder*="Keluhan"]');
        textareas.forEach(ta => {
            ta.value = query;
            ta.dispatchEvent(new Event('input', { bubbles: true }));
            ta.focus();
        });
    }, 500);
}

// ── Reveal on scroll observer ──
(function initReveal() {
    const els = document.querySelectorAll('.reveal');
    const io = new IntersectionObserver((entries) => {
        entries.forEach(e => {
            if (e.isIntersecting) {
                const delay = parseInt(e.target.dataset.delay || 0);
                setTimeout(() => e.target.classList.add('visible'), delay);
                io.unobserve(e.target);
            }
        });
    }, { threshold: 0.1 });
    els.forEach(el => io.observe(el));
})();
</script>
@endpush
