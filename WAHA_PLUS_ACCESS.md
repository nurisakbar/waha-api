# 🔐 WAHA PLUS Access Guide

Panduan lengkap untuk mendapatkan akses WAHA PLUS dan setup Docker login.

## 📋 Apa itu WAHA PLUS?

WAHA PLUS adalah versi berbayar dari WAHA yang menyediakan:
- ✅ **Multiple Sessions** - Bisa membuat banyak session WhatsApp
- ✅ **Advanced Features** - Fitur-fitur premium
- ✅ **Priority Support** - Dukungan prioritas
- ✅ **Regular Updates** - Update terbaru

## 🔑 Cara Mendapatkan Akses WAHA PLUS

### 1. Subscribe ke WAHA PLUS

WAHA PLUS adalah layanan berbayar. Untuk mendapatkan akses:

1. **Kunjungi website WAHA:**
   - https://waha.devlike.pro/
   - Atau https://boosty.to/wa-http-api

2. **Pilih paket subscription:**
   - Pilih paket yang sesuai kebutuhan
   - Lakukan pembayaran

3. **Dapatkan akses:**
   - Setelah pembayaran, Anda akan mendapat akses ke Docker image `devlikeapro/waha-plus`
   - Akses biasanya dikaitkan dengan Docker Hub account Anda

### 2. Hubungkan dengan Docker Hub

Setelah subscribe, Anda perlu:

1. **Docker Hub Account:**
   - Buat akun di https://hub.docker.com (jika belum punya)
   - Atau gunakan akun yang sudah ada

2. **Hubungkan Subscription:**
   - Admin WAHA akan memberikan akses ke Docker Hub organization
   - Atau Anda akan mendapat token/credentials khusus

3. **Verify Access:**
   ```bash
   docker login
   # Masukkan username dan password Docker Hub Anda
   
   # Test pull image
   docker pull devlikeapro/waha-plus:latest
   ```

## 🚀 Setup Docker Login

### Cara 1: Login dengan Username/Password

```bash
# Login ke Docker Hub
docker login

# Masukkan:
# Username: <your-dockerhub-username>
# Password: <your-dockerhub-password>
```

**Contoh:**
```
$ docker login
Username: yourusername
Password: ********
Login Succeeded
```

### Cara 2: Login dengan Access Token (Lebih Aman)

1. **Buat Access Token di Docker Hub:**
   - Login ke https://hub.docker.com
   - Settings → Security → New Access Token
   - Copy token yang dihasilkan

2. **Login dengan Token:**
   ```bash
   echo "YOUR_ACCESS_TOKEN" | docker login --username YOUR_USERNAME --password-stdin
   ```

### Cara 3: Login dengan Credentials File

```bash
# Simpan credentials di file
cat > ~/.docker/config.json <<EOF
{
  "auths": {
    "https://index.docker.io/v1/": {
      "auth": "$(echo -n 'username:password' | base64)"
    }
  }
}
EOF
```

## ✅ Verify Login

Setelah login, verify dengan:

```bash
# Check login status
docker info | grep Username

# Test pull image
docker pull devlikeapro/waha-plus:latest
```

Jika berhasil, Anda akan melihat:
```
latest: Pulling from devlikeapro/waha-plus
...
Status: Downloaded newer image for devlikeapro/waha-plus:latest
```

## ⚠️ Troubleshooting

### Error: "pull access denied"

**Penyebab:**
- Belum subscribe ke WAHA PLUS
- Belum login ke Docker Hub
- Subscription expired
- Docker Hub account tidak terhubung dengan subscription

**Solusi:**

1. **Check subscription status:**
   - Pastikan subscription masih aktif
   - Hubungi support WAHA jika perlu

2. **Re-login:**
   ```bash
   docker logout
   docker login
   ```

3. **Verify access:**
   ```bash
   docker pull devlikeapro/waha-plus:latest
   ```

### Error: "unauthorized: authentication required"

**Penyebab:**
- Login expired
- Wrong credentials

**Solusi:**
```bash
# Logout dan login ulang
docker logout
docker login
```

## 🔄 Auto-Login (Optional)

Untuk production, Anda bisa setup auto-login:

### 1. Save Credentials Securely

```bash
# Create credentials helper (lebih aman)
mkdir -p ~/.docker
cat > ~/.docker/config.json <<EOF
{
  "auths": {
    "https://index.docker.io/v1/": {}
  },
  "credsStore": "osxkeychain"  # macOS
  # atau "credsStore": "pass" untuk Linux
}
EOF
```

### 2. Login Once

```bash
docker login
# Credentials akan tersimpan secara aman
```

## 📝 Summary

**Yang Anda Butuhkan:**

1. ✅ **Subscription WAHA PLUS** (berbayar)
2. ✅ **Docker Hub Account** (gratis)
3. ✅ **Akses ke image** (diberikan setelah subscribe)
4. ✅ **Login ke Docker Hub** (`docker login`)

**Langkah Setup:**

```bash
# 1. Login ke Docker Hub
docker login

# 2. Verify access
docker pull devlikeapro/waha-plus:latest

# 3. Setup project
./waha.sh setup

# 4. Start WAHA
./waha.sh start
```

## 🔗 Resources

- **WAHA Website**: https://waha.devlike.pro/
- **WAHA Pricing**: https://waha.devlike.pro/pricing
- **Docker Hub**: https://hub.docker.com
- **Docker Login Docs**: https://docs.docker.com/engine/reference/commandline/login/

---

**Last Updated**: 2025-01-27

