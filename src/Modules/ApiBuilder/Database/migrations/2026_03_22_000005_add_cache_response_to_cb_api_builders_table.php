<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cb_api_builders', function (Blueprint $table) {
            $table->boolean('cache_response_enabled')->default(false)->after('response_mapper');
        });
    }

    public function down(): void
    {
        Schema::table('cb_api_builders', function (Blueprint $table) {
            $table->dropColumn('cache_response_enabled');
        });
    }
};
