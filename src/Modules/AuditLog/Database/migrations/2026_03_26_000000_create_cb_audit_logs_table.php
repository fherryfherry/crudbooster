<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cb_audit_logs', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->string('user_id', 64)->nullable();
            $table->string('user_email')->nullable();
            $table->string('user_name')->nullable();
            $table->string('module_key', 100)->nullable();
            $table->string('entity_type')->nullable();
            $table->string('entity_id', 100)->nullable();
            $table->string('action', 50);
            $table->string('http_method', 10)->nullable();
            $table->string('path')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->char('request_id', 36)->nullable();
            $table->json('before_data')->nullable();
            $table->json('after_data')->nullable();
            $table->json('changed_fields')->nullable();
            $table->json('request_payload')->nullable();
            $table->string('outcome', 20)->default('success');
            $table->timestamp('created_at')->useCurrent();

            $table->index('created_at');
            $table->index(['user_id', 'created_at']);
            $table->index(['module_key', 'action', 'created_at']);
            $table->index(['entity_type', 'entity_id']);
            $table->index('request_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cb_audit_logs');
    }
};

