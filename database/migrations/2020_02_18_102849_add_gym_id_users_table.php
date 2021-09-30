<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGymIdUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {

            if (!Schema::hasColumn('users', 'gym_id')) {
                $table->unsignedBigInteger('gym_id')->nullable(true)->after('auth_id');
                $table->foreign('gym_id')->references('id')->on('gyms');
            }

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('users'))
        {
            Schema::table('users', function (Blueprint $table) {

                if (Schema::hasColumn('users', 'gym_id')) {
                    $table->dropForeign('users_gym_id_foreign');
                    $table->dropColumn('gym_id');
                }

            });
        }
    }
}
