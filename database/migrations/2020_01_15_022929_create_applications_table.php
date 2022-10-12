<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->string('reference_no');
			$table->string('application_status');
			$table->enum('customer_type',['Walk-In','PIATA','PTAA','Corporate']);
			$table->string('customer_company');
			$table->enum('branch',['Manila','Cebu','Davao']);
			$table->string('lastname');
			$table->string('firstname');
			$table->string('middlename');
			$table->date('birthdate');
			$table->string('address');
			$table->string('email');
			$table->string('telephone_no');
			$table->string('mobile_no');
			$table->string('passport_no');
			$table->date('passport_expiry');
			$table->date('departure_date');
			$table->string('remarks');
			$table->string('visa_type');
			$table->decimal('visa_price');
			$table->string('visa_price_type');
			$table->string('documents_submitted');
			$table->string('payment_status');
			$table->string('or_number');
			$table->string('vpr_number');
			$table->string('tracking_no');
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
        Schema::dropIfExists('applications');
    }
}
