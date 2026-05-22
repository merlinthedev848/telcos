# TelecomOS

Version: `1.0.0.0`

TelecomOS is a Laravel-based modular telecom OSS/BSS platform. It is built as a modular monolith under `app/Modules` so customer, billing, provisioning, paywall, support, email, switching, automation, integration, and reporting capabilities can later split into services without rewriting core workflows.

## Current Bootstrap

- Laravel 11 application skeleton with Docker Compose.
- PHP-FPM, Nginx, MySQL, Redis, queue worker, scheduler, Horizon, Mailpit, and optional Meilisearch.
- LAMP-compatible Apache profile for hosts that need classic Apache/PHP deployment parity.
- Module registry and RBAC permission seed.
- WHMCS-inspired Tailwind admin layout.
- Customers, Customer360, billing, dunning, DNS paywall, provisioning adapter interface, TP-Link Omada adapter skeleton, Kayako-style support inbox, email threading, audit/event infrastructure, and One Touch Switching workflow skeleton.
- Disabled future module slots for live chat, NOC, GIS, and field service.

## Start

```bash
cp .env.example .env
docker compose up -d --build
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
docker compose exec app npm install
docker compose exec app npm run build
```

Open `http://localhost:8080`.

For LAMP-style Apache locally:

```bash
docker compose --profile lamp up -d apache mysql redis mailpit
```

Open `http://localhost:8081`.

## Shared Hosting

TelecomOS v1.0.0.0 includes a shared-hosting deployment path for Apache/cPanel-style environments where the app is uploaded into `public_html` and connected to a MySQL database.

Read [docs/SHARED_HOSTING.md](docs/SHARED_HOSTING.md) before uploading.

## Architecture

Critical operations should use service classes, dispatch `CriticalOperationRecorded`, and write to `audit_logs`. Queue listeners and module jobs should hang off those events rather than being buried in controllers.

## Default Admin

- Email: `admin@telecomos.local`
- Password: `telecomos`

## API Surface

Initial API endpoints live under `/api/v1` for customers, invoices, dunning, paywall apply/restore, TP-Link provisioning activation, support tickets, and OTS workflows.
