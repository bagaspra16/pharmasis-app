<?php

namespace App\Console\Commands;

use App\Services\Facilities\FacilityService;
use Illuminate\Console\Command;

class FacilitiesValidate extends Command
{
    protected $signature = 'facilities:validate {--pause=8 : Seconds between map requests}';

    protected $description = 'Measure live facility searches at public landmarks (uses the public map services)';

    /** Landmark positions are approximate, entered by hand. */
    private const PLACES = [
        'Monumen Nasional, Jakarta'      => [-6.1754, 106.8272],
        'Gedung Sate, Bandung'           => [-6.9025, 107.6188],
        'Tugu Pahlawan, Surabaya'        => [-7.2459, 112.7378],
        'Tugu Yogyakarta'                => [-7.7829, 110.3671],
        'Bandar Udara Komodo, Labuan Bajo' => [-8.4866, 119.8890],
    ];

    public function handle(FacilityService $facilities): int
    {
        // A private empty cache, so the first search of each area is truly cold.
        config(['cache.default' => 'array']);
        $pause = (int) $this->option('pause');

        $geocoding = [];
        foreach (self::PLACES as $name => [$lat, $lon]) {
            sleep(2);
            try {
                $places = $facilities->geocode($name, 'id');
                $off = $places ? round($facilities->distanceKm($lat, $lon, $places[0]['lat'], $places[0]['lon']), 2) : null;
                $geocoding[$name] = ['matches' => count($places), 'km_from_expected' => $off];
            } catch (\Throwable $e) {
                $geocoding[$name] = ['error' => mb_substr($e->getMessage(), 0, 80)];
            }
            $this->line("geocode {$name}: " . json_encode($geocoding[$name]));
        }

        $searches = [];
        $all = [];
        foreach (self::PLACES as $name => [$lat, $lon]) {
            foreach (['care', 'emergency', 'pharmacy'] as $category) {
                sleep($pause);
                $row = ['place' => $name, 'category' => $category];
                try {
                    $start = microtime(true);
                    $found = $facilities->search($lat, $lon, 5, $category);
                    $row['first_ms'] = (int) round((microtime(true) - $start) * 1000);
                    $start = microtime(true);
                    $facilities->search($lat, $lon, 5, $category);
                    $row['repeat_ms'] = (int) round((microtime(true) - $start) * 1000);
                    $row += ['total' => $found['meta']['total'], 'shown' => count($found['results']), 'truncated' => $found['meta']['truncated']];
                    if ($category === 'emergency') {
                        $row['listed_emergency'] = count(array_filter($found['results'], fn ($r) => $r['emergency'] === 'yes'));
                    }
                    foreach ($found['results'] as $record) {
                        $all[$record['id']] = $record;
                    }
                } catch (\Throwable $e) {
                    $row['error'] = mb_substr($e->getMessage(), 0, 80);
                }
                $searches[] = $row;
                $this->line(json_encode($row, JSON_UNESCAPED_UNICODE));
            }
        }

        $share = fn (callable $has) => $all ? round(100 * count(array_filter($all, $has)) / count($all), 1) : null;
        $times = array_values(array_filter(array_column($searches, 'first_ms'), 'is_int'));
        sort($times);
        $summary = [
            'distinct_records'    => count($all),
            'percent_with'        => [
                'address'       => $share(fn ($r) => $r['address'] !== null),
                'phone'         => $share(fn ($r) => $r['phone'] !== null),
                'opening_hours' => $share(fn ($r) => $r['opening_hours'] !== null),
                'website'       => $share(fn ($r) => $r['website'] !== null),
                'services'      => $share(fn ($r) => $r['services'] !== []),
                'status_label'  => $share(fn ($r) => in_array($r['status'], FacilityService::STATUSES, true)),
            ],
            'percent_stale'       => $share(fn ($r) => $r['stale']),
            'searches_ok'         => count($times) . '/' . count($searches),
            'first_search_ms'     => $times ? [
                'median' => $times[intdiv(count($times), 2)],
                'p95'    => $times[(int) ceil(0.95 * count($times)) - 1],
                'max'    => end($times),
            ] : null,
            'repeat_search_ms_max' => max(array_column($searches, 'repeat_ms') ?: [null]),
        ];

        $dir = base_path('docs/facility-runs');
        is_dir($dir) || mkdir($dir);
        $file = "{$dir}/" . now()->format('Y-m-d-His') . '.json';
        file_put_contents($file, json_encode(compact('summary', 'geocoding', 'searches'), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        $this->info(json_encode($summary, JSON_PRETTY_PRINT));
        $this->info('Saved ' . $file);

        return self::SUCCESS;
    }
}
