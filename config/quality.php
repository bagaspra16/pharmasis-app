<?php

// Response quality checks. Guide: docs/QUALITY.md
// The word patterns were written by developers and have not been reviewed by a clinician.
return [
    'enabled' => env('QUALITY_ENABLED', false),

    // One extra model call that looks for contradictions and off-topic answers.
    'model_review' => env('QUALITY_MODEL_REVIEW', true),
    'review_model' => env('QUALITY_REVIEW_MODEL', 'openai/gpt-oss-120b'),

    // Promises about the outcome. Never appropriate.
    'reassurance' => [
        '(nothing|no need) to (worry|be concerned)',
        "(do not|don'?t) (need to )?worry",
        'no cause for (concern|alarm)',
        '(completely|totally|perfectly|100%) (safe|harmless|normal|fine)',
        '(you|you\'ll|you will) (are|be) (fine|okay|ok|all right)\b',
        '(no need|not necessary) to (see|visit|consult) (a |your |the )?(doctor|clinic)',
        '(tidak|gak|nggak) (perlu|usah) (khawatir|cemas|takut)',
        'jangan (khawatir|cemas|takut)',
        '(tidak|gak|nggak) (perlu|usah) (ke|periksa ke) dokter',
        'pasti (akan )?(sembuh|membaik|aman|hilang)',
        '(sangat|sepenuhnya|100%) aman',
    ],

    // Statements of certainty this tool cannot make.
    'certainty' => [
        'you (definitely|certainly|clearly|undoubtedly) (have|are suffering)',
        'this is (definitely|certainly|clearly|undoubtedly)',
        '(will|is going to) (definitely|certainly|surely)',
        'guaranteed? (to )?(cure|work|relief|recovery)',
        '(i|we) (can )?(confirm|diagnose)',
        '(anda|kamu) (pasti|jelas) (mengalami|menderita|terkena)',
        '(ini|itu) (pasti|jelas) ',
        'dijamin (sembuh|aman|ampuh)',
    ],

    // Advice to wait. Flagged only, never removed. Checked when the assessment says Urgent or Unable to assess.
    'delay' => [
        '(wait|give it) (and see|a few days|\d+)',
        '\bif\b.{0,60}\b(persists?|lasts?|continues?|does ?n[o\']t (improve|get better)|not improv\w+|more than \d+)',
        '(usually|often|will) (go(es)? away|resolves?|gets? better|improves?|passes?) (on its own|by itself)',
        '(tunggu|pantau) (dulu )?(beberapa|\d+) (hari|minggu)',
        '\b(jika|kalau|bila|apabila)\b.{0,60}\b(berlanjut|berlangsung|menetap|(tidak|belum) (membaik|reda)|lebih dari \d+)',
        '(biasanya |akan )(sembuh|hilang|membaik) (dengan )?sendiri',
    ],

    // A promise in a sentence containing these is flagged, not removed: it may also carry a warning.
    'keep_words' => '\b(if|unless|but|however|doctor|seek|call|go to|hospital|clinic|emergency|immediately|jika|kalau|bila|kecuali|tapi|tetapi|namun|dokter|hubungi|periksa\w*|rumah sakit|darurat|segera|igd|ugd)\b',
    // The sentence is already denying the promise: leave it alone.
    'negations' => "(cannot|can ?not|can't|does not mean|doesn't mean|not possible to|tidak bisa|belum bisa|bukan berarti)",

    'medicine' => '\d ?(mg|mcg|ml)\b|\b(paracetamol|acetaminophen|ibuprofen|aspirin|antibiotic\w*|antihistamine\w*|medicines?|medication\w*|tablets?|parasetamol|antibiotik|obat\w*)\b',
];
