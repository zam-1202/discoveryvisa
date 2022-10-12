<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PendingApprovals extends Model
{
    protected $fillable = [
		'application_id',
		'request_type',
		'requested_by',
		'request_date',
		'officer_in_charge',
		'action_date',
		'action'
	];
}
