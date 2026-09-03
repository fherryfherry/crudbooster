<?php

namespace CrudBooster\Modules\AuditLog\Listeners;

use CrudBooster\Events\EventDataDeleted;
use CrudBooster\Events\EventDataDeleting;
use CrudBooster\Events\EventFormSaved;
use CrudBooster\Events\EventFormSaving;
use CrudBooster\Modules\AuditLog\Services\AuditLogService;
use CrudBooster\Modules\Auth\Events\LoginAttemptFailed;
use CrudBooster\Modules\Auth\Events\LoginAttemptSuccess;
use CrudBooster\Modules\Auth\Events\LogoutSuccess;

class AuditEventSubscriber
{
    public function __construct(
        private readonly AuditLogService $auditLogService
    ) {
    }

    public function onFormSaving(EventFormSaving $event): void
    {
        $this->auditLogService->onFormSaving($event);
    }

    public function onFormSaved(EventFormSaved $event): void
    {
        $this->auditLogService->onFormSaved($event);
    }

    public function onDataDeleting(EventDataDeleting $event): void
    {
        $this->auditLogService->onDataDeleting($event);
    }

    public function onDataDeleted(EventDataDeleted $event): void
    {
        $this->auditLogService->onDataDeleted($event);
    }

    public function onLoginSuccess(LoginAttemptSuccess $event): void
    {
        $this->auditLogService->onLoginSuccess($event);
    }

    public function onLoginFailed(LoginAttemptFailed $event): void
    {
        $this->auditLogService->onLoginFailed($event);
    }

    public function onLogoutSuccess(LogoutSuccess $event): void
    {
        $this->auditLogService->onLogoutSuccess($event);
    }
}

