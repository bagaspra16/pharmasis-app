<?php

namespace App\Services\Facilities;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

/**
 * Finds healthcare facilities from map and curated records. Nothing is written
 * by a model: a field that no source gives stays empty.
 */
class FacilityService
{
    public const CATEGORIES = ['care', 'all', 'emergency', 'hospital', 'puskesmas', 'clinic', 'pharmacy', 'doctor', 'dentist', 'laboratory'];

    public const STATUSES = ['officially_registered', 'verified', 'community', 'unverified'];

    private const PUSKESMAS = 'puskesmas|pusat kesehatan masyarakat';

    /** Map tags per category. */
    private const SELECTORS = [
        'hospital'   => ['["amenity"="hospital"]', '["healthcare"="hospital"]'],
        'puskesmas'  => ['["amenity"~"^(clinic|hospital|doctors)$"]["name"~"' . self::PUSKESMAS . '",i]'],
        'clinic'     => ['["amenity"="clinic"]', '["healthcare"~"^(clinic|centre)$"]'],
        'pharmacy'   => ['["amenity"="pharmacy"]', '["healthcare"="pharmacy"]'],
        'doctor'     => ['["amenity"="doctors"]', '["healthcare"="doctor"]'],
        'dentist'    => ['["amenity"="dentist"]'],
        'laboratory' => ['["healthcare"="laboratory"]'],
    ];

    /**
     * @return array{results:array,meta:array}
     * @throws \RuntimeException when no map data can be had at all
     */
    public function search(float $lat, float $lon, int $radiusKm, string $category): array
    {
        $area = $this->fetchArea($lat, $lon, $radiusKm, $category);

        $records = array_filter(array_map(fn ($e) => $this->fromMap($e), $area['elements']));
        $records = array_merge($this->curated(), array_values($records));

        $near = [];
        foreach ($records as $record) {
            $km = $this->distanceKm($lat, $lon, $record['lat'], $record['lon']);
            if ($km <= $radiusKm && $this->matches($record, $category)) {
                $near[] = ['distance_km' => round($km, 2)] + $record;
            }
        }
        $near = $this->deduplicate($near);

        // In an emergency, a listed emergency department comes before a closer hospital without one.
        usort($near, fn ($a, $b) => $category === 'emergency'
            ? [$a['emergency'] !== 'yes', $a['distance_km']] <=> [$b['emergency'] !== 'yes', $b['distance_km']]
            : $a['distance_km'] <=> $b['distance_km']);

        return [
            'results' => array_slice($near, 0, config('facilities.max_results')),
            'meta'    => [
                'total'        => count($near),
                'radius_km'    => $radiusKm,
                'category'     => $category,
                'retrieved_on' => date('Y-m-d', $area['at']),
                'old_copy'     => $area['old'],
                'truncated'    => count($area['elements']) >= config('facilities.fetch_limit'),
            ],
        ];
    }

    /**
     * @return array<int,array{label:string,lat:float,lon:float,country:?string,wide:bool}>
     * @throws \RuntimeException
     */
    public function geocode(string $query, string $lang): array
    {
        $key = 'geocode_' . md5(mb_strtolower($query) . '|' . $lang);
        if (($hit = Cache::get($key)) !== null) {
            return $hit;
        }
        // The public geocoder allows one request per second.
        if (!RateLimiter::attempt('geocoder', 1, fn () => true, 1)) {
            throw new \RuntimeException('busy');
        }

        $response = Http::timeout(8)->withHeaders(['User-Agent' => config('facilities.user_agent')])
            ->get(config('facilities.geocoder_url'), [
                'q' => $query, 'format' => 'jsonv2', 'limit' => 5, 'addressdetails' => 1,
                'accept-language' => $lang === 'id' ? 'id' : 'en',
            ]);
        if (!$response->successful() || !is_array($response->json())) {
            throw new \RuntimeException('Geocoder error ' . $response->status());
        }

        $places = [];
        foreach ($response->json() as $row) {
            if (!isset($row['lat'], $row['lon'], $row['display_name'])) {
                continue;
            }
            $box = array_map('floatval', $row['boundingbox'] ?? [0, 0, 0, 0]);
            $places[] = [
                'label'   => (string) $row['display_name'],
                'lat'     => round((float) $row['lat'], 4),
                'lon'     => round((float) $row['lon'], 4),
                'country' => $row['address']['country_code'] ?? null,
                // Distances from the middle of a large area are only rough.
                'wide'    => $this->distanceKm($box[0], $box[2], $box[1], $box[3]) > 2,
            ];
        }
        Cache::put($key, $places, now()->addDays(7));

        return $places;
    }

    public function distanceKm(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;

        return 6371.0088 * 2 * asin(min(1, sqrt($a)));
    }

    /**
     * The map service only receives a grid point about 1 km wide, never the
     * user's own position.
     */
    private function fetchArea(float $lat, float $lon, int $radiusKm, string $category): array
    {
        $lat = round($lat, 2);
        $lon = round($lon, 2);
        $type = $category === 'emergency' ? 'hospital' : $category;
        $key = "facility_area_v1_{$lat}_{$lon}_{$radiusKm}_{$type}";

        $cached = Cache::get($key);
        if ($cached && $cached['at'] > now()->getTimestamp() - 3600 * config('facilities.fresh_hours')) {
            return $cached + ['old' => false];
        }

        $around = '(around:' . ($radiusKm * 1000 + 800) . ",{$lat},{$lon});";
        // Puskesmas are already covered by the hospital, clinic and doctor tags.
        $selectors = match ($type) {
            'all'   => array_merge(...array_values(array_diff_key(self::SELECTORS, ['puskesmas' => 1]))),
            'care'  => array_merge(self::SELECTORS['hospital'], self::SELECTORS['clinic'], self::SELECTORS['doctor']),
            default => self::SELECTORS[$type],
        };
        $query = '[out:json][timeout:15];(' . implode('', array_map(fn ($s) => "nwr{$s}{$around}", $selectors))
            . ');out center meta ' . (int) config('facilities.fetch_limit') . ';';

        foreach (config('facilities.overpass_urls') as $url) {
            try {
                $response = Http::timeout(18)->connectTimeout(5)
                    ->withHeaders(['User-Agent' => config('facilities.user_agent')])
                    ->asForm()->post($url, ['data' => $query]);
                // A "remark" means the server gave up part way: the list would be incomplete.
                if ($response->successful() && is_array($response->json('elements')) && !$response->json('remark')) {
                    $area = ['at' => now()->getTimestamp(), 'elements' => $response->json('elements')];
                    Cache::put($key, $area, now()->addDays(config('facilities.keep_days')));

                    return $area + ['old' => false];
                }
                Log::warning('Facility map service refused', ['status' => $response->status()]);
            } catch (\Throwable $e) {
                // The message is not logged: it can contain the searched area.
                Log::warning('Facility map service unreachable', ['type' => get_class($e)]);
            }
        }

        if ($cached) {
            return $cached + ['old' => true];
        }
        throw new \RuntimeException('Facility data unavailable');
    }

    private function fromMap(array $element): ?array
    {
        $tags = $element['tags'] ?? [];
        $lat = $element['lat'] ?? $element['center']['lat'] ?? null;
        $lon = $element['lon'] ?? $element['center']['lon'] ?? null;
        $name = trim((string) ($tags['name'] ?? $tags['official_name'] ?? ''));
        // A nameless point cannot be found or checked by the user.
        if ($name === '' || $lat === null || $lon === null || preg_match('/\b(hewan|veterinary|vet clinic|pet ?care)\b/iu', $name)) {
            return null;
        }
        $category = $this->category($name, $tags);
        if (!$category) {
            return null;
        }

        $edited = substr((string) ($element['timestamp'] ?? ''), 0, 10) ?: null;
        $address = $tags['addr:full'] ?? implode(', ', array_filter([
            trim(($tags['addr:street'] ?? '') . ' ' . ($tags['addr:housenumber'] ?? '')),
            $tags['addr:subdistrict'] ?? $tags['addr:suburb'] ?? null,
            $tags['addr:city'] ?? null,
            $tags['addr:postcode'] ?? null,
        ]));
        $specialities = array_filter(explode(';', (string) ($tags['healthcare:speciality'] ?? '')));
        $emergency = $tags['emergency'] ?? null;

        return [
            'id'            => "osm-{$element['type']}-{$element['id']}",
            'name'          => $name,
            'category'      => $category,
            'lat'           => (float) $lat,
            'lon'           => (float) $lon,
            'address'       => trim($address) ?: null,
            'phone'         => $this->phone($tags['phone'] ?? $tags['contact:phone'] ?? null),
            'website'       => $this->url($tags['website'] ?? $tags['contact:website'] ?? null),
            'opening_hours' => mb_substr(trim((string) ($tags['opening_hours'] ?? '')), 0, 120) ?: null,
            'services'      => array_map(fn ($s) => str_replace('_', ' ', trim($s)), array_slice(array_values($specialities), 0, 8)),
            'emergency'     => in_array($emergency, ['yes', 'no'], true) ? $emergency : null,
            // Anyone can edit the map, so it is never more than community-submitted.
            'status'        => 'community',
            'source_name'   => 'OpenStreetMap',
            'source_url'    => "https://www.openstreetmap.org/{$element['type']}/{$element['id']}",
            'checked_on'    => null,
            'updated_on'    => $edited,
            'stale'         => !$edited || strtotime($edited) < time() - 86400 * config('facilities.stale_days'),
        ];
    }

    private function category(string $name, array $tags): ?string
    {
        $amenity = $tags['amenity'] ?? null;
        $care = $tags['healthcare'] ?? null;

        return match (true) {
            preg_match('/' . self::PUSKESMAS . '/iu', $name) === 1 && $amenity !== 'pharmacy' => 'puskesmas',
            $amenity === 'hospital' || $care === 'hospital' => 'hospital',
            $amenity === 'pharmacy' || $care === 'pharmacy' => 'pharmacy',
            $amenity === 'dentist' => 'dentist',
            $care === 'laboratory' => 'laboratory',
            $amenity === 'clinic' || in_array($care, ['clinic', 'centre'], true) => 'clinic',
            $amenity === 'doctors' || $care === 'doctor' => 'doctor',
            default => null,
        };
    }

    private function matches(array $record, string $category): bool
    {
        return match ($category) {
            'all'       => true,
            'care'      => in_array($record['category'], ['hospital', 'puskesmas', 'clinic', 'doctor'], true),
            'emergency' => $record['category'] === 'hospital' && $record['emergency'] !== 'no',
            default     => $record['category'] === $category,
        };
    }

    /** @return array<int,array<string,mixed>> Operator records, with the status their evidence allows. */
    private function curated(): array
    {
        $path = config('facilities.curated_path');
        $rows = is_file($path) ? (json_decode(file_get_contents($path), true)['records'] ?? []) : [];
        $records = [];

        foreach ($rows as $i => $row) {
            if (empty($row['name']) || !isset($row['lat'], $row['lon']) || !isset(self::SELECTORS[$row['category'] ?? ''])) {
                Log::warning('Curated facility skipped: name, category and position are required', ['index' => $i]);
                continue;
            }
            $source = $this->url($row['source_url'] ?? null);
            $checked = strtotime((string) ($row['checked_on'] ?? '')) ?: null;
            $status = in_array($row['status'] ?? null, self::STATUSES, true) ? $row['status'] : 'unverified';
            $stale = !$checked || $checked < time() - 86400 * config('facilities.verification_days');
            // No claim of verification without a source and a recent check.
            if (in_array($status, ['officially_registered', 'verified'], true) && (!$source || $stale)) {
                $status = 'unverified';
            }

            $records[] = [
                'id'            => "curated-{$i}",
                'name'          => trim($row['name']),
                'category'      => $row['category'],
                'lat'           => (float) $row['lat'],
                'lon'           => (float) $row['lon'],
                'address'       => $row['address'] ?? null,
                'phone'         => $this->phone($row['phone'] ?? null),
                'website'       => $this->url($row['website'] ?? null),
                'opening_hours' => $row['opening_hours'] ?? null,
                'services'      => array_values((array) ($row['services'] ?? [])),
                'emergency'     => in_array($row['emergency'] ?? null, ['yes', 'no'], true) ? $row['emergency'] : null,
                'status'        => $status,
                'source_name'   => $row['source_name'] ?? null,
                'source_url'    => $source,
                'checked_on'    => $checked ? date('Y-m-d', $checked) : null,
                'updated_on'    => $checked ? date('Y-m-d', $checked) : null,
                'stale'         => $stale,
            ];
        }

        return $records;
    }

    /** The same place is often mapped twice. Curated records win, then the fuller one. */
    private function deduplicate(array $records): array
    {
        $score = fn (array $r) => [
            str_starts_with($r['id'], 'curated-'),
            count(array_filter([$r['address'], $r['phone'], $r['website'], $r['opening_hours'], $r['emergency'], $r['services']])),
        ];
        usort($records, fn ($a, $b) => $score($b) <=> $score($a));

        $kept = [];
        $seen = [];
        foreach ($records as $record) {
            $key = preg_replace(['/\brs\b/u', '/[^\p{L}\p{N}]+/u'], ['rumah sakit', ''], mb_strtolower($record['name']));
            foreach ($seen[$key] ?? [] as $other) {
                // Chains repeat a name, so only entries at nearly the same spot are merged.
                if ($this->distanceKm($record['lat'], $record['lon'], $other['lat'], $other['lon']) < 0.15) {
                    continue 2;
                }
            }
            $seen[$key][] = $record;
            $kept[] = $record;
        }

        return $kept;
    }

    private function phone(mixed $value): ?string
    {
        $first = trim(explode(';', (string) $value)[0]);

        return preg_match('/^\+?[\d\s().-]{3,25}$/', $first) && preg_match_all('/\d/', $first) >= 3 ? $first : null;
    }

    /** Only web addresses: a record must not be able to run script in the page. */
    private function url(mixed $value): ?string
    {
        $value = trim((string) $value);
        if ($value !== '' && !str_contains($value, '://')) {
            $value = preg_match('/^[\w-]+(\.[\w-]+)+(\/\S*)?$/', $value) ? "https://{$value}" : '';
        }

        return preg_match('~^https?://[^\s"\'<>]+$~i', $value) ? $value : null;
    }
}
