# Google OAuth Setup Guide

## Error: redirect_uri_mismatch

Error ini terjadi ketika redirect URI yang dikonfigurasi di Google Cloud Console tidak sesuai dengan yang digunakan aplikasi.

## Langkah-langkah Perbaikan

### 1. Tentukan Redirect URI yang Benar

Redirect URI aplikasi adalah:
- **Local Development**: `http://localhost:8000/auth/google/callback`
- **Production**: `https://wacloud.id/auth/google/callback`

### 2. Konfigurasi di Google Cloud Console

1. Buka [Google Cloud Console](https://console.cloud.google.com/)
2. Pilih project Anda
3. Pergi ke **APIs & Services** > **Credentials**
4. Klik pada **OAuth 2.0 Client ID** yang Anda gunakan
5. Di bagian **Authorized redirect URIs**, pastikan Anda menambahkan:

   **Untuk Development:**
   ```
   http://localhost:8000/auth/google/callback
   ```

   **Untuk Production:**
   ```
   https://wacloud.id/auth/google/callback
   ```

6. Klik **Save**

### 3. Konfigurasi di File .env

Pastikan file `.env` Anda memiliki konfigurasi berikut:

```env
# Google OAuth Configuration
GOOGLE_CLIENT_ID=your-google-client-id-here
GOOGLE_CLIENT_SECRET=your-google-client-secret-here
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback

# Untuk Production, gunakan:
# GOOGLE_REDIRECT_URI=https://wacloud.id/auth/google/callback

# APP URL (penting untuk redirect URI default)
APP_URL=http://localhost:8000
# Untuk Production:
# APP_URL=https://wacloud.id
```

### 4. Catatan Penting

- **Redirect URI harus EXACT match** - termasuk protocol (http/https), domain, port, dan path
- Jika menggunakan `http://localhost:8000`, pastikan portnya sesuai
- Jika menggunakan custom domain, pastikan menggunakan `https://`
- Jangan menambahkan trailing slash (`/`) di akhir URI
- Redirect URI case-sensitive untuk path

### 5. Format Redirect URI yang Benar

✅ **Benar:**
```
http://localhost:8000/auth/google/callback
https://wacloud.id/auth/google/callback
https://www.wacloud.id/auth/google/callback
```

❌ **Salah:**
```
http://localhost:8000/auth/google/callback/  (ada trailing slash)
http://localhost/auth/google/callback  (port tidak sesuai)
https://wacloud.id/auth/google/callback/  (ada trailing slash)
```

### 6. Verifikasi Konfigurasi

Setelah mengkonfigurasi, clear cache Laravel:

```bash
php artisan config:clear
php artisan cache:clear
```

### 7. Testing

1. Pastikan `.env` sudah dikonfigurasi dengan benar
2. Pastikan redirect URI sudah ditambahkan di Google Cloud Console
3. Tunggu beberapa menit untuk perubahan di Google Cloud Console diterapkan
4. Coba login dengan Google lagi

## Troubleshooting

### Masih Error redirect_uri_mismatch?

1. **Cek APP_URL di .env** - Pastikan sesuai dengan domain yang digunakan
2. **Cek GOOGLE_REDIRECT_URI** - Pastikan sesuai dengan yang didaftarkan di Google Cloud Console
3. **Tunggu beberapa menit** - Perubahan di Google Cloud Console butuh waktu untuk diterapkan
4. **Clear browser cache** - Kadang browser cache redirect URI lama
5. **Cek di Google Cloud Console** - Pastikan redirect URI sudah tersimpan dengan benar

### Debug Redirect URI yang Digunakan

Untuk melihat redirect URI yang sebenarnya digunakan aplikasi, tambahkan di `GoogleAuthController.php`:

```php
public function redirectToGoogle()
{
    $redirectUrl = config('services.google.redirect');
    \Log::info('Google OAuth Redirect URI: ' . $redirectUrl);
    return Socialite::driver('google')->redirect();
}
```

Kemudian cek log untuk melihat URI yang digunakan.

## Multiple Environments

Jika Anda menggunakan multiple environments (development, staging, production), pastikan untuk:

1. Menambahkan semua redirect URI di Google Cloud Console:
   - `http://localhost:8000/auth/google/callback` (development)
   - `https://wacloud.id/auth/google/callback` (production)

2. Menggunakan environment variables yang berbeda untuk setiap environment

## Security Notes

- Jangan commit file `.env` ke repository
- Gunakan environment variables yang berbeda untuk development dan production
- Pastikan OAuth credentials disimpan dengan aman
- Pertimbangkan untuk menggunakan IP whitelist di production

