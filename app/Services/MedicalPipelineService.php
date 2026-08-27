<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class MedicalPipelineService
{
    // Model assignments (do not change)
    const MODEL_SYMPTOM_ANALYSIS = 'openai/gpt-oss-120b';
    const MODEL_DRUG_MATCHING    = 'openai/gpt-oss-20b';
    const MODEL_INTERACTION      = 'openai/gpt-oss-120b';
    const MODEL_SUMMARY          = 'openai/gpt-oss-120b';

    public function __construct(private GroqService $groq) {}

    /**
     * Run the full 5-step analysis pipeline.
     *
     * @param  string $symptoms        Raw symptom text (from voice or typed)
     * @param  string $lang            'en' or 'id'
     * @param  array  $profile         Optional patient profile
     * @param  string $mode            'classic' (default) or 'journey' (adds Infographic Journey zones)
     * @return array                   Structured result with all steps
     */
    public function run(string $symptoms, string $lang = 'auto', array $profile = [], string $mode = 'classic'): array
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

        $result = [
            'symptoms'   => $symptoms,
            'lang'       => $lang,
            'step1'      => $step1,
            'step2'      => $step2,
            'step3'      => $step3,
            'step4'      => $step4,
            'timestamp'  => now()->toISOString(),
        ];

        // ── Infographic Journey (opt-in, only for the /id social experience) ────
        if ($mode === 'journey') {
            $result['journey'] = $this->buildJourney(
                $symptoms, $lang, $langInstr, $profileCtx,
                $step1, $step2, $step3, $step4
            );
        }

        return $result;
    }

    /**
     * Build the "Infographic Journey" (Progressive Disclosure) structure.
     *
     * Zona Bawah (obat OTC/Resep + safety alerts) is DERIVED in PHP from the
     * existing step2/step3 so no clinical detail is lost. Zona Atas & Tengah
     * (body map, empathy summary, energy score, checklist, ICD-10) require a
     * single extra AI call. On any failure this returns a partial structure and
     * the frontend falls back to the classic step1-5 view.
     */
    private function buildJourney(
        string $symptoms, string $lang, string $langInstr, string $profileCtx,
        array $step1, array $step2, array $step3, array $step4
    ): array {
        // ── Zona Bawah: derived purely from existing data (no AI, no data loss) ──
        $otc = [];
        $rx  = [];
        foreach (($step2['drugs'] ?? []) as $d) {
            $bucket = !empty($d['otc']) ? 'otc' : 'rx';
            $entry = [
                'name'              => $d['name'] ?? '',
                'generic'           => $d['generic'] ?? '',
                'drug_class'        => $d['drug_class'] ?? '',
                'dosage'            => trim(($d['dose'] ?? '') . ' · ' . ($d['frequency'] ?? ''), ' ·'),
                'purpose'           => $d['purpose'] ?? '',
                // Full clinical detail preserved for the bottom-sheet drawer
                'drawer_click_details' => [
                    'mechanism'         => $d['mechanism'] ?? '',
                    'how_to_take'       => $d['how_to_take'] ?? '',
                    'duration'          => $d['duration'] ?? '',
                    'route'             => $d['route'] ?? '',
                    'side_effects'      => $d['side_effects'] ?? [],
                    'contraindications' => $d['contraindications'] ?? [],
                    'reference_source'  => $d['reference_source'] ?? '',
                ],
                'db_url'   => $d['db_url'] ?? null,
                'db_found' => $d['db_found'] ?? false,
            ];
            if ($bucket === 'otc') $otc[] = $entry; else $rx[] = $entry;
        }

        // Safety alerts derived from step3 interactions (severity → alert level)
        $alerts = [];
        foreach (($step3['interactions'] ?? []) as $i) {
            $sev = strtolower($i['severity'] ?? '');
            $level = in_array($sev, ['avoid', 'hindari']) ? 'CRITICAL'
                   : (in_array($sev, ['caution', 'hati-hati']) ? 'HIGH_ALERT' : 'MONITOR');
            $alerts[] = [
                'level'         => $level,
                'title'         => 'Interaksi Obat Terdeteksi',
                'short_warning' => trim(($i['drug_a'] ?? '') . ' + ' . ($i['drug_b'] ?? '')) . ': ' . ($i['effect'] ?? ''),
                'drawer_click_details' => [
                    'mechanism'        => $i['effect'] ?? '',
                    'action_for_doctor'=> !empty($i['substitute']) ? ('Pertimbangkan substitusi dengan: ' . $i['substitute']) : 'Pemantauan ketat disarankan.',
                ],
            ];
        }

        $zonaBawah = [
            'medication_recommendations' => [
                'over_the_counter'  => $otc,
                'prescription_only' => $rx,
            ],
            'safety_alerts' => $alerts,
        ];

        // ── Zona Atas & Tengah + ICD-10: one AI call ────────────────────────────
        try {
            $raw = $this->groq->chat(self::MODEL_SUMMARY, [
                ['role' => 'system', 'content' =>
                    "You are a warm, empathetic AI medical companion for laypeople. Turn the clinical analysis "
                  . "into a calming, human infographic. CRITICAL: Return ONLY valid JSON starting with { and ending "
                  . "with }. No conversational text.\n{$langInstr}"],
                ['role' => 'user', 'content' =>
                    "Patient symptoms: {$symptoms}\n"
                  . "Detected conditions: " . json_encode($step1['conditions'] ?? []) . "\n"
                  . "Recovery plan: " . json_encode($step4 ?? []) . "\n{$profileCtx}\n\n"
                  . "Return JSON with EXACTLY this structure:\n"
                  . '{"status_page_theme":"warm_yellow|calm_teal|alert_red (pick based on urgency)",'
                  . '"human_mapping_widget":{"highlighted_areas":[{"part":"head|throat|chest|stomach|abdomen|back|arms|legs|whole_body","status":"short word e.g. fatigue/pain/empty","color_code":"#FFB020 for caution or #EF4444 for emergency"}]},'
                  . '"ai_empathy_summary":"Warm, reassuring 2-3 sentences in the patient language. Avoid scary medical jargon. End by mentioning an approximate body energy score.",'
                  . '"energy_score":"calculate dynamic energy score 0-100 based on symptom severity (e.g., 80-100 for mild/healthy, 50-70 for moderate, 20-40 for severe/chronic, <20 for emergency). Only return the number as a string, e.g. 35",'
                  . '"interactive_timeline_checklist":[{"time":"Sekarang|Pagi|Siang (13:00)|Malam (21:00)","task":"one specific short self-care action","is_done":false}],'
                  . '"clinical_pdf_export":{"suspected_icd10":"ICD-10 code(s), e.g. F32.9 / R53.83","clinical_notes":"formal clinical summary in pure medical terminology for a real doctor"}}'
                ],
            ], ['temperature' => 0.5, 'max_tokens' => 1600, 'response_format' => ['type' => 'json_object']]);

            $ai = $this->parseJson($raw, 'journey');
        } catch (\Throwable $e) {
            Log::warning('MedicalPipeline journey AI failed', ['message' => $e->getMessage()]);
            $ai = [];
        }

        return [
            'status_page_theme' => $ai['status_page_theme'] ?? 'calm_teal',
            'zona_atas' => [
                'human_mapping_widget' => $ai['human_mapping_widget'] ?? ['highlighted_areas' => []],
                'ai_empathy_summary'   => $ai['ai_empathy_summary'] ?? '',
                'energy_score'         => $ai['energy_score'] ?? null,
            ],
            'zona_tengah' => [
                'interactive_timeline_checklist' => $ai['interactive_timeline_checklist'] ?? [],
            ],
            'zona_bawah' => array_merge($zonaBawah, [
                'clinical_pdf_export' => $ai['clinical_pdf_export'] ?? null,
            ]),
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
