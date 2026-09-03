<?php

namespace Tests\Unit;

use CrudBooster\Tests\BaseTestCase;
use CrudBooster\Components\Type\Select\Function\Select;
use CrudBooster\Components\Type\Select\Function\SelectComponent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use ReflectionClass;

class Category extends Model
{
    protected $table = 'categories';
    protected $guarded = [];
    public $timestamps = false;
}

class SelectSearchableTest extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            \Livewire\LivewireServiceProvider::class,
            \CrudBooster\CBServiceProvider::class,
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('active')->default(true);
        });

        Category::create(['name' => 'Technology', 'active' => 1]);
        Category::create(['name' => 'Health', 'active' => 1]);
        Category::create(['name' => 'Sports', 'active' => 0]);
        Category::create(['name' => 'Science', 'active' => 1]);
    }

    public function testSearchableWithClosure()
    {
        // 1. Define the Select configuration
        $selectOption = Select::option()
            ->searchable(function($query, $keyword) {
                $query->where('name', 'like', '%' . $keyword . '%');
            })
            ->model(Category::class, 'id', 'name', function($query) {
                $query->where('active', 1);
            });

        // Get the options array
        $options = $selectOption->__getOption();

        // 2. Instantiate SelectComponent and inject configuration
        $component = new SelectComponent();
        $component->column = ['option' => $options];
        $component->keyword = 'Tech'; // Search for 'Technology'

        // 3. Invoke protected method findKeyword via Reflection
        $reflection = new ReflectionClass($component);
        $method = $reflection->getMethod('findKeyword');
        $method->setAccessible(true);
        $method->invoke($component);

        // 4. Assert results
        // 'Technology' should be found (active=1, matches keyword)
        // 'Sports' should NOT be found (active=0, even if keyword matched)
        // 'Health' should NOT be found (doesn't match keyword)
        
        $dataset = $component->dataset;
        
        $this->assertCount(1, $dataset);
        $this->assertEquals('Technology', $dataset[0]['label']);
        $this->assertEquals(1, $dataset[0]['key']);
    }

    public function testSearchableWithClosureNoResult()
    {
        $selectOption = Select::option()
            ->searchable(function($query, $keyword) {
                $query->where('name', 'like', '%' . $keyword . '%');
            })
            ->model(Category::class, 'id', 'name', function($query) {
                $query->where('active', 1);
            });

        $options = $selectOption->__getOption();

        $component = new SelectComponent();
        $component->column = ['option' => $options];
        $component->keyword = 'Sports'; // 'Sports' exists but is inactive

        $reflection = new ReflectionClass($component);
        $method = $reflection->getMethod('findKeyword');
        $method->setAccessible(true);
        $method->invoke($component);

        $dataset = $component->dataset;
        
        $this->assertCount(0, $dataset);
    }
}
