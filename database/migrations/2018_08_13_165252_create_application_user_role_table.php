<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApplicationUserRoleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('application_user_role', function (Blueprint $table) {
            $table->uuid('application_id');
            $table->uuid('user_id');
            $table->uuid('role_id');
            $table->primary(['application_id', 'user_id', 'role_id']);
            $table->boolean('default')->default(false);
            $table->timestamps();

            $table->foreign('application_id')->references('id')->on('applications');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('role_id')->references('id')->on('roles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('application_user_role');
    }
}
