<?php

namespace CrudBooster\Tests\Feature;

use CrudBooster\Modules\ApiBuilder\Livewire\ApiBuilderList;
use CrudBooster\Modules\ApiBuilder\Models\CbApiBuilder;
use CrudBooster\Modules\ApiBuilder\Models\CbApiToken;
use CrudBooster\Tests\BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Livewire\Livewire;

class ApiBuilderLivewireTest extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->artisan('migrate');

        // Create a dummy table for Quick Mode testing
        if (!Schema::hasTable('test_products')) {
            Schema::create('test_products', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->decimal('price', 8, 2);
                $table->timestamps();
            });
        }
    }

    public function test_can_render_api_builder_list_component()
    {
        Livewire::test(ApiBuilderList::class)
            ->assertStatus(200)
            ->assertSee('API Infrastructure');
    }

    public function test_can_switch_tabs_in_list_component()
    {
        Livewire::test(ApiBuilderList::class)
            ->set('activeTab', 'list')
            ->assertSee('Total APIs')
            ->call('setTab', 'credential')
            ->assertSet('activeTab', 'credential')
            ->assertSee('Security Credentials')
            ->call('setTab', 'logs')
            ->assertSet('activeTab', 'logs')
            ->assertSee('Real-time Activity Log');
    }

    public function test_can_generate_new_token()
    {
        Livewire::test(ApiBuilderList::class)
            ->call('generateToken')
            ->assertSet('showTokenModal', true)
            ->set('newTokenName', 'My Test Token')
            ->set('newTokenStatus', 'active')
            ->set('newTokenScope', '/v1/custom/*')
            ->call('submitGenerateToken')
            ->assertSet('showTokenModal', false)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('cb_api_tokens', [
            'name' => 'My Test Token',
            'status' => 'active',
            'scope_endpoint' => '/v1/custom/*',
        ]);
        
        $this->assertEquals(1, CbApiToken::count());
    }

    public function test_token_validation_fails_on_empty_name()
    {
        Livewire::test(ApiBuilderList::class)
            ->call('generateToken')
            ->set('newTokenName', '') // Empty name
            ->call('submitGenerateToken')
            ->assertHasErrors(['name' => 'required']);
    }

    public function test_token_validation_fails_on_invalid_scope()
    {
        Livewire::test(ApiBuilderList::class)
            ->call('generateToken')
            ->set('newTokenName', 'Scope Test')
            ->set('newTokenScope', 'javascript:alert(1)')
            ->call('submitGenerateToken')
            ->assertHasErrors(['scope' => 'regex']);
    }

    public function test_quick_mode_generates_crud_apis()
    {
        Livewire::test(ApiBuilderList::class)
            ->call('openNewApiModal')
            ->assertSet('showNewApiModal', true)
            ->assertSet('newApiMode', 'quick')
            ->set('quickModeTable', 'test_products')
            ->call('proceedNewApi')
            ->assertSet('showNewApiModal', false);

        // Quick mode should generate 5 APIs (List, Detail, Create, Update, Delete)
        $this->assertEquals(5, CbApiBuilder::count());
        
        $this->assertDatabaseHas('cb_api_builders', [
            'name' => 'List Test products',
            'endpoint_path' => '/v1/test-products/list',
            'method' => 'GET',
        ]);
        
        $this->assertDatabaseHas('cb_api_builders', [
            'name' => 'Create Test products',
            'endpoint_path' => '/v1/test-products/create',
            'method' => 'POST',
        ]);
    }

    public function test_can_delete_api()
    {
        $api = CbApiBuilder::create([
            'name' => 'API to delete',
            'endpoint_path' => '/v1/delete-me',
            'method' => 'GET',
            'status' => 'active',
            'payload_schema' => [],
            'process_steps' => [],
            'response_mapper' => ['mode' => 'last_action', 'mapping' => []],
        ]);

        Livewire::test(ApiBuilderList::class)
            ->call('deleteApi', $api->id)
            ->assertSet('confirmTitle', __('api_builder::api_builder.alerts.confirm_delete_title'))
            ->call('deleteApiConfirmed', $api->id);

        $this->assertDatabaseMissing('cb_api_builders', [
            'id' => $api->id
        ]);
    }

    public function test_can_open_and_close_test_modal()
    {
        $api = CbApiBuilder::create([
            'name' => 'Test API Modal',
            'endpoint_path' => '/v1/test-modal',
            'method' => 'GET',
            'status' => 'active',
            'payload_schema' => [],
            'process_steps' => [],
            'response_mapper' => ['mode' => 'last_action', 'mapping' => []],
        ]);

        Livewire::test(ApiBuilderList::class)
            ->call('openTestModal', $api->id)
            ->assertSet('showTestModal', true)
            ->assertSet('testApiId', $api->id)
            ->assertSet('testMethod', 'GET')
            ->call('closeTestModal')
            ->assertSet('showTestModal', false)
            ->assertSet('testApiId', null);
    }

    public function test_can_add_condition_action()
    {
        Livewire::test(\CrudBooster\Modules\ApiBuilder\Livewire\ApiBuilderCreate::class)
            ->call('openAddActionModal')
            ->set('actionForm.action_type', 'condition')
            ->set('actionForm.alias', 'check_something')
            ->set('actionForm.condition_logical_operator', 'and')
            ->set('actionForm.condition_rules', [
                [
                    'source_ref' => 'payload.amount',
                    'operator' => '>',
                    'value' => '1000'
                ]
            ])
            ->call('saveActionModal')
            ->assertHasNoErrors()
            ->assertSet('showActionModal', false);
    }

    public function test_livewire_populates_prefixed_table_columns()
    {
        Livewire::test(\CrudBooster\Modules\ApiBuilder\Livewire\ApiBuilderCreate::class)
            ->call('openAddActionModal')
            ->set('actionForm.target_table', 'test_products')
            // After setting target_table, tableColumns should be populated with prefixed names
            ->assertSet('tableColumns', [
                'test_products.id',
                'test_products.name',
                'test_products.price',
                'test_products.created_at',
                'test_products.updated_at',
            ]);
    }

    public function test_livewire_populates_prefixed_join_columns()
    {
        // Setup another table for join
        if (!Schema::hasTable('test_categories')) {
            Schema::create('test_categories', function (Blueprint $table) {
                $table->id();
                $table->string('cat_name');
            });
        }

        Livewire::test(\CrudBooster\Modules\ApiBuilder\Livewire\ApiBuilderCreate::class)
            ->call('openAddActionModal')
            ->set('actionForm.target_table', 'test_products')
            ->call('addJoin')
            ->set('actionForm.joins.0.target_table', 'test_categories')
            ->set('actionForm.joins.0.alias', 'cats')
            // After adding join with alias, tableColumns should include joined table columns with alias prefix
            ->assertSet('tableColumns', function ($cols) {
                return in_array('cats.cat_name', $cols) && in_array('test_products.name', $cols);
            });
    }
}
