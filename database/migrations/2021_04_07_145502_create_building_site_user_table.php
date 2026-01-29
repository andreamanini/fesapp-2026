<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBuildingSiteUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('building_site_user', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('building_site_id')->unsigned();

            $table->bigInteger('user_id')->unsigned();

            $table->timestamps();

            $table->foreign('building_site_id')
                ->references('id')
                ->on('building_sites');

            $table->foreign('user_id')
                ->references('id')
                ->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('building_site_user');
    }
}
