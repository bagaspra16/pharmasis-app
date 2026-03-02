<?php

namespace App\Http\Controllers;

use App\DTOs\DrugDTO;
use App\Models\Drug;
use App\Services\DrugSearchService;
use App\Services\OpenFdaService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class DrugController extends Controller
{
    public function __construct(
        private DrugSearchService $searchService,
        private OpenFdaService $fdaService,
        )
    {
    }

    // ─── Homepage ─────────────────────────────────────────────────────────────

    public function home()
    {
        $dbOffline = false;
        $fdaMode = false;
        $featured = collect();
        $alphaIndex = [];

        try {
            $featured = $this->searchService->getFeatured(8);
            $alphaIndex = $this->searchService->getAlphaIndex();
        }
        catch (\Exception $e) {
            $dbOffline = true;
            $fdaMode = true;
            Log::warning('DB offline, falling back to OpenFDA on homepage.');

            $fdaData = $this->fdaService->popular();
            $featured = collect(array_map(fn($d) => DrugDTO::fromArray($d), $fdaData));
        }

        return view('home', compact('featured', 'alphaIndex', 'dbOffline', 'fdaMode'));
    }

    // ─── Drug detail (from local DB) ──────────────────────────────────────────

    public function show(string $id)
    {
        try {
            $drug = Drug::findOrFail($id);
            $fdaMode = false;
            return view('drugs.show', compact('drug', 'fdaMode'));
        }
        catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            abort(404, 'Medicine not found.');
        }
        catch (\Exception $e) {
            Log::warning('DB offline on drug detail, redirecting offline page.');
            return view('errors.db_offline');
        }
    }

    // ─── Drug detail (from OpenFDA by slug) ───────────────────────────────────

    public function showFda(string $slug)
    {
        $data = $this->fdaService->getBySlug($slug);

        if (!$data) {
            return view('errors.db_offline')->with([
                'errorTitle' => 'Medicine Not Found',
                'errorMessage' => 'Could not find this medicine in the OpenFDA database. Try searching again.',
            ]);
        }

        $drug = DrugDTO::fromArray($data);
        $fdaMode = true;
        return view('drugs.show', compact('drug', 'fdaMode'));
    }

    // ─── Search / Browse ──────────────────────────────────────────────────────

    public function searchPage(Request $request)
    {
        $query = (string)$request->get('q', '');
        $filters = $request->only(['drug_class', 'alpha', 'translated', 'order']);
        $dbOffline = false;
        $fdaMode = false;
        $results = null;
        $fdaResults = [];
        $classes = [];
        $alphaIndex = [];

        try {
            $results = $this->searchService->search($query, $filters, 18);
            $classes = $this->searchService->getClasses();
            $alphaIndex = $this->searchService->getAlphaIndex();
        }
        catch (\Exception $e) {
            $dbOffline = true;
            $fdaMode = true;
            Log::warning('DB offline, falling back to OpenFDA on search page.');

            // Use OpenFDA for search results
            $fdaResults = $query
                ? $this->fdaService->search($query, 18)
                : $this->fdaService->popular();

            // Wrap as empty paginator (cards rendered separately for FDA)
            $results = new LengthAwarePaginator([], 0, 18, 1, ['path' => $request->url()]);
        }

        return view('drugs.index', compact(
            'results', 'query', 'filters', 'classes', 'alphaIndex',
            'dbOffline', 'fdaMode', 'fdaResults'
        ));
    }

    // ─── Interaction Checker ────────────────────────────────────────────────

    public function interactionPage()
    {
        return view('interactions.index');
    }
}