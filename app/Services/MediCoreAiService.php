<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * MediCore AI — Intelligent Medical Intent Router
 *
 * Classifies free-form user input into one of three operational routes:
 *  • medi_facts  — educational/factual query, no active symptoms
 *  • medi_check  — active personal symptoms reported
 *  • medi_combo  — factual question + active symptoms simultaneously
 *
 * For emergencies (chest pain, stroke signs, severe dyspnoea, etc.) it
 * overrides all routes and returns an emergency_alert payload immediately.
 *
 * Output always conforms to the MediCore JSON schema so the frontend can
 * render adaptive UI components without additional parsing.
 */
class MediCoreAiService
{
    private const MODEL = 'openai/gpt-oss-120b';

    // Cache medi_facts responses for 7 days (facts don't change often).
    // medi_check / medi_combo are never cached (personal + contextual).
    private const FACTS_CACHE_TTL_DAYS = 7;

    public function __construct(private GroqService $groq) {}

    /**
     * Main entry point.
     *
     * @param  string $input  Raw user input (symptom description, question, or both)
     * @param  string $lang   'en' | 'id' | 'auto'
     * @return array          Structured MediCore JSON payload
     */
    public function process(string $input, string $lang = 'auto'): array
    {
        $input        = trim(substr(strip_tags($input), 0, 2000));
        $resolvedLang = $lang === 'auto' ? $this->detectLang($input) : $lang;
        $langInstr    = $this->langInstruction($resolvedLang);

        if (empty($input)) {
            return $this->errorPayload('Input kosong. Tolong ketik gejala atau pertanyaan medis kamu.', $resolvedLang);
        }

        try {
            // Phase 1: Intent classification + emergency check
            $classifyResult = $this->classify($input, $langInstr);

            // Emergency override: return immediately without further AI calls
            if (!empty($classifyResult['is_emergency'])) {
                return $this->buildEmergencyPayload($classifyResult, $resolvedLang);
            }

            $classification  = $classifyResult['classification'] ?? 'medi_check';
            $confidence      = (float) ($classifyResult['confidence_score'] ?? 0.80);
            $summaryTitle    = $classifyResult['summary_title'] ?? '';
            $intentReasoning = $classifyResult['intent_reasoning'] ?? '';
            $entities        = $classifyResult['detected_entities'] ?? null;

            // Phase 2: Branch-specific processing
            $factsPayload     = null;
            $screeningPayload = null;
            $conclusions      = null;

            $cacheKey = 'medicore_facts_' . md5($input . '_' . $resolvedLang);

            if ($classification === 'medi_facts') {
                if (Cache::has($cacheKey)) {
                    return Cache::get($cacheKey);
                }
                $factsPayload = $this->generateFactsPayload($input, $langInstr);
                $conclusions  = $this->generateFactsConclusions($input, $langInstr);
            } elseif ($classification === 'medi_check') {
                $screeningPayload = $this->generateScreeningPayload($input, $langInstr);
                $conclusions      = $this->generateCheckConclusions($input, $langInstr);
            } else {
                // medi_combo: dual processing
                [$factsPayload, $screeningPayload, $conclusions] = $this->generateComboPayload($input, $langInstr);
            }

            // Phase 3: Assemble final structured payload
            $result = $this->assemblePayload(
                classification:   $classification,
                confidence:       $confidence,
                summaryTitle:     $summaryTitle,
                intentReasoning:  $intentReasoning,
                entities:         $entities,
                factsPayload:     $factsPayload,
                screeningPayload: $screeningPayload,
                conclusions:      $conclusions,
            );

            if ($classification === 'medi_facts') {
                Cache::put($cacheKey, $result, now()->addDays(self::FACTS_CACHE_TTL_DAYS));
            }

            return $result;

        } catch (\RuntimeException $e) {
            Log::error('MediCoreAiService RuntimeException', ['message' => $e->getMessage()]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('MediCoreAiService unexpected error', ['message' => $e->getMessage()]);
            return $this->errorPayload('Terjadi kesalahan tidak terduga. Coba lagi nanti.', $resolvedLang);
        }
    }

    // Phase 1: Classification & Semantic Intent Analysis

    private function classify(string $input, string $langInstr): array
    {
        $raw = $this->groq->chat(self::MODEL, [
            ['role' => 'system', 'content' =>
                "You are MediCore AI, an elite clinical intent router and triage intelligence system. "
              . "Your primary objective is to analyze user input with deep semantic and medical accuracy, classify its operational intent into exactly ONE route (`medi_facts`, `medi_check`, or `medi_combo`), and detect any life-threatening medical emergencies.\n\n"
              . "SEMANTIC INTENT FRAMEWORK:\n"
              . "Evaluate the input along three core clinical axes:\n"
              . "A. Experiential vs Objective (Ownership): Does the user report personal/active physical symptoms (first-person or family member: 'saya', 'keluarga saya', 'my head hurts', 'anak demam') vs asking an impersonal/academic question ('dosis parasetamol', 'apa itu diabetes')?\n"
              . "B. Actionable Triage vs Knowledge Retrieval: Is the user seeking diagnostic evaluation/symptom screening vs seeking pharmacology, disease mechanism, dosage, or health advice?\n"
              . "C. Dual Intent (Hybrid): Does the input combine active personal symptom complaints WITH specific informational/pharmacological queries?\n\n"
              . "EXACT CLASSIFICATION RULES:\n"
              . "1. `medi_facts` (Pure Educational / Knowledge Query):\n"
              . "   - User asks about drug indications, dosages, side effects, physiological mechanisms, lab value interpretations, or general health facts.\n"
              . "   - CRITICAL: NO active personal symptoms or ongoing patient discomfort are reported.\n"
              . "   - Examples: 'Berapa dosis aman parasetamol?', 'Apakah amlodipine bisa bikin batuk?', 'Berapa gula darah normal puasa?'\n\n"
              . "2. `medi_check` (Active Symptom Triage & Screening):\n"
              . "   - User reports active personal symptoms, physical discomfort, onset of illness, or distress (experienced by user or a dependant).\n"
              . "   - User requires clinical symptom screening, differential assessment, and triage guidance.\n"
              . "   - Examples: 'Kepala saya pusing berputar dan mual sejak tadi pagi.', 'Anak saya demam 38.5C dan batuk berdahak.'\n\n"
              . "3. `medi_combo` (Dual Intent — Active Symptom + Specific Factual Query):\n"
              . "   - User simultaneously presents active personal symptoms AND explicitly asks a factual/medical/pharmacological question.\n"
              . "   - Examples: 'Saya minum amlodipine 5 hari dan sekarang batuk kering terus, kenapa obat ini bikin batuk dan berbahaya gak?', 'Gula darah saya 260 mg/dL dan kaki kesemutan, kenapa bisa kesemutan dan obat apa yang cocok?'\n\n"
              . "EMERGENCY RED FLAGS — If ANY of these are detected, set is_emergency=true IMMEDIATELY:\n"
              . "• Acute chest pain radiating to arm, neck, jaw, or back\n"
              . "• Severe sudden dyspnoea, choking, or respiratory distress\n"
              . "• Stroke red flags (FAST): sudden facial drooping, arm weakness, slurred speech\n"
              . "• Anaphylaxis / severe systemic allergic reaction (lip/throat swelling, stridor)\n"
              . "• Sudden loss of consciousness, unresponsiveness, or status epilepticus\n"
              . "• Active severe uncontrolled hemorrhage or major trauma\n"
              . "• Suicidal ideation, acute psychosis, or self-harm threat\n\n"
              . "CRITICAL: Return ONLY valid JSON. No conversational text or markdown wrappers.\n{$langInstr}",
            ],
            ['role' => 'user', 'content' =>
                "User input: \"{$input}\"\n\n"
              . "Analyze intent and return JSON with EXACTLY this structure:\n"
              . '{"classification":"medi_facts|medi_check|medi_combo",'
              . '"confidence_score":0.95,'
              . '"intent_reasoning":"Precise 1-sentence explanation of why this input maps to the selected classification based on ownership and intent.",'
              . '"detected_entities":{"symptoms":[],"medications":[],"medical_concepts":[]},'
              . '"summary_title":"Short descriptive title of the topic (max 8 words)",'
              . '"is_emergency":false,'
              . '"emergency_message":"Only fill if is_emergency=true: clear instructions to call emergency services immediately",'
              . '"emergency_numbers":{"id":"119","international":"112"}}'
            ],
        ], ['temperature' => 0.1, 'max_tokens' => 450, 'response_format' => ['type' => 'json_object']]);

        return $this->parseJson($raw, 'classify');
    }

    // Phase 2a: medi_facts

    private function generateFactsPayload(string $input, string $langInstr): array
    {
        $raw = $this->groq->chat(self::MODEL, [
            ['role' => 'system', 'content' =>
                "You are a board-certified medical knowledge engine. Provide accurate, evidence-based medical facts. "
              . "Structure your output into clear educational chunks. "
              . "CRITICAL: Return ONLY valid JSON. No conversational text.\n{$langInstr}",
            ],
            ['role' => 'user', 'content' =>
                "Medical question: \"{$input}\"\n\n"
              . "Return JSON:\n"
              . '{"fact_badge":"Verified Medical Fact",'
              . '"core_explanation":"Concise primary answer (2-3 sentences)",'
              . '"key_points":['
              .   '{"heading":"Mechanism","content":"..."},'
              .   '{"heading":"Common Causes","content":"..."},'
              .   '{"heading":"Health Impact","content":"..."}'
              . '],'
              . '"medical_sources":["WHO Guidelines","OpenFDA Database","Clinical Evidence"],'
              . '"disclaimer":"Educational information only. Not a substitute for professional medical advice."}'
            ],
        ], ['temperature' => 0.2, 'max_tokens' => 900, 'response_format' => ['type' => 'json_object']]);

        return $this->parseJson($raw, 'facts_payload');
    }

    private function generateFactsConclusions(string $input, string $langInstr): array
    {
        $raw = $this->groq->chat(self::MODEL, [
            ['role' => 'system', 'content' =>
                "You are MediCore AI generating dual-mode conclusions for a medical facts query. "
              . "Provide both patient-friendly and clinician-level responses simultaneously. "
              . "CRITICAL: Return ONLY valid JSON.\n{$langInstr}",
            ],
            ['role' => 'user', 'content' =>
                "Medical question: \"{$input}\"\n\n"
              . "Return JSON:\n"
              . '{"patient_mode":{'
              .   '"explanation":"Plain language explanation for everyday people",'
              .   '"self_care_tips":["actionable tip 1","tip 2"],'
              .   '"recommended_specialist":"e.g., General Practitioner"'
              . '},'
              . '"clinician_mode":{'
              .   '"differential_diagnoses":[{"disease":"...","icd_10":"...","likelihood":"High|Moderate|Low"}],'
              .   '"pathophysiology":"Precise pathophysiological explanation",'
              .   '"recommended_workup":["diagnostic test 1","test 2"],'
              .   '"drug_regimen_considerations":"Pharmacological considerations or N/A"'
              . '}}'
            ],
        ], ['temperature' => 0.3, 'max_tokens' => 1200, 'response_format' => ['type' => 'json_object']]);

        return $this->parseJson($raw, 'facts_conclusions');
    }

    // Phase 2b: medi_check

    private function generateScreeningPayload(string $input, string $langInstr): array
    {
        $raw = $this->groq->chat(self::MODEL, [
            ['role' => 'system', 'content' =>
                "You are a clinical intake specialist. Generate 3-5 targeted screening questions to gather "
              . "clinical context (onset, severity, associated symptoms, history, triggers). "
              . "Do NOT include a 'doctor vs patient' role question. "
              . "CRITICAL: Return ONLY valid JSON.\n{$langInstr}",
            ],
            ['role' => 'user', 'content' =>
                "Patient's active symptoms: \"{$input}\"\n\n"
              . "Return JSON:\n"
              . '{"transition_message":"Empathetic 1-sentence message acknowledging symptoms",'
              . '"screening_questions":['
              .   '{"id":"q1","question":"...","hint":"brief helper text","options":["option 1","option 2","option 3"]}'
              . ']}'
            ],
        ], ['temperature' => 0.35, 'max_tokens' => 900, 'response_format' => ['type' => 'json_object']]);

        return $this->parseJson($raw, 'screening_payload');
    }

    private function generateCheckConclusions(string $input, string $langInstr): array
    {
        $raw = $this->groq->chat(self::MODEL, [
            ['role' => 'system', 'content' =>
                "You are MediCore AI generating preliminary dual-mode conclusions based on reported symptoms. "
              . "These are initial assessments before full screening. "
              . "CRITICAL: Return ONLY valid JSON.\n{$langInstr}",
            ],
            ['role' => 'user', 'content' =>
                "Reported symptoms: \"{$input}\"\n\n"
              . "Return JSON:\n"
              . '{"patient_mode":{'
              .   '"explanation":"Warm, plain-language overview of what the symptoms might indicate",'
              .   '"self_care_tips":["immediate self-care tip 1","tip 2"],'
              .   '"recommended_specialist":"which type of doctor/clinic to visit"'
              . '},'
              . '"clinician_mode":{'
              .   '"differential_diagnoses":[{"disease":"...","icd_10":"...","likelihood":"High|Moderate|Low"}],'
              .   '"pathophysiology":"Pathophysiological rationale for the most likely differential",'
              .   '"recommended_workup":["recommended diagnostic workup 1","workup 2"],'
              .   '"drug_regimen_considerations":"Initial pharmacological considerations"'
              . '}}'
            ],
        ], ['temperature' => 0.3, 'max_tokens' => 1200, 'response_format' => ['type' => 'json_object']]);

        return $this->parseJson($raw, 'check_conclusions');
    }

    // Phase 2c: medi_combo

    private function generateComboPayload(string $input, string $langInstr): array
    {
        $factsPayload     = $this->generateFactsPayload($input, $langInstr);
        $screeningPayload = $this->generateScreeningPayload($input, $langInstr);
        $conclusions      = $this->generateCheckConclusions($input, $langInstr);

        return [$factsPayload, $screeningPayload, $conclusions];
    }

    // Emergency

    private function buildEmergencyPayload(array $classifyResult, string $lang): array
    {
        $isId = $lang === 'id';

        return [
            'classification'   => 'emergency_alert',
            'confidence_score' => 1.0,
            'summary_title'    => $isId ? 'Kondisi Darurat Medis Terdeteksi' : 'Medical Emergency Detected',
            'is_emergency'     => true,
            'emergency_alert'  => [
                'title'         => $isId ? '🚨 SEGERA Hubungi Layanan Darurat' : '🚨 Contact Emergency Services IMMEDIATELY',
                'message'       => $classifyResult['emergency_message'] ?? (
                    $isId
                    ? 'Gejala yang Anda laporkan mengindikasikan potensi kondisi medis darurat. Harap segera hubungi layanan darurat atau menuju IGD rumah sakit terdekat.'
                    : 'The symptoms you reported indicate a potential medical emergency. Please contact emergency services or proceed to the nearest emergency department immediately.'
                ),
                'emergency_numbers' => $classifyResult['emergency_numbers'] ?? ['id' => '119', 'international' => '112'],
                'actions'           => $isId
                    ? ['Hubungi 119 (Indonesia)', 'Pergi ke UGD terdekat', 'Minta seseorang menemani Anda']
                    : ['Call 112 or local emergency number', 'Go to nearest Emergency Room', 'Do not drive alone'],
            ],
            'ui_route' => [
                'show_fact_card'      => false,
                'show_screening_card' => false,
                'layout_mode'         => 'emergency',
            ],
            'medical_facts_payload' => null,
            'screening_payload'     => null,
            'conclusions'           => null,
        ];
    }

    // Assembly

    private function assemblePayload(
        string  $classification,
        float   $confidence,
        string  $summaryTitle,
        string  $intentReasoning = '',
        ?array  $entities = null,
        ?array  $factsPayload = null,
        ?array  $screeningPayload = null,
        ?array  $conclusions = null,
    ): array {
        $showFact      = in_array($classification, ['medi_facts', 'medi_combo']);
        $showScreening = in_array($classification, ['medi_check', 'medi_combo']);
        $layoutMode    = match ($classification) {
            'medi_facts' => 'single_knowledge_wiki',
            'medi_check' => 'screening_flow',
            'medi_combo' => 'split_edu_screening',
            default      => 'screening_flow',
        };

        return [
            'classification'        => $classification,
            'confidence_score'      => $confidence,
            'summary_title'         => $summaryTitle,
            'intent_reasoning'      => $intentReasoning,
            'detected_entities'     => $entities ?? ['symptoms' => [], 'medications' => [], 'medical_concepts' => []],
            'is_emergency'          => false,
            'ui_route'              => [
                'show_fact_card'      => $showFact,
                'show_screening_card' => $showScreening,
                'layout_mode'         => $layoutMode,
            ],
            'medical_facts_payload' => $factsPayload,
            'screening_payload'     => $screeningPayload,
            'conclusions'           => $conclusions,
        ];
    }

    // Helpers

    private function errorPayload(string $message, string $lang): array
    {
        return [
            'classification'        => 'error',
            'confidence_score'      => 0.0,
            'summary_title'         => 'Error',
            'is_emergency'          => false,
            'error'                 => $message,
            'ui_route'              => ['show_fact_card' => false, 'show_screening_card' => false, 'layout_mode' => 'error'],
            'medical_facts_payload' => null,
            'screening_payload'     => null,
            'conclusions'           => null,
        ];
    }

    private function detectLang(string $text): string
    {
        $t = ' ' . mb_strtolower($text) . ' ';
        $idWords = [
            ' saya ', ' aku ', ' yang ', ' dan ', ' tidak ', ' dengan ', ' sakit ',
            ' demam ', ' batuk ', ' sejak ', ' nyeri ', ' hari ', ' terasa ', ' sudah ',
            ' kepala ', ' perut ', ' badan ', ' mual ', ' pusing ', ' sesak ', ' lemas ',
            ' obat ', ' dokter ', ' tolong ', ' ada ', ' juga ', ' kenapa ', ' apa ',
            ' berapa ', ' apakah ', ' bagaimana ', ' kondisi ', ' gejala ',
        ];
        $hits = 0;
        foreach ($idWords as $w) {
            if (str_contains($t, $w)) $hits++;
        }
        return $hits >= 1 ? 'id' : 'en';
    }

    private function langInstruction(string $lang): string
    {
        if ($lang === 'id') {
            return 'LANGUAGE DIRECTIVE (MANDATORY): The user wrote in Bahasa Indonesia. '
                 . 'You MUST write every field of your JSON response entirely in Bahasa Indonesia. '
                 . 'Do NOT use English anywhere in any field value. No exceptions.';
        }
        return 'LANGUAGE DIRECTIVE (MANDATORY): The user wrote in English. '
             . 'You MUST write every field of your JSON response entirely in English. '
             . 'Do NOT use Bahasa Indonesia anywhere in any field value. No exceptions.';
    }

    private function parseJson(string $raw, string $step): array
    {
        $clean   = preg_replace('/```(?:json)?\s*([\s\S]*?)```/i', '$1', trim($raw));
        $clean   = trim($clean);
        $decoded = json_decode($clean, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::warning("MediCoreAiService {$step} JSON parse error", [
                'raw'   => substr($raw, 0, 600),
                'error' => json_last_error_msg(),
            ]);
            return ['_raw' => $clean, '_error' => 'JSON parse failed at: ' . $step];
        }

        return $decoded;
    }
}
