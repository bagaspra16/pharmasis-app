<?php

// Medical evidence settings. Maintenance guide: docs/EVIDENCE.md
return [
    'enabled' => env('EVIDENCE_ENABLED', true),

    'model_terms'  => env('EVIDENCE_TERMS_MODEL', 'openai/gpt-oss-20b'),
    'model_verify' => env('EVIDENCE_VERIFY_MODEL', 'openai/gpt-oss-120b'),

    'max_claims'    => 20,
    'max_sources'   => 5,
    'passage_chars' => 1600,
    'cache_hours'   => 24,

    // Age in days after which a source is flagged as possibly outdated.
    // Proposed internal values, not an established standard.
    'stale_after_days' => [
        'drug_label'        => 1825,
        'health_topic'      => 1095,
        'who_fact_sheet'    => 1095,
        'systematic_review' => 1825,
    ],

    // 1 = government authority, 2 = international health organisation,
    // 3 = peer-reviewed systematic review or meta-analysis.
    'tiers' => [
        'drug_label'        => 1,
        'health_topic'      => 1,
        'who_fact_sheet'    => 2,
        'systematic_review' => 3,
    ],

    // International (INN) names that US drug labels list differently.
    'drug_synonyms' => [
        'paracetamol'    => 'acetaminophen',
        'parasetamol'    => 'acetaminophen',
        'salbutamol'     => 'albuterol',
        'adrenaline'     => 'epinephrine',
        'glibenclamide'  => 'glyburide',
        'amoxycillin'    => 'amoxicillin',
        'asam mefenamat' => 'mefenamic acid',
    ],

    // WHO fact sheets: slug => lowercase English phrases that select it.
    // Page text and date are read live; nothing is transcribed here.
    // Run `php artisan evidence:check-sources` after any change.
    'who_fact_sheets' => [
        'diabetes'                 => ['diabetes', 'blood sugar', 'hyperglycemia', 'blood glucose'],
        'hypertension'             => ['hypertension', 'high blood pressure'],
        'dengue-and-severe-dengue' => ['dengue'],
        'tuberculosis'             => ['tuberculosis'],
        'asthma'                   => ['asthma'],
        'malaria'                  => ['malaria'],
        'antimicrobial-resistance' => ['antimicrobial resistance', 'antibiotic resistance', 'antibiotic'],
        'diarrhoeal-disease'       => ['diarrhea', 'diarrhoea'],
        'headache-disorders'       => ['headache', 'migraine'],
        'depression'               => ['depression'],
        'influenza-(seasonal)'     => ['influenza', 'flu'],
        'obesity-and-overweight'   => ['obesity', 'overweight'],
        'anaemia'                  => ['anemia', 'anaemia'],
    ],
];
