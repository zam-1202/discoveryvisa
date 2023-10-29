<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $fillable = [
		'reference_no',
		'application_status',
		'customer_type',
		'customer_company',
		'group_name',
		'submitter',
		'pickupMethod',
		'pickup_fee',
		'branch',
		'lastname',
		'firstname',
		'middlename',
		'birthdate',
		'gender',
		'marital_status',
		'address',
		'email',
		'telephone_no',
		'mobile_no',
		'passport_no',
		'passport_expiry',
		'departure_date',
		'remarks',
		'visa_type',
		'visa_price',
		'handling_price',
		'promo_code',
		'documents_submitted',
		'payment_status',
		'or_number',
		'vpr_number',
		'tracking_no',
		'verification_no',
		'application_date',
		'encoded_by',
		'last_update_by',
		'payment_received_by',
		'payment_date',
		'batch_no',
		'date_received_by_main_office',
		'submission_batch_no',
		'submitted_to_embassy',
		'receiver_from_embassy',
		'released_by_embassy',
		'visa_result',
		'released_to',
		'courier_tracking'

	];
}
