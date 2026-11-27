<?php

namespace App\Http\Middleware;

use App\Services\Security\BotGuard;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GuardAgainstBots
{
    public function __construct(private BotGuard $guard) {}

    public function handle(Request $request, Closure $next)
    {
        $mode = config('botguard.mode');
        if ($mode === 'off') {
            return $next($request);
        }

        // A fault in the protection must not take the health service down with it.
        try {
            if ($request->is(...config('botguard.probe_paths'))) {
                $this->guard->noteProbe($request);

                return $next($request);
            }
            $class = $this->guard->classFor($request);
            if (!$class) {
                return $next($request);
            }
            $this->guard->client($request);
            $decision = $this->guard->decide($request, $class);
            if ($decision['action'] !== 'allow' || in_array('emergency_wording', $decision['signals'], true)) {
                $this->guard->record($decision['action'], $class, $request, $decision['score'], $decision['signals']);
            }

            $enforced = $mode === 'enforce' || config("botguard.classes.{$class}.always");
            if ($enforced && in_array($decision['action'], ['challenge', 'restrict', 'limit'], true)) {
                return $this->refuse($request, $decision);
            }
        } catch (\Throwable $e) {
            Log::error('BotGuard failed open', ['message' => $e->getMessage()]);
        }

        return $next($request);
    }

    /** The reply never says which signals were seen. */
    private function refuse(Request $request, array $decision)
    {
        $locale = $this->guard->locale($request);
        $limited = $decision['action'] === 'limit';
        $body = [
            'success'   => false,
            'error'     => trans($limited ? 'botguard.limited' : 'botguard.check_needed', [], $locale),
            'emergency' => trans('botguard.emergency', [], $locale),
        ];
        $body['error'] .= ' ' . $body['emergency'];
        // Some pages show "message" rather than "error".
        $body['message'] = $body['error'];

        if (!$limited && $decision['class'] !== 'sensitive') {
            $body['bot_challenge'] = $this->guard->challenge($request, $decision['action'] === 'restrict');
        }

        return response()->json($body, 429, $limited ? ['Retry-After' => $decision['retry_after']] : []);
    }
}
