<?php

namespace Tests\Feature;

use App\Services\Quality\ResponseQuality;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class QualityTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config([
            'services.groq.key' => 'test-key', 'quality.enabled' => true, 'quality.model_review' => false,
            'evidence.enabled' => false, 'triage.enabled' => false, 'intake.enabled' => false,
        ]);
        Http::preventStrayRequests();
    }

    private function reply(array ...$contents): void
    {
        $sequence = Http::sequence();
        foreach ($contents as $content) {
            $sequence->push(['choices' => [['message' => ['content' => json_encode($content)]]]]);
        }
        Http::fake(['api.groq.com/*' => $sequence]);
    }

    public function test_wording_cases(): void
    {
        $cases = json_decode(file_get_contents(base_path('tests/Fixtures/quality_cases.json')), true)['wording'];

        foreach ($cases as $case) {
            $this->assertSame($case['expect'], app(ResponseQuality::class)->verdict($case['text'], $case['cautious']) ?? 'keep', $case['text']);
        }
    }

    public function test_unsafe_sentences_are_removed_and_lists_stay_lists(): void
    {
        [$response, $report] = app(ResponseQuality::class)->apply([
            'reassurance' => 'Do not worry at all.',
            'self_care' => ['You will be fine.', 'Drink water.'],
            'when_to_seek_care' => 'Wait a few days first. Go to hospital now if breathing gets harder.',
            'disclaimer' => 'Generic disclaimer.',
        ], 'cough', [], 'en', ['category' => 'urgent', 'title' => 'Urgent', 'meaning' => '']);

        $this->assertSame([
            'self_care' => ['Drink water.'],
            'when_to_seek_care' => 'Wait a few days first. Go to hospital now if breathing gets harder.',
        ], $response);
        $this->assertSame(2, $report['removed']);
        $this->assertContains(trans('quality.delay', [], 'en'), $report['notices']);
    }

    public function test_nothing_is_removed_from_warning_sign_fields(): void
    {
        [$response] = app(ResponseQuality::class)->apply(
            ['red_flags' => ['You will be fine.'], 'when_to_seek_care' => 'Do not worry.'], 'cough', [], 'en'
        );

        $this->assertSame(['red_flags' => ['You will be fine.'], 'when_to_seek_care' => 'Do not worry.'], $response);
    }

    public function test_advice_to_wait_is_left_in_clinician_output(): void
    {
        [$response] = app(ResponseQuality::class)->apply(
            ['mode' => 'doctor', 'management' => ['Refer if cough persists beyond 3 weeks.']],
            'cough', [], 'en', ['category' => 'urgent']
        );

        $this->assertCount(1, $response['management']);
    }

    public function test_limits_follow_the_case(): void
    {
        $quality = app(ResponseQuality::class);
        $limits = trans('quality.limits', [], 'en');

        [, $report] = $quality->apply(['self_care' => ['Take paracetamol.']], 'headache', [
            ['question' => 'Age?', 'answer' => "I don't know"], ['question' => 'Else?', 'answer' => ''],
        ], 'en');
        $this->assertSame([$limits['no_exam'], str_replace(':count', '2', $limits['unanswered']), $limits['medicine']], $report['limits']);

        [, $report] = $quality->apply(['explanation' => 'A cold is a viral infection.'], 'what is a cold', [], 'en', general: true);
        $this->assertSame([$limits['general']], $report['limits']);
    }

    public function test_only_contradictions_quoted_from_the_answer_are_shown(): void
    {
        config(['quality.model_review' => true]);
        $this->reply(['addresses_concern' => false, 'contradictions' => [
            ['a' => 'Take ibuprofen for the pain.', 'b' => 'Avoid ibuprofen completely.'],
            ['a' => 'Take ibuprofen for the pain.', 'b' => 'Something the answer never said.'],
        ], 'unsafe' => ['This is nothing serious.', 'Another sentence never said.']]);

        [$response, $report] = app(ResponseQuality::class)->apply(
            ['self_care' => ['Take ibuprofen for the pain.', 'Avoid ibuprofen completely.', 'This is nothing serious.']], 'knee pain', [], 'en'
        );

        // Off topic, one flagged sentence, one contradiction. Nothing is deleted on the model's word.
        $this->assertCount(3, $response['self_care']);
        $this->assertCount(3, $report['notices']);
        $this->assertStringContainsString('This is nothing serious.', $report['notices'][1]);
        $this->assertStringContainsString('Avoid ibuprofen completely.', $report['notices'][2]);
    }

    public function test_review_failure_is_disclosed(): void
    {
        config(['quality.model_review' => true]);
        Http::fake(['api.groq.com/*' => Http::response([], 500)]);

        [, $report] = app(ResponseQuality::class)->apply(['summary' => 'A cold is likely.'], 'cold', [], 'en');

        $this->assertContains(trans('quality.limits.review_failed', [], 'en'), $report['limits']);
    }

    public function test_conclusion_is_checked_and_the_urgency_reaches_the_writer(): void
    {
        config(['triage.enabled' => true, 'triage.allow_unreviewed' => true]);
        $this->reply(
            ['present' => [], 'other_symptoms' => false],
            ['mode' => 'patient', 'self_care' => ['Rest. You will be fine.']]
        );

        $this->postJson('/medicheck/conclude', [
            'symptoms' => 'fever', 'role' => 'patient', 'triage' => ['duration' => '3_to_7'],
        ])->assertOk()
            ->assertJsonPath('data.conclusion.triage.category', 'urgent')
            ->assertJsonPath('data.conclusion.self_care.0', 'Rest.')
            ->assertJsonPath('data.conclusion.quality.removed', 1);

        Http::assertSent(fn ($request) => str_contains($request->body(), 'rated urgency as: Urgent'));
    }

    public function test_off_by_default_without_the_flag(): void
    {
        config(['quality.enabled' => false]);
        $this->assertSame('', app(ResponseQuality::class)->rules());

        $this->reply(['mode' => 'patient', 'reassurance' => 'Do not worry.']);

        $this->postJson('/medicheck/conclude', ['symptoms' => 'cough', 'role' => 'patient'])
            ->assertOk()
            ->assertJsonPath('data.conclusion.reassurance', 'Do not worry.')
            ->assertJsonMissingPath('data.conclusion.quality');
    }
}
