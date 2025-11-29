# 🚀 SaaS WhatsApp API Platform

Platform SaaS berbasis WAHA (WhatsApp HTTP API) yang memungkinkan pengguna untuk mendaftar, mengelola sesi WhatsApp, dan menggunakan berbagai fitur WhatsApp melalui REST API.

## 📋 Dokumentasi Proyek

### 1. [ANALISIS_SISTEM.md](./ANALISIS_SISTEM.md)
Dokumentasi lengkap analisis sistem, fitur-fitur, arsitektur, dan teknologi yang digunakan.

### 2. [CHECKLIST_MODUL.md](./CHECKLIST_MODUL.md)
Checklist modul pengembangan untuk tracking progress. **Gunakan file ini untuk tracking development!**

### 3. [DATABASE_SCHEMA.sql](./DATABASE_SCHEMA.sql)
Database schema lengkap untuk MySQL dengan semua tabel, relasi, dan index yang diperlukan.

## 📁 Struktur Repo

```
wahaapi/
├── ANALISIS_SISTEM.md       → Dokumen analisis & fitur
├── CHECKLIST_MODUL.md       → Progress checklist
├── DATABASE_SCHEMA.sql      → Schema referensi
├── README.md                → File ini
├── docker-compose.yml        → Docker Compose untuk WAHA
├── waha-start.sh            → Script start WAHA
├── waha-stop.sh             → Script stop WAHA
├── waha-restart.sh          → Script restart WAHA
├── waha-status.sh           → Script cek status WAHA
├── waha-logs.sh             → Script lihat logs WAHA
└── app/                     → Source code Laravel 11
    ├── app/                 → Controllers, Models, dll.
    ├── resources/           → Blade, JS, SCSS
    ├── routes/              → web/api routes
    └── ...
```

## 🛠️ Tech Stack

- **Framework**: Laravel 11 (Full-Stack) — berada di folder `app/`
- **Database**: MySQL (MAMP default, port 8889)
- **Authentication**: Laravel session-based (Sanctum planned for API access)
- **Frontend**: Blade Templates + vanilla JS
- **UI Framework**: Bootstrap 5 (via `laravel/ui`)
- **Build Tools**: Vite + Sass
- **WhatsApp API**: WAHA (WhatsApp HTTP API) via Docker

## 📦 Struktur Modul

Proyek ini dibagi menjadi 12 modul utama:

1. **Setup Project & Infrastructure** - Setup dasar project
2. **Authentication & User Management** - Login, register, profile
3. **WhatsApp Session Management** - Create, pair, manage sessions
4. **Messaging** - Send/receive messages
5. **Webhook** - Webhook configuration & receiver
6. **Contacts & Groups** - Manage contacts and groups
7. **Dashboard & UI** - User interface
8. **API Management** - API keys & authentication
9. **Billing** - Subscription & plans
10. **Analytics** - Usage statistics
11. **Error Handling** - Error handling & validation
12. **Testing & Deployment** - Testing & deployment prep

## 🎯 Cara Menggunakan Checklist

1. Buka file `CHECKLIST_MODUL.md`
2. Update checklist dengan menandai task yang sudah selesai dengan `[x]`
3. Update progress percentage di bagian bawah
4. Catat notes/issues jika ada

### Contoh Update Checklist:
```markdown
- [x] Create registration form (frontend)  ✅ Selesai
- [x] Create registration API endpoint     ✅ Selesai
- [ ] Email validation                    🔄 In Progress
```

## 🚀 Quick Start (Setelah Setup)

### 1. Install Dependencies
```bash
cd app
/Applications/MAMP/bin/php/php8.3.14/bin/php /Applications/MAMP/bin/php/composer install
npm install
```

### 2. Setup Environment
```bash
cp .env.example .env
/Applications/MAMP/bin/php/php8.3.14/bin/php artisan key:generate

# Pastikan konfigurasi DB (sesuaikan dengan lokal)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=8889
DB_DATABASE=wahaapi
DB_USERNAME=root
DB_PASSWORD=root
```

### 3. Setup WAHA via Docker
```bash
# Start WAHA API
./waha-start.sh

# Atau manual dengan docker-compose
docker-compose up -d waha

# Check status
./waha-status.sh

# View logs
./waha-logs.sh

# Stop WAHA
./waha-stop.sh
```

### 4. Setup Database
```bash
# Opsional: import schema lengkap
/Applications/MAMP/Library/bin/mysql80/bin/mysql -u root -proot wahaapi < ../DATABASE_SCHEMA.sql

# Default: jalankan migration Laravel
/Applications/MAMP/bin/php/php8.3.14/bin/php artisan migrate

# Seed default plans
/Applications/MAMP/bin/php/php8.3.14/bin/php artisan db:seed --class=PlanSeeder
```

### 5. Run Development Server
```bash
/Applications/MAMP/bin/php/php8.3.14/bin/php artisan serve
npm run dev   # jalankan di terminal terpisah
```

## 📊 Progress Tracking

Lihat file `CHECKLIST_MODUL.md` untuk melihat progress detail setiap modul.

**Current Status:** 🟢 MVP Ready - Core Features Complete (~84%)

## 📝 Development Phases

### Phase 1 - Core (Weeks 1-2)
- Setup Project
- Authentication
- Basic Dashboard

### Phase 2 - Essential (Weeks 3-4)
- Session Management
- Messaging
- Webhook

### Phase 3 - Important (Weeks 5-6)
- Contacts & Groups
- API Management
- Error Handling

### Phase 4 - Nice to Have (Weeks 7-8)
- Billing
- Analytics
- Testing & Deployment

## 🐳 WAHA Docker Setup

Lihat [WAHA_DOCKER.md](./WAHA_DOCKER.md) untuk panduan lengkap setup WAHA via Docker.

**Quick Start:**
```bash
./waha-start.sh    # Start WAHA
./waha-status.sh    # Check status
./waha-logs.sh      # View logs
./waha-stop.sh      # Stop WAHA
```

## 🔗 Resources

- [WAHA Documentation](https://waha.devlike.pro/)
- [Laravel Documentation](https://laravel.com/docs)
- [Bootstrap 5 Documentation](https://getbootstrap.com/docs/5.0/)

## 📧 Support

Untuk pertanyaan atau issues, silakan buat issue di repository ini.

---

**Last Updated:** [Update saat development dimulai]
**Version:** 0.1.0 (MVP)

