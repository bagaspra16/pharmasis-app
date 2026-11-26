<?php

namespace Tests\Feature;

use App\Services\Evidence\ClaimExtractor;
use App\Services\Evidence\EvidenceService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class EvidenceTest extends TestCase
{
    private const LABEL_TEXT = 'Do not exceed 3200 mg total daily dose of ibuprofen. Ibuprofen may cause stomach bleeding.';

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.groq.key' => 'test-key', 'evidence.enabled' => true]);
        Http::preventStrayRequests();
    }

    /** Fake the sources and make the verifier model return the given results. */
    private function fake(array $verifierResults, string $effective = '20250101', int $labelStatus = 200): void
    {
        Http::fake([
            'api.fda.gov/*' => Http::response(['results' => [[
                'set_id' => 'abc-123',
                'effective_time' => $effective,
                'openfda' => ['generic_name' => ['IBUPROFEN']],
                'dosage_and_administration' => [self::LABEL_TEXT],
            ]]], $labelStatus),
            'wsearch.nlm.nih.gov/*' => Http::response('<nlmSearchResult><list/></nlmSearchResult>'),
            'eutils.ncbi.nlm.nih.gov/*' => Http::response(['esearchresult' => ['idlist' => []]]),
            'api.groq.com/*' => Http::response(['choices' => [['message' => [
                'content' => json_encode(['results' => $verifierResults]),
            ]]]]),
        ]);
    }

    private function assess(string $claim): array
    {
        return app(EvidenceService::class)->assess(['explanation' => $claim], '', 'en', ['drugs' => ['ibuprofen']]);
    }

    private function cite(string $quote, string $source = 'S1'): array
    {
        return [['claim' => 1, 'supports' => [['source' => $source, 'quote' => $quote]], 'contradicts' => []]];
    }

    public function test_supported_claim_links_to_the_real_source(): void
    {
        $this->fake($this->cite('Do not exceed 3200 mg total daily dose of ibuprofen.'));

        $report = $this->assess('Do not take more than 3200 mg of ibuprofen daily.');

        $this->assertSame('verified', $report['status']);
        $this->assertSame('established', $report['claims'][0]['level']);
        $this->assertSame('https://dailymed.nlm.nih.gov/dailymed/drugInfo.cfm?setid=abc-123', $report['sources'][0]['url']);
        $this->assertSame('2025-01-01', $report['sources'][0]['date']);
        $this->assertArrayNotHasKey('passage', $report['sources'][0]);
    }

    public function test_quote_not_present_in_the_source_is_rejected(): void
    {
        $this->fake($this->cite('Ibuprofen is completely safe at any daily dose.'));

        $report = $this->assess('Ibuprofen is safe at any dose.');

        $this->assertSame('insufficient', $report['status']);
        $this->assertSame('unsupported', $report['claims'][0]['status']);
        $this->assertSame([], $report['sources']);
    }

    public function test_citation_of_an_unknown_source_is_rejected(): void
    {
        $this->fake($this->cite('Ibuprofen may cause stomach bleeding.', 'S9'));

        $this->assertSame('unsupported', $this->assess('Ibuprofen may cause stomach bleeding.')['claims'][0]['status']);
    }

    public function test_dose_that_differs_from_the_quote_is_not_supported(): void
    {
        $this->fake($this->cite('Do not exceed 3200 mg total daily dose of ibuprofen.'));

        $report = $this->assess('Do not take more than 6400 mg of ibuprofen daily.');

        $this->assertSame('unsupported', $report['claims'][0]['status']);
        $this->assertSame(1, $report['summary']['critical_unsupported']);
    }

    public function test_contradiction_is_reported_with_both_sides(): void
    {
        $results = $this->cite('Ibuprofen may cause stomach bleeding.');
        $results[0]['contradicts'] = [['source' => 'S1', 'quote' => 'Do not exceed 3200 mg total daily dose']];
        $this->fake($results);

        $report = $this->assess('Ibuprofen may cause stomach bleeding.');

        $this->assertSame('conflicting', $report['status']);
        $this->assertSame('conflicting', $report['claims'][0]['status']);
    }

    public function test_old_source_is_flagged_outdated(): void
    {
        $this->fake($this->cite('Ibuprofen may cause stomach bleeding.'), '20100101');

        $report = $this->assess('Ibuprofen may cause stomach bleeding.');

        $this->assertTrue($report['sources'][0]['stale']);
        $this->assertSame('outdated', $report['claims'][0]['level']);
    }

    public function test_source_outage_is_reported_as_unavailable(): void
    {
        $this->fake([], '20250101', 500);

        $report = $this->assess('Ibuprofen may cause stomach bleeding.');

        $this->assertSame('unavailable', $report['status']);
        $this->assertContains('openFDA', $report['failed_providers']);
        $this->assertSame('unchecked', $report['claims'][0]['status']);
    }

    public function test_no_matching_source_is_reported_as_insufficient(): void
    {
        $this->fake([], '20250101', 404);

        $this->assertSame('insufficient', $this->assess('Ibuprofen may cause stomach bleeding.')['status']);
    }

    public function test_only_short_search_terms_leave_the_server(): void
    {
        $this->fake([]);

        app(EvidenceService::class)->assess(
            ['explanation' => 'Ibuprofen may cause stomach bleeding.'],
            '',
            'en',
            ['drugs' => ['ibuprofen'], 'topics' => ['my name is budi and i live at jalan mawar 5']]
        );

        Http::assertNotSent(fn ($request) => !str_contains($request->url(), 'groq')
            && str_contains(urldecode($request->url()), 'budi'));
    }

    public function test_claims_are_extracted_with_critical_ones_first(): void
    {
        $claims = (new ClaimExtractor())->extract([
            'lifestyle' => ['Drink enough water during the day.'],
            'drugs' => [['name' => 'Ibuprofen', 'dose' => '400 mg', 'frequency' => '3x daily', 'contraindications' => ['kidney disease']]],
            'reassurance' => 'You are going to be perfectly fine.',
        ]);

        $this->assertSame('Ibuprofen: 400 mg, 3x daily', $claims[0]['text']);
        $this->assertTrue($claims[1]['critical']);
        $this->assertFalse($claims[2]['critical']);
        $this->assertCount(3, $claims);
    }

    public function test_ai_inferred_interaction_is_never_marked_supported(): void
    {
        $this->fake($this->cite('Ibuprofen may cause stomach bleeding.'));

        $report = app(EvidenceService::class)->assess(['interactions' => [[
            'drug_a' => 'Ibuprofen', 'drug_b' => 'Loratadine',
            'effect' => 'May cause stomach bleeding.', 'coverage' => 'ai_inferred',
        ]]], '', 'en', ['drugs' => ['ibuprofen']]);

        $this->assertSame('unsupported', $report['claims'][0]['status']);
    }

    public function test_dose_limit_sentence_survives_excerpt_selection(): void
    {
        config(['evidence.passage_chars' => 200]);
        $filler = str_repeat('Ibuprofen was studied in adults with stomach discomfort. ', 20);
        Http::fake([
            'api.fda.gov/*' => Http::response(['results' => [[
                'set_id' => 'abc', 'effective_time' => '20250101',
                'dosage_and_administration' => [$filler . 'Do not exceed 3200 mg total daily dose.'],
                'openfda' => ['generic_name' => ['IBUPROFEN']],
            ]]]),
            '*' => Http::response('', 500),
        ]);

        $sources = app(\App\Services\Evidence\EvidenceRetriever::class)
            ->retrieve(['drugs' => ['ibuprofen'], 'keywords' => ['stomach', 'adults']]);

        $this->assertStringContainsString('Do not exceed 3200 mg', $sources[0]['passage']);
    }

    public function test_screening_conclusion_endpoint_carries_evidence(): void
    {
        $this->fake([]);

        $this->postJson('/medicheck/conclude', ['symptoms' => 'mild headache for two days', 'role' => 'patient', 'lang' => 'en'])
            ->assertOk()
            ->assertJsonStructure(['data' => ['conclusion' => ['evidence' => ['status', 'claims', 'sources', 'labels']]]]);
    }

    public function test_evidence_panel_renders(): void
    {
        $html = view('partials.evidence-panel', ['expr' => 'results?.evidence'])->render();

        $this->assertStringContainsString('results?.evidence', $html);
        $this->assertStringNotContainsString('x-html', $html);
    }

    public function test_interaction_endpoint_returns_an_evidence_report(): void
    {
        $this->fake([]);

        $this->postJson('/api/v1/interactions/check', ['drug_ids' => ['fda-ibuprofen', 'fda-ibuprofen']])
            ->assertOk()
            ->assertJsonStructure(['data' => ['evidence' => ['status', 'claims', 'sources', 'summary', 'labels']]]);
    }
}
