<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Drug;
use App\Services\AiSimplifierService;
use App\Services\AnalyticsRecorder;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AiController extends Controller
{
    public function __construct(private AiSimplifierService $aiService)
    {
    }

    /**
     * POST /api/ai/simplify
     * Body: { drug_id, field, text }
     */
    public function simplify(Request $request): JsonResponse
    {
        $startedAt = microtime(true);

        $request->validate([
            'drug_id' => 'required|string',
            'field' => 'required|string|in:uses,warnings,before_taking,dosage,side_effects,interactions',
            'text' => 'required|string|min:10',
            'language' => 'nullable|string|max:50',
        ]);

        $result = $this->aiService->simplify(
            $request->string('drug_id'),
            $request->string('field'),
            $request->string('text'),
            $request->string('language')->value() ?: 'English'
        );

        AnalyticsRecorder::simplifier($request, [
            'output_length' => isset($result['simplified']) && is_string($result['simplified'])
                ? mb_strlen($result['simplified'])
                : (isset($result['text']) && is_string($result['text']) ? mb_strlen($result['text']) : null),
            'cache_hit'   => (bool) ($result['cache_hit'] ?? $result['cached'] ?? false),
            'success'     => (bool) ($result['success'] ?? false),
            'error_code'  => empty($result['success']) ? substr((string) ($result['error'] ?? 'unknown'), 0, 64) : null,
            'duration_ms' => (int) round((microtime(true) - $startedAt) * 1000),
        ]);

        return response()->json($result, $result['success'] ? 200 : 503);
    }
}