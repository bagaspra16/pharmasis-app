<?php

namespace App\Http\Controllers;

use App\Services\AnalyticsRecorder;
use App\Services\Facilities\FacilityService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Searches use POST so positions and addresses stay out of URLs and access logs.
 * Neither is stored by the application.
 */
class FacilityController extends Controller
{
    public function __construct(private FacilityService $facilities) {}

    public function index()
    {
        return view('facilities');
    }

    public function geocode(Request $request)
    {
        abort_unless(config('facilities.enabled'), 404);
        $data = $request->validate(['query' => 'required|string|min:3|max:200', 'lang' => 'nullable|string|max:5']);

        try {
            return response()->json(['success' => true, 'places' => $this->facilities->geocode(trim($data['query']), $data['lang'] ?? 'en')]);
        } catch (\Throwable $e) {
            $busy = $e->getMessage() === 'busy';

            return response()->json(['success' => false, 'error' => $busy ? 'busy' : 'unavailable'], $busy ? 429 : 503);
        }
    }

    public function search(Request $request)
    {
        abort_unless(config('facilities.enabled'), 404);
        $startedAt = microtime(true);
        $data = $request->validate([
            'lat'      => 'required|numeric|between:-90,90',
            'lon'      => 'required|numeric|between:-180,180',
            'radius'   => ['nullable', Rule::in(config('facilities.radius_km'))],
            'category' => ['nullable', Rule::in(FacilityService::CATEGORIES)],
            'country'  => 'nullable|string|size:2',
        ]);

        // The emergency number is returned even when the facility data cannot be.
        $country = config('facilities.countries')[strtolower($data['country'] ?? '')] ?? null;
        $number = config('triage.emergency_numbers')[$country] ?? null;
        $emergency = ['number' => $number ?? config('triage.default_emergency_number'), 'country' => $number ? $country : null];

        try {
            $found = $this->facilities->search(
                (float) $data['lat'], (float) $data['lon'],
                (int) ($data['radius'] ?? config('facilities.default_radius_km')),
                $data['category'] ?? 'care'
            );
        } catch (\RuntimeException $e) {
            AnalyticsRecorder::medicheck($request, 'nearby', ['success' => false, 'error_code' => 'map_unavailable']);

            return response()->json(['success' => false, 'error' => 'unavailable', 'emergency' => $emergency], 503);
        }

        AnalyticsRecorder::medicheck($request, 'nearby', [
            'providers_returned' => count($found['results']),
            'success'            => true,
            'duration_ms'        => (int) round((microtime(true) - $startedAt) * 1000),
        ]);

        return response()->json(['success' => true, 'emergency' => $emergency] + $found);
    }
}
