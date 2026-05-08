<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class MedicalPipelineService
{
    // Model assignments (do not change)
    const MODEL_SYMPTOM_ANALYSIS = 'llama-3.3-70b-versatile';
    const MODEL_DRUG_MATCHING    = 'llama-3.1-8b-instant';
    const MODEL_INTERACTION      = 'meta-llama/llama-4-scout-17b-16e-instruct';
    const MODEL_SUMMARY          = 'llama-3.3-70b-versatile';

    public function __construct(private GroqService $groq) {}

    /**
     * Run the full 5-step analysis pipeline.
     *
     * @param  string $symptoms        Raw symptom text (from voice or typed)
     * @param  string $lang            'en' or 'id'
     * @param  array  $profile         Optional patient profile
     * @return array                   Structured result with all steps
     */
    public function run(string $symptoms, string $lang = 'auto', array $profile = []): array
    {
        $langInstr = $this->langInstruction($lang);
        $profileCtx = $this->buildProfileContext($profile);

        // ── Step 1: Symptom Analysis ────────────────────────────────────────────
        $step1Raw = $this->groq->chat(self::MODEL_SYMPTOM_ANALYSIS, [
            ['role' => 'system', 'content' => "You are an expert clinical symptom analyst. Analyze the patient's described symptoms and provide a DIFFERENTIAL DIAGNOSIS. Identify 2-4 most likely medical conditions with confidence levels, explaining your reasoning. CRITICAL: Return ONLY valid JSON starting with { and ending with }. DO NOT include any conversational text.\n{$langInstr}"],
            ['role' => 'user', 'content' => "Patient symptoms: {$symptoms}\n{$profileCtx}\n\nRespond with JSON:\n{\"conditions\":[{\"name\":\"...\",\"likelihood\":\"High|Moderate|Low\",\"description\":\"... (explain reasoning based on symptoms)\"}],\"urgency\":\"Emergency|Urgent|Routine\",\"summary\":\"...\"}"],
        ], ['temperature' => 0.3, 'max_tokens' => 1024, 'response_format' => ['type' => 'json_object']]);

        $step1 = $this->parseJson($step1Raw, 'step1');

        // ── Step 2: Drug Matching ───────────────────────────────────────────────
        $conditionsText = collect($step1['conditions'] ?? [])->pluck('name')->join(', ');
        $step2Raw = $this->groq->chat(self::MODEL_DRUG_MATCHING, [
            ['role' => 'system', 'content' =>
                "You are a senior clinical pharmacist with deep drug knowledge. "
              . "Provide a COMPREHENSIVE medication regimen. If the patient has multiple symptoms (e.g. fever AND sore throat), recommend MULTIPLE drugs that safely work together (e.g. a pain reliever AND a lozenge). "
              . "For each recommended medication, provide COMPLETE clinical details: mechanism, dosage, side effects, contraindications, and how to take it. "
              . "CRITICAL: Return ONLY valid JSON starting with { and ending with }. DO NOT include any conversational text like 'Here is the regimen'.\n{$langInstr}"],
            ['role' => 'user', 'content' =>
                "Identified conditions: {$conditionsText}\n{$profileCtx}\n\n"
              . "For each drug, include all fields below. Return JSON:\n"
              . '{"drugs":[{'
              . '"name":"Brand name",'
              . '"generic":"Generic/INN name",'
              . '"drug_class":"e.g. NSAID, Antibiotic, Antihistamine",'
              . '"dose":"e.g. 500mg",'
              . '"frequency":"e.g. 3x daily",'
              . '"duration":"e.g. 5-7 days",'
              . '"route":"oral|topical|injection",'
              . '"purpose":"What condition this treats",'
              . '"mechanism":"How this drug works in the body (1-2 sentences)",'
              . '"how_to_take":"Specific instructions: with/without food, timing, water amount, do not crush etc.",'
              . '"side_effects":["common side effect 1","common side effect 2"],'
              . '"contraindications":["e.g. pregnancy","e.g. liver disease"],'
              . '"otc":true,'
              . '"reference_source":"e.g. WHO Essential Medicines / FDA / MedlinePlus"'
              . '}],"pharmacist_notes":"Overall prescribing notes and important warnings"}'
            ],
        ], ['temperature' => 0.3, 'max_tokens' => 2048, 'response_format' => ['type' => 'json_object']]);

        $step2 = $this->parseJson($step2Raw, 'step2');

        // ── Step 3: Interaction Check ───────────────────────────────────────────
        $drugNames = collect($step2['drugs'] ?? [])->pluck('name')->join(', ');
        $step3Raw = $this->groq->chat(self::MODEL_INTERACTION, [
            ['role' => 'system', 'content' => "You are a drug interaction specialist. Check all drug pairs for interactions. CRITICAL: Return ONLY valid JSON starting with { and ending with }. DO NOT include any conversational text.\n{$langInstr}"],
            ['role' => 'user', 'content' => "Drugs to check: {$drugNames}\n\nRespond with JSON:\n{\"interactions\":[{\"drug_a\":\"...\",\"drug_b\":\"...\",\"severity\":\"Avoid|Caution|Monitor\",\"effect\":\"...\",\"substitute\":\"...\"}],\"safe_combinations\":\"...\",\"final_drug_list\":[\"...\"]}"],
        ], ['temperature' => 0.2, 'max_tokens' => 1024, 'response_format' => ['type' => 'json_object']]);

        $step3 = $this->parseJson($step3Raw, 'step3');

        // ── Step 4: Full Summary ────────────────────────────────────────────────
        $step4Raw = $this->groq->chat(self::MODEL_SUMMARY, [
            ['role' => 'system', 'content' =>
                "You are a compassionate medical advisor writing a comprehensive, actionable patient care plan. "
              . "Be specific, practical, and evidence-based. Use simple language the patient can follow. "
              . "CRITICAL: Return ONLY valid JSON starting with { and ending with }. DO NOT include any conversational text.\n{$langInstr}"],
            ['role' => 'user', 'content' =>
                "Symptoms: {$symptoms}\n"
              . "Conditions: " . json_encode($step1['conditions'] ?? []) . "\n"
              . "Medications: " . json_encode($step2['drugs'] ?? []) . "\n"
              . "Interactions: " . json_encode($step3['interactions'] ?? []) . "\n"
              . "{$profileCtx}\n\n"
              . "Return JSON with this exact structure:\n"
              . '{"recovery_timeline":"Expected recovery duration",'
              . '"usage_instructions":[{'
              . '"drug":"Drug name",'
              . '"step_by_step":["Step 1: ...","Step 2: ..."],'
              . '"timing":"e.g. Morning after breakfast, before sleep",'
              . '"with_food":true,'
              . '"max_daily_dose":"e.g. Do not exceed 4g/day",'
              . '"what_to_avoid":"e.g. Alcohol, antacids within 2 hours",'
              . '"what_to_watch":"Signs the drug is working or not working"'
              . '}],'
              . '"lifestyle_tips":["specific actionable tip 1","tip 2"],'
              . '"diet_recommendations":["food to eat","food to avoid"],'
              . '"warning_signs":["Go to ER if...","Call doctor if..."],'
              . '"follow_up":"When to see a doctor for follow-up",'
              . '"disclaimer":"Medical disclaimer text"}'
            ],
        ], ['temperature' => 0.4, 'max_tokens' => 3000, 'response_format' => ['type' => 'json_object']]);

        $step4 = $this->parseJson($step4Raw, 'step4');

        return [
            'symptoms'   => $symptoms,
            'lang'       => $lang,
            'step1'      => $step1,
            'step2'      => $step2,
            'step3'      => $step3,
            'step4'      => $step4,
            'timestamp'  => now()->toISOString(),
        ];
    }

    // ── Helpers ─────────────────────────────────────────────────────────────────

    private function langInstruction(string $lang): string
    {
        // Auto-detect: AI reads the input language and responds in the same language.
        // Do NOT translate or force a language — match whatever the user typed/spoke.
        return 'IMPORTANT: Detect the language of the patient\'s symptom input automatically. '
             . 'If the patient wrote or spoke in Bahasa Indonesia, respond entirely in Bahasa Indonesia. '
             . 'If the patient used English, respond entirely in English. '
             . 'If mixed, use whichever language dominates. '
             . 'Never translate the input — always mirror the patient\'s own language in every field of your JSON response.';
    }

    private function buildProfileContext(array $profile): string
    {
        if (empty(array_filter($profile))) {
            return '';
        }

        $lines = ["Patient profile:"];
        if (!empty($profile['age']))       $lines[] = "- Age: {$profile['age']} years old";
        if (!empty($profile['weight']))    $lines[] = "- Weight: {$profile['weight']} kg";
        if (!empty($profile['gender']))    $lines[] = "- Gender: {$profile['gender']}";
        if (!empty($profile['allergies'])) $lines[] = "- Known allergies: {$profile['allergies']}";
        if (!empty($profile['conditions'])) $lines[] = "- Existing conditions: {$profile['conditions']}";
        if (!empty($profile['medications'])) $lines[] = "- Currently taking: {$profile['medications']}";
        $lines[] = "Adjust all drug recommendations, dosages, and warnings based on this profile.";
        $lines[] = "Flag any drugs that conflict with the patient's known allergies or existing conditions.";

        return implode("\n", $lines);
    }

    private function parseJson(string $raw, string $step): array
    {
        // Strip markdown code fences if model wraps in them
        $clean = preg_replace('/```(?:json)?\s*([\s\S]*?)```/i', '$1', trim($raw));
        $clean = trim($clean);

        $decoded = json_decode($clean, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::warning("MedicalPipeline {$step} JSON parse error", [
                'raw'   => substr($raw, 0, 500),
                'error' => json_last_error_msg(),
            ]);
            return ['_raw' => $clean, '_error' => 'JSON parse failed'];
        }

        return $decoded;
    }
}
