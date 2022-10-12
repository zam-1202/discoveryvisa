<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePendingApprovalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pending_approvals', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->string('application_id');
			$table->string('request_type');
			$table->string('requested_by');
			$table->date('request_date');
			$table->string('approved_by');
			$table->date('approval_date');
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
        Schema::dropIfExists('pending_approvals');
    }
}
