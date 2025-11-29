# 💾 WAHA Session Persistence Guide

Panduan lengkap untuk memastikan session WAHA tidak hilang saat container restart.

## 🔧 Konfigurasi Saat Ini

### Bind Mount (Recommended)

Saat ini menggunakan **bind mount** ke direktori lokal:
```
./docker-data/waha-sessions → /app/.sessions (di dalam container)
```

**Keuntungan:**
- ✅ Data tersimpan di host, mudah diakses
- ✅ Mudah di-backup dan restore
- ✅ Tidak hilang meskipun volume Docker dihapus
- ✅ Bisa diakses langsung dari host

### Restart Policy

Container dikonfigurasi dengan `restart: unless-stopped`:
- ✅ Auto-restart saat Docker daemon restart
- ✅ Auto-restart saat container crash
- ❌ Tidak restart jika di-stop manual (untuk maintenance)

## 🚀 Cara Kerja

### 1. Session Storage

WAHA menyimpan semua session data di `/app/.sessions` di dalam container. Dengan bind mount:
- Data disimpan di `./docker-data/waha-sessions/` di host
- Data tetap ada meskipun container dihapus
- Data tetap ada meskipun `docker-compose down`

### 2. Restart Container

```bash
# Restart container (session TIDAK hilang)
docker-compose restart waha

# Stop dan start ulang (session TIDAK hilang)
docker-compose stop waha
docker-compose start waha

# Rebuild dan start (session TIDAK hilang)
docker-compose up -d --build waha
```

### 3. Hapus Container (Session Tetap Ada)

```bash
# Hapus container saja (session TIDAK hilang)
docker-compose down

# Hapus container + network (session TIDAK hilang)
docker-compose down --remove-orphans
```

**⚠️ PERINGATAN:** Session akan hilang jika:
- Menghapus direktori `./docker-data/waha-sessions/` secara manual
- Menjalankan `docker-compose down -v` (menghapus volumes)

## 📦 Backup & Restore

### Backup Sessions

```bash
# Backup otomatis dengan script
./waha-backup.sh

# Backup ke direktori custom
./waha-backup.sh /path/to/backup/dir
```

Script akan:
- Membuat backup dengan timestamp
- Menyimpan di `./backups/` (default)
- Menampilkan ukuran backup
- Menampilkan daftar backup terbaru

### Restore Sessions

```bash
# Restore dari backup
./waha-restore.sh ./backups/waha-sessions-20250127_120000.tar.gz

# List available backups
ls -lh ./backups/waha-sessions-*.tar.gz
```

**Catatan:** Script akan otomatis backup session saat ini sebelum restore.

### Manual Backup

```bash
# Backup manual
tar -czf waha-sessions-backup.tar.gz -C docker-data waha-sessions

# Restore manual
tar -xzf waha-sessions-backup.tar.gz -C docker-data
```

## 🔍 Verifikasi Persistence

### Check Session Directory

```bash
# Dari host
ls -la ./docker-data/waha-sessions/

# Dari dalam container
docker exec waha-api ls -la /app/.sessions
```

### Test Restart

```bash
# 1. Check sessions sebelum restart
docker exec waha-api ls -la /app/.sessions

# 2. Restart container
docker-compose restart waha

# 3. Check sessions setelah restart (harus sama)
docker exec waha-api ls -la /app/.sessions
```

## 🛡️ Best Practices

### 1. Regular Backup

```bash
# Tambahkan ke crontab untuk backup otomatis harian
0 2 * * * cd /path/to/project && ./waha-backup.sh
```

### 2. Backup Sebelum Update

```bash
# Selalu backup sebelum update WAHA
./waha-backup.sh
docker-compose pull waha
docker-compose up -d waha
```

### 3. Monitor Disk Space

```bash
# Check ukuran session directory
du -sh ./docker-data/waha-sessions/

# Check ukuran semua backups
du -sh ./backups/
```

### 4. Cleanup Old Backups

```bash
# Hapus backup lebih dari 30 hari
find ./backups/ -name "waha-sessions-*.tar.gz" -mtime +30 -delete
```

## 🔄 Migration dari Named Volume

Jika sebelumnya menggunakan named volume dan ingin migrasi ke bind mount:

```bash
# 1. Backup dari volume
docker run --rm -v wahaapi_waha-sessions:/data -v $(pwd):/backup alpine tar czf /backup/waha-sessions-migration.tar.gz /data

# 2. Extract ke bind mount directory
mkdir -p ./docker-data/waha-sessions
tar -xzf waha-sessions-migration.tar.gz -C ./docker-data/waha-sessions --strip-components=1

# 3. Update docker-compose.yml (sudah dilakukan)
# 4. Restart container
docker-compose up -d waha
```

## ⚠️ Troubleshooting

### Session Hilang Setelah Restart

1. **Check bind mount:**
   ```bash
   docker inspect waha-api | grep -A 10 Mounts
   ```

2. **Check directory permissions:**
   ```bash
   ls -la ./docker-data/waha-sessions/
   chmod -R 755 ./docker-data/waha-sessions/
   ```

3. **Check container logs:**
   ```bash
   docker-compose logs waha | grep -i session
   ```

### Permission Denied

```bash
# Fix permissions
sudo chown -R $USER:$USER ./docker-data/waha-sessions/
chmod -R 755 ./docker-data/waha-sessions/
```

### Container Tidak Bisa Write

```bash
# Check if directory is writable
docker exec waha-api touch /app/.sessions/test.txt
docker exec waha-api rm /app/.sessions/test.txt
```

## 📊 Monitoring

### Check Session Count

```bash
# Count session files
docker exec waha-api find /app/.sessions -type f | wc -l
```

### Check Session Size

```bash
# Total size
docker exec waha-api du -sh /app/.sessions
```

## 🔐 Security

- ✅ Session data disimpan di host (bisa di-encrypt jika perlu)
- ✅ Backup bisa disimpan di lokasi terpisah
- ✅ Git ignore sudah dikonfigurasi (tidak ter-commit)

---

**Last Updated**: 2025-01-27

