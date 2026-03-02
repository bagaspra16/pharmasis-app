<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Drug;
use App\Services\AiSimplifierService;
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
        $request->validate([
            'drug_id' => 'required|string',
            'field' => 'required|string|in:uses,warnings,before_taking,dosage,side_effects,interactions',
            'text' => 'required|string|min:10',
        ]);

        $result = $this->aiService->simplify(
            $request->string('drug_id'),
            $request->string('field'),
            $request->string('text'),
        );

        return response()->json($result, $result['success'] ? 200 : 503);
    }
}