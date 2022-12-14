@extends('layouts.app')

@php
	$visatypearray = array();
	foreach($visatypes as $type)
	{
		$visatypearray[$type->id] = $type->name;
	}

	$application_status_array = array('1' => 'NEW Application', '2' => 'Incomplete', '3' => 'Submitted to Embassy', '4' => 'Received from Embassy', '5' => 'Sent to/Claimed by Client');
@endphp


@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2 text-center">
        <div class="jumbotron bg-dark text-white">
			<h1>Update an Application</h1>
		</div>

        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        <br />
        @endif
        <form method="post" action="{{ route('applications.update', $application->id) }}">
            @method('PATCH')
            @csrf
            <div class="form-group row">
				<div class="col-md-4">
				  <label for="reference_no">Reference No</label>
				  {{Form::text('reference_no', $application->reference_no, ['readonly' => 'readonly', 'class' => 'form-control text-center'])}}
				</div>
				<div class="col-md-4"></div>
				<div class="col-md-4">
				  <label for="application_status">Application Status</label>
				  {{Form::text('application_status', $application_status_array[$application->application_status], ['class' => 'form-control text-center','disabled' => 'disabled'])}}
				  {{Form::hidden('application_status', $application->application_status)}}
				</div>
			</div>

			<div class="form-group row">
				<div class="col-md-4">
				  <label for="branch">Branch</label>
				  {{Form::text('branch', $application->branch, ['class' => 'form-control text-center', 'readonly' => 'readonly']) }}
				</div>
				<div class="col-md-4"></div>
				<div class="col-md-4">
				  <label for="tracking_no">Tracking No</label>
				  {{Form::text('tracking_no', $application->tracking_no, ['class' => 'form-control text-center'])}}
				</div>
			</div>

			<div class="form-group row">
				<div class="col-md-4">
				  <label for="customer_type">Customer Type</label>
				  {{Form::select('customer_type', $customer_type_array, $application->customer_type, ['class' => 'form-control', 'id' => 'customer_type'])}}
				</div>
				<div class="col-md-8">
				  <label for="customer_company">Customer Company</label>
				  <select class="form-control" name="customer_company" id="customer_company" disabled>
				  </select>
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
					<div class="col-md-9">
					  <label for="lastname">Last Name</label>
					  {{Form::text('lastname', $application->lastname, ['class' => 'form-control text-center text-uppercase'])}}
					</div>
				</div>

				<div class="form-group row">
					<div class="col-md-9">
					  <label for="first_name">First Name</label>
					  {{Form::text('firstname', $application->firstname, ['class' => 'form-control text-center text-uppercase'])}}
					</div>
				</div>

				<div class="form-group row">
					<div class="col-md-9">
					  <label for="middlename">Middle Name</label>
					  {{Form::text('middlename', $application->middlename, ['class' => 'form-control text-center text-uppercase'])}}
					</div>
				</div>

				<div class="form-group row">
					<div class="col-md-6">
					  <label for="birthdate">Birthdate</label>
					  {{Form::date('birthdate', $application->birthdate, ['class' => 'form-control text-center'])}}
					</div>
					<div class="col-md-3">
					  <label for="gender">Gender</label>
					  {{Form::select('gender', array('Female' => 'Female', 'Male' => 'Male'), $application->gender, ['class' => 'form-control'])}}
					</div>
					<div class="col-md-3">
					  <label for="marital_status">Marital Status</label>
					  {{Form::select('marital_status', array('Single' => 'Single', 'Married' => 'Married', 'Widowed' => 'Widowed'), $application->marital_status, ['class' => 'form-control'])}}
					</div>
				</div>

				<div class="form-group row">
					<div class="col-md-6">
					  <label for="email">Email:</label>
					  {{Form::text('email', $application->email, ['class' => 'form-control text-center'])}}
					</div>
					<div class="col-md-3">
					  <label for="telephone_no">Telephone No</label>
					  {{Form::text('telephone_no', $application->telephone_no, ['class' => 'form-control text-center'])}}
					</div>
					<div class="col-md-3">
					  <label for="mobile_no">Mobile No:</label>
					  {{Form::text('mobile_no', $application->mobile_no, ['class' => 'form-control text-center'])}}
					</div>
				</div>

				<div class="form-group row">
					<div class="col-md-12">
					  <label for="address">Address</label>
					  {{Form::textarea('address', $application->address, ['class' => 'form-control text-center text-uppercase', 'rows' => '3'])}}
					</div>
				</div>

				<div class="form-group row">
					<div class="col-md-4">
					  <label for="passport_no">Passport No</label>
					  {{Form::text('passport_no', $application->passport_no, ['class' => 'form-control text-center'])}}
					</div>
					<div class="col-md-4">
					  <label for="passport_expiry">Passport Expiry</label>
					  {{Form::date('passport_expiry', $application->passport_expiry, ['class' => 'form-control text-center'])}}
					</div>
					<div class="col-md-4">
					  <label for="departure_date">Expected Departure Date</label>
					  {{Form::date('departure_date', $application->departure_date, ['class' => 'form-control text-center'])}}
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
					<div class="col-md-3"></div>
					<div class="col-md-6">
					  <label for="visa_type">Visa Type:</label>
					  {{Form::select('visa_type', $visatypearray, $application->visa_type, ['class' => 'form-control', 'id' => 'visa_type'])}}
					</div>
					<div class="col-md-3"></div>
				</div>

				<div class="form-group row">
					<div class="col-md-3"></div>
					<div class="col-md-6">
					  <label for="visa_price">Visa Price:</label>
					  {{Form::text('visa_price', $application->visa_price, ['class' => 'form-control text-center', 'id' => 'visa_price', 'readonly' => 'readonly'])}}
					  <span id="discount-text" class="text-success"></span>
					</div>
					<div class="col-md-3"></div>
				</div>
				<div class="form-group row">
					<div class="col-md-3"></div>
					<div class="col-md-6">
					  <label for="promo_code">Promo Code:</label>
					  {{Form::text('promo_code', $application->promo_code, ['class' => 'form-control text-center text-uppercase', 'id' => 'promo_code', 'placeholder' => '(optional)'])}}
					  <a class="btn btn-success text-white" id="promo_code_btn">Use Promo Code</a>
					</div>
					<div class="col-md-3">
						{{Form::hidden('discount_amount', $application->discount_amount)}}
						{{Form::hidden('discount', 0)}}
					</div>
				</div>

				<div class="form-group row">
					<div class="col-md-12">
					  <label for="documents_submitted">Documents Required:</label>
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
										<ul class="list-group" id="filipino_documents">
										</ul>
									</td>
									<td class="bg-info text-left">
										<ul class="list-group" id="japanese_documents">
										</ul>
									</td>
									<td class="bg-dark text-left">
										<ul class="list-group" id="foreign_documents">
										</ul>
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

	$(document).ready(function(){


		var visaTypeArray = {!! $visatypes->toJson() !!};

		populate_partner_companies("{{$application->customer_type}}");
		get_promo_code();

        var visaType = $('#visa_type').find('option:selected').val();
		on_change_visa_type(visaType,true);

		function populate_partner_companies(filterType = '')
		{
			$('#customer_company').html('');
			$('#customer_company').attr('disabled', true);

			if(filterType == 'PIATA' || filterType == 'PTAA' && filterType == 'Corporate')
			{
				$.ajax({
					url: "../../partner_companies/getpartners",
					data: {filterType:filterType},
					success: function(data)
					{
						var options = '';
						var selected = '';

						data.forEach(function(row){
							selected = '';
							if("{{$application->customer_company}}" == row.id) selected = "selected ";
							options += "<option " + selected + "value='" + row.id + "'>" + row.name + "</option>"
						});

						$('#customer_company').attr('disabled', false);
						$('#customer_company').html(options);
					}
				});
			}
		}

		function on_change_visa_type(visaType,fromUser)
		{
			var selectedVisaType = $.grep(visaTypeArray, function(obj){return obj.id == visaType;})[0];
			get_required_documents(selectedVisaType);
			restore_checkboxes();
			$('#visa_price').val(selectedVisaType.price);
			get_promo_code();
		}

		function get_required_documents(selectedVisaType)
		{
			var filipinoDocuments = selectedVisaType.filipino_documents.split(",");
			var japaneseDocuments = selectedVisaType.japanese_documents.split(",");
			var foreignDocuments = selectedVisaType.foreign_documents.split(",");

			$('#filipino_documents').html(generate_checkboxes(filipinoDocuments));
			$('#japanese_documents').html(generate_checkboxes(japaneseDocuments));
			$('#foreign_documents').html(generate_checkboxes(foreignDocuments));
		}

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

		function update_visa_price()
		{
			if($("input[name='promo_code']:checked").val() === 'Promo')
			{
				$('#visa_price').attr('readonly', false);
			}
			else
			{
				$('#visa_price').attr('readonly', true);
				$('#visa_type').change();
			}
		}

		function get_promo_code()
		{
			var promo_code = $("#promo_code").val().toUpperCase();
			if(promo_code != "")
			{
				var id = {{$application->id}};
				$.ajax({
					url: "../redeem_promo_code",
					data: {id:id, promo_code:promo_code, page:"edit"},
					success: function(data)
					{
						apply_promo_code(data[0]);
					}
				});
			}
			else {
				apply_promo_code("");
			}
		}

		function apply_promo_code(discount)
		{
            var currentVisaType = {{$application->visa_type}};
			var visaType = $('#visa_type').find('option:selected').val();
			var selectedVisaType = $.grep(visaTypeArray, function(obj){return obj.id == visaType;})[0];
			if(discount != "")
			{
				var discount_amount = 0;

				if(discount.substring(discount.length-1, discount.length) == '%')
				{
					discount_amount = selectedVisaType.price * (Number(discount.substring(0, discount.length-1))/100);
				} else {
					discount_amount = Number(discount);
				}
				$('#discount-text').html('-'.concat(discount_amount.toFixed(2)));

                if (currentVisaType == selectedVisaType.id) {
                    $('#visa_price').val(({{ $application->visa_price }} - discount_amount).toFixed(2));
                } else {
                    $('#visa_price').val((selectedVisaType.price - discount_amount).toFixed(2));
                }

			}
			else
			{
				$('#discount-text').html('');
                if (currentVisaType == selectedVisaType.id) {
                    $('#visa_price').val({{ $application->visa_price }});
                } else {
                    $('#visa_price').val(selectedVisaType.price);
                }

			}
		}

		$(document).on('change', '#customer_type', function(){
			var filterType = $(this).val();
			populate_partner_companies(filterType);
		});

		$(document).on('change', '#visa_type', function(){
			var visaType = $(this).find('option:selected').val();
			on_change_visa_type(visaType,true);
		});

		$(document).on('change', 'input[name="submitted_documents"]', function(){
			var checkboxes = $('input[name="submitted_documents"]:checked');

			var output = [];

			checkboxes.each(function(){
				output.push($(this).val());
			});

			$('input[name="documents_submitted"]').val(output.join(","));
		});

		$(document).on('click', '#promo_code_btn', function(){
			get_promo_code();
		});

	});

</script>
@endsection
