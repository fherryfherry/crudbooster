<?php

namespace CrudBooster\Tests;

use Illuminate\Support\Facades\Log;
use Orchestra\Testbench\TestCase;

class BaseTestCase extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            \Livewire\LivewireServiceProvider::class,
            'CrudBooster\CBServiceProvider',
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('app.key', 'base64:Hup+k9Qk2hKqH1tFh8mZ0nF+hL/9b1s8t4QkR9hL8mA=');
    }

    protected function setUp(): void
    {
        parent::setUp();

        Log::debug('BaseTestCase setup');

        // Additional setup if needed
    }

}
