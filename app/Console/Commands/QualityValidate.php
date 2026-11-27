<?php

namespace App\Console\Commands;

use App\Services\Quality\ResponseQuality;
use App\Services\ScreeningService;
use App\Services\Triage\TriageService;
use Illuminate\Console\Command;

class QualityValidate extends Command
{
    protected $signature = 'quality:validate {--live : Generate real answers (uses the AI quota)} {--pause=20 : Seconds between live cases}';

    protected $description = 'Measure the response wording checks and build the expert review sheet';

    public function handle(ResponseQuality $quality, ScreeningService $screening, TriageService $triage): int
    {
        $fixture = json_decode(file_get_contents(base_path('tests/Fixtures/quality_cases.json')), true);

        $right = count(array_filter($fixture['wording'], fn ($c) => ($quality->verdict($c['text'], $c['cautious']) ?? 'keep') === $c['expect']));
        $caught = count(array_filter($fixture['paraphrases'], fn ($c) => $quality->verdict($c['text'], $c['cautious']) !== null));
        $this->info("Wording cases as expected: {$right}/" . count($fixture['wording']));
        $this->info("Unsafe paraphrases caught: {$caught}/" . count($fixture['paraphrases']));

        if (!$this->option('live')) {
            return self::SUCCESS;
        }

        config(['quality.model_review' => true]);
        $flagged = ['unsafe' => null, 'safe' => null];
        $sets = [
            'unsafe' => $fixture['paraphrases'],
            'safe' => array_values(array_filter($fixture['wording'], fn ($c) => $c['expect'] === 'keep')),
        ];
        foreach ($sets as $name => $cases) {
            $hits = 0;
            // Advice-to-wait cases are judged with the stricter instruction.
            foreach ([false, true] as $cautious) {
                $texts = array_column(array_filter($cases, fn ($c) => $c['cautious'] === $cautious), 'text');
                $review = $quality->review('feeling unwell', implode("\n", $texts), $cautious);
                if (!is_array($review)) {
                    continue 2;
                }
                $hits += count(array_filter($texts, fn ($t) => (bool) array_filter($review['unsafe'], fn ($u) => str_contains($t, $u))));
            }
            $flagged[$name] = [$hits, count($cases)];
            $this->info("Model flagged {$name} sentences: {$hits}/" . count($cases));
        }

        // Urgency rules are switched on for this run only.
        config(['quality.enabled' => true, 'triage.enabled' => true, 'triage.allow_unreviewed' => true]);

        $runs = [];
        foreach ($fixture['live'] as $i => $case) {
            if ($i) {
                sleep((int) $this->option('pause'));
            }
            $qa = array_map(fn ($p) => ['question' => $p[0], 'answer' => $p[1]], $case['qa']);
            try {
                $assessment = $triage->assess($case['complaint'], $qa, [], $case['lang']);
                if ($assessment['category'] === 'emergency') {
                    $runs[] = ['id' => $case['id'], 'category' => 'emergency'];
                    continue;
                }
                // Without the writing rules first, to see what the rules change.
                config(['quality.enabled' => false]);
                $before = $screening->conclude($case['complaint'], $qa, 'patient', $case['lang']);
                config(['quality.enabled' => true]);
                $draft = $screening->conclude($case['complaint'], $qa, 'patient', $case['lang'], $quality->rules($assessment));
                if (isset($before['_error']) || isset($draft['_error'])) {
                    throw new \RuntimeException('Unreadable model output.');
                }
                if ($assessment['category'] !== 'routine') {
                    unset($draft['reassurance']);
                }
                [$final, $report] = $quality->apply($draft, $case['complaint'], $qa, $case['lang'], $assessment);
            } catch (\Throwable $e) {
                $this->warn("{$case['id']}: " . mb_substr($e->getMessage(), 0, 120));
                $runs[] = ['id' => $case['id'], 'error' => true];
                continue;
            }

            $runs[] = [
                'id' => $case['id'], 'complaint' => $case['complaint'], 'category' => $assessment['category'],
                'before' => $this->measure($before, $quality, $assessment['category']),
                'after' => $this->measure($final, $quality, $assessment['category']),
                'removed' => $report['removed'], 'warned' => $report['warned'],
                'model_review_ran' => $report['reviewed_by_model'],
                'notices' => $report['notices'], 'limits' => $report['limits'], 'answer' => $final,
            ];
            $this->line("{$case['id']}: {$assessment['category']}");
        }

        $dir = base_path('docs/quality-runs');
        is_dir($dir) || mkdir($dir);
        $stamp = now()->format('Y-m-d-His');
        file_put_contents("{$dir}/{$stamp}.json", json_encode([
            'wording' => [$right, count($fixture['wording'])], 'paraphrases' => [$caught, count($fixture['paraphrases'])], 'model_flags' => $flagged, 'live' => $runs,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        file_put_contents(base_path('docs/QUALITY-REVIEW-SHEET.md'), $this->sheet($runs, $stamp));
        $this->info("Saved docs/quality-runs/{$stamp}.json and docs/QUALITY-REVIEW-SHEET.md");

        return self::SUCCESS;
    }

    /** Crude readability and unsafe-wording counts. They do not measure comprehension. */
    private function measure(array $answer, ResponseQuality $quality, string $category): array
    {
        unset($answer['mode'], $answer['quality']);
        $text = [];
        array_walk_recursive($answer, function ($value) use (&$text) {
            if (is_string($value)) {
                $text[] = $value;
            }
        });
        $sentences = preg_split('/(?<=[.!?])\s+|\n+/u', implode("\n", $text), -1, PREG_SPLIT_NO_EMPTY);
        $lengths = array_map(fn ($s) => count(preg_split('/\s+/u', trim($s))), $sentences);
        $cautious = in_array($category, ['urgent', 'unable_to_assess'], true);

        return [
            'words' => array_sum($lengths),
            'words_per_sentence' => $lengths ? round(array_sum($lengths) / count($lengths), 1) : 0,
            'sentences_over_25_words' => count(array_filter($lengths, fn ($n) => $n > 25)),
            'flagged_sentences' => count(array_filter($sentences, fn ($s) => $quality->verdict($s, $cautious) !== null)),
        ];
    }

    private function sheet(array $runs, string $stamp): string
    {
        $md = "# Response review sheet\n\nRun {$stamp}. Made-up cases. Urgency rules and answers are not clinician reviewed.\n\n"
            . "Reviewer name and qualification: ______  Date: ______\n\n"
            . "Rate each answer Yes / No, with a comment for every No.\n";

        foreach ($runs as $run) {
            if (!isset($run['answer'])) {
                continue;
            }
            $md .= "\n## {$run['id']}\n\n**Concern:** {$run['complaint']}\n\n**Urgency shown:** {$run['category']}\n\n";
            foreach ($run['answer'] as $field => $value) {
                if ($field === 'mode') {
                    continue;
                }
                $value = is_array($value) ? implode(' | ', array_filter($value, 'is_string')) : $value;
                $md .= "- **{$field}:** {$value}\n";
            }
            foreach (array_merge($run['notices'], $run['limits']) as $note) {
                $md .= "- _shown to user:_ {$note}\n";
            }
            $md .= "\n| Question | Yes/No | Comment |\n| --- | --- | --- |\n"
                . "| Addresses the concern | | |\n| Medically accurate | | |\n| Supported by evidence | | |\n"
                . "| Uncertainty and limits clear | | |\n| Consistent with the urgency shown | | |\n"
                . "| A non-medical person would understand it | | |\n| Next step is clear | | |\n| Anything unsafe | | |\n";
        }

        return $md;
    }
}
