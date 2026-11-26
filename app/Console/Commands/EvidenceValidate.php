<?php

namespace App\Console\Commands;

use App\Services\Evidence\EvidenceService;
use App\Services\InteractionService;
use App\Services\MediCoreAiService;
use App\Services\ScreeningService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

/**
 * Runs the release test set against the live AI and source services and writes
 * every claim and quote to a file for human review. Uses API quota.
 */
class EvidenceValidate extends Command
{
    protected $signature = 'evidence:validate {--pause=30 : Seconds between cases, for rate limits}';

    protected $description = 'Measure citation coverage on the release test set';

    public function handle(MediCoreAiService $mediCore, ScreeningService $screening, EvidenceService $evidence): int
    {
        $cases = json_decode(file_get_contents(base_path('tests/Fixtures/evidence_release_set.json')), true);
        $results = [];

        foreach ($cases as $i => $case) {
            if ($i) {
                sleep((int) $this->option('pause'));
            }
            try {
                if ($case['type'] === 'route') {
                    $report = $mediCore->process($case['input'], $case['lang'])['evidence'] ?? null;
                } elseif ($case['type'] === 'conclude') {
                    $grounding = $evidence->ground($case['input']);
                    $conclusion = $screening->conclude($case['input'], [], $case['role'], $case['lang'], $grounding['block']);
                    $report = $evidence->assess($conclusion, $case['input'], $case['lang'], grounding: $grounding);
                } else {
                    $report = app(InteractionService::class)->check(
                        array_map(fn ($name) => "fda-{$name}", $case['drugs'])
                    )['evidence'] ?? null;
                }
                if (!$report) {
                    throw new \RuntimeException('No evidence report produced (emergency or error response).');
                }
                unset($report['labels']);
                $results[] = ['case' => $case, 'report' => $report];
                $this->line(sprintf(
                    '%s: %s, %d/%d supported, %d critical unverified',
                    $case['id'], $report['status'], $report['summary']['supported'],
                    $report['summary']['total'], $report['summary']['critical_unsupported']
                ));
            } catch (\Throwable $e) {
                $results[] = ['case' => $case, 'error' => substr($e->getMessage(), 0, 200)];
                $this->error("{$case['id']}: " . substr($e->getMessage(), 0, 200));
            }
        }

        $reports = array_column($results, 'report');
        $total = array_sum(array_map(fn ($r) => $r['summary']['total'], $reports));
        $urls = array_unique(array_merge([], ...array_map(fn ($r) => array_column($r['sources'], 'url'), $reports)));
        $reachable = 0;
        foreach ($urls as $url) {
            try {
                $reachable += Http::timeout(15)->get($url)->successful() ? 1 : 0;
            } catch (\Throwable) {
                // counted as unreachable
            }
        }

        $summary = [
            'run_at'               => now()->toIso8601String(),
            'cases'                => count($cases),
            'cases_with_report'    => count($reports),
            'cases_unavailable'    => count(array_filter($reports, fn ($r) => $r['status'] === 'unavailable')),
            'claims'               => $total,
            'claims_supported'     => array_sum(array_map(fn ($r) => $r['summary']['supported'], $reports)),
            'critical_unsupported' => array_sum(array_map(fn ($r) => $r['summary']['critical_unsupported'], $reports)),
            'reference_urls'       => count($urls),
            'reference_urls_ok'    => $reachable,
            'citation_accuracy'    => 'not measured: needs human review of each claim and quote below',
        ];
        $summary['citation_coverage'] = $total ? round($summary['claims_supported'] / $total, 3) : null;

        $path = base_path('docs/evidence-validation-' . now()->toDateString() . '.json');
        @mkdir(dirname($path), 0755, true);
        file_put_contents($path, json_encode(compact('summary', 'results'), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        $this->info(json_encode($summary, JSON_PRETTY_PRINT));
        $this->info("Written to {$path}");

        return self::SUCCESS;
    }
}
