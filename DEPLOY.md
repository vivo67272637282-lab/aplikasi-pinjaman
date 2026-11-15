# Panduan Deploy: Railway (Database) + Vercel (Aplikasi)

## Prasyarat
- Akun [Railway](https://railway.app) (login via GitHub)
- Akun [Vercel](https://vercel.com) (login via GitHub)
- Repository project ini sudah di-push ke GitHub
- Git sudah terinstall di komputer

---

## Bagian 1 — Setup Database di Railway

### Langkah 1: Buat MySQL di Railway

1. Login ke [railway.app](https://railway.app)
2. Klik **"New Project"** → **"Provision MySQL"**
3. Tunggu hingga service MySQL aktif (beberapa detik)

### Langkah 2: Ambil Kredensial Database

1. Klik service MySQL → tab **"Connect"** atau **"Variables"**
2. Catat nilai berikut (akan dipakai di Vercel nanti):
   ```
   MYSQLHOST      → DB_HOST di .env
   MYSQLPORT      → DB_PORT di .env  
   MYSQLDATABASE  → DB_DATABASE di .env
   MYSQLUSER      → DB_USERNAME di .env
   MYSQLPASSWORD  → DB_PASSWORD di .env
   ```

### Langkah 3: Import Schema Database

**Opsi A — via MySQL CLI** (jika MySQL client terinstall):
```bash
mysql -h <MYSQLHOST> -P <MYSQLPORT> -u <MYSQLUSER> -p<MYSQLPASSWORD> <MYSQLDATABASE> < database/schema.sql
```

**Opsi B — via GUI (TablePlus / DBeaver / HeidiSQL)**:
1. Buat koneksi baru dengan kredensial Railway
2. Buka file `database/schema.sql`
3. Jalankan seluruh query

**Opsi C — via phpMyAdmin Railway**:
1. Di Railway, klik service MySQL → **"Query"** tab
2. Copy-paste isi `database/schema.sql` → Run

### Langkah 4: Seed Data Admin (Opsional)

Setelah schema berhasil diimport, jalankan seeder untuk membuat user admin:
```bash
# Di komputer lokal, ubah .env dulu ke koneksi Railway, lalu:
php artisan db:seed --class=UsersTableSeeder
```

Atau insert manual:
```sql
INSERT INTO users (name, username, email, password, role, is_active, created_at, updated_at)
VALUES ('Admin', 'admin', 'admin@example.com', '$2y$10$...', 'admin', 1, NOW(), NOW());
```
(Generate hash password dengan `php artisan tinker` → `bcrypt('password123')`)

---

## Bagian 2 — Deploy Aplikasi ke Vercel

### Langkah 1: Push ke GitHub

Pastikan project sudah ada di GitHub. Jika belum:
```bash
git init
git add .
git commit -m "Initial commit"
git remote add origin https://github.com/USERNAME/REPO-NAME.git
git push -u origin main
```

> **Catatan:** `.env` sudah ada di `.gitignore`, jadi tidak akan ter-push. Bagus!

### Langkah 2: Import Project di Vercel

1. Login ke [vercel.com](https://vercel.com)
2. Klik **"Add New..."** → **"Project"**
3. Pilih repository GitHub `aplikasi-pinjaman`
4. Framework Preset: **"Other"**
5. Jangan ubah apapun, langsung klik **"Deploy"** dulu (akan gagal, itu normal)

### Langkah 3: Set Environment Variables di Vercel

Di dashboard Vercel → project → **Settings** → **Environment Variables**, tambahkan:

| Key | Value |
|-----|-------|
| `APP_NAME` | `Aplikasi Pinjaman` |
| `APP_ENV` | `production` |
| `APP_KEY` | `base64:51QmBiY0eTPn5fzFH9vNX29zEZS2ztBXXH2R1kYulCQ=` |
| `APP_DEBUG` | `false` |
| `APP_URL` | `https://NAMA-PROJECT.vercel.app` |
| `DB_CONNECTION` | `mysql` |
| `DB_HOST` | *(MYSQLHOST dari Railway)* |
| `DB_PORT` | *(MYSQLPORT dari Railway)* |
| `DB_DATABASE` | *(MYSQLDATABASE dari Railway)* |
| `DB_USERNAME` | *(MYSQLUSER dari Railway)* |
| `DB_PASSWORD` | *(MYSQLPASSWORD dari Railway)* |
| `SESSION_DRIVER` | `cookie` |
| `CACHE_DRIVER` | `array` |
| `QUEUE_CONNECTION` | `sync` |
| `FILESYSTEM_DISK` | `public` |
| `LOG_CHANNEL` | `stderr` |
| `LOG_LEVEL` | `error` |
| `MAIL_MAILER` | `smtp` |
| `MAIL_HOST` | `smtp.gmail.com` |
| `MAIL_PORT` | `587` |
| `MAIL_USERNAME` | *(email gmail)* |
| `MAIL_PASSWORD` | *(app password gmail)* |
| `MAIL_ENCRYPTION` | `tls` |
| `FONNTE_TOKEN` | *(token fonnte WA)* |

### Langkah 4: Redeploy

Setelah semua env variable diset:
1. Vercel → project → **Deployments**
2. Klik deployment terbaru → **"Redeploy"**
3. Tunggu hingga status **"Ready"**

### Langkah 5: Verifikasi

Buka URL `https://NAMA-PROJECT.vercel.app` — aplikasi harus tampil.

---

## Catatan Penting

### Storage / Upload File
Vercel bersifat **stateless** (filesystem tidak persisten). File yang diupload (foto, dokumen) akan hilang setelah redeploy. Solusi:
- Gunakan **Cloudinary** atau **AWS S3** untuk penyimpanan file
- Update `FILESYSTEM_DISK` ke `s3` dan konfigurasi driver S3

### Session
Session menggunakan `cookie` driver agar tidak butuh filesystem/Redis.

### Cache
Cache menggunakan `array` driver (in-memory, tidak persisten antar request). Cukup untuk aplikasi ini.

### Alternatif Deploy yang Lebih Cocok untuk Laravel
Jika menemui masalah di Vercel, platform berikut lebih native untuk PHP:
- **Railway** (tambah service PHP/Nixpacks) — recommended
- **Render** (free tier tersedia)
- **Fly.io** (dengan Dockerfile)

---

## Troubleshooting

| Masalah | Solusi |
|---------|--------|
| Error 500 | Cek Vercel Function Logs di dashboard |
| DB Connection refused | Pastikan Railway MySQL tidak di-sleep, cek kredensial |
| Storage link error | Jalankan `php artisan storage:link` tidak berlaku di Vercel |
| Session tidak persisten | Pastikan `SESSION_DRIVER=cookie` |
| APP_KEY error | Pastikan nilai `APP_KEY` sudah diset di env variables |
