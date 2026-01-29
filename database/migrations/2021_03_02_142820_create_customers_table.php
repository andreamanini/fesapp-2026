<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();

            $table->string('company_name');

            $table->string('manager')->nullable();
            
            $table->string('vatnumber')->nullable();
            
            $table->string('taxcode')->nullable();
            
            $table->string('sdi')->nullable();

            $table->string('email')->nullable();

            $table->string('email2')->nullable();

            $table->string('email3')->nullable();

            $table->string('telephone')->nullable();

            $table->string('telephone2')->nullable();

            $table->string('telephone3')->nullable();

            $table->string('address')->nullable();

            $table->string('city')->nullable();

            $table->string('postcode')->nullable();

            $table->string('county')->nullable();

            $table->string('country')->nullable();

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
        Schema::dropIfExists('customers');
    }
}
