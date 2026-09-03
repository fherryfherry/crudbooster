<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cb_api_tokens', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->string('name');
            $table->string('scope_endpoint')->default('/v1/*');
            $table->enum('auth_method', ['bearer_token', 'api_key', 'oauth2'])->default('api_key');
            $table->enum('status', ['active', 'expired', 'disabled'])->default('active');
            $table->string('token_prefix', 20)->nullable();
            $table->string('token_hash');
            $table->unsignedInteger('failed_attempt_24h')->default(0);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cb_api_tokens');
    }
};
