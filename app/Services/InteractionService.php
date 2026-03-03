<?php

namespace App\Services;

use App\Models\Drug;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class InteractionService
{
    private AiInteractionService $aiService;
    private OpenFdaService $openFdaService;

    public function __construct(AiInteractionService $aiService, OpenFdaService $openFdaService)
    {
        $this->aiService = $aiService;
        $this->openFdaService = $openFdaService;
    }

    /**
     * Check interactions between 2 to 10 drugs.
     * Supports both local DB UUIDs and "fda-..." IDs from OpenFDA.
     */
    public function check(array $drugIds): array
    {
        $drugs = collect();

        // 1. Resolve drugs (Local DB or OpenFDA)
        foreach ($drugIds as $id) {
            if (Str::startsWith($id, 'fda-')) {
                // Remove 'fda-' prefix to get the slug
                $slug = substr($id, 4);
                $fdaData = $this->openFdaService->getBySlug($slug);
                if ($fdaData) {
                    // Create a simulated Drug model so AiInteractionService can process it normally
                    $drug = new Drug([
                        'id' => $fdaData['id'],
                        'name' => $fdaData['name'],
                        'generic_name' => $fdaData['generic_name'],
                        'drug_class' => $fdaData['drug_class'],
                        'uses' => $fdaData['uses'] ?? null,
                        'warnings' => $fdaData['warnings'] ?? null,
                        'before_taking' => $fdaData['before_taking'] ?? null,
                    ]);
                    // Temporarily store the clean interactions string for lookup
                    $drug->setAttribute('clean_interactions', $fdaData['interactions'] ?? '');
                    $drugs->push($drug);
                }
            }
            else {
                // Local DB fallback
                $dbDrug = Drug::find($id);
                if ($dbDrug) {
                    $drugs->push($dbDrug);
                }
            }
        }

        // Must have at least 2 resolved drugs to compare
        if ($drugs->count() < 2) {
            return [
                'pairs' => [],
                'overall_risk' => 'unknown',
                'coverage_status' => 'none',
                'disclaimer_required' => true,
                'error' => 'Not enough valid drugs found to check interactions.'
            ];
        }

        $pairs = $this->generatePairs($drugs);

        $results = [];
        $coverageCount = 0;
        $maxRiskValue = 0;

        $riskScores = [
            'unknown' => 0,
            'minor' => 1,
            'moderate' => 2,
            'major' => 3
        ];

        foreach ($pairs as $pair) {
            $drugA = $pair[0];
            $drugB = $pair[1];

            // Database/OpenFDA text lookup
            $foundInteractions = $this->lookupInteractions($drugA, $drugB);

            if ($foundInteractions) {
                // Mode 1: Summarize existing FDA interaction text
                $aiResult = $this->aiService->summarize($drugA, $drugB, $foundInteractions);
                $coverage = 'database';
                $confidence = 'high';
                $coverageCount++;
            }
            else {
                // Mode 2: Inference Fallback by drug class
                $aiResult = $this->aiService->infer($drugA, $drugB);
                $coverage = 'ai_inferred';
                $confidence = 'low';
            }

            $riskLevel = $aiResult['risk_level'] ?? 'unknown';
            $maxRiskValue = max($maxRiskValue, $riskScores[$riskLevel] ?? 0);

            $results[] = [
                'drug_a' => $drugA->name,
                'drug_b' => $drugB->name,
                'coverage' => $coverage,
                'risk_level' => $riskLevel,
                'summary' => $aiResult['summary'] ?? 'Unable to generate summary.',
                'confidence' => $confidence
            ];
        }

        // Determine overall metrics
        $overallRisk = array_search($maxRiskValue, $riskScores) ?: 'unknown';

        $coverageStatus = 'none';
        if ($coverageCount > 0) {
            $coverageStatus = $coverageCount === count($pairs) ? 'full' : 'partial';
        }

        return [
            'pairs' => $results,
            'overall_risk' => $overallRisk,
            'coverage_status' => $coverageStatus,
            'disclaimer_required' => true
        ];
    }

    /**
     * Generate combinatorial pairs (e.g. A,B,C -> AB, AC, BC)
     */
    private function generatePairs(Collection $drugs): array
    {
        $pairs = [];
        $drugArray = $drugs->values()->all();
        $count = count($drugArray);

        for ($i = 0; $i < $count; $i++) {
            for ($j = $i + 1; $j < $count; $j++) {
                $pairs[] = [$drugArray[$i], $drugArray[$j]];
            }
        }

        return $pairs;
    }

    /**
     * Find mentions of B in A's interactions, and A in B's interactions.
     * Uses case-insensitive regex matching.
     */
    private function lookupInteractions(Drug $drugA, Drug $drugB): ?string
    {
        $combinedText = '';

        $aInteractions = strip_tags($drugA->clean_interactions ?? '');
        $bInteractions = strip_tags($drugB->clean_interactions ?? '');

        // Safe regex quote handles potential nulls and weird characters
        $nameA = preg_quote(strtolower($drugA->name ?? ''), '/');
        $genericA = preg_quote(strtolower($drugA->generic_name ?? ''), '/');

        $nameB = preg_quote(strtolower($drugB->name ?? ''), '/');
        $genericB = preg_quote(strtolower($drugB->generic_name ?? ''), '/');

        $patternA = "/\b(" . trim("$nameB" . ($genericB ? "|$genericB" : ""), '|') . ")\b/i";
        $patternB = "/\b(" . trim("$nameA" . ($genericA ? "|$genericA" : ""), '|') . ")\b/i";

        if (!empty($aInteractions) && !empty($nameB) && preg_match($patternA, $aInteractions)) {
            $combinedText .= "Interactions on {$drugA->name} label: " . $this->extractContext($aInteractions, $patternA) . "\n";
        }

        if (!empty($bInteractions) && !empty($nameA) && preg_match($patternB, $bInteractions)) {
            $combinedText .= "Interactions on {$drugB->name} label: " . $this->extractContext($bInteractions, $patternB) . "\n";
        }

        return empty($combinedText) ? null : trim($combinedText);
    }

    /**
     * Extract surrounding sentence context for a regex match to avoid sending massive texts.
     */
    private function extractContext(string $text, string $pattern): string
    {
        // Split text into sentences roughly
        $sentences = preg_split('/(?<=[.!?])\s+/', $text);

        $matchedContexts = [];
        foreach ($sentences as $sentence) {
            if (preg_match($pattern, $sentence)) {
                $matchedContexts[] = trim($sentence);
            }
        }

        return implode(" ", array_slice($matchedContexts, 0, 3)); // Return up to 3 relevant sentences
    }
}