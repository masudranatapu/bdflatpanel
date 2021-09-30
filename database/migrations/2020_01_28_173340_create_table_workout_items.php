<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableWorkoutItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workout_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('workout_name');
            $table->string('photo')->defalult('');
            $table->string('photo_url')->defalult('');
            $table->bigInteger('body_parts_id')->unsigned();
            $table->foreign('body_parts_id')->references('id')->on('workout_body_parts');
            $table->tinyInteger('status')->default(1);
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
        Schema::drop('workout_items');
    }
}
