# 🚀 Deenhag - Green Printing Management System

<p align="center">
  <img src="public/icons/icon.svg" width="120" alt="Deenhag Logo">
</p>

<p align="center">
  <b>Modern Printing Management System</b><br>
  Order • Production • Inventory • Customer • PWA Installable
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12-red">
  <img src="https://img.shields.io/badge/PHP-8.3-blue">
  <img src="https://img.shields.io/badge/PWA-Installable-green">
  <img src="https://img.shields.io/badge/WAHA-Cloud-success">
  <img src="https://img.shields.io/badge/License-Private-orange">
</p>

---

# 📌 Tentang Project

**Deenhag** adalah aplikasi **manajemen percetakan modern** berbasis web yang membantu pengelolaan:

- Produk & harga
- Customer order
- Upload desain
- Produksi
- Stok bahan
- Pembayaran
- Invoice
- Laporan
- Dashboard analytics

Aplikasi ini juga mendukung **PWA (Progressive Web App)** sehingga dapat di-install seperti aplikasi Android/Desktop dan tetap memiliki **offline fallback**.

---

# ✨ Fitur Utama

## 👥 User Management
- Admin
- Staff
- Customer
- Reset password
- Basic 2FA

---

## 🛍️ Produk & Katalog
- Produk
- Kategori
- Opsi produk
- Varian produk
- Sample desain

---

## 💰 Kalkulator Harga
- Harga otomatis
- Perhitungan quantity
- Opsi tambahan
- Dynamic pricing

---

## 🛒 Cart & Checkout
- Keranjang belanja
- Checkout order
- Upload desain customer
- Catatan pesanan

---

## 🏭 Production Management
- Order management
- Status produksi
- Workflow produksi
- Service Order

### Checklist Produksi
- Desain
- Approval
- Cetak
- Finishing
- Packing
- Pengiriman

---

## 🧾 Invoice & PDF
- Cetak invoice PDF
- Download invoice
- Riwayat invoice

---

## 💳 Pembayaran
- Pembayaran manual transfer
- Upload bukti transfer
- Approval pembayaran
- Status pembayaran

---

## 📦 Inventory & Stock
- Stok bahan
- Stock movement
- Barang masuk
- Barang keluar
- Riwayat stok

---

## 👤 Customer Management
- Data customer
- Histori order
- Reset password
- Basic 2FA

---

## 📊 Dashboard Analytics
- Grafik penjualan
- Statistik order
- Revenue chart
- Product performance

---

## 📈 Reporting
Filter lengkap:
- Search
- Date range
- Export
- Print

Laporan:
- Penjualan
- Order
- Produk
- Stok bahan

---

## 📧 SMTP Email
Digunakan untuk:

- Lupa password
- OTP
- Notifikasi sistem

---

## 📱 WAHA Cloud Integration

### Konfigurasi

```text
WAHA Base URL:


WAHA API Key:
Isi X-Api-Key dari WAHA Cloud

Session:
xxxxx

Nomor Admin:
62xxxxxxxxxx
```

### Webhook

```text
https://domain-anda.com/webhooks/waha
```

---

# 📲 Progressive Web App (PWA)

Aplikasi mendukung:

✅ Install seperti aplikasi Android/Desktop  
✅ Offline fallback  
✅ Cache halaman otomatis  
✅ Fast loading  
✅ HTTPS ready  

### Struktur File PWA

```text
public/
│── manifest.webmanifest
│── sw.js
│── offline.html
│
└── icons/
    ├── icon.svg
    └── splash.svg
```

Setelah deploy menggunakan **HTTPS**, browser otomatis menampilkan opsi:

> **Install App**

---

# ⚙️ Requirements

Server minimum:

| Requirement | Version |
|-------------|----------|
| PHP | 8.3+ |
| NodeJS | 20+ |
| Composer | Latest |
| MySQL | 8+ |
| NPM | Latest |

---

# 🛠️ Installation

## 1. Clone Repository

```bash
git clone https://github.com/username/deenhag.git
cd deenhag
```

---

## 2. Install Dependency

```bash
composer install
npm install
```

---

## 3. Setup Environment

```bash
cp .env.example .env
```

Generate key:

```bash
php artisan key:generate
```

---

## 4. Database Migration

```bash
php artisan migrate --seed
```

---

## 5. Storage Link

```bash
php artisan storage:link
```

---

## 6. Build Asset

```bash
npm run build
```

---

## 7. Run Application

```bash
php artisan serve
```

App URL:

```text
http://127.0.0.1:8000
```

---

# 🔑 Demo Login

| Role | Email | Password |
|------|--------|----------|
| Admin | admin@greenprinting.test | password |
| Staff | staff@greenprinting.test | password |
| Customer | customer@greenprinting.test | password |

---

# 🧩 Tech Stack

### Backend
- Laravel
- MySQL
- Queue
- Storage

### Frontend
- Blade
- TailwindCSS
- AlpineJS
- ChartJS

### Notification
- WAHA Cloud
- SMTP

### Mobile Experience
- PWA
- Service Worker
- Offline Support

---

# 🚀 Deployment

Lihat dokumentasi:

```text
DEPLOYMENT.md
```

Pastikan menggunakan:

- HTTPS
- SSL aktif
- Queue worker
- Cron job
- Supervisor (production)

---

# 🔒 Security

Fitur keamanan dasar:

- CSRF Protection
- Password Hashing
- Role Permission
- Basic 2FA
- Login validation
- Secure Upload

---

# 📄 License

Private Project  
© 2026 **Deenhag**

---

<p align="center">
Made with ❤️ by GreenTech
</p>
