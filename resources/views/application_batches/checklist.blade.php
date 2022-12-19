@extends('layouts.app')

@section('content')

<div class="container">
	<div class="row justify-content-center">
		<div class="col-md-8">
			@if (session('status'))
				<div class="alert alert-primary alert-dismissible fade show text-center" role="alert">
					<strong>{{ session('status') }}</strong>
					<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
			@endif
			<div class="card">
				<div class="card-header bg-primary text-white font-weight-bold">
					<h1 class="text-center">{{$branch}} Checklist for {{$date}}</h1>
				</div>
				<div class="card-body">
					<div class="row px-3">
						<div class="col-md-12 text-right p-1">
							<a href="{{ route('download_checklist_pdf') }}" class="btn btn-success">Download PDF <span style="font-size:20px;" class="material-icons align-bottom">&#xe2c4;</span></a>
						</div>
						<div class="table-responsive">
							<table class="table table-striped table-bordered text-center">
								<tr class="bg-dark text-white">
									<td style="width:10%;" class="font-weight-bold">No.</td>
									<td style="width:40%;" class="font-weight-bold">Applicant</td>
									<td style="width:25%;" class="font-weight-bold">Reference No</td>
									<td style="width:25%;"> </td>
								</tr>
								@if($walkin_applications->count() > 0)
									@php
										$i = 1;
									@endphp
									@foreach($walkin_applications as $walkin)
										<tr>
											<td>{{ $i }}</td>
											<td>{{ $walkin->lastname }}, {{ $walkin->firstname }} {{ $walkin->middlename }}</td>
											<td>{{ $walkin->reference_no }}</td>
											<td>
												@if($walkin->application_status == 1)
                                                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#mark_as_incomplete" id="{{$walkin->id}}" name="incomplete_btn">
                                                        <span class="text-white small">Mark as Incomplete</span>
                                                    </button>
                                                @elseif ($walkin->application_status == 10)
                                                    <span class="font-weight-bold text-danger">Pending Approval</span>
                                                @elseif ($walkin->application_status == 9)
                                                    <span class="font-weight-bold text-danger">Incomplete</span>
                                                @endif
											</td>
										</tr>
										@php
											$i++;
										@endphp
									@endforeach
								@else
									<tr>
										<td>-</td>
										<td>-</td>
										<td>-</td>
										<td>-</td>
									</tr>
								@endif
								<tr class="bg-dark text-white">
									<td style="width:10%;" class="font-weight-bold">No.</td>
									<td style="width:40%;" class="font-weight-bold">PIATA</td>
									<td style="width:25%;" class="font-weight-bold">Reference No</td>
									<td style="width:25%;"> </td>
								</tr>
								@if($piata_applications->count() > 0)
									@php
										$i = 1;
									@endphp
									@foreach($piata_applications as $piata)
										<tr>
											<td>{{ $i }}</td>
											<td>{{ $piata->lastname }}, {{ $piata->firstname }} {{ $piata->middlename }}</td>
											<td>{{ $piata->reference_no }}</td>
											<td>
												@if($piata->application_status == 1)
                                                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#mark_as_incomplete" id="{{$piata->id}}" name="incomplete_btn">
                                                        <span class="text-white small">Mark as Incomplete</span>
                                                    </button>
                                                @elseif ($piata->application_status == 10)
                                                    <span class="font-weight-bold text-danger">Pending Approval</span>
                                                @elseif ($piata->application_status == 9)
                                                    <span class="font-weight-bold text-danger">Incomplete</span>
                                                @endif
											</td>
										</tr>
										@php
											$i++;
										@endphp
									@endforeach
								@else
									<tr>
										<td>-</td>
										<td>-</td>
										<td>-</td>
										<td>-</td>
									</tr>
								@endif
								<tr class="bg-dark text-white">
									<td style="width:10%;" class="font-weight-bold">No.</td>
									<td style="width:40%;" class="font-weight-bold">PTAA</td>
									<td style="width:25%;" class="font-weight-bold">Reference No</td>
									<td style="width:25%;"> </td>
								</tr>
								@if($ptaa_applications->count() > 0)
									@php
										$i = 1;
									@endphp
									@foreach($ptaa_applications as $ptaa)
										<tr>
											<td>{{ $i }}</td>
											<td>{{ $ptaa->lastname }}, {{ $ptaa->firstname }} {{ $ptaa->middlename }}</td>
											<td>{{ $ptaa->reference_no }}</td>
											<td>
												@if($ptaa->application_status == 1)
                                                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#mark_as_incomplete" id="{{$ptaa->id}}" name="incomplete_btn">
                                                        <span class="text-white small">Mark as Incomplete</span>
                                                    </button>
                                                @elseif ($ptaa->application_status == 10)
                                                    <span class="font-weight-bold text-danger">Pending Approval</span>
                                                @elseif ($ptaa->application_status == 9)
                                                    <span class="font-weight-bold text-danger">Incomplete</span>
                                                @endif
											</td>
										</tr>
										@php
											$i++;
										@endphp
									@endforeach
								@else
									<tr>
										<td>-</td>
										<td>-</td>
										<td>-</td>
										<td>-</td>
									</tr>
								@endif
								<tr class="bg-dark text-white">
									<td style="width:10%;" class="font-weight-bold">No.</td>
									<td style="width:40%;" class="font-weight-bold">Corporate</td>
									<td style="width:25%;" class="font-weight-bold">Reference No</td>
									<td style="width:25%;"> </td>
								</tr>
								@if($corporate_applications->count() > 0)
									@php
										$i = 1;
									@endphp
									@foreach($corporate_applications as $corporate)
										<tr>
											<td>{{ $i }}</td>
											<td>{{ $corporate->lastname }}, {{ $corporate->firstname }} {{ $corporate->middlename }}</td>
											<td>{{ $corporate->reference_no }}</td>
											<td>
                                                @if($corporate->application_status == 1)
                                                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#mark_as_incomplete" id="{{$corporate->id}}" name="incomplete_btn">
                                                        <span class="text-white small">Mark as Incomplete</span>
                                                    </button>
                                                @elseif ($corporate->application_status == 10)
                                                    <span class="font-weight-bold text-danger">Pending Approval</span>
                                                @elseif ($corporate->application_status == 9)
                                                    <span class="font-weight-bold text-danger">Incomplete</span>
                                                @endif
											</td>
										</tr>
										@php
											$i++;
										@endphp
									@endforeach
								@else
									<tr>
										<td>-</td>
										<td>-</td>
										<td>-</td>
										<td>-</td>
									</tr>
								@endif
                                <tr class="bg-dark text-white">
									<td style="width:10%;" class="font-weight-bold">No.</td>
									<td style="width:40%;" class="font-weight-bold">POEA</td>
									<td style="width:25%;" class="font-weight-bold">Reference No</td>
									<td style="width:25%;"> </td>
								</tr>
                                @if($poea_applications->count() > 0)
									@php
										$i = 1;
									@endphp
									@foreach($poea_applications as $poea)
										<tr>
											<td>{{ $i }}</td>
											<td>{{ $poea->lastname }}, {{ $poea->firstname }} {{ $poea->middlename }}</td>
											<td>{{ $poea->reference_no }}</td>
											<td>
												@if($poea->application_status == 1)
                                                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#mark_as_incomplete" id="{{$poea->id}}" name="incomplete_btn">
                                                        <span class="text-white small">Mark as Incomplete</span>
                                                    </button>
                                                @elseif ($poea->application_status == 10)
                                                    <span class="font-weight-bold text-danger">Pending Approval</span>
                                                @elseif ($poea->application_status == 9)
                                                    <span class="font-weight-bold text-danger">Incomplete</span>
                                                @endif
											</td>
										</tr>
										@php
											$i++;
										@endphp
									@endforeach
								@else
									<tr>
										<td>-</td>
										<td>-</td>
										<td>-</td>
										<td>-</td>
									</tr>
								@endif
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal" id="mark_as_incomplete">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header bg-secondary text-white">
				<h4 class="modal-title">Mark as Incomplete</h4>
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
$(document).ready(function(){

	function submit_incomplete_form(){
		$.ajax({
			url: "../applications/mark_as_incomplete",
			data: {request_type:'Mark as Incomplete',application_id:$('#selected_application').val(), approval_code:$('#approval_code').val()},
			success: function()
			{
				location.reload(true);
			}
		});
	}

	$(document).on('click','button[name="incomplete_btn"]',function(){
		var current_row = $(this).closest('tr');
		var incomplete_form_html = "<div class='container'>" +
								   "<input type='hidden' value='" + $(this).attr('id') + "' id='selected_application'>" +
								   "<div class='row p-1'>" +
								   "<div class='col-md-4 text-right'>Reference No: </div>" +
								   "<div class='col-md-8 text-center font-weight-bold'>" + current_row.find('td:nth-child(3)').html() + "</div>" +
								   "</div>" +
								   "<div class='row p-1'>" +
								   "<div class='col-md-4 text-right'>Name: </div>" +
								   "<div class='col-md-8 text-center font-weight-bold'>" + current_row.find('td:nth-child(2)').html() + "</div>" +
								   "</div>" +
								   "<div class='row p-1'>" +
								   "<div class='col-md-4 text-right'>Approval Code: </div>" +
								   "<div class='col-md-8'><input class='form-control text-center' type='text' id='approval_code'></div>" +
								   "</div>" +
								   "<div class='row p-1'>" +
								   "<div class='col-md-8 offset-md-4 text-center'><a class='btn btn-success text-white' id='incomplete_form_btn'>Mark as Incomplete</a></div>" +
								   "</div>" +
								   "</div>";
		$('#mark_as_incomplete div div.modal-body').html(incomplete_form_html);
	});

	$(document).on('click','#incomplete_form_btn', function(){
		submit_incomplete_form();
	});

	$('#mark_as_incomplete').on('hidden.bs.modal', function(){
		$('#mark_as_incomplete div div.modal-body').html('<div class="spinner-border text-info"></div>');
	});
});
</script>

@endsection
