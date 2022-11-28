@extends('layouts.app')

@section('content')
<div class="row">
 <div class="col-sm-8 offset-sm-2 text-center">
  <div class="jumbotron bg-dark text-white">
    <h1>Create a New Visa Application</h1>
  </div>
  <div>
    @if ($errors->any())
      <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
        </ul>
      </div><br />
    @endif
      <form method="post" action="{{ route('applications.store') }}">
          @csrf
		  <div class="form-group row">
			<div class="col-md-4">
              <label for="customer_type">Customer Type</label>
			  <select class="form-control" name="customer_type" id="customer_type">
			    <option value="Walk-In">Walk-In</option>
				<option value="PIATA">PIATA</option>
				<option value="PTAA">PTAA</option>
				<option value="Corporate">Corporate</option>
			  </select>
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
			  {{Form::text('lastname', old('lastname'), ['class' => 'form-control text-center text-uppercase', 'id' => 'createApplication_lastname'])}}
			</div>
	      </div>
		  <div class="form-group row">
			<div class="col-md-9">
			  <label for="first_name">First Name</label>
			  {{Form::text('firstname', old('firstname'), ['class' => 'form-control text-center text-uppercase', 'id' => 'createApplication_firstname'])}}
			</div>
			<div class="col-md-3">
			  <label><small class="text-dark" id="verifyTooltip">&nbsp;</small></label><br>
			  <button type="button" data-toggle="modal" data-target="#verifyApplicant" id="verifyBtn" class="btn btn-dark text-white" disabled="true">CHECK<i class="material-icons" style="font-size:12px;">cancel</i></button>
			</div>
		  </div>
		  <div class="form-group row">
		    <div class="col-md-9">
			  <label for="middlename">Middle Name</label>
              {{Form::text('middlename', old('middlename'), ['class' => 'form-control text-center text-uppercase'])}}
			</div>
		  </div>
		  <div class="form-group row">
			<div class="col-md-6">
              <label for="birthdate">Birthdate</label>
              {{Form::date('birthdate', old('birthdate'), ['class' => 'form-control text-center'])}}
			</div>
			<div class="col-md-3">
              <label for="gender">Gender</label>
              {{Form::select('gender', array('Female' => 'Female', 'Male' => 'Male'), old('gender'), ['class' => 'form-control text-center'])}}
			</div>
			<div class="col-md-3">
			  <label for="marital_status">Marital Status</label>
              {{Form::select('marital_status', array('Single' => 'Single', 'Married' => 'Married', 'Widowed' => 'Widowed'), old('marital_status'), ['class' => 'form-control text-center'])}}
			</div>
          </div>
		  <div class="form-group row">
		    <div class="col-md-6">
		      <label for="email">Email</label>
              {{Form::text('email', old('email'), ['class' => 'form-control text-center'])}}
			</div>
			<div class="col-md-3">
              <label for="telephone_no">Telephone No</label>
              {{Form::text('telephone_no', old('telephone_no'), ['class' => 'form-control text-center'])}}
			</div>
			<div class="col-md-3">
			  <label for="mobile_no">Mobile No</label>
              {{Form::text('mobile_no', old('mobile_no'), ['class' => 'form-control text-center'])}}
			</div>
          </div>
		  <div class="form-group row">
		    <div class="col-md-12">
              <label for="address">Address</label>
              {{Form::textarea('address', old('address'), ['class' => 'form-control text-center text-uppercase', 'rows' => '3'])}}
			</div>
          </div>
		  <div class="form-group row">
		    <div class="col-md-4">
              <label for="passport_no">Passport No</label>
              {{Form::text('passport_no', old('passport_no'), ['class' => 'form-control text-center'])}}
			</div>
			<div class="col-md-4">
			  <label for="passport_expiry">Passport Expiry</label>
              {{Form::date('passport_expiry', old('passport_expiry'), ['class' => 'form-control text-center'])}}
			</div>
			<div class="col-md-4">
			  <label for="departure_date">Expected Departure Date</label>
              {{Form::date('departure_date', old('departure_date'), ['class' => 'form-control text-center'])}}
			</div>
          </div>
		  <div class="form-group row">
		    <div class="col-md-12">
              <label for="remarks">Remarks</label>
              {{Form::textarea('remarks', old('remarks'), ['class' => 'form-control text-center', 'rows' => '3'])}}
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
              <select class="form-control" name="visa_type" id="visa_type">
			    <option disabled selected>--- Select a Visa Type ---</option>
			    @foreach ($visatypes as $visatype)
				<option value="{{$visatype->id}}">{{$visatype->name}}</option>
				@endforeach
			  </select>
			</div>
			<div class="col-md-3"></div>
          </div>
		  <div class="form-group row">
		    <div class="col-md-3"></div>
			<div class="col-md-6">
              <label for="visa_price">Visa Price:</label>
              <input type="text" class="form-control text-center" name="visa_price" id="visa_price" readonly/>
			</div>
			<div class="col-md-3"></div>
          </div>
		  <div class="form-group row">
		    <div class="col-md-3"></div>
			<div class="col-md-6">
              <input type="radio" name="promo_code" value="Regular" checked> Regular
			  <input type="radio" name="promo_code" value="Promo"> Promo
			</div>
			<div class="col-md-3"></div>
          </div>
		  <div class="form-group row">
		    <div class="col-md-12">
              <label for="documents_submitted">Documents Required:</label>
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
		  <div class="row">
		    <div class="col">
              <button type="submit" class="btn btn-primary">Submit Visa Application</button>
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

	$(document).ready(function()
	{

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

		function populate_partner_companies(filterType = '')
		{
			$.ajax({
				url: "../partner_companies/getpartners",
				data: {filterType:filterType},
				success: function(data)
				{
					var options = '';
					data.forEach(function(row){
						options += "<option value='" + row.id + "'>" + row.name + "</option>"
					});

					$('#customer_company').attr('disabled', false);
					$('#customer_company').html(options);
				}
			});
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

		$(document).on('change', '#customer_type', function(){
			var filterType = $(this).val();

			$('#customer_company').html('');
			$('#customer_company').attr('disabled', true);

			if(filterType != 'Walk-In'){
				populate_partner_companies(filterType);
			}
		});

		$(document).on('change', '#visa_type', function(){
			var visaType = $(this).find('option:selected').val();
			var typeArray = {!! $visatypes->toJson() !!};
			var selectedVisaType = $.grep(typeArray, function(obj){return obj.id == visaType;})[0];

			$('#visa_price').val(selectedVisaType.price);

			var filipinoDocuments = selectedVisaType.filipino_documents.split(",");
			var japaneseDocuments = selectedVisaType.japanese_documents.split(",");
			var foreignDocuments = selectedVisaType.foreign_documents.split(",");

			$('#filipino_documents').html(generate_checkboxes(filipinoDocuments));
			$('#japanese_documents').html(generate_checkboxes(japaneseDocuments));
			$('#foreign_documents').html(generate_checkboxes(foreignDocuments));
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
