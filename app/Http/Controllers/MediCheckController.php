<?php

namespace App\Http\Controllers;

use App\Models\Drug;
use App\Models\FdaDrug;
use App\Services\AnalyticsRecorder;
use App\Services\GroqService;
use App\Services\MedicalPipelineService;
use App\Services\ScreeningService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MediCheckController extends Controller
{
    public function __construct(
        private GroqService $groq,
        private MedicalPipelineService $pipeline,
        private ScreeningService $screening
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
            'mode'       => 'nullable|in:classic,journey',
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
            $mode    = $request->input('mode', 'classic');
            $profile = $request->only(['age', 'weight', 'gender', 'allergies', 'conditions', 'medications', 'location']);

            // ── Run pipeline ────────────────────────────────────────────────────
            $result = $this->pipeline->run($symptoms, $lang, $profile, $mode);

            // ── Cross-reference drugs with DB ───────────────────────────────────
            if (isset($result['step2']['drugs']) && is_array($result['step2']['drugs'])) {
                $this->crossReferenceDrugs($result['step2']['drugs']);
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
     * Transcribe an audio recording to text (Whisper), used to seed the first
     * screening prompt from voice input.
     * POST /medicheck/transcribe
     */
    public function transcribe(Request $request)
    {
        $request->validate([
            'audio' => 'required|file|mimes:webm,ogg,mp4,wav,m4a,mp3|max:20480',
        ]);

        try {
            $audioFile = $request->file('audio');
            if (!$audioFile->isValid()) {
                return response()->json(['error' => 'Invalid audio file.'], 422);
            }

            $text = $this->groq->transcribe($audioFile->getPathname(), $audioFile->getMimeType() ?: 'audio/webm');

            if (empty(trim((string) $text))) {
                return response()->json(['error' => 'Could not transcribe audio. Please try again or type your complaint.'], 422);
            }

            AnalyticsRecorder::medicheck($request, 'transcribe', ['success' => true]);
            return response()->json(['success' => true, 'text' => $text]);

        } catch (\RuntimeException $e) {
            Log::error('MediCheck transcribe error', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Voice transcription is temporarily unavailable. Please type your complaint.'], 503);
        } catch (\Exception $e) {
            Log::error('MediCheck transcribe unexpected error', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'An unexpected error occurred. Please type your complaint.'], 500);
        }
    }

    /**
     * Generate tailored screening questions for the patient's first complaint.
     * The fixed role question (doctor vs patient) is appended here so its shape
     * is guaranteed regardless of the AI output.
     * POST /medicheck/screen
     */
    public function screen(Request $request)
    {
        $startedAt = microtime(true);
        $request->validate([
            'symptoms' => 'required|string|max:2000',
            'lang'     => 'nullable|in:en,id,auto',
        ]);

        try {
            $symptoms = trim($request->input('symptoms'));
            $lang     = $request->input('lang', 'auto');

            $questions = $this->screening->generateQuestions($symptoms, $lang);
            $questions[] = $this->roleQuestion($this->resolveLang($lang, $symptoms));

            AnalyticsRecorder::medicheck($request, 'screen', [
                'questions_generated' => count($questions),
                'success'     => true,
                'duration_ms' => (int) round((microtime(true) - $startedAt) * 1000),
            ]);

            return response()->json(['success' => true, 'questions' => $questions]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (\RuntimeException $e) {
            Log::error('MediCheck screen error', ['message' => $e->getMessage()]);
            AnalyticsRecorder::medicheck($request, 'screen', ['success' => false, 'error_code' => 'ai_unavailable']);
            return response()->json(['error' => 'AI service is temporarily unavailable. Please try again in a moment.'], 503);
        } catch (\Exception $e) {
            Log::error('MediCheck screen unexpected error', ['message' => $e->getMessage()]);
            AnalyticsRecorder::medicheck($request, 'screen', ['success' => false, 'error_code' => 'unexpected']);
            return response()->json(['error' => 'An unexpected error occurred. Please try again.'], 500);
        }
    }

    /**
     * Produce the final conclusion from the screening transcript, branched by
     * the user's role (doctor vs patient).
     * POST /medicheck/conclude
     */
    public function conclude(Request $request)
    {
        $startedAt = microtime(true);
        $request->validate([
            'symptoms'      => 'required|string|max:2000',
            'qa'            => 'nullable|array|max:10',
            'qa.*.question' => 'nullable|string|max:1000',
            'qa.*.answer'   => 'nullable|string|max:2000',
            'role'          => 'required|in:doctor,patient',
            'lang'          => 'nullable|in:en,id,auto',
        ]);

        try {
            $symptoms = trim($request->input('symptoms'));
            $role     = $request->input('role');
            $lang     = $request->input('lang', 'auto');
            $qa       = $request->input('qa', []);

            $conclusion = $this->screening->conclude($symptoms, $qa, $role, $lang);

            // Cross-reference clinical drug list with the DB (doctor branch only).
            if ($role === 'doctor' && isset($conclusion['drugs']) && is_array($conclusion['drugs'])) {
                $this->crossReferenceDrugs($conclusion['drugs']);
            }

            $result = [
                'symptoms'   => $symptoms,
                'lang'       => $lang,
                'role'       => $role,
                'qa'         => $qa,
                'conclusion' => $conclusion,
                'timestamp'  => now()->toISOString(),
            ];

            // ── Save to session history (max 5) ─────────────────────────────────
            $history = session('medicheck_history', []);
            array_unshift($history, [
                'id'               => (string) Str::uuid(),
                'timestamp'        => now()->format('Y-m-d H:i:s'),
                'symptoms_preview' => Str::limit($symptoms, 80),
                'conditions'       => $role === 'doctor'
                    ? collect($conclusion['differential'] ?? [])->pluck('name')->take(2)->all()
                    : array_filter([$conclusion['assumed_condition']['name'] ?? null]),
                'result'           => $result,
            ]);
            $history = array_slice($history, 0, 5);
            session(['medicheck_history' => $history]);

            AnalyticsRecorder::medicheck($request, 'conclude', [
                'role'        => $role,
                'success'     => true,
                'duration_ms' => (int) round((microtime(true) - $startedAt) * 1000),
            ]);

            return response()->json(['success' => true, 'data' => $result]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (\RuntimeException $e) {
            Log::error('MediCheck conclude error', ['message' => $e->getMessage()]);
            AnalyticsRecorder::medicheck($request, 'conclude', ['success' => false, 'error_code' => 'ai_unavailable']);
            return response()->json(['error' => 'AI service is temporarily unavailable. Please try again in a moment.'], 503);
        } catch (\Exception $e) {
            Log::error('MediCheck conclude unexpected error', ['message' => $e->getMessage()]);
            AnalyticsRecorder::medicheck($request, 'conclude', ['success' => false, 'error_code' => 'unexpected']);
            return response()->json(['error' => 'An unexpected error occurred. Please try again.'], 500);
        }
    }

    /**
     * Resolve 'auto' to 'en' or 'id' with a light heuristic on the input text,
     * so the appended role question matches the language the AI will answer in.
     */
    private function resolveLang(string $lang, string $text): string
    {
        if ($lang === 'en' || $lang === 'id') return $lang;

        $t = ' ' . mb_strtolower($text) . ' ';
        $idHits = 0;
        foreach ([
            ' saya ', ' aku ', ' yang ', ' dan ', ' tidak ', ' dengan ', ' sakit ',
            ' demam ', ' batuk ', ' sejak ', ' nyeri ', ' hari ', ' terasa ', ' sudah ',
            ' kepala ', ' perut ', ' badan ', ' mual ', ' pusing ', ' sesak ', ' lemas ',
            ' minum ', ' obat ', ' dokter ', ' tolong ', ' bantu ', ' ada ', ' juga ',
        ] as $w) {
            if (str_contains($t, $w)) $idHits++;
        }
        return $idHits >= 1 ? 'id' : 'en';
    }

    /**
     * The fixed final screening question — doctor vs patient. Localized by lang.
     */
    private function roleQuestion(string $lang): array
    {
        $id = $lang !== 'en';
        return [
            'id'             => 'role',
            'question'       => $id
                ? 'Sebelum kami simpulkan — apakah Anda seorang tenaga kesehatan/dokter, atau pasien/masyarakat umum?'
                : 'Before we conclude — are you a healthcare professional/doctor, or a patient/member of the public?',
            'hint'           => $id
                ? 'Jawaban Anda menentukan tingkat kedetailan medis yang kami tampilkan.'
                : 'Your answer determines the level of medical detail we present.',
            'choices'        => $id
                ? ['Tenaga kesehatan / Dokter', 'Pasien / Masyarakat umum']
                : ['Healthcare professional / Doctor', 'Patient / General public'],
            'allow_multiple' => false,
            'free_text'      => false,
            'is_role'        => true,
        ];
    }

    /**
     * Attach db_url / db_found to each AI-recommended drug by matching against
     * the local Drug table, then the FDA table, then a search fallback.
     */
    private function crossReferenceDrugs(array &$drugs): void
    {
        foreach ($drugs as &$drug) {
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
                'openai/gpt-oss-120b',
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
