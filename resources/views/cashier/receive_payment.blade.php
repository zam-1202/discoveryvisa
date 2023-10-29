@extends('layouts.app')
@section('content')
<div class="container">
	<div class="row justify-content-center">
		<div class="card col-md-12">
			<div class="row card-header bg-success text-white font-weight-bold"><h4>Receive Payment</h4></div>
		
			<div class="row card-body align-items-center">
			<a href="{{ route('cashier.unpaidList')}}" class="btn btn-danger">Back</a>
			<div class="col-md-3 text-right font-weight-bold">Enter Reference No:</div>
				<div class="col-md-6">{{Form::text('reference_no', $referenceNo, ['class' => 'form-control', 'id' => 'search_string']) }}</div>
				<div class="col-md-2"><a type="button" class="btn btn-success text-white" id="receive_payment_btn">SEARCH</a></div>
			</div>
			<div class="card-footer mb-3">
				<div id="receive_payment_form">
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="confirm_payment_modal">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header bg-success text-white font-weight-bold">
				<h4>Payment Confirmation</h4>
			</div>
			<div class="modal-body text-center font-weight-bold">
				<span id="confirm_payment_message">
				</span>
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

	$(document).ready(function(){
		$('[data-toggle="tooltip"]').tooltip();

		$(document).on('click','#receive_payment_btn', function(){
			$.ajax({
				url: "../cashier/confirm_payment",
				data: {searchString:$('#search_string').val()},
				success: function(applicationForm){
					$('#receive_payment_form').html(applicationForm);
				}
			});
		});

		$(document).ready(function() {
		// Trigger the click event of the SEARCH button
		$('#receive_payment_btn').trigger('click');
	});


		$(document).on('click','#close_btn', function(){
			$('#search_string').val('');
			$('#receive_payment_form').html('');
		});

		$(document).on('click', '#confirm_btn', function(){
			$('#vpr_number').tooltip('hide');
			if($('#vpr_number').val() == '')
			{
				$('#vpr_number').tooltip('show');
				$('#vpr_number').focus();
				return;
			}
			$.ajax({
				url: "../cashier/customer_payment",
				data: {or_number:$('#or_number').val(),reference_no:$('#reference_no').val(),payment_mode:$('#payment_mode').val(), payment_request:$('#payment_request').val()},
				success: function(message){
					$('#confirm_payment_message').html(message);
					$('#confirm_payment_modal').modal();
					$('#btn_receipt').removeClass('disabled');
				}
			});
		});

		$('#confirm_payment_modal').on('hidden.bs.modal', function(){
			$('#receive_payment_btn').click();
		});

	});

</script>

@endsection
