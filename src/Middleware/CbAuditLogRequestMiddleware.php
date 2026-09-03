<?php

namespace CrudBooster\Middleware;

use Closure;
use CrudBooster\Modules\AuditLog\Services\AuditLogService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CbAuditLogRequestMiddleware
{
    public function __construct(
        private readonly AuditLogService $auditLogService
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $requestId = $this->auditLogService->ensureRequestId($request);
        try {
            /** @var Response $response */
            $response = $next($request);
            $this->auditLogService->captureRequest($request, $response);
            $response->headers->set('X-CB-Request-Id', $requestId);

            return $response;
        } catch (\Throwable $exception) {
            $this->auditLogService->captureRequestException($request, $exception);
            throw $exception;
        }
    }
}
