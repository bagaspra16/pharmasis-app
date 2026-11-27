<?php

// Risk-based protection against automated abuse. Guide: docs/BOTGUARD.md
// All numbers are developer estimates, not tuned on real traffic.
return [
    // off | monitor (log what would happen, block nothing) | enforce
    'mode' => env('BOTGUARD_MODE', 'monitor'),

    /*
     | Endpoint classes. "client" is one address and browser; "address" is everyone
     | behind one address, kept far higher because mobile networks share addresses.
     | Limits are [requests, seconds].
     */
    // The hourly ai limit allows a shared kiosk or clinic tablet.
    'classes' => [
        'ai' => [
            'paths'   => ['medicheck/route', 'medicheck/screen', 'medicheck/conclude', 'medicheck/analyze', 'medicheck/transcribe', 'api/v1/ai/*', 'api/v1/interactions/check'],
            'client'  => [[20, 60], [300, 3600]],
            'address' => [[100, 60], [1000, 3600]],
            'burst'   => [8, 10],
        ],
        'lookup' => [
            'paths'   => ['medicheck/next', 'medicheck/history', 'medicheck/history/*', 'facilities/search', 'facilities/geocode', 'api/v1/search', 'api/v1/search/*'],
            'client'  => [[120, 60], [1500, 3600]],
            'address' => [[600, 60], [8000, 3600]],
            'burst'   => [40, 10],
        ],
        // Not used by the public. Limited even in monitor mode.
        'sensitive' => [
            'paths'   => ['sync-env'],
            'client'  => [[3, 3600]],
            'address' => [[5, 3600]],
            'burst'   => [3, 10],
            'always'  => true,
        ],
        'verify' => [
            'paths'   => ['bot/verify'],
            'client'  => [[20, 60]],
            'address' => [[100, 60]],
            'burst'   => [10, 10],
            'no_challenge' => true,
        ],
    ],

    // Pages nobody using this site would request.
    'probe_paths' => ['wp-login.php', 'wp-admin*', 'xmlrpc.php', '.env', '.git/*', 'phpmyadmin*', 'vendor/phpunit/*'],

    'weights' => [
        'no_user_agent'     => 40,
        'automation_agent'  => 50,
        'no_language'       => 15,
        'no_page_context'   => 20,
        'no_session_cookie' => 15,
        'repeated_payload'  => 25,
        'burst'             => 30,
        'high_volume'       => 20,
        'probed'            => 50,
        'failed_checks'     => 30,
    ],
    'thresholds' => ['monitor' => 30, 'challenge' => 50, 'restrict' => 80],

    'automation_agents' => '/\b(curl|wget|python|requests|aiohttp|httpx|scrapy|go-http-client|java|okhttp|libwww|node-fetch|axios|headlesschrome|phantomjs|puppeteer|playwright|selenium)\b/i',

    // Browser check: find a number whose SHA-256 starts with this many zero bits.
    'difficulty'        => 14,
    'strict_difficulty' => 17,
    'challenge_seconds' => 120,
    // Requests a passed check is good for, and how long it lasts.
    'pass'              => ['work' => 30, 'question' => 5],
    'pass_seconds'      => 900,

    /*
     | A message containing emergency wording is never challenged, up to this
     | allowance per client. Paths whose text is checked: the symptom endpoints.
     */
    'emergency_paths'     => ['medicheck/route', 'medicheck/screen', 'medicheck/conclude', 'medicheck/next'],
    'emergency_allowance' => [10, 600],
];
