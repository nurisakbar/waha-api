# 🐳 Docker Setup Guide

Panduan setup Docker untuk WAHA API Platform.

## 📋 Services

Docker Compose ini menyediakan 2 service utama:

1. **waha** - WAHA WhatsApp API (Port 3000)
2. **redis** - Redis Cache (Port 6379) - dapat diakses dari host

**Catatan**: 
- Laravel berjalan di luar Docker menggunakan MAMP/PHP lokal
- MySQL menggunakan MAMP (Port 8889) - tidak menggunakan Docker

## 🔐 Security Setup

### ⚠️ IMPORTANT: WAHA PLUS (Paid Version)

Proyek ini menggunakan **WAHA PLUS** (versi berbayar) yang memerlukan Docker login.

### 1. Login ke Docker Hub (WAJIB untuk WAHA PLUS)

```bash
# Login ke Docker Hub dengan akun yang memiliki akses WAHA PLUS
docker login

# Masukkan username dan password Docker Hub Anda
```

### 2. Setup Environment

**Setup Otomatis (Recommended):**

```bash
# Setup environment dengan password otomatis
./waha.sh setup
```

Script akan:
- Membuat file `.env` dari template
- Generate password yang aman secara otomatis
- Setup direktori yang diperlukan
- Menggunakan `devlikeapro/waha-plus:latest` sebagai default

**Setup Manual:**

```bash
# Copy template
cp docker.env.example .env

# Edit file .env dan ganti semua password dengan yang aman
nano .env
```

## 🚀 Quick Start

### 1. Login ke Docker Hub (WAJIB)

```bash
docker login
```

### 2. Setup Environment (First Time Only)

```bash
# Setup environment
./waha.sh setup
```

### 3. Start Services

```bash
# Start WAHA (akan otomatis check docker login)
./waha.sh start

# Atau start semua services (WAHA + Redis)
docker-compose up -d
```

**Catatan:** Script `waha.sh start` akan otomatis meminta login jika belum login ke Docker Hub.

### 2. Setup Laravel Environment

Edit file `frontend/.env`:

```env
# Database - menggunakan MySQL dari MAMP
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=8889
DB_DATABASE=wahaapi
DB_USERNAME=root
DB_PASSWORD=root

# Redis - menggunakan Redis dari Docker
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_PASSWORD=  # Kosongkan jika tidak ada password

# WAHA API
WAHA_API_URL=http://localhost:3000
```

### 3. Run Laravel Migrations

```bash
cd frontend
php artisan migrate
```

## 📝 Useful Commands

### WAHA Management (Menggunakan waha.sh)

```bash
# Setup environment
./waha.sh setup

# Start WAHA
./waha.sh start

# Stop WAHA
./waha.sh stop

# Restart WAHA
./waha.sh restart

# Check status
./waha.sh status

# View logs
./waha.sh logs
./waha.sh logs -f  # Follow logs

# Backup sessions
./waha.sh backup

# Restore sessions
./waha.sh restore <backup_file>

# Update WAHA
./waha.sh update

# Open shell in container
./waha.sh shell

# Show help
./waha.sh help
```

### Docker Compose Commands (Alternative)

```bash
# View logs semua services
docker-compose logs -f

# Stop semua services
docker-compose stop

# Remove containers
docker-compose down
```

## 🔍 Troubleshooting

### Docker Login Issues

**Error: "pull access denied for devlikeapro/waha-plus"**

**Penyebab:**
- Belum login ke Docker Hub
- Akun tidak memiliki akses ke WAHA PLUS
- Subscription expired

**Solusi:**

1. **Login ke Docker Hub:**
```bash
docker login
# Masukkan username dan password yang memiliki akses WAHA PLUS
```

2. **Verify login:**
```bash
docker info | grep Username
```

3. **Test pull image:**
```bash
docker pull devlikeapro/waha-plus:latest
```

4. **Jika masih error, check subscription:**
- Pastikan akun Docker Hub Anda memiliki akses ke WAHA PLUS
- Hubungi support WAHA jika subscription expired

### WAHA Connection Issues

Jika Laravel tidak bisa connect ke WAHA:

1. Pastikan WAHA container running:
   ```bash
   docker-compose ps waha
   ```

2. Test WAHA API:
   ```bash
   curl http://localhost:3000/api/version
   ```

3. Check logs:
   ```bash
   docker-compose logs waha
   ```

### Redis Connection Issues

Jika Laravel tidak bisa connect ke Redis:

1. Pastikan Redis container running:
   ```bash
   docker-compose ps redis
   ```

2. Test Redis connection:
   ```bash
   redis-cli -h 127.0.0.1 -p 6379 ping
   ```

3. Jika menggunakan password:
   ```bash
   redis-cli -h 127.0.0.1 -p 6379 -a your_password ping
   ```

### Port Conflicts

Jika port sudah digunakan:

1. Edit `.env` dan ubah port:
   ```env
   REDIS_PORT=6380
   WAHA_PORT=3001
   ```

2. Update `frontend/.env` sesuai:
   ```env
   REDIS_PORT=6380
   WAHA_API_URL=http://localhost:3001
   ```

### Permission Issues

```bash
# Fix storage permissions (jika perlu)
cd frontend
chmod -R 775 storage bootstrap/cache
```

## 💾 Data Persistence

Data disimpan di Docker volumes:
- `waha-sessions` - WAHA session data
- `redis-data` - Redis data

### Backup Redis

```bash
# Backup Redis data
docker exec waha-redis redis-cli SAVE
docker cp waha-redis:/data/dump.rdb ./redis-backup.rdb
```

## 🔄 Update Services

```bash
# Pull latest images
docker-compose pull

# Restart dengan image baru
docker-compose up -d
```

## 📊 Health Checks

Semua services memiliki health checks:

```bash
# Check service health
docker-compose ps
```

## ⚠️ Security Best Practices

1. **Jangan commit file `.env`** - sudah di-ignore oleh git
2. **Gunakan password yang kuat** - minimal 16 karakter, kombinasi huruf, angka, simbol
3. **Ganti password default** - jangan gunakan password dari contoh
4. **Rotate password secara berkala** - terutama untuk production
5. **Limit network access** - jika perlu, batasi akses ke Redis hanya dari aplikasi

## 📌 MAMP MySQL Configuration

Karena MySQL menggunakan MAMP, pastikan:

1. MAMP MySQL running di port 8889
2. Database `wahaapi` sudah dibuat
3. User `root` dengan password `root` (atau sesuai konfigurasi MAMP)
4. Update `frontend/.env` dengan kredensial MAMP yang benar

---

**Last Updated**: 2025-01-27
