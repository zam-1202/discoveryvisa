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

			#pickup_fee_container {
			display: none;
			}

        </style>
    </head>
<body>
@php
$branches = App\Branch::all();
$branch_lookup = [];
	foreach($branches as $branch){
		$branch_lookup[$branch->code] = $branch->description;
	}
@endphp

@extends('layouts.app')

@section('content')

<div class="row">
 <div class="col-sm-20 text-center">
  <div class="jumbotron bg-dark text-white" style="padding: 10px">
    <h1>Create a New Visa Application</h1>
  </div>
  <div>

      <form method="post" action="{{ route('applications.store') }}">
          @csrf

			<div class="form-group">
				<div class = "row">
					<div class="col-md-3">
					<label for="customer_type">Customer Type<span class="required-field">*</span></label>
							<select class="form-control" name="customer_type" id="customer_type" required>
								<option value="">- Select a customer type -</option>
									@php
										$displayedTypes = [];
									@endphp
										@foreach($customer_company as $partnerCompanies)
										@if(!in_array($partnerCompanies->type, $displayedTypes))
										<option value="{{ $partnerCompanies->type }}">
											{{ $partnerCompanies->type }}
										</option>
									@php
										$displayedTypes[] = $partnerCompanies->type;
									@endphp
									@endif
								@endforeach
							</select>
					</div>

					<div class="col-md-3">
					<label for="customer_company">Customer Company<span class="required-field">*</span></label>
						<select class="form-control" name="customer_company" id="customer_company">
							@foreach($customer_company as $partnerCompanies)
								<option value="{{ $partnerCompanies->name }}">
								{{ $partnerCompanies->name }}</option>
							@endforeach
						</select>
					</div>

					<div class="col-md-4">
						<label for="group_name">Group Name</label>
						{{Form::text('group_name', old('group_name'), ['class' => 'form-control text-center text-uppercase', 'id' => 'group_name'])}}
					</div>

					<div class="col-md-2">
						<label for="pickupMethod">Pick Up Method<span class="required-field">*</span></label>
						{{Form::select('pickupMethod', array('On-site' => 'On-site', 'Courier' => 'Courier'), old('pickupMethod'), ['class' => 'form-control text-center', 'id' => 'pickupMethod', 'onchange' => 'displaySelectedValue(this)']) }}
					</div>
				</div>
			</div>

			<div id="pickup_fee_container" class="col-md-2">
				<label for="pickupFee">Pick Up Fee</label>
				{{ Form::text('pickup_fee', null, ['class' => 'form-control text-center', 'readonly' => 'readonly', 'id' => 'pickup_fee']) }}
			</div>


			<br>
		  <div class="row">
			<div class="col-md-4"><hr style="border: 1px solid black;"></div>
			<div class="col-md-4"><h4 style="text-align:center;">PERSONAL DETAILS</h4></div>
			<div class="col-md-4"><hr style="border: 1px solid black;"></div>
		  </div>
		  <br>

		  <div class="col-md-12 text-center">
				<p style="font-size: 9px; color: red">
					Valid characters are uppercase letters (A-Z), lowercase letters (a-z), numbers (0-9), period (.), apostrophe ('), hyphen/dash (-), and spaces. No other characters are allowed. <br> Asterisks denote required fields.
				</p>
			</div>
			
        <div class="form-group row">
				<div class="col-md-3">
				<label for="lastname">Last Name<span class="required-field">*</span></label>
				{{Form::text('lastname', old('lastname'), ['class' => 'form-control text-center text-uppercase', 'id' => 'createApplication_lastname', 'maxlength' => '128'])}}
				</div>


				<div class="col-md-3">
				<label for="firstname">First Name<span class="required-field">*</span></label>
					{{Form::text('firstname', old('firstname'), ['class' => 'form-control text-center text-uppercase', 'id' => 'createApplication_firstname', 'maxlength' => '128'])}}
				</div>

				<div class="col-md-3">
					<label for="middlename">Middle Name</label>
					{{Form::text('middlename', old('middlename'), ['class' => 'form-control text-center text-uppercase', 'maxlength' => '128'])}}
				</div>

				<div class="col-md-2">
					<label><small class="text-dark" id="verifyTooltip">&nbsp;</small></label><br>
					<button type="button" data-toggle="modal" data-target="#verifyApplicant" id="verifyBtn" class="btn btn-dark text-white" disabled="true">CHECK</button>
				</div>
		</div>

		

		<div class="form-group row">
			<div class="col-md-3">
			<label for="birthdate">Birthday<span class="required-field">*</span></label>
				{{Form::date('birthdate', old('birthdate'), ['class' => 'form-control text-center', 'id' => 'birthdate'])}}
			</div>
			<div class="col-md-2">
			<label for="gender">Gender<span class="required-field">*</span></label>
				{{Form::select('gender', array('Female' => 'Female', 'Male' => 'Male'), old('gender'), ['class' => 'form-control text-center'])}}
			</div>
			<div class="col-md-2">
			<label for="marital_status">Marital Status<span class="required-field">*</span></label>
				{{Form::select('marital_status', array('Single' => 'Single', 'Married' => 'Married', 'Widowed' => 'Widowed'), old('marital_status'), ['class' => 'form-control text-center'])}}
			</div>
			<div class="col-md-5">
				<label for="email">Email</label>
				{{Form::text('email', old('email'), ['class' => 'form-control text-center', 'id' => 'email'])}}
			</div>
        </div>

		<div class="form-group row">
			<div class="col-md-2">
				<label for="telephone_no">Telephone No</label>
				{{Form::text('telephone_no', old('telephone_no'), ['class' => 'form-control text-center', 'id' => 'telno'])}}
			</div>
			<div class="col-md-2">
				<label for="mobile_no">Mobile No</label>
				{{Form::text('mobile_no', old('mobile_no'), ['class' => 'form-control text-center', 'id' => 'mobno'])}}
			</div>
			<div class="col-md-8">
				<label for="address">Address (Characters: <span id="addressLength">0/50</span>)<span class="required-field">*</span></label>
				{{Form::textarea('address', old('address'), ['class' => 'form-control text-center text-uppercase', 'rows' => '2', 'id' => 'address', 'oninput' => 'checkAddressLength()', 'maxlength' => '50'])}}
			</div>
        </div>

		  <div class="form-group row">
		    <div class="col-md-4">
			  <label for="passport_no">Passport No<span class="required-field">*</span></label>
              {{Form::text('passport_no', old('passport_no'), ['class' => 'form-control text-center', 'id' => 'passport_no', 'onkeyup' => 'this.value = this.value.toUpperCase()']) }}
			</div>
			<div class="col-md-4">
			  <label for="passport_expiry">Passport Expiry<span class="required-field">*</span></label>
              {{Form::date('passport_expiry', old('passport_expiry'), ['class' => 'form-control text-center', 'id' => 'passport_expiry'])}}
			</div>
			<div class="col-md-4">
			  <label for="departure_date">Expected Departure Date<span class="required-field">*</span></label>
              {{Form::date('departure_date', old('departure_date'), ['class' => 'form-control text-center', 'id' => 'departure_date'])}}
			</div>
          </div>
		  <div class="form-group row">
		    <div class="col-md-12">
              <label for="remarks">Remarks</label>
              {{Form::textarea('remarks', old('remarks'), ['class' => 'form-control text-center', 'rows' => '3', 'id' => 'remarks'])}}
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
							data-handling-fee="{{ $visaType->handling_fee }}">
						{{ $visaType->name }}
						</option>
						@endforeach
					</select>
				</div>

			<div class="col-md-3">
				<label for="visa_price">Visa Price:</label>
				<input type="text" class="form-control text-center" name="visa_price" id="visa_price" readonly/>
			</div>
			<div class="col-md-3">
              <label for="handling_fee">Handling Fee:</label>
              <input type="text" class="form-control text-center" name="handling_price" id="handling_price" readonly/>
			</div>
			<div class="col-md-2">
				<div class="col-md-4"><hr style=""></div>
              <input type="radio" name="promo_code" value="Regular" checked> Regular
			  <input type="radio" name="promo_code" value="Promo"> Promo
			</div>
		</div>

		<div class="form-group row">
		<div class="col-md-3"></div>

		<div class="col-md-3"></div>
          </div>
		  <div class="form-group row">
		    <div class="col-md-12">
				{{Form::hidden('documents_submitted', null, ['id' => 'documents_submitted'])}}
				<label for="documents_submitted">Documents Required:<span class="required-field">*</span></label>
				{{Form::hidden('documents_submitted')}}
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
													<li class='list-group-item'><input type='checkbox' name='submitted_documents' value='{{ $value->id }}' id='{{ $value->id }}'/> {{ $value->name }} </li>                                                    @endforeach
                                                </ul>
												</div>
                                            </td>
                                            <td class="bg-info text-left">
												<div class="scrollable-div">
                                                <ul class="list-group" id="japanese_documents">
                                                    @foreach ($docs_japanese as $value)
													<li class='list-group-item'><input type='checkbox' name='submitted_documents' value='{{ $value->id }}' id='{{ $value->id }}'/> {{ $value->name }} </li>                                                    @endforeach
                                                </ul>
												</div>
                                            </td>
                                            <td class="bg-dark text-left">
												<div class="scrollable-div">
                                                <ul class="list-group" id="foreign_documents">
                                                    @foreach ($docs_foreign as $value)
													<li class='list-group-item'><input type='checkbox' name='submitted_documents' value='{{ $value->id }}' id='{{ $value->id }}'/> {{ $value->name }} </li>                                                    @endforeach
                                                </ul>
												</div>
                                            </td>
                                          </tr>
                                      	</tbody>
                    </table>
				</div>
			</div>
        </div>

		  <div class="row">
		    <div class="col">
              <button type="submit" id="submitApplication" class="btn btn-primary">Submit Visa Application</button>
			  <a href="{{url()->previous()}}" class="btn btn-danger">Back</a>
			</div>
		  </div>
      </form>
  </div>
 </div>
</div>

<div class="modal" id="verifyApplicant">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Past Applications</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body d-flex justify-content-center">
				<div class="spinner-border text-info"></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
		    </div>
		</div>
	</div>
</div>
@endsection


@section('scripts')
<script type="text/javascript">


function displaySelectedValue(select) {
    var pickupMethod = select.value;
    var pickupFeeInput = document.getElementById('pickup_fee');

        if (pickupMethod === 'Courier') {
            var pickupPrice = {{ $pickupPrice }};
            pickupFeeInput.value = pickupPrice;
        } else {
            pickupFeeInput.value = '';
        }
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

$(document).on('keypress', '#birthdate', function(event) {
    var keyPressed = String.fromCharCode(event.charCode || event.which);
    var inputDate = $(this).val() + keyPressed;
    var currentDate = moment().format('YYYY-MM-DD');
    var isValidDate = moment(inputDate, 'YYYY-MM-DD', true).isValid();

    if (!isValidDate || inputDate.length > 10) {
        event.preventDefault();
        return false;
    }

    var year = moment(inputDate).year();
    if (year < 1000 || year > 9999) {
        event.preventDefault();
        return false;
    }

    if (moment(inputDate).isAfter(currentDate)) {
        event.preventDefault();
        return false;
    }
});


	$(document).ready(function()
	{
	$(document).on('keypress', '#createApplication_lastname, #createApplication_firstname, #middlename', function(event) {
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

		$(document).on('keypress', '#visa_price', function(event){
		var regex = new RegExp("^[0-9]+$");
		var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
		if (!regex.test(key)) {
       event.preventDefault();
       return false;
		}
		});

		function enable_verify_button(lastname = '', firstname = '')
		{
			if(lastname != '' && firstname != ''){
				$('#verifyBtn').attr('disabled', false).removeClass('btn-dark').addClass('btn-info');
				$('#verifyTooltip').html('Check past applications');
			} else {
				$('#verifyBtn').attr('disabled', true).removeClass('btn-info').addClass('btn-dark');
				$('#verifyTooltip').html('&nbsp;');
			}
		}


	const customerTypeSelect = document.getElementById('customer_type');
	const customerCompanySelect = document.getElementById('customer_company');
	const partnerCompanies = {!! json_encode($customer_company) !!};

  // Function to update the Customer Company dropdown options
	function updateCustomerCompanyOptions() {
    const selectedType = customerTypeSelect.value;
    const customerNameSelect = document.getElementById('customer_company');
    customerNameSelect.innerHTML = ''; // Clear previous options

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

		$(document).on('click', '#verifyBtn', function(){
			$.ajax({
				url: "../applications/past_applications",
				data: {lastname:$('#createApplication_lastname').val(),firstname:$('#createApplication_firstname').val()},
				success: function(pastApplications)
				{
					$('#verifyApplicant').find('.modal-body').html(pastApplications);
				}
			});
		});

		$(document).on('keyup', '#createApplication_lastname', function(){
			var lastname = $(this).val();
			var firstname = $('#createApplication_firstname').val();

			enable_verify_button(lastname, firstname);
		});

		$(document).on('keyup', '#createApplication_firstname', function(){
			var firstname = $(this).val();
			var lastname = $('#createApplication_lastname').val();

			enable_verify_button(lastname, firstname);
		});


		$(document).on('change', 'input[name="submitted_documents"]', function(){
			var checkboxes = $('input[name="submitted_documents"]:checked');

			var output = [];

			checkboxes.each(function(){
				output.push($(this).val());
			});

			$('input[name="documents_submitted"]').val(output.join(","));
		});

		$(document).on('change', 'input[name="promo_code"]', function(){
			if($("input[name='promo_code']:checked").val() === 'Promo')
			{
				$('#visa_price').attr('readonly', false);
			}
			else
			{
				$('#visa_price').attr('readonly', true);
				$('#visa_type').change();
			}
		});

	});
</script>
@endsection

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>