<?php

namespace App\Console\Commands;

use App\Http\Middleware\GuardAgainstBots;
use App\Services\Security\BotGuard;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class BotGuardEvaluate extends Command
{
    protected $signature = 'botguard:evaluate';

    protected $description = 'Replay made-up legitimate and automated traffic against the protection and time it';

    private const BROWSER = 'Mozilla/5.0 (Linux; Android 14) AppleWebKit/537.36 Chrome/130 Mobile Safari/537.36';

    public function handle(): int
    {
        $log = tempnam(sys_get_temp_dir(), 'sec');
        // Made-up traffic must not appear in the real security log.
        config(['logging.channels.security' => ['driver' => 'single', 'path' => $log, 'level' => 'info']]);
        Log::forgetChannel('security');
        $store = config('cache.default');
        config(['cache.default' => 'array']);
        $this->rebindLimiter();

        $legitimate = $this->replay($this->legitimate());
        $automated = $this->replay($this->automated());

        config(['cache.default' => $store]);
        $this->rebindLimiter();
        $latency = $this->latency();
        @unlink($log);

        $sum = fn (array $rows, string $key) => array_sum(array_column($rows, $key));
        $summary = [
            'legitimate_profiles'         => count($legitimate),
            'legitimate_profiles_stopped' => count(array_filter($legitimate, fn ($r) => $r['stopped'] > 0)),
            'legitimate_requests_stopped' => $sum($legitimate, 'stopped') . '/' . $sum($legitimate, 'requests'),
            'automated_profiles'          => count($automated),
            'automated_profiles_stopped_at_all' => count(array_filter($automated, fn ($r) => $r['stopped'] > 0)),
            'automated_requests_stopped'  => $sum($automated, 'stopped') . '/' . $sum($automated, 'requests'),
            'added_latency_ms'            => $latency,
        ];

        $this->table(['Legitimate', 'Requests', 'Stopped', 'First stop'], array_map('array_values', $legitimate));
        $this->table(['Automated', 'Requests', 'Stopped', 'First stop'], array_map('array_values', $automated));
        $this->info(json_encode($summary, JSON_PRETTY_PRINT));

        $dir = base_path('docs/botguard-runs');
        is_dir($dir) || mkdir($dir);
        $file = "{$dir}/" . now()->format('Y-m-d-His') . '.json';
        file_put_contents($file, json_encode(compact('summary', 'legitimate', 'automated'), JSON_PRETTY_PRINT));
        $this->info('Saved ' . $file);

        return self::SUCCESS;
    }

    /** The limiter keeps the cache store it was first built with. */
    private function rebindLimiter(): void
    {
        app()->forgetInstance(\Illuminate\Cache\RateLimiter::class);
        \Illuminate\Support\Facades\RateLimiter::clearResolvedInstances();
    }

    private function replay(array $profiles): array
    {
        $guard = app(BotGuard::class);
        $rows = [];
        foreach ($profiles as $name => $requests) {
            Cache::flush();
            $time = Carbon::create(2026, 1, 1, 8);
            $count = $stopped = 0;
            $first = null;
            foreach ($requests as [$gap, $request]) {
                Carbon::setTestNow($time = $time->copy()->addSeconds($gap));
                if ($request->is(...config('botguard.probe_paths'))) {
                    $guard->noteProbe($request);
                    continue;
                }
                $count++;
                $action = $guard->decide($request, $guard->classFor($request))['action'];
                if (in_array($action, ['challenge', 'restrict', 'limit'], true)) {
                    $stopped++;
                    $first ??= "{$action} at request {$count}";
                }
            }
            $rows[] = ['profile' => $name, 'requests' => $count, 'stopped' => $stopped, 'first' => $first ?? '-'];
        }
        Carbon::setTestNow();

        return $rows;
    }

    /** One request as a browser on the site would send it, with parts swapped out per profile. */
    private function request(string $path, array $o = []): Request
    {
        $o += [
            'method' => 'POST', 'body' => ['symptoms' => 'cough'], 'ip' => '203.0.113.5', 'ua' => self::BROWSER,
            'lang' => 'id', 'origin' => true, 'session' => 'a',
        ];
        $server = ['REMOTE_ADDR' => $o['ip'], 'HTTP_USER_AGENT' => $o['ua'], 'HTTP_ACCEPT_LANGUAGE' => $o['lang']];
        if ($o['origin']) {
            $server['HTTP_ORIGIN'] = 'http://localhost';
        }
        $cookies = $o['session']
            ? [config('session.cookie') => Crypt::encryptString(str_repeat('x', 41) . $o['session'])] : [];

        return Request::create("http://localhost/{$path}", $o['method'], $o['method'] === 'GET' ? [] : $o['body'], $cookies, [], $server);
    }

    /** @param callable(int):Request $make */
    private function series(int $count, int $gap, callable $make): array
    {
        return array_map(fn ($i) => [$gap, $make($i)], range(1, $count));
    }

    private function legitimate(): array
    {
        $say = fn (int $i) => ['symptoms' => "cough, answer {$i}"];

        return [
            'one consultation'                 => $this->series(7, 20, fn ($i) => $this->request($i === 1 ? 'medicheck/screen' : 'medicheck/next', ['body' => $say($i)])),
            'typing in drug search'            => $this->series(30, 2, fn () => $this->request('api/v1/search/instant', ['method' => 'GET'])),
            'quick tapping through questions'  => $this->series(12, 2, fn ($i) => $this->request('medicheck/next', ['body' => $say($i)])),
            'retrying after errors'            => $this->series(6, 5, fn () => $this->request('medicheck/screen')),
            'privacy browser, no language'     => $this->series(6, 20, fn ($i) => $this->request('medicheck/next', ['lang' => '', 'body' => $say($i)])),
            'cookies blocked'                  => $this->series(6, 20, fn ($i) => $this->request('medicheck/next', ['session' => null, 'body' => $say($i)])),
            '40 people, one mobile network'    => $this->series(120, 5, fn ($i) => $this->request('medicheck/screen', ['session' => 'user' . ($i % 40), 'body' => $say($i)])),
            '150 people, one mobile network in 5 minutes' => $this->series(450, 1, fn ($i) => $this->request('medicheck/screen', ['session' => 'user' . ($i % 150), 'body' => $say($i)])),
            'shared clinic tablet, 30 patients in an hour' => $this->series(180, 20, fn ($i) => $this->request('medicheck/screen', ['body' => $say($i)])),
        ];
    }

    private function automated(): array
    {
        $bare = ['lang' => '', 'origin' => false, 'session' => null];
        $say = fn (int $i) => ['symptoms' => "question {$i}"];

        return [
            'curl script'                       => $this->series(30, 1, fn ($i) => $this->request('medicheck/route', ['ua' => 'curl/8.5.0', 'body' => $say($i)] + $bare)),
            'python scraper of search'          => $this->series(60, 1, fn () => $this->request('api/v1/search', ['method' => 'GET', 'ua' => 'python-requests/2.32'] + $bare)),
            'no browser name'                   => $this->series(30, 1, fn ($i) => $this->request('medicheck/route', ['ua' => '', 'body' => $say($i)] + $bare)),
            'headless browser'                  => $this->series(30, 3, fn ($i) => $this->request('medicheck/screen', ['ua' => 'Mozilla/5.0 HeadlessChrome/130', 'body' => $say($i)])),
            'script faking a browser, fast'     => $this->series(60, 1, fn ($i) => $this->request('medicheck/route', ['origin' => false, 'session' => null, 'body' => $say($i)])),
            'script faking a browser, slow'     => $this->series(60, 30, fn ($i) => $this->request('medicheck/route', ['origin' => false, 'session' => null, 'body' => $say($i)])),
            'replaying one message'             => $this->series(60, 2, fn () => $this->request('medicheck/conclude')),
            'full browser imitation, slow'      => $this->series(60, 30, fn ($i) => $this->request('medicheck/route', ['body' => $say($i)])),
            'rotating addresses, slow'          => $this->series(60, 5, fn ($i) => $this->request('medicheck/route', ['ip' => "198.51.100.{$i}", 'session' => "s{$i}", 'body' => $say($i)])),
            'scanner then use'                  => array_merge(
                [[1, $this->request('wp-login.php', ['method' => 'GET'] + $bare)]],
                $this->series(20, 5, fn ($i) => $this->request('medicheck/route', ['session' => null, 'body' => $say($i)]))
            ),
            'adding emergency words to get in'  => $this->series(60, 1, fn () => $this->request('medicheck/route', ['ua' => 'curl/8.5.0', 'body' => ['input' => 'chest pain']] + $bare)),
        ];
    }

    /** Time the middleware itself, using the configured cache store and real log writes. */
    private function latency(): array
    {
        config(['botguard.mode' => 'monitor']);
        $middleware = app(GuardAgainstBots::class);
        $times = [];
        $run = bin2hex(random_bytes(4));
        for ($i = 0; $i < 200; $i++) {
            $request = $this->request('medicheck/screen', [
                'session' => $run . ($i % 20), 'body' => ['symptoms' => "timing {$run} {$i}"], 'ua' => $i % 2 ? self::BROWSER : 'curl/8',
            ]);
            $start = hrtime(true);
            $middleware->handle($request, fn () => response('ok'));
            $times[] = (hrtime(true) - $start) / 1e6;
        }
        sort($times);

        return ['median' => round($times[100], 1), 'p95' => round($times[189], 1), 'max' => round(end($times), 1)];
    }
}
