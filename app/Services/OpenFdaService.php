<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenFdaService
{
    private const BASE_URL = 'https://api.fda.gov/drug/label.json';
    private const CACHE_TTL = 86400; // 24 hours in seconds

    /**
     * Search drugs via OpenFDA.
     * Tries brand_name first, then generic_name, then a combined loose search.
     */
    public function search(string $query, int $limit = 18): array
    {
        $term = trim($query);
        if (strlen($term) < 2) {
            return $this->popular();
        }

        $cacheKey = 'openfda_search_' . md5(strtolower($term) . '_' . $limit);

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($term, $limit) {
            $encoded = urlencode($term);
            $results = [];

            foreach ([
            "openfda.brand_name:{$encoded}",
            "openfda.generic_name:{$encoded}",
            "openfda.substance_name:{$encoded}",
            ] as $searchExpr) {
                try {
                    $response = Http::timeout(8)->get(self::BASE_URL, [
                        'search' => $searchExpr,
                        'limit' => $limit,
                    ]);
                    if ($response->successful() && !empty($response->json('results'))) {
                        $results = array_merge($results, $response->json('results', []));
                        if (count($results) >= $limit)
                            break;
                    }
                }
                catch (\Exception $e) {
                    Log::warning('OpenFDA search error: ' . $e->getMessage());
                }
            }

            $normalized = $this->normalizeResults(array_slice($results, 0, $limit));

            // ── Pre-cache each drug individually by slug so getBySlug() hits cache ──
            foreach ($normalized as $drug) {
                Cache::put('openfda_drug_' . md5($drug['slug']), $drug, self::CACHE_TTL);
            }

            return $normalized;
        });
    }

    /**
     * Get a single drug by slug.
     * First checks the individual slug cache (written during search/popular),
     * then falls back to multiple OpenFDA search strategies.
     */
    public function getBySlug(string $slug): ?array
    {
        // 1. Check if we already have it from a previous search
        $cached = Cache::get('openfda_drug_' . md5($slug));
        if ($cached)
            return $cached;

        // 2. Build name from slug and try multiple search strategies
        $name = trim(str_replace('-', ' ', $slug));
        $encoded = urlencode($name);

        $strategies = [
            "openfda.brand_name:{$encoded}",
            "openfda.generic_name:{$encoded}",
            "openfda.substance_name:{$encoded}",
            // Keyword search as final fallback
            $encoded,
        ];

        foreach ($strategies as $searchExpr) {
            try {
                $response = Http::timeout(10)->get(self::BASE_URL, [
                    'search' => $searchExpr,
                    'limit' => 5,
                ]);

                if ($response->successful()) {
                    $apiResults = $response->json('results', []);
                    foreach ($apiResults as $r) {
                        $drug = $this->normalizeSingle($r);
                        if (!$drug)
                            continue;

                        // Cache this result for future use
                        Cache::put('openfda_drug_' . md5($drug['slug']), $drug, self::CACHE_TTL);

                        // Return the closest match (prefer slug match)
                        if ($drug['slug'] === $slug)
                            return $drug;

                        // Accept first result if no exact slug match
                        $firstMatch = $firstMatch ?? $drug;
                    }
                }
            }
            catch (\Exception $e) {
                Log::warning("OpenFDA getBySlug strategy [{$searchExpr}] error: " . $e->getMessage());
            }
        }

        return $firstMatch ?? null;
    }

    /**
     * Instant search for navbar dropdown — top 8 results.
     */
    public function instant(string $query): array
    {
        $term = trim($query);
        if (strlen($term) < 2)
            return [];

        $cacheKey = 'openfda_instant_' . md5(strtolower($term));

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($term) {
            try {
                $response = Http::timeout(5)->get(self::BASE_URL, [
                    'search' => 'openfda.brand_name:' . urlencode($term),
                    'limit' => 8,
                ]);

                if ($response->successful()) {
                    $normalized = $this->normalizeResults($response->json('results', []));

                    // Pre-cache each for detail page
                    foreach ($normalized as $drug) {
                        Cache::put('openfda_drug_' . md5($drug['slug']), $drug, self::CACHE_TTL);
                    }

                    return collect($normalized)->map(fn($d) => [
                    'id' => $d['id'],
                    'name' => $d['name'],
                    'generic_name' => $d['generic_name'],
                    'drug_class' => $d['drug_class'],
                    'alpha_index' => $d['alpha_index'],
                    'slug' => $d['slug'],
                    'is_fda' => true,
                    ])->toArray();
                }
            }
            catch (\Exception $e) { /* silent for dropdown */
            }
            return [];
        });
    }

    /**
     * Popular drugs — used when DB is offline and no search query.
     */
    public function popular(): array
    {
        return Cache::remember('openfda_popular', self::CACHE_TTL, function () {
            $names = ['aspirin', 'ibuprofen', 'metformin', 'atorvastatin',
                'amoxicillin', 'lisinopril', 'omeprazole', 'metoprolol'];
            $results = [];

            foreach (array_slice($names, 0, 6) as $name) {
                try {
                    $response = Http::timeout(6)->get(self::BASE_URL, [
                        'search' => 'openfda.brand_name:' . urlencode($name),
                        'limit' => 2,
                    ]);
                    if ($response->successful()) {
                        $batch = $this->normalizeResults($response->json('results', []));
                        $results = array_merge($results, $batch);

                        // Pre-cache each for detail navigation
                        foreach ($batch as $drug) {
                            Cache::put('openfda_drug_' . md5($drug['slug']), $drug, self::CACHE_TTL);
                        }
                    }
                }
                catch (\Exception $e) { /* skip */
                }
            }

            return $results;
        });
    }

    // ─── Normalizers ──────────────────────────────────────────────────────────

    private function normalizeResults(array $results): array
    {
        return array_values(array_filter(array_map(
        fn($r) => $this->normalizeSingle($r), $results
        )));
    }

    private function normalizeSingle(array $r): ?array
    {
        $brandNames = $r['openfda']['brand_name'] ?? [];
        $genericNames = $r['openfda']['generic_name'] ?? [];
        $pharmClasses = $r['openfda']['pharm_class_epc']
            ?? $r['openfda']['pharm_class_cs']
            ?? [];

        $name = $brandNames[0] ?? $genericNames[0] ?? null;
        if (!$name)
            return null;

        $genericName = $genericNames[0] ?? null;
        $drugClass = $pharmClasses[0] ?? null;

        // Build a stable, clean slug from the brand name
        $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', trim($name)));
        $slug = trim($slug, '-');
        $alphaIndex = strtoupper(substr($name, 0, 2));

        $clean = fn(?array $arr) => $arr ? strip_tags(implode(' ', $arr)) : null;

        $data = [
            'id' => 'fda-' . md5($slug),
            'slug' => $slug,
            'name' => ucwords(strtolower($name)),
            'generic_name' => $genericName ? ucwords(strtolower($genericName)) : null,
            'drug_class' => $drugClass,
            'alpha_index' => $alphaIndex,
            'uses' => $clean($r['indications_and_usage'] ?? null),
            'warnings' => $clean($r['warnings'] ?? $r['boxed_warning'] ?? null),
            'before_taking' => $clean($r['contraindications'] ?? null),
            'dosage' => $clean($r['dosage_and_administration'] ?? null),
            'side_effects' => $clean($r['adverse_reactions'] ?? null),
            'interactions' => $clean($r['drug_interactions'] ?? null),
            'source' => 'OpenFDA',
            'translated' => true,
            'is_fda' => true,
            'url' => 'https://open.fda.gov/apis/drug/label/',
        ];

        return $data;
    }
}