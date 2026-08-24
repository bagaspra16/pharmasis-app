<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class GeoIpService
{
    /**
     * Resolve user location (City, Region, Country) from IP Address.
     * Caches results for 24 hours to prevent rate limits.
     *
     * @param string $ip
     * @return string
     */
    public function resolveLocation(string $ip): string
    {
        // 1. Clean IP address (remove ports or multiple headers)
        $ip = trim(explode(',', $ip)[0]);

        // 2. Fallback for local / private range IPs
        if ($this->isPrivateIp($ip)) {
            // Use a stable public IP in Jakarta, Indonesia for local development testing
            $ip = '103.10.200.1';
        }

        $cacheKey = 'geoip_location_' . md5($ip);

        return Cache::remember($cacheKey, 86400, function () use ($ip) {
            // Try ip-api.com first (45 requests/min limit)
            try {
                $response = Http::timeout(5)->get("http://ip-api.com/json/{$ip}");
                if ($response->ok() && $response->json('status') === 'success') {
                    $city = $response->json('city');
                    $region = $response->json('regionName');
                    $country = $response->json('country');

                    if ($city && $region && $country) {
                        return "{$city}, {$region}, {$country}";
                    }
                }
            } catch (\Exception $e) {
                Log::warning("GeoIpService: ip-api.com lookup failed for {$ip}", ['error' => $e->getMessage()]);
            }

            // Fallback to ipapi.co (1000 requests/day limit)
            try {
                $response = Http::timeout(5)->get("https://ipapi.co/{$ip}/json/");
                if ($response->ok() && !$response->json('error')) {
                    $city = $response->json('city');
                    $region = $response->json('region');
                    $country = $response->json('country_name');

                    if ($city && $region && $country) {
                        return "{$city}, {$region}, {$country}";
                    }
                }
            } catch (\Exception $e) {
                Log::warning("GeoIpService: ipapi.co lookup failed for {$ip}", ['error' => $e->getMessage()]);
            }

            // Ultimate fallback
            return 'Jakarta, Jakarta, Indonesia';
        });
    }

    /**
     * Check if an IP address is in a private or loopback range.
     *
     * @param string $ip
     * @return bool
     */
    private function isPrivateIp(string $ip): bool
    {
        if (in_array($ip, ['127.0.0.1', '::1'])) {
            return true;
        }

        return !filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        );
    }
}
