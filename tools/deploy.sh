#!/bin/bash
# =============================================================
# Script: Full deploy ke Vercel (jalankan setelah setup-vercel-env.sh)
# =============================================================

echo "========================================"
echo " Deploy Aplikasi ke Vercel"
echo "========================================"

# Cek vercel CLI
if ! command -v vercel &> /dev/null; then
    echo "Install Vercel CLI dulu..."
    npm install -g vercel
fi

cd "$(dirname "$0")/.."

echo "[1/3] Login ke Vercel (skip jika sudah login)..."
vercel whoami 2>/dev/null || vercel login

echo "[2/3] Link project ke Vercel..."
vercel link --yes

echo "[3/3] Deploy ke production..."
vercel --prod

echo ""
echo "========================================"
echo " Deploy selesai!"
echo " Cek URL di output di atas."
echo "========================================"
