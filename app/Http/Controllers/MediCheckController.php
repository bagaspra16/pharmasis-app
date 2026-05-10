<?php

namespace App\Http\Controllers;

use App\Models\Drug;
use App\Models\FdaDrug;
use App\Services\AnalyticsRecorder;
use App\Services\GroqService;
use App\Services\MedicalPipelineService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MediCheckController extends Controller
{
    public function __construct(
        private GroqService $groq,
        private MedicalPipelineService $pipeline
    ) {}

    /**
     * Analyze symptoms (text or voice) through the 5-model pipeline.
     * POST /medicheck/analyze
     */
    public function analyze(Request $request)
    {
        $startedAt = microtime(true);
        $request->validate([
            'symptoms'   => 'nullable|string|max:2000',
            'audio'      => 'nullable|file|mimes:webm,ogg,mp4,wav,m4a,mp3|max:20480',
            'lang'       => 'nullable|in:en,id,auto',
            'age'        => 'nullable|integer|min:0|max:120',
            'weight'     => 'nullable|numeric|min:0|max:500',
            'gender'     => 'nullable|in:male,female,other',
            'allergies'  => 'nullable|string|max:500',
            'conditions' => 'nullable|string|max:500',
            'medications'=> 'nullable|string|max:500',
            'location'   => 'nullable|string|max:200',
        ]);

        try {
            // ── Step 0: STT if audio provided ───────────────────────────────────
            $symptoms = $request->input('symptoms', '');

            if ($request->hasFile('audio') && $request->file('audio')->isValid()) {
                $audioFile = $request->file('audio');
                $tempPath  = $audioFile->getPathname();
                $symptoms  = $this->groq->transcribe($tempPath, $audioFile->getMimeType() ?: 'audio/webm');

                if (empty($symptoms)) {
                    return response()->json(['error' => 'Could not transcribe audio. Please try again or type your symptoms.'], 422);
                }
            }

            if (empty(trim($symptoms))) {
                return response()->json(['error' => 'Please describe your symptoms — either by voice or text.'], 422);
            }

            $lang    = $request->input('lang', 'en');
            $profile = $request->only(['age', 'weight', 'gender', 'allergies', 'conditions', 'medications', 'location']);

            // ── Run pipeline ────────────────────────────────────────────────────
            $result = $this->pipeline->run($symptoms, $lang, $profile);

            // ── Cross-reference drugs with DB ───────────────────────────────────
            if (isset($result['step2']['drugs']) && is_array($result['step2']['drugs'])) {
                foreach ($result['step2']['drugs'] as &$drug) {
                    $name = $drug['name'] ?? '';
                    $generic = $drug['generic'] ?? '';
                    
                    if (empty($name) && empty($generic)) continue;

                    // 1. Try local DB
                    $localMatch = Drug::where('name', 'ILIKE', "%{$name}%")
                        ->orWhere('generic_name', 'ILIKE', "%{$generic}%")
                        ->first();
                        
                    if ($localMatch) {
                        $drug['db_url'] = route('drugs.show', $localMatch->id);
                        $drug['db_found'] = true;
                        continue;
                    }

                    // 2. Try FDA DB (if exists)
                    if (class_exists(FdaDrug::class)) {
                        $fdaMatch = FdaDrug::where('brand_name', 'ILIKE', "%{$name}%")
                            ->orWhere('generic_name', 'ILIKE', "%{$generic}%")
                            ->first();
                            
                        if ($fdaMatch) {
                            $drug['db_url'] = route('drugs.show_fda', $fdaMatch->slug);
                            $drug['db_found'] = true;
                            continue;
                        }
                    }

                    // 3. Fallback
                    $drug['db_url'] = route('drugs.search', ['q' => $name]);
                    $drug['db_found'] = false;
                }
            }

            // ── Save to session history (max 5) ─────────────────────────────────
            $history = session('medicheck_history', []);
            array_unshift($history, [
                'id'               => (string) Str::uuid(),
                'timestamp'        => now()->format('Y-m-d H:i:s'),
                'symptoms_preview' => Str::limit($symptoms, 80),
                'conditions'       => collect($result['step1']['conditions'] ?? [])->pluck('name')->take(2)->all(),
                'result'           => $result,
            ]);
            $history = array_slice($history, 0, 5);
            session(['medicheck_history' => $history]);

            AnalyticsRecorder::medicheck($request, 'analyze', [
                'pipeline_steps_completed' => is_array($result) ? count(array_filter(array_keys($result), fn($k) => str_starts_with((string) $k, 'step'))) : null,
                'success'     => true,
                'duration_ms' => (int) round((microtime(true) - $startedAt) * 1000),
            ]);

            return response()->json(['success' => true, 'data' => $result]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            AnalyticsRecorder::medicheck($request, 'analyze', [
                'success'     => false,
                'error_code'  => 'validation',
                'duration_ms' => (int) round((microtime(true) - $startedAt) * 1000),
            ]);
            return response()->json(['error' => $e->errors()], 422);
        } catch (\RuntimeException $e) {
            Log::error('MediCheckController analyze error', ['message' => $e->getMessage()]);
            AnalyticsRecorder::medicheck($request, 'analyze', [
                'success'     => false,
                'error_code'  => 'ai_unavailable',
                'duration_ms' => (int) round((microtime(true) - $startedAt) * 1000),
            ]);
            return response()->json(['error' => 'AI service is temporarily unavailable. Please try again in a moment.'], 503);
        } catch (\Exception $e) {
            Log::error('MediCheckController unexpected error', ['message' => $e->getMessage()]);
            AnalyticsRecorder::medicheck($request, 'analyze', [
                'success'     => false,
                'error_code'  => 'unexpected',
                'duration_ms' => (int) round((microtime(true) - $startedAt) * 1000),
            ]);
            return response()->json(['error' => 'An unexpected error occurred. Please try again.'], 500);
        }
    }

    /**
     * Get session history.
     * GET /medicheck/history
     */
    public function history(Request $request)
    {
        AnalyticsRecorder::medicheck($request, 'history_list');
        $history = session('medicheck_history', []);
        // Strip full result from list for performance
        $list = array_map(fn($h) => [
            'id'               => $h['id'],
            'timestamp'        => $h['timestamp'],
            'symptoms_preview' => $h['symptoms_preview'],
            'conditions'       => $h['conditions'],
        ], $history);

        return response()->json(['history' => $list]);
    }

    /**
     * Get a single history item with full result.
     * GET /medicheck/history/{id}
     */
    public function historyItem(Request $request, string $id)
    {
        AnalyticsRecorder::medicheck($request, 'history_view');
        $history = session('medicheck_history', []);
        foreach ($history as $item) {
            if ($item['id'] === $id) {
                return response()->json(['data' => $item['result']]);
            }
        }
        return response()->json(['error' => 'History item not found.'], 404);
    }
    /**
     * Find nearby healthcare providers based on location + conditions.
     * POST /medicheck/nearby
     */
    public function nearby(Request $request)
    {
        $startedAt = microtime(true);
        $request->validate([
            'location'   => 'required|string|max:200',
            'symptoms'   => 'nullable|string|max:500',
            'conditions' => 'nullable|string|max:500',
        ]);

        try {
            $location   = $request->input('location');
            $symptoms   = $request->input('symptoms', '');
            $conditions = $request->input('conditions', '');

            // Build Google Maps search URLs as guaranteed fallback
            $searchQuery = urlencode("hospital clinic near {$location}");
            $mapsUrl = "https://www.google.com/maps/search/{$searchQuery}";

            // Use Groq to suggest real facility names + addresses
            $prompt = "The patient is located in: {$location}.\n";
            if ($symptoms) $prompt .= "Symptoms: {$symptoms}\n";
            if ($conditions) $prompt .= "Likely conditions: {$conditions}\n";
            $prompt .= "\nSuggest 4 real hospitals or clinics in that exact city/area. "
                     . "Return JSON: {\"providers\":[{\"name\":\"...\",\"type\":\"Hospital|Clinic|Specialist\",\"address\":\"...\",\"contact\":\"phone or website if known, otherwise empty\"}],\"emergency_numbers\":\"local emergency number\"}";

            $raw = $this->groq->chat(
                'llama-3.3-70b-versatile',
                [
                    ['role' => 'system', 'content' => 'You are a local healthcare directory assistant. Return ONLY valid JSON. No conversational text.'],
                    ['role' => 'user',   'content' => $prompt],
                ],
                ['temperature' => 0.4, 'max_tokens' => 800]
            );

            // Parse the JSON, extract providers
            $clean = preg_replace('/```(?:json)?\s*([\s\S]*?)```/i', '$1', trim($raw ?? ''));
            $parsed = json_decode(trim($clean), true);

            // Attach a Google Maps search link to each provider
            $providers = $parsed['providers'] ?? [];
            foreach ($providers as &$p) {
                $q = urlencode(($p['name'] ?? '') . ' ' . ($p['address'] ?? '') . ' ' . $location);
                $p['maps_url'] = "https://www.google.com/maps/search/{$q}";
                // If no contact, leave empty — frontend will show Maps link
                if (empty($p['contact'])) {
                    $p['contact'] = '';
                }
            }

            AnalyticsRecorder::medicheck($request, 'nearby', [
                'providers_returned' => count($providers),
                'success'            => true,
                'duration_ms'        => (int) round((microtime(true) - $startedAt) * 1000),
            ]);

            return response()->json([
                'success'          => true,
                'providers'        => $providers,
                'emergency_numbers'=> $parsed['emergency_numbers'] ?? null,
                'maps_search_url'  => $mapsUrl,
            ]);

        } catch (\Exception $e) {
            Log::error('MediCheck nearby error', ['message' => $e->getMessage()]);
            AnalyticsRecorder::medicheck($request, 'nearby', [
                'providers_returned' => 0,
                'success'            => false,
                'error_code'         => 'unexpected',
                'duration_ms'        => (int) round((microtime(true) - $startedAt) * 1000),
            ]);
            // Return empty so frontend can still show Maps link
            $q = urlencode("hospital clinic near " . $request->input('location', ''));
            return response()->json([
                'success'          => false,
                'providers'        => [],
                'maps_search_url'  => "https://www.google.com/maps/search/{$q}",
            ]);
        }
    }
}
