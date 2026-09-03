<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cb_api_builders', function (Blueprint $table) {
            $table->text('description')->nullable()->after('endpoint_path');
            $table->boolean('rate_limit_enabled')->default(true)->after('status');
            $table->unsignedInteger('rate_limit_rpm')->default(1000)->after('rate_limit_enabled');
            $table->json('payload_schema')->nullable()->after('rate_limit_rpm');
            $table->json('process_steps')->nullable()->after('payload_schema');
            $table->json('response_mapper')->nullable()->after('process_steps');
        });
    }

    public function down(): void
    {
        Schema::table('cb_api_builders', function (Blueprint $table) {
            $table->dropColumn([
                'description',
                'rate_limit_enabled',
                'rate_limit_rpm',
                'payload_schema',
                'process_steps',
                'response_mapper',
            ]);
        });
    }
};
