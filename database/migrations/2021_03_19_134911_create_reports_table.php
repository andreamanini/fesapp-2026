<?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateReportsTable extends Migration
    {
        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create('reports', function (Blueprint $table) {
                $table->id();

                $table->string('truck_no')->nullable();

                $table->string('truck_driver_name')->nullable();

                $table->string('meals_no')->nullable();

                $table->dateTime('time_start')->nullable();

                $table->dateTime('time_end')->nullable();

                $table->float('total_working_hours')->nullable();

                $table->float('total_break_time')->nullable()->default(0);

                $table->string('break_from_to')->nullable();

                $table->integer('travel_time')->nullable()->default(0);

                $table->text('employees')->nullable();

                $table->text('job_details')->nullable();

                $table->text('equipment')->nullable();

                $table->text('work_description')->nullable();

                $table->text('extra_work_description')->nullable();

                $table->string('time_lost')->nullable();

                $table->text('materials')->nullable();

                $table->string('extra_expenses')->nullable();

                $table->integer('tot_petrol_used')->default(0);

                $table->string('location_lat')->nullable();

                $table->string('location_lng')->nullable();

                $table->string('report_type')->default('daily');    // daily / customer

                $table->string('report_view')->default('employee'); // employee / internal

                $table->bigInteger('user_id')->unsigned();

                $table->bigInteger('building_site_id')->unsigned();

                $table->bigInteger('signed_off_by_report_id')->unsigned()->nullable();

                $table->string('updated_by')->nullable();

                $table->timestamps();

                $table->softDeletes();

                $table->foreign('user_id')
                    ->references('id')
                    ->on('users');

                $table->foreign('building_site_id')
                    ->references('id')
                    ->on('building_sites');

                $table->foreign('signed_off_by_report_id')
                    ->references('id')
                    ->on('customer_reports');
            });
        }

        /**
         * Reverse the migrations.
         *
         * @return void
         */
        public function down()
        {
            Schema::dropIfExists('reports');
        }
    }
