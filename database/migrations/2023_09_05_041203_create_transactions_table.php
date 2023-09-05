<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('budget_id')->default(1);
            $table->foreign('budget_id')->references('id')->on('budgets')->onDelete('cascade');
            $table->unsignedBigInteger('state_id')->default(1);
            $table->foreign('state_id')->references('id')->on('transaction_states')->onDelete('cascade');
            $table->text('description');
            $table->string('type');
            $table->double('value');
            $table->double('balance_stamp');
            $table->unsignedBigInteger('responsible')->default(1);
            $table->foreign('responsible')->references('id')->on('users')->onDelete('cascade');
            $table->text('documents');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
