@extends('layouts.app')

@php
$branches = App\Branch::all();
$branch_lookup = [];
	foreach($branches as $branch){
		$branch_lookup[$branch->code] = $branch->description;
	}

	$visatypearray = array();
	foreach($visatypes as $type)
	{
		$visatypearray[$type->id] = $type->name;
	}

	$selectedVisaType = $application->visa_type_id;

    $application_status_array = array('1' => 'NEW Application',
                                      '2' => 'Sent to Main Office',
                                      '3' => 'Received by Main Office',
                                      '4' => 'Sent to Original Branch',
                                      '5' => 'Received by Original Branch',
                                      '6' => 'Submitted to Embassy',
                                      '7' => 'Received from Embassy',
                                      '8' => 'Sent to/Claimed by Client',
									  '9' => 'Incomplete',
									  '10' => 'Pending Approval');

	$application_status = $application->application_status;
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
		<style>

			.scrollable-div {
			max-height: 200px; /* Adjust the height as needed */
			overflow-y: auto;
			}

			.required-field,
			.required-text {
			color: red;
			font-size: 13px;
			}

			.required-text {
			font-style: italic;
			}

        </style>
    </head>
<body>

@section('content')
<div class="row">
	<div class="col-sm-20 text-center">
	<div class="jumbotron bg-dark text-white" style="padding: 10px">
			<h1>Update an Application</h1>
			<div class="col-sm-20 text-left">
				<strong>Filer:</strong> {{$application->encoded_by}}
				<br>
				<strong>Last Encoded by:</strong> {{$application->last_update_by}} 
			</div>
		</div>

        <form method="post" action="{{ route('applications.update', $application->id) }}">
            @method('PATCH')
            @csrf
            <div class="form-group row">
				<div class="col-md-1">
				  <label for="branch">Branch</label>
				  {{Form::text('branch', $application->branch, ['class' => 'form-control text-center', 'readonly' => 'readonly']) }}
				</div>
				<div class="col-md-2">
				  <label for="reference_no">Reference No.</label>
				  {{Form::text('reference_no', $application->reference_no, ['readonly' => 'readonly', 'class' => 'form-control text-center'])}}
				</div>

				<div class="col-md-3">
				  <label for="application_status">Application Status</label>
                  @if ($application->application_status == '9')
                    {{Form::select('application_status', $application_status_array_incomplete, $application->application_status, ['class' => 'form-control text-center'])}}
                  @else
                    {{Form::text('application_status', $application_status_array[$application->application_status], ['class' => 'form-control text-center','disabled' => 'disabled'])}}
                    {{Form::hidden('application_status', $application->application_status)}}
                  @endif
				</div>
				<div class="col-md-2">
				<label for="date_received_from_embassy">Embassy Filing</label>
				@if ($application->application_status == '7')
					{{Form::text('date_received_from_embassy', $application->date_received_from_embassy, ['class' => 'form-control text-center', 'disabled' => 'disabled']) }}
				@else
					{{Form::text('date_received_from_embassy', ($application->application_status == $application->date_received_from_embassy) ? $application->date_received_from_embassy : '', ['class' => 'form-control text-center', 'disabled' => 'disabled']) }}
				@endif
				</div>


				<div class="col-md-2">
				  <label for="tracking_no">Tracking No.</label>
				  {{Form::text('tracking_no', $application->tracking_no, ['class' => 'form-control text-center', 'oninput' => 'validateInput(event)']) }}
				</div>
				<div class="col-md-2">
				  <label for="tracking_no">Verification No.</label>
				  {{Form::text('verification_no', $application->verification_no, ['class' => 'form-control text-center', 'oninput' => 'validateInput(event)']) }}
				</div>
			</div>

			<div class="form-group row">

				<div class="col-md-4"></div>

			</div>

			<div class="form-group row">
				<div class="col-md-3">
				<label for="customer_type">Customer Type<span class="required-field">*</span></label>
				  {{Form::select('customer_type', $customer_type_array, $application->customer_type, ['class' => 'form-control', 'id' => 'customer_type'])}}
				</div>

				<div class="col-md-3">
				<label for="customer_company">Customer Company<span class="required-field">*</span></label>
					<select name="customer_company" class="form-control" id="customer_company" disabled>
						@foreach ($customer_company as $partnerCompany)
							<option value="{{ $partnerCompany }}" {{ $partnerCompany === $application->customer_company ? 'selected' : '' }}>
								{{ $partnerCompany }}
							</option>
						@endforeach
					</select>
				</div>

				
				<div class="col-md-4">
					<label for="group_name">Group Name</label>
					{{ Form::textarea('group_name', $application->group_name, ['class' => 'form-control text-center text-uppercase', 'rows' => '1', 'id' => 'group_name']) }}
				</div>
				<div class="col-md-2">
				<label for="pickupMethod">Pick Up Method<span class="required-field">*</span></label>
					<?php
					$pickupMethodOptions = ['On-site' => 'On-site', 'Courier' => 'Courier'];
					$defaultPickupMethod = $application->pickupMethod; // Fetch the value from the database
					?>
					{{ Form::select('pickupMethod', $pickupMethodOptions, $defaultPickupMethod, ['class' => 'form-control text-center']) }}
				</div>
			</div>

				<br>
				<div class="row">
				<div class="col-md-4"><hr style="border: 1px solid black;"></div>
				<div class="col-md-4"><h4 style="text-align:center;">PERSONAL DETAILS</h4></div>
				<div class="col-md-4"><hr style="border: 1px solid black;"></div>
				</div>
				<br>

				<div class="form-group row">
					<div class="col-md-3">
					<label for="lastname">Last Name<span class="required-field">*</span></label>
						{{ Form::text('lastname', $application->lastname, ['class' => 'form-control text-center text-uppercase', 'id' => 'editApplication_lastname']) }}
						<!-- {{Form::text('lastname', old('lastname'), ['class' => 'form-control text-center text-uppercase', 'id' => 'createApplication_lastname', 'maxlength' => '128'])}} -->
					</div>
					<div class="col-md-3">
					<label for="firstname">First Name<span class="required-field">*</span></label>
						{{Form::text('firstname', $application->firstname, ['class' => 'form-control text-center text-uppercase', 'id' => 'editApplication_firstname']) }}
					</div>
					<div class="col-md-3">
						<label for="middlename">Middle Name</label>
						{{Form::text('middlename', $application->middlename, ['class' => 'form-control text-center text-uppercase', 'id' => 'middlename']) }}
					</div>
				</div>

				<div class="form-group row">
				<div class="col-md-3">
				<label for="birthdate">Birthday<span class="required-field">*</span></label>
					{{ Form::date('birthdate', $application->birthdate, ['class' => 'form-control text-center', 'id' => 'birthdate', 'max' => \Carbon\Carbon::now()->subDay()->format('Y-m-d'), 'title' => 'Please enter valid birthdate', 'required']) }}
					@error('birthdate')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
				</div>
				<div class="col-md-2">
				<label for="gender">Gender<span class="required-field">*</span></label>
					  {{Form::select('gender', array('Female' => 'Female', 'Male' => 'Male'), $application->gender, ['class' => 'form-control'])}}
					</div>
					<div class="col-md-2">
					<label for="marital_status">Marital Status<span class="required-field">*</span></label>
					  {{Form::select('marital_status', array('Single' => 'Single', 'Married' => 'Married', 'Widowed' => 'Widowed'), $application->marital_status, ['class' => 'form-control'])}}
					</div>
					<div class="col-md-5">
					  <label for="email">Email:</label>
					  {{Form::text('email', $application->email, ['class' => 'form-control text-center'])}}
					</div>
				</div>

				<div class="form-group row">
					<div class="col-md-2">
					  <label for="telephone_no">Telephone No</label>
					  {{Form::text('telephone_no', $application->telephone_no, ['class' => 'form-control text-center', 'id' => 'telno', 'maxlength' => '15', 'onkeypress' => 'return isNumericKey(event)', 'onpaste' => 'return validatePastedText(event)', 'oninput' => 'validateTelephoneNo(this.value)']) }}
					</div>
					<div class="col-md-2">
					  <label for="mobile_no">Mobile No:</label>
					  {{Form::text('mobile_no', $application->mobile_no, ['class' => 'form-control text-center', 'id' => 'mobno', 'maxlength' => '15', 'onkeypress' => 'return isNumericKey(event)', 'onpaste' => 'return validatePastedText(event)', 'oninput' => 'validateMobileNo(this.value)'])}}
					</div>
					<div class="col-md-8">
					<label for="address">Address (Characters: <span id="addressLength">0/50</span>)<span class="required-field">*</span></label>
						{{Form::textarea('address', $application->address, ['class' => 'form-control text-center text-uppercase', 'rows' => '2', 'id' => 'address', 'oninput' => 'checkAddressLength()', 'maxlength' => '50'])}}
					</div>
				</div>

				<div class="form-group row">
					<div class="col-md-4">
					<label for="passport_no">Passport No<span class="required-field">*</span></label>
			  {{ Form::text('passport_no', $application->passport_no, ['class' => 'form-control text-center', 'id' => 'passport_no', 'style' => 'text-transform: uppercase;', 'onkeyup' => 'validatePassportNo(this.value)']) }}			  @error('passport_no')
			<span class="invalid-feedback" role="alert">
				<strong>{{ $message }}</strong>
			</span>
			@enderror
			</div>
					<div class="col-md-4">
					<label for="passport_expiry">Passport Expiry<span class="required-field">*</span></label>
					  {{Form::date('passport_expiry', $application->passport_expiry, ['class' => 'form-control text-center', 'min' => now()->addDay()->format('Y-m-d')]) }}
					</div>
					<div class="col-md-4">
					<label for="departure_date">Expected Departure Date<span class="required-field">*</span></label>
					  {{Form::date('departure_date', $application->departure_date, ['class' => 'form-control text-center', 'min' => now()->addDay()->format('Y-m-d')]) }}
					</div>
				</div>

				<div class="form-group row">
					<div class="col-md-12">
					  <label for="remarks">Remarks:</label>
					  {{Form::textarea('remarks', $application->remarks, ['class' => 'form-control text-center', 'rows' => '3'])}}
					</div>
				</div>

				<br>
				<div class="row">
				<div class="col-md-4"><hr style="border: 1px solid black;"></div>
				<div class="col-md-4"><h4 style="text-align:center;">VISA DETAILS</h4></div>
				<div class="col-md-4"><hr style="border: 1px solid black;"></div>
				</div>
				<br>

				<div class="form-group row">
						<div class="col-md-4">
						<label for="visa_type">Visa Type:<span class="required-field">*</span></label>
								<select class="form-control" name="visa_type" id="visa_type">
								<option value="">--- Select a visa type ---</option>
									@foreach($visatypes as $visaType)
										<option value="{{ $visaType->name }}"
											data-visa-fee="{{ $visaType->visa_fee }}"
											data-handling-fee="{{ $visaType->handling_fee }}"
											{{ $visaType->name === $application->visa_type ? 'selected' : '' }}>            	
											{{ $visaType->name }}
										</option>
									@endforeach
								</select>
						</div>


				<div class="col-md-2">
					<label for="visa_price">Visa Price:</label>
						{{ Form::text('visa_price', $application->visa_price, ['data-visa-fee' => $visaType->visa_fee, 'class' => 'form-control text-center', 'id' => 'visa_price', 'readonly' => 'readonly']) }}
				</div>

				<div class="col-md-2">
					<label for="handling_price">Handling Price:</label>
					  {{ Form::text('handling_price', $application->handling_price, ['data-handling-fee' => $visaType->handling_fee, 'class' => 'form-control text-center', 'id' => 'handling_price', 'readonly' => 'readonly']) }}
				</div>

        
				<div class="col-md-4">
					<label for="promo_code">Promo Code:</label>
					<div class="input-group">
						{{Form::text('promo_code', $application->promo_code, ['class' => 'form-control text-center text-uppercase', 'id' => 'promo_code', 'placeholder' => '(optional)'])}}
						&nbsp;	
						<div class="input-group-append">
								<a class="btn btn-success text-white" id="promo_code_btn">Use Promo Code</a>
							</div>
					</div>	
				</div>

					<div class="col-md-3">
						{{Form::hidden('discount_amount', $application->discount_amount)}}
						{{Form::hidden('discount', 0)}}
					</div>
				</div>
				

				<div class="form-group row">
					<div class="col-md-12">
					<label for="documents_submitted">Documents Required:<span class="required-field">*</span></label>
					  {{Form::hidden('documents_submitted', $application->documents_submitted)}}
					  <div class="table-responsive">
						  <table class="table table-sm table-bordered">
							  <thead class="thead-light">
								  <th style="width:33.33%;" class="bg-success text-white">FILIPINO DOCUMENTS</th>
								  <th style="width:33.33%;" class="bg-info text-white">JAPANESE DOCUMENTS</th>
								  <th style="width:33.33%;" class="bg-dark text-white">FOREIGNER DOCUMENTS</th>
							  </thead>
							  <tbody>
                                          <tr>
                                            <td class="bg-success text-left">
											<div class="scrollable-div">
                                                <ul class="list-group" id="filipino_documents">
                                                    @foreach ($docs_filipino as $value)
                                                        <li class='list-group-item'><input type='checkbox' name='submitted_documents' value='{{ $value->id }}' id='{{ $value->id }}'/> {{ $value->name }} </li>
                                                    @endforeach
                                                </ul>
												</div>
                                            </td>
                                            <td class="bg-info text-left">
											<div class="scrollable-div">
                                                <ul class="list-group" id="japanese_documents">
                                                    @foreach ($docs_japanese as $value)
                                                        <li class='list-group-item'><input type='checkbox' name='submitted_documents' value='{{ $value->id }}' id='{{ $value->id }}'/> {{ $value->name }} </li>
                                                    @endforeach
                                                </ul>
												</div>
                                            </td>
                                            <td class="bg-dark text-left">
											<div class="scrollable-div">
                                                <ul class="list-group" id="foreign_documents">
                                                    @foreach ($docs_foreign as $value)
                                                        <li class='list-group-item'><input type='checkbox' name='submitted_documents' value='{{ $value->id }}' id='{{ $value->id }}'/> {{ $value->name }} </li>
                                                    @endforeach
                                                </ul>
												</div>
                                            </td>
                                          </tr>
                                      </tbody>
						  </table>
					  </div>
					</div>
				</div>

				{{Form::hidden('payment_status', $application->payment_status)}}
				<div class="row">
					<div class="col-md-2 offset-md-4">
						<button type="submit" class="btn btn-primary">Update</button>
					</div>
        </form>
					<div class="col-md-2">
						<a href="{{ url()->previous() }}" class="btn btn-danger">Back</a>
					</div>
				</div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">

function validateInput(event) {
  const input = event.target;
  const newValue = input.value.replace(/\D/g, ''); // Remove all non-digit characters
  input.value = newValue;
}
function checkAddressLength() {
    var address = document.getElementById("address");
    var addressLength = document.getElementById("addressLength");
    
    if (address.value.length > 50) {
        addressLength.style.color = "red";
    } else {
        addressLength.style.color = "black";
    }
    
    addressLength.textContent = address.value.length + "/50";
}

function isNumericKey(event) {
  const charCode = (event.which) ? event.which : event.keyCode;
  if ((charCode < 48 || charCode > 57) && charCode !== 45 && charCode !== 43) {
    return false;
  }
  return true;
}

function validatePastedText(event) {
  const pastedText = event.clipboardData.getData('text/plain');
  if (!/^\d+(-\d*){0,14}$/.test(pastedText) && !/^\+\d+(-\d*){0,14}$/.test(pastedText)) {
    event.preventDefault();
    return false;
  }
  return true;
}

function validateTelephoneNo(value) {
  const telnoInput = document.getElementById('telno');
  const isValid = /^\d+(-\d*){0,14}$/.test(value) || /^\+\d+(-\d*){0,14}$/.test(value);
  if (!isValid) {
    telnoInput.setCustomValidity('Invalid telephone number');
  } else {
    telnoInput.setCustomValidity('');
  }
}

function validateMobileNo(value) {
  const mobnoInput = document.getElementById('mobno');
  const isValid = /^\d+(-\d*){0,14}$/.test(value) || /^\+\d+(-\d*){0,14}$/.test(value);
  if (!isValid) {
    mobnoInput.setCustomValidity('Invalid mobile number');
  } else {
    mobnoInput.setCustomValidity('');
  }
}


function validatePassportNo(value) {
  var regex = /^[A-Za-z0-9\- ]{0,15}$/;
  var containsLettersOrNumbers = /[A-Za-z0-9]/.test(value);
  var isValid = regex.test(value) && containsLettersOrNumbers;
  var passportNoInput = document.getElementById('passport_no');

  if (!isValid) {
    passportNoInput.setCustomValidity('Please enter a valid passport number');
  } else {
    passportNoInput.setCustomValidity('');
  }
}

	$(document).ready(function(){

		$(document).ready(function() {
			restore_checkboxes();
		});
		
	$(document).on('keypress', '#editApplication_lastname, #editApplication_firstname, #middlename', function(event) {
    var inputValue = this.value;
    var keyPressed = String.fromCharCode(event.charCode || event.which);
    
    if (inputValue.length === 0 && !isLetter(keyPressed)) {
        event.preventDefault();
        return false;
    }
    
    var regex = new RegExp("^[A-Za-z0-9.'\\s-]+$");
    if (!regex.test(keyPressed) && event.which !== 0 && !event.ctrlKey && !event.metaKey) {
        event.preventDefault();
        return false;
    }
});

function isLetter(char) {
    return /^[A-Za-z]+$/.test(char);
}




	// document.getElementById("updateApplication").addEventListener("click", function(event){
	// 	// event.preventDefault(); // Prevent the form from submitting
	// 	validatefield(); // Call the validation function
	// 	});

	// function validatefield() {
	// 		const lastname = $('#createApplication_lastname').val();
	// 		const firstname = $('#createApplication_firstname').val();
	// 		const bday = $('#birthdate').val();
	// 		const home_address = $('#address').val();
	// 		const passport_num = $('#passport_no').val();
	// 		const departure = $('#departure_date').val();
	// 		const rmk = $('#remarks').val();
	// 		const v_type = $('#visa_type').val();
	// 		const docs_sub = $('#documents_submitted').val();
	// 		const customerType = $('#customer_type').val();
	// 		// const handle = $('#handling_fee').val();
	// 		// const vFee = $('#visa_price').val();
			
	// 	{
	// 		if (lastname == '' || firstname == '' || bday == '' || home_address == '' || passport_num == '' || departure == '' || 
	// 		rmk == '' || docs_sub == '' || customerType == '') {
	// 			console.log("Not all fields are filled out.");
	// 			Swal.fire({
	// 				position: 'center',
	// 				icon: 'warning',
	// 				title: 'Please fill out all required fields.',
	// 				showConfirmButton: true,
    //                 timer: 6000	
	// 			});
	// 		} 
	// 		else {
	// 			console.log("Fields are filled out");
	// 		}
	// 	}
	// }

	const customerTypeSelect = document.getElementById('customer_type');
	const customerCompanySelect = document.getElementById('customer_company');
	const partnerCompanies = {!! json_encode($customer_company) !!};

  // Function to update the Customer Company dropdown options
	function updateCustomerCompanyOptions() {
    const selectedType = customerTypeSelect.value;
    const customerNameSelect = document.getElementById('customer_company');
    customerNameSelect.innerHTML = ''; // Clear previous options

    console.log('Selected Type:', selectedType);
    console.log('Customer Name Select:', customerCompanySelect);

    if (selectedType === 'Walk-in' || selectedType === '') {
      customerNameSelect.disabled = true;
    } else {
      customerNameSelect.disabled = false;

      // Populate options based on selected type
      const options = partnerCompanies.filter(company => company.type === selectedType);
      options.forEach(option => {
        const optionElement = document.createElement('option');
        optionElement.value = option.name;
        optionElement.textContent = option.name;
        customerNameSelect.appendChild(optionElement);
      });
    }
  }

  // Event listener for Customer Type selection change
  customerTypeSelect.addEventListener('change', updateCustomerCompanyOptions);

  // Initial update of Customer Company options
  updateCustomerCompanyOptions();


	document.getElementById('visa_type').addEventListener('change', function(event) {
        const selectedVisaType = event.target.value;
        const selectedOption = event.target.selectedOptions[0];
        const visaFee = selectedOption.dataset.visaFee;
        const handlingFee = selectedOption.dataset.handlingFee;

        const visaTypeField = document.getElementById('visa_type');
        const visaPriceField = document.getElementById('visa_price');
        const visaHandlingfeeField = document.getElementById('handling_price');

        if(selectedVisaType) {
            visaTypeField.value = selectedVisaType;
            visaPriceField.value = visaFee;
            visaHandlingfeeField.value = handlingFee;
        } else {
            visaTypeField.value = '';
            visaPriceField.value = '';
            visaHandlingfeeField.value = '';
        }
    });



		var visaTypeArray = {!! $visatypes->toJson() !!};

		// populate_partner_companies("{{$application->customer_type}}");
		// get_promo_code();

        var visaType = $('#visa_type').find('option:selected').val();
		// on_change_visa_type(visaType,true);

		// function populate_partner_companies(filterType = '')
		// {
		// 	$('#customer_company').html('');
		// 	$('#customer_company').attr('disabled', true);

		// 	if(filterType != 'Walk-In')
		// 	{
		// 		$.ajax({
		// 			url: "../../partner_companies/getpartners",
		// 			data: {filterType:filterType},
		// 			success: function(data)
		// 			{
		// 				var options = '';
		// 				var selected = '';

		// 				data.forEach(function(row){
		// 					selected = '';
		// 					if("{{$application->customer_company}}" == row.id) selected = "selected ";
		// 					options += "<option " + selected + "value='" + row.id + "'>" + row.name + "</option>"
		// 				});

		// 				$('#customer_company').attr('disabled', false);
		// 				$('#customer_company').html(options);
		// 			}
		// 		});
		// 	}
		// }

		// function on_change_visa_type(visaType,fromUser)
		// {
		// 	var selectedVisaType = $.grep(visaTypeArray, function(obj){return obj.id == visaType;})[0];
		// 	get_required_documents(selectedVisaType);
		// 	restore_checkboxes();
		// 	$('#visa_price').val(selectedVisaType.price);
		// 	get_promo_code();
		// }

		// function get_required_documents(selectedVisaType)
		// {
		// 	var filipinoDocuments = selectedVisaType.filipino_documents.split(",");
		// 	var japaneseDocuments = selectedVisaType.japanese_documents.split(",");
		// 	var foreignDocuments = selectedVisaType.foreign_documents.split(",");

		// 	$('#filipino_documents').html(generate_checkboxes(filipinoDocuments));
		// 	$('#japanese_documents').html(generate_checkboxes(japaneseDocuments));
		// 	$('#foreign_documents').html(generate_checkboxes(foreignDocuments));
		// }

		function generate_checkboxes(documentArray = [])
		{
			var outputString = '';
			var docsArray = {!! $documentlist->toJson() !!};

			documentArray.forEach(function(item){
				var req_document = $.grep(docsArray, function(obj){return obj.id == item})[0];
				outputString += "<li class='list-group-item'> <input type='checkbox' name='submitted_documents' value='" + req_document.id + "'/>" + req_document.name + "</li>";
			});

			return outputString;
		}

		function restore_checkboxes()
		{
			var submitted_documents = "{{$application->documents_submitted}}".split(",");
			$('input[name="submitted_documents"]').each(function(){
				if(submitted_documents.includes($(this).val()))
				{
					$(this).attr("checked", true);
				}
			});
		}

		// function update_visa_price()
		// {
		// 	if($("input[name='promo_code']:checked").val() === 'Promo')
		// 	{
		// 		$('#visa_price').attr('readonly', false);
		// 	}
		// 	else
		// 	{
		// 		$('#visa_price').attr('readonly', true);
		// 		$('#visa_type').change();
		// 	}
		// }

		// function get_promo_code()
		// {
		// 	var promo_code = $("#promo_code").val().toUpperCase();
		// 	if(promo_code != "")
		// 	{
		// 		var id = {{$application->id}};
		// 		$.ajax({
		// 			url: "../redeem_promo_code",
		// 			data: {id:id, promo_code:promo_code, page:"edit"},
		// 			success: function(data)
		// 			{
		// 				apply_promo_code(data[0]);
		// 			}
		// 		});
		// 	}
		// 	else {
		// 		apply_promo_code("");
		// 	}
		// }

		// function apply_promo_code(discount)
		// {
        //     var currentVisaType = {{$application->visa_type}};
		// 	var visaType = $('#visa_type').find('option:selected').val();
		// 	var selectedVisaType = $.grep(visaTypeArray, function(obj){return obj.id == visaType;})[0];
		// 	if(discount != "")
		// 	{
		// 		var discount_amount = 0;

		// 		if(discount.substring(discount.length-1, discount.length) == '%')
		// 		{
		// 			discount_amount = selectedVisaType.price * (Number(discount.substring(0, discount.length-1))/100);
		// 		} else {
		// 			discount_amount = Number(discount);
		// 		}
		// 		$('#discount-text').html('-'.concat(discount_amount.toFixed(2)));

        //         if (currentVisaType == selectedVisaType.id) {
        //             $('#visa_price').val(({{ $application->visa_price }} - discount_amount).toFixed(2));
        //         } else {
        //             $('#visa_price').val((selectedVisaType.price - discount_amount).toFixed(2));
        //         }

		// 	}
		// 	else
		// 	{
		// 		$('#discount-text').html('');
        //         if (currentVisaType == selectedVisaType.id) {
        //             $('#visa_price').val({{ $application->visa_price }});
        //         } else {
        //             $('#visa_price').val(selectedVisaType.price);
        //         }

		// 	}
		// }

		// $(document).on('change', '#customer_type', function(){
		// 	var filterType = $(this).val();
		// 	populate_partner_companies(filterType);
		// });

		$(document).on('change', '#visa_type', function(){
			var visaType = $(this).find('option:selected').val();
			// on_change_visa_type(visaType,true);
		});

		$(document).on('change', 'input[name="submitted_documents"]', function(){
			var checkboxes = $('input[name="submitted_documents"]:checked');

			var output = [];

			checkboxes.each(function(){
				output.push($(this).val());
			});

			$('input[name="documents_submitted"]').val(output.join(","));
		});

		// $(document).on('click', '#promo_code_btn', function(){
		// 	get_promo_code();
		// });

	});

</script>
@endsection


<!-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> -->