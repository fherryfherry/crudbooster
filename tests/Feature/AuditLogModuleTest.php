<?php

namespace CrudBooster\Tests\Feature;

use CrudBooster\Events\EventDataDeleted;
use CrudBooster\Events\EventDataDeleting;
use CrudBooster\Events\EventFormSaved;
use CrudBooster\Events\EventFormSaving;
use CrudBooster\Modules\AuditLog\Livewire\AuditLogList;
use CrudBooster\Modules\AuditLog\Models\CbAuditLog;
use CrudBooster\Modules\Auth\Events\LoginAttemptFailed;
use CrudBooster\Modules\Auth\Events\LoginAttemptSuccess;
use CrudBooster\Modules\Auth\Events\LogoutSuccess;
use CrudBooster\Tests\BaseTestCase;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Auth\GenericUser;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;

class AuditLogModuleTest extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('cb.audit_log.enabled', true);
        $this->artisan('migrate');

        if (!Schema::hasTable('test_audit_entities')) {
            Schema::create('test_audit_entities', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->nullable();
                $table->string('password')->nullable();
                $table->timestamps();
            });
        }

        Route::middleware(['web', 'cb.audit'])->post('/audit-test', function () {
            return response()->json(['ok' => true]);
        });

        Route::middleware(['web', 'cb.audit'])->post('/livewire/mock', function () {
            return response()->json(['ok' => true]);
        });

        Route::middleware(['web', 'cb.audit'])->post('/audit-error', function () {
            throw new \RuntimeException('audit test error');
        });

    }

    public function test_capture_create_update_delete_logs_with_before_after_diff(): void
    {
        event(new EventFormSaving(TestAuditEntity::class, [
            'name' => 'Alpha',
            'email' => 'alpha@example.com',
            'password' => 'secret-alpha',
        ], null));

        $entity = TestAuditEntity::query()->create([
            'name' => 'Alpha',
            'email' => 'alpha@example.com',
            'password' => 'secret-alpha',
        ]);

        event(new EventFormSaved(TestAuditEntity::class, [
            'name' => 'Alpha',
            'email' => 'alpha@example.com',
            'password' => 'secret-alpha',
        ], $entity->id));

        event(new EventFormSaving(TestAuditEntity::class, [
            'name' => 'Beta',
            'password' => 'secret-beta',
        ], $entity->id));

        $entity->update([
            'name' => 'Beta',
            'password' => 'secret-beta',
        ]);

        event(new EventFormSaved(TestAuditEntity::class, [
            'name' => 'Beta',
            'password' => 'secret-beta',
        ], $entity->id));

        event(new EventDataDeleting(TestAuditEntity::class, $entity->fresh(), $entity->id));
        $entity->delete();
        event(new EventDataDeleted(TestAuditEntity::class, ['id' => $entity->id], $entity->id));

        $createLog = CbAuditLog::query()->where('action', 'create')->latest('created_at')->first();
        $this->assertNotNull($createLog);
        $this->assertStringNotContainsString('secret-alpha', json_encode($createLog->after_data));
        $this->assertSame('[MASKED]', $createLog->request_payload['password'] ?? null);

        $updateLog = CbAuditLog::query()->where('action', 'update')->latest('created_at')->first();
        $this->assertNotNull($updateLog);
        $this->assertContains('name', $updateLog->changed_fields ?? []);
        $this->assertSame('Alpha', $updateLog->before_data['name'] ?? null);
        $this->assertSame('Beta', $updateLog->after_data['name'] ?? null);
        $this->assertSame('[MASKED]', $updateLog->after_data['password'] ?? null);

        $deleteLog = CbAuditLog::query()->where('action', 'delete')->latest('created_at')->first();
        $this->assertNotNull($deleteLog);
        $this->assertArrayHasKey('name', $deleteLog->before_data ?? []);
        $this->assertSame([], $deleteLog->after_data ?? []);
    }

    public function test_noop_update_does_not_create_empty_duplicate_update_log(): void
    {
        $entity = TestAuditEntity::query()->create([
            'name' => 'Alpha',
            'email' => 'alpha@example.com',
            'password' => 'secret-alpha',
        ]);

        event(new EventFormSaving(TestAuditEntity::class, [
            'name' => 'Beta',
        ], $entity->id));

        $entity->update([
            'name' => 'Beta',
        ]);

        event(new EventFormSaved(TestAuditEntity::class, [
            'name' => 'Beta',
        ], $entity->id));

        // Simulate repeated lifecycle trigger with same values (no actual change).
        event(new EventFormSaving(TestAuditEntity::class, [
            'name' => 'Beta',
        ], $entity->id));
        event(new EventFormSaved(TestAuditEntity::class, [
            'name' => 'Beta',
        ], $entity->id));

        $updateLogs = CbAuditLog::query()
            ->where('action', 'update')
            ->where('entity_type', TestAuditEntity::class)
            ->where('entity_id', (string) $entity->id)
            ->get();

        $this->assertCount(1, $updateLogs);
        $this->assertSame(['name'], $updateLogs->first()->changed_fields ?? []);
    }

    public function test_capture_auth_events_for_login_failed_and_logout(): void
    {
        $user = (object) [
            'id' => 'user-audit-1',
            'name' => 'Audit User',
            'email' => 'audit-user@example.com',
        ];

        event(new LoginAttemptSuccess($user));
        event(new LoginAttemptFailed('bad-user@example.com'));
        event(new LogoutSuccess($user));

        $this->assertDatabaseHas('cb_audit_logs', [
            'action' => 'login',
            'outcome' => 'success',
            'user_email' => 'audit-user@example.com',
        ]);

        $this->assertDatabaseHas('cb_audit_logs', [
            'action' => 'login',
            'outcome' => 'failed',
            'user_email' => 'bad-user@example.com',
        ]);

        $this->assertDatabaseHas('cb_audit_logs', [
            'action' => 'logout',
            'outcome' => 'success',
            'user_email' => 'audit-user@example.com',
        ]);
    }

    public function test_livewire_event_uses_original_page_path_instead_of_livewire_update_path(): void
    {
        $snapshot = json_encode([
            'memo' => [
                'path' => 'cms/auth/login',
            ],
        ]);

        $request = Request::create('/livewire/update', 'POST', [
            'components' => [
                [
                    'snapshot' => $snapshot,
                ],
            ],
        ], [], [], [
            'HTTP_REFERER' => 'http://localhost:8000/cms/auth/login',
        ]);

        $originalRequest = app('request');
        app()->instance('request', $request);

        $user = (object) [
            'id' => 'livewire-user-1',
            'name' => 'Livewire User',
            'email' => 'livewire-user@example.com',
        ];
        event(new LoginAttemptSuccess($user));
        app()->instance('request', $originalRequest);

        $loginLog = CbAuditLog::query()
            ->where('action', 'login')
            ->where('user_email', 'livewire-user@example.com')
            ->latest('created_at')
            ->first();

        $this->assertNotNull($loginLog);
        $this->assertSame('/cms/auth/login', $loginLog->path);
    }

    public function test_capture_request_metadata_and_mask_payload(): void
    {
        $this->postJson('/audit-test', [
            'email' => 'test@example.com',
            'password' => 'super-secret',
            'meta' => [
                'token' => 'abc-123',
                'note' => 'hello',
            ],
        ])->assertOk();

        $requestLog = CbAuditLog::query()
            ->where('action', 'request')
            ->where('path', '/audit-test')
            ->latest('created_at')
            ->first();

        $this->assertNotNull($requestLog);
        $this->assertSame('[MASKED]', $requestLog->request_payload['password'] ?? null);
        $this->assertSame('[MASKED]', $requestLog->request_payload['meta']['token'] ?? null);
        $this->assertSame('hello', $requestLog->request_payload['meta']['note'] ?? null);
        $this->assertNotNull($requestLog->request_id);

        $before = CbAuditLog::query()->where('path', '/livewire/mock')->count();
        $this->postJson('/livewire/mock', ['value' => 1])->assertOk();
        $after = CbAuditLog::query()->where('path', '/livewire/mock')->count();
        $this->assertSame($before, $after);

        $this->withExceptionHandling()
            ->postJson('/audit-error', ['token' => 'error-secret'])
            ->assertStatus(500);

        $errorLog = CbAuditLog::query()
            ->where('action', 'request')
            ->where('path', '/audit-error')
            ->latest('created_at')
            ->first();

        $this->assertNotNull($errorLog);
        $this->assertSame('failed', $errorLog->outcome);
        $this->assertSame('[MASKED]', $errorLog->request_payload['token'] ?? null);
    }

    public function test_can_filter_logs_from_livewire_component(): void
    {
        Gate::shouldReceive('allows')->with('read', 'audit-log')->andReturn(true);

        $user = new GenericUser([
            'id' => 'user-super-admin',
            'name' => 'Super Admin',
            'email' => 'super-admin@example.com',
            'password' => 'irrelevant',
        ]);
        $this->actingAs($user);

        CbAuditLog::query()->create([
            'user_id' => (string) $user->id,
            'user_email' => $user->email,
            'user_name' => $user->name,
            'module_key' => 'auth',
            'action' => 'login',
            'outcome' => 'success',
            'path' => '/cms/auth/login',
            'created_at' => now(),
        ]);

        CbAuditLog::query()->create([
            'user_id' => (string) $user->id,
            'user_email' => $user->email,
            'user_name' => $user->name,
            'module_key' => 'user',
            'action' => 'update',
            'outcome' => 'success',
            'path' => '/cms/user/1/edit',
            'created_at' => now(),
        ]);

        Livewire::test(AuditLogList::class)
            ->set('filterAction', 'login')
            ->assertSee('/cms/auth/login')
            ->assertDontSee('/cms/user/1/edit')
            ->set('filterAction', null)
            ->set('filterModule', 'user')
            ->assertSee('/cms/user/1/edit')
            ->assertDontSee('/cms/auth/login');
    }

    public function test_prune_command_respects_retention_days(): void
    {
        $old = CbAuditLog::query()->create([
            'action' => 'request',
            'outcome' => 'success',
            'path' => '/old',
            'created_at' => now()->subDays(120),
        ]);

        $new = CbAuditLog::query()->create([
            'action' => 'request',
            'outcome' => 'success',
            'path' => '/new',
            'created_at' => now()->subDays(2),
        ]);

        $this->artisan('cb:audit-log:prune --days=90')
            ->assertSuccessful();

        $this->assertDatabaseMissing('cb_audit_logs', ['id' => $old->id]);
        $this->assertDatabaseHas('cb_audit_logs', ['id' => $new->id]);
    }

    public function test_can_export_filtered_logs_csv(): void
    {
        Gate::shouldReceive('allows')->with('read', 'audit-log')->andReturn(true);

        $user = new GenericUser([
            'id' => 'user-export',
            'name' => 'Exporter',
            'email' => 'exporter@example.com',
            'password' => 'irrelevant',
        ]);
        $this->actingAs($user);

        CbAuditLog::query()->create([
            'user_id' => (string) $user->id,
            'user_email' => $user->email,
            'user_name' => $user->name,
            'module_key' => 'auth',
            'action' => 'login',
            'outcome' => 'success',
            'path' => '/cms/auth/login',
            'created_at' => now(),
        ]);

        Livewire::test(AuditLogList::class)
            ->set('filterAction', 'login')
            ->call('exportCsv')
            ->assertDispatched('cb-download-csv');
    }
}

class TestAuditEntity extends Model
{
    protected $table = 'test_audit_entities';

    protected $guarded = [];
}
