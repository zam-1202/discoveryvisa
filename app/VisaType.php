<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VisaType extends Model
{
    protected $fillable = [
		'name',
		'price',
		'filipino_documents',
		'japanese_documents',
		'foreign_documents'
	];
}
