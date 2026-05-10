<?php

namespace App\Http\Middleware;

use App\Services\AnalyticsRecorder;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class TrackTraffic
{
    /**
     * Insert an "in-progress" traffic_visits row so controllers can reference
     * its id while the request is still running. The row is finalized in
     * terminate() with the real status code and duration.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $path = '/' . ltrim($request->path(), '/');

        if (! $this->shouldSkip($path)) {
            try {
                $id = AnalyticsRecorder::visit($request, 0, 0);
                if ($id !== null) {
                    $request->attributes->set(AnalyticsRecorder::VISIT_ATTR, $id);
                }
            } catch (Throwable $e) {
                Log::warning('TrackTraffic.handle failed', ['error' => $e->getMessage()]);
            }
        }

        return $next($request);
    }

    /**
     * After the response is sent, update the row with the final status code
     * and total request duration. Runs out-of-band so the user is not delayed.
     */
    public function terminate(Request $request, Response $response): void
    {
        $visitId = $request->attributes->get(AnalyticsRecorder::VISIT_ATTR);
        if (! $visitId) {
            return;
        }

        $start = defined('LARAVEL_START') ? LARAVEL_START : microtime(true);
        $duration = (int) round((microtime(true) - $start) * 1000);

        try {
            DB::table('traffic_visits')
                ->where('id', $visitId)
                ->update([
                    'status_code' => $response->getStatusCode(),
                    'duration_ms' => $duration,
                ]);
        } catch (Throwable $e) {
            Log::warning('TrackTraffic.terminate failed', ['error' => $e->getMessage()]);
        }
    }

    private function shouldSkip(string $path): bool
    {
        if ($path === '/favicon.ico' || $path === '/robots.txt') {
            return true;
        }
        foreach (['/build/', '/storage/', '/_debugbar/', '/vendor/', '/livewire/'] as $prefix) {
            if (str_starts_with($path, $prefix)) {
                return true;
            }
        }
        return false;
    }
}
