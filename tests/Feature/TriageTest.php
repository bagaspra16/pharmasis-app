<?php

namespace Tests\Feature;

use App\Services\Triage\TriageEngine;
use App\Services\Triage\TriageService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TriageTest extends TestCase
{
    private const NONE = ['red_flags' => ['none'], 'age' => '18_to_64', 'duration' => 'under_3', 'risk' => ['none']];

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.groq.key' => 'test-key', 'triage.enabled' => true, 'triage.allow_unreviewed' => true, 'evidence.enabled' => false]);
        Http::preventStrayRequests();
    }

    private function fakeAi(array $content = ['present' => [], 'other_symptoms' => false], int $status = 200): void
    {
        Http::fake(['api.groq.com/*' => Http::response(['choices' => [['message' => ['content' => json_encode($content)]]]], $status)]);
    }

    private function category(string $complaint, array $answers = self::NONE, string $lang = 'en'): string
    {
        return app(TriageService::class)->assess($complaint, [], $answers, $lang)['category'];
    }

    public function test_every_rule_fires_for_its_findings(): void
    {
        $engine = app(TriageEngine::class);
        $rank = ['urgent' => 1, 'emergency' => 2];

        foreach ($engine->ruleset()['rules'] as $rule) {
            $findings = array_merge($rule['all'] ?? [], array_slice($rule['any'] ?? [], 0, 1));
            $result = $engine->assess($findings, ['duration' => $rule['duration'][0] ?? null, 'risk' => $rule['risk'] ?? null]);

            $this->assertGreaterThanOrEqual($rank[$rule['level']], $rank[$result['category']] ?? 0, $rule['id']);
        }
    }

    public function test_routine_needs_every_condition(): void
    {
        $this->fakeAi();
        $this->assertSame('routine', $this->category('I have a runny nose'));

        foreach (array_keys(self::NONE) as $key) {
            $this->assertSame('unable_to_assess', $this->category('I have a runny nose', array_diff_key(self::NONE, [$key => 1])), "missing {$key}");
        }
        $this->assertSame('unable_to_assess', $this->category('I have a runny nose', ['age' => '65_plus'] + self::NONE));
        $this->assertSame('unable_to_assess', $this->category('I have a runny nose', ['risk' => ['chronic']] + self::NONE));
        $this->assertSame('unable_to_assess', $this->category('I feel strange'));
        $this->assertSame('unable_to_assess', $this->category('I have a runny nose', self::NONE, 'fr'));
    }

    public function test_ai_failure_or_extra_symptom_blocks_routine(): void
    {
        $this->fakeAi(['present' => [], 'other_symptoms' => true]);
        $this->assertSame('unable_to_assess', $this->category('I have a runny nose and an odd lump'));
    }

    public function test_ai_outage_blocks_routine(): void
    {
        $this->fakeAi([], 500);
        $this->assertSame('unable_to_assess', $this->category('I have a runny nose'));
    }

    public function test_ai_can_raise_but_never_lower_urgency(): void
    {
        $this->fakeAi(['present' => ['chest_pain'], 'other_symptoms' => false]);
        $this->assertSame('emergency', $this->category('my ticker is playing up'));

        // The model reporting nothing does not remove a checklist answer.
        $this->fakeAi();
        $this->assertSame('emergency', $this->category('I have a runny nose', ['red_flags' => ['seizure']] + self::NONE));
    }

    public function test_negation_is_limited_to_the_phrase(): void
    {
        $engine = app(TriageEngine::class);

        $this->assertNotContains('fever', $engine->match('No fever, just tired'));
        $this->assertContains('chest_pain', $engine->match('No fever, but I have chest pain'));
        $this->assertContains('chest_pain', $engine->match('tidak demam tapi nyeri dada'));
        $this->assertContains('severe_breathing', $engine->match("I can't breathe"));
    }

    public function test_questions_are_not_read_as_symptoms(): void
    {
        $this->fakeAi();
        $result = app(TriageService::class)->assess('runny nose', [['question' => 'Any chest pain?', 'answer' => 'No']], self::NONE, 'en');

        $this->assertSame('routine', $result['category']);
    }

    public function test_emergency_text_escalates_without_any_model_call(): void
    {
        Http::fake();

        $this->postJson('/medicheck/screen', ['symptoms' => 'I have crushing chest pain', 'lang' => 'en'])
            ->assertOk()->assertJsonPath('triage.category', 'emergency')->assertJsonPath('questions', []);
        $this->postJson('/medicheck/route', ['input' => 'Saya nyeri dada', 'lang' => 'id'])
            ->assertOk()->assertJsonPath('data.classification', 'emergency_alert')->assertJsonPath('data.triage.category', 'emergency');
        $this->postJson('/medicheck/conclude', ['symptoms' => 'chest pain', 'role' => 'patient'])
            ->assertOk()->assertJsonPath('data.conclusion.triage.category', 'emergency');

        Http::assertNothingSent();
    }

    public function test_warning_sign_questions_come_first_even_if_the_ai_is_down(): void
    {
        $this->fakeAi([], 500);

        $this->postJson('/medicheck/screen', ['symptoms' => 'runny nose', 'lang' => 'en'])
            ->assertOk()
            ->assertJsonPath('questions.0.triage_key', 'red_flags')
            ->assertJsonPath('questions.0.escalation.category', 'emergency');
    }

    public function test_unreviewed_rules_stay_off(): void
    {
        config(['triage.allow_unreviewed' => false]);

        $this->assertFalse(app(TriageService::class)->isActive());
        $this->assertNull(app(TriageService::class)->emergencyFromText('chest pain', 'en'));
    }

    public function test_approval_is_void_when_rules_change(): void
    {
        $approval = ['reviewer' => 'A', 'qualification' => 'B', 'date' => '2026-01-01', 'ruleset_hash' => app(TriageEngine::class)->hash()];
        config(['triage.approval' => $approval]);
        $this->assertTrue(app(TriageEngine::class)->isApproved());

        config(['triage.approval.ruleset_hash' => 'old']);
        $this->assertFalse(app(TriageEngine::class)->isApproved());
    }

    public function test_result_never_names_a_disease_and_card_renders(): void
    {
        $this->fakeAi();
        $result = app(TriageService::class)->assess('I have a runny nose', [], [], 'en');

        $this->assertSame('unable_to_assess', $result['category']);
        $this->assertStringContainsString('does not mean you are safe', $result['meaning']);
        $this->assertNotEmpty($result['missing']);
        $this->assertStringNotContainsString('x-html', view('partials.triage-card', ['expr' => 'conclusion?.triage'])->render());
    }
}
