<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMachineriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('machineries', function (Blueprint $table) {
            $table->id();

            $table->string('machine_name');

            $table->string('machine_number')->nullable();

            $table->text('machine_description')->nullable();

            $table->string('machine_type')->nullable()->default('vehicle');

            $table->string('created_by');

            $table->string('updated_by')->nullable();

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
        Schema::dropIfExists('machineries');
    }
}
