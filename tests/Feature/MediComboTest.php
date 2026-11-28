<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MediComboTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config([
            'services.groq.key' => 'test-key',
            'triage.enabled' => false,
            'evidence.enabled' => false,
        ]);
        Http::preventStrayRequests();
    }

    public function test_medi_check_route_returns_classification_without_duplicate_screening(): void
    {
        Http::fake([
            'api.groq.com/*' => Http::response([
                'choices' => [[
                    'message' => [
                        'content' => json_encode([
                            'classification' => 'medi_check',
                            'confidence' => 0.95,
                            'summary_title' => 'Fever and cough screening',
                            'detected_entities' => ['fever', 'cough'],
                            'reasoning' => 'User describes personal symptoms.',
                        ]),
                    ],
                ]],
            ], 200),
        ]);

        $response = $this->postJson('/medicheck/route', [
            'input' => 'I have a fever and cough since yesterday',
            'lang' => 'auto',
            'ui_lang' => 'id',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.classification', 'medi_check')
            ->assertJsonPath('data.resolved_lang', 'en')
            ->assertJsonMissingPath('data.screening_payload')
            ->assertJsonMissingPath('data.medical_facts_payload');
    }

    public function test_medi_combo_route_returns_facts_without_duplicate_screening(): void
    {
        Http::fakeSequence()
            ->push([
                'choices' => [[
                    'message' => [
                        'content' => json_encode([
                            'classification' => 'medi_combo',
                            'confidence' => 0.92,
                            'summary_title' => 'Paracetamol & headache screening',
                            'detected_entities' => ['paracetamol', 'headache'],
                            'reasoning' => 'User asks about a drug while reporting symptoms.',
                        ]),
                    ],
                ]],
            ], 200)
            ->push([
                'choices' => [[
                    'message' => [
                        'content' => json_encode([
                            'core_explanation' => 'Paracetamol is an analgesic and antipyretic.',
                            'key_points' => [
                                ['heading' => 'Dosage', 'content' => '500-1000 mg every 4-6 hours, max 4g/day.'],
                            ],
                        ]),
                    ],
                ]],
            ], 200)
            ->push([
                'choices' => [[
                    'message' => [
                        'content' => json_encode([
                            'patient_mode' => [
                                'explanation' => 'Suitable for mild-to-moderate headache if no liver disease.',
                                'self_care_tips' => ['Stay hydrated'],
                                'recommended_specialist' => 'General Practitioner',
                            ],
                            'clinician_mode' => [
                                'differential_diagnoses' => [],
                                'pathophysiology' => 'COX inhibition in CNS.',
                                'recommended_workup' => [],
                                'drug_regimen_considerations' => ['Avoid >4g/24h'],
                            ],
                        ]),
                    ],
                ]],
            ], 200);

        $response = $this->postJson('/medicheck/route', [
            'input' => 'Can I take paracetamol for my headache since this morning?',
            'lang' => 'auto',
            'ui_lang' => 'id',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.classification', 'medi_combo')
            ->assertJsonPath('data.resolved_lang', 'en')
            ->assertJsonStructure(['data' => ['medical_facts_payload', 'conclusions']])
            ->assertJsonMissingPath('data.screening_payload');
    }
}
