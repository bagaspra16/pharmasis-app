<?php

namespace App\Services;

use App\Models\Drug;
use Illuminate\Support\Collection;

class InteractionService
{
    private AiInteractionService $aiService;

    public function __construct(AiInteractionService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Check interactions between 2 to 10 drugs.
     */
    public function check(array $drugIds): array
    {
        $drugs = Drug::whereIn('id', $drugIds)->get()->keyBy('id');

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

            // Database lookup
            $foundInteractions = $this->lookupInteractions($drugA, $drugB);

            if ($foundInteractions) {
                // Mode 1: Summarize
                $aiResult = $this->aiService->summarize($drugA, $drugB, $foundInteractions);
                $coverage = 'database';
                $confidence = 'high';
                $coverageCount++;
            }
            else {
                // Mode 2: Inference Fallback
                $aiResult = $this->aiService->infer($drugA, $drugB);
                $coverage = 'ai_inferred';
                $confidence = 'low';
            }

            $riskLevel = $aiResult['risk_level'] ?? 'unknown';
            $maxRiskValue = max($maxRiskValue, $riskScores[$riskLevel]);

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

        $nameA = preg_quote(strtolower($drugA->name), '/');
        $genericA = preg_quote(strtolower($drugA->generic_name ?? ''), '/');

        $nameB = preg_quote(strtolower($drugB->name), '/');
        $genericB = preg_quote(strtolower($drugB->generic_name ?? ''), '/');

        $patternA = "/\b($nameB" . ($genericB ? "|$genericB" : "") . ")\b/i";
        $patternB = "/\b($nameA" . ($genericA ? "|$genericA" : "") . ")\b/i";

        if (!empty($aInteractions) && preg_match($patternA, $aInteractions)) {
            $combinedText .= "Interactions on {$drugA->name} label: " . $this->extractContext($aInteractions, $patternA) . "\n";
        }

        if (!empty($bInteractions) && preg_match($patternB, $bInteractions)) {
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