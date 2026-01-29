<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->string('name');

            $table->string('surname');

            $table->string('email')->unique();

            $table->string('telephone')->nullable();

            $table->timestamp('email_verified_at')->nullable();

            $table->string('password');

            $table->string('role')->default('employee');

            $table->boolean('active')->default(1);

            $table->rememberToken();

            $table->string('created_by');

            $table->string('updated_by')->nullable();

            $table->softDeletes('deleted_at', 0);

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
        Schema::dropIfExists('users');
    }
}
