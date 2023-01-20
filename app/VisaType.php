<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VisaType extends Model
{
    protected $fillable = [
		'name',
		'handling_fee',
		'visa_fee'
	];

    public function documents()
    {
        return $this->belongstoMany(RequiredDocument::class, 'visa_document', 'visa_type_id', 'required_document_id');
    }
}
