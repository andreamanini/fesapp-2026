<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_reports', function (Blueprint $table) {
            $table->id();

            $table->string('company_name');

            $table->string('company_address');

            $table->string('company_city');

            $table->string('billing_to');

            $table->string('billing_to_company')->nullable();

            $table->string('job_type');

            $table->text('work_description');

            $table->string('signature_name');

            $table->string('signature_company_name');

            $table->string('employee_name');

            $table->text('additional_notes')->nullable();

            $table->string('customer_signature');

            $table->string('customer_pdf');

            $table->string('lat')->nullable();

            $table->string('lng')->nullable();

            $table->bigInteger('building_site_id')->unsigned();

            $table->bigInteger('user_id')->unsigned();

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
        Schema::dropIfExists('customer_reports');
    }
}
