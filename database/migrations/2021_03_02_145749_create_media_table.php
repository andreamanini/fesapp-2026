<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();

            $table->string('media_name');

            $table->string('extension')->nullable();

            $table->string('directory')->nullable();

            $table->smallInteger('ordering')->default(0);

            $table->boolean('primary')->default(0);

            $table->text('description')->nullable();

            $table->string('media_type')->default('image'); // media types: image, file

            $table->text('notes')->nullable();

            $table->bigInteger('mediable_id');

            $table->string('mediable_type');

            $table->bigInteger('note_id')->nullable();

            $table->string('coordinates')->nullable();

            $table->boolean('job_proof')->nullable()->default(0);

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
        Schema::dropIfExists('media');
    }
}
