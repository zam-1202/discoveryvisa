<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = [
		'code',
		'description',
		'pickup_price'
	];
}
