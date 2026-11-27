# Bot protection

**Status: monitor mode. It records what it would do and blocks nothing, except on `/sync-env`. Not complete: not tested on real traffic, phones or screen readers.**

## Responses

| Level | What the visitor sees | When |
| --- | --- | --- |
| Allow | Nothing | No meaningful signal |
| Monitor | Nothing. An event is recorded | Weak signals |
| Challenge | A short browser calculation, then the request repeats. A sum to answer is the alternative | Stronger signals |
| Restrict | A harder calculation. No alternative question | Several strong signals |
| Rate limit | "Too many requests" with a wait time | Over a request ceiling, whatever the score |

Signals include request volume and bursts, automation tools naming themselves, missing browser headers or session, repeated identical messages, probing for other software and failed checks. Weights and limits are in `config/botguard.php`. They are developer estimates. Replies never say which signals were seen.

## Keeping healthcare access open

- Ordinary pages are never challenged, including the home, drug and facility pages.
- A symptom message with emergency wording skips all checks and limits, up to 10 times in 10 minutes.
- Every refusal includes the emergency number.
- If the protection itself fails, requests are let through and the error is logged.
- People behind one mobile network are counted apart by session.

**A real emergency can still be refused** if the sender is already challenged or limited and words it in a way the patterns miss. The patterns are English and Indonesian only and caught about half of unseen phrasings (see `TRIAGE-VALIDATION.md`). The refusal still shows the number.

## Turning it on

```env
BOTGUARD_MODE=monitor   # default
BOTGUARD_MODE=enforce   # block and challenge
BOTGUARD_MODE=off
TRUSTED_PROXIES=        # your real proxy addresses
```

**Set `TRUSTED_PROXIES` before relying on any limit.** Empty keeps the old behaviour of trusting any proxy. A server reachable without a proxy then lets a visitor forge their address.

Run monitor mode on real traffic, review the report, then decide about enforce.

## Monitoring and incidents

- Events: `storage/logs/security-*.log`, kept 30 days.
- Summary: `php artisan botguard:report --days=7`.
- No automatic alerts. Someone has to read the report.

| Situation | Action |
| --- | --- |
| Real users are being stopped | Set `BOTGUARD_MODE=monitor`. `php artisan cache:clear` resets counters (it also clears other cached data) |
| Abuse in progress | Set `BOTGUARD_MODE=enforce`, lower the limits for the affected class |
| Attack the app cannot absorb | Block at the proxy, CDN or host. This code is not protection against denial of service |

If the configuration is cached, run `php artisan config:cache` after changing it.

## Privacy

- Counters are keyed by keyed hashes of address, browser name and session. No address, browser name or message is stored by this feature.
- Event client ids change daily.
- No third-party CAPTCHA service. No data leaves the server.
- One extra cookie, `pharmasis_pass`: a random token, 15 minutes, set only after a check.
- Existing analytics still store a hash of the visitor address.

## Accessibility

| Checked | Result |
| --- | --- |
| No picture or audio puzzle | Yes |
| Normally no action needed from the visitor | Yes |
| Alternative when the browser cannot calculate | A sum, from the challenge level only |
| Dialog labelled, answer box focused, Esc and Cancel close it | Code reviewed. Label, focus and 44 px targets confirmed in headless Edge |
| Screen reader, keyboard-only walkthrough, zoom, real phones | **Not tested** |

- The sum is in English or Indonesian only.
- Restricted visitors get no alternative. A browser that cannot calculate is stuck until the signals expire.
- The calculation needs HTTPS (or `localhost`). Without it the sum is shown.
- A person using assistive software that looks automated could be challenged.

## Validation

`php artisan botguard:evaluate` replays made-up traffic. Results: `botguard-runs/`. Tests: `tests/Feature/BotGuardTest.php`. Profiles and rules were written by the same author.

| Proposed target | Result |
| --- | --- |
| Bot detection recall at least 95% | **Not met.** 290 of 530 automated requests stopped (55%). 8 of 11 profiles stopped at some point |
| Legitimate false positives at most 1% | 0 of 817 made-up requests. **Not established** on real traffic |
| Legitimate challenge completion at least 95% | Not measured. Two automated browser runs completed (about 1.2 s on a desktop) |
| Critical endpoint monitoring 100% | Every current POST route and the search routes have a class. A test checks POST routes |
| Added latency p95 at most 200 ms | 38 ms for the middleware on the development machine, file cache |

- The first evaluation stopped patients on a shared kiosk tablet. The hourly limit was then raised from 150 to 300, so that result was tuned.
- **Not stopped:** a script copying browser headers at a slow pace, and requests spread over many addresses.
- A capable bot or automated browser can solve the calculation. It raises cost and does not prevent bots.
- Added emergency words bought a script 10 extra requests per 10 minutes.

## Security findings outside this feature

1. **`app/Services/EnvSyncService.php` contains the Groq API keys with the key to decrypt them in the same file.** Anyone with the source has them. Rotate the keys and keep them only in the server environment.
2. **`/sync-env` is protected by a fixed token written in `routes/web.php`.** It rewrites `.env`. It is now limited to 3 calls an hour per visitor. Remove it.
3. `TrustProxies` trusts any proxy unless `TRUSTED_PROXIES` is set.

There are no login pages, so no authentication workflow needed protection.

## Other limits

- Counters use the configured cache. The file cache is not exact under heavy parallel traffic and is not shared between servers.
- Page requests (GET) are not rate limited.
- The existing 60 per minute API limit and the facility limits still apply in every mode.

## Needed before enforcing

1. The findings above.
2. Monitor mode on real traffic, with a review of every would-be challenge.
3. Accessibility testing with a screen reader and phones.
4. Alerts, and a named person to act on them.
