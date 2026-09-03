<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cb_api_builders', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->string('name');
            $table->string('endpoint_path')->unique();
            $table->string('method', 10)->default('GET');
            $table->enum('status', ['active', 'testing', 'disabled'])->default('testing');
            $table->unsignedInteger('avg_response_ms')->nullable();
            $table->decimal('error_rate_percent', 8, 4)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cb_api_builders');
    }
};
