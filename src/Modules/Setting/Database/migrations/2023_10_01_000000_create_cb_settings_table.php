<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cb_settings', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->string('name')->unique(); // represent the name of module
            $table->json('general_setting')->nullable();
            $table->json('production_setting')->nullable();
            $table->json('staging_setting')->nullable();
            $table->json('development_setting')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cb_settings');
    }
};
