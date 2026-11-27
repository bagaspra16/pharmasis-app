<?php

namespace App\Console\Commands;

use App\Services\Triage\TriageEngine;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

/** Writes the sheet a clinician signs, and checks each quote is still on its source page. */
class TriageReviewSheet extends Command
{
    protected $signature = 'triage:review-sheet';

    protected $description = 'Generate docs/TRIAGE-CLINICAL-REVIEW.md';

    public function handle(TriageEngine $engine): int
    {
        $rules = $engine->ruleset();
        $all = array_merge($rules['rules'], array_map(fn ($r) => $r + ['level' => 'routine'], $rules['routine']));
        $normalise = fn (string $t) => preg_replace('/\s+/u', ' ', mb_strtolower(str_replace(
            ['’', '‘'], "'", html_entity_decode(strip_tags($t), ENT_QUOTES | ENT_HTML5, 'UTF-8')
        )));

        $pages = [];
        $missing = 0;
        $rows = '';
        foreach ($all as $rule) {
            $url = $rule['source']['url'];
            $pages[$url] ??= rescue(fn () => $normalise(Http::timeout(30)->get($url)->throw()->body()), '', false);
            $found = str_contains($pages[$url], $normalise($rule['source']['quote']));
            $missing += $found ? 0 : 1;

            $label = fn (array $ids) => implode(', ', array_map(fn ($id) => $rules['findings'][$id]['label']['en'], $ids));
            $when = array_filter([
                isset($rule['finding']) ? $label([$rule['finding']]) : null,
                empty($rule['all']) ? null : 'all of: ' . $label($rule['all']),
                empty($rule['any']) ? null : 'any of: ' . $label($rule['any']),
                empty($rule['duration']) ? null : 'duration: ' . implode(', ', $rule['duration']),
                empty($rule['risk']) ? null : 'risk answer: ' . implode(', ', $rule['risk']),
            ]);

            $rows .= "| {$rule['id']} | {$rule['level']} | " . implode('; ', $when)
                . " | [{$rule['source']['publisher']}: {$rule['source']['title']}]({$url}) \"{$rule['source']['quote']}\""
                . ($found ? '' : ' **(quote not found on page)**')
                . ' | ' . ($rule['adaptation'] ?: '-') . " | | |\n";
        }

        $patterns = '';
        foreach ($rules['findings'] as $id => $finding) {
            $patterns .= "- **{$finding['label']['en']}** (`{$id}`): `" . implode('` `', $finding['patterns']) . "`\n";
        }

        $status = $engine->isApproved() ? 'Approved' : '**NOT REVIEWED. NOT APPROVED.**';
        $sheet = "# Triage rules for clinical review\n\n"
            . "Status: {$status}\n\nRuleset: {$rules['version']}, hash `{$engine->hash()}`. Generated " . now()->toDateString() . ".\n\n"
            . "The rules were drafted by an AI assistant from public pages. \"Adaptation\" shows where a rule differs from its source. "
            . "Please check every rule, the word patterns, the emergency numbers in `config/triage.php`, and the user messages in `lang/*/triage.php`.\n\n"
            . "## Rules\n\n| ID | Level | When | Source and quote | Adaptation | Approve / change | Comment |\n| --- | --- | --- | --- | --- | --- | --- |\n{$rows}\n"
            . "Anything that matches no rule, or has missing information, is \"unable to assess\".\n\n"
            . "## Word patterns\n\n{$patterns}\n"
            . "## Sign-off\n\nReviewer:\n\nQualification and registration number:\n\nDate:\n\nDecision (approve / approve with changes / reject):\n\n"
            . "After approval, copy the reviewer, qualification, date and hash `{$engine->hash()}` into `config/triage.php`.\n";

        file_put_contents(base_path('docs/TRIAGE-CLINICAL-REVIEW.md'), $sheet);
        $this->info(count($all) . " rules written; {$missing} quotes not found on their page.");

        return $missing ? self::FAILURE : self::SUCCESS;
    }
}
