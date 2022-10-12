<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PartnerCompany extends Model
{
    protected $fillable = [
		'name',
		'type'
	];
}
