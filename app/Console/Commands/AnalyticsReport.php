<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

/**
 * Live analytics report viewable from the server terminal.
 *
 * Examples:
 *   php artisan analytics:report
 *   php artisan analytics:report --since=24h
 *   php artisan analytics:report --since=7d --top=20
 *   php artisan analytics:report --user=42
 *   php artisan analytics:report --feature=medicheck
 *   php artisan analytics:report --watch          # live refresh every 5s
 *   php artisan analytics:report --tail=20        # stream most recent visits
 */
class AnalyticsReport extends Command
{
    protected $signature = 'analytics:report
                            {--since=24h : Window: e.g. 30m, 6h, 24h, 7d, 30d, all}
                            {--top=10 : Rows in each "top N" table}
                            {--user= : Filter by users.id}
                            {--feature= : Filter by feature_area (medicheck|ai_simplifier|ai_interaction|drugs|auth|marketing|other)}
                            {--watch : Refresh every 5 seconds until Ctrl+C}
                            {--tail= : Tail the N most recent traffic_visits rows and exit}';

    protected $description = 'Show a server-side analytics report (traffic + feature usage) from the analytics tables.';

    public function handle(): int
    {
        if (! $this->ensureTablesExist()) {
            return self::FAILURE;
        }

        if ($this->option('tail') !== null) {
            return $this->renderTail((int) $this->option('tail') ?: 20);
        }

        if ($this->option('watch')) {
            return $this->renderWatch();
        }

        $this->renderReport();
        return self::SUCCESS;
    }

    // ── render modes ────────────────────────────────────────────────────

    private function renderReport(): void
    {
        $since   = $this->parseSince((string) $this->option('since'));
        $top     = max(1, (int) $this->option('top'));
        $userId  = $this->option('user') !== null ? (int) $this->option('user') : null;
        $feature = $this->option('feature');

        $label = $since ? $since->diffForHumans(null, true) : 'all time';
        $this->newLine();
        $this->line("<fg=cyan;options=bold>━━ Pharmasis Analytics ━━</> <fg=gray>window: {$label}"
            . ($userId ? " · user={$userId}" : '')
            . ($feature ? " · feature={$feature}" : '')
            . '</>');
        $this->newLine();

        $this->summary($since, $userId, $feature);
        $this->byFeature($since, $userId);
        $this->topUsers($since, $top, $feature);
        $this->topPaths($since, $top, $userId, $feature);
        $this->medicheckBreakdown($since, $userId);
        $this->simplifierBreakdown($since, $userId);
        $this->interactionBreakdown($since, $userId);
    }

    private function renderWatch(): int
    {
        while (true) {
            $this->output->write("\033[2J\033[H"); // clear + home
            $this->renderReport();
            $this->line('<fg=gray>Refreshing every 5s · Ctrl+C to exit</>');
            sleep(5);
        }
    }

    private function renderTail(int $n): int
    {
        $rows = DB::table('traffic_visits')
            ->orderByDesc('id')
            ->limit($n)
            ->get(['id', 'user_id', 'method', 'path', 'feature_area', 'status_code', 'duration_ms', 'device_type', 'created_at']);

        if ($rows->isEmpty()) {
            $this->warn('No traffic recorded yet.');
            return self::SUCCESS;
        }

        $this->table(
            ['id', 'user', 'method', 'path', 'feature', 'status', 'ms', 'device', 'time'],
            $rows->reverse()->map(fn($r) => [
                $r->id,
                $r->user_id ?? '-',
                $r->method,
                str($r->path)->limit(40),
                $r->feature_area,
                $r->status_code ?? '-',
                $r->duration_ms ?? '-',
                $r->device_type,
                $r->created_at,
            ])->all()
        );

        return self::SUCCESS;
    }

    // ── sections ────────────────────────────────────────────────────────

    private function summary(?Carbon $since, ?int $userId, ?string $feature): void
    {
        $base = $this->scoped(DB::table('traffic_visits'), $since, $userId, 'created_at');
        if ($feature) {
            $base->where('feature_area', $feature);
        }

        $total       = (clone $base)->count();
        $unique      = (clone $base)->whereNotNull('user_id')->distinct()->count('user_id');
        $anon        = (clone $base)->whereNull('user_id')->count();
        $avgMs       = (clone $base)->whereNotNull('duration_ms')->avg('duration_ms');
        $errorRate   = $total > 0
            ? (clone $base)->where('status_code', '>=', 500)->count() / $total * 100
            : 0;

        $this->table(['metric', 'value'], [
            ['Total visits',         number_format($total)],
            ['Logged-in users',      number_format($unique)],
            ['Anonymous visits',     number_format($anon)],
            ['Avg request duration', $avgMs ? round($avgMs) . ' ms' : '-'],
            ['5xx error rate',       round($errorRate, 2) . ' %'],
        ]);
    }

    private function byFeature(?Carbon $since, ?int $userId): void
    {
        $rows = $this->scoped(DB::table('traffic_visits'), $since, $userId, 'created_at')
            ->select('feature_area', DB::raw('COUNT(*) AS hits'), DB::raw('COUNT(DISTINCT user_id) AS users'))
            ->groupBy('feature_area')
            ->orderByDesc('hits')
            ->get();

        if ($rows->isEmpty()) {
            return;
        }

        $this->line('<fg=yellow;options=bold>Traffic by feature area</>');
        $this->table(['feature', 'hits', 'distinct users'],
            $rows->map(fn($r) => [$r->feature_area, $r->hits, $r->users])->all()
        );
    }

    private function topUsers(?Carbon $since, int $top, ?string $feature): void
    {
        $q = $this->scoped(DB::table('traffic_visits'), $since, null, 'created_at')
            ->whereNotNull('user_id');
        if ($feature) {
            $q->where('feature_area', $feature);
        }

        $rows = $q->select('user_id', DB::raw('COUNT(*) AS hits'))
            ->groupBy('user_id')
            ->orderByDesc('hits')
            ->limit($top)
            ->get();

        if ($rows->isEmpty()) {
            return;
        }

        $userIds = $rows->pluck('user_id')->all();
        $names   = Schema::hasTable('users')
            ? DB::table('users')->whereIn('id', $userIds)->pluck('email', 'id')
            : collect();

        $this->line("<fg=yellow;options=bold>Top {$top} users by traffic</>");
        $this->table(['user_id', 'email', 'hits'],
            $rows->map(fn($r) => [$r->user_id, $names[$r->user_id] ?? '-', $r->hits])->all()
        );
    }

    private function topPaths(?Carbon $since, int $top, ?int $userId, ?string $feature): void
    {
        $q = $this->scoped(DB::table('traffic_visits'), $since, $userId, 'created_at');
        if ($feature) {
            $q->where('feature_area', $feature);
        }

        $rows = $q->select('path', DB::raw('COUNT(*) AS hits'))
            ->groupBy('path')
            ->orderByDesc('hits')
            ->limit($top)
            ->get();

        if ($rows->isEmpty()) {
            return;
        }

        $this->line("<fg=yellow;options=bold>Top {$top} paths</>");
        $this->table(['path', 'hits'],
            $rows->map(fn($r) => [str($r->path)->limit(60), $r->hits])->all()
        );
    }

    private function medicheckBreakdown(?Carbon $since, ?int $userId): void
    {
        $base = $this->scoped(DB::table('medicheck_events'), $since, $userId, 'created_at');
        $total = (clone $base)->count();
        if ($total === 0) {
            return;
        }

        $byAction = (clone $base)
            ->select('action',
                DB::raw('COUNT(*) AS c'),
                DB::raw('COUNT(*) FILTER (WHERE success) AS ok'),
                DB::raw('AVG(duration_ms) AS avg_ms'))
            ->groupBy('action')->orderByDesc('c')->get();

        $byInput = (clone $base)
            ->select('input_mode', DB::raw('COUNT(*) AS c'))
            ->groupBy('input_mode')->orderByDesc('c')->get();

        $byLang = (clone $base)
            ->select('lang', DB::raw('COUNT(*) AS c'))
            ->whereNotNull('lang')->groupBy('lang')->orderByDesc('c')->get();

        $this->line("<fg=green;options=bold>MediCheck</> <fg=gray>{$total} events</>");
        $this->table(['action', 'count', 'success', 'avg ms'],
            $byAction->map(fn($r) => [$r->action, $r->c, $r->ok, $r->avg_ms ? round($r->avg_ms) : '-'])->all()
        );
        if ($byInput->isNotEmpty()) {
            $this->line('<fg=gray>  input mode:</> ' . $byInput->map(fn($r) => "{$r->input_mode}={$r->c}")->implode('  '));
        }
        if ($byLang->isNotEmpty()) {
            $this->line('<fg=gray>  language:  </> ' . $byLang->map(fn($r) => "{$r->lang}={$r->c}")->implode('  '));
        }
        $this->newLine();
    }

    private function simplifierBreakdown(?Carbon $since, ?int $userId): void
    {
        $base = $this->scoped(DB::table('ai_simplifier_events'), $since, $userId, 'created_at');
        $total = (clone $base)->count();
        if ($total === 0) {
            return;
        }

        $byField = (clone $base)
            ->select('field',
                DB::raw('COUNT(*) AS c'),
                DB::raw('COUNT(*) FILTER (WHERE success) AS ok'),
                DB::raw('COUNT(*) FILTER (WHERE cache_hit) AS cached'))
            ->groupBy('field')->orderByDesc('c')->get();

        $byLang = (clone $base)
            ->select('language', DB::raw('COUNT(*) AS c'))
            ->groupBy('language')->orderByDesc('c')->get();

        $this->line("<fg=green;options=bold>AI Simplifier</> <fg=gray>{$total} events</>");
        $this->table(['field', 'count', 'success', 'cache_hit'],
            $byField->map(fn($r) => [$r->field, $r->c, $r->ok, $r->cached])->all()
        );
        if ($byLang->isNotEmpty()) {
            $this->line('<fg=gray>  language:</> ' . $byLang->map(fn($r) => "{$r->language}={$r->c}")->implode('  '));
        }
        $this->newLine();
    }

    private function interactionBreakdown(?Carbon $since, ?int $userId): void
    {
        $base = $this->scoped(DB::table('ai_interaction_events'), $since, $userId, 'created_at');
        $total = (clone $base)->count();
        if ($total === 0) {
            return;
        }

        $bySev = (clone $base)
            ->select('severity_max', DB::raw('COUNT(*) AS c'))
            ->groupBy('severity_max')->orderByDesc('c')->get();

        $avgDrugs = (clone $base)->avg('drug_count');
        $repeats  = (clone $base)
            ->select('drug_ids_hash', DB::raw('COUNT(*) AS c'))
            ->whereNotNull('drug_ids_hash')
            ->groupBy('drug_ids_hash')->having('c', '>', 1)->orderByDesc('c')->limit(5)->get();

        $this->line("<fg=green;options=bold>AI Interaction</> <fg=gray>{$total} events · avg drugs/check: " . ($avgDrugs ? round($avgDrugs, 1) : '-') . '</>');
        $this->table(['severity_max', 'count'],
            $bySev->map(fn($r) => [$r->severity_max, $r->c])->all()
        );
        if ($repeats->isNotEmpty()) {
            $this->line('<fg=gray>  repeated drug-set queries:</>');
            foreach ($repeats as $r) {
                $this->line('    ' . substr($r->drug_ids_hash, 0, 12) . "...  ×{$r->c}");
            }
        }
        $this->newLine();
    }

    // ── helpers ─────────────────────────────────────────────────────────

    private function scoped($builder, ?Carbon $since, ?int $userId, string $col)
    {
        if ($since) {
            $builder->where($col, '>=', $since);
        }
        if ($userId !== null) {
            $builder->where('user_id', $userId);
        }
        return $builder;
    }

    private function parseSince(string $raw): ?Carbon
    {
        $raw = strtolower(trim($raw));
        if ($raw === '' || $raw === 'all') {
            return null;
        }
        if (! preg_match('/^(\d+)\s*(m|h|d)$/', $raw, $m)) {
            $this->warn("Unrecognized --since '{$raw}', defaulting to 24h.");
            return now()->subHours(24);
        }
        [$_, $n, $unit] = $m;
        return match ($unit) {
            'm' => now()->subMinutes((int) $n),
            'h' => now()->subHours((int) $n),
            'd' => now()->subDays((int) $n),
        };
    }

    private function ensureTablesExist(): bool
    {
        $required = ['traffic_visits', 'medicheck_events', 'ai_simplifier_events', 'ai_interaction_events'];
        $missing = [];
        foreach ($required as $t) {
            try {
                if (! Schema::hasTable($t)) {
                    $missing[] = $t;
                }
            } catch (Throwable $e) {
                $this->error('Could not connect to the database: ' . $e->getMessage());
                return false;
            }
        }
        if ($missing) {
            $this->error('Analytics tables are missing: ' . implode(', ', $missing));
            $this->line('Import the schema first:  <fg=cyan>mysql -u USER -p DB < database/analytics_schema.sql</>');
            return false;
        }
        return true;
    }
}
