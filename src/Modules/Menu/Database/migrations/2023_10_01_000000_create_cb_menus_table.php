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
        Schema::create('cb_menus', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->string('tag')->nullable();
            $table->string('name');
            $table->text('icon')->nullable();
            $table->string('menu_type')->nullable(); // MODULE, URL
            $table->string('menu_value')->nullable();
            $table->char('parent_id', 36)->nullable();
            $table->boolean('is_dashboard')->default(false);
            $table->integer('menu_order')->default(0);
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
        Schema::dropIfExists('cb_menus');
    }
};
