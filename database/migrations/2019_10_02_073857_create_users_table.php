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
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('alt_mobile_no', 14)->nullable();
            $table->string('designation')->nullable();
            $table->bigInteger('auth_id')->unsigned();
            $table->foreign('auth_id')->references('id')->on('auths');
            $table->string('profile_pic', 255)->nullable();
            $table->string('profile_pic_url', 255)->nullable();
            $table->string('pic_mime_type', 50)->nullable();
            $table->tinyInteger('user_type')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
