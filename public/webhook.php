<?php

// =============================================================================
// public/webhook.php — GitHub Webhook Handler untuk Pharmasis App
// URL: https://yourdomain.com/webhook.php?token=WEBHOOK_SECRET_TOKEN
//
// Setup di GitHub:
//   Repository → Settings → Webhooks → Add webhook
//   Payload URL : https://yourdomain.com/webhook.php
//   Content type: application/json
//   Secret      : isi sama dengan WEBHOOK_SECRET_TOKEN di bawah
//   Event       : Just the push event
// =============================================================================

// ── Konfigurasi ──────────────────────────────────────────────────────────────
define('WEBHOOK_SECRET', 'pharmasis_webhook_2024_secret');  // <-- ganti token ini
define('BRANCH',         'main');
define('PROJECT_DIR',    dirname(__DIR__));                  // root pharmasis-app/
define('LOG_FILE',       PROJECT_DIR . '/storage/logs/webhook.log');

// ── Helpers ──────────────────────────────────────────────────────────────────
function logMsg(string $msg): void {
    $line = '[' . date('Y-m-d H:i:s') . '] ' . $msg . PHP_EOL;
    file_put_contents(LOG_FILE, $line, FILE_APPEND | LOCK_EX);
    echo $line;
}

function setEnv(string $key, string $value, string $envFile): void {
    $content = file_exists($envFile) ? file_get_contents($envFile) : '';
    if (preg_match("/^{$key}=.*/m", $content)) {
        $content = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $content);
    } else {
        $content .= PHP_EOL . "{$key}={$value}";
    }
    file_put_contents($envFile, $content);
}

function run(string $cmd): string {
    return shell_exec("cd " . PROJECT_DIR . " && {$cmd} 2>&1") ?? '';
}

// ── Verifikasi Keamanan ───────────────────────────────────────────────────────
$payload  = file_get_contents('php://input');
$hubSig   = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';
$expected = 'sha256=' . hash_hmac('sha256', $payload, WEBHOOK_SECRET);

if (!hash_equals($expected, $hubSig)) {
    http_response_code(403);
    logMsg('ERROR: Signature tidak valid — request ditolak');
    exit('Forbidden');
}

// ── Cek branch ───────────────────────────────────────────────────────────────
$data   = json_decode($payload, true);
$branch = basename($data['ref'] ?? '');

if ($branch !== BRANCH) {
    http_response_code(200);
    logMsg("SKIP: Push ke branch '{$branch}', bukan '" . BRANCH . "'");
    exit('Skipped');
}

// ── 1. Git Pull ───────────────────────────────────────────────────────────────
logMsg("--- Deploy dimulai (push oleh: " . ($data['pusher']['name'] ?? 'unknown') . ") ---");

$pullResult = run('git pull origin ' . BRANCH);
logMsg("Git pull: " . trim($pullResult));

// ── 2. Update .env dari .secrets ─────────────────────────────────────────────
$secretsFile = PROJECT_DIR . '/.secrets';
$envFile     = PROJECT_DIR . '/.env';

if (file_exists($secretsFile)) {
    // Parse file .secrets (format: export KEY="VALUE")
    $lines = file($secretsFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if (str_starts_with($line, '#') || $line === '') continue;

        // Hapus prefix "export " jika ada
        $line = preg_replace('/^export\s+/', '', $line);

        // Pisah KEY=VALUE
        [$key, $value] = explode('=', $line, 2);
        $value = trim($value, '"\'');

        if ($key && $value) {
            setEnv(trim($key), $value, $envFile);
        }
    }
    logMsg('.env berhasil diperbarui dari .secrets');
} else {
    logMsg('WARNING: File .secrets tidak ditemukan, .env tidak diupdate');
}

// ── 3. Laravel post-deploy ────────────────────────────────────────────────────
logMsg('Config cache: '  . trim(run('php artisan config:clear && php artisan config:cache')));
logMsg('Route cache: '   . trim(run('php artisan route:cache')));
logMsg('View cache: '    . trim(run('php artisan view:cache')));

// Composer install (opsional, uncomment jika perlu)
// logMsg('Composer: ' . trim(run('composer install --no-dev --optimize-autoloader --no-interaction')));

logMsg("--- Deploy selesai ---");

http_response_code(200);
echo 'OK';
