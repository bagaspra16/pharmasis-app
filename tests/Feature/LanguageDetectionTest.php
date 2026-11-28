<?php

namespace Tests\Feature;

use App\Services\LanguageDetector;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class LanguageDetectionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config([
            'services.groq.key' => 'test-key',
            'triage.enabled' => true,
            'triage.allow_unreviewed' => true,
            'evidence.enabled' => false,
        ]);
        Http::preventStrayRequests();
    }

    public function test_detects_indonesian_text(): void
    {
        $this->assertSame('id', LanguageDetector::detect('Saya mengalami demam tinggi dan batuk sejak kemarin'));
    }

    public function test_detects_english_text(): void
    {
        $this->assertSame('en', LanguageDetector::detect('I have had a sore throat and fever since yesterday'));
    }

    public function test_detects_japanese_text(): void
    {
        $this->assertSame('ja', LanguageDetector::detect('昨日から頭痛と発熱があります'));
    }

    public function test_resolve_respects_explicit_language_over_ui_lang(): void
    {
        $this->assertSame('id', LanguageDetector::resolve('id', 'I have a headache', 'en'));
    }

    public function test_resolve_auto_uses_input_language_instead_of_ui_lang(): void
    {
        $this->assertSame('en', LanguageDetector::resolve('auto', 'I have a severe headache and nausea', 'id'));
        $this->assertSame('id', LanguageDetector::resolve('auto', 'Kepala saya pusing dan mual sejak pagi', 'en'));
    }

    public function test_screen_endpoint_propagates_resolved_lang(): void
    {
        Http::fake([
            'api.groq.com/*' => Http::response([
                'choices' => [[
                    'message' => [
                        'content' => json_encode([
                            'questions' => [
                                [
                                    'id' => 'q1',
                                    'question' => 'How long have you had these symptoms?',
                                    'hint' => 'Select one',
                                    'choices' => ['1-2 days', '3+ days'],
                                    'allow_multiple' => false,
                                ],
                            ],
                        ]),
                    ],
                ]],
            ], 200),
        ]);

        $response = $this->postJson('/medicheck/screen', [
            'symptoms' => 'I have had a headache and mild fever since yesterday morning',
            'lang' => 'auto',
            'ui_lang' => 'id',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('resolved_lang', 'en');
    }
}
