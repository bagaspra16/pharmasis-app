<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DrugSearchService;
use App\Services\OpenFdaService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SearchController extends Controller
{
    public function __construct(
        private DrugSearchService $searchService,
        private OpenFdaService $fdaService,
        )
    {
    }

    public function search(Request $request): JsonResponse
    {
        $query = (string)$request->get('q', '');
        $filters = $request->only(['drug_class', 'alpha', 'translated', 'order']);
        $perPage = min((int)$request->get('per_page', 15), 50);

        try {
            $results = $this->searchService->search($query, $filters, $perPage);
            return response()->json([
                'success' => true,
                'source' => 'db',
                'data' => $results->items(),
                'meta' => [
                    'current_page' => $results->currentPage(),
                    'last_page' => $results->lastPage(),
                    'per_page' => $results->perPage(),
                    'total' => $results->total(),
                    'query' => $query,
                ],
            ]);
        }
        catch (\Exception $e) {
            // Fallback to OpenFDA
            try {
                $fdaResults = $this->fdaService->search($query, $perPage);
                return response()->json([
                    'success' => true,
                    'source' => 'openfda',
                    'data' => $fdaResults,
                    'meta' => [
                        'current_page' => 1,
                        'last_page' => 1,
                        'per_page' => $perPage,
                        'total' => count($fdaResults),
                        'query' => $query,
                    ],
                ]);
            }
            catch (\Exception $fdaException) {
                return response()->json([
                    'success' => false,
                    'data' => [],
                    'error' => 'All data sources temporarily unavailable.',
                ], 503);
            }
        }
    }

    public function instant(Request $request): JsonResponse
    {
        $query = (string)$request->get('q', '');

        try {
            $results = $this->searchService->instant($query);
            return response()->json(['success' => true, 'source' => 'db', 'data' => $results]);
        }
        catch (\Exception $e) {
            // Fallback to OpenFDA instant
            try {
                $results = $this->fdaService->instant($query);
                return response()->json(['success' => true, 'source' => 'openfda', 'data' => $results]);
            }
            catch (\Exception $e2) {
                return response()->json(['success' => true, 'source' => 'none', 'data' => []]);
            }
        }
    }
}