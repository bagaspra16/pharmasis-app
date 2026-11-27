<?php

namespace Tests\Feature;

use App\Services\Intake\IntakeService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class IntakeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config([
            'services.groq.key' => 'test-key', 'evidence.enabled' => false,
            'triage.enabled' => true, 'triage.allow_unreviewed' => true, 'intake.enabled' => true,
        ]);
        Http::preventStrayRequests();
    }

    private function slots(array $plan): array
    {
        return array_map(fn ($q) => preg_replace('/^(triage|intake)_/', '', $q['id']), $plan);
    }

    public function test_scenarios_ask_what_is_needed_and_nothing_already_given(): void
    {
        $cases = json_decode(file_get_contents(base_path('tests/Fixtures/intake_scenarios.json')), true);

        foreach ($cases as $case) {
            $slots = $this->slots(app(IntakeService::class)->plan($case['text'], [], $case['lang']));

            $this->assertSame($case['ask'], $slots, $case['id']);
            $this->assertSame([], array_intersect($case['skip'], $slots), $case['id']);
        }
    }

    public function test_ambiguous_wording_is_asked_rather_than_assumed(): void
    {
        $intake = app(IntakeService::class);

        foreach (['Fever every 2 days for a week', 'Demam 2 hari sekali sudah seminggu', 'My weight dropped, berat badan turun', 'The pain is not severe', 'My son is 3 and I am 30 years old, my daughter is 5 years old'] as $text) {
            $known = $intake->known($text);
            $this->assertArrayNotHasKey('duration', $known, $text);
            $this->assertArrayNotHasKey('severity', $known, $text);
        }
        $this->assertArrayNotHasKey('age', $intake->known('I am 30 years old and my daughter is 5 years old'));
    }

    public function test_every_choice_question_allows_unknown_and_declining(): void
    {
        foreach (app(IntakeService::class)->plan('fever', [], 'en') as $q) {
            if (isset($q['values'])) {
                $this->assertContains('unknown', $q['values'], $q['id']);
                $this->assertContains('declined', $q['values'], $q['id']);
                $this->assertCount(count($q['values']), $q['choices'], $q['id']);
            } else {
                $this->assertContains('Prefer not to say', $q['choices']);
            }
        }
    }

    public function test_symptoms_already_mentioned_are_not_offered_again(): void
    {
        $plan = app(IntakeService::class)->plan('Fever and I keep vomiting', [], 'en');
        $associated = collect($plan)->firstWhere('id', 'triage_associated');

        $this->assertNotContains('persistent_vomiting', $associated['values']);
    }

    public function test_pregnancy_is_not_offered_for_young_children(): void
    {
        $plan = app(IntakeService::class)->plan('cough', ['age' => '3_months_to_4'], 'en');

        $this->assertNotContains('pregnant', collect($plan)->firstWhere('id', 'triage_risk')['values']);
    }

    public function test_emergency_in_a_typed_answer_interrupts_without_a_model_call(): void
    {
        Http::fake();

        $this->postJson('/medicheck/next', [
            'symptoms' => 'runny nose', 'lang' => 'en',
            'qa' => [['question' => 'Anything else?', 'answer' => 'now I have chest pain']],
        ])->assertOk()->assertJsonPath('triage.category', 'emergency')->assertJsonPath('questions', []);

        Http::assertNothingSent();
    }

    public function test_emergency_combination_from_selected_answers_interrupts(): void
    {
        $this->postJson('/medicheck/next', ['symptoms' => 'I have dengue', 'triage' => ['associated' => ['gum_nose_bleed']]])
            ->assertOk()->assertJsonPath('triage.category', 'emergency');
    }

    public function test_medicine_answer_is_not_read_as_a_symptom(): void
    {
        $this->postJson('/medicheck/next', [
            'symptoms' => 'runny nose', 'lang' => 'en',
            'qa' => [['question' => trans('intake.questions.meds', [], 'en'), 'answer' => 'seizure medicine']],
        ])->assertOk()->assertJsonMissingPath('triage');
    }

    public function test_duration_from_first_message_reaches_the_assessment_and_dont_know_blocks_routine(): void
    {
        Http::fake(['api.groq.com/*' => Http::response(['choices' => [['message' => [
            'content' => json_encode(['present' => [], 'other_symptoms' => false, 'mode' => 'patient']),
        ]]]])]);

        $this->postJson('/medicheck/conclude', ['symptoms' => 'fever for four days', 'role' => 'patient'])
            ->assertOk()->assertJsonPath('data.conclusion.triage.category', 'urgent');

        $this->postJson('/medicheck/conclude', [
            'symptoms' => 'runny nose since yesterday', 'role' => 'patient',
            'triage' => ['red_flags' => ['unknown'], 'age' => '18_to_64', 'risk' => ['none']],
        ])->assertOk()->assertJsonPath('data.conclusion.triage.category', 'unable_to_assess');
    }

    public function test_screen_asks_no_ai_questions_for_a_recognised_complaint(): void
    {
        Http::fake();

        $this->postJson('/medicheck/screen', ['symptoms' => 'fever', 'lang' => 'en'])
            ->assertOk()->assertJsonPath('adaptive', true)->assertJsonPath('questions.0.triage_key', 'red_flags');

        Http::assertNothingSent();
    }

    public function test_off_by_default_without_the_flag(): void
    {
        config(['intake.enabled' => false]);

        $this->postJson('/medicheck/next', ['symptoms' => 'fever'])->assertNotFound();
    }
}
