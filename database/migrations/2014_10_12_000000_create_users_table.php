<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
public function up()
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('mobile')->nullable();
        $table->string('country')->nullable();
        $table->string('state')->nullable();
        $table->json('skills')->nullable();
        $table->enum('role', ['admin','seller'])->default('seller');
    });
}



    /**
     * Reverse the migrations.
     *
     * @return void
     */
public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['mobile','country','state','skills','role']);
    });
}
}
