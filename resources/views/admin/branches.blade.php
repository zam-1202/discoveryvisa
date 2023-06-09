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
				<div class="card-header bg-primary text-white text-center"><h1>Branches</h1></div>
				<div class="card-body">
					<div class="row p-1">
						<div class="col-md-12 text-right">
							<a href="" class="btn btn-success" data-toggle="modal" data-target="#add_branch_modal">Add New Branch</a>
						</div>
					</div>
					<table class="table table-hover table-bordered text-center">
						<thead class="thead-dark">
							<th style="width: 30%;">Branch Code</th>
							<th style="width: 40%;">Branch Description</th>
							<th style="width: 60%;">Pick-Up Fee</th>
							<th style="width: 10%;"> </th>
						</thead>
						<tbody>
							@if($branches->count() > 0)
								@foreach($branches as $branch)
									<tr>
										<td>{{$branch->code}}</td>
										<td>{{$branch->description}}</td>
										<td>{{$branch->pickup_price}}</td>
										<td><a href="" class="btn btn-primary" data-toggle="modal" data-target="#update_branch_modal" data-id="{{ $branch->id }}" data-code="{{ $branch->code }}" data-description="{{ $branch->description }}" data-pickup_price="{{ $branch->pickup_price }}">Update</a></td>
									</tr>
								@endforeach
							@else
								<tr>
									<td colspan="3" class="text-center font-weight-bold">No Data Found</td>
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

<div class="modal" id="add_branch_modal">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header bg-success text-white">
				<h4 class="modal-title">Add New Branch</h4>
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
						<div class="col-md-5 text-right"><label>Branch Code</label></div>
						<div class="col-md-7">{{Form::text('code', '', ['class' => 'form-control text-uppercase', 'id' => 'branch_code', 'maxlength' => '3'])}}</div>
					</div>
					<div class="form-group row">
						<div class="col-md-5 text-right"><label>Branch Description</label></div>
						<div class="col-md-7">{{Form::text('description', '', ['class' => 'form-control', 'id' => 'branch_desc'])}}</div>
					</div>
					<div class="form-group row">
						<div class="col-md-5 text-right"><label>Pick-Up Fee</label></div>
						<div class="col-md-7">{{Form::text('pickup_price', '', ['class' => 'form-control', 'id' => 'branch_pickup_price'])}}</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-success" id="submit_btn">Submit</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
		    </div>
		</div>
	</div>
</div>

<div class="modal" id="update_branch_modal">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header bg-success text-white">
				<h4 class="modal-title">Update Branch</h4>
				<button type="button" class="close" data-dismiss="modal">x</button>
			</div>
			<div class="modal-body d-flex justify-content-center">
				<div class="container">
                    <input type="hidden" id="update_branch_id" value="" />
					<div class="form-group row">
						<div class="col-md-12">
							<label><small class="text-danger" id="update_errorMsg">&nbsp;</small></label>
						</div>
					</div>
					<div class="form-group row">
						<div class="col-md-5 text-right"><label>Branch Code</label></div>
						<div class="col-md-7">{{Form::text('code', '', ['class' => 'form-control text-uppercase', 'id' => 'update_branch_code', 'maxlength' => '3'])}}</div>
					</div>
					<div class="form-group row">
						<div class="col-md-5 text-right"><label>Branch Description</label></div>
						<div class="col-md-7">{{Form::text('description', '', ['class' => 'form-control', 'id' => 'update_branch_desc'])}}</div>
					</div>
					<div class="form-group row">
						<div class="col-md-5 text-right"><label>Pick-Up Fee</label></div>
						<div class="col-md-7">{{Form::text('pickup_price', '', ['class' => 'form-control', 'id' => 'update_branch_pickup_price'])}}</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-success" id="update_btn">Update</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
		    </div>
		</div>
	</div>
</div>
@endsection

@section('scripts')

<script type="text/javascript">

	$(document).ready(function(){

		$(document).on('click','#submit_btn', function()
		{
			var branch_code = $("#branch_code").val().toUpperCase();
			var branch_desc = $("#branch_desc").val();
			var branch_pickup_price = $("#branch_pickup_price").val();


			if(branch_code == '' || branch_desc == '' || branch_pickup_price == ''){
				$("#errorMsg").html('Branch Code or Branch Description cannot be empty.');
			} else if(branch_code.length != 3){
				$("#errorMsg").html('Branch Code should be exactly three characters');	
			} else {
				var branch_array = {!! $branches->toJson() !!};
				var branchCheck = $.grep(branch_array, function(obj){ return obj.code == branch_code;});

				if(branchCheck.length > 0) {
					$("#errorMsg").html('Branch Code already exists. Please input a unique Branch Code.');
				} else {
					$.ajax({
						url: "../admin/addbranch",
						data: {branch_code:branch_code,branch_desc:branch_desc,branch_pickup_price:branch_pickup_price},
						success: function()
						{
							location.reload(true);
						}
					});
				}
			}
		});

        $(document).on('shown.bs.modal', '#update_branch_modal' , function (event) {
            let button = $(event.relatedTarget); // Button that triggered the modal
            let id = button.attr('data-id');
            let code = button.attr('data-code');
            let description = button.attr('data-description');
			let pickup_price = button.attr('data-pickup_price');

            $('#update_branch_id').val(id);
            $('#update_branch_code').val(code);
            $('#update_branch_desc').val(description);
			$('#update_branch_pickup_price').val(pickup_price);
        });


        $(document).on('click','#update_btn', function()
		{
            var barnch_id = $("#update_branch_id").val();
			var branch_code = $("#update_branch_code").val().toUpperCase();
			var branch_desc = $("#update_branch_desc").val();
			var branch_pickup_price = $("#update_branch_pickup_price").val();

			if(branch_code == '' || branch_desc == ''){
				$("#update_errorMsg").html('Branch Code or Branch Description cannot be empty.');
			} else if(branch_code.length != 3){
				$("#update_errorMsg").html('Branch Code should be exactly three characters');
			} else {
				var branch_array = {!! $branches->toJson() !!};
                branch_array = $.grep(branch_array, function(obj){ return obj.id != barnch_id;});

				var branchCheck = $.grep(branch_array, function(obj){ return obj.code == branch_code;});

				if(branchCheck.length > 0) {
					$("#update_errorMsg").html('Branch Code already exists. Please input a unique Branch Code.');
				} else {
					$.ajax({
						url: "../admin/updatebranch",
						data: {branch_id:barnch_id,branch_code:branch_code,branch_desc:branch_desc,branch_pickup_price:branch_pickup_price},
						success: function()
						{
							location.reload(true);
						}
					});
				}
			}
		});

	});
</script>

@endsection
