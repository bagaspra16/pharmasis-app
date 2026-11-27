<?php

namespace App\Services\Intake;

use App\Services\Triage\TriageEngine;
use App\Services\Triage\TriageService;

/**
 * Chooses which follow-up questions to ask, in code. Stateless: the plan is
 * recomputed from the complaint and the answers so far.
 */
class IntakeService
{
    /** Answers that mean "no information". They exclude every other choice. */
    public const NO_INFO = ['unknown', 'declined'];

    private const NUMBERS = [
        'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5, 'six' => 6, 'seven' => 7,
        'satu' => 1, 'dua' => 2, 'tiga' => 3, 'empat' => 4, 'lima' => 5, 'enam' => 6, 'tujuh' => 7,
    ];

    public function __construct(private TriageEngine $engine, private TriageService $triage) {}

    public function isActive(): bool
    {
        return config('intake.enabled') && $this->triage->isActive();
    }

    /**
     * Facts the user already stated, so they are not asked again. Only explicit
     * wording counts; nothing about the person is guessed.
     *
     * @return array{duration?:string,age?:string,severity?:string}
     */
    public function known(string $complaint): array
    {
        $text = mb_strtolower($complaint);
        $number = '(\d{1,3}|' . implode('|', array_keys(self::NUMBERS)) . ')';
        $value = fn (string $n) => self::NUMBERS[$n] ?? (int) $n;
        $known = [];

        // Age needs an age word, so "for 2 years" is not read as an age.
        $ages = [];
        preg_match_all("/\\b{$number}[ -](year|month)s?[ -]old\\b/u", $text, $english, PREG_SET_ORDER);
        preg_match_all("/\\b(?:umur|usia|berumur|berusia)(?: saya| anak saya)? {$number} (tahun|bulan)/u", $text, $indonesian, PREG_SET_ORDER);
        foreach (array_merge($english, $indonesian) as $m) {
            $months = $value($m[1]) * (in_array($m[2], ['year', 'tahun'], true) ? 12 : 1);
            $ages[] = match (true) {
                $months < 3 => 'under_3_months',
                $months < 60 => '3_months_to_4',
                $months < 216 => '5_to_17',
                $months < 780 => '18_to_64',
                default => '65_plus',
            };
        }
        // Two different ages mean we cannot tell whose is meant: ask.
        if (count(array_unique($ages)) === 1) {
            $known['age'] = $ages[0];
        }

        $withoutAges = str_replace(array_column(array_merge($english, $indonesian), 0), ' ', $text);
        $days = [];
        // Only "for / since / sudah N units" states how long. "Every 2 days" does not.
        $unit = '(day|days|hari|week|weeks|minggu|month|months|bulan|year|years|tahun)';
        preg_match_all("/\\b(?:for|since|past|last|sudah|udah|selama|sejak) {$number} {$unit}\\b/u", $withoutAges, $spans, PREG_SET_ORDER);
        foreach ($spans as $m) {
            $days[] = $value($m[1]) * match (true) {
                in_array($m[2], ['day', 'days', 'hari'], true) => 1,
                in_array($m[2], ['week', 'weeks', 'minggu'], true) => 7,
                default => 30,
            };
        }
        if (!$days && preg_match('/\b(since yesterday|since this morning|since today|sejak kemarin|sejak tadi|sejak pagi|sejak hari ini)\b/u', $text)) {
            $days[] = 1;
        }
        // A longer period worded some other way ("a week", "berbulan-bulan") means: ask.
        $rest = str_replace(array_column($spans, 0), ' ', $withoutAges);
        if (preg_match('/\b(weeks?|months?|years?|minggu|seminggu|berminggu|bulan|sebulan|berbulan|tahun|setahun|bertahun|lama)\b/u', $rest)) {
            $days = [];
        }
        if ($days) {
            // With several spans, the longest is the cautious reading.
            $longest = max($days);
            $known['duration'] = $longest < 3 ? 'under_3' : ($longest <= 7 ? '3_to_7' : 'over_7');
        }

        // "berat badan" is body weight. A negated word is left for the question.
        if (preg_match('/(?<!not |tidak |gak |no )\b(severe|very bad|unbearable|parah|hebat|sakit sekali|berat(?! badan))\b/u', $text)) {
            $known['severity'] = 'severe';
        } elseif (preg_match('/(?<!not |tidak |gak |no )\b(mild|slight|ringan)\b/u', $text)) {
            $known['severity'] = 'mild';
        }

        return $known;
    }

    /** @return string[] Complaint groups; empty when none is recognised. */
    public function groups(string $complaint): array
    {
        return array_keys(array_filter(
            config('intake.groups'),
            fn ($group) => preg_match('~(' . $group['pattern'] . ')~iu', $complaint) === 1
        ));
    }

    /**
     * The fixed questions still relevant, in priority order. Emergency signs
     * first. Answered questions stay in the list so the client can keep them.
     *
     * @param array $answers Structured answers so far, keyed like the questions
     */
    public function plan(string $complaint, array $answers, string $lang): array
    {
        $locale = $lang === 'id' ? 'id' : 'en';
        $l = trans('intake', [], $locale);
        $known = $this->known($complaint);
        $base = array_column($this->triage->questions($lang), null, 'triage_key');
        $noInfo = [self::NO_INFO, [$l['unknown'], $l['declined']]];
        $withNoInfo = function (array $q) use ($noInfo) {
            $q['values'] = array_merge($q['values'], $noInfo[0]);
            $q['choices'] = array_merge($q['choices'], $noInfo[1]);

            return $q;
        };

        $plan = [$withNoInfo($base['red_flags'])];
        if ($known) {
            $plan[0]['hint'] = $l['noted'] . ' ' . implode(', ', array_map(
                fn ($key, $v) => $l['known'][$key][$v],
                array_keys($known), $known
            ));
        }

        foreach (['age', 'duration'] as $key) {
            if (!isset($known[$key])) {
                $plan[] = $withNoInfo($base[$key]);
            }
        }

        $asked = [];
        foreach ($this->groups($complaint) as $group) {
            $asked = array_merge($asked, config("intake.groups.{$group}.ask"));
        }
        // Drop what the user has already told us.
        $asked = array_values(array_diff(array_unique($asked), $this->engine->match($complaint)));
        if ($asked) {
            $findings = $this->engine->ruleset()['findings'];
            $plan[] = $withNoInfo($this->question(
                'triage_associated', 'associated', $l['questions']['associated'],
                [...$asked, 'none'],
                [...array_map(fn ($f) => $findings[$f]['label'][$locale], $asked), $l['none']],
                true
            ));
        }

        if (!isset($known['severity'])) {
            $plan[] = $withNoInfo($this->question(
                'triage_severity', 'severity', $l['questions']['severity'],
                ['mild', 'moderate', 'severe'], array_values($l['severities']), false
            ));
        }

        $risk = $withNoInfo($base['risk']);
        $age = $answers['age'] ?? $known['age'] ?? null;
        // Pregnancy is not offered for young children.
        if (in_array($age, ['under_3_months', '3_months_to_4'], true)) {
            $i = array_search('pregnant', $risk['values'], true);
            array_splice($risk['values'], $i, 1);
            array_splice($risk['choices'], $i, 1);
        }
        $plan[] = $risk;

        // Needed because the answer may suggest medicines.
        $plan[] = [
            'id' => 'intake_meds', 'question' => $l['questions']['meds'], 'hint' => $l['meds_hint'],
            'choices' => [$l['none'], $l['declined']], 'allow_multiple' => false, 'free_text' => true, 'is_role' => false,
        ];

        return $plan;
    }

    private function question(string $id, string $key, string $text, array $values, array $choices, bool $multiple): array
    {
        return [
            'id' => $id, 'triage_key' => $key, 'question' => $text, 'hint' => '', 'values' => $values,
            'choices' => $choices, 'allow_multiple' => $multiple, 'free_text' => false, 'is_role' => false,
        ];
    }
}
