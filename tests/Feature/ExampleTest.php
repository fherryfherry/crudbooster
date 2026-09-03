<?php

namespace CrudBooster\Tests\Feature;

use Orchestra\Testbench\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        // mock route to avoid 404
        $this->app['router']->get('/', function () {
            return 'Hello World';
        });

        $response = $this->get(url('/'));
        $response->assertStatus(200);
    }
}
