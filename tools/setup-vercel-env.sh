#!/bin/bash
# =============================================================
# Script: Set semua environment variables ke Vercel via CLI
# Jalankan dari Git Bash setelah isi kredensial di bawah
# =============================================================

# ---- ISI INI ----
DB_HOST=""
DB_PORT="3306"
DB_DATABASE=""
DB_USERNAME=""
DB_PASSWORD=""
APP_URL=""           # contoh: https://aplikasi-pinjaman.vercel.app
APP_KEY="base64:51QmBiY0eTPn5fzFH9vNX29zEZS2ztBXXH2R1kYulCQ="
MAIL_USERNAME=""     # email gmail kamu
MAIL_PASSWORD=""     # app password gmail
FONNTE_TOKEN="EfQgoTFgSoou3YbuGKRP"
# -----------------

# Validasi
if [ -z "$DB_HOST" ] || [ -z "$APP_URL" ]; then
    echo "ERROR: Isi dulu DB_HOST dan APP_URL di dalam script ini."
    exit 1
fi

# Cek vercel CLI
if ! command -v vercel &> /dev/null; then
    echo "Vercel CLI belum terinstall. Menginstall..."
    npm install -g vercel
fi

echo "========================================"
echo " Set Environment Variables ke Vercel"
echo "========================================"

set_env() {
    local key=$1
    local value=$2
    echo "$value" | vercel env add "$key" production --force 2>/dev/null || \
    echo "$value" | vercel env add "$key" production
    echo "  SET: $key"
}

set_env "APP_NAME"        "Aplikasi Pinjaman"
set_env "APP_ENV"         "production"
set_env "APP_KEY"         "$APP_KEY"
set_env "APP_DEBUG"       "false"
set_env "APP_URL"         "$APP_URL"
set_env "DB_CONNECTION"   "mysql"
set_env "DB_HOST"         "$DB_HOST"
set_env "DB_PORT"         "$DB_PORT"
set_env "DB_DATABASE"     "$DB_DATABASE"
set_env "DB_USERNAME"     "$DB_USERNAME"
set_env "DB_PASSWORD"     "$DB_PASSWORD"
set_env "SESSION_DRIVER"  "cookie"
set_env "CACHE_DRIVER"    "array"
set_env "QUEUE_CONNECTION" "sync"
set_env "FILESYSTEM_DISK" "public"
set_env "LOG_CHANNEL"     "stderr"
set_env "LOG_LEVEL"       "error"
set_env "MAIL_MAILER"     "smtp"
set_env "MAIL_HOST"       "smtp.gmail.com"
set_env "MAIL_PORT"       "587"
set_env "MAIL_USERNAME"   "$MAIL_USERNAME"
set_env "MAIL_PASSWORD"   "$MAIL_PASSWORD"
set_env "MAIL_ENCRYPTION" "tls"
set_env "FONNTE_TOKEN"    "$FONNTE_TOKEN"

echo ""
echo "========================================"
echo " Semua env variable sudah diset!"
echo " Sekarang jalankan: vercel --prod"
echo "========================================"
