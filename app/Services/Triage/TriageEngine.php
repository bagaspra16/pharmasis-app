<?php

namespace App\Services\Triage;

/**
 * The clinical safety decision. Deterministic: no network, no model.
 *
 * Findings can only raise the category. "Routine" is reached only when every
 * requirement is positively met; anything missing gives "unable_to_assess".
 */
class TriageEngine
{
    private array $rules;

    private string $hash;

    public function __construct()
    {
        $json = str_replace("\r\n", "\n", file_get_contents(config('triage.rules_path')));
        $this->rules = json_decode($json, true, flags: JSON_THROW_ON_ERROR);
        $this->hash = substr(hash('sha256', $json), 0, 16);
    }

    public function ruleset(): array
    {
        return $this->rules;
    }

    public function hash(): string
    {
        return $this->hash;
    }

    public function isApproved(): bool
    {
        $approval = config('triage.approval');

        return !empty($approval['reviewer']) && !empty($approval['qualification'])
            && !empty($approval['date']) && ($approval['ruleset_hash'] ?? null) === $this->hash;
    }

    /** Whether the assessment may be shown to users. */
    public function isActive(): bool
    {
        return config('triage.enabled') && ($this->isApproved() || config('triage.allow_unreviewed'));
    }

    /** @return string[] Ids of findings whose patterns appear, without negation, in the text. */
    public function match(string $text): array
    {
        $text = mb_strtolower(str_replace(['’', '‘'], "'", $text));
        $found = [];

        foreach ($this->rules['findings'] as $id => $finding) {
            foreach ($finding['patterns'] as $pattern) {
                preg_match_all('~' . $pattern . '~u', $text, $matches, PREG_OFFSET_CAPTURE);
                foreach ($matches[0] as [, $offset]) {
                    if (!$finding['negatable'] || !$this->isNegated(substr($text, 0, $offset))) {
                        $found[$id] = $id;
                        break 2;
                    }
                }
            }
        }

        return array_values($found);
    }

    /**
     * @param string[]    $findings      Finding ids present
     * @param array       $context       age, duration, risk[], denied_warning_signs
     * @param bool|null   $otherSymptoms null when nothing could tell
     * @return array{category:string,rules:array,findings:string[],missing:string[]}
     */
    public function assess(array $findings, array $context = [], ?bool $otherSymptoms = null): array
    {
        $age = $context['age'] ?? null;
        $risk = $context['risk'] ?? null;
        if ($age === 'under_3_months') {
            $findings[] = 'infant_young';
        }
        $findings = array_values(array_unique(array_intersect($findings, array_keys($this->rules['findings']))));

        $matched = array_values(array_filter(
            $this->rules['rules'],
            fn ($rule) => $this->applies($rule, $findings, $context)
        ));

        foreach (['emergency', 'urgent'] as $level) {
            $hits = array_values(array_filter($matched, fn ($r) => $r['level'] === $level));
            if ($hits) {
                return $this->result($level, $hits, $findings);
            }
        }

        $missing = [];
        if (!$age) {
            $missing[] = 'age';
        }
        if (empty($context['duration'])) {
            $missing[] = 'duration';
        }
        if ($risk === null) {
            $missing[] = 'risk';
        }
        if (empty($context['denied_warning_signs'])) {
            $missing[] = 'warning_signs';
        }

        $routine = array_values(array_filter(
            $this->rules['routine'],
            fn ($r) => in_array($r['finding'], $findings, true)
        ));
        // A finding outside the routine list means this is not a routine picture.
        $unexplained = array_diff($findings, array_column($this->rules['routine'], 'finding'));

        if (!$routine || $unexplained || $otherSymptoms !== false) {
            $missing[] = 'recognised_complaint';
        }
        if ($age && !in_array($age, ['5_to_17', '18_to_64'], true)) {
            $missing[] = 'age_group_not_covered';
        }
        if ($risk) {
            $missing[] = 'risk_condition';
        }
        if (($context['severity'] ?? null) === 'severe') {
            $missing[] = 'severe_symptoms';
        }

        return $missing
            ? $this->result('unable_to_assess', [], $findings, $missing)
            : $this->result('routine', $routine, $findings);
    }

    private function applies(array $rule, array $findings, array $context): bool
    {
        if (!empty($rule['all']) && array_diff($rule['all'], $findings)) {
            return false;
        }
        if (!empty($rule['any']) && !array_intersect($rule['any'], $findings)) {
            return false;
        }
        if (!empty($rule['duration']) && !in_array($context['duration'] ?? null, $rule['duration'], true)) {
            return false;
        }
        if (!empty($rule['risk']) && !array_intersect($rule['risk'], $context['risk'] ?? [])) {
            return false;
        }

        return true;
    }

    private function isNegated(string $before): bool
    {
        // Only a negator directly in front of the phrase, within the same clause.
        $clause = preg_split('/[.,;!?\n]|\b(but|tapi|tetapi|namun)\b/u', $before);

        return (bool) preg_match(
            "/\\b(no|not|without|don't have|do not have|never had|tidak|tak|tanpa|bukan|gak|ga|nggak|enggak)\\s+((any|ada|a|an)\\s+)?$/u",
            end($clause)
        );
    }

    private function result(string $category, array $rules, array $findings, array $missing = []): array
    {
        return compact('category', 'rules', 'findings', 'missing') + [
            'ruleset_version' => $this->rules['version'],
            'ruleset_hash'    => $this->hash,
        ];
    }
}
