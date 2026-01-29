<?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class BuildingSiteMachineryTable extends Migration
    {
        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create('building_site_machinery', function (Blueprint $table) {
                $table->id();

                $table->bigInteger('building_site_id')->unsigned();

                $table->bigInteger('machinery_id')->unsigned();

                $table->string('created_by');

                $table->timestamps();

                $table->foreign('building_site_id')
                    ->references('id')
                    ->on('building_sites');

                $table->foreign('machinery_id')
                    ->references('id')
                    ->on('machineries');
            });
        }

        /**
         * Reverse the migrations.
         *
         * @return void
         */
        public function down()
        {
            Schema::dropIfExists('building_site_machinery');
        }
    }
