<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class ScreeningService
{
    const MODEL = 'groq/compound';

    public function __construct(private GroqService $groq) {}

    /**
     * Generate 3-5 focused screening questions tailored to the patient's first
     * complaint. The fixed role question (doctor vs patient) is appended by the
     * controller — not here — so its shape and values are guaranteed.
     *
     * @return array<int,array<string,mixed>>  List of question objects.
     */
    public function generateQuestions(string $firstPrompt, string $lang = 'auto'): array
    {
        $resolvedLang = $lang === 'auto' ? $this->detectLang($firstPrompt) : $lang;
        $langInstr    = $this->langInstruction($resolvedLang);

        $raw = $this->groq->chat(self::MODEL, [
            ['role' => 'system', 'content' =>
                "You are an expert clinical intake specialist conducting a focused screening interview. "
              . "Given the patient's initial complaint, generate 3 to 5 SHORT, highly relevant follow-up questions "
              . "whose only goal is to gather enough detailed clinical context (onset, duration, severity, "
              . "location, associated symptoms, triggers, aggravating/relieving factors, relevant history, "
              . "current medications) to later form a clear conclusion about the patient's problem. "
              . "Ask ONLY what is genuinely useful for THIS specific complaint — do not pad with generic questions. "
              . "For each question, when it makes sense, offer 2-5 quick-choice options the user can tap; always also allow free text. "
              . "Do NOT ask whether the user is a doctor or patient — that question is handled separately. "
              . "CRITICAL: Return ONLY valid JSON starting with { and ending with }. No conversational text.\n{$langInstr}"],
            ['role' => 'user', 'content' =>
                "Patient's initial complaint: {$firstPrompt}\n\n"
              . "Return JSON with EXACTLY this structure:\n"
              . '{"questions":[{'
              . '"id":"q1",'
              . '"question":"the follow-up question text",'
              . '"hint":"a short helper line clarifying why you ask or how to answer (optional, can be empty)",'
              . '"choices":["quick option 1","quick option 2"],'
              . '"allow_multiple":false,'
              . '"free_text":true'
              . '}]}'
              . "\nProvide between 3 and 5 questions. Use an empty choices array [] if a question is best answered freely."
            ],
        ], ['temperature' => 0.4, 'max_tokens' => 1200, 'response_format' => ['type' => 'json_object']]);

        $parsed = $this->parseJson($raw, 'screen_questions');
        $questions = $parsed['questions'] ?? [];

        // Guard: clamp to 3-5 and normalize shape so the frontend never breaks.
        $questions = array_slice(array_values(array_filter($questions, fn($q) => !empty($q['question']))), 0, 5);

        return array_map(function ($q, $i) {
            return [
                'id'             => $q['id'] ?? ('q' . ($i + 1)),
                'question'       => (string) ($q['question'] ?? ''),
                'hint'           => (string) ($q['hint'] ?? ''),
                'choices'        => array_values(array_filter((array) ($q['choices'] ?? []), fn($c) => is_string($c) && $c !== '')),
                'allow_multiple' => (bool) ($q['allow_multiple'] ?? false),
                'free_text'      => array_key_exists('free_text', $q) ? (bool) $q['free_text'] : true,
                'is_role'        => false,
            ];
        }, $questions, array_keys($questions));
    }

    /**
     * Produce the final conclusion, branched by role.
     *
     * @param  array<int,array{question:string,answer:string}> $qa
     * @param  string $role  'doctor' or 'patient'
     */
    public function conclude(string $firstPrompt, array $qa, string $role, string $lang = 'auto'): array
    {
        $resolvedLang = $lang === 'auto' ? $this->detectLang($firstPrompt) : $lang;
        $langInstr    = $this->langInstruction($resolvedLang);
        $transcript   = $this->buildTranscript($firstPrompt, $qa);

        return $role === 'doctor'
            ? $this->concludeDoctor($transcript, $langInstr)
            : $this->concludePatient($transcript, $langInstr);
    }

    // ── Doctor branch ─────────────────────────────────────────────────────────
    private function concludeDoctor(string $transcript, string $langInstr): array
    {
        $raw = $this->groq->chat(self::MODEL, [
            ['role' => 'system', 'content' =>
                "You are a senior physician writing a concise, rigorous clinical assessment for a FELLOW healthcare "
              . "professional. Use precise medical terminology. Provide a differential diagnosis, a pharmacologic "
              . "regimen with full prescribing detail, drug-drug interactions with management, clinical management "
              . "steps, red flags, a suspected ICD-10 code, and a formal clinical summary. "
              . "CRITICAL: Return ONLY valid JSON starting with { and ending with }. No conversational text.\n{$langInstr}"],
            ['role' => 'user', 'content' =>
                "Screening transcript:\n{$transcript}\n\n"
              . "Return JSON with EXACTLY this structure:\n"
              . '{"mode":"doctor",'
              . '"differential":[{"name":"condition","likelihood":"High|Moderate|Low","rationale":"clinical reasoning"}],'
              . '"drugs":[{"name":"Brand","generic":"INN","drug_class":"class","dose":"e.g. 500mg","frequency":"e.g. 3x/day","duration":"e.g. 5-7 days","route":"oral|IV|topical","mechanism":"MoA 1-2 sentences","contraindications":["..."],"notes":"prescribing pearls"}],'
              . '"interactions":[{"drug_a":"...","drug_b":"...","severity":"Avoid|Caution|Monitor","effect":"...","management":"specific action"}],'
              . '"management":["clinical management / non-pharmacologic step"],'
              . '"red_flags":["escalation / referral criterion"],'
              . '"icd10":"e.g. J06.9",'
              . '"clinical_summary":"formal one-paragraph summary in medical terminology"}'
            ],
        ], ['temperature' => 0.3, 'max_tokens' => 3000, 'response_format' => ['type' => 'json_object']]);

        return $this->parseJson($raw, 'conclude_doctor') + ['mode' => 'doctor'];
    }

    // ── Patient branch ──────────────────────────────────────────────────────
    private function concludePatient(string $transcript, string $langInstr): array
    {
        $raw = $this->groq->chat(self::MODEL, [
            ['role' => 'system', 'content' =>
                "You are a warm, calm, and reassuring health companion speaking to an ordinary person (not a "
              . "medical professional). Open by acknowledging their feelings and gently reassuring them, THEN share "
              . "what the symptoms most likely point to in plain, non-frightening language. Avoid alarming jargon. "
              . "Give practical initial self-care, lifestyle adjustments, clear guidance on WHEN to visit a health "
              . "facility, WHICH type of doctor to see, and WHAT to ask that doctor. Never be dismissive; never "
              . "over-diagnose; always include a gentle disclaimer. "
              . "CRITICAL: Return ONLY valid JSON starting with { and ending with }. No conversational text.\n{$langInstr}"],
            ['role' => 'user', 'content' =>
                "Screening transcript:\n{$transcript}\n\n"
              . "Return JSON with EXACTLY this structure:\n"
              . '{"mode":"patient",'
              . '"reassurance":"a warm, motivating 2-3 sentence opener that calms the person",'
              . '"assumed_condition":{"name":"likely condition in plain words","plain_explanation":"gentle, simple explanation of what it is and why they likely have it"},'
              . '"self_care":["specific initial self-care action"],'
              . '"lifestyle":["lifestyle / daily-habit adjustment"],'
              . '"when_to_seek_care":"clear description of the signs that mean they should go to a health facility now",'
              . '"which_doctor":"which type of doctor/clinic to see, e.g. Dokter Umum or Spesialis Paru",'
              . '"what_to_ask_doctor":["a concrete question to ask the doctor"],'
              . '"disclaimer":"gentle reminder this is educational and not a diagnosis"}'
            ],
        ], ['temperature' => 0.5, 'max_tokens' => 2400, 'response_format' => ['type' => 'json_object']]);

        return $this->parseJson($raw, 'conclude_patient') + ['mode' => 'patient'];
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function buildTranscript(string $firstPrompt, array $qa): string
    {
        $lines = ["Initial complaint: {$firstPrompt}", ''];
        foreach ($qa as $pair) {
            $q = trim((string) ($pair['question'] ?? ''));
            $a = trim((string) ($pair['answer'] ?? ''));
            if ($q === '' && $a === '') continue;
            $lines[] = "Q: {$q}";
            $lines[] = "A: " . ($a !== '' ? $a : '(no answer)');
        }
        return implode("\n", $lines);
    }

    /**
     * Detect dominant language from free text using a simple keyword heuristic.
     * Returns 'id' for Bahasa Indonesia, 'en' otherwise.
     */
    private function detectLang(string $text): string
    {
        $t = ' ' . mb_strtolower($text) . ' ';
        $idWords = [
            ' saya ', ' aku ', ' yang ', ' dan ', ' tidak ', ' dengan ', ' sakit ',
            ' demam ', ' batuk ', ' sejak ', ' nyeri ', ' hari ', ' terasa ', ' sudah ',
            ' kepala ', ' perut ', ' badan ', ' mual ', ' pusing ', ' sesak ', ' lemas ',
            ' minum ', ' obat ', ' dokter ', ' tolong ', ' bantu ', ' ada ', ' juga ',
        ];
        $hits = 0;
        foreach ($idWords as $w) {
            if (str_contains($t, $w)) $hits++;
        }
        return $hits >= 1 ? 'id' : 'en';
    }

    /**
     * Build a hard language directive from an already-resolved language code.
     * This removes ambiguity — the AI is told exactly which language to use,
     * not asked to guess.
     */
    private function langInstruction(string $lang): string
    {
        if ($lang === 'id') {
            return 'LANGUAGE DIRECTIVE (MANDATORY): The patient wrote in Bahasa Indonesia. '
                 . 'You MUST write every field of your JSON response — including question text, hint, '
                 . 'and every choice option — entirely in Bahasa Indonesia. '
                 . 'Do NOT use English anywhere in any field value. No exceptions.';
        }

        return 'LANGUAGE DIRECTIVE (MANDATORY): The patient wrote in English. '
             . 'You MUST write every field of your JSON response — including question text, hint, '
             . 'and every choice option — entirely in English. '
             . 'Do NOT use Bahasa Indonesia anywhere in any field value. No exceptions.';
    }

    private function parseJson(string $raw, string $step): array
    {
        $clean = preg_replace('/```(?:json)?\s*([\s\S]*?)```/i', '$1', trim($raw));
        $clean = trim($clean);

        $decoded = json_decode($clean, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::warning("ScreeningService {$step} JSON parse error", [
                'raw'   => substr($raw, 0, 500),
                'error' => json_last_error_msg(),
            ]);
            return ['_raw' => $clean, '_error' => 'JSON parse failed'];
        }

        return $decoded;
    }
}
