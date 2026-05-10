<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsRecorder;
use App\Services\InteractionService;
use Illuminate\Http\Request;

class InteractionController extends Controller
{
    public function check(Request $request, InteractionService $interactionService)
    {
        $startedAt = microtime(true);
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

            AnalyticsRecorder::interaction($request, [
                'severity_max'       => $this->extractMaxSeverity($result),
                'interactions_found' => $this->countInteractions($result),
                'cache_hit'          => (bool) ($result['cache_hit'] ?? $result['cached'] ?? false),
                'success'            => true,
                'duration_ms'        => (int) round((microtime(true) - $startedAt) * 1000),
            ]);

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

            AnalyticsRecorder::interaction($request, [
                'success'     => false,
                'error_code'  => 'unexpected',
                'duration_ms' => (int) round((microtime(true) - $startedAt) * 1000),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to check interactions. Please try again later.'
            ], 500);
        }
    }

    private function extractMaxSeverity($result): string
    {
        $allowed = ['none', 'minor', 'moderate', 'major', 'contraindicated'];
        $rank = array_flip($allowed);
        $best = -1;

        $items = is_array($result) ? ($result['interactions'] ?? $result['data']['interactions'] ?? []) : [];
        if (! is_array($items)) {
            return 'unknown';
        }

        foreach ($items as $item) {
            $sev = strtolower((string) ($item['severity'] ?? $item['level'] ?? ''));
            if (isset($rank[$sev]) && $rank[$sev] > $best) {
                $best = $rank[$sev];
            }
        }

        return $best >= 0 ? $allowed[$best] : 'unknown';
    }

    private function countInteractions($result): ?int
    {
        if (! is_array($result)) {
            return null;
        }
        $items = $result['interactions'] ?? $result['data']['interactions'] ?? null;
        return is_array($items) ? count($items) : null;
    }
}