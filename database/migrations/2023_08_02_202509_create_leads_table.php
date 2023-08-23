<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->unsignedBigInteger('operator_id');
            $table->foreign('operator_id')->references('id')->on('users');

            $table->string('city');
            $table->string('subcity')->nullable();
            $table->string('address');

            $table->string('meeting_date');
            $table->string('time_period');

            $table->string('client_fullname');
            $table->string('phone');
            $table->text('comment')->nullable();

            $table->unsignedBigInteger('job_type');
            $table->foreign('job_type')->references('id')->on('service_types');

            $table->boolean('range')->default(false);
            $table->boolean('measuring')->default(false);

            $table->text('note')->nullable();
            $table->string('status');

            $table->unsignedBigInteger('manager_id')->nullable();
            $table->foreign('manager_id')->references('id')->on('users');

            $table->string('accepted')->nullable();
            $table->string('entered')->nullable();
            $table->string('exited')->nullable();

            $table->unsignedDouble('check')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leads');
    }
}
