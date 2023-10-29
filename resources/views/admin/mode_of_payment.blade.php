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
				<div class="card-header bg-primary text-white text-center font-weight-bold">
					<h1>List of Mode of Payments</h1>
				</div>
				<div class="card-body">
					<div class="row p-1">
						<div class="col-md-12 text-right">
                            <a href="" class="btn btn-success" data-toggle="modal" data-target="#add_mode_of_payment_modal">Add</a>
						</div>
					</div>

					<table class="table table-hover table-bordered text-center">
						<thead class="thead-dark">
							<tr>
								<th style="width:80%;">Name</th>
								<th style="width:20%;"></th>
							</tr>
						</thead>
						<tbody>
							@if($result->count() > 0)
								@foreach($result as $row)
									<tr>
										<td>{{$row->name}}</td>
										<td><a href="" class="btn btn-primary" data-toggle="modal" data-target="#update_mode_of_payment_modal" data-id="{{ $row->id }}" data-name="{{ $row->name }}">Update</a></td>
									</tr>
								@endforeach
							@else
								<tr>
									<td colspan="2" class="font-weight-bold">No Data Found</td>
								</tr>
							@endif
						</tbody>
					</table>

					{!! $result->links() !!}

					<div class="row">
						<div class="col-md-12 text-center">
							<a href="{{url('/')}}" class="btn btn-danger">Back</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal" id="add_mode_of_payment_modal">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header bg-success text-white">
				<h4 class="modal-title">Add New Mode of Payment</h4>
				<button type="button" class="close" data-dismiss="modal">x</button>
			</div>
			<div class="modal-body d-flex justify-content-center">
				<div class="container">
					<div class="form-group row">
						<div class="col-md-12">
							<label><small class="text-danger" id="errorMsg">&nbsp;</small></label>
						</div>
					</div>
					<div class="form-group row">
						<div class="col-md-2 text-right"><label>Name</label></div>
						<div class="col-md-10">{{Form::text('name', '', ['class' => 'form-control', 'id' => 'payment_name'])}}</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-success" id="submit_mode_of_payment_btn">Submit</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
		    </div>
		</div>
	</div>
</div>

<div class="modal" id="update_mode_of_payment_modal">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header bg-success text-white">
				<h4 class="modal-title">Update Mode of Payment</h4>
				<button type="button" class="close" data-dismiss="modal">x</button>
			</div>
			<div class="modal-body d-flex justify-content-center">
				<div class="container">
                    <input type="hidden" id="update_mode_of_payment_id" value="" />
					<div class="form-group row">
						<div class="col-md-12">
							<label><small class="text-danger" id="update_errorMsg">&nbsp;</small></label>
						</div>
					</div>
					<div class="form-group row">
						<div class="col-md-2 text-right"><label>Name</label></div>
						<div class="col-md-10">{{Form::text('name', '', ['class' => 'form-control', 'id' => 'update_payment_name'])}}</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-success" id="update_mode_of_payment_btn">Update</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
		    </div>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
    $(document).ready(function(){
        $(document).on('shown.bs.modal', '#add_mode_of_payment_modal' , function (event) {
            $('#payment_name').val('');
            $("#errorMsg").html('');
        });

        $(document).on('shown.bs.modal', '#update_mode_of_payment_modal' , function (event) {
            let button = $(event.relatedTarget); // Button that triggered the modal
            let id = button.attr('data-id');
            let name = button.attr('data-name');

            $('#update_mode_of_payment_id').val(id);
            $('#update_payment_name').val(name);
            $("#update_errorMsg").html('');
        });

        $(document).on('click','#submit_mode_of_payment_btn', function()
		{
            var name = $("#payment_name").val();

            $.ajax({
                url: "../admin/add_mode_of_payment",
                data: {name: name},
                success: function()
                {
                    location.reload(true);
                },
                error: function(xhr)
                {
                    var errors = jQuery.parseJSON(xhr.responseText)['errors'];
                    if (errors['name']){
                        $("#errorMsg").html(errors['name'][0]);
                    }
                }
            });
        });

        $(document).on('click','#update_mode_of_payment_btn', function()
		{
            var id = $("#update_mode_of_payment_id").val();
            var name = $("#update_payment_name").val();

            $.ajax({
                url: "../admin/update_mode_of_payment",
                data: {id:id, name: name},
                success: function()
                {
                    location.reload(true);
                },
                error: function(xhr)
                {
                    var errors = jQuery.parseJSON(xhr.responseText)['errors'];
                    if (errors['name']){
                        $("#update_errorMsg").html(errors['name'][0]);
                    }
                }
            });
        });
    });
</script>
@endsection

