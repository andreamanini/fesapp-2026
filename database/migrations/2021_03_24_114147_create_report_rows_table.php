<?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateReportRowsTable extends Migration
    {
        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create('report_rows', function (Blueprint $table) {
                $table->id();

                $table->string('work_type')->nullable();

                $table->string('strutt_sabbiata')->nullable();

                $table->string('strutt_verniciata')->nullable();

                $table->string('strutt_lavaggio')->nullable();

                $table->string('strutt_soffiatura')->nullable();

                $table->string('strutt_intonaco')->nullable();
                
                $table->string('strutt_verniciata_anticorrosiva')->nullable();
                
                $table->string('strutt_verniciata_carrozzeria')->nullable();
                
                $table->string('strutt_verniciata_impregnante')->nullable();
                
                $table->string('strutt_verniciata_intumescente')->nullable();
                
                $table->string('strutt_intonaci_intumescenti')->nullable();
                
                $table->string('strutt_altro')->nullable();

                $table->string('materiale')->nullable();

                $table->integer('qty')->nullable();

                $table->float('mq_lavorati_x')->nullable();

                $table->float('mq_lavorati_y')->nullable();

                $table->float('mq_lavorati_z')->nullable();

                $table->float('mq_lavorati_tot')->nullable();

                $table->bigInteger('report_id')->unsigned();

                $table->timestamps();

                $table->foreign('report_id')
                    ->references('id')
                    ->on('reports');
            });
        }

        /**
         * Reverse the migrations.
         *
         * @return void
         */
        public function down()
        {
            Schema::dropIfExists('report_rows');
        }
    }
