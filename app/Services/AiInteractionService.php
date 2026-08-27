<?php

namespace App\Services;

use App\Models\Drug;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AiInteractionService
{
    private GroqService $groq;
    private string $model;

    public function __construct(GroqService $groq)
    {
        $this->groq = $groq;
        // Use the highly capable compound model via Groq for excellent medical reasoning
        $this->model = 'openai/gpt-oss-120b';
    }

    /**
     * Mode 1: Summarization – DB data exists, ask AI to summarise.
     */
    public function summarize(Drug $drugA, Drug $drugB, string $rawInteractionsText): array
    {
        $cacheKey = 'ai_interaction_sum_v2_' . md5("{$drugA->id}_{$drugB->id}_{$rawInteractionsText}");
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $prompt = <<<PROMPT
You are a medical safety summarizer. Summarize the known interaction risks between "{$drugA->name}" and "{$drugB->name}" using ONLY the text below.
Do NOT invent risks not present in the text. Keep language simple and patient-friendly.
Also determine the risk level: minor, moderate, or major.

Text:
"""
{$rawInteractionsText}
"""

Respond ONLY in valid JSON (no markdown, no extra text):
{"risk_level":"minor|moderate|major|unknown","summary":"1-2 sentence plain-language summary."}
PROMPT;

        return $this->callAI($prompt, $cacheKey, 0.2, 300);
    }

    /**
     * Mode 2: Inference Fallback – no DB data, reason by drug class and OpenFDA label texts.
     */
    public function infer(Drug $drugA, Drug $drugB): array
    {
        $ids = [$drugA->id, $drugB->id];
        sort($ids);
        $cacheKey = 'ai_interaction_inf_v2_' . md5(implode('_', $ids));
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $classA = $drugA->drug_class ?? 'Unknown';
        $classB = $drugB->drug_class ?? 'Unknown';

        $warningsA = substr(strip_tags($drugA->warnings ?? 'No specific warnings available.'), 0, 800);
        $warningsB = substr(strip_tags($drugB->warnings ?? 'No specific warnings available.'), 0, 800);

        $prompt = <<<PROMPT
You are a pharmacology risk analyst. No direct database interaction was found between "{$drugA->name}" and "{$drugB->name}".
However, you have access to their pharmacological classes and key FDA label warnings:

Drug 1: "{$drugA->name}"
- Class: {$classA}
- Warnings Extract: {$warningsA}

Drug 2: "{$drugB->name}"
- Class: {$classB}
- Warnings Extract: {$warningsB}

Based on these drug classes and their individual warnings, analyze the likelihood of an interaction or compounded adverse effect. 
Give a cautious, conservative assessment based on the provided data.
Clearly state this is an inference based on individual drug profiles. Avoid definitive claims. Default to minor/unknown if no clear concern exists.

Respond ONLY in valid JSON (no markdown, no extra text):
{"risk_level":"minor|moderate|major|unknown","summary":"AI-generated inference based on individual FDA drug profiles: [2-3 sentence explanation combining reasoning from classes and warnings]"}
PROMPT;

        return $this->callAI($prompt, $cacheKey, 0.3, 400);
    }

    // ─────────────────────────────────────────────────────────────────────────

    private function callAI(string $prompt, string $cacheKey, float $temperature, int $maxTokens): array
    {
        try {
            $text = $this->groq->chat($this->model, [
                ['role' => 'system', 'content' => 'You are a medical safety API. Always respond with raw JSON only. Format: {"risk_level":"...","summary":"..."}'],
                ['role' => 'user', 'content' => $prompt],
            ], [
                'temperature' => $temperature,
                'max_tokens' => $maxTokens,
                'response_format' => ['type' => 'json_object'] // Ensure strict JSON output
            ]);

            if (!$text) {
                return $this->fallback('unknown', 'AI service returned empty response.');
            }

            // Strip possible markdown fences just in case
            $text = preg_replace('/^```(?:json)?\s*|\s*```$/m', '', trim($text));

            $parsed = json_decode($text, true);
            if (!$parsed || !isset($parsed['risk_level'], $parsed['summary'])) {
                Log::warning('AI Interaction unparseable', ['text' => $text]);
                return $this->fallback('unknown', 'Could not parse AI response.');
            }

            Cache::put($cacheKey, $parsed, now()->addDays(30));
            return $parsed;
        }
        catch (\Exception $e) {
            Log::error('AiInteractionService error', ['error' => $e->getMessage()]);
            return $this->fallback('unknown', 'Failed to reach AI service for interaction checking.');
        }
    }

    private function fallback(string $riskLevel, string $summary): array
    {
        return ['risk_level' => $riskLevel, 'summary' => $summary];
    }
}