<?php

namespace App\Services\Evidence;

/**
 * Splits an AI response into checkable claims. Done in code, not by a model,
 * so the coverage figure cannot be improved by skipping difficult claims.
 */
class ClaimExtractor
{
    /** field name => is a safety-critical claim */
    private const FIELDS = [
        'when_to_seek_care' => true, 'red_flags' => true, 'warning_signs' => true,
        'contraindications' => true, 'max_daily_dose' => true, 'what_to_avoid' => true,
        'drug_regimen_considerations' => true,
        'core_explanation' => false, 'content' => false, 'explanation' => false,
        'plain_explanation' => false, 'rationale' => false, 'description' => false,
        'pathophysiology' => false, 'self_care' => false, 'self_care_tips' => false,
        'lifestyle' => false, 'lifestyle_tips' => false, 'diet_recommendations' => false,
        'management' => false, 'recommended_workup' => false, 'clinical_summary' => false,
        'pharmacist_notes' => false, 'step_by_step' => false, 'summary' => false,
    ];

    private const CRITICAL_PATTERN = '/\d\s?(mg|mcg|µg|g|ml|iu)\b|\b(overdose|contraindicat\w*|do not|never|avoid|emergency|fatal|dosis|jangan|hindari|darurat|segera)\b/iu';

    /** @return array<int,array{id:int,text:string,critical:bool}> Critical claims first. */
    public function extract(array $response): array
    {
        $claims = [];
        $this->walk($response, null, $claims);

        $unique = [];
        foreach ($claims as $claim) {
            $key = mb_strtolower($claim['text']);
            $unique[$key] = [
                'text'     => $claim['text'],
                'critical' => $claim['critical'] || ($unique[$key]['critical'] ?? false),
            ] + (empty($claim['inference']) ? [] : ['inference' => true]) + [
            ];
        }

        $ordered = array_values($unique);
        usort($ordered, fn ($a, $b) => $b['critical'] <=> $a['critical']);

        return array_map(
            fn ($claim, $i) => ['id' => $i + 1] + $claim,
            $ordered,
            array_keys($ordered)
        );
    }

    private function walk(mixed $node, ?string $field, array &$claims): void
    {
        if (is_array($node)) {
            // Drug and interaction entries are claims as a whole: a dose means
            // nothing without the drug it belongs to.
            if (!empty($node['name']) && (!empty($node['dose']) || !empty($node['frequency']))) {
                $regimen = array_filter([$node['dose'] ?? null, $node['frequency'] ?? null, $node['duration'] ?? null], 'is_string');
                $claims[] = [
                    'text'     => trim($node['name'] . (empty($node['generic']) ? '' : " ({$node['generic']})") . ': ' . implode(', ', $regimen)),
                    'critical' => true,
                ];
                foreach ((array) ($node['contraindications'] ?? []) as $item) {
                    if (is_string($item) && trim($item) !== '') {
                        $claims[] = ['text' => "{$node['name']} contraindication: {$item}", 'critical' => true];
                    }
                }

                foreach (['mechanism', 'how_to_take', 'notes', 'side_effects'] as $key) {
                    foreach ((array) ($node[$key] ?? []) as $item) {
                        if (is_string($item) && trim($item) !== '') {
                            $claims[] = ['text' => "{$node['name']} {$key}: {$item}", 'critical' => $key !== 'mechanism'];
                        }
                    }
                }

                return;
            }
            if (!empty($node['drug_a']) && !empty($node['drug_b'])) {
                $effect = $node['effect'] ?? $node['summary'] ?? null;
                if (is_string($effect)) {
                    $claims[] = [
                        'text'      => "{$node['drug_a']} + {$node['drug_b']}: {$effect}",
                        'critical'  => true,
                        'inference' => ($node['coverage'] ?? null) === 'ai_inferred',
                    ];
                }
                if (!empty($node['management']) && is_string($node['management'])) {
                    $claims[] = ['text' => "{$node['drug_a']} + {$node['drug_b']} management: {$node['management']}", 'critical' => true];
                }

                return;
            }

            foreach ($node as $key => $value) {
                if ($key === 'evidence' || $key === 'qa') {
                    continue;
                }
                $this->walk($value, is_string($key) ? $key : $field, $claims);
            }

            return;
        }

        if (!is_string($node) || $field === null || !array_key_exists($field, self::FIELDS)) {
            return;
        }

        foreach (preg_split('/(?<=[.!?。])\s+/u', trim($node), -1, PREG_SPLIT_NO_EMPTY) as $sentence) {
            if (mb_strlen($sentence) < 15) {
                continue;
            }
            $claims[] = [
                'text'     => $sentence,
                'critical' => self::FIELDS[$field] || preg_match(self::CRITICAL_PATTERN, $sentence) === 1,
            ];
        }
    }
}
