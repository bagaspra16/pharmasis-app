<?php

namespace App\Console\Commands;

use App\Services\Evidence\EvidenceRetriever;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class EvidenceCheckSources extends Command
{
    protected $signature = 'evidence:check-sources';

    protected $description = 'Check that every registered WHO fact sheet is reachable, readable and current';

    public function handle(EvidenceRetriever $retriever): int
    {
        $failed = 0;
        $rows = [];
        $limit = now()->subDays((int) config('evidence.stale_after_days.who_fact_sheet'))->toDateString();

        foreach (array_keys(config('evidence.who_fact_sheets')) as $slug) {
            Cache::forget("evidence_src_who_{$slug}");
            try {
                $doc = $retriever->whoFactSheet($slug);
                $state = !$doc['date'] ? 'NO DATE' : ($doc['date'] < $limit ? 'STALE' : 'ok');
                $rows[] = [$slug, $doc['date'] ?? '-', $state];
            } catch (\Throwable $e) {
                $failed++;
                $rows[] = [$slug, '-', 'FAILED: ' . substr($e->getMessage(), 0, 80)];
            }
        }

        $this->table(['WHO fact sheet', 'Updated', 'State'], $rows);

        return $failed ? self::FAILURE : self::SUCCESS;
    }
}
