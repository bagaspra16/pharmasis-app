<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * AnalyticsRecorder
 *
 * Lightweight, fire-and-forget writer for the four analytics tables defined in
 * database/analytics_schema.sql. Failures are swallowed and logged so analytics
 * never breaks user-facing flows.
 */
class AnalyticsRecorder
{
    /** Request attribute key used to share the visit id with controllers. */
    public const VISIT_ATTR = 'analytics.visit_id';

    /**
     * Insert a traffic_visits row. Returns the inserted id (or null on failure).
     */
    public static function visit(Request $request, int $statusCode, int $durationMs): ?int
    {
        try {
            $path = '/' . ltrim($request->path(), '/');

            $id = DB::table('traffic_visits')->insertGetId([
                'user_id'       => optional($request->user())->id,
                'session_id'    => $request->hasSession() ? hash('sha1', $request->session()->getId()) : null,
                'visitor_uid'   => $request->cookie('visitor_uid'),
                'method'        => substr($request->getMethod(), 0, 8),
                'route_name'    => optional($request->route())->getName(),
                'path'          => substr($path, 0, 255),
                'feature_area'  => self::featureArea($path),
                'status_code'   => $statusCode,
                'duration_ms'   => $durationMs,
                'referrer_host' => self::referrerHost($request),
                'device_type'   => self::deviceType($request->userAgent()),
                'locale'        => substr((string) ($request->getPreferredLanguage() ?: app()->getLocale()), 0, 8),
                'ip_hash'       => self::hashIp($request->ip()),
                'created_at'    => now(),
            ]);

            return is_numeric($id) ? (int) $id : null;
        } catch (Throwable $e) {
            Log::warning('analytics.visit failed', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Record a MediCheck event (analyze / nearby / history view).
     */
    public static function medicheck(Request $request, string $action, array $extra = []): void
    {
        try {
            $row = array_merge([
                'visit_id'                 => $request->attributes->get(self::VISIT_ATTR),
                'user_id'                  => optional($request->user())->id,
                'action'                   => $action,
                'lang'                     => substr((string) $request->input('lang', ''), 0, 8) ?: null,
                'input_mode'               => self::inputMode($request),
                'symptoms_length'          => self::clampLen($request->input('symptoms')),
                'has_audio'                => $request->hasFile('audio'),
                'has_location'             => $request->filled('location'),
                'age_provided'             => $request->filled('age'),
                'weight_provided'          => $request->filled('weight'),
                'gender_provided'          => $request->filled('gender'),
                'allergies_count'          => self::tokenCount($request->input('allergies')),
                'conditions_count'         => self::tokenCount($request->input('conditions')),
                'medications_count'        => self::tokenCount($request->input('medications')),
                'pipeline_steps_completed' => null,
                'providers_returned'       => null,
                'success'                  => true,
                'error_code'               => null,
                'duration_ms'              => null,
                'created_at'               => now(),
            ], $extra);
            $row = self::normalizeBools($row, ['has_audio','has_location','age_provided','weight_provided','gender_provided','success']);

            DB::table('medicheck_events')->insert($row);
        } catch (Throwable $e) {
            Log::warning('analytics.medicheck failed', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Record an AI Simplifier event.
     */
    public static function simplifier(Request $request, array $extra = []): void
    {
        try {
            $row = array_merge([
                'visit_id'      => $request->attributes->get(self::VISIT_ATTR),
                'user_id'       => optional($request->user())->id,
                'drug_id'       => is_numeric($request->input('drug_id')) ? (int) $request->input('drug_id') : null,
                'field'         => substr((string) $request->input('field', 'uses'), 0, 32),
                'language'      => substr((string) $request->input('language', 'en'), 0, 8),
                'input_length'  => self::clampLen($request->input('text'), 4294967295),
                'output_length' => null,
                'cache_hit'     => false,
                'success'       => true,
                'error_code'    => null,
                'duration_ms'   => null,
                'created_at'    => now(),
            ], $extra);
            $row = self::normalizeBools($row, ['cache_hit','success']);

            DB::table('ai_simplifier_events')->insert($row);
        } catch (Throwable $e) {
            Log::warning('analytics.simplifier failed', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Record an AI Interaction (drug-interaction check) event.
     */
    public static function interaction(Request $request, array $extra = []): void
    {
        try {
            $ids = (array) $request->input('drug_ids', []);
            $sorted = array_values(array_unique(array_map('strval', $ids)));
            sort($sorted);

            $row = array_merge([
                'visit_id'           => $request->attributes->get(self::VISIT_ATTR),
                'user_id'            => optional($request->user())->id,
                'drug_count'         => min(255, count($ids)),
                'drug_ids_hash'      => $sorted ? hash('sha256', implode('|', $sorted)) : null,
                'language'           => substr((string) $request->input('language', 'en'), 0, 8),
                'severity_max'       => 'unknown',
                'interactions_found' => null,
                'cache_hit'          => false,
                'success'            => true,
                'error_code'         => null,
                'duration_ms'        => null,
                'created_at'         => now(),
            ], $extra);
            $row = self::normalizeBools($row, ['cache_hit','success']);

            DB::table('ai_interaction_events')->insert($row);
        } catch (Throwable $e) {
            Log::warning('analytics.interaction failed', ['error' => $e->getMessage()]);
        }
    }

    // ── helpers ─────────────────────────────────────────────────────────

    private static function featureArea(string $path): string
    {
        return match (true) {
            str_starts_with($path, '/medicheck')                 => 'medicheck',
            str_starts_with($path, '/api/v1/ai/')                => 'ai_simplifier',
            str_starts_with($path, '/api/v1/interactions')       => 'ai_interaction',
            str_starts_with($path, '/interactions')              => 'ai_interaction',
            str_starts_with($path, '/drugs') || str_starts_with($path, '/search') => 'drugs',
            str_starts_with($path, '/login') || str_starts_with($path, '/register') || str_starts_with($path, '/logout') => 'auth',
            $path === '/' || str_starts_with($path, '/about') || str_starts_with($path, '/contact') => 'marketing',
            default                                              => 'other',
        };
    }

    private static function deviceType(?string $ua): string
    {
        if (! $ua) {
            return 'unknown';
        }
        $ua = strtolower($ua);
        if (preg_match('/(bot|crawler|spider|slurp|bingpreview)/', $ua)) {
            return 'bot';
        }
        if (str_contains($ua, 'tablet') || str_contains($ua, 'ipad')) {
            return 'tablet';
        }
        if (preg_match('/(mobile|iphone|android.+mobile)/', $ua)) {
            return 'mobile';
        }
        return 'desktop';
    }

    private static function referrerHost(Request $request): ?string
    {
        $ref = $request->headers->get('referer');
        if (! $ref) {
            return null;
        }
        $host = parse_url($ref, PHP_URL_HOST);
        return $host ? substr($host, 0, 120) : null;
    }

    private static function hashIp(?string $ip): ?string
    {
        if (! $ip) {
            return null;
        }
        $salt = config('app.key', '') ?: 'pharmasis';
        return hash('sha256', $ip . '|' . $salt);
    }

    private static function inputMode(Request $request): string
    {
        $hasText  = $request->filled('symptoms');
        $hasAudio = $request->hasFile('audio');
        return match (true) {
            $hasText && $hasAudio => 'mixed',
            $hasAudio             => 'audio',
            $hasText              => 'text',
            default               => 'unknown',
        };
    }

    private static function clampLen($value, int $max = 65535): ?int
    {
        if (! is_string($value) && ! is_numeric($value)) {
            return null;
        }
        return min($max, mb_strlen((string) $value));
    }

    private static function normalizeBools(array $row, array $keys): array
    {
        foreach ($keys as $k) {
            if (array_key_exists($k, $row)) {
                $row[$k] = (bool) $row[$k];
            }
        }
        return $row;
    }

    private static function tokenCount($value): int
    {
        if (! is_string($value) || trim($value) === '') {
            return 0;
        }
        $parts = preg_split('/[,;\n]+/', $value) ?: [];
        return count(array_filter(array_map('trim', $parts)));
    }
}
