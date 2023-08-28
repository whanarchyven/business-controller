<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRepairsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('repairs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('lead_id');
            $table->foreign('lead_id')->references('id')->on('leads')->onDelete('cascade');
            $table->unsignedBigInteger('master_id')->nullable();
            $table->foreign('master_id')->references('id')->on('users')->onDelete('cascade');
            $table->double('check')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('repairs');
    }
}
