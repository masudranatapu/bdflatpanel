<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuthsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auths', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('username', 50)->unique()->nullable();
            $table->string('email', 150)->unique()->nullable();
            $table->string('mobile_no', 14)->unique();
            $table->string('password');
            $table->string('salt', 20)->nullable();
            $table->bigInteger('model_id')->unsigned()->comment('1 = Admin');
            $table->tinyInteger('gender')->default(1);
            $table->date('dob')->nullable();
            $table->bigInteger('facebook_id')->nullable();
            $table->bigInteger('google_id')->nullable();
            $table->string('activation_code')->nullable();
            $table->dateTime('activation_code_expire')->nullable();
            $table->tinyInteger('is_first_login')->default(1);
            $table->tinyInteger('user_type')->default(0)->comment('0 = Admin');
            $table->tinyInteger('can_login')->default(1)->comment('1 = Can login, 0 = Can not login');
            $table->tinyInteger('status')->default(1)->comment('1 = Active, 0 = Inactive');
            $table->integer('created_by')->unsigned()->default(0);
            $table->integer('updated_by')->unsigned()->default(0);
            $table->rememberToken();
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
        Schema::dropIfExists('auths');
    }
}
