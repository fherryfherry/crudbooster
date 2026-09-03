<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cb_roles', function (Blueprint $table) {
            $table->char('id',36)->primary();
            $table->string('name');
            $table->json('permissions');
            $table->timestamps();
        });

        Schema::create('cb_role_users', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->char('role_id', 36)->constrained('cb_roles')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
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
        Schema::dropIfExists('cb_roles');
        Schema::dropIfExists('cb_role_users');
    }
};
