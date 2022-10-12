<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisaTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visa_types', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->string('name');
			$table->decimal('price');
			$table->string('filipino_documents');
			$table->string('japanese_documents');
			$table->string('foreign_documents');
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
        Schema::dropIfExists('visa_types');
    }
}
