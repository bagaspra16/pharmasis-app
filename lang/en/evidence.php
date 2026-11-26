<?php

return [
    'title' => 'Evidence and references',
    'ai_summary' => 'AI-generated summary',
    'status' => [
        'verified' => 'A source passage was matched to every statement. This is not a medical verification: read the source.',
        'partial' => 'A source passage was matched to only some statements. Treat the others as unverified.',
        'conflicting' => 'Some statements conflict with a source. Confirm with a doctor or pharmacist.',
        'insufficient' => 'Not enough evidence was found. Treat this answer as unverified.',
        'unavailable' => 'Sources could not be checked. Treat this answer as unverified.',
    ],
    'claim' => [
        'supported' => 'Matching passage found',
        'unsupported' => 'Not verified',
        'conflicting' => 'Sources conflict',
        'contradicted' => 'Contradicted by source',
        'unchecked' => 'Not checked',
    ],
    'level' => [
        'established' => 'From an official or international source',
        'research' => 'Research review, may not be settled',
        'outdated' => 'Source may be outdated',
    ],
    'tier' => [1 => 'Government authority', 2 => 'International health organisation', 3 => 'Systematic review'],
    'date_kind' => ['effective' => 'Effective', 'updated' => 'Updated', 'published' => 'Published'],
    'no_date' => 'Date not stated',
    'stale' => 'May be outdated',
    'critical' => 'Safety-related',
    'against' => 'Source says otherwise',
    'count' => 'statements have a matching source passage',
    'critical_warning' => 'safety-related statements are not verified. Confirm them with a doctor or pharmacist before acting.',
    'show' => 'Show each statement',
    'hide' => 'Hide statements',
    'references' => 'References',
    'checked_on' => 'Checked on',
    'method' => 'Automated check against excerpts of the sources. It can be wrong; open the source to confirm.',
];
