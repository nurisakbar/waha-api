# 🐳 WAHA Docker Image Setup

Panduan untuk setup Docker image WAHA yang benar.

## 📦 Image Options

WAHA memiliki 2 versi:

### 1. WAHA Free (devlikeapro/waha)
- ✅ **Gratis** - Tidak perlu login
- ✅ **Open source** - Tersedia di Docker Hub
- ⚠️ **Limited** - Hanya 1 session (default)
- 📦 **Image**: `devlikeapro/waha:latest`

### 2. WAHA PLUS (devlikeapro/waha-plus)
- 💰 **Berbayar** - Memerlukan subscription
- ✅ **Full features** - Multiple sessions, advanced features
- 🔐 **Requires login** - Perlu `docker login` terlebih dahulu
- 📦 **Image**: `devlikeapro/waha-plus:latest`

## 🚀 Quick Setup

### Option 1: Free Version (Recommended untuk testing)

Edit file `.env`:

```env
WAHA_IMAGE=devlikeapro/waha:latest
```

Kemudian:

```bash
./waha.sh start
```

### Option 2: WAHA PLUS (Paid Version)

1. **Login ke Docker Hub:**

```bash
docker login
# Masukkan username dan password Docker Hub Anda
```

2. **Edit file `.env`:**

```env
WAHA_IMAGE=devlikeapro/waha-plus:latest
```

3. **Start WAHA:**

```bash
./waha.sh start
```

## 🔧 Setup Otomatis

Script `waha.sh setup` akan otomatis menggunakan versi free:

```bash
./waha.sh setup
# Akan membuat .env dengan WAHA_IMAGE=devlikeapro/waha:latest
```

## ⚠️ Troubleshooting

### Error: "pull access denied for devlikeapro/waha-plus"

**Penyebab:**
- Menggunakan WAHA PLUS tanpa login
- Atau subscription expired

**Solusi:**

1. **Login ke Docker Hub:**
```bash
docker login
```

2. **Atau switch ke free version:**
```bash
# Edit .env
nano .env
# Ubah: WAHA_IMAGE=devlikeapro/waha:latest

# Restart
./waha.sh restart
```

### Error: "repository does not exist"

**Penyebab:**
- Image name salah
- Internet connection issue

**Solusi:**

1. **Check image name di .env:**
```bash
grep WAHA_IMAGE .env
```

2. **Test pull manual:**
```bash
docker pull devlikeapro/waha:latest
```

3. **Check internet connection:**
```bash
ping docker.io
```

## 📝 Update Image

Untuk mengubah image (misalnya dari free ke plus):

1. **Edit .env:**
```bash
nano .env
# Ubah WAHA_IMAGE=devlikeapro/waha-plus:latest
```

2. **Login (jika menggunakan plus):**
```bash
docker login
```

3. **Restart:**
```bash
./waha.sh restart
```

## 🔍 Check Current Image

```bash
# Check image di .env
grep WAHA_IMAGE .env

# Check image yang digunakan container
docker inspect waha-api | grep Image
```

## 💡 Recommendations

- **Development/Testing**: Gunakan `devlikeapro/waha:latest` (free)
- **Production**: Gunakan `devlikeapro/waha-plus:latest` (jika butuh multiple sessions)
- **Single Session**: Free version sudah cukup

---

**Last Updated**: 2025-01-27

