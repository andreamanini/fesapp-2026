<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notes', function (Blueprint $table) {
            $table->id();

            $table->text('body');

            $table->text('geotagging')->nullable();

            $table->bigInteger('building_site_id')->unsigned();

            $table->bigInteger('user_id')->unsigned();

            $table->string('created_by');

            $table->string('updated_by')->nullable();

            $table->timestamps();

            $table->softDeletes();

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
        Schema::dropIfExists('notes');
    }
}
