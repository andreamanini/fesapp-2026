<?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateBuildingSiteNotesTable extends Migration
    {
        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create('building_site_notes', function (Blueprint $table) {
                $table->id();

                $table->string('note_title')->nullable();

                $table->text('note_body');

                $table->date('note_date')->nullable();

                $table->bigInteger('building_site_id')->unsigned();

                $table->timestamps();

                $table->foreign('building_site_id')
                    ->references('id')
                    ->on('building_sites');
            });
        }

        /**
         * Reverse the migrations.
         *
         * @return void
         */
        public function down()
        {
            Schema::dropIfExists('building_site_notes');
        }
    }
