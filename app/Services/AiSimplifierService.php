<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiSimplifierService
{
    private string $apiKey;
    private string $apiBase;
    private string $model;

    public function __construct()
    {
        $this->apiKey = config('services.groq.key', '');
        $this->apiBase = config('services.groq.base', 'https://api.groq.com/openai/v1');
        $this->model = config('services.groq.model', 'openai/gpt-oss-120b');
    }

    /**
     * Simplify a piece of medical text into plain language and translate it.
     * Caches result for 30 days.
     */
    public function simplify(string $drugId, string $field, string $text, string $language = 'English'): array
    {
        if (empty($this->apiKey)) {
            return [
                'success' => false,
                'error' => 'AI service is not configured. Please add your GROQ_API_KEY to .env',
            ];
        }

        $langSlug = \Illuminate\Support\Str::slug($language);
        $cacheKey = "ai_simplify_{$drugId}_{$field}_{$langSlug}";

        if (Cache::has($cacheKey)) {
            return ['success' => true, 'text' => Cache::get($cacheKey), 'cached' => true];
        }

        $truncated = substr(strip_tags($text), 0, 2000);

        $prompt = <<<PROMPT
You are a helpful medical information assistant. Your job is to explain medical information in simple, easy-to-understand language for everyday people.

Rewrite the following medical information about "{$field}" in simple terms:
- Use plain language (avoid medical jargon)
- Keep it concise and structured
- Use short bullet points where helpful
- Do NOT give medical advice or diagnose conditions
- End with: "Always consult a healthcare professional for personal medical advice."

IMPORTANT: You MUST write your explanation in the following language: {$language}. 
If the requested language is not English, ensure the entire output is translated naturally and accurately into {$language}.

Medical text to simplify:
"""
{$truncated}
"""

Simplified explanation in {$language}:
PROMPT;

        try {
            $response = Http::timeout(30)
                ->withToken($this->apiKey)
                ->post("{$this->apiBase}/chat/completions", [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a helpful, friendly medical information simplifier. Never diagnose or recommend treatment.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'max_tokens' => 600,
                'temperature' => 0.4,
            ]);

            if ($response->failed()) {
                Log::error('AI Simplifier (Groq) error', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return ['success' => false, 'error' => 'AI service returned an error. Please try again later.'];
            }

            $simplified = $response->json('choices.0.message.content', '');

            if (empty($simplified)) {
                return ['success' => false, 'error' => 'No response from AI.'];
            }

            Cache::put($cacheKey, $simplified, now()->addDays(30));
            return ['success' => true, 'text' => $simplified, 'cached' => false];

        } catch (\Exception $e) {
            Log::error('AiSimplifierService (Groq) error', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => 'Failed to reach AI service. Please try again later.'];
        }
    }

    /**
     * Humanize & translate all drug information sections in a single AI call.
     * Returns translated/humanized text for each section present.
     * Caches result for 30 days per drug+language combination.
     */
    public function humanizeDrug(string $drugId, string $drugName, array $sections, string $language = 'English'): array
    {
        if (empty($this->apiKey)) {
            return [
                'success' => false,
                'error' => 'AI service is not configured. Please add your GROQ_API_KEY to .env',
            ];
        }

        $langSlug = \Illuminate\Support\Str::slug($language);
        $cacheKey = "ai_humanize_drug_{$drugId}_{$langSlug}";

        if (Cache::has($cacheKey)) {
            return ['success' => true, 'sections' => Cache::get($cacheKey), 'cached' => true];
        }

        // Build the sections content for the prompt (only non-empty sections)
        $sectionLines = [];
        $sectionLabels = [
            'uses'         => 'Uses & Indications',
            'warnings'     => 'Warnings & Precautions',
            'dosage'       => 'Dosage & Administration',
            'side_effects' => 'Side Effects',
            'interactions' => 'Drug Interactions',
        ];

        foreach ($sections as $key => $text) {
            if (!empty(trim((string) $text)) && isset($sectionLabels[$key])) {
                $truncated = substr(strip_tags((string) $text), 0, 800);
                $label = $sectionLabels[$key];
                $sectionLines[] = "### {$label}\n{$truncated}";
            }
        }

        if (empty($sectionLines)) {
            return ['success' => false, 'error' => 'No drug sections provided.'];
        }

        $sectionsText = implode("\n\n", $sectionLines);

        $prompt = <<<PROMPT
You are a clinical medical information humanizer. Your task is to rewrite each section of a drug monograph for {$drugName} into clear, patient-friendly, easy-to-understand language.

Rules:
- Use plain language (no heavy medical jargon — explain any necessary terms in parentheses)
- Keep each section concise but informative
- Use short bullet points where appropriate
- Do NOT give personalized medical advice or diagnose conditions
- Be accurate and educational
- IMPORTANT: You MUST translate and write ALL output in the following language: {$language}

Return your response in this EXACT JSON format (no extra text outside the JSON):
{
  "uses": "humanized text or null if section not provided",
  "warnings": "humanized text or null if section not provided",
  "dosage": "humanized text or null if section not provided",
  "side_effects": "humanized text or null if section not provided",
  "interactions": "humanized text or null if section not provided"
}

Drug Monograph Sections to Humanize:
{$sectionsText}

Respond ONLY with valid JSON, all content written in {$language}:
PROMPT;

        try {
            $response = Http::timeout(45)
                ->withToken($this->apiKey)
                ->post("{$this->apiBase}/chat/completions", [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a precise medical information humanizer. Return only valid JSON. Translate all content to the requested language.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'max_tokens' => 2000,
                'temperature' => 0.35,
                'response_format' => ['type' => 'json_object'],
            ]);

            if ($response->failed()) {
                Log::error('AI Humanizer (Groq) error', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return ['success' => false, 'error' => 'AI service returned an error. Please try again later.'];
            }

            $rawContent = $response->json('choices.0.message.content', '');

            if (empty($rawContent)) {
                return ['success' => false, 'error' => 'No response from AI.'];
            }

            // Parse JSON response
            $parsed = json_decode($rawContent, true);
            if (json_last_error() !== JSON_ERROR_NONE || !is_array($parsed)) {
                Log::warning('AI Humanizer JSON parse error', ['raw' => substr($rawContent, 0, 500)]);
                return ['success' => false, 'error' => 'AI returned an unexpected format.'];
            }

            // Merge with nulls for missing sections
            $result = array_merge([
                'uses' => null, 'warnings' => null, 'dosage' => null,
                'side_effects' => null, 'interactions' => null,
            ], $parsed);

            Cache::put($cacheKey, $result, now()->addDays(30));
            return ['success' => true, 'sections' => $result, 'cached' => false];

        } catch (\Exception $e) {
            Log::error('AiSimplifierService humanizeDrug error', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => 'Failed to reach AI service. Please try again later.'];
        }
    }
}