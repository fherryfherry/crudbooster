<?php

namespace CrudBooster\Helpers;

use Illuminate\Support\Facades\Route;

class CBRoute
{
    private static function withAuditMiddleware(array $middleware): array
    {
        if (!config('cb.audit_log.enabled', true)) {
            return $middleware;
        }

        if (in_array('cb.audit', $middleware, true)) {
            return $middleware;
        }

        $middleware[] = 'cb.audit';
        return $middleware;
    }

    /**
     * Create a new route for CRUD
     * @param $path
     * @param string $browseComponent
     * @param string $formComponent
     * @param array $middleware
     * @return void
     */
    public static function createRoute($path, string $browseComponent, string $formComponent, array $middleware = ['web', 'auth']): void
    {
        $middleware = self::withAuditMiddleware($middleware);
        Route::prefix(CBPathUtil::getCmsPath($path))->group(function () use ($path, $browseComponent, $formComponent, $middleware) {
            Route::get('/', $browseComponent)->middleware($middleware)->name($path.'.index');
            Route::get('/{actionOne}/{actionTwo}', $formComponent)->middleware($middleware)->name($path.'.edit');
            Route::get('/{actionOne}', $formComponent)->middleware($middleware)->name($path.'.detail');
        });
    }

    public static function createRouteOne($path, string $component, $middleware = ['web', 'auth']): void
    {
        $middleware = self::withAuditMiddleware($middleware);
        Route::prefix(CBPathUtil::getCmsPath($path))->group(function () use ($path, $component, $middleware) {
            Route::get('/', $component)->middleware($middleware)->name($path);
        });
    }

}
