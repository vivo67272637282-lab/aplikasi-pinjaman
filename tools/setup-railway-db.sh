#!/bin/bash
# =============================================================
# Script: Import schema + seed ke Railway MySQL
# Jalankan dari Git Bash setelah isi kredensial di bawah
# =============================================================

# ---- ISI INI DENGAN KREDENSIAL DARI RAILWAY ----
DB_HOST=""
DB_PORT=""
DB_DATABASE=""
DB_USERNAME=""
DB_PASSWORD=""
# ------------------------------------------------

# Validasi input
if [ -z "$DB_HOST" ] || [ -z "$DB_PASSWORD" ]; then
    echo "ERROR: Isi dulu kredensial Railway di dalam script ini."
    exit 1
fi

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"
SCHEMA_FILE="$PROJECT_ROOT/database/schema.sql"

echo "========================================"
echo " Setup Railway Database"
echo "========================================"
echo " Host     : $DB_HOST"
echo " Port     : $DB_PORT"
echo " Database : $DB_DATABASE"
echo " User     : $DB_USERNAME"
echo "========================================"
echo ""

# Cek apakah mysql client tersedia
if ! command -v mysql &> /dev/null; then
    echo "ERROR: mysql client tidak ditemukan."
    echo "Install dulu: https://dev.mysql.com/downloads/mysql/"
    exit 1
fi

# Test koneksi
echo "[1/3] Test koneksi ke Railway..."
mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USERNAME" -p"$DB_PASSWORD" -e "SELECT 1;" "$DB_DATABASE" > /dev/null 2>&1
if [ $? -ne 0 ]; then
    echo "ERROR: Koneksi gagal. Cek kredensial Railway."
    exit 1
fi
echo "      Koneksi berhasil!"

# Import schema
echo "[2/3] Import schema database..."
mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" < "$SCHEMA_FILE" 2>&1
if [ $? -ne 0 ]; then
    echo "WARNING: Ada error saat import schema (mungkin tabel sudah ada, lanjutkan...)"
else
    echo "      Schema berhasil diimport!"
fi

# Seed user admin
echo "[3/3] Insert user admin & operator..."
mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" <<'EOF'
INSERT IGNORE INTO users (name, username, email, password, role, is_active, created_at, updated_at)
VALUES
  ('Admin Sistem', 'admin',    'admin@example.com',    '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin',    1, NOW(), NOW()),
  ('Operator',     'operator', 'operator@example.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'operator', 1, NOW(), NOW());
EOF
echo "      Seeder selesai!"
echo ""
echo "========================================"
echo " SELESAI!"
echo " Login credentials:"
echo "   Admin    -> username: admin    | password: password"
echo "   Operator -> username: operator | password: password"
echo "========================================"
