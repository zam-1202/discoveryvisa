<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RequiredDocument extends Model
{
    protected $fillable = [
		'name',
		'type'
	];
}
