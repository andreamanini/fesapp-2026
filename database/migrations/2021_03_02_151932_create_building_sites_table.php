<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBuildingSitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('building_sites', function (Blueprint $table) {
            $table->id();

            $table->string('site_name');

            $table->text('address')->nullable();

            $table->text('notes')->nullable();

            $table->text('customer_notes')->nullable();

            $table->text('materials')->nullable();

            $table->text('site_type')->nullable();

            $table->dateTime('closing_date')->nullable();

            $table->string('closed_by')->nullable();

            $table->string('status')->default('open');

            $table->string('quote_number')->nullable();

            $table->date('quote_date')->nullable();

            $table->string('order_number')->nullable();

            $table->string('created_by');

            $table->string('updated_by')->nullable();

            $table->bigInteger('manager_id')->unsigned()->nullable();

            $table->bigInteger('customer_id')->unsigned();

            $table->timestamps();

            $table->softDeletes();

            $table->foreign('customer_id')
                ->references('id')
                ->on('customers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('building_sites');
    }
}
