<?php

namespace CrudBooster\Tests\Feature;

use CrudBooster\Modules\ApiBuilder\Models\CbApiBuilder;
use CrudBooster\Modules\ApiBuilder\Models\CbApiToken;
use CrudBooster\Tests\BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Str;

class ApiBuilderExecutionTest extends BaseTestCase
{
    use RefreshDatabase;
    
    private string $runtimeTokenRaw = 'cb_runtime_test_token';

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->artisan('migrate');

        // Create a dummy table for testing
        if (!Schema::hasTable('test_users')) {
            Schema::create('test_users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->timestamps();
            });
        }

        CbApiToken::create([
            'name' => 'Runtime Test Token',
            'status' => 'active',
            'scope_endpoint' => '/v1/*',
            'token_hash' => hash('sha256', $this->runtimeTokenRaw),
            'token_encrypted' => $this->runtimeTokenRaw,
            'auth_method' => 'api_key',
        ]);

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->runtimeTokenRaw,
        ]);
    }

    public function test_api_execution_select_action()
    {
        // Insert dummy data
        DB::table('test_users')->insert([
            ['name' => 'John Doe', 'email' => 'john@example.com'],
            ['name' => 'Jane Smith', 'email' => 'jane@example.com'],
        ]);

        $api = CbApiBuilder::create([
            'name' => 'Get Users',
            'endpoint_path' => '/v1/test-users',
            'method' => 'GET',
            'status' => 'active',
            'payload_schema' => [],
            'process_steps' => [
                [
                    'alias' => 'users_data',
                    'action_type' => 'select',
                    'target_table' => 'test_users',
                    'column_mappings' => [
                        ['column' => 'id'],
                        ['column' => 'name'],
                        ['column' => 'email'],
                    ],
                    'conditions' => [],
                ]
            ],
            'response_mapper' => [
                'mode' => 'last_action',
                'mapping' => []
            ],
            'rate_limit_enabled' => false,
            'cache_response_enabled' => false,
        ]);

        $response = $this->get('/api/v1/test-users');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                '*' => ['id', 'name', 'email']
            ]
        ]);
        $this->assertCount(2, $response->json('data'));
    }

    public function test_api_execution_insert_action()
    {
        $api = CbApiBuilder::create([
            'name' => 'Create User',
            'endpoint_path' => '/v1/test-users',
            'method' => 'POST',
            'status' => 'active',
            'payload_schema' => [
                ['key' => 'user_name', 'type' => 'string', 'required' => true],
                ['key' => 'user_email', 'type' => 'string', 'required' => true],
            ],
            'process_steps' => [
                [
                    'alias' => 'create_user',
                    'action_type' => 'insert',
                    'target_table' => 'test_users',
                    'column_mappings' => [
                        ['column' => 'name', 'source_ref' => 'payload.user_name'],
                        ['column' => 'email', 'source_ref' => 'payload.user_email'],
                    ],
                    'conditions' => [],
                ]
            ],
            'response_mapper' => [
                'mode' => 'last_action',
                'mapping' => []
            ],
            'rate_limit_enabled' => false,
        ]);

        $response = $this->postJson('/api/v1/test-users', [
            'user_name' => 'Alice',
            'user_email' => 'alice@example.com',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('test_users', [
            'name' => 'Alice',
            'email' => 'alice@example.com',
        ]);
    }

    public function test_api_execution_update_action()
    {
        $id = DB::table('test_users')->insertGetId([
            'name' => 'Old Name',
            'email' => 'old@example.com'
        ]);

        $api = CbApiBuilder::create([
            'name' => 'Update User',
            'endpoint_path' => '/v1/test-users/update',
            'method' => 'POST',
            'status' => 'active',
            'payload_schema' => [
                ['key' => 'id', 'type' => 'integer', 'required' => true],
                ['key' => 'new_name', 'type' => 'string', 'required' => true],
            ],
            'process_steps' => [
                [
                    'alias' => 'update_user',
                    'action_type' => 'update',
                    'target_table' => 'test_users',
                    'column_mappings' => [
                        ['column' => 'name', 'source_ref' => 'payload.new_name'],
                    ],
                    'conditions' => [
                        ['field' => 'id', 'operator' => '=', 'value_ref' => 'payload.id']
                    ],
                ]
            ],
            'response_mapper' => [
                'mode' => 'last_action',
                'mapping' => []
            ],
            'rate_limit_enabled' => false,
        ]);

        $response = $this->postJson('/api/v1/test-users/update', [
            'id' => $id,
            'new_name' => 'Updated Name',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('test_users', [
            'id' => $id,
            'name' => 'Updated Name',
            'email' => 'old@example.com' // Email shouldn't change
        ]);
    }

    public function test_api_execution_delete_action()
    {
        $id = DB::table('test_users')->insertGetId([
            'name' => 'To Delete',
            'email' => 'delete@example.com'
        ]);

        $api = CbApiBuilder::create([
            'name' => 'Delete User',
            'endpoint_path' => '/v1/test-users/delete',
            'method' => 'POST',
            'status' => 'active',
            'payload_schema' => [
                ['key' => 'id', 'type' => 'integer', 'required' => true],
            ],
            'process_steps' => [
                [
                    'alias' => 'delete_user',
                    'action_type' => 'delete',
                    'target_table' => 'test_users',
                    'conditions' => [
                        ['field' => 'id', 'operator' => '=', 'value_ref' => 'payload.id']
                    ],
                ]
            ],
            'response_mapper' => [
                'mode' => 'last_action',
                'mapping' => []
            ],
            'rate_limit_enabled' => false,
        ]);

        $response = $this->postJson('/api/v1/test-users/delete', [
            'id' => $id,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('test_users', [
            'id' => $id,
        ]);
    }

    public function test_api_execution_select_with_joins_and_conditions()
    {
        // Setup related table
        if (!Schema::hasTable('test_posts')) {
            Schema::create('test_posts', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id');
                $table->string('title');
                $table->timestamps();
            });
        }

        $userId = DB::table('test_users')->insertGetId(['name' => 'Blogger', 'email' => 'blogger@example.com']);
        DB::table('test_posts')->insert([
            ['user_id' => $userId, 'title' => 'Post 1'],
            ['user_id' => $userId, 'title' => 'Post 2'],
        ]);

        $api = CbApiBuilder::create([
            'name' => 'Get User Posts',
            'endpoint_path' => '/v1/user-posts',
            'method' => 'GET',
            'status' => 'active',
            'payload_schema' => [
                ['key' => 'email', 'type' => 'string', 'required' => true]
            ],
            'process_steps' => [
                [
                    'alias' => 'posts_data',
                    'action_type' => 'select',
                    'target_table' => 'test_users',
                    'joins' => [
                        [
                            'target_table' => 'test_posts',
                            'type' => 'inner',
                            'on_primary' => 'test_users.id',
                            'on_foreign' => 'test_posts.user_id'
                        ]
                    ],
                    'column_mappings' => [
                        ['column' => 'test_users.name'],
                        ['column' => 'test_posts.title'],
                    ],
                    'conditions' => [
                        ['field' => 'test_users.email', 'operator' => '=', 'value_ref' => 'payload.email']
                    ],
                ]
            ],
            'response_mapper' => [
                'mode' => 'last_action',
                'mapping' => []
            ],
            'rate_limit_enabled' => false,
        ]);

        $response = $this->get('/api/v1/user-posts?email=blogger@example.com');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertCount(2, $data);
        $this->assertEquals('Blogger', $data[0]['name']);
        $this->assertEquals('Post 1', $data[0]['title']);
    }

    public function test_api_execution_condition_action_throws_error()
    {
        $api = CbApiBuilder::create([
            'name' => 'Condition Test API',
            'endpoint_path' => '/v1/test-condition',
            'method' => 'POST',
            'status' => 'active',
            'payload_schema' => [
                ['key' => 'age', 'type' => 'integer', 'required' => true],
            ],
            'process_steps' => [
                [
                    'alias' => 'check_age',
                    'action_type' => 'condition',
                    'condition_source_ref' => 'payload.age',
                    'condition_operator' => '<',
                    'condition_value' => '18',
                    'true_actions' => [
                        [
                            'alias' => 'error_step',
                            'action_type' => 'throw_error',
                            'error_message' => 'Must be 18 or older',
                            'error_status_code' => 403,
                        ]
                    ]
                ]
            ],
            'response_mapper' => [
                'mode' => 'last_action',
                'mapping' => []
            ],
            'rate_limit_enabled' => false,
        ]);

        // If age < 18, it jumps to error_step and throws.
        $response = $this->postJson('/api/v1/test-condition', [
            'age' => 16,
        ]);

        $response->assertStatus(403);
        $response->assertJson([
            'success' => false,
            'error' => 'Must be 18 or older'
        ]);

        // If age >= 18, it passes check_age, hits success_step, and continues... wait, if it continues it hits error_step!
        // We need a way to skip error_step. Let's just put error_step first, and check_age jumps *over* it if age >= 18.
        // Actually, let's fix the test process_steps structure:
        
        \CrudBooster\Modules\ApiBuilder\Http\ApiBuilderController::invalidateAllApiCache();
        
        $api->update([
            'process_steps' => [
                [
                    'alias' => 'check_age',
                    'action_type' => 'condition',
                    'condition_source_ref' => 'payload.age',
                    'condition_operator' => '<',
                    'condition_value' => '18',
                    'true_actions' => [
                        [
                            'alias' => 'error_step',
                            'action_type' => 'throw_error',
                            'error_message' => 'Must be 18 or older',
                            'error_status_code' => 403,
                        ]
                    ]
                ],
                [
                    'alias' => 'success_step',
                    'action_type' => 'condition',
                    'condition_source_ref' => 'payload.age',
                    'condition_operator' => '=',
                    'condition_value' => '999', // Dummy to just act as a landing pad
                ]
            ]
        ]);

        // Test passing condition
        $responseSuccess = $this->postJson('/api/v1/test-condition', [
            'age' => 20,
        ]);

        $responseSuccess->assertStatus(200);
    }

    public function test_api_execution_select_with_column_comparison()
    {
        // Insert dummy data where name equals email (just for testing column comparison)
        DB::table('test_users')->insert([
            ['name' => 'match@example.com', 'email' => 'match@example.com'],
            ['name' => 'Different Name', 'email' => 'no-match@example.com'],
        ]);

        $api = CbApiBuilder::create([
            'name' => 'Column Comparison Test',
            'endpoint_path' => '/v1/column-comparison',
            'method' => 'GET',
            'status' => 'active',
            'payload_schema' => [],
            'process_steps' => [
                [
                    'alias' => 'users_data',
                    'action_type' => 'select',
                    'target_table' => 'test_users',
                    'column_mappings' => [
                        ['column' => 'id'],
                        ['column' => 'name'],
                        ['column' => 'email'],
                    ],
                    'conditions' => [
                        // Here we use test_users.name = test_users.email
                        // The executor should detect test_users.email as a column reference
                        ['field' => 'test_users.name', 'operator' => '=', 'value_ref' => 'test_users.email']
                    ],
                ]
            ],
            'response_mapper' => [
                'mode' => 'last_action',
                'mapping' => []
            ],
            'rate_limit_enabled' => false,
            'cache_response_enabled' => false,
        ]);

        $response = $this->get('/api/v1/column-comparison');

        $response->assertStatus(200);
        $data = $response->json('data');
        
        // Should only find the one where name == email
        $this->assertCount(1, $data);
        $this->assertEquals('match@example.com', $data[0]['name']);
        $this->assertEquals('match@example.com', $data[0]['email']);
    }

    public function test_api_execution_select_with_manual_value()
    {
        // Insert dummy data
        DB::table('test_users')->insert([
            ['name' => 'Target User', 'email' => 'target@example.com'],
            ['name' => 'Other User', 'email' => 'other@example.com'],
        ]);

        $api = CbApiBuilder::create([
            'name' => 'Manual Value Test',
            'endpoint_path' => '/v1/manual-value',
            'method' => 'GET',
            'status' => 'active',
            'payload_schema' => [],
            'process_steps' => [
                [
                    'alias' => 'users_data',
                    'action_type' => 'select',
                    'target_table' => 'test_users',
                    'column_mappings' => [
                        ['column' => 'id'],
                        ['column' => 'name'],
                    ],
                    'conditions' => [
                        [
                            'field' => 'email',
                            'operator' => '=',
                            'value_ref' => '__manual__',
                            'manual_value' => 'target@example.com'
                        ]
                    ],
                ]
            ],
            'response_mapper' => [
                'mode' => 'last_action',
                'mapping' => []
            ],
            'rate_limit_enabled' => false,
        ]);

        $response = $this->get('/api/v1/manual-value');

        $response->assertStatus(200);
        $data = $response->json('data');
        
        $this->assertCount(1, $data);
        $this->assertEquals('Target User', $data[0]['name']);
    }

    public function test_api_execution_select_with_raw_sql()
    {
        // Insert dummy data
        DB::table('test_users')->insert([
            ['name' => 'Alice', 'email' => 'alice@example.com'],
            ['name' => 'Bob', 'email' => 'bob@example.com'],
        ]);

        $api = CbApiBuilder::create([
            'name' => 'Raw SQL Test',
            'endpoint_path' => '/v1/raw-sql',
            'method' => 'GET',
            'status' => 'active',
            'payload_schema' => [
                ['key' => 'min_id', 'type' => 'integer']
            ],
            'process_steps' => [
                [
                    'alias' => 'users_data',
                    'action_type' => 'select',
                    'target_table' => 'test_users',
                    'column_mappings_raw' => true,
                    'column_mappings_raw_sql' => 'id, name as full_name',
                    'conditions_raw' => true,
                    'conditions_raw_sql' => 'id >= {payload.min_id} AND email LIKE \'%@example.com\'',
                ]
            ],
            'response_mapper' => [
                'mode' => 'last_action',
                'mapping' => []
            ],
            'rate_limit_enabled' => false,
        ]);

        $response = $this->get('/api/v1/raw-sql?min_id=1');

        $response->assertStatus(200);
        $data = $response->json('data');
        
        $this->assertCount(2, $data);
        $this->assertArrayHasKey('full_name', $data[0]);
    }

    public function test_api_token_authorization()
    {
        $tokenRaw = Str::random(40);
        $token = CbApiToken::create([
            'name' => 'Test Token',
            'status' => 'active',
            'token_hash' => hash('sha256', $tokenRaw),
            'token_encrypted' => $tokenRaw
        ]);
        
        $this->assertDatabaseHas('cb_api_tokens', [
            'name' => 'Test Token',
            'status' => 'active',
        ]);
        
        $this->assertNotNull($token->token_encrypted);
        $this->assertNotNull($token->token_hash);
    }

    public function test_api_updates_token_last_used_at_when_request_is_authorized()
    {
        CbApiBuilder::create([
            'name' => 'Auth Protected API',
            'endpoint_path' => '/v1/auth-last-used',
            'method' => 'GET',
            'status' => 'active',
            'payload_schema' => [],
            'process_steps' => [
                [
                    'alias' => 'users_data',
                    'action_type' => 'select',
                    'target_table' => 'test_users',
                    'column_mappings' => [
                        ['column' => 'id'],
                    ],
                    'conditions' => [],
                ],
            ],
            'response_mapper' => [
                'mode' => 'last_action',
                'mapping' => [],
            ],
            'rate_limit_enabled' => false,
        ]);

        $tokenRaw = 'cb_test_last_used_token';
        $token = CbApiToken::create([
            'name' => 'Auth Token',
            'status' => 'active',
            'scope_endpoint' => '/v1/*',
            'token_hash' => hash('sha256', $tokenRaw),
            'token_encrypted' => $tokenRaw,
            'auth_method' => 'api_key',
        ]);

        $this->assertNull($token->fresh()->last_used_at);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $tokenRaw,
        ])->get('/api/v1/auth-last-used');

        $response->assertStatus(200);
        $this->assertNotNull($token->fresh()->last_used_at);
    }

    public function test_api_returns_unauthorized_when_active_token_exists_but_header_is_missing()
    {
        CbApiBuilder::create([
            'name' => 'Auth Required API',
            'endpoint_path' => '/v1/auth-required',
            'method' => 'GET',
            'status' => 'active',
            'payload_schema' => [],
            'process_steps' => [],
            'response_mapper' => [
                'mode' => 'last_action',
                'mapping' => [],
            ],
            'rate_limit_enabled' => false,
        ]);

        CbApiToken::create([
            'name' => 'Auth Token',
            'status' => 'active',
            'scope_endpoint' => '/v1/*',
            'token_hash' => hash('sha256', 'cb_required_token'),
            'token_encrypted' => 'cb_required_token',
            'auth_method' => 'api_key',
        ]);

        $response = $this->withoutHeader('Authorization')->get('/api/v1/auth-required');

        $response->assertStatus(401);
        $response->assertJson([
            'success' => false,
            'error' => 'Unauthorized',
        ]);
    }

    public function test_api_enforces_http_method_and_returns_405(): void
    {
        CbApiBuilder::create([
            'name' => 'Method Enforced API',
            'endpoint_path' => '/v1/method-enforced',
            'method' => 'GET',
            'status' => 'active',
            'payload_schema' => [],
            'process_steps' => [],
            'response_mapper' => ['mode' => 'last_action', 'mapping' => []],
            'rate_limit_enabled' => false,
        ]);

        $response = $this->postJson('/api/v1/method-enforced', []);
        $response->assertStatus(405);
        $response->assertJson([
            'success' => false,
            'error' => 'Method Not Allowed',
        ]);
        $this->assertContains('GET', $response->json('allowed_methods', []));
    }

    public function test_raw_sql_guard_blocks_dangerous_statement(): void
    {
        CbApiBuilder::create([
            'name' => 'Raw SQL Guard API',
            'endpoint_path' => '/v1/raw-sql-guard',
            'method' => 'GET',
            'status' => 'active',
            'payload_schema' => [],
            'process_steps' => [
                [
                    'alias' => 'users_data',
                    'action_type' => 'select',
                    'target_table' => 'test_users',
                    'column_mappings_raw' => true,
                    'column_mappings_raw_sql' => 'id, name',
                    'conditions_raw' => true,
                    'conditions_raw_sql' => '1=1; DROP TABLE test_users',
                ],
            ],
            'response_mapper' => ['mode' => 'last_action', 'mapping' => []],
            'rate_limit_enabled' => false,
        ]);

        $response = $this->get('/api/v1/raw-sql-guard');
        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
        ]);
    }

    public function test_invalidate_all_api_cache_does_not_flush_unrelated_cache_keys(): void
    {
        cache()->put('cb_unrelated_key', 'safe', 600);

        CbApiBuilder::create([
            'name' => 'Cache API',
            'endpoint_path' => '/v1/cache-api',
            'method' => 'GET',
            'status' => 'active',
            'payload_schema' => [],
            'process_steps' => [],
            'response_mapper' => ['mode' => 'last_action', 'mapping' => []],
            'rate_limit_enabled' => false,
        ]);

        \CrudBooster\Modules\ApiBuilder\Http\ApiBuilderController::invalidateAllApiCache();
        $this->assertSame('safe', cache()->get('cb_unrelated_key'));
    }

    public function test_internal_error_is_masked_when_debug_false(): void
    {
        config(['app.debug' => false]);

        CbApiBuilder::create([
            'name' => 'Masked Error API',
            'endpoint_path' => '/v1/masked-error',
            'method' => 'POST',
            'status' => 'active',
            'payload_schema' => [
                ['key' => 'email', 'type' => 'string', 'required' => true],
            ],
            'process_steps' => [
                [
                    'alias' => 'create_user',
                    'action_type' => 'insert',
                    'target_table' => 'test_users',
                    'column_mappings' => [
                        ['column' => 'email', 'source_ref' => 'payload.email'],
                    ],
                ],
            ],
            'response_mapper' => ['mode' => 'last_action', 'mapping' => []],
            'rate_limit_enabled' => false,
        ]);

        $response = $this->postJson('/api/v1/masked-error', [
            'email' => 'masked@example.com',
        ]);

        $response->assertStatus(500);
        $response->assertJson([
            'success' => false,
            'error' => 'Internal server error',
        ]);
    }

    public function test_internal_error_message_is_visible_when_debug_true(): void
    {
        config(['app.debug' => true]);

        CbApiBuilder::create([
            'name' => 'Debug Error API',
            'endpoint_path' => '/v1/debug-error',
            'method' => 'POST',
            'status' => 'active',
            'payload_schema' => [
                ['key' => 'email', 'type' => 'string', 'required' => true],
            ],
            'process_steps' => [
                [
                    'alias' => 'create_user',
                    'action_type' => 'insert',
                    'target_table' => 'test_users',
                    'column_mappings' => [
                        ['column' => 'email', 'source_ref' => 'payload.email'],
                    ],
                ],
            ],
            'response_mapper' => ['mode' => 'last_action', 'mapping' => []],
            'rate_limit_enabled' => false,
        ]);

        $response = $this->postJson('/api/v1/debug-error', [
            'email' => 'debug@example.com',
        ]);

        $response->assertStatus(500);
        $this->assertStringContainsString('SQLSTATE', (string) $response->json('error'));
    }
}
