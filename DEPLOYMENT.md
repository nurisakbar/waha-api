# 🚀 Deployment Guide - Ubuntu Server

Panduan deployment WAHA API Platform di Ubuntu Server.

## 📋 Prerequisites

- Ubuntu Server (20.04 atau lebih baru)
- Docker & Docker Compose terinstall
- User dengan sudo access
- Akses ke WAHA PLUS (paid version)

## 🔧 Setup Awal

### 1. Install Docker & Docker Compose

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh

# Add user to docker group (optional, untuk tidak perlu sudo)
sudo usermod -aG docker $USER
newgrp docker

# Install Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose

# Verify installation
docker --version
docker-compose --version
```

### 2. Clone Project

```bash
# Clone repository
cd /var/www
git clone <your-repo-url> waha-api
cd waha-api
```

### 3. Login ke Docker Hub (WAJIB untuk WAHA PLUS)

```bash
# Login dengan akun yang memiliki akses WAHA PLUS
docker login

# Masukkan username dan password Docker Hub
# Username: <your-dockerhub-username>
# Password: <your-dockerhub-password>
```

**⚠️ IMPORTANT:** WAHA PLUS memerlukan Docker login. Pastikan akun Anda memiliki akses.

### 4. Setup Environment

```bash
# Setup environment otomatis
./waha.sh setup

# Atau manual
cp docker.env.example .env
nano .env  # Edit sesuai kebutuhan
```

### 5. Start WAHA

```bash
# Start WAHA (akan otomatis check docker login)
./waha.sh start

# Check status
./waha.sh status
```

## 🔐 Production Configuration

### 1. Update .env untuk Production

```bash
nano .env
```

Pastikan:
- `WAHA_SWAGGER_PASSWORD` - Password yang kuat
- `WAHA_DASHBOARD_PASSWORD` - Password yang kuat
- `WAHA_API_KEY` - API key yang aman
- `WAHA_IMAGE=devlikeapro/waha-plus:latest` - Pastikan menggunakan PLUS

### 2. Setup Firewall

```bash
# Allow port 3000 (WAHA API)
sudo ufw allow 3000/tcp

# Allow port 80/443 jika menggunakan reverse proxy
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp

# Enable firewall
sudo ufw enable
```

### 3. Setup Reverse Proxy (Nginx) - Optional

```nginx
# /etc/nginx/sites-available/waha-api
server {
    listen 80;
    server_name your-domain.com;

    location / {
        proxy_pass http://localhost:3000;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host $host;
        proxy_cache_bypass $http_upgrade;
    }
}
```

```bash
# Enable site
sudo ln -s /etc/nginx/sites-available/waha-api /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

## 🔄 Auto-Start on Boot

### 1. Setup Systemd Service

```bash
# Create service file
sudo nano /etc/systemd/system/waha.service
```

```ini
[Unit]
Description=WAHA API Service
Requires=docker.service
After=docker.service

[Service]
Type=oneshot
RemainAfterExit=yes
WorkingDirectory=/var/www/waha-api
ExecStart=/usr/local/bin/docker-compose -f /var/www/waha-api/docker-compose.yml --env-file /var/www/waha-api/.env up -d waha
ExecStop=/usr/local/bin/docker-compose -f /var/www/waha-api/docker-compose.yml --env-file /var/www/waha-api/.env stop waha
User=root

[Install]
WantedBy=multi-user.target
```

```bash
# Enable service
sudo systemctl daemon-reload
sudo systemctl enable waha.service
sudo systemctl start waha.service
```

## 📊 Monitoring

### 1. Check Logs

```bash
# View logs
./waha.sh logs

# Follow logs
./waha.sh logs -f
```

### 2. Check Status

```bash
# Status
./waha.sh status

# Container stats
docker stats waha-api
```

### 3. Health Check

```bash
# API health
curl http://localhost:3000/api/health

# Version
curl http://localhost:3000/api/version
```

## 🔄 Update & Maintenance

### 1. Update WAHA

```bash
# Pull latest image
docker pull devlikeapro/waha-plus:latest

# Restart
./waha.sh restart
```

### 2. Backup Sessions

```bash
# Backup sessions
./waha.sh backup

# Backup akan tersimpan di ./backups/
```

### 3. Restore Sessions

```bash
# Restore dari backup
./waha.sh restore ./backups/waha-sessions-YYYYMMDD_HHMMSS.tar.gz
```

## ⚠️ Troubleshooting

### Docker Login Expired

```bash
# Re-login
docker login

# Verify
docker info | grep Username
```

### Container Tidak Start

```bash
# Check logs
./waha.sh logs

# Check docker status
docker ps -a | grep waha-api

# Restart
./waha.sh restart
```

### Permission Issues

```bash
# Fix permissions
sudo chown -R $USER:$USER /var/www/waha-api
chmod +x /var/www/waha-api/waha.sh
```

### Port Already in Use

```bash
# Check what's using port 3000
sudo lsof -i :3000

# Kill process atau ubah port di .env
```

## 🔒 Security Best Practices

1. **Firewall**: Aktifkan UFW dan hanya buka port yang diperlukan
2. **Passwords**: Gunakan password yang kuat di `.env`
3. **SSL/TLS**: Gunakan HTTPS dengan reverse proxy (Let's Encrypt)
4. **Updates**: Update sistem dan Docker secara berkala
5. **Backups**: Backup sessions secara berkala
6. **Monitoring**: Setup monitoring untuk container health

## 📝 Useful Commands

```bash
# Start
./waha.sh start

# Stop
./waha.sh stop

# Restart
./waha.sh restart

# Status
./waha.sh status

# Logs
./waha.sh logs -f

# Backup
./waha.sh backup

# Update
./waha.sh update

# Shell access
./waha.sh shell
```

---

**Last Updated**: 2025-01-27

