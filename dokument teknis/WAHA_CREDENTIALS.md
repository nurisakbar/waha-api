# 🔐 WAHA API Credentials

## 📚 Swagger/API Docs Access

**URL:** http://localhost:3000/api-docs

**Current Credentials:**
- **Username:** `admin`
- **Password:** `68b458d48cc1427b876388281c78ea69` (auto-generated)

⚠️ **Note:** Password ini akan berubah setiap kali container di-restart!

## 🔑 Set Password Tetap

Untuk menggunakan password tetap, edit `docker-compose.yml` dan tambahkan:

```yaml
environment:
  - WHATSAPP_SWAGGER_USERNAME=admin
  - WHATSAPP_SWAGGER_PASSWORD=your_password_here
  - WAHA_DASHBOARD_USERNAME=admin
  - WAHA_DASHBOARD_PASSWORD=your_password_here
```

Kemudian restart:
```bash
docker-compose restart waha
```

## 🔍 Cara Mendapatkan Password Saat Ini

```bash
# Check current password
docker logs waha-api | grep "WHATSAPP_SWAGGER_PASSWORD"

# Atau gunakan script
./waha-logs.sh | grep "SWAGGER"
```

## 📝 Default (Jika Tidak Di-Set)

Jika tidak di-set di docker-compose.yml, WAHA akan generate password random setiap start.

---

**Last Updated:** 2025-11-26
