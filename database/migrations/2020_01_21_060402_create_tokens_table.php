<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tokens', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('auth_id')->unsigned();
            $table->foreign('auth_id')->references('id')->on('auths');
            $table->tinyInteger('user_type')->default(0);
            $table->string('token');
            $table->string('client');
            $table->tinyInteger('is_expire')->default(0)->comment('0 = Alive, 0 = Expire');
            $table->dateTime('started_at');
            $table->dateTime('expire_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tokens');
    }
}
