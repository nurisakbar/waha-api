# Instruksi Migration UUID

## Masalah
Tabel `messages` tidak ditemukan karena migration belum dijalankan.

## Solusi

### 1. Backup Database (PENTING!)
```bash
mysqldump -u root -p wahaapi > backup_$(date +%Y%m%d_%H%M%S).sql
```

### 2. Jalankan Migration Fresh dengan Seeding
```bash
cd app
php artisan migrate:fresh --seed
```

Atau jika menggunakan MAMP:
```bash
cd app
/Applications/MAMP/bin/php/php8.2.0/bin/php artisan migrate:fresh --seed
```

### 3. Atau Jalankan Migration Tanpa Fresh (jika sudah ada data)
```bash
cd app
php artisan migrate
```

### 4. Verifikasi Migration
```bash
php artisan migrate:status
```

## Perubahan yang Sudah Dibuat

### Models
- ✅ `User` - menggunakan UUID
- ✅ `WhatsAppSession` - menggunakan UUID  
- ✅ `Message` - menggunakan UUID

### Migrations
- ✅ Semua migration sudah diupdate untuk menggunakan UUID
- ✅ Foreign keys sudah diupdate
- ✅ Migration UUID sudah dibuat dengan safety checks

### Controllers
- ✅ `HomeController` - sudah diupdate untuk menggunakan string UUID

## Catatan

1. **Fresh Migration akan menghapus semua data!** Pastikan backup terlebih dahulu.
2. Jika ada data existing, gunakan `migrate` (bukan `migrate:fresh`)
3. UUID format: 36 karakter (contoh: `550e8400-e29b-41d4-a716-446655440000`)

## Troubleshooting

### Error: Table already exists
- Hapus tabel yang duplikat atau jalankan `migrate:fresh`

### Error: Foreign key constraint fails
- Pastikan semua migration dijalankan dalam urutan yang benar
- Cek apakah tabel `users` dan `whatsapp_sessions` sudah ada

### Error: Column type mismatch
- Pastikan migration UUID dijalankan setelah semua tabel dibuat
- Atau gunakan `migrate:fresh` untuk clean start

---

**Last Updated:** 2025-11-28



