#!/bin/bash

# =============================================================================
# post-deploy.sh — Dijalankan otomatis oleh cPanel setelah git pull
# JANGAN jalankan manual, gunakan deploy.sh untuk deploy manual
# =============================================================================

echo "⚙️  Post-deploy: memperbarui .env dan cache Laravel..."

# Fungsi patch .env per-baris (tidak overwrite seluruh file)
set_env() {
    local key="$1"
    local value="$2"
    local env_file=".env"

    if grep -q "^${key}=" "$env_file" 2>/dev/null; then
        sed -i.bak "s|^${key}=.*|${key}=${value}|" "$env_file"
    else
        echo "${key}=${value}" >> "$env_file"
    fi
}

# Baca secrets dari file .secrets (upload via cPanel File Manager)
SECRETS_FILE="$(dirname "$0")/.secrets"

if [ -f "$SECRETS_FILE" ]; then
    # shellcheck source=/dev/null
    source "$SECRETS_FILE"
    set_env "GROQ_API_KEY"   "${GROQ_API_KEY}"
    set_env "GROQ_API_KEY_2" "${GROQ_API_KEY_2}"
    set_env "GROQ_API_KEY_3" "${GROQ_API_KEY_3}"
    set_env "GROQ_API_KEY_4" "${GROQ_API_KEY_4}"
    set_env "GROQ_API_KEY_5" "${GROQ_API_KEY_5}"
    set_env "GROQ_MODEL"     "${GROQ_MODEL:-openai/gpt-oss-120b}"
    echo "✅ GROQ API Keys berhasil diperbarui"
else
    echo "⚠️  File .secrets tidak ditemukan — upload dulu via cPanel File Manager"
fi

rm -f .env.bak

# Laravel cache
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Post-deploy selesai!"

