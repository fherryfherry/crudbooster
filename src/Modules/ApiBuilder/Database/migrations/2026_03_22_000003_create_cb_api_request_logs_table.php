<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cb_api_request_logs', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->string('endpoint');
            $table->string('method', 10);
            $table->unsignedSmallInteger('status_code');
            $table->string('status_text')->nullable();
            $table->unsignedInteger('latency_ms')->default(0);
            $table->boolean('is_error')->default(false);
            $table->timestamps();
            $table->index(['created_at', 'is_error']);
            $table->index(['endpoint', 'is_error']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cb_api_request_logs');
    }
};
