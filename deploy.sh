#!/bin/bash

# =============================================================================
# deploy.sh — Script deploy otomatis untuk Pharmasis App
# Jalankan di server production: bash deploy.sh
# =============================================================================

set -e  # Hentikan script jika ada error

echo "🚀 Memulai proses deploy Pharmasis..."

# ---------------------------------------------------------------------------
# 1. Git Pull
# ---------------------------------------------------------------------------
echo "📥 Menarik kode terbaru dari GitHub..."
git pull origin main

# ---------------------------------------------------------------------------
# 2. Update ENV — patch nilai yang berubah tanpa overwrite seluruh file .env
# ---------------------------------------------------------------------------
echo "🔑 Memperbarui konfigurasi API di .env..."

# Fungsi: set_env KEY VALUE
# Menambahkan key jika belum ada, atau mengupdate nilainya jika sudah ada
set_env() {
    local key="$1"
    local value="$2"
    local env_file=".env"

    if grep -q "^${key}=" "$env_file" 2>/dev/null; then
        # Key sudah ada → update
        sed -i.bak "s|^${key}=.*|${key}=${value}|" "$env_file"
    else
        # Key belum ada → tambahkan di akhir file
        echo "${key}=${value}" >> "$env_file"
    fi
}

# ── GROQ API Keys ──────────────────────────────────────────────────────────
# Keys dibaca dari file secrets di server (JANGAN taruh key di sini langsung)
# Buat file /etc/pharmasis/secrets.sh di server dengan isi:
#   export GROQ_API_KEY="gsk_..."
#   export GROQ_API_KEY_2="gsk_..."
#   dst.
# Lalu file ini akan membacanya secara aman.

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
    echo "✅ GROQ API Keys berhasil dimuat dari .secrets"
else
    echo "⚠️  File .secrets tidak ditemukan di dalam folder project"
    echo "   Buat file pharmasis-app/.secrets dan isi dengan:"
    echo "     export GROQ_API_KEY=\"gsk_...\""
    echo "     export GROQ_API_KEY_2=\"gsk_...\""
    echo "     dst."
fi

# Hapus file backup sementara dari sed
rm -f .env.bak

# ---------------------------------------------------------------------------
# 3. Laravel post-deploy commands
# ---------------------------------------------------------------------------
echo "⚙️  Menjalankan perintah Laravel..."

php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Jalankan migration jika ada yang pending (aman untuk production)
# php artisan migrate --force

# ---------------------------------------------------------------------------
# 4. Install/update Composer dependencies (skip dev)
# ---------------------------------------------------------------------------
echo "📦 Mengupdate Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# ---------------------------------------------------------------------------
# 5. Selesai
# ---------------------------------------------------------------------------
echo ""
echo "✅ Deploy selesai!"
echo "   - Kode terbaru sudah ditarik dari GitHub"
echo "   - GROQ API Keys sudah diperbarui di .env"
echo "   - Config Laravel sudah di-cache ulang"

