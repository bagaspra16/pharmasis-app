<?php

namespace App\Services;

use App\Models\Drug;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiInteractionService
{
    private string $apiKey;
    private string $apiBase;
    private string $model;

    public function __construct()
    {
        $this->apiKey = config('services.openai.key', '');
        $this->apiBase = config('services.openai.base', 'https://api.scaleway.ai/72d6b375-b838-47b0-9fbb-ccf253147079/v1');
        $this->model = config('services.openai.model', 'llama-3.3-70b-instruct');
    }

    /**
     * Mode 1: Summarization – DB data exists, ask AI to summarise.
     */
    public function summarize(Drug $drugA, Drug $drugB, string $rawInteractionsText): array
    {
        if (empty($this->apiKey)) {
            return $this->fallback('moderate', 'Summary unavailable (AI API key not configured). Raw: ' . $rawInteractionsText);
        }

        $cacheKey = 'ai_interaction_sum_' . md5("{$drugA->id}_{$drugB->id}_{$rawInteractionsText}");
        if (Cache::has($cacheKey))
            return Cache::get($cacheKey);

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
        if (empty($this->apiKey)) {
            return $this->fallback('unknown', 'AI inference unavailable (API key not configured).');
        }

        $ids = [$drugA->id, $drugB->id];
        sort($ids);
        $cacheKey = 'ai_interaction_inf_' . md5(implode('_', $ids));
        if (Cache::has($cacheKey))
            return Cache::get($cacheKey);

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
            $response = Http::timeout(30)
                ->withToken($this->apiKey)
                ->post("{$this->apiBase}/chat/completions", [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a medical safety API. Always respond with raw JSON only, never markdown code fences.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'max_tokens' => $maxTokens,
                'temperature' => $temperature,
            ]);

            if ($response->failed()) {
                Log::error('AI Interaction error', ['status' => $response->status(), 'body' => $response->body()]);
                return $this->fallback('unknown', 'AI service returned an error.');
            }

            $text = $response->json('choices.0.message.content', '');
            // Strip possible markdown fences
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
            return $this->fallback('unknown', 'Failed to reach AI service.');
        }
    }

    private function fallback(string $riskLevel, string $summary): array
    {
        return ['risk_level' => $riskLevel, 'summary' => $summary];
    }
}