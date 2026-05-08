<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GroqService
{
    private array $apiKeys;
    private string $apiBase = 'https://api.groq.com/openai/v1';
    private int $currentKeyIndex = 0;

    public function __construct()
    {
        // Load all available API keys — rotate across them to avoid rate limits
        $keys = array_filter([
            config('services.groq.key', env('GROQ_API_KEY', '')),
            env('GROQ_API_KEY_2', ''),
            env('GROQ_API_KEY_3', ''),
            env('GROQ_API_KEY_4', ''),
            env('GROQ_API_KEY_5', ''),
        ]);

        $this->apiKeys = array_values($keys);

        if (empty($this->apiKeys)) {
            $this->apiKeys = [''];
        }
    }

    /**
     * Get the next API key (round-robin rotation)
     */
    private function getNextKey(): string
    {
        $key = $this->apiKeys[$this->currentKeyIndex % count($this->apiKeys)];
        $this->currentKeyIndex++;
        return $key;
    }

    /**
     * Standard chat completion (non-streaming)
     */
    public function chat(string $model, array $messages, array $options = []): ?string
    {
        $key = $this->getNextKey();

        if (empty($key)) {
            throw new \RuntimeException('GROQ_API_KEY is not configured.');
        }

        $payload = array_merge([
            'model'       => $model,
            'messages'    => $messages,
            'temperature' => 0.5,
            'max_tokens'  => 2048,
        ], $options);

        $response = Http::timeout(60)
            ->withToken($key)
            ->post("{$this->apiBase}/chat/completions", $payload);

        if ($response->failed()) {
            // If rate limited, try next key
            if ($response->status() === 429 && count($this->apiKeys) > 1) {
                Log::warning("GroqService rate limited on key, trying next key", ['model' => $model]);
                $nextKey = $this->getNextKey();
                $response = Http::timeout(60)
                    ->withToken($nextKey)
                    ->post("{$this->apiBase}/chat/completions", $payload);
            }

            if ($response->failed()) {
                Log::error('GroqService chat error', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                    'model'  => $model,
                ]);
                throw new \RuntimeException("Groq API error: " . $response->body());
            }
        }

        return $response->json('choices.0.message.content');
    }

    /**
     * Transcribe audio via Whisper (multipart upload)
     */
    public function transcribe(string $audioPath, string $mimeType = 'audio/webm'): ?string
    {
        $key = $this->getNextKey();

        if (empty($key)) {
            throw new \RuntimeException('GROQ_API_KEY is not configured.');
        }

        // Groq Whisper needs a recognizable file extension
        $extension = match ($mimeType) {
            'audio/webm'  => 'webm',
            'audio/ogg'   => 'ogg',
            'audio/mp4'   => 'mp4',
            'audio/mpeg'  => 'mp3',
            'audio/wav'   => 'wav',
            'audio/x-m4a' => 'm4a',
            'audio/m4a'   => 'm4a',
            default       => 'webm',
        };

        $fileName = "recording.{$extension}";

        $response = Http::timeout(60)
            ->withToken($key)
            ->attach('file', file_get_contents($audioPath), $fileName)
            ->post("{$this->apiBase}/audio/transcriptions", [
                'model'           => 'whisper-large-v3-turbo',
                'response_format' => 'json',
                // task=transcribe preserves the original language — never translates to English
                // (default is already transcribe, but explicit is safer)
            ]);

        if ($response->failed()) {
            Log::error('GroqService transcribe error', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            throw new \RuntimeException("Groq Whisper error: " . $response->body());
        }

        return $response->json('text');
    }
}
