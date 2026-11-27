<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BotGuardReport extends Command
{
    protected $signature = 'botguard:report {--days=7}';

    protected $description = 'Summarise bot protection events for review';

    public function handle(): int
    {
        $since = now()->subDays((int) $this->option('days'))->toDateString();
        $actions = $signals = $routes = [];
        $clients = [];

        foreach (glob(storage_path('logs/security*.log')) as $file) {
            $lines = new \SplFileObject($file);
            foreach ($lines as $line) {
                if (!preg_match('/^\[(\d{4}-\d{2}-\d{2})[^\]]*\] \S+: botguard (\{.*\})/', (string) $line, $m) || $m[1] < $since) {
                    continue;
                }
                $event = json_decode($m[2], true);
                if (!$event) {
                    continue;
                }
                $key = "{$event['mode']} {$event['class']} {$event['action']}";
                $actions[$key] = ($actions[$key] ?? 0) + 1;
                $routes[$event['route']] = ($routes[$event['route']] ?? 0) + 1;
                foreach ($event['signals'] as $signal) {
                    $signals[$signal] = ($signals[$signal] ?? 0) + 1;
                }
                $clients[$event['client']] = true;
            }
        }

        if (!$actions) {
            $this->info("No events since {$since}.");

            return self::SUCCESS;
        }
        foreach (['Mode, class, action' => $actions, 'Signal' => $signals, 'Route' => $routes] as $title => $counts) {
            arsort($counts);
            $this->table([$title, 'Events'], array_map(fn ($k, $v) => [$k, $v], array_keys($counts), $counts));
        }
        $this->info('Distinct daily client ids: ' . count($clients));
        $this->line('Review: for each challenge or limit, ask whether a real person could have caused it.');

        return self::SUCCESS;
    }
}
