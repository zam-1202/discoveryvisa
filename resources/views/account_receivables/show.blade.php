@php
    $branches = App\Branch::all();
	$branch_lookup = array();
	foreach($branches as $branch){
		$branch_lookup[$branch->code] = $branch->description;
	}

    $application_status_array = array('1' => 'NEW Application', '2' => 'Submitted to Embassy', '3' => 'Received from Embassy', '4' => 'Sent/Claimed by Client');
@endphp

@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-sm-12">
		<div class="row">
			<div class="jumbotron bg-dark text-white text-center col-md-8 offset-md-2">
				<h1>Account Receivables</h1>
			</div>
		</div>

		<br>

		<div class="table-responsive">
            <table class="table table-striped table-hover text-center" id="applicantIndex">
                <thead class="thead-dark">
                    <tr>
                      <th style="width:15%;">Reference Number</th>
                      <th style="width:7.5%;">Status</th>
                      <th style="width:7.5%;">Branch</th>
                      <th style="width:40%;">Name</th>
                      <th style="width:10%;">Amount</th>
                      <th style="width:10%;">Customer Type</th>
                      <th style="width:10%;">Payment Status</th>
                      <th style="width:25%;"></th>
                    </tr>
                </thead>
                <tbody>
                @if($data->count() > 0)
                  @foreach($data as $row)
                  @php
                            $totalAmount = $row->visa_price + $row->handling_price;
                        @endphp
                    <tr>
                        <td>{{ $row->reference_no }}</td>
                        <td>{{ $application_status_array[$row->application_status] }}</td>
                        <td>{{ $branch_lookup[$row->branch] }}</td>
                        <td>{{ $row->lastname }}, {{ $row->firstname }} {{ $row->middlename }}</td>
                        <td>{{ $totalAmount }}</td>
                        <td>{{ $row->customer_type }}</td>
                        <td>{{ $row->payment_status }}</td>
                        <td colspan="2" class="text-center">
                            <button class="btn btn-success" id="confirm_btn">Confirm Payment</button>
                        </td>
                    </tr>
                  @endforeach
                @else
                    <tr>
                        <td colspan='7'> No Data Found </td>
                    </tr>
                @endif
                </tbody>
            </table>

            {!! $data->links() !!}
        </div>

		<div class="row">
			<div class="col-md-12 text-center">
				<a href="{{ route('account_receivables.index')}}" class="btn btn-danger">Back</a>
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
				url: "../account_receivables/show",
				data: {
                    or_number:$('#or_number').val(),
                    reference_no:$('#reference_no').val(),
                    payment_mode:$('#payment_mode').val(),
                    payment_request:$('#payment_request').val()
                },
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