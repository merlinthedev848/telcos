# Shared Hosting Deployment

TelecomOS v1.0.0.0 can be tested on Apache shared hosting with MySQL. The preferred production layout is still to point the web root at Laravel's `public` directory, but many shared hosts only expose `public_html`. This guide supports that constraint.

## Requirements

- PHP 8.3 or newer.
- MySQL 8 or MariaDB 10.6+.
- Apache with `.htaccess` and `mod_rewrite` enabled.
- PHP extensions: `ctype`, `curl`, `dom`, `fileinfo`, `filter`, `hash`, `mbstring`, `openssl`, `pdo_mysql`, `session`, `tokenizer`, `xml`.
- SSH or local Composer access to install dependencies before upload.

## Build The Upload Folder

On your workstation or any machine with PHP and Composer:

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
php tools/build-shared-hosting-package.php
```

Upload the contents of `build/shared-hosting/public_html` into the host's `public_html` directory.

## Configure `.env`

Copy `.env.example` to `.env` inside `public_html` and set:

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.example
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_cpanel_database
DB_USERNAME=your_cpanel_user
DB_PASSWORD=your_cpanel_password
QUEUE_CONNECTION=database
CACHE_STORE=database
SESSION_DRIVER=database
MAIL_MAILER=smtp
```

Generate an app key before upload with `php artisan key:generate --show`, then paste it into `APP_KEY`.

## Database

Run migrations over SSH if available:

```bash
php artisan migrate --seed --force
```

If SSH is unavailable, run the app locally against the same schema version, export SQL from MySQL, and import it through phpMyAdmin. The seeded admin login is:

- Email: `admin@telecomos.local`
- Password: `telecomos`

Change this immediately after testing.

## Queues And Scheduler

Shared hosting usually cannot run Redis, Horizon, or long-running queue workers. For public_html testing, use:

```dotenv
QUEUE_CONNECTION=database
CACHE_STORE=database
SESSION_DRIVER=database
```

If cron is available, add:

```bash
* * * * * /usr/local/bin/php /home/USER/public_html/artisan schedule:run >> /dev/null 2>&1
```

If cron is not available, billing, dunning, and paywall commands can still be run manually through SSH.

## Security Notes

The root `.htaccess` denies direct browser access to Laravel internals such as `app`, `config`, `database`, `routes`, `storage`, `vendor`, and `.env`, then routes traffic to `public/index.php`.

For a real production install, place the Laravel app outside `public_html` and copy only the contents of `public` into the web root. The bundled public_html mode exists so v1.0.0.0 can be tested on commodity shared hosting.
