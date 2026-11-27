<?php

namespace App\Services\Security;

use App\Services\Triage\TriageEngine;
use Illuminate\Cookie\CookieValuePrefix;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

/**
 * Scores a request and decides: allow, monitor, challenge, limit or restrict.
 * Counters are keyed by keyed hashes; no address, agent or message is stored.
 */
class BotGuard
{
    public const PASS_COOKIE = 'pharmasis_pass';

    public function __construct(private TriageEngine $engine) {}

    public function classFor(Request $request): ?string
    {
        foreach (config('botguard.classes') as $name => $class) {
            if ($request->is(...$class['paths'])) {
                return $name;
            }
        }

        return null;
    }

    /** Address and browser. Cannot be changed by dropping cookies. */
    private function device(Request $request): string
    {
        return $this->hash($request->ip() . '|' . $request->userAgent());
    }

    /**
     * One browser session where there is one, so people sharing a mobile network and
     * phone model are counted apart. Worked out once, before cookies are decrypted.
     */
    public function client(Request $request): string
    {
        if (!$request->attributes->has('bg.client')) {
            $request->attributes->set('bg.client', $this->hash($this->device($request) . '|' . $this->sessionId($request)));
        }

        return $request->attributes->get('bg.client');
    }

    /** Only a cookie this server encrypted counts. */
    private function sessionId(Request $request): string
    {
        $cookie = $request->cookies->get((string) config('session.cookie'));
        if (!is_string($cookie)) {
            return '';
        }
        try {
            return CookieValuePrefix::remove(Crypt::decryptString($cookie));
        } catch (\Throwable) {
            return '';
        }
    }

    private function address(Request $request): string
    {
        return $this->hash((string) $request->ip());
    }

    private function hash(string $value): string
    {
        return substr(hash_hmac('sha256', $value, (string) config('app.key')), 0, 20);
    }

    public function noteProbe(Request $request): void
    {
        Cache::put('bg:probe:' . $this->device($request), true, 3600);
        $this->record('probe', 'probe', $request, 0, ['probed']);
    }

    /**
     * @return array{action:string,score:int,signals:string[],retry_after:int,class:string}
     */
    public function decide(Request $request, string $class): array
    {
        $settings = config("botguard.classes.{$class}");
        $client = $this->client($request);
        $decision = fn (string $action, int $score = 0, array $signals = [], int $wait = 0) =>
            ['action' => $action, 'score' => $score, 'signals' => $signals, 'class' => $class, 'retry_after' => $wait];

        // Someone describing an emergency is not asked to prove anything.
        if ($this->isEmergency($request) && $this->take("bg:sos:{$client}", config('botguard.emergency_allowance'))) {
            return $decision('allow', 0, ['emergency_wording']);
        }

        $volume = 0.0;
        $wait = 0;
        foreach (['client' => $client, 'address' => $this->address($request)] as $scope => $key) {
            foreach ($settings[$scope] as [$max, $seconds]) {
                $limiter = "bg:{$class}:{$scope}:{$seconds}:{$key}";
                $count = RateLimiter::hit($limiter, $seconds);
                if ($count > $max) {
                    $wait = max($wait, RateLimiter::availableIn($limiter));
                }
                if ($scope === 'client') {
                    $volume = max($volume, $count / $max);
                }
            }
        }
        if ($wait) {
            return $decision('limit', 0, ['over_limit'], $wait);
        }
        if (!empty($settings['no_challenge'])) {
            return $decision('allow');
        }

        $signals = $this->signals($request, $class, $client, $volume);
        $weights = config('botguard.weights');
        $score = array_sum(array_map(fn ($s) => $weights[$s], $signals));
        $levels = config('botguard.thresholds');

        // The strongest response needs more than one reason.
        $action = match (true) {
            $score >= $levels['restrict'] && count($signals) > 1 => 'restrict',
            $score >= $levels['challenge'] => 'challenge',
            $score >= $levels['monitor']   => 'monitor',
            default                        => 'allow',
        };

        if (in_array($action, ['challenge', 'restrict'], true) && $this->usePass($request)) {
            return $decision('allow', $score, [...$signals, 'passed_check']);
        }

        return $decision($action, $score, $signals);
    }

    /** @return string[] */
    private function signals(Request $request, string $class, string $client, float $volume): array
    {
        $agent = (string) $request->userAgent();
        $signals = [];

        if ($agent === '') {
            $signals[] = 'no_user_agent';
        } elseif (preg_match(config('botguard.automation_agents'), $agent)) {
            $signals[] = 'automation_agent';
        }
        if (!$request->headers->get('accept-language')) {
            $signals[] = 'no_language';
        }
        if ($request->isMethod('POST')) {
            $origin = parse_url((string) ($request->headers->get('origin') ?: $request->headers->get('referer')), PHP_URL_HOST);
            if ($request->headers->get('sec-fetch-site') !== 'same-origin' && $origin !== $request->getHost()) {
                $signals[] = 'no_page_context';
            }
            if ($this->sessionId($request) === '') {
                $signals[] = 'no_session_cookie';
            }
            $body = json_encode($request->except('_token'));
            if (strlen($body) > 2 && RateLimiter::hit('bg:body:' . $client . ':' . $this->hash($request->path() . $body), 600) >= 5) {
                $signals[] = 'repeated_payload';
            }
        }
        [$burst, $seconds] = config("botguard.classes.{$class}.burst");
        if (RateLimiter::hit("bg:burst:{$class}:{$client}", $seconds) > $burst) {
            $signals[] = 'burst';
        }
        if ($volume > 0.6) {
            $signals[] = 'high_volume';
        }
        if (Cache::has('bg:probe:' . $this->device($request))) {
            $signals[] = 'probed';
        }
        if (RateLimiter::attempts('bg:failed:' . $this->device($request)) >= 3) {
            $signals[] = 'failed_checks';
        }

        return $signals;
    }

    private function isEmergency(Request $request): bool
    {
        if (!$request->is(...config('botguard.emergency_paths'))) {
            return false;
        }
        $text = (string) ($request->input('symptoms') ?: $request->input('input'));
        foreach ((array) $request->input('qa', []) as $pair) {
            $text .= "\n" . (is_array($pair) && is_string($pair['answer'] ?? null) ? $pair['answer'] : '');
        }
        if (trim($text) === '') {
            return false;
        }
        // The patterns are used here even while urgency assessment is off: they only widen access.
        $text = mb_substr($text, 0, 4000);

        return $this->engine->assess($this->engine->match($text))['category'] === 'emergency';
    }

    private function take(string $key, array $allowance): bool
    {
        [$max, $seconds] = $allowance;

        return RateLimiter::hit($key, $seconds) <= $max;
    }

    /* ── Challenges ─────────────────────────────────────────────────────── */

    public function challenge(Request $request, bool $strict): array
    {
        $id = Str::random(32);
        $nonce = Str::random(24);
        $bits = (int) config($strict ? 'botguard.strict_difficulty' : 'botguard.difficulty');
        // The question is the alternative for browsers that cannot do the calculation.
        $a = random_int(2, 9);
        $b = random_int(1, 9);

        Cache::put("bg:challenge:{$id}", [
            'client' => $this->client($request), 'nonce' => $nonce, 'bits' => $bits,
            'answer' => $strict ? null : $a + $b,
        ], (int) config('botguard.challenge_seconds'));

        return ['id' => $id, 'nonce' => $nonce, 'bits' => $bits]
            + ($strict ? [] : ['question' => trans('botguard.question', compact('a', 'b'), $this->locale($request))]);
    }

    /** @return string|null A pass token, or null when the answer is wrong or used up. */
    public function verify(Request $request, string $id, ?string $solution, ?string $answer): ?string
    {
        // Pulled, so each challenge can be tried once.
        $challenge = Cache::pull("bg:challenge:{$id}");
        $client = $this->client($request);
        $method = null;

        if ($challenge && $challenge['client'] === $client) {
            if ($solution !== null && $this->zeroBits(hash('sha256', $challenge['nonce'] . ':' . $solution, true)) >= $challenge['bits']) {
                $method = 'work';
            } elseif ($answer !== null && $challenge['answer'] !== null && trim($answer) === (string) $challenge['answer']) {
                $method = 'question';
            }
        }
        if (!$method) {
            RateLimiter::hit('bg:failed:' . $this->device($request), 600);
            $this->record('check_failed', 'verify', $request, 0, []);

            return null;
        }

        $token = Str::random(48);
        Cache::put("bg:pass:{$token}", ['client' => $client, 'left' => config("botguard.pass.{$method}")], (int) config('botguard.pass_seconds'));
        $this->record("check_passed_{$method}", 'verify', $request, 0, []);

        return $token;
    }

    public function zeroBits(string $binary): int
    {
        $bits = 0;
        foreach (str_split($binary) as $char) {
            $byte = ord($char);
            if ($byte === 0) {
                $bits += 8;
                continue;
            }

            return $bits + 8 - strlen(decbin($byte));
        }

        return $bits;
    }

    private function usePass(Request $request): bool
    {
        $token = $request->cookies->get(self::PASS_COOKIE);
        if (!is_string($token) || strlen($token) !== 48) {
            return false;
        }
        $pass = Cache::get("bg:pass:{$token}");
        if (!$pass || $pass['client'] !== $this->client($request) || $pass['left'] < 1) {
            return false;
        }
        $pass['left']--;
        Cache::put("bg:pass:{$token}", $pass, (int) config('botguard.pass_seconds'));

        return true;
    }

    /* ── Events ─────────────────────────────────────────────────────────── */

    /** The client id changes daily, so events cannot be linked across days. */
    public function record(string $action, string $class, Request $request, int $score, array $signals): void
    {
        Log::channel('security')->info('botguard', [
            'action'  => $action,
            'class'   => $class,
            'route'   => in_array($class, ['probe', 'verify'], true) ? $class : $request->path(),
            'mode'    => config('botguard.mode'),
            'score'   => $score,
            'signals' => $signals,
            'client'  => substr($this->hash(now()->toDateString() . $this->client($request)), 0, 12),
        ]);
    }

    public function locale(Request $request): string
    {
        $lang = is_string($request->input('lang')) ? $request->input('lang') : null;

        return ($lang ?: substr((string) $request->headers->get('accept-language'), 0, 2)) === 'id' ? 'id' : 'en';
    }
}
