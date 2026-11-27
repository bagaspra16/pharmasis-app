<?php

namespace App\Console\Commands;

use App\Services\Triage\TriageEngine;
use App\Services\Triage\TriageService;
use Illuminate\Console\Command;

/** Runs the triage scenarios and records sensitivity and false reassurance. */
class TriageValidate extends Command
{
    protected $signature = 'triage:validate
        {file=tests/Fixtures/triage_scenarios.json}
        {--ai : Add the AI interpreter (uses API quota)}
        {--pause=2 : Seconds between AI calls}';

    protected $description = 'Measure the triage rules against a scenario file';

    private const AGE = ['adult' => '18_to_64'];

    private const DURATION = ['under_3_days' => 'under_3', 'over_7_days' => 'over_7'];

    public function handle(TriageEngine $engine, TriageService $triage): int
    {
        $cases = json_decode(file_get_contents(base_path($this->argument('file'))), true);
        $rows = [];
        $aiFailures = 0;

        foreach ($cases as $case) {
            $context = [
                'age'                  => self::AGE[$case['age'] ?? ''] ?? null,
                'duration'             => self::DURATION[$case['duration'] ?? ''] ?? null,
                'risk'                 => empty($case['denied_all']) ? null : [],
                'denied_warning_signs' => !empty($case['denied_all']),
            ];
            $findings = $engine->match($case['text']);
            // Without the AI, "no other symptoms" is assumed only for the answered cases.
            $other = empty($case['denied_all']) ? null : false;

            if ($this->option('ai')) {
                sleep((int) $this->option('pause'));
                $read = $triage->interpret($case['text'], []);
                $aiFailures += $read === null ? 1 : 0;
                $findings = array_merge($findings, $read['present'] ?? []);
                $other = $read['other_symptoms'] ?? null;
            }

            $rows[] = ['id' => $case['id'], 'expect' => $case['expect'], 'got' => $engine->assess($findings, $context, $other)['category'], 'text' => $case['text']];
        }

        $rank = ['routine' => 0, 'unable_to_assess' => 1, 'urgent' => 2, 'emergency' => 3];
        $sensitivity = function (string $level) use ($rows, $rank) {
            $expected = array_filter($rows, fn ($r) => $r['expect'] === $level);
            $caught = array_filter($expected, fn ($r) => $rank[$r['got']] >= $rank[$level]);

            return $expected ? count($caught) . '/' . count($expected) . ' (' . round(100 * count($caught) / count($expected), 1) . '%)' : 'no cases';
        };

        $summary = [
            'ruleset'               => $engine->ruleset()['version'] . ' ' . $engine->hash(),
            'cases'                 => count($rows),
            'ai'                    => (bool) $this->option('ai'),
            'ai_failures'           => $aiFailures,
            'emergency_sensitivity' => $sensitivity('emergency'),
            'urgent_sensitivity'    => $sensitivity('urgent'),
            'exact_agreement'       => count(array_filter($rows, fn ($r) => $r['expect'] === $r['got'])) . '/' . count($rows),
            'false_reassurance'     => count(array_filter($rows, fn ($r) => $r['got'] === 'routine' && $r['expect'] !== 'routine')),
            'expert_agreement'      => 'not measured: expected categories were not set by a clinician',
        ];
        $mismatches = array_values(array_filter($rows, fn ($r) => $r['expect'] !== $r['got']));

        $this->line(json_encode($summary, JSON_PRETTY_PRINT));
        foreach ($mismatches as $m) {
            $this->line("{$m['id']} expected {$m['expect']}, got {$m['got']}: {$m['text']}");
        }

        $name = pathinfo($this->argument('file'), PATHINFO_FILENAME) . ($this->option('ai') ? '-ai' : '');
        @mkdir(base_path('docs/triage-runs'), 0755, true);
        file_put_contents(
            base_path("docs/triage-runs/{$name}-" . now()->format('Ymd-His') . '.json'),
            json_encode(compact('summary', 'rows'), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );

        return self::SUCCESS;
    }
}
