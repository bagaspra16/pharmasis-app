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
     * POST /api/v1/ai/simplify
     * Body: { drug_id, field, text, language }
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

    /**
     * POST /api/v1/ai/humanize-drug
     * Humanizes and translates ALL drug sections in one AI call.
     * Body: { drug_id, drug_name, language_code, language_name, sections: { uses, warnings, dosage, side_effects, interactions } }
     */
    public function humanizeDrug(Request $request): JsonResponse
    {
        $request->validate([
            'drug_id'       => 'required|string',
            'drug_name'     => 'required|string|max:200',
            'language_code' => 'nullable|string|max:10',
            'language_name' => 'nullable|string|max:50',
            'sections'      => 'required|array',
            'sections.uses'         => 'nullable|string',
            'sections.warnings'     => 'nullable|string',
            'sections.dosage'       => 'nullable|string',
            'sections.side_effects' => 'nullable|string',
            'sections.interactions' => 'nullable|string',
        ]);

        $language = $request->string('language_name')->value() ?: 'English';

        $result = $this->aiService->humanizeDrug(
            $request->string('drug_id'),
            $request->string('drug_name'),
            $request->input('sections', []),
            $language
        );

        return response()->json($result, $result['success'] ? 200 : 503);
    }
}