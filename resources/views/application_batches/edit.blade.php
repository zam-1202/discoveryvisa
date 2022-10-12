@extends('layouts.app')

@php
	$status_array = array();
	foreach($status_list as $status)
	{
		$status_array[$status->id] = $status->description;
	}
@endphp

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
			
			<form method="post" action="{{ route('application_batches.update', $batch->id)}}">
			@method('PATCH')
			@csrf
			
			<div class="card">
				<div class="card-header text-center font-weight-bold bg-primary text-white">
					<h1>Batch No: {{$batch->batch_no}}</h1>
				</div>
				<div class="card-body">
					<div class="form-group row">
						<div class="col-md-3 offset-md-3 text-right">
							<label for="batch_date">Batch Date: </label>
						</div>
						
						<div class="col-md-4">
							<label>{{$batch->batch_date}}</label>
						</div>
					</div>

					<div class="form-group row">
						<div class="col-md-3 offset-md-3 text-right">
							<label for="total_applications">Total Applications: </label>
						</div>
						
						<div class="col-md-4">
							<label>{{$batch->total_applications}}</label>
						</div>
					</div>

					<div class="form-group row">
						<div class="col-md-3 offset-md-3 text-right">
							<label for="status">Status: </label>
						</div>
						
						<div class="col-md-4">
							{{Form::select('status', $status_array, $batch->status, ['class' => 'form-control text-center']) }}
						</div>
					</div>
					
					<div class="form-group row">
						<div class="col-md-3 offset-md-3 text-right">
							<label for="tracking_no">Tracking No: </label>
						</div>
						
						<div class="col-md-4">
							{{Form::text('tracking_no', $batch->tracking_no, ['class' => 'form-control text-center'])}}
						</div>
					</div>
					
					<div class="form-group row">
						<div class="col-md-6 offset-md-6">
							<button type="submit" class="btn btn-primary">Save Changes</button>
						</div>
					</div>
					
					<table class="table table-striped table-hover table-bordered text-center">
						<thead class="thead-dark">
							<tr>
								<th style="width:10%;">No.</th>
								<th style="width:35%;">Applicant</th>
								<th style="width:20%;">Reference No</th>
								<th style="width:10%;">Action</th>
								<th style="width:25%;">Status</th>
							</tr>
						</thead>
						<tbody>
						@if($batch_contents->count() > 0)
							@php
								$i = 1;
							@endphp
							@foreach($batch_contents as $row)
								<tr>
									<td>{{$i}}</td>
									<td>{{$row->lastname}}, {{$row->firstname}} {{$row->middlename}}</td>
									<td>{{$row->reference_no}}
									<td><a href="{{route('applications.edit', $row->id)}}" class="btn btn-primary">Update</a></td>
									<td>
										@if($row->application_status != 2)
										<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#mark_as_incomplete" id="{{$row->id}}" name="incomplete_btn">
											<span class="text-white small">Mark as Incomplete</span>
										</button>
										@else
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
								<td colspan="4" class="text-center font-weight-bold">No Data Found</td>
							</tr>
						@endif
						</tbody>
					</table>
					
					<div class="row">
						<div class="col-md-12 text-center">
							<a class="btn btn-danger" href="{{url('/application_batches')}}">Back</a>
						</div>
					</div>
				</div>
			</div>
			</form>
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
			url: "/applications/mark_as_incomplete",
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