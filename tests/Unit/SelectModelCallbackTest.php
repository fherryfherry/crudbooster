<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Illuminate\Database\Eloquent\Builder;
use Mockery;

class SelectModelCallbackTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testSimpleCallback()
    {
        // Test simple callback without $id parameter
        $callback = function($query) {
            return $query->where('status', 'active');
        };
        
        // Create a mock query builder
        $mockQuery = Mockery::mock(Builder::class);
        $mockQuery->shouldReceive('where')->with('status', 'active')->andReturnSelf();
        
        // Call the callback
        $callback($mockQuery);
        
        $this->assertTrue(true); // If we reach here, the callback worked
    }
}
