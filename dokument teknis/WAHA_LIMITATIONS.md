# ⚠️ WAHA Core Limitations

## 🔒 Session Limitation

**WAHA Core (Free Version)** hanya mendukung **1 session** dengan nama **`default`**.

### Keterangan:
- ✅ Satu session dengan nama `default` per instance WAHA
- ❌ Tidak bisa membuat session dengan nama custom
- ❌ Tidak bisa membuat multiple sessions
- 💰 Untuk multiple sessions, perlu **WAHA PLUS** (berbayar)

## 🔑 API Key Authentication

WAHA memerlukan API key untuk semua request. API key di-generate otomatis saat container start.

### Cara Mendapatkan API Key:
```bash
docker logs waha-api | grep "WAHA_API_KEY="
```

### Set di .env:
```env
WAHA_API_KEY=your_api_key_here
```

## 📝 Solusi untuk Multiple Users

Karena WAHA Core hanya support 1 session, ada beberapa opsi:

### Opsi 1: Satu Session untuk Semua User (Shared)
- Semua user share session `default` yang sama
- Tidak ideal untuk production

### Opsi 2: Upgrade ke WAHA PLUS
- Support multiple sessions
- Setiap user bisa punya session sendiri
- Berbayar

### Opsi 3: Multiple WAHA Instances
- Setiap user punya WAHA instance sendiri
- Lebih kompleks untuk manage

## 🚀 Current Implementation

Aplikasi saat ini:
- ✅ Menggunakan session `default` untuk semua user
- ✅ Validasi: hanya 1 session per user di database
- ✅ API key sudah dikonfigurasi

## 📚 References

- [WAHA Documentation](https://waha.devlike.pro/)
- [WAHA PLUS Features](https://waha.devlike.pro/pricing)

---

**Last Updated:** 2025-11-26



