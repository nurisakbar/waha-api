# UUID Migration - Dokumentasi

## Overview

Semua ID di database telah diubah dari `BIGINT UNSIGNED AUTO_INCREMENT` menjadi `UUID (CHAR(36))`.

## Tabel yang Diubah

### Primary Keys (ID)
1. **users.id** - UUID
2. **whatsapp_sessions.id** - UUID
3. **messages.id** - UUID

### Foreign Keys
Semua foreign keys yang reference ke tabel di atas juga diubah menjadi UUID:
- `whatsapp_sessions.user_id` → UUID
- `messages.user_id` → UUID
- `messages.session_id` → UUID
- `webhooks.user_id` → UUID
- `webhooks.session_id` → UUID (nullable)
- `api_keys.user_id` → UUID
- `subscriptions.user_id` → UUID
- `invoices.user_id` → UUID
- `usage_statistics.user_id` → UUID
- `api_usage_logs.user_id` → UUID

## Migration

File: `database/migrations/2025_11_26_100000_change_ids_to_uuid.php`

Migration ini akan:
1. Drop semua foreign keys
2. Ubah primary keys menjadi UUID
3. Ubah foreign keys menjadi UUID
4. Recreate foreign keys

**PENTING:** Migration ini akan menghapus semua data yang ada! Pastikan backup database sebelum menjalankan migration.

## Models yang Diupdate

### User Model
```php
public $incrementing = false;
protected $keyType = 'string';
```

### WhatsAppSession Model
```php
public $incrementing = false;
protected $keyType = 'string';
```

### Message Model
```php
public $incrementing = false;
protected $keyType = 'string';
```

## Seeders

### 1. UserSeeder
File: `database/seeders/UserSeeder.php`

Membuat 5 sample users:
- Admin User (admin@example.com)
- John Doe (john@example.com)
- Jane Smith (jane@example.com)
- Bob Wilson (bob@example.com)
- Alice Brown (alice@example.com)

Password default: `password`

### 2. WhatsAppSessionSeeder
File: `database/seeders/WhatsAppSessionSeeder.php`

Membuat 1-3 sessions per user dengan status random:
- pairing
- connected
- disconnected
- failed

### 3. MessageSeeder
File: `database/seeders/MessageSeeder.php`

Membuat 10-50 messages per connected session dengan:
- Message types: text, image, video, audio, document
- Directions: incoming, outgoing
- Status: sent, delivered, read, pending
- Random timestamps (last 30 days)

## Cara Menjalankan

### 1. Backup Database (PENTING!)
```bash
mysqldump -u root -p wahaapi > backup.sql
```

### 2. Fresh Migration dengan Seeding
```bash
cd app
php artisan migrate:fresh --seed
```

### 3. Atau Run Seeders Terpisah
```bash
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=WhatsAppSessionSeeder
php artisan db:seed --class=MessageSeeder
```

### 4. Atau Run Semua Seeders
```bash
php artisan db:seed
```

## Catatan Penting

1. **UUID Format**: Menggunakan `Str::uuid()->toString()` yang menghasilkan UUID v4 (36 karakter)
2. **Performance**: UUID sedikit lebih lambat daripada auto-increment integer, tapi lebih aman dan scalable
3. **Foreign Keys**: Semua foreign keys sudah diupdate untuk menggunakan UUID
4. **Factories**: UserFactory sudah diupdate untuk generate UUID

## Testing

Setelah migration, pastikan:
1. ✅ Users bisa dibuat dengan UUID
2. ✅ Sessions bisa dibuat dengan UUID
3. ✅ Messages bisa dibuat dengan UUID
4. ✅ Foreign keys berfungsi dengan benar
5. ✅ Seeder berjalan tanpa error

## Rollback

Jika perlu rollback:
```bash
php artisan migrate:rollback --step=1
```

**PENTING:** Rollback akan mengubah kembali ke BIGINT, tapi data UUID akan hilang!

---

**Last Updated:** 2025-11-26

