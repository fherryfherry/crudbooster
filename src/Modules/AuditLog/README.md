# Audit Log Module

Audit Log is a built-in, read-only module for tracing user activities with hybrid capture:

- CRUD lifecycle events (`create`, `update`, `delete`)
- Auth activities (`login`, `logout`)
- Request metadata (`method`, `path`, `ip`, `request_id`, masked payload)

## Retention

Default retention is 90 days (configurable via `cb.audit_log.retention_days`).

Run prune manually:

```bash
php artisan cb:audit-log:prune
php artisan cb:audit-log:prune --days=120
```

Schedule daily prune in the host app `routes/console.php`:

```php
use Illuminate\Support\Facades\Schedule;

Schedule::command('cb:audit-log:prune')->dailyAt('02:10');
```

