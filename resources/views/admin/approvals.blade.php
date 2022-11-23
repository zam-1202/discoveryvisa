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
				<div class="card-header bg-primary text-white text-center"><h1>Approval Requests</h1></div>
				<div class="card-body">
					<table class="table table-hover table-bordered text-center">
						<thead class="thead-dark">
							<th style="width: 20%;" class="align-middle">Application ID</th>
							<th style="width: 15%;" class="align-middle">Request Type</th>
							<th style="width: 15%;" class="align-middle">Requested By</th>
							<th style="width: 20%;" class="align-middle">Request Date</th>
							<th style="width: 30%;" class="align-middle">Action</th>
						</thead>
						<tbody>
							@if($approval_requests->count() > 0)
								@foreach($approval_requests as $req)
									<tr>
										<td><a href="{{route('applications.edit', $req->application_id)}}">Application#{{$req->application_id}}</a></td>
										<td>{{$req->request_type}}</td>
										<td>{{$req->requested_by}}</td>
										<td>{{$req->request_date}}</td>
										<td>
											<a reqid="{{$req->id}}" title="Approval" class="btn btn-success text-white">Approve</a>
											<a reqid="{{$req->id}}" title="Rejection" class="btn btn-danger text-white">Reject</a>
										</td>
									</tr>
								@endforeach
							@else
								<tr>
									<td colspan="5" class="text-center font-weight-bold">No Data Found</td>
								</tr>
							@endif
						</tbody>
					</table>

					<div class="text-center">
						<a href="{{url('/')}}" class="btn btn-danger">Back</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal" id="confirm_modal">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header bg-success text-white">
				<span class="modal-title">Confirmation Screen</span>
				<button type="button" class="close" data-dismiss="modal">x</button>
			</div>
			<div class="modal-body d-flex justify-content-center">
				<h4 class="font-weight-bold" id="confirm_msg"></h4>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-success" id="submit_btn" data-action="" data-id="">Confirm</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
		    </div>
		</div>
	</div>
</div>

@endsection

@section('scripts')

<script type="text/javascript">

	$(document).ready(function(){

		$(document).on('click','.btn', function(){
			if($(this).attr('reqid') != null)
			{
				$action = $(this).attr('title');
				$id = $(this).attr('reqid');
				$("#submit_btn").attr('data-action', $action);
				$("#submit_btn").attr('data-id', $id);
				$("#confirm_msg").html('Confirm '.concat($action, '?'));
				$("#confirm_modal").modal("show");
			}
		});

		$(document).on('click', '#submit_btn', function()
		{
			$.ajax({
				url: "../applications/mark_as_incomplete",
				data: {request_type:'Mark as Incomplete',application_id:$(this).attr("data-id"), approval_code:$(this).attr("data-action")},
				success: function()
				{
					location.reload(true);
				}
			});
		});

	});
</script>

@endsection
