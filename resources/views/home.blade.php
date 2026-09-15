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
            transition: transform 280ms cubic-bezier(.22, 1, .36, 1), box-shadow 280ms ease, border-color 280ms ease, background-color 280ms ease;
        }

        .glass-panel-ultra:hover {
            background: rgba(255, 255, 255, 0.92);
            box-shadow: 0 20px 48px rgba(15, 118, 110, 0.10), 0 1px 0 rgba(255, 255, 255, 1) inset;
        }

        /* ── Document Dossier Cards with Raised Top-Left Tab (Clinical Inquiries) ── */
        .doc-inquiry-card {
            display: flex;
            flex-direction: column;
            text-align: left;
            position: relative;
            background: transparent;
            transition: transform 200ms cubic-bezier(.22, 1, .36, 1);
        }

        .doc-tab-ear {
            display: inline-flex;
            align-items: center;
            padding: 0.3rem 0.75rem;
            background: rgba(255, 255, 255, 0.94);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(203, 213, 225, 0.85);
            border-bottom: none;
            border-top-left-radius: 0.75rem;
            border-top-right-radius: 0.75rem;
            margin-left: 0.5rem;
            position: relative;
            z-index: 2;
            margin-bottom: -1px;
            box-shadow: 0 -2px 6px rgba(15, 118, 110, 0.03);
            transition: all 200ms ease;
        }

        .doc-tab-body {
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(203, 213, 225, 0.85);
            border-radius: 1.15rem;
            border-top-left-radius: 0.35rem;
            padding: 1rem;
            box-shadow: 0 8px 24px rgba(15, 118, 110, 0.04);
            position: relative;
            z-index: 1;
            transition: all 200ms cubic-bezier(.22, 1, .36, 1);
        }

        @media (min-width: 640px) {
            .doc-tab-body {
                padding: 1.15rem;
            }
        }

        .doc-inquiry-card:hover {
            transform: translateY(-3px);
        }

        .doc-inquiry-card:hover .doc-tab-ear {
            background: #ffffff;
            border-color: rgba(13, 148, 136, 0.45);
            box-shadow: 0 -4px 10px rgba(13, 148, 136, 0.08);
        }

        .doc-inquiry-card:hover .doc-tab-body {
            background: #ffffff;
            border-color: rgba(13, 148, 136, 0.45);
            box-shadow: 0 18px 38px rgba(15, 118, 110, 0.09);
        }

        .doc-inquiry-card:active {
            transform: scale(0.985);
        }

        /* ── MediCore Layered Document Dossier System ── */
        .dossier-tab-ear {
            padding: 0.6rem 1rem;
            background: rgba(255, 255, 255, 0.70);
            border: 1px solid rgba(203, 213, 225, 0.85);
            border-bottom: none;
            border-top-left-radius: 0.85rem;
            border-top-right-radius: 0.85rem;
            position: relative;
            z-index: 2;
            margin-bottom: -1px;
            transition: background-color 150ms ease, border-color 150ms ease;
        }

        @media (min-width: 640px) {
            .dossier-tab-ear {
                padding: 0.65rem 1.35rem;
            }
        }

        .dossier-tab-ear.active {
            background: #ffffff !important;
            border-color: rgba(13, 148, 136, 0.5) !important;
            box-shadow: 0 -2px 10px rgba(13, 148, 136, 0.06) !important;
        }

        .dossier-binder-sheet {
            background: rgba(255, 255, 255, 0.90);
            backdrop-filter: blur(32px);
            -webkit-backdrop-filter: blur(32px);
            border: 1px solid rgba(203, 213, 225, 0.85);
            border-radius: 1.5rem;
            border-top-left-radius: 0.35rem;
            box-shadow: 0 16px 40px rgba(15, 118, 110, 0.07), 0 1px 0 rgba(255, 255, 255, 0.95) inset;
            position: relative;
            z-index: 1;
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
            gap: 1rem;
            width: max-content;
            animation: medMarqueeScroll 48s linear infinite;
        }

        @media (min-width: 640px) {
            .med-marquee-track {
                gap: 1.25rem;
            }
        }

        .med-marquee-track:hover {
            animation-play-state: paused;
        }

        @keyframes medMarqueeScroll {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-50%);
            }
        }

        .med-card {
            width: 290px;
            max-width: 82vw;
            flex-shrink: 0;
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.92);
            box-shadow: 0 10px 30px rgba(11, 31, 36, 0.05);
            border-radius: 1.25rem;
            transition: transform 200ms ease, box-shadow 200ms ease, background-color 200ms ease;
        }

        @media (min-width: 640px) {
            .med-card {
                width: 320px;
            }
        }

        .med-card:hover {
            transform: translateY(-4px);
            background: #ffffff;
            box-shadow: 0 18px 40px rgba(11, 31, 36, 0.10);
        }

        /* ── Reveal Animation ── */
        .reveal {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.6s ease, transform 0.6s cubic-bezier(.22, 1, .36, 1);
        }

        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }

        @media (prefers-reduced-motion: reduce) {

            .doc-inquiry-card,
            .med-card,
            .dossier-binder-sheet,
            .reveal {
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7" />
                </svg>
                <p class="text-xs text-amber-800 flex-1">
                    <strong>OpenFDA Data Mode.</strong> Results are currently retrieved directly from the OpenFDA clinical
                    registry.
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
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-20" id="section-conditions">

        <div class="text-center mb-8 sm:mb-10 reveal" data-delay="0">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-[10px] sm:text-[11px] font-semibold mb-3.5 glass-soft text-teal-800 uppercase tracking-[0.16em]"
                data-i18n="inquiries_badge">
                Clinical Inquiries Overview
            </div>
            <h2 class="display-roman text-2xl sm:text-3xl md:text-4xl lg:text-5xl text-ink-900 leading-tight max-w-2xl mx-auto"
                data-i18n="inquiries_title">
                Symptom Case Studies & Real-Time Triage
            </h2>
            <p class="text-xs sm:text-sm md:text-base text-slate-500 mt-2 sm:mt-3 max-w-xl mx-auto leading-relaxed"
                data-i18n="inquiries_desc">
                Explore authentic patient presentations processed through the MediCore multi-tier clinical verification
                engine.
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

        {{-- Document Dossier Cards with Raised Top-Left Tab --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 reveal" data-delay="100">
            @foreach($displayedTopics as $topic)
                <button onclick="pharmasisFillCondition({{ json_encode($topic['query']) }})" class="doc-inquiry-card group"
                    aria-label="Screen for {{ $topic['title'] }}">

                    {{-- Raised Top-Left Document Tab / Ear --}}
                    <div class="flex items-end">
                        <div class="doc-tab-ear">
                            <span
                                class="text-[9px] font-bold tracking-wider uppercase text-slate-600 group-hover:text-teal-900 transition-colors">
                                {{ $topic['specialty'] }}
                            </span>
                        </div>
                    </div>

                    {{-- Main Document Card Body --}}
                    <div class="doc-tab-body flex-1 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-2.5">
                                <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded"
                                    style="color: {{ $topic['badge_color'] }}; background: {{ $topic['badge_bg'] }};">
                                    {{ $topic['severity_label'] }}
                                </span>
                                <span class="text-[10px] font-mono font-semibold text-slate-400">
                                    FILE #{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                                </span>
                            </div>
                            <h3
                                class="text-xs font-bold text-slate-900 leading-snug mb-1.5 group-hover:text-teal-800 transition-colors">
                                {{ $topic['title'] }}
                            </h3>
                            <p class="text-[11px] text-slate-500 leading-relaxed line-clamp-3">
                                {{ $topic['symptom'] }}
                            </p>
                        </div>
                        <div
                            class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[10px] font-semibold text-slate-400 group-hover:text-teal-700 transition-colors">
                            <span>Clinical Intake</span>
                            <span class="flex items-center gap-1 font-bold text-teal-800">Select File →</span>
                        </div>
                    </div>
                </button>
            @endforeach
        </div>

        <p class="text-center text-xs text-slate-400 mt-8 reveal" data-delay="200">
            Clinical screening is intended for educational orientation. Always consult a licensed medical professional for
            formal clinical diagnosis.
        </p>

    </section>

    {{-- ══════════════════════ CONTINUOUS MEDICINE CATALOG CAROUSEL ══════════════════════ --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 pb-24" id="section-medicines" x-data="{
                    quickModalOpen: false,
                    selectedDrug: null,
                    humanizedDrug: null,
                    contextLang: (window.PharmasisI18n ? window.PharmasisI18n.getLanguage() : 'en'),
                    contextLangOpen: false,
                    get availableLangs() {
                        return window.PharmasisI18n ? window.PharmasisI18n.languages : [];
                    },
                    openDetailModal(drug) {
                        this.contextLang = window.PharmasisI18n ? window.PharmasisI18n.getLanguage() : 'en';
                        this.selectedDrug = drug;
                        this.humanizedDrug = window.PharmasisI18n ? window.PharmasisI18n.humanizeDrug(drug, this.contextLang) : drug;
                        this.quickModalOpen = true;
                    },
                    switchContextLang(code) {
                        this.contextLang = code;
                        this.contextLangOpen = false;
                        if (this.selectedDrug && window.PharmasisI18n) {
                            this.humanizedDrug = window.PharmasisI18n.humanizeDrug(this.selectedDrug, code);
                        }
                    },
                    init() {
                        window.addEventListener('pharmasis:languageChanged', (e) => {
                            this.contextLang = e.detail.lang;
                            if (this.selectedDrug && window.PharmasisI18n) {
                                this.humanizedDrug = window.PharmasisI18n.humanizeDrug(this.selectedDrug, e.detail.lang);
                            }
                        });
                    }
                }">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-6 gap-4 reveal" data-delay="0">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <p class="text-[10px] sm:text-[11px] font-bold text-teal-800 uppercase tracking-[0.16em]"
                        data-i18n="med_repo_badge">Active Medicine Repository</p>
                </div>
                <h2 class="display-roman text-2xl sm:text-3xl md:text-4xl text-ink-900 leading-tight">
                    <span data-i18n="med_repo_title_1">Continuous pharmacological</span>
                    <span class="display-italic text-primary" data-i18n="med_repo_title_2">catalog stream</span>
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-lg" data-i18n="med_repo_desc">
                    Hover anywhere over the catalog to pause and inspect formulations in detail.
                </p>
            </div>

            <div class="flex items-center gap-2.5 sm:gap-3 flex-wrap">
                <span
                    class="text-[10px] sm:text-[11px] font-medium text-slate-400 bg-white/80 border border-slate-200 px-3 py-1.5 rounded-full shadow-sm"
                    data-i18n="med_repo_autoscroll">
                    Auto-scrolling · Hover to pause
                </span>
                <a href="{{ route('drugs.search') }}"
                    class="px-4 sm:px-5 py-2 rounded-full text-xs font-semibold bg-teal-700 hover:bg-teal-800 text-white shadow-sm inline-flex items-center gap-1.5 transition-colors">
                    <span data-i18n="med_repo_browse_btn">Browse Full Directory</span>
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
                                <span
                                    class="text-[10px] font-bold text-teal-800 bg-teal-50 px-2.5 py-1 rounded-md border border-teal-100">
                                    {{ $initials }} · RX
                                </span>
                                <span
                                    class="text-[10px] font-semibold px-2 py-0.5 rounded bg-slate-100/90 text-slate-700 max-w-[170px] truncate border border-slate-200">
                                    {{ $drugClass }}
                                </span>
                            </div>

                            {{-- Name --}}
                            <h3
                                class="font-bold text-slate-900 text-sm leading-snug mb-1 line-clamp-1 hover:text-teal-700 transition-colors">
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
                            <button @click="openDetailModal({{ json_encode($drugJson) }})" type="button"
                                class="text-xs font-semibold text-teal-800 hover:text-teal-900 hover:bg-teal-50 px-2.5 py-1.5 rounded-lg transition-colors"
                                data-i18n-title="med_repo_quick_info">
                                <span data-i18n="med_repo_quick_info">Quick Info</span>
                            </button>

                            <a href="{{ $href }}"
                                class="text-xs font-bold text-slate-700 hover:text-teal-700 inline-flex items-center gap-1 transition-colors">
                                <span data-i18n="med_repo_monograph">Monograph</span>
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Quick View Modal (Alpine) — AI Humanized + In-Modal Language Switcher --}}
        <div x-show="quickModalOpen" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 transform scale-100"
            x-transition:leave-end="opacity-0 transform scale-95"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
            style="display: none;">

            <div @click.away="quickModalOpen = false"
                class="bg-white/95 backdrop-blur-xl rounded-2xl sm:rounded-3xl max-w-2xl lg:max-w-3xl w-full max-h-[88vh] overflow-y-auto p-5 sm:p-6 shadow-2xl border border-white/90 relative transform transition-all">
                <template x-if="humanizedDrug">
                    <div class="flex flex-col">
                        {{-- Modal Header Bar --}}
                        <div class="flex items-start justify-between gap-3 pb-3 border-b border-slate-100">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 mb-1 flex-wrap">
                                    <span
                                        class="text-[9.5px] font-bold uppercase tracking-wider text-teal-800 bg-teal-50 px-2 py-0.5 rounded-md border border-teal-200"
                                        x-text="humanizedDrug.displayClass || humanizedDrug.class"></span>
                                    <div class="badge-antigravity-ai px-2 py-0.5 rounded-full flex items-center gap-1 text-[9.5px] font-bold">
                                        <svg class="w-2.5 h-2.5 text-indigo-600 animate-spin" style="animation-duration: 9s;" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2v4m0 12v4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83M2 12h4m12 0h4M4.93 19.07l2.83-2.83m8.48-8.48l2.83-2.83" />
                                        </svg>
                                        <span class="text-antigravity-gradient" data-i18n="med_repo_ai_badge">AI Humanized</span>
                                    </div>
                                </div>
                                <h3 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight leading-snug" x-text="humanizedDrug.name"></h3>
                                <p class="text-xs text-slate-500 italic mt-0.5 truncate"
                                    x-text="humanizedDrug.displayGeneric || (humanizedDrug.generic ? 'Generic: ' + humanizedDrug.generic : 'Standard Formulation')"></p>
                            </div>

                            <div class="flex items-center gap-1.5 flex-shrink-0">
                                {{-- In-Modal Context Language Switcher --}}
                                <div class="relative" @click.outside="contextLangOpen = false">
                                    <button type="button" @click="contextLangOpen = !contextLangOpen"
                                        class="flex items-center gap-1.5 text-[11px] font-bold text-teal-800 bg-white border border-teal-200 px-2.5 py-1 rounded-lg hover:bg-teal-50 transition-colors shadow-sm focus:outline-none">
                                        <svg class="w-3 h-3 text-teal-700 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                                        </svg>
                                        <span class="uppercase font-mono tracking-wider font-bold" x-text="contextLang.toUpperCase()"></span>
                                        <svg class="w-2.5 h-2.5 text-teal-500 transition-transform duration-200" :class="{ 'rotate-180': contextLangOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>
                                    <div x-show="contextLangOpen" x-cloak
                                        x-transition:enter="transition ease-out duration-150"
                                        x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                                        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                        x-transition:leave="transition ease-in duration-100"
                                        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                        x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                                        class="absolute right-0 top-full mt-1.5 w-44 rounded-xl p-1 z-50 shadow-xl border border-slate-200/80 max-h-56 overflow-y-auto"
                                        style="background: rgba(255,255,255,0.98); backdrop-filter: blur(16px);">
                                        <template x-for="lang in availableLangs" :key="lang.code">
                                            <button type="button" @click="switchContextLang(lang.code)"
                                                class="w-full flex items-center gap-2 px-2 py-1.5 rounded-lg text-xs transition-colors"
                                                :class="contextLang === lang.code ? 'bg-teal-50 text-teal-800 font-bold' : 'hover:bg-slate-50 text-slate-700 font-medium'">
                                                <span class="w-5 text-center text-[9px] font-bold font-mono uppercase bg-slate-100 text-slate-600 border border-slate-200 rounded px-0.5" x-text="lang.code"></span>
                                                <span class="truncate" x-text="lang.native"></span>
                                            </button>
                                        </template>
                                    </div>
                                </div>

                                {{-- Close Button --}}
                                <button @click="quickModalOpen = false"
                                    class="text-slate-400 hover:text-slate-700 p-1.5 rounded-lg hover:bg-slate-100 transition-colors"
                                    aria-label="Close modal">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- Essential Compact 2-Column Grid --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 my-4">
                            {{-- Card 1: Therapeutic Indications --}}
                            <div class="p-4 rounded-xl bg-gradient-to-br from-teal-50/90 via-emerald-50/30 to-white border border-teal-200/80 shadow-sm flex flex-col">
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="w-6 h-6 rounded-md bg-teal-600/10 text-teal-700 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                        </svg>
                                    </div>
                                    <h4 class="font-bold text-teal-950 uppercase tracking-wider text-[10.5px]"
                                        data-i18n="med_repo_indications">Therapeutic Indications</h4>
                                </div>
                                <p class="text-slate-700 text-xs leading-relaxed flex-1"
                                    x-text="humanizedDrug.displayUses || humanizedDrug.uses"></p>
                            </div>

                            {{-- Card 2: Adverse Effects & Precautions --}}
                            <div class="p-4 rounded-xl bg-gradient-to-br from-amber-50/90 via-orange-50/30 to-white border border-amber-200/80 shadow-sm flex flex-col">
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="w-6 h-6 rounded-md bg-amber-600/10 text-amber-700 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                    </div>
                                    <h4 class="font-bold text-amber-950 uppercase tracking-wider text-[10.5px]"
                                        data-i18n="med_repo_adverse">Adverse Effects &amp; Precautions</h4>
                                </div>
                                <p class="text-amber-900/90 text-xs leading-relaxed flex-1"
                                    x-text="humanizedDrug.displaySideEffects || humanizedDrug.side_effects || 'Consult a healthcare professional regarding potential adverse reactions.'"></p>
                            </div>
                        </div>

                        {{-- Modal Footer Actions --}}
                        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-3 border-t border-slate-100">
                            <div class="flex items-center gap-1.5 text-[10.5px] text-slate-400">
                                <svg class="w-3 h-3 text-teal-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span>Clinical Intelligence Verified</span>
                            </div>

                            <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                                <button
                                    @click="pharmasisFillCondition('Provide comprehensive clinical details for ' + humanizedDrug.name + ' (' + (humanizedDrug.generic || '') + ')'); quickModalOpen = false;"
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-semibold bg-teal-50 text-teal-800 hover:bg-teal-100 transition-colors shadow-sm"
                                    data-i18n="med_repo_analyze_btn">
                                    Analyze with MediCheck
                                </button>
                                <a :href="humanizedDrug.url"
                                    class="px-4 py-1.5 rounded-xl text-xs font-semibold bg-gradient-to-r from-teal-700 to-teal-800 text-white hover:from-teal-800 hover:to-teal-900 shadow-sm transition-all inline-flex items-center gap-1">
                                    <span data-i18n="med_repo_open_monograph">Full Monograph</span>
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

    </section>

    {{-- ══════════════════════ MEDICORE ARCHITECTURE & INTERACTIVE TIMELINE ══════════════════════ --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-20 overflow-hidden" id="section-how" x-data="{
                    activeMode: 0,
                    activeStage: 0,
                    modes: [],
                    updateModes() {
                        const i18n = window.PharmasisI18n;
                        const t = (k, f) => i18n ? i18n.t(k, f) : f;
                        this.modes = [
                            {
                                id: 'medi_facts',
                                title: t('tab_medifacts_name', 'MediFacts'),
                                summary: t('tab_medifacts_desc', 'Deterministic retrieval of validated pharmacological monographs.'),
                                sampleInput: t('case1_query', 'What is the precise cellular mechanism of Metformin and what renal parameters require dosage titration?'),
                                stages: [
                                    { num: '01', title: 'Intent Identification', desc: 'Identifies active drug ingredients without diagnostic assumptions.' },
                                    { num: '02', title: 'Monograph Retrieval', desc: 'Queries drug databases for clearance, dosing, and mechanism of action.' },
                                    { num: '03', title: 'Safety Stratification', desc: 'Evaluates black box warnings, eGFR renal thresholds, and interactions.' },
                                    { num: '04', title: 'Structured Knowledge', desc: 'Synthesizes plain-language patient summaries alongside clinician profiles.' }
                                ],
                                outputs: [
                                    {
                                        role: 'Patient Educational Brief',
                                        title: 'Plain-Language Therapeutic Orientation',
                                        summary: t('case1_patient_out', 'Dry cough is a known, non-allergic effect of ACE inhibitors occurring in 10-20% of patients due to bradykinin accumulation. Not infectious.'),
                                        points: [
                                            'Primary Action: Reduces blood pressure by preventing angiotensin conversion',
                                            'Standard Administration: Taken with or without food at consistent daily time',
                                            'Safety Note: Periodic kidney function and potassium checks recommended'
                                        ]
                                    },
                                    {
                                        role: 'Clinician Pharmacological Monograph',
                                        title: 'Cellular Mechanism & Dosing Parameters',
                                        summary: t('case1_clinician_out', 'Etiology: ACEI-induced bradykinin/substance-P accumulation. Recommendation: Discontinue Lisinopril, switch to ARB (e.g., Losartan).'),
                                        points: [
                                            'Renal Parameters: Obtain baseline eGFR prior to therapy initiation',
                                            'Titration Protocol: eGFR 30-44 mL/min requires maximum daily dose reduction',
                                            'Absolute Contraindications: eGFR <30 mL/min or history of angioedema'
                                        ]
                                    }
                                ]
                            },
                            {
                                id: 'medi_check',
                                title: t('tab_medicheck_name', 'MediCheck'),
                                summary: t('tab_medicheck_desc', 'Adaptive clinical screening with targeted follow-up probing and triage.'),
                                sampleInput: t('case2_query', '42yo female reporting 3 days of progressive right upper quadrant abdominal pain postprandially, with mild nausea and low-grade fever.'),
                                stages: [
                                    { num: '01', title: 'Complaint Intake', desc: 'Extracts symptom duration, localization, severity, and red flags.' },
                                    { num: '02', title: 'Dynamic Probing', desc: 'Generates targeted questions to evaluate aggravating triggers and differentials.' },
                                    { num: '03', title: 'Clinical Reasoning', desc: 'Maps symptom clusters against diagnostic criteria to establish hypotheses.' },
                                    { num: '04', title: 'Triage Synthesis', desc: 'Delivers guidance on emergency versus scheduled outpatient consultation.' }
                                ],
                                outputs: [
                                    {
                                        role: 'Patient Clinical Guidance',
                                        title: 'Immediate Action & Red Flag Orientation',
                                        summary: t('case2_patient_out', 'Symptoms indicate gallbladder inflammation (biliary colic or cholecystitis). Requires direct physical exam and ultrasound.'),
                                        points: [
                                            'Suspected Context: Biliary or upper gastrointestinal inflammation',
                                            'Recommended Step: Schedule in-person clinical consultation with physical exam',
                                            'Emergency Warning: Seek emergency care if pain becomes unremitting or yellowing appears'
                                        ]
                                    },
                                    {
                                        role: 'Clinician Differential Matrix',
                                        title: 'ICD-10 Aligned Triage Assessment',
                                        summary: t('case2_clinician_out', 'Differential: Acute cholecystitis vs symptomatic cholelithiasis. Recommended labs: CBC, LFTs, Lipase, Right upper quadrant ultrasound.'),
                                        points: [
                                            'Primary Differential: Acute Cholecystitis / Biliary Colic (ICD-10 K81.0 / K80.20)',
                                            'Secondary Hypotheses: Peptic Ulcer Disease (K27.9), Acute Pancreatitis (K85.9)',
                                            'Recommended Workup: CBC, LFTs, Serum Amylase/Lipase, Abdominal Ultrasound'
                                        ]
                                    }
                                ]
                            },
                            {
                                id: 'medi_combo',
                                title: t('tab_medicombo_name', 'MediCombo'),
                                summary: t('tab_medicombo_desc', 'Dual-track analysis linking active symptoms with ongoing drug regimens.'),
                                sampleInput: t('case3_query', '70yo male taking Warfarin for atrial fibrillation started on high-dose Fluconazole for oral candidiasis.'),
                                stages: [
                                    { num: '01', title: 'Entity Extraction', desc: 'Captures active bodily symptoms alongside specific medication regimens.' },
                                    { num: '02', title: 'Pharmacovigilance', desc: 'Cross-checks Adverse Drug Reaction frequencies against onset timeline.' },
                                    { num: '03', title: 'Dual-Track Analysis', desc: 'Differentiates direct drug side effects from separate emergent pathology.' },
                                    { num: '04', title: 'Consultation Brief', desc: 'Produces a cohesive report detailing potential modifications or alternatives.' }
                                ],
                                outputs: [
                                    {
                                        role: 'Patient Medication Safety Report',
                                        title: 'Adverse Effect Verification',
                                        summary: t('case3_patient_out', 'Major interaction alert: Fluconazole significantly amplifies Warfarin blood-thinning potency, creating dangerous bleeding risk.'),
                                        points: [
                                            'Key Risk: Elevated probability of significant abnormal bruising or bleeding',
                                            'Important Precaution: Do not discontinue or alter doses without physician supervision',
                                            'Next Action: Contact prescribing physician immediately to review concurrent therapy'
                                        ]
                                    },
                                    {
                                        role: 'Clinician Pharmacovigilance Summary',
                                        title: 'Therapeutic Review & Management Strategy',
                                        summary: t('case3_clinician_out', 'Mechanism: CYP2C9 inhibition by Fluconazole slows S-warfarin metabolism. Action: Reduce Warfarin dose by 50% & monitor INR closely.'),
                                        points: [
                                            'Pharmacological Mechanism: CYP2C9/3A4 competitive inhibition reducing S-warfarin clearance',
                                            'Clinical Consequence: S-warfarin AUC increased 2-3 fold with significant INR elevation',
                                            'Management Options: Reduce Warfarin dose by 50% with serial INR monitoring or switch antifungal'
                                        ]
                                    }
                                ]
                            }
                        ];
                    },
                    init() {
                        this.updateModes();
                        window.addEventListener('pharmasis:languageChanged', () => {
                            this.updateModes();
                        });
                    }
                }">

        {{-- Section Header --}}
        <div class="text-center mb-8 sm:mb-10 reveal" data-delay="0">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-[10px] sm:text-[11px] font-semibold mb-3.5 glass-soft text-teal-800 uppercase tracking-[0.16em]"
                data-i18n="arch_badge">
                MediCore Architecture
            </div>
            <h2 class="display-roman text-2xl sm:text-3xl md:text-4xl lg:text-5xl text-ink-900 leading-tight max-w-3xl mx-auto"
                data-i18n="arch_title">
                MediCore Clinical Architecture
            </h2>
            <p class="text-xs sm:text-sm md:text-base text-slate-500 mt-2 max-w-xl mx-auto leading-relaxed"
                data-i18n="arch_desc">
                Three specialized layers engineered to convert unstructured human experience into validated clinical
                insights.
            </p>
        </div>

        {{-- Layered Document Folder System (Raised Top Tabs + Main Dossier Sheet) --}}
        <div class="reveal" data-delay="100">

            {{-- Raised Dossier Folder Tabs (Clean fit, No scrollbar) --}}
            <div class="flex items-end gap-1.5 sm:gap-3 px-1 sm:px-4">
                <template x-for="(m, mIdx) in modes" :key="m.id">
                    <button @click="activeMode = mIdx; activeStage = 0"
                        :class="activeMode === mIdx ? 'dossier-tab-ear active' : 'dossier-tab-ear'"
                        class="px-3.5 sm:px-5 py-2 sm:py-2.5 text-[11px] sm:text-xs font-bold transition-colors cursor-pointer text-slate-800">
                        <span x-text="m.title"></span>
                    </button>
                </template>
            </div>

            {{-- Main Layered Dossier Document Sheet --}}
            <div class="dossier-binder-sheet p-4 sm:p-7 md:p-10">

                {{-- Header --}}
                <div
                    class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 sm:pb-5 mb-5 sm:mb-7 border-b border-slate-200/80">
                    <div>
                        <h3 class="text-lg sm:text-xl md:text-2xl font-bold text-slate-900"
                            x-text="modes[activeMode].title + ' Workflow'"></h3>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-600 max-w-md leading-relaxed"
                        x-text="modes[activeMode].summary"></p>
                </div>

                {{-- 1. Sample Query --}}
                <div class="mb-5 sm:mb-7">
                    <h4 class="text-[11px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider mb-2"
                        data-i18n="dossier_query_title">
                        01 / Sample Query & Clinical Entry
                    </h4>
                    <div class="p-3.5 sm:p-4 rounded-xl sm:rounded-2xl bg-slate-50/90 border border-slate-200/80 text-xs sm:text-sm text-slate-800 italic leading-relaxed"
                        x-text="'“' + modes[activeMode].sampleInput + '”'">
                    </div>
                </div>

                {{-- 2. Processing Pipeline --}}
                <div class="mb-7 sm:mb-9">
                    <h4 class="text-[11px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider mb-2.5 sm:mb-3"
                        data-i18n="dossier_pipeline_title">
                        02 / Execution Pipeline
                    </h4>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-3.5">
                        <template x-for="(stage, sIdx) in modes[activeMode].stages" :key="sIdx">
                            <div @click="activeStage = sIdx"
                                :class="activeStage === sIdx ? 'bg-white shadow-sm border-teal-600' : 'bg-slate-50/80 border-slate-200/80 hover:bg-white'"
                                class="p-4 sm:p-5 rounded-xl sm:rounded-2xl border transition-colors cursor-pointer flex flex-col justify-between">
                                <div>
                                    <span class="text-xs font-bold text-teal-800 font-mono mb-1.5 sm:mb-2 block"
                                        x-text="stage.num"></span>
                                    <h5 class="text-xs sm:text-sm font-bold text-slate-900 mb-1" x-text="stage.title"></h5>
                                    <p class="text-[11px] text-slate-600 leading-relaxed" x-text="stage.desc"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- 3. Dual Clinical Outputs (Neutral rows, no colors/stripes) --}}
                <div>
                    <h4 class="text-[11px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider mb-2.5 sm:mb-3"
                        data-i18n="dossier_output_title">
                        03 / Clinical Output Preview
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5">
                        <template x-for="(out, oIdx) in modes[activeMode].outputs" :key="oIdx">
                            <div
                                class="p-4 sm:p-6 md:p-7 rounded-xl sm:rounded-2xl bg-white border border-slate-200/90 shadow-sm flex flex-col justify-between">
                                <div>
                                    <div class="mb-3">
                                        <span
                                            class="text-[10px] sm:text-[11px] font-bold text-teal-800 uppercase tracking-wider block mb-1"
                                            x-text="out.role"></span>
                                        <h5 class="text-sm sm:text-base font-bold text-slate-900" x-text="out.title"></h5>
                                    </div>
                                    <p class="text-xs text-slate-600 leading-relaxed mb-3.5 sm:mb-4" x-text="out.summary">
                                    </p>

                                    {{-- Neutral List Items --}}
                                    <div class="space-y-2">
                                        <template x-for="(pt, pIdx) in out.points" :key="pIdx">
                                            <div
                                                class="p-2.5 sm:p-3 rounded-lg sm:rounded-xl bg-slate-50 border border-slate-200/70 text-xs text-slate-700 leading-relaxed">
                                                <span x-text="pt"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

            </div>

        </div>

    </section>

    {{-- ══════════════════════ CORE MISSION & VALUES (UNIFIED SINGLE GLASS PANEL) ══════════════════════ --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-20" id="section-goals">

        <div class="relative rounded-3xl overflow-hidden p-6 sm:p-10 lg:p-14 border border-white/90 reveal" data-delay="0"
            style="background: rgba(255, 255, 255, 0.75); backdrop-filter: blur(28px); -webkit-backdrop-filter: blur(28px); box-shadow: 0 24px 60px rgba(15, 118, 110, 0.08), 0 1px 0 rgba(255, 255, 255, 1) inset;">

            {{-- Subtle ambient glow --}}
            <div class="absolute -top-24 -right-24 w-96 h-96 bg-teal-500/10 rounded-full blur-3xl pointer-events-none">
            </div>
            <div class="absolute -bottom-24 -left-24 w-80 h-80 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none">
            </div>

            <div class="relative z-10">
                {{-- Top Header & Overview --}}
                <div class="max-w-3xl mb-8 sm:mb-12">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-[10px] sm:text-[11px] font-semibold mb-3.5 glass-soft text-teal-800 uppercase tracking-[0.16em]"
                        data-i18n="mission_badge">
                        Clinical Integrity
                    </div>
                    <h2 class="display-roman text-2xl sm:text-3xl md:text-4xl lg:text-5xl text-ink-900 leading-tight mb-3 sm:mb-4"
                        data-i18n="mission_title">
                        Our Mission & Clinical Architecture
                    </h2>
                    <p class="text-xs sm:text-base md:text-lg text-slate-600 leading-relaxed" data-i18n="mission_desc">
                        Pharmasis was created to bridge the critical gap between confusing internet medical searches and
                        actionable clinical understanding. We do not replace healthcare providers—we empower patients with
                        objective triage and equip clinicians with structured summaries.
                    </p>
                </div>

                {{-- 3 Key Pillars (Simple, clean typography layout - no nested cards) --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8 pt-6 sm:pt-8 border-t border-slate-200/80">
                    <div>
                        <span
                            class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-teal-700 block mb-1.5 sm:mb-2">01</span>
                        <h3 class="text-base sm:text-lg font-bold text-slate-900 mb-1.5" data-i18n="pillar_1_title">Verified
                            Evidence</h3>
                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed mb-3" data-i18n="pillar_1_desc">
                            Cross-referenced against 16,000+ curated clinical profiles from WebMD and 67,000+ approved drug
                            records from the U.S. FDA OpenFDA database — totaling 83,000+ verified pharmacological entries.
                        </p>
                    </div>

                    <div>
                        <span
                            class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-teal-700 block mb-1.5 sm:mb-2">02</span>
                        <h3 class="text-base sm:text-lg font-bold text-slate-900 mb-1.5" data-i18n="pillar_2_title">
                            Objective Triage</h3>
                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed" data-i18n="pillar_2_desc">
                            Real-time severity stratification based on verified emergency triage standards.
                        </p>
                    </div>

                    <div>
                        <span
                            class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-teal-700 block mb-1.5 sm:mb-2">03</span>
                        <h3 class="text-base sm:text-lg font-bold text-slate-900 mb-1.5" data-i18n="pillar_3_title">
                            Dual-Format Syntheses</h3>
                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed" data-i18n="pillar_3_desc">
                            Produces easy-to-understand explanations for patients and concise summaries for physicians.
                        </p>
                    </div>
                </div>

                {{-- Clean Metrics Bar --}}
                <div
                    class="grid grid-cols-2 sm:grid-cols-4 gap-4 sm:gap-6 pt-6 sm:pt-8 mt-6 sm:mt-8 border-t border-slate-200/60">
                    <div>
                        <span class="text-xl sm:text-2xl md:text-3xl font-bold text-slate-900">83,000+</span>
                        <p class="text-[10px] sm:text-xs text-slate-500 mt-0.5 sm:mt-1" data-i18n="metric_1_label">Verified
                            Drug Records</p>
                        <p class="text-[9px] text-slate-400 mt-0.5">WebMD + openFDA</p>
                    </div>
                    <div>
                        <span class="text-xl sm:text-2xl md:text-3xl font-bold text-slate-900">99.4%</span>
                        <p class="text-[10px] sm:text-xs text-slate-500 mt-0.5 sm:mt-1" data-i18n="metric_2_label">Triage
                            Accuracy Target</p>
                    </div>
                    <div>
                        <span class="text-xl sm:text-2xl md:text-3xl font-bold text-slate-900">250K+</span>
                        <p class="text-[10px] sm:text-xs text-slate-500 mt-0.5 sm:mt-1" data-i18n="metric_3_label">Clinical
                            Summaries Generated</p>
                    </div>
                    <div>
                        <span class="text-xl sm:text-2xl md:text-3xl font-bold text-slate-900">100% Free</span>
                        <p class="text-[10px] sm:text-xs text-slate-500 mt-0.5 sm:mt-1">Open Health Access</p>
                    </div>
                </div>
            </div>

        </div>

    </section>

    {{-- ══════════════════════ AI HISTORY MODAL ══════════════════════ --}}
    @include('partials.medicheck-history')

    {{-- ══════════════════════ CTA CALLOUT (INTERACTIVE MOUSE-TRACKING GRID) ══════════════════════ --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-20" id="section-cta">
        <div class="relative overflow-hidden rounded-3xl p-6 sm:p-12 md:p-16 text-center text-white"
            style="background: linear-gradient(135deg, #04181a 0%, #08383b 45%, #0f5256 80%, #156b70 100%); box-shadow: 0 24px 64px rgba(4, 24, 26, 0.35);"
            x-data="socialGrid()" @mousemove="handleMouseMove" x-ref="container">

            {{-- Interactive Grid Layer 1: Base grid --}}
            <div class="absolute inset-0 z-0 opacity-15 pointer-events-none">
                <svg class="w-full h-full text-teal-400">
                    <defs>
                        <pattern id="cta-grid-base" width="40" height="40" patternUnits="userSpaceOnUse" :x="gridOffsetX"
                            :y="gridOffsetY">
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
                        <pattern id="cta-grid-active" width="40" height="40" patternUnits="userSpaceOnUse" :x="gridOffsetX"
                            :y="gridOffsetY">
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
                <span
                    class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-[10px] sm:text-[11px] font-bold uppercase tracking-widest text-teal-300 bg-white/10 border border-white/15 backdrop-blur-md mb-4 sm:mb-6"
                    data-i18n="cta_badge">
                    Direct Health Navigation
                </span>

                <h2 class="display-roman text-2xl sm:text-3xl md:text-4xl lg:text-5xl leading-tight mb-3 sm:mb-4"
                    data-i18n="cta_title">
                    Healthcare Intelligence Platform
                </h2>

                <p class="text-xs sm:text-sm md:text-base text-teal-100/85 max-w-xl mx-auto mb-6 sm:mb-8 leading-relaxed"
                    data-i18n="cta_desc">
                    Search our directory of over 67,000 verified medicines, analyze interactions, and access instant symptom
                    screening.
                </p>

                {{-- Single Action Button --}}
                <div class="flex items-center justify-center">
                    <a href="{{ route('home') }}#ceksehat-section"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-3 px-7 sm:px-9 py-3.5 sm:py-4 rounded-full text-xs sm:text-sm font-bold bg-white text-teal-950 hover:bg-slate-50 transition-all duration-300 shadow-[0_12px_36px_rgba(0,0,0,0.25)] hover:shadow-[0_20px_48px_rgba(0,0,0,0.35)] active:scale-95 group">
                        <span data-i18n="cta_btn">Launch MediCore Screening</span>
                        <svg class="w-4 h-4 text-teal-700 group-hover:translate-x-1 transition-transform" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M14 5l7 7m0 0l-7 7m7-7H3" />
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