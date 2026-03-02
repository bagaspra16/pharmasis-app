<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\InteractionService;
use Illuminate\Http\Request;

class InteractionController extends Controller
{
    public function check(Request $request, InteractionService $interactionService)
    {
        $validated = $request->validate([
            'drug_ids' => 'required|array|min:2|max:10',
            'drug_ids.*' => 'string'
        ]);

        try {
            $result = $interactionService->check($validated['drug_ids']);

            return response()->json([
                'success' => true,
                'data' => $result
            ]);
        }
        catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('API Interaction Check Failed', [
                'error' => $e->getMessage(),
                'drug_ids' => $validated['drug_ids'] ?? []
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to check interactions. Please try again later.'
            ], 500);
        }
    }
}