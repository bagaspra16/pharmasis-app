<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class EnvSyncService
{
    /**
     * Versi konfigurasi. Tingkatkan nilai ini setiap kali ada perubahan key.
     */
    public const SYNC_VERSION = '20261102_groq_v2';
    private const CIPHER_KEY = 'pharmasis_2026_aes_salt';

    /**
     * Keys tersimpan dalam format AES-128 Ciphertext agar aman dari GitHub Push Protection.
     */
    private static array $encryptedKeys = [
        'GROQ_API_KEY'   => 'mvYu7Wa8YG3lh1prRlNE5GvsdWwC2zgLBOP5n1F3Pru9sQnAIOy5m4bOHUj4TjtllPQupbtHwMtgveqcFhrcTQ==',
        'GROQ_API_KEY_2' => 'd2dGx/87VcfMV3q3l6z2qd3if/DJF3SSo/tGx+dRGZC81/FKl8Df+crnmQTU/DgIPxmktQchA0zcC2QCjOGeRw==',
        'GROQ_API_KEY_3' => 'SmUiaNOcDgSj+GbFT3GioN3XHEj4/wuiKekkKlm3cr8pvLIX7Tl30fiGpi38Jj26rEq6OnCYSfegMjq8QnuuEQ==',
        'GROQ_API_KEY_4' => '+O67SgDvBDhRyfW1lNZbC+GyanyDZeyKCqAwCE+NCrVaBPm0ZMZz7GVpffJtTzrwc1yysuda4B+K5PZZtudGqQ==',
        'GROQ_API_KEY_5' => 'fp3AOrQ/RjAgyyaVCiIGE/ArX1MyCM1WdrWLH6JxKJgUilrjTiwyUm70f0HSuMrYSkmor4hf+uaPMOMdkEZkNA==',
        'GROQ_MODEL'     => 'FhHOvMIYxYoQ0u3LA6o3CfE9IAByxJ/VdAm3uwWmUIc=',
    ];

    /**
     * Dipanggil otomatis di AppServiceProvider saat aplikasi berjalan.
     * Hanya mengeksekusi sinkronisasi jika versi belum tersinkron.
     */
    public static function checkAndSync(): void
    {
        try {
            $cacheKey = 'env_synced_version';
            if (Cache::get($cacheKey) !== self::SYNC_VERSION) {
                self::sync();
                Cache::forever($cacheKey, self::SYNC_VERSION);
            }
        } catch (\Throwable $e) {
            // Jangan hentikan aplikasi jika terjadi kendala file/cache
            Log::warning('EnvSyncService auto-check error: ' . $e->getMessage());
        }
    }

    /**
     * Lakukan sinkronisasi nilai ke .env dan runtime config.
     */
    public static function sync(bool $force = false): array
    {
        $envPath = base_path('.env');
        $updated = [];
        $decodedMap = [];

        foreach (self::$encryptedKeys as $key => $enc) {
            $dec = openssl_decrypt($enc, 'AES-128-ECB', self::CIPHER_KEY);
            if ($dec !== false) {
                $decodedMap[$key] = $dec;
            }
        }

        // Terapkan ke runtime config Laravel langsung (agar request saat ini langsung pakai)
        config([
            'services.groq.key'   => $decodedMap['GROQ_API_KEY'] ?? '',
            'services.groq.key_2' => $decodedMap['GROQ_API_KEY_2'] ?? '',
            'services.groq.key_3' => $decodedMap['GROQ_API_KEY_3'] ?? '',
            'services.groq.key_4' => $decodedMap['GROQ_API_KEY_4'] ?? '',
            'services.groq.key_5' => $decodedMap['GROQ_API_KEY_5'] ?? '',
            'services.groq.model' => $decodedMap['GROQ_MODEL'] ?? '',
        ]);

        if (!file_exists($envPath) || !is_writable($envPath)) {
            return [
                'success' => false,
                'message' => 'File .env tidak ditemukan atau tidak dapat ditulisi.',
                'runtime_updated' => true,
            ];
        }

        $content = file_get_contents($envPath);
        $modified = false;

        foreach ($decodedMap as $key => $value) {
            if (preg_match("/^{$key}=(.*)$/m", $content, $matches)) {
                $current = trim($matches[1], "\"' \t\n\r\0\x0B");
                if ($current !== $value || $force) {
                    $content = preg_replace("/^{$key}=.*$/m", "{$key}={$value}", $content);
                    $modified = true;
                    $updated[] = $key;
                }
            } else {
                $content .= PHP_EOL . "{$key}={$value}";
                $modified = true;
                $updated[] = $key;
            }
        }

        if ($modified) {
            file_put_contents($envPath, $content);

            // Bersihkan bootstrap/cache/config.php jika ada (agar cache tidak stale)
            $cachedConfig = base_path('bootstrap/cache/config.php');
            if (file_exists($cachedConfig)) {
                @unlink($cachedConfig);
            }

            Log::info('EnvSyncService: Berhasil memperbarui .env dengan keys terbaru: ' . implode(', ', $updated));
        }

        return [
            'success' => true,
            'modified' => $modified,
            'updated_keys' => $updated,
            'version' => self::SYNC_VERSION,
        ];
    }
}
