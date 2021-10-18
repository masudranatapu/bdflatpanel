<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVerifyMobileNoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('verify_mobile_no', function (Blueprint $table) {
            $table->increments('id');
            $table->string('mobile_no', 14);
            $table->string('code');
            $table->tinyInteger('user_type')->default(0)->comment('0 = Admin, 1 = User');
            $table->tinyInteger('purpose')->default(0)->comment('0 = Verify-mobile,  1 = Reset-password 2= Forgot-password');
            $table->tinyInteger('status')->default(1)->comment('1 = Last-one, 0 = Used, 2 = Unused');
            $table->dateTime('expire_at');
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
        Schema::drop('verify_mobile_no');
    }
}
