<?php

use CrudBooster\Helpers\CBRoute;
use CrudBooster\Modules\AuditLog\Livewire\AuditLogList;

CBRoute::createRouteOne('audit-log', AuditLogList::class, ['web', 'auth']);

