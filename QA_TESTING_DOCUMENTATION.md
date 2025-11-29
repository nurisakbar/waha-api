# 📋 Dokumentasi Testing QA - WAHA Gateway SaaS

**Versi:** 1.0.0  
**Tanggal:** 2025-11-29  
**Target:** Quality Assurance Testing Guide

---

## 📖 Daftar Isi

1. [Autentikasi & Authorization](#1-autentikasi--authorization)
2. [Dashboard](#2-dashboard)
3. [Manajemen Device (Sessions)](#3-manajemen-device-sessions)
4. [Manajemen Pesan](#4-manajemen-pesan)
5. [Template Pesan](#5-template-pesan)
6. [Webhooks](#6-webhooks)
7. [API Keys](#7-api-keys)
8. [Kontak & Grup](#8-kontak--grup)
9. [Manajemen Quota](#9-manajemen-quota)
10. [Sistem Referral](#10-sistem-referral)
11. [Profil Pengguna](#11-profil-pengguna)
12. [Admin Dashboard](#12-admin-dashboard)
13. [Admin Pricing Settings](#13-admin-pricing-settings)
14. [Admin Payment Reports](#14-admin-payment-reports)
15. [Admin Referral Settings](#15-admin-referral-settings)
16. [API Endpoints](#16-api-endpoints)
17. [OTP Management](#17-otp-management)
18. [Webhook External](#18-webhook-external)

---

## 1. Autentikasi & Authorization

### 1.1 Registrasi Pengguna

**URL:** `/register`  
**Method:** GET, POST

#### Test Cases:

| ID | Test Case | Steps | Expected Result |
|---|---|---|---|
| TC-AUTH-001 | Registrasi berhasil | 1. Buka `/register`<br>2. Isi form (name, email, password, password confirmation)<br>3. Submit | User berhasil registrasi, redirect ke login, email terverifikasi |
| TC-AUTH-002 | Registrasi dengan referral code | 1. Buka `/register`<br>2. Isi form lengkap<br>3. Masukkan referral code yang valid<br>4. Submit | User berhasil registrasi, referrer mendapat bonus quota |
| TC-AUTH-003 | Registrasi dengan email duplikat | 1. Buka `/register`<br>2. Isi email yang sudah terdaftar<br>3. Submit | Error: "Email sudah terdaftar" |
| TC-AUTH-004 | Registrasi dengan password tidak match | 1. Buka `/register`<br>2. Isi password dan confirmation berbeda<br>3. Submit | Error: "Password confirmation tidak cocok" |
| TC-AUTH-005 | Registrasi dengan referral code invalid | 1. Buka `/register`<br>2. Masukkan referral code yang tidak ada<br>3. Submit | Error: "Referral code tidak valid" atau tetap bisa registrasi tanpa bonus |

### 1.2 Login

**URL:** `/login`  
**Method:** GET, POST

#### Test Cases:

| ID | Test Case | Steps | Expected Result |
|---|---|---|---|
| TC-AUTH-006 | Login berhasil (Client) | 1. Buka `/login`<br>2. Masukkan email & password valid<br>3. Submit | Redirect ke `/home` (dashboard client) |
| TC-AUTH-007 | Login berhasil (Admin) | 1. Buka `/login`<br>2. Masukkan email & password admin<br>3. Submit | Redirect ke `/admin/dashboard` |
| TC-AUTH-008 | Login dengan kredensial salah | 1. Buka `/login`<br>2. Masukkan email/password salah<br>3. Submit | Error: "Kredensial tidak valid" |
| TC-AUTH-009 | Login dengan email belum terdaftar | 1. Buka `/login`<br>2. Masukkan email yang tidak ada<br>3. Submit | Error: "Kredensial tidak valid" |
| TC-AUTH-010 | Remember me functionality | 1. Buka `/login`<br>2. Centang "Remember me"<br>3. Login<br>4. Tutup browser, buka lagi | User tetap login (session persisten) |

### 1.3 Logout

**URL:** `/logout`  
**Method:** POST

#### Test Cases:

| ID | Test Case | Steps | Expected Result |
|---|---|---|---|
| TC-AUTH-011 | Logout berhasil | 1. Login sebagai user<br>2. Klik tombol logout | Session dihapus, redirect ke `/login` |
| TC-AUTH-012 | Akses halaman setelah logout | 1. Logout<br>2. Coba akses `/home` | Redirect ke `/login` |

---

## 2. Dashboard

### 2.1 Dashboard Client

**URL:** `/home`  
**Method:** GET  
**Access:** Authenticated Client

#### Test Cases:

| ID | Test Case | Steps | Expected Result |
|---|---|---|---|
| TC-DASH-001 | Tampilkan dashboard client | 1. Login sebagai client<br>2. Akses `/home` | Menampilkan:<br>- Statistik quota (Balance, Text Quota, Multimedia Quota, Free Text Quota)<br>- Grafik penggunaan quota harian<br>- Total Devices<br>- Active Devices<br>- Total Messages<br>- Link ke Devices |
| TC-DASH-002 | Grafik quota usage | 1. Login sebagai client<br>2. Akses `/home`<br>3. Periksa grafik | Grafik line chart menampilkan penggunaan Text Quota (Premium) dan Multimedia Quota per hari |
| TC-DASH-003 | Redirect admin ke admin dashboard | 1. Login sebagai admin<br>2. Akses `/home` | Otomatis redirect ke `/admin/dashboard` |

### 2.2 Dashboard Admin

**URL:** `/admin/dashboard`  
**Method:** GET  
**Access:** Admin/Super Admin

#### Test Cases:

| ID | Test Case | Steps | Expected Result |
|---|---|---|---|
| TC-DASH-004 | Tampilkan dashboard admin | 1. Login sebagai admin<br>2. Akses `/admin/dashboard` | Menampilkan:<br>- Total Users<br>- New Users This Month<br>- Active Devices<br>- Total Devices<br>- Total Revenue<br>- Revenue This Month<br>- Revenue Growth<br>- Pending Purchases<br>- Completed Purchases |
| TC-DASH-005 | Grafik pertumbuhan user | 1. Login sebagai admin<br>2. Akses `/admin/dashboard`<br>3. Periksa grafik | Grafik line chart menampilkan pertumbuhan user per bulan |
| TC-DASH-006 | Tabel recent purchases | 1. Login sebagai admin<br>2. Akses `/admin/dashboard`<br>3. Scroll ke bawah | Tabel menampilkan 10 pembelian quota terbaru |

---

## 3. Manajemen Device (Sessions)

### 3.1 List Devices

**URL:** `/sessions`  
**Method:** GET  
**Access:** Authenticated

#### Test Cases:

| ID | Test Case | Steps | Expected Result |
|---|---|---|---|
| TC-DEV-001 | Tampilkan daftar devices | 1. Login<br>2. Akses `/sessions` | Menampilkan tabel devices dengan kolom:<br>- Device Name<br>- Phone Number<br>- Status<br>- Created At<br>- Actions |
| TC-DEV-002 | Filter devices by status | 1. Login<br>2. Akses `/sessions`<br>3. Filter by status (connected/disconnected) | Tabel hanya menampilkan devices dengan status yang dipilih |
| TC-DEV-003 | Search devices | 1. Login<br>2. Akses `/sessions`<br>3. Ketik nama device di search box | Tabel menampilkan devices yang namanya mengandung keyword |

### 3.2 Create Device

**URL:** `/sessions/create`  
**Method:** GET, POST

#### Test Cases:

| ID | Test Case | Steps | Expected Result |
|---|---|---|---|
| TC-DEV-004 | Tampilkan form create device | 1. Login<br>2. Akses `/sessions/create` | Form dengan field:<br>- Device Name (required)<br>- Phone Number (required, format +62) |
| TC-DEV-005 | Create device berhasil | 1. Login<br>2. Akses `/sessions/create`<br>3. Isi form lengkap<br>4. Submit | Device dibuat, redirect ke halaman pair dengan QR code |
| TC-DEV-006 | Create device dengan nama duplikat | 1. Login<br>2. Buat device dengan nama yang sudah ada<br>3. Submit | Error: "Device name sudah digunakan" |
| TC-DEV-007 | Create device dengan nomor telepon invalid | 1. Login<br>2. Masukkan nomor telepon tidak valid<br>3. Submit | Error: "Format nomor telepon tidak valid" |
| TC-DEV-008 | Rate limiting create device | 1. Login<br>2. Buat 5 devices dalam 1 menit<br>3. Coba buat device ke-6 | Error: "Terlalu banyak request. Coba lagi nanti." (429) |

### 3.3 Pair Device (QR Code)

**URL:** `/sessions/{session}/pair`  
**Method:** GET

#### Test Cases:

| ID | Test Case | Steps | Expected Result |
|---|---|---|---|
| TC-DEV-009 | Tampilkan QR code | 1. Login<br>2. Buat device baru<br>3. Akses halaman pair | QR code ditampilkan, instruksi scan QR code |
| TC-DEV-010 | QR code auto refresh | 1. Login<br>2. Akses halaman pair<br>3. Tunggu beberapa detik | QR code otomatis refresh jika belum terhubung |
| TC-DEV-011 | Refresh QR code manual | 1. Login<br>2. Akses halaman pair<br>3. Klik tombol "Refresh QR Code" | QR code baru ditampilkan |
| TC-DEV-012 | Status check otomatis | 1. Login<br>2. Akses halaman pair<br>3. Scan QR code dengan WhatsApp | Status berubah menjadi "connected" otomatis |
| TC-DEV-013 | Rate limiting refresh QR | 1. Login<br>2. Klik refresh QR 10 kali dalam 1 menit<br>3. Klik refresh ke-11 | Error: "Terlalu banyak request" (429) |

### 3.4 Device Details

**URL:** `/sessions/{session}`  
**Method:** GET

#### Test Cases:

| ID | Test Case | Steps | Expected Result |
|---|---|---|---|
| TC-DEV-014 | Tampilkan detail device | 1. Login<br>2. Klik device dari list<br>3. Akses detail page | Menampilkan:<br>- Device Name<br>- Phone Number<br>- Status<br>- Created At<br>- Last Activity<br>- Actions (Stop, Delete) |
| TC-DEV-015 | Update device name | 1. Login<br>2. Akses detail device<br>3. Klik edit nama<br>4. Ubah nama<br>5. Submit | Nama device berhasil diupdate, pesan sukses |

### 3.5 Stop Device

**URL:** `/sessions/{session}/stop`  
**Method:** POST

#### Test Cases:

| ID | Test Case | Steps | Expected Result |
|---|---|---|---|
| TC-DEV-016 | Stop device berhasil | 1. Login<br>2. Akses device yang connected<br>3. Klik "Stop Device"<br>4. Konfirmasi | Device dihentikan, status berubah menjadi "disconnected" |
| TC-DEV-017 | Stop device yang sudah disconnected | 1. Login<br>2. Akses device yang disconnected<br>3. Klik "Stop Device" | Error atau tombol disabled |

### 3.6 Delete Device

**URL:** `/sessions/{session}`  
**Method:** DELETE

#### Test Cases:

| ID | Test Case | Steps | Expected Result |
|---|---|---|---|
| TC-DEV-018 | Delete device berhasil | 1. Login<br>2. Akses detail device<br>3. Klik "Delete"<br>4. Konfirmasi | Device dihapus, redirect ke list devices |
| TC-DEV-019 | Delete device dengan pesan | 1. Login<br>2. Hapus device yang punya pesan<br>3. Konfirmasi | Device dihapus, pesan tetap ada (soft delete) atau ikut terhapus |

---

## 4. Manajemen Pesan

### 4.1 List Messages

**URL:** `/messages`  
**Method:** GET  
**Access:** Authenticated

#### Test Cases:

| ID | Test Case | Steps | Expected Result |
|---|---|---|---|
| TC-MSG-001 | Tampilkan daftar pesan | 1. Login<br>2. Akses `/messages` | Tabel menampilkan pesan dengan kolom:<br>- To/From<br>- Content Preview<br>- Type<br>- Status<br>- Device<br>- Created At |
| TC-MSG-002 | Filter messages by device | 1. Login<br>2. Akses `/messages`<br>3. Filter by device | Tabel hanya menampilkan pesan dari device yang dipilih |
| TC-MSG-003 | Filter messages by status | 1. Login<br>2. Akses `/messages`<br>3. Filter by status | Tabel hanya menampilkan pesan dengan status yang dipilih |
| TC-MSG-004 | Search messages | 1. Login<br>2. Akses `/messages`<br>3. Ketik keyword di search | Tabel menampilkan pesan yang mengandung keyword |

### 4.2 Create Single Message

**URL:** `/messages/create`  
**Method:** GET, POST

#### Test Cases:

| ID | Test Case | Steps | Expected Result |
|---|---|---|---|
| TC-MSG-005 | Tampilkan form create message | 1. Login<br>2. Akses `/messages/create` | Form dengan tab:<br>- Single Message<br>- Bulk Message (Excel) |
| TC-MSG-006 | Send text message | 1. Login<br>2. Akses `/messages/create`<br>3. Pilih device<br>4. Masukkan nomor tujuan (format +62)<br>5. Ketik pesan<br>6. Submit | Pesan dikirim, redirect ke list dengan pesan sukses |
| TC-MSG-007 | Send text message dengan nomor invalid | 1. Login<br>2. Masukkan nomor tidak valid<br>3. Submit | Error: "Format nomor telepon tidak valid" |
| TC-MSG-008 | Send image message | 1. Login<br>2. Pilih type "Image"<br>3. Masukkan URL gambar<br>4. (Optional) Masukkan caption<br>5. Submit | Pesan gambar dikirim |
| TC-MSG-009 | Send document message | 1. Login<br>2. Pilih type "Document"<br>3. Masukkan URL dokumen<br>4. (Optional) Masukkan filename & caption<br>5. Submit | Pesan dokumen dikirim |
| TC-MSG-010 | Send video message | 1. Login<br>2. Pilih type "Video"<br>3. Masukkan URL video<br>4. (Optional) Masukkan caption<br>5. Submit | Pesan video dikirim |
| TC-MSG-011 | Send message tanpa device | 1. Login<br>2. Tidak pilih device<br>3. Submit | Error: "Device harus dipilih" |
| TC-MSG-012 | Send message dengan quota habis | 1. Login dengan quota habis<br>2. Coba kirim pesan | Error: "Quota tidak mencukupi" |

### 4.3 Create Bulk Message (Excel)

**URL:** `/messages/create` (Tab Bulk Message)  
**Method:** POST

#### Test Cases:

| ID | Test Case | Steps | Expected Result |
|---|---|---|---|
| TC-MSG-013 | Download sample Excel | 1. Login<br>2. Akses `/messages/create`<br>3. Klik tab "Bulk Message"<br>4. Klik link download sample | File Excel sample terdownload |
| TC-MSG-014 | Upload Excel valid | 1. Login<br>2. Pilih file Excel dengan format benar<br>3. Pilih device<br>4. (Optional) Set delay<br>5. Submit | Pesan dikirim untuk setiap baris, tampilkan statistik sukses/gagal |
| TC-MSG-015 | Upload Excel dengan format salah | 1. Login<br>2. Upload file Excel dengan format salah<br>3. Submit | Error: "Format file tidak valid" |
| TC-MSG-016 | Upload file bukan Excel | 1. Login<br>2. Upload file PDF/Word<br>3. Submit | Error: "File harus berformat Excel (.xlsx, .xls)" |
| TC-MSG-017 | Bulk message dengan delay | 1. Login<br>2. Upload Excel<br>3. Set delay 5 detik<br>4. Submit | Pesan dikirim dengan delay 5 detik antar pesan |

### 4.4 Message Details

**URL:** `/messages/{message}`  
**Method:** GET

#### Test Cases:

| ID | Test Case | Steps | Expected Result |
|---|---|---|---|
| TC-MSG-018 | Tampilkan detail pesan | 1. Login<br>2. Klik pesan dari list<br>3. Akses detail | Menampilkan:<br>- Full content<br>- Type<br>- Status<br>- Device<br>- To/From<br>- Timestamps<br>- Error details (jika ada) |

---

## 5. Template Pesan

### 5.1 List Templates

**URL:** `/templates`  
**Method:** GET

#### Test Cases:

| ID | Test Case | Steps | Expected Result |
|---|---|---|---|
| TC-TPL-001 | Tampilkan daftar template | 1. Login<br>2. Akses `/templates` | Tabel menampilkan template dengan kolom:<br>- Name<br>- Content Preview<br>- Variables<br>- Status<br>- Created<br>- Actions |
| TC-TPL-002 | Empty state | 1. Login (user baru tanpa template)<br>2. Akses `/templates` | Menampilkan pesan "Tidak ada template ditemukan" dan tombol "Create Template" |

### 5.2 Create Template

**URL:** `/templates/create`  
**Method:** GET, POST

#### Test Cases:

| ID | Test Case | Steps | Expected Result |
|---|---|---|---|
| TC-TPL-003 | Tampilkan form create template | 1. Login<br>2. Akses `/templates/create` | Form dengan field:<br>- Template Name (required)<br>- Template Content (required)<br>- Variables (optional, comma-separated)<br>- Active checkbox |
| TC-TPL-004 | Create template berhasil | 1. Login<br>2. Isi form lengkap<br>3. Submit | Template dibuat, redirect ke list dengan pesan sukses |
| TC-TPL-005 | Create template dengan variables | 1. Login<br>2. Buat template dengan content: "Halo @{{name}}, pesanan @{{order_id}} telah dikonfirmasi!"<br>3. Masukkan variables: "name, order_id"<br>4. Submit | Template dibuat dengan variables tersimpan |
| TC-TPL-006 | Create template tanpa variables | 1. Login<br>2. Buat template tanpa variables<br>3. Submit | Template dibuat, variables kosong |

### 5.3 Edit Template

**URL:** `/templates/{template}/edit`  
**Method:** GET, PUT

#### Test Cases:

| ID | Test Case | Steps | Expected Result |
|---|---|---|---|
| TC-TPL-007 | Tampilkan form edit | 1. Login<br>2. Klik edit pada template<br>3. Akses form edit | Form terisi dengan data template |
| TC-TPL-008 | Update template berhasil | 1. Login<br>2. Edit template<br>3. Ubah content<br>4. Submit | Template terupdate, redirect ke list |
| TC-TPL-009 | Update status template | 1. Login<br>2. Edit template<br>3. Uncheck "Active"<br>4. Submit | Template status menjadi inactive |

### 5.4 View Template

**URL:** `/templates/{template}`  
**Method:** GET

#### Test Cases:

| ID | Test Case | Steps | Expected Result |
|---|---|---|---|
| TC-TPL-010 | Tampilkan detail template | 1. Login<br>2. Klik template dari list | Menampilkan:<br>- Full content<br>- Variables (jika ada)<br>- Status<br>- Created/Updated date<br>- API usage example |

### 5.5 Delete Template

**URL:** `/templates/{template}`  
**Method:** DELETE

#### Test Cases:

| ID | Test Case | Steps | Expected Result |
|---|---|---|---|
| TC-TPL-011 | Delete template berhasil | 1. Login<br>2. Klik delete pada template<br>3. Konfirmasi | Template dihapus, redirect ke list |
| TC-TPL-012 | Delete template milik user lain | 1. Login sebagai user A<br>2. Coba hapus template milik user B | Error 403: Forbidden |

---

## 6. Webhooks

### 6.1 List Webhooks

**URL:** `/webhooks`  
**Method:** GET

#### Test Cases:

| ID | Test Case | Steps | Expected Result |
|---|---|---|---|
| TC-WH-001 | Tampilkan daftar webhooks | 1. Login<br>2. Akses `/webhooks` | Tabel menampilkan webhooks dengan kolom:<br>- Name<br>- URL<br>- Events<br>- Status<br>- Last Triggered<br>- Actions |

### 6.2 Create Webhook

**URL:** `/webhooks/create`  
**Method:** GET, POST

#### Test Cases:

| ID | Test Case | Steps | Expected Result |
|---|---|---|---|
| TC-WH-002 | Tampilkan form create webhook | 1. Login<br>2. Akses `/webhooks/create` | Form dengan field:<br>- Name (required)<br>- URL (required)<br>- Device Events (checkbox: message, status, session)<br>- Active checkbox |
| TC-WH-003 | Create webhook berhasil | 1. Login<br>2. Isi form lengkap<br>3. Submit | Webhook dibuat, redirect ke list |
| TC-WH-004 | Create webhook dengan URL invalid | 1. Login<br>2. Masukkan URL tidak valid<br>3. Submit | Error: "URL tidak valid" |
| TC-WH-005 | Create webhook tanpa events | 1. Login<br>2. Tidak pilih event apapun<br>3. Submit | Error: "Minimal pilih 1 event" |

### 6.3 Edit Webhook

**URL:** `/webhooks/{webhook}/edit`  
**Method:** GET, PUT

#### Test Cases:

| ID | Test Case | Steps | Expected Result |
|---|---|---|---|
| TC-WH-006 | Update webhook berhasil | 1. Login<br>2. Edit webhook<br>3. Ubah URL atau events<br>4. Submit | Webhook terupdate |

### 6.4 Delete Webhook

**URL:** `/webhooks/{webhook}`  
**Method:** DELETE

#### Test Cases:

| ID | Test Case | Steps | Expected Result |
|---|---|---|---|
| TC-WH-007 | Delete webhook berhasil | 1. Login<br>2. Hapus webhook<br>3. Konfirmasi | Webhook dihapus |

### 6.5 Webhook Trigger Test

#### Test Cases:

| ID | Test Case | Steps | Expected Result |
|---|---|---|---|
| TC-WH-008 | Webhook triggered saat pesan masuk | 1. Setup webhook dengan event "message"<br>2. Kirim pesan ke device<br>3. Periksa webhook logs | Webhook dipanggil, log tercatat |
| TC-WH-009 | Webhook triggered saat status berubah | 1. Setup webhook dengan event "status"<br>2. Ubah status pesan<br>3. Periksa webhook logs | Webhook dipanggil |

---

## 7. API Keys

### 7.1 View API Key

**URL:** `/api-keys`  
**Method:** GET

#### Test Cases:

| ID | Test Case | Steps | Expected Result |
|---|---|---|---|
| TC-API-001 | Tampilkan API key | 1. Login<br>2. Akses `/api-keys` | Menampilkan API key (masked), tombol "Regenerate" |
| TC-API-002 | Copy API key | 1. Login<br>2. Klik tombol copy | API key tercopy ke clipboard |

### 7.2 Regenerate API Key

**URL:** `/api-keys/regenerate`  
**Method:** POST

#### Test Cases:

| ID | Test Case | Steps | Expected Result |
|---|---|---|---|
| TC-API-003 | Regenerate API key berhasil | 1. Login<br>2. Klik "Regenerate"<br>3. Konfirmasi | API key baru dibuat, API key lama tidak valid lagi |
| TC-API-004 | API key lama tidak valid setelah regenerate | 1. Regenerate API key<br>2. Coba gunakan API key lama di API request | Error: "Invalid API key" (401) |

---

## 8. Kontak & Grup

### 8.1 List Contacts

**URL:** `/contacts`  
**Method:** GET

#### Test Cases:

| ID | Test Case | Steps | Expected Result |
|---|---|---|---|
| TC-CONT-001 | Tampilkan daftar devices untuk kontak | 1. Login<br>2. Akses `/contacts` | Menampilkan list devices, link "View contacts for this device" |
| TC-CONT-002 | View contacts per device | 1. Login<br>2. Klik "View contacts for this device" | Menampilkan daftar kontak dari device tersebut |

### 8.2 List Groups

**URL:** `/groups`  
**Method:** GET

#### Test Cases:

| ID | Test Case | Steps | Expected Result |
|---|---|---|---|
| TC-GRP-001 | Tampilkan daftar devices untuk grup | 1. Login<br>2. Akses `/groups` | Menampilkan list devices, link "View groups for this device" |
| TC-GRP-002 | View groups per device | 1. Login<br>2. Klik "View groups for this device" | Menampilkan daftar grup dari device tersebut |

---

## 9. Manajemen Quota

### 9.1 View Quota

**URL:** `/quota`  
**Method:** GET

#### Test Cases:

| ID | Test Case | Steps | Expected Result |
|---|---|---|---|
| TC-QT-001 | Tampilkan informasi quota | 1. Login<br>2. Akses `/quota` | Menampilkan:<br>- Current Quota (Text Quota, Multimedia Quota, Free Text Quota)<br>- Message Pricing<br>- Purchase History<br>- Tombol "Purchase Quota" |
| TC-QT-002 | Tampilkan purchase history | 1. Login<br>2. Akses `/quota`<br>3. Scroll ke purchase history | Tabel menampilkan history pembelian dengan status |

### 9.2 Create Purchase

**URL:** `/quota/create`  
**Method:** GET, POST

#### Test Cases:

| ID | Test Case | Steps | Expected Result |
|---|---|---|---|
| TC-QT-003 | Tampilkan form purchase | 1. Login<br>2. Akses `/quota/create` | Form dengan field:<br>- Text Quota Quantity<br>- Multimedia Quota Quantity<br>- Amount (auto-calculated)<br>- Payment Method (Xendit/Manual) |
| TC-QT-004 | Auto calculate amount | 1. Login<br>2. Masukkan quantity text quota<br>3. Masukkan quantity multimedia quota | Amount otomatis terhitung berdasarkan pricing |
| TC-QT-005 | Purchase dengan Xendit | 1. Login<br>2. Isi form purchase<br>3. Pilih payment method "Xendit"<br>4. Submit | Invoice Xendit dibuat, redirect ke payment page Xendit |
| TC-QT-006 | Purchase dengan Manual | 1. Login<br>2. Isi form purchase<br>3. Pilih payment method "Manual"<br>4. Submit | Purchase dibuat dengan status "Menunggu Pembayaran" |

### 9.3 Confirm Manual Payment

**URL:** `/quota/purchase/{purchase}/confirm-payment`  
**Method:** GET, POST

#### Test Cases:

| ID | Test Case | Steps | Expected Result |
|---|---|---|---|
| TC-QT-007 | Tampilkan form konfirmasi | 1. Login<br>2. Akses purchase dengan status "Menunggu Pembayaran"<br>3. Klik "Konfirmasi Pembayaran" | Form dengan field:<br>- Upload Payment Proof (image)<br>- Payment Reference<br>- Notes |
| TC-QT-008 | Upload payment proof | 1. Login<br>2. Upload bukti pembayaran<br>3. Isi payment reference<br>4. Submit | Status berubah menjadi "Menunggu Konfirmasi Admin" |
| TC-QT-009 | Upload file bukan image | 1. Login<br>2. Upload file PDF<br>3. Submit | Error: "File harus berupa gambar" |

### 9.4 Xendit Payment Callback

**URL:** `/quota/payment/{purchase}/success` atau `/failure`  
**Method:** GET

#### Test Cases:

| ID | Test Case | Steps | Expected Result |
|---|---|---|---|
| TC-QT-010 | Payment success redirect | 1. Selesaikan pembayaran di Xendit<br>2. Redirect ke success page | Status purchase menjadi "completed", quota ditambahkan |
| TC-QT-011 | Payment failure redirect | 1. Batalkan pembayaran di Xendit<br>2. Redirect ke failure page | Status purchase menjadi "failed" |

---

## 10. Sistem Referral

### 10.1 View Referral

**URL:** `/referral`  
**Method:** GET

#### Test Cases:

| ID | Test Case | Steps | Expected Result |
|---|---|---|---|
| TC-REF-001 | Tampilkan informasi referral | 1. Login<br>2. Akses `/referral` | Menampilkan:<br>- Referral Code<br>- Referral Link<br>- Total Referrals<br>- Bonus Settings<br>- List Users yang mendaftar dengan kode ini |

### 10.2 Referral Registration

#### Test Cases:

| ID | Test Case | Steps | Expected Result |
|---|---|---|---|
| TC-REF-002 | Registrasi dengan referral code | 1. Buka `/register`<br>2. Masukkan referral code valid<br>3. Registrasi | User baru terdaftar, referrer mendapat bonus quota |

---

## 11. Profil Pengguna

### 11.1 View Profile

**URL:** `/profile`  
**Method:** GET

#### Test Cases:

| ID | Test Case | Steps | Expected Result |
|---|---|---|---|
| TC-PROF-001 | Tampilkan profil | 1. Login<br>2. Akses `/profile` | Menampilkan:<br>- Name<br>- Email<br>- Referral Code<br>- Form edit |

### 11.2 Update Profile

**URL:** `/profile`  
**Method:** PUT

#### Test Cases:

| ID | Test Case | Steps | Expected Result |
|---|---|---|---|
| TC-PROF-002 | Update name berhasil | 1. Login<br>2. Ubah name<br>3. Submit | Name terupdate, pesan sukses |
| TC-PROF-003 | Update dengan email duplikat | 1. Login<br>2. Ubah email ke email yang sudah ada<br>3. Submit | Error: "Email sudah digunakan" |

---

## 12. Admin Dashboard

### 12.1 View Admin Dashboard

**URL:** `/admin/dashboard`  
**Method:** GET  
**Access:** Admin/Super Admin

#### Test Cases:

| ID | Test Case | Steps | Expected Result |
|---|---|---|---|
| TC-ADM-001 | Tampilkan dashboard admin | 1. Login sebagai admin<br>2. Akses `/admin/dashboard` | Menampilkan statistik lengkap (lihat TC-DASH-004) |
| TC-ADM-002 | Akses sebagai client | 1. Login sebagai client<br>2. Coba akses `/admin/dashboard` | Error 403: Forbidden |

---

## 13. Admin Pricing Settings

### 13.1 View Pricing Settings

**URL:** `/admin/pricing`  
**Method:** GET  
**Access:** Admin

#### Test Cases:

| ID | Test Case | Steps | Expected Result |
|---|---|---|---|
| TC-PRC-001 | Tampilkan pricing settings | 1. Login sebagai admin<br>2. Akses `/admin/pricing` | Form dengan field:<br>- Text with Watermark Price<br>- Text without Watermark Price<br>- Multimedia Price<br>- Watermark Text<br>- Active checkbox |

### 13.2 Update Pricing Settings

**URL:** `/admin/pricing`  
**Method:** PUT

#### Test Cases:

| ID | Test Case | Steps | Expected Result |
|---|---|---|---|
| TC-PRC-002 | Update pricing berhasil | 1. Login sebagai admin<br>2. Ubah harga<br>3. Submit | Pricing terupdate, pesan sukses |
| TC-PRC-003 | Update watermark text | 1. Login sebagai admin<br>2. Ubah watermark text<br>3. Submit | Watermark text terupdate, pesan baru menggunakan watermark baru |

---

## 14. Admin Payment Reports

### 14.1 List Payment Reports

**URL:** `/admin/quota-purchases`  
**Method:** GET  
**Access:** Admin

#### Test Cases:

| ID | Test Case | Steps | Expected Result |
|---|---|---|---|
| TC-PAY-001 | Tampilkan daftar purchases | 1. Login sebagai admin<br>2. Akses `/admin/quota-purchases` | Tabel menampilkan purchases dengan filter:<br>- Status (Pending, Completed, Failed)<br>- Payment Method<br>- Search |
| TC-PAY-002 | Filter by status | 1. Login sebagai admin<br>2. Filter by "Pending" | Tabel hanya menampilkan purchases dengan status pending |
| TC-PAY-003 | Search purchases | 1. Login sebagai admin<br>2. Ketik keyword di search | Tabel menampilkan purchases yang sesuai |

### 14.2 View Purchase Details

**URL:** `/admin/quota-purchases/{quotaPurchase}`  
**Method:** GET

#### Test Cases:

| ID | Test Case | Steps | Expected Result |
|---|---|---|---|
| TC-PAY-004 | Tampilkan detail purchase | 1. Login sebagai admin<br>2. Klik purchase dari list | Menampilkan:<br>- Purchase Details<br>- User Info<br>- Payment Method<br>- Payment Proof (jika manual)<br>- Status<br>- Action buttons (Approve/Reject) |

### 14.3 Approve Purchase

**URL:** `/admin/quota-purchases/{quotaPurchase}/approve`  
**Method:** POST

#### Test Cases:

| ID | Test Case | Steps | Expected Result |
|---|---|---|---|
| TC-PAY-005 | Approve purchase berhasil | 1. Login sebagai admin<br>2. View purchase dengan status "Menunggu Konfirmasi Admin"<br>3. Klik "Approve" | Status menjadi "completed", quota ditambahkan ke user |

### 14.4 Reject Purchase

**URL:** `/admin/quota-purchases/{quotaPurchase}/reject`  
**Method:** POST

#### Test Cases:

| ID | Test Case | Steps | Expected Result |
|---|---|---|---|
| TC-PAY-006 | Reject purchase berhasil | 1. Login sebagai admin<br>2. Klik "Reject" | Status menjadi "failed" |

---

## 15. Admin Referral Settings

### 15.1 View Referral Settings

**URL:** `/admin/referral-settings`  
**Method:** GET  
**Access:** Admin

#### Test Cases:

| ID | Test Case | Steps | Expected Result |
|---|---|---|---|
| TC-REF-ADM-001 | Tampilkan referral settings | 1. Login sebagai admin<br>2. Akses `/admin/referral-settings` | Form dengan field:<br>- Text Quota Bonus<br>- Multimedia Quota Bonus<br>- Active checkbox |

### 15.2 Update Referral Settings

**URL:** `/admin/referral-settings`  
**Method:** PUT

#### Test Cases:

| ID | Test Case | Steps | Expected Result |
|---|---|---|---|
| TC-REF-ADM-002 | Update referral settings berhasil | 1. Login sebagai admin<br>2. Ubah bonus quota<br>3. Submit | Settings terupdate, referral baru menggunakan bonus baru |

---

## 16. API Endpoints

### 16.1 Authentication

**Base URL:** `/api/v1`  
**Authentication:** API Key (Header: `X-API-Key`)

#### Test Cases:

| ID | Test Case | Steps | Expected Result |
|---|---|---|---|
| TC-API-AUTH-001 | Request tanpa API key | 1. Kirim request tanpa header X-API-Key | Error 401: "Unauthorized" |
| TC-API-AUTH-002 | Request dengan API key invalid | 1. Kirim request dengan API key salah | Error 401: "Invalid API key" |
| TC-API-AUTH-003 | Request dengan API key valid | 1. Kirim request dengan API key valid | Request berhasil |

### 16.2 Health Check

**URL:** `/api/health`  
**Method:** GET  
**Auth:** Not Required

#### Test Cases:

| ID | Test Case | Steps | Expected Result |
|---|---|---|---|
| TC-API-HEALTH-001 | Health check | 1. GET `/api/health` | Response: `{"status": "ok", "timestamp": "..."}` |

### 16.3 Sessions API

**URL:** `/api/v1/sessions`  
**Method:** GET

#### Test Cases:

| ID | Test Case | Steps | Expected Result |
|---|---|---|---|
| TC-API-SESS-001 | List sessions | 1. GET `/api/v1/sessions` dengan API key | Response: Array of sessions |
| TC-API-SESS-002 | Get session details | 1. GET `/api/v1/sessions/{session}` | Response: Session details |
| TC-API-SESS-003 | Get session status | 1. GET `/api/v1/sessions/{session}/status` | Response: `{"status": "connected", "is_connected": true}` |

### 16.4 Messages API

**URL:** `/api/v1/messages`  
**Method:** POST, GET

#### Test Cases:

| ID | Test Case | Steps | Expected Result |
|---|---|---|---|
| TC-API-MSG-001 | Send text message | 1. POST `/api/v1/messages`<br>Body: `{"device_id": "...", "message_type": "text", "to": "628...", "message": "Hello"}` | Response: Message created |
| TC-API-MSG-002 | Send text message dengan device_id | 1. POST `/api/v1/messages`<br>Body: `{"device_id": "...", ...}` | Message dikirim |
| TC-API-MSG-003 | Send text message dengan session_id (deprecated) | 1. POST `/api/v1/messages`<br>Body: `{"session_id": "...", ...}` | Error atau tetap bekerja (backward compatibility) |
| TC-API-MSG-004 | Send image message | 1. POST `/api/v1/messages`<br>Body: `{"device_id": "...", "message_type": "image", "to": "...", "image": "https://...", "caption": "..."}` | Image message dikirim |
| TC-API-MSG-005 | Send video message | 1. POST `/api/v1/messages`<br>Body: `{"device_id": "...", "message_type": "video", "to": "...", "video": "https://..."}` | Video message dikirim |
| TC-API-MSG-006 | Send document message | 1. POST `/api/v1/messages`<br>Body: `{"device_id": "...", "message_type": "document", "to": "...", "document": "https://..."}` | Document message dikirim |
| TC-API-MSG-007 | Send message dengan template | 1. POST `/api/v1/messages`<br>Body: `{"device_id": "...", "template_id": "...", "to": "...", "variables": {...}}` | Message dengan template dikirim |
| TC-API-MSG-008 | List messages | 1. GET `/api/v1/messages?device_id=...` | Response: Array of messages |
| TC-API-MSG-009 | Get message details | 1. GET `/api/v1/messages/{message}` | Response: Message details |
| TC-API-MSG-010 | Send message dengan quota habis | 1. POST message dengan quota 0 | Error: "Insufficient quota" |
| TC-API-MSG-011 | Send message dengan free quota | 1. POST text message<br>2. User punya free_text_quota | Message dikirim dengan watermark, free_text_quota berkurang |
| TC-API-MSG-012 | Send message dengan premium quota | 1. POST text message<br>2. User punya text_quota | Message dikirim tanpa watermark, text_quota berkurang |
| TC-API-MSG-013 | Send message dengan multimedia quota | 1. POST image/video message<br>2. User punya multimedia_quota | Message dikirim, multimedia_quota berkurang |

### 16.5 Templates API

**URL:** `/api/v1/templates`  
**Method:** GET, POST, PUT, DELETE

#### Test Cases:

| ID | Test Case | Steps | Expected Result |
|---|---|---|---|
| TC-API-TPL-001 | List templates | 1. GET `/api/v1/templates` | Response: Array of templates |
| TC-API-TPL-002 | Create template | 1. POST `/api/v1/templates`<br>Body: `{"name": "...", "content": "...", "variables": [...]}` | Template created |
| TC-API-TPL-003 | Get template details | 1. GET `/api/v1/templates/{template}` | Response: Template details |
| TC-API-TPL-004 | Update template | 1. PUT `/api/v1/templates/{template}`<br>Body: `{"name": "...", "content": "..."}` | Template updated |
| TC-API-TPL-005 | Delete template | 1. DELETE `/api/v1/templates/{template}` | Template deleted |
| TC-API-TPL-006 | Preview template | 1. POST `/api/v1/templates/{template}/preview`<br>Body: `{"variables": {...}}` | Response: Processed template content |

### 16.6 Account API

**URL:** `/api/v1/account`  
**Method:** GET

#### Test Cases:

| ID | Test Case | Steps | Expected Result |
|---|---|---|---|
| TC-API-ACC-001 | Get account info | 1. GET `/api/v1/account` | Response: Account details |
| TC-API-ACC-002 | Get account usage | 1. GET `/api/v1/account/usage` | Response: Usage statistics |

---

## 17. Webhook External

### 17.1 Xendit Webhook

**URL:** `/webhook/xendit`  
**Method:** POST  
**Auth:** Not Required (Public)

#### Test Cases:

| ID | Test Case | Steps | Expected Result |
|---|---|---|---|
| TC-WH-XEN-001 | Xendit payment paid | 1. Simulasikan webhook Xendit dengan status "PAID"<br>2. POST ke `/webhook/xendit` | Purchase status menjadi "completed", quota ditambahkan |
| TC-WH-XEN-002 | Xendit payment expired | 1. Simulasikan webhook dengan status "EXPIRED" | Purchase status menjadi "failed" |

### 17.2 WAHA Webhook Receiver

**URL:** `/webhook/receive/{session}`  
**Method:** POST  
**Auth:** Not Required (Public)

#### Test Cases:

| ID | Test Case | Steps | Expected Result |
|---|---|---|---|
| TC-WH-REC-001 | Receive incoming message | 1. WAHA mengirim webhook untuk pesan masuk<br>2. POST ke `/webhook/receive/{session}` | Message disimpan, webhook user dipanggil |

---

## 17. OTP Management

### 17.1 Template OTP

**URL:** `/templates`  
**Method:** GET, POST, PUT

#### Test Cases:

| ID | Test Case | Steps | Expected Result |
|---|---|---|---|
| TC-OTP-001 | Create template OTP | 1. Login<br>2. Akses `/templates/create`<br>3. Pilih jenis template "OTP"<br>4. Isi form dengan content yang mengandung `{{kode_otp}}`<br>5. Submit | Template OTP dibuat, variabel `kode_otp` otomatis ditambahkan ke variables |
| TC-OTP-002 | Template OTP auto-add kode_otp | 1. Buat template OTP<br>2. Periksa variables | `kode_otp` otomatis ada di variables meskipun tidak ditambahkan manual |
| TC-OTP-003 | Edit template OTP | 1. Edit template OTP<br>2. Ubah content<br>3. Submit | Template terupdate, `kode_otp` tetap ada di variables |

### 17.2 Send OTP via API

**URL:** `/api/v1/messages/otp`  
**Method:** POST

#### Test Cases:

| ID | Test Case | Steps | Expected Result |
|---|---|---|---|
| TC-OTP-004 | Send OTP dengan template | 1. POST `/api/v1/messages/otp`<br>Body: `{"device_id": "...", "to": "628...", "template_id": "..."}` | OTP code generated, pesan dikirim dengan kode OTP, response berisi `otp_id` |
| TC-OTP-005 | Send OTP tanpa template | 1. POST `/api/v1/messages/otp`<br>Body: `{"device_id": "...", "to": "628..."}` | OTP code generated, pesan default dikirim, response berisi `otp_id` |
| TC-OTP-006 | Send OTP dengan expiry custom | 1. POST dengan `expiry_minutes: 15` | OTP expires dalam 15 menit |
| TC-OTP-007 | Send OTP dengan device tidak connected | 1. POST dengan device_id yang disconnected | Error: "Device tidak connected" |

### 17.3 Verify OTP via API

**URL:** `/api/v1/messages/verify-otp`  
**Method:** POST

#### Test Cases:

| ID | Test Case | Steps | Expected Result |
|---|---|---|---|
| TC-OTP-008 | Verify OTP berhasil | 1. Send OTP<br>2. POST `/api/v1/messages/verify-otp`<br>Body: `{"phone_number": "628...", "code": "123456"}` | Response: `{"success": true, "message": "OTP berhasil diverifikasi"}` |
| TC-OTP-009 | Verify OTP dengan kode salah | 1. Send OTP<br>2. POST dengan kode salah | Response: `{"success": false, "message": "Kode OTP salah", "remaining_attempts": 4}` |
| TC-OTP-010 | Verify OTP yang expired | 1. Send OTP<br>2. Tunggu sampai expired<br>3. Verify | Response: `{"success": false, "message": "OTP sudah kadaluarsa"}` |
| TC-OTP-011 | Verify OTP yang sudah digunakan | 1. Verify OTP berhasil<br>2. Verify lagi dengan kode yang sama | Response: `{"success": false, "message": "OTP sudah pernah digunakan"}` |
| TC-OTP-012 | Verify OTP dengan attempts > 5 | 1. Coba verify 5 kali dengan kode salah<br>2. Coba verify lagi | OTP status menjadi "failed", tidak bisa verify lagi |

### 17.4 Get OTP Status

**URL:** `/api/v1/messages/otp/{otp_id}/status`  
**Method:** GET

#### Test Cases:

| ID | Test Case | Steps | Expected Result |
|---|---|---|---|
| TC-OTP-013 | Get OTP status | 1. Send OTP<br>2. GET `/api/v1/messages/otp/{otp_id}/status` | Response berisi status, expires_at, is_expired, is_verified, attempts |

---

## 18. Webhook External

### Web Features
- ✅ Autentikasi (11 test cases)
- ✅ Dashboard (6 test cases)
- ✅ Device Management (19 test cases)
- ✅ Message Management (18 test cases)
- ✅ Template Management (12 test cases)
- ✅ Webhook Management (9 test cases)
- ✅ API Keys (4 test cases)
- ✅ Contacts & Groups (4 test cases)
- ✅ Quota Management (11 test cases)
- ✅ Referral System (2 test cases)
- ✅ Profile Management (3 test cases)
- ✅ Admin Dashboard (2 test cases)
- ✅ Admin Pricing (3 test cases)
- ✅ Admin Payment Reports (6 test cases)
- ✅ Admin Referral Settings (2 test cases)

### API Features
- ✅ Authentication (3 test cases)
- ✅ Health Check (1 test case)
- ✅ Sessions API (3 test cases)
- ✅ Messages API (13 test cases)
- ✅ Templates API (6 test cases)
- ✅ Account API (2 test cases)
- ✅ OTP Management (10 test cases)

### External Webhooks
- ✅ Xendit Webhook (2 test cases)
- ✅ WAHA Webhook Receiver (1 test cases)

**Total Test Cases: 133**

---

## 🔧 Test Environment Setup

### Prerequisites
1. Laravel application running on `http://localhost:8000`
2. WAHA Docker container running on `http://localhost:3000`
3. Database MySQL/SQLite configured
4. Xendit API keys configured (untuk testing payment)

### Test Data
- Admin user: `admin@example.com` / `password`
- Client user: Create via registration
- Test device: Create via web interface
- Test API key: Generate via `/api-keys`

### Tools Recommended
- Postman/Insomnia untuk API testing
- Browser DevTools untuk web testing
- Database client untuk verify data
- WhatsApp mobile untuk testing QR scan

---

## 📝 Notes

1. **Rate Limiting**: Beberapa endpoint memiliki rate limiting, perhatikan saat testing
2. **Quota System**: Pastikan quota cukup sebelum testing pengiriman pesan
3. **Watermark**: Pesan dengan free quota akan otomatis ditambahkan watermark
4. **Device Status**: Pastikan device dalam status "connected" sebelum testing pesan
5. **Payment Testing**: Gunakan Xendit sandbox untuk testing payment

---

**Last Updated:** 2025-11-29  
**Version:** 1.0.0  
**Maintained By:** Development Team

