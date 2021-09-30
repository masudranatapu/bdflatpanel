<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_users', function (Blueprint $table) {
            $table->bigIncrements('id');$table->string('first_name');
            $table->string('last_name');
            $table->string('designation')->nullable();
            $table->bigInteger('auth_id')->unsigned();
            $table->foreign('auth_id')->references('id')->on('auths');
            $table->string('profile_pic', 255)->nullable();
            $table->string('profile_pic_url', 255)->nullable();
            $table->string('pic_mime_type', 50)->nullable();
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
        Schema::dropIfExists('admin_users');
    }
}
