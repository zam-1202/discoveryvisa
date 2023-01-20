<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisaDocumentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visa_document', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('visa_type_id');
            $table->foreign('visa_type_id')->references('id')->on('visa_types')->onDelete('cascade');

            $table->unsignedBigInteger('required_document_id');
            $table->foreign('required_document_id')->references('id')->on('required_documents')->onDelete('cascade');
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
        Schema::dropIfExists('visa_document');
    }
}
