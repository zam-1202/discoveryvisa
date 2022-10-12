<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ApplicationBatch extends Model
{
    protected $fillable = [
		'batch_no',
		'batch_date',
		'total_applications',
		'status',
		'tracking_no'
	];
}
