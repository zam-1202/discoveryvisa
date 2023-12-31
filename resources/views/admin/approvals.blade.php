@extends('layouts.app')

@section('content')


<div class="container">
	<div class="row justify-content-center">
		<div class="col-md-12">
			@if (session('status'))
				<div class="alert alert-primary alert-dismissible fade show text-center" role="alert">
					<strong>{{ session('status') }}</strong>
					<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
			@endif

			<div class="card">
				<div class="card-header bg-primary text-white text-center">
					<h1>Approval Requests</h1>
					<div class="text-white text-center">
						<!-- <h4 style="font-size: 14px;">
							<span class="custom-success" style="background-color: #28a745; color: #fff; padding: 4px 8px; border-radius: 4px;">Approve is INCOMPLETE</span>
							
							<span class="custom-danger" style="background-color: #dc3545; color: #fff; padding: 4px 8px; border-radius: 4px;">Reject is NEW Application</span>
						</h4> -->
					</div>
				</div>
			</div>


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
									@if ($req->action !== 'APPROVED' && $req->action !== 'REJECTED')
										<tr>
											<td><a href="{{route('applications.edit', $req	->application_id)}}">Application#{{$req->application_id}}</a></td>
											<td>{{$req->request_type}}</td>
											<td>{{$req->requested_by}}</td>
											<td>{{$req->request_date}}</td>
											<td>
												<a reqid="{{$req->id}}" title="Approval" class="btn btn-success text-white">Approve</a>
												<a reqid="{{$req->id}}" title="Rejection" class="btn btn-danger text-white">Reject</a>
											</td>
										</tr>
									@endif
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

<div class="modal fade" id="confirm_modal">
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

    // $(document).on('click', '#submit_btn', function()
    // {
    //     $.ajax({
    //         url: "../applications/mark_as_completed",
    //         data: {
    //             request_type: 'Mark as Completed',
    //             application_id: $(this).attr("data-id"),
    //             approval_code: $(this).attr("data-action")
    //         },
    //         success: function() {
    //             location.reload(true);
    //         }
    //     });
    // });
});

</script>

@endsection
