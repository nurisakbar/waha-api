# 🐳 WAHA Docker Setup Guide

Panduan lengkap untuk menjalankan WAHA (WhatsApp HTTP API) menggunakan Docker.

## 📋 Prerequisites

- Docker Desktop terinstall dan running
- Docker Compose (biasanya sudah included dengan Docker Desktop)

## 🚀 Quick Start

### 1. Start WAHA
```bash
./waha-start.sh
```

### 2. Check Status
```bash
./waha-status.sh
```

### 3. View Logs
```bash
./waha-logs.sh          # View last 100 lines
./waha-logs.sh --follow # Follow logs in real-time
```

### 4. Stop WAHA
```bash
./waha-stop.sh
```

### 5. Restart WAHA
```bash
./waha-restart.sh
```

## 📝 Manual Commands

Jika tidak ingin menggunakan script, bisa menggunakan Docker Compose langsung:

```bash
# Start
docker-compose up -d waha

# Stop
docker-compose stop waha

# Restart
docker-compose restart waha

# View logs
docker logs -f waha-api

# Remove container
docker-compose down waha
```

## 🔧 Configuration

### Docker Compose Configuration

File `docker-compose.yml` sudah dikonfigurasi dengan:
- **Port**: 3000 (http://localhost:3000)
- **Image**: devlikeapro/waha-plus:latest
- **Volume**: Persistent storage untuk sessions di `waha-sessions` volume
- **Health Check**: Otomatis check kesehatan container
- **Restart Policy**: Auto-restart kecuali dihentikan manual
- **Default Engine**: GOWS (mendukung lebih banyak fitur seperti poll)

### Environment Variables

Untuk mengubah konfigurasi, edit `docker-compose.yml`:

```yaml
environment:
  - WAHA_LOG_LEVEL=info        # debug, info, warn, error
  - WAHA_SWAGGER_ENABLED=true   # Enable/disable Swagger UI
  - WHATSAPP_DEFAULT_ENGINE=GOWS  # Engine: GOWS, WEBJS, NOWEB, BAILEYS
```

### Engine Configuration

WAHA mendukung beberapa engine:
- **GOWS** (default) - Engine generasi baru, lebih efisien, mendukung poll dan fitur advanced
- **WEBJS** - Engine berbasis browser, beberapa fitur tidak didukung (seperti poll)
- **NOWEB** - Engine tanpa browser
- **BAILEYS** - Engine alternatif

**Catatan:** Setelah mengubah engine, restart container WAHA:
```bash
docker-compose restart waha
# atau
./waha-restart.sh
```

### Port Configuration

Jika port 3000 sudah digunakan, ubah di `docker-compose.yml`:

```yaml
ports:
  - "3001:3000"  # Host:Container
```

Dan update `.env` di folder `app/`:
```env
WAHA_API_URL=http://localhost:3001
```

## 🌐 Access Points

Setelah WAHA running, akses:

- **API Base URL**: http://localhost:3000
- **Swagger UI**: http://localhost:3000/api-docs
- **Health Check**: http://localhost:3000/api/health

## 🔍 Troubleshooting

### Container tidak start
```bash
# Check logs
docker logs waha-api

# Check container status
docker ps -a | grep waha-api
```

### Port sudah digunakan
```bash
# Check what's using port 3000
lsof -i :3000

# Atau ubah port di docker-compose.yml
```

### Container crash loop
```bash
# Check detailed logs
docker logs --tail 100 waha-api

# Remove and recreate
docker-compose down
docker-compose up -d waha
```

### Volume issues
```bash
# Remove volume dan recreate
docker-compose down -v
docker-compose up -d waha
```

## 📊 Monitoring

### Check Container Stats
```bash
docker stats waha-api
```

### Check Container Health
```bash
docker inspect waha-api | grep -A 10 Health
```

### Check Network
```bash
docker network ls
docker network inspect waha-network
```

## 🔄 Update WAHA

Untuk update ke versi terbaru:

```bash
# Pull latest image
docker-compose pull waha

# Restart dengan image baru
docker-compose up -d waha
```

## 💾 Data Persistence

Sessions disimpan di Docker volume `waha-sessions`. Data akan tetap ada meskipun container dihapus, kecuali volume juga dihapus.

### Backup Volume
```bash
docker run --rm -v wahaapi_waha-sessions:/data -v $(pwd):/backup alpine tar czf /backup/waha-sessions-backup.tar.gz /data
```

### Restore Volume
```bash
docker run --rm -v wahaapi_waha-sessions:/data -v $(pwd):/backup alpine tar xzf /backup/waha-sessions-backup.tar.gz -C /data
```

## 🛑 Cleanup

Untuk menghapus semua (container + volume):

```bash
docker-compose down -v
```

**Warning**: Ini akan menghapus semua session data!

## 📚 Additional Resources

- [WAHA Documentation](https://waha.devlike.pro/)
- [Docker Compose Documentation](https://docs.docker.com/compose/)
- [WAHA GitHub](https://github.com/devlikeapro/waha)

---

**Last Updated:** 2025-11-26

