# Deployment Guide

## Server Requirements

| Requirement | Minimum | Recommended |
|-------------|---------|-------------|
| PHP | 8.5 | 8.5.4 |
| Database | MySQL 8.0 / PostgreSQL 15 | MySQL 8.4 / PG 16 |
| Web Server | Apache 2.4 / Nginx 1.24 | Nginx 1.26 |
| RAM | 2 GB | 4 GB |
| Storage | 20 GB | 50 GB (SSD) |
| PHP Extensions | BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML, GD, Zip | Same |

## Environment Setup

### 1. Clone Repository

```bash
git clone https://github.com/alk-tech/istana-laundry.git
cd istana-laundry
```

### 2. Install Dependencies

```bash
composer install --optimize-autoloader --no-dev
npm install && npm run build
```

### 3. Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

**`.env` key settings:**
```env
APP_NAME="Istana Laundry"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://admin.istanalaundry.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=istana_laundry
DB_USERNAME=istana_user
DB_PASSWORD=secure_password

SESSION_DRIVER=file
SESSION_LIFETIME=120

MIDTRANS_SERVER_KEY=
MIDTRANS_CLIENT_KEY=
MIDTRANS_IS_PRODUCTION=false
```

### 4. Database Migration & Seeder

```bash
php artisan migrate --seed
```

### 5. Storage & Cache

```bash
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 6. Web Server

**Nginx config:**
```nginx
server {
    listen 443 ssl;
    server_name admin.istanalaundry.com;
    root /var/www/istana-laundry/public;
    
    ssl_certificate /etc/ssl/certs/istanalaundry.crt;
    ssl_certificate_key /etc/ssl/private/istanalaundry.key;
    
    index index.php;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.5-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    location ~ /\.ht { deny all; }
    location ~ \.env$ { deny all; }
    
    # Public tracking (separate subdomain or same server)
}
```

### 7. Scheduler (for cron tasks)

```cron
# Run every minute for queued jobs
* * * * * cd /var/www/istana-laundry && php artisan schedule:run >> /dev/null 2>&1
```

### 8. Backup

Manual backup via admin UI or CLI:

```bash
php artisan backup:run
```

Backup location: `storage/app/backups/`

## Public Tracking Page

Served on same or separate subdomain:

```
https://track.istanalaundry.com/track/{token}
```

No special config needed — handled by Laravel routing.

## Monitoring (Future)

- Laravel Telescope for development debugging
- Error tracking: Sentry / Flare
- Uptime monitoring: Better Uptime / Upptime

## Rollback Plan

```bash
# 1. Restore database from backup
php artisan backup:restore

# 2. Roll back code
git checkout previous-tag

# 3. Clear cache
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 4. Run migrations if needed
php artisan migrate
```
