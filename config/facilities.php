<?php

// Nearby healthcare facilities. Guide: docs/FACILITIES.md
return [
    'enabled' => env('FACILITIES_ENABLED', true),

    // Public community servers with usage limits. Use your own or a paid service before real traffic.
    'overpass_urls' => array_filter(explode(',', env('FACILITIES_OVERPASS_URLS',
        'https://overpass-api.de/api/interpreter,https://overpass.kumi.systems/api/interpreter'))),
    'geocoder_url' => env('FACILITIES_GEOCODER_URL', 'https://nominatim.openstreetmap.org/search'),
    // Sent to the map services. Add a contact address here if their policy requires one.
    'user_agent' => env('FACILITIES_USER_AGENT', 'healthcare-facility-finder/1.0'),

    'radius_km'   => [2, 5, 10, 20],
    'default_radius_km' => 5,
    'max_results' => 30,
    // Largest number of map elements read for one area.
    'fetch_limit' => 1500,
    'fresh_hours' => 24,
    // An older copy is still shown, with its date, if the map service is down.
    'keep_days'   => 14,
    // A map entry not edited for this long is flagged as possibly outdated.
    'stale_days'  => 730,
    // A "verified" curated record older than this drops back to unverified.
    'verification_days' => 365,

    // Records checked by this project's operators. See docs/FACILITIES.md for the format.
    'curated_path' => resource_path('facilities/curated.json'),

    // Country code from the geocoder => name used in triage.emergency_numbers.
    'countries' => [
        'id' => 'Indonesia', 'us' => 'United States', 'ca' => 'Canada', 'gb' => 'United Kingdom',
        'au' => 'Australia', 'sg' => 'Singapore', 'my' => 'Malaysia', 'jp' => 'Japan',
    ],
];
