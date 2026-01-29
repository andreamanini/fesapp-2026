<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('works', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('user_id')->unsigned();
            
            $table->bigInteger('building_site_id')->unsigned();
            
            $table->string('truck_no')->nullable();

            $table->date('date')->nullable();

            $table->time('time')->nullable();
            
            $table->text('work_description')->nullable();

            $table->string('created_by');

            $table->string('updated_by')->nullable();

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
        Schema::dropIfExists('works');
    }
}
