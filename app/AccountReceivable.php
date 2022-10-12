<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccountReceivable extends Model
{
    protected $fillable = [
		'company',
		'batch_no',
		'application_date',
		'total_amount',
		'payment_status'
	];
}
