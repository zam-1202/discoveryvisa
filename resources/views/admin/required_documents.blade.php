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
					<h1>List of Required Documents</h1>
				</div>
				<div class="card-body">
					<div class="row p-1">
						<div class="col-md-12 text-right">
                            <a href="" class="btn btn-success" data-toggle="modal" data-target="#add_required_document_modal">Add</a>
						</div>
					</div>

					<table class="table table-hover table-bordered text-center">
						<thead class="thead-dark">
							<tr>
								<th style="width:50%;">Name</th>
                                <th style="width:30%;">Type</th>
								<th style="width:20%;"></th>
							</tr>
						</thead>
						<tbody>
							@if($result->count() > 0)
								@foreach($result as $row)
									<tr>
										<td>{{$row->name}}</td>
                                        <td>{{$row->type}}</td>
										<td><a href="" class="btn btn-primary" data-toggle="modal" data-target="#update_required_document_modal" data-id="{{ $row->id }}" data-name="{{ $row->name }}" data-type="{{ $row->type }}">Update</a></td>
									</tr>
								@endforeach
							@else
								<tr>
									<td colspan="3" class="font-weight-bold">No Data Found</td>
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

<div class="modal" id="add_required_document_modal">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header bg-success text-white">
				<h4 class="modal-title">Add New Required Document</h4>
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
						<div class="col-md-10">{{Form::text('name', '', ['class' => 'form-control', 'id' => 'required_document_name'])}}</div>
					</div>
                    <div class="form-group row">
						<div class="col-md-2 text-right"><label>Type</label></div>
                        <div class="col-md-10">{{Form::select('type', array('FILIPINO' => 'FILIPINO', 'JAPANESE' => 'JAPANESE', 'FOREIGN' => 'FOREIGN'), null, ['class' => 'form-control', 'id' => 'required_document_type'])}}</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-success" id="submit_required_document_btn">Submit</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
		    </div>
		</div>
	</div>
</div>

<div class="modal" id="update_required_document_modal">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header bg-success text-white">
				<h4 class="modal-title">Update Required Document</h4>
				<button type="button" class="close" data-dismiss="modal">x</button>
			</div>
			<div class="modal-body d-flex justify-content-center">
				<div class="container">
                    <input type="hidden" id="update_required_document_id" value="" />
					<div class="form-group row">
						<div class="col-md-12">
							<label><small class="text-danger" id="update_errorMsg">&nbsp;</small></label>
						</div>
					</div>
					<div class="form-group row">
						<div class="col-md-2 text-right"><label>Name</label></div>
						<div class="col-md-10">{{Form::text('name', '', ['class' => 'form-control', 'id' => 'update_required_document_name'])}}</div>
					</div>
                    <div class="form-group row">
						<div class="col-md-2 text-right"><label>Type</label></div>
                        <div class="col-md-10">{{Form::select('type', array('FILIPINO' => 'FILIPINO', 'JAPANESE' => 'JAPANESE', 'FOREIGN' => 'FOREIGN'), null, ['class' => 'form-control', 'id' => 'update_required_document_type'])}}</div>
					</div>

				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-success" id="update_required_document_btn">Update</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
		    </div>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
    $(document).ready(function(){
        $(document).on('shown.bs.modal', '#add_required_document_modal' , function (event) {
            $('#required_document_name').val('');
            $('#required_document_type').val('FILIPINO');
            $("#errorMsg").html('');
        });

        $(document).on('shown.bs.modal', '#update_required_document_modal' , function (event) {
            let button = $(event.relatedTarget); // Button that triggered the modal
            let id = button.attr('data-id');
            let name = button.attr('data-name');
            let type = button.attr('data-type');

            $('#update_required_document_id').val(id);
            $('#update_required_document_name').val(name);
            $('#update_required_document_type').val(type);
            $("#update_errorMsg").html('');
        });

        $(document).on('click','#submit_required_document_btn', function()
		{
            var name = $("#required_document_name").val();
            var type = $("#required_document_type").val();

            $.ajax({
                url: "../admin/add_required_document",
                data: {name: name, type: type},
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

        $(document).on('click','#update_required_document_btn', function()
		{
            var id = $("#update_required_document_id").val();
            var name = $("#update_required_document_name").val();
            var type = $("#update_required_document_type").val();

            $.ajax({
                url: "../admin/update_required_document",
                data: {id:id, name: name, type:type},
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

