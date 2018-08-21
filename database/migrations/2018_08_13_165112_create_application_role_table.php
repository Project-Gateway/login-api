<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApplicationRoleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('application_role', function (Blueprint $table) {
            $table->uuid('application_id');
            $table->uuid('role_id');
            $table->boolean('default')->default(false);
            $table->primary(['application_id', 'role_id']);
            $table->timestamps();

            $table->foreign('application_id')->references('id')->on('applications');
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
        Schema::dropIfExists('application_role');
    }
}
