<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNomenclatureReceiptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nomenclature_receipts', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->double('quantity');
            $table->unsignedBigInteger('nomenclature_id');
            $table->foreign('nomenclature_id')->references('id')->on('nomenclatures')->onDelete('cascade');
            $table->unsignedBigInteger('receipt_id');
            $table->foreign('receipt_id')->references('id')->on('receipts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nomenclature_receipts');
    }
}
