<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UsersRefreshTokens extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_refresh_tokens', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('users_refresh_tokens_id');
            $table->unsignedInteger('users_id');
            $table->string('ip')->default('');
            $table->string('user_agent')->default('');
            $table->string('device_token')->unique();
            $table->text('token');
            $table->integer('created_at');
            $table->integer('updated_at');
            $table->foreign('users_id')->references('users_id')->on('users')->onDelete('cascade')->onTruncate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_refresh_tokens');
    }
}
