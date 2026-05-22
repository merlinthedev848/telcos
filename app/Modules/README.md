# TelecomOS Modules

TelecomOS is a modular Laravel monolith. Keep business workflows in module services, not controllers.

## Rules

- Every critical operation records `CriticalOperationRecorded`.
- Every critical operation writes `audit_logs` through the queued listener.
- Any work that can wait should be queued on `critical`, `mail`, `provisioning`, or `default`.
- Controllers stay thin and delegate to services.
- Modules own their models, services, controllers, API endpoints, and future jobs.
- Cross-module integrations go through service contracts or events.

## Bounded Modules

- `Core`: module registry, audit, RBAC, dashboard shell.
- `Customers`: customer CRUD and Customer360.
- `Billing`: invoices, payments, dunning.
- `Provisioning`: adapter contracts and TP-Link Omada implementation.
- `Paywall`: DNS restriction and instant restore policy.
- `Support`: Kayako-style inbox and tickets.
- `Email`: email message threading.
- `Switching`: UK One Touch Switching workflow.
- `Automations`: scheduled and event-driven jobs.
- `Integrations`: external API connectors.
- `Reports`: operational and revenue reporting.
