<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RequiredDocument extends Model
{
    protected $fillable = [
		'name',
		'type'
	];

    public function visas()
    {
        return $this->belongstoMany(VisaType::class, 'visa_document', 'required_document_id' , 'visa_type_id');
    }
}
