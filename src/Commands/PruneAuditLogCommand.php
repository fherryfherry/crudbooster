<?php

namespace CrudBooster\Commands;

use CrudBooster\Modules\AuditLog\Services\AuditLogService;
use Illuminate\Console\Command;

class PruneAuditLogCommand extends Command
{
    protected $signature = 'cb:audit-log:prune {--days= : Retention days override}';

    protected $description = 'Prune old CRUDBooster audit logs based on retention policy.';

    public function handle(AuditLogService $auditLogService): int
    {
        $configuredDays = (int) config('cb.audit_log.retention_days', 90);
        $days = (int) ($this->option('days') ?: $configuredDays);
        if ($days <= 0) {
            $this->error('Retention days must be greater than 0.');
            return self::FAILURE;
        }

        $deleted = $auditLogService->prune($days);
        $this->info("Audit log prune completed. Deleted {$deleted} record(s) older than {$days} day(s).");

        return self::SUCCESS;
    }
}

