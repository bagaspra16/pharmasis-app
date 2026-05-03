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
            'drug_ids.*' => 'string',
            // Optional: richer payload from UI to help OpenFDA fallback if DB is down
            'drugs' => 'sometimes|array|min:2|max:10',
            'drugs.*.id' => 'required_with:drugs|string',
            'drugs.*.name' => 'sometimes|nullable|string',
            'drugs.*.slug' => 'sometimes|nullable|string',
            'drugs.*.generic_name' => 'sometimes|nullable|string',
        ]);

        try {
            $metaById = [];
            foreach (($validated['drugs'] ?? []) as $d) {
                $id = (string)($d['id'] ?? '');
                if ($id === '') {
                    continue;
                }
                $metaById[$id] = [
                    'name' => $d['name'] ?? null,
                    'slug' => $d['slug'] ?? null,
                    'generic_name' => $d['generic_name'] ?? null,
                ];
            }

            $result = $interactionService->check($validated['drug_ids'], $metaById);

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