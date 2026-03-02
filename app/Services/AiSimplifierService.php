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
        $this->apiKey = config('services.openai.key', '');
        $this->apiBase = config('services.openai.base', 'https://api.scaleway.ai/72d6b375-b838-47b0-9fbb-ccf253147079/v1');
        $this->model = config('services.openai.model', 'llama-3.3-70b-instruct');
    }

    /**
     * Simplify a piece of medical text into plain language.
     * Caches result for 30 days.
     */
    public function simplify(string $drugId, string $field, string $text): array
    {
        if (empty($this->apiKey)) {
            return [
                'success' => false,
                'error' => 'AI service is not configured. Please add your OPENAI_API_KEY (Scaleway key) to .env',
            ];
        }

        $cacheKey = "ai_simplify_{$drugId}_{$field}";

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

Medical text to simplify:
"""
{$truncated}
"""

Simplified explanation:
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
                Log::error('AI Simplifier error', ['status' => $response->status()]);
                return ['success' => false, 'error' => 'AI service returned an error. Please try again later.'];
            }

            $simplified = $response->json('choices.0.message.content', '');

            if (empty($simplified)) {
                return ['success' => false, 'error' => 'No response from AI.'];
            }

            Cache::put($cacheKey, $simplified, now()->addDays(30));
            return ['success' => true, 'text' => $simplified, 'cached' => false];

        }
        catch (\Exception $e) {
            Log::error('AiSimplifierService error', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => 'Failed to reach AI service. Please try again later.'];
        }
    }
}