<?php

namespace Tests\Feature;

use App\Services\Security\BotGuard;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class BotGuardTest extends TestCase
{
    private const BROWSER = 'Mozilla/5.0 (Linux; Android 14) AppleWebKit/537.36 Chrome/130 Mobile Safari/537.36';

    private string $log;

    protected function setUp(): void
    {
        parent::setUp();
        $this->log = tempnam(sys_get_temp_dir(), 'sec');
        config([
            'botguard.mode' => 'enforce', 'botguard.difficulty' => 8, 'cache.default' => 'array',
            'logging.channels.security' => ['driver' => 'single', 'path' => $this->log, 'level' => 'info'],
            'intake.enabled' => false,
        ]);
        Cache::flush();
        Http::preventStrayRequests();
    }

    protected function tearDown(): void
    {
        @unlink($this->log);
        parent::tearDown();
    }

    /** /medicheck/next answers 404 while intake is off, so anything but 429 means "let through". */
    private function ask(array $headers = [], array $body = ['symptoms' => 'runny nose'], ?string $session = 'one')
    {
        $this->flushHeaders();
        $this->unencryptedCookies = $session ? [config('session.cookie') => Crypt::encryptString(str_repeat('x', 41) . $session)] : [];

        return $this->withCredentials()->postJson('/medicheck/next', $body, $headers + [
            'User-Agent' => self::BROWSER, 'Accept-Language' => 'id', 'Origin' => 'http://localhost', 'Referer' => 'http://localhost/',
        ]);
    }

    private function script(): array
    {
        return ['User-Agent' => 'curl/8.5.0', 'Accept-Language' => '', 'Origin' => '', 'Referer' => ''];
    }

    public function test_a_normal_browser_is_not_interrupted(): void
    {
        foreach (range(1, 8) as $i) {
            $this->ask(body: ['symptoms' => "runny nose {$i}"])->assertNotFound();
        }
    }

    public function test_a_script_is_stopped_without_being_told_why(): void
    {
        $response = $this->ask($this->script(), session: null)->assertStatus(429);

        $this->assertStringContainsString('119', $response->json('emergency'));
        $this->assertStringContainsString('119', $response->json('message'));
        $this->assertArrayNotHasKey('question', $response->json('bot_challenge'), 'strongest level offers no easy alternative');
        foreach (['score', 'signals', 'automation', 'curl'] as $secret) {
            $this->assertStringNotContainsString($secret, $response->getContent());
        }
    }

    public function test_monitor_mode_records_but_never_blocks(): void
    {
        config(['botguard.mode' => 'monitor']);

        $this->ask($this->script(), session: null)->assertNotFound();

        $this->assertStringContainsString('"action":"restrict"', file_get_contents($this->log));
    }

    public function test_one_odd_header_is_not_enough(): void
    {
        $this->ask(['Accept-Language' => ''])->assertNotFound();
        $this->ask(['Origin' => '', 'Referer' => ''])->assertNotFound();
    }

    public function test_emergency_wording_is_never_challenged_within_its_allowance(): void
    {
        config(['botguard.emergency_allowance' => [2, 600]]);
        $body = ['symptoms' => 'I have severe chest pain'];

        $this->ask($this->script(), $body, null)->assertNotFound();
        $this->ask($this->script(), $body, null)->assertNotFound();
        // The allowance stops the wording from being a free pass.
        $this->ask($this->script(), $body, null)->assertStatus(429);
    }

    public function test_solving_the_check_lets_the_request_through_a_limited_number_of_times(): void
    {
        config(['botguard.pass.work' => 2]);
        $headers = ['User-Agent' => 'HeadlessChrome/130'];
        $challenge = $this->ask($headers)->assertStatus(429)->json('bot_challenge');

        for ($n = 0; app(BotGuard::class)->zeroBits(hash('sha256', "{$challenge['nonce']}:{$n}", true)) < $challenge['bits']; $n++);
        $verify = fn () => $this->postJson('/bot/verify', ['id' => $challenge['id'], 'solution' => (string) $n], $headers + ['Accept-Language' => 'id']);

        $first = $verify()->assertOk();
        $token = $first->getCookie('pharmasis_pass', false)->getValue();
        $verify()->assertStatus(422); // one try per challenge

        $with = function () use ($headers, $token) {
            $this->flushHeaders();
            $this->unencryptedCookies = [
                config('session.cookie') => Crypt::encryptString(str_repeat('x', 41) . 'one'), 'pharmasis_pass' => $token,
            ];

            return $this->withCredentials()->postJson('/medicheck/next', ['symptoms' => 'cough'], $headers + ['Accept-Language' => 'id', 'Origin' => 'http://localhost']);
        };
        $with()->assertNotFound();
        $with()->assertNotFound();
        $with()->assertStatus(429); // used up
    }

    public function test_question_alternative_and_binding_to_the_client(): void
    {
        $guard = app(BotGuard::class);
        $make = fn (string $ip) => \Illuminate\Http\Request::create('/bot/verify', 'POST', server: ['REMOTE_ADDR' => $ip, 'HTTP_ACCEPT_LANGUAGE' => 'en']);

        $answer = function (array $challenge) {
            preg_match_all('/\d+/', $challenge['question'], $m);

            return (string) ($m[0][0] + $m[0][1]);
        };

        $one = $guard->challenge($make('10.0.0.1'), false);
        $this->assertNull($guard->verify($make('10.0.0.2'), $one['id'], null, $answer($one)), 'another client cannot use it');

        $two = $guard->challenge($make('10.0.0.1'), false);
        $token = $guard->verify($make('10.0.0.1'), $two['id'], null, $answer($two));
        $this->assertNotNull($token);
        $this->assertSame(config('botguard.pass.question'), Cache::get("bg:pass:{$token}")['left']);

        $this->assertArrayNotHasKey('question', $guard->challenge($make('10.0.0.1'), true));
    }

    public function test_a_flood_is_limited_with_a_retry_time(): void
    {
        config(['botguard.classes.lookup.client' => [[3, 60]]]);
        foreach (range(1, 3) as $i) {
            $this->ask(body: ['symptoms' => "cough {$i}"])->assertNotFound();
        }

        $response = $this->ask(body: ['symptoms' => 'cough 4'])->assertStatus(429)->assertHeader('Retry-After');
        $this->assertNull($response->json('bot_challenge'));
        // Someone else on the same network keeps their own allowance.
        $this->ask(session: 'two')->assertNotFound();
    }

    public function test_emergency_wording_gets_through_after_the_rate_limit(): void
    {
        config(['botguard.classes.lookup.client' => [[1, 60]]]);
        $this->ask()->assertNotFound();
        $this->ask(body: ['symptoms' => 'cough again'])->assertStatus(429);

        $this->ask(body: ['symptoms' => 'severe chest pain'])->assertNotFound();
    }

    public function test_the_environment_sync_route_is_limited_even_in_monitor_mode(): void
    {
        config(['botguard.mode' => 'monitor']);
        foreach (range(1, 3) as $i) {
            $this->get('/sync-env?token=wrong')->assertForbidden();
        }

        $this->get('/sync-env?token=wrong')->assertStatus(429);
        $this->assertStringContainsString('sync-env', file_get_contents($this->log));
        $this->assertStringNotContainsString('wrong', file_get_contents($this->log));
    }

    public function test_probing_for_other_software_raises_the_risk_of_what_follows(): void
    {
        $this->get('/wp-login.php', ['User-Agent' => self::BROWSER])->assertNotFound();

        $this->ask()->assertStatus(429);
    }

    public function test_events_hold_no_address_browser_or_message(): void
    {
        config(['botguard.mode' => 'monitor']);
        $this->ask($this->script(), ['symptoms' => 'private words'], null);

        $log = file_get_contents($this->log);
        foreach (['127.0.0.1', 'curl', 'private words'] as $personal) {
            $this->assertStringNotContainsString($personal, $log);
        }
    }

    public function test_a_fault_in_the_protection_does_not_block_the_service(): void
    {
        Log::spy();
        config(['botguard.classes.lookup.burst' => null]);

        $this->ask($this->script(), session: null)->assertNotFound();
    }

    public function test_every_post_route_is_covered(): void
    {
        $paths = array_merge(...array_values(array_column(config('botguard.classes'), 'paths')));

        foreach (Route::getRoutes() as $route) {
            if (!in_array('POST', $route->methods(), true) || str_starts_with($route->uri(), '_ignition') || str_contains($route->getActionName(), 'RedirectController')) {
                continue;
            }
            $this->assertTrue(\Illuminate\Support\Str::is($paths, $route->uri()), "No protection class for {$route->uri()}");
        }
    }

    public function test_pages_are_never_challenged(): void
    {
        $this->get('/facilities', ['User-Agent' => 'curl/8'])->assertOk();
    }
}
