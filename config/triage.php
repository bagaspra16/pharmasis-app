<?php

// Urgency assessment (triage). Guide: docs/TRIAGE.md
return [
    // Off by default: the rules have not been reviewed by a clinician.
    'enabled' => env('TRIAGE_ENABLED', false),

    // For local testing only. Never set this on a server that real people use.
    'allow_unreviewed' => env('TRIAGE_ALLOW_UNREVIEWED', false),

    'rules_path' => resource_path('triage/rules.json'),

    // A clinician fills this in after reviewing docs/TRIAGE-CLINICAL-REVIEW.md.
    // The hash ties the approval to that exact ruleset: any rule change voids it.
    'approval' => [
        'reviewer'      => null,
        'qualification' => null,
        'date'          => null,
        'ruleset_hash'  => null,
    ],

    // AI reads free text to find warning signs the patterns miss. It can only add
    // findings, so it can raise urgency but never lower it.
    'interpreter_model' => env('TRIAGE_MODEL', 'openai/gpt-oss-20b'),

    // Emergency numbers by country name. Anyone elsewhere sees the default text.
    // Verify before release.
    'emergency_numbers' => [
        'Indonesia'      => '119',
        'United States'  => '911',
        'Canada'         => '911',
        'United Kingdom' => '999',
        'Australia'      => '000',
        'Singapore'      => '995',
        'Malaysia'       => '999',
        'Japan'          => '119',
    ],
    'default_emergency_number' => '112',
];
