# GreenPrinting Deployment

## Requirement

- PHP 8.2+
- Composer
- MySQL atau MariaDB
- Node.js dan npm
- Nginx/Apache dengan document root ke folder `public`
- SSL certificate

## Environment Production

Contoh konfigurasi `.env`:

```env
APP_NAME=GreenPrinting
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domain-anda.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=greenprinting
DB_USERNAME=greenprinting_user
DB_PASSWORD=password_kuat

FILESYSTEM_DISK=local
```

## Deploy

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
cp .env.production.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Permission

```bash
chmod -R 775 storage bootstrap/cache
```

## Cron

```bash
* * * * * cd /path/greenprinting && php artisan schedule:run >> /dev/null 2>&1
```

## Backup

Backup minimal:

- Database MySQL harian
- Folder `storage/app/private`
- Folder invoice PDF jika disimpan permanen

File desain customer dan bukti transfer disimpan di private storage, bukan folder public langsung.

## PWA

Pastikan aplikasi berjalan di HTTPS agar browser mengizinkan instalasi PWA.

File PWA:

```text
public/manifest.webmanifest
public/sw.js
public/offline.html
public/icons/icon.svg
public/icons/splash.svg
```

Setelah deploy, buka domain di browser mobile/desktop dan pilih install app.

## GitHub Checklist

Sebelum push:

```bash
php artisan test
npm run build
git init
git add .
git commit -m "Initial GreenPrinting release"
git branch -M main
git remote add origin https://github.com/username/greenprinting.git
git push -u origin main
```

Jangan commit `.env`, `vendor`, `node_modules`, `storage/app/private`, atau `public/storage`. File tersebut sudah diabaikan oleh `.gitignore`.
