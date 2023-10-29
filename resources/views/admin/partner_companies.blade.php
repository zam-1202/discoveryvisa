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
					<h1>Partner Companies</h1>
				</div>
				<div class="card-body">
					<div class="row p-1">
						<div class="col-md-12 text-right">
                            <a href="" class="btn btn-success" data-toggle="modal" data-target="#add_partner_company_modal">Add</a>
						</div>
					</div>

					<table class="table table-hover table-bordered text-center">
						<thead class="thead-dark">
							<tr>
                                <th style="width:30%;">Type</th>
                                <th style="width:50%;">Name</th>
								<th style="width:20%;"></th>
							</tr>
						</thead>
						<tbody>
							@if($result->count() > 0)
								@foreach($result as $row)
									<tr>
                                        <td>{{$row->type}}</td>
                                        <td>{{$row->name}}</td>
										<td><a href="" class="btn btn-primary" data-toggle="modal" data-target="#update_partner_company_modal" data-id="{{ $row->id }}" data-name="{{ $row->name }}" data-type="{{ $row->type }}">Update</a></td>
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

<div class="modal" id="add_partner_company_modal">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header bg-success text-white">
				<h4 class="modal-title">Add New Partner Company</h4>
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
                    <div class="col-md-2 text-right">
            <label>Type</label>
        </div>
        <div class="col-md-10">
            <select class="form-control" name="customer_type" id="customer_type" required>
                <option value="">- Select a customer type -</option>
                @php
                    $displayedTypes = [];
                @endphp
                @foreach($customer_company as $partnerCompanies)
                    @if(!in_array($partnerCompanies->type, $displayedTypes))
                        <option value="{{ $partnerCompanies->type }}">
                            {{ $partnerCompanies->type }}
                        </option>
                        @php
                            $displayedTypes[] = $partnerCompanies->type;
                        @endphp
                    @endif
                @endforeach
            </select>
        </div>
    </div>
                    <div class="form-group row">
                        <div class="col-md-10 offset-md-2">{{Form::text('other', '', ['class' => 'form-control', 'id' => 'other_partner_company_type', 'hidden'=> true, 'disabled' => true])}}</div>
					</div>
					<div class="form-group row">
						<div class="col-md-2 text-right"><label>Name</label></div>
						<div class="col-md-10">{{Form::text('name', '', ['class' => 'form-control', 'id' => 'partner_company_name'])}}</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-success" id="submit_partner_company_btn">Submit</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
		    </div>
		</div>
	</div>
</div>

<div class="modal" id="update_partner_company_modal">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header bg-success text-white">
				<h4 class="modal-title">Update Partner Company</h4>
				<button type="button" class="close" data-dismiss="modal">x</button>
			</div>
			<div class="modal-body d-flex justify-content-center">
				<div class="container">
                    <input type="hidden" id="update_partner_company_id" value="" />
					<div class="form-group row">
						<div class="col-md-12">
							<label><small class="text-danger" id="update_errorMsg">&nbsp;</small></label>
						</div>
					</div>

					<div class="align-items-start">
						<div class="form-group row">
							<div class="col-md-2 text-right">
								<label>Type</label>
							</div>
							<div class="col-md-10">
								<select class="form-control" name="customer_type" id="update_customer_type" required>
									<option value="">- Select a customer type -</option>
									@php
										$displayedTypes = [];
									@endphp
									@foreach($customer_company as $partnerCompanies)
										@if(!in_array($partnerCompanies->type, $displayedTypes))
											<option value="{{ $partnerCompanies->type }}">
												{{ $partnerCompanies->type }}
											</option>
											@php
												$displayedTypes[] = $partnerCompanies->type;
											@endphp
										@endif
									@endforeach
								</select>
							</div>
						</div>
    <div class="form-group row">
        <div class="col-md-2 text-right">
            <label>Name</label>
        </div>
        <div class="col-md-10">
            {{Form::text('name', $partnerCompanies->name, ['class' => 'form-control text-center', 'id' => 'update_partner_company_name'])}}
        </div>
    </div>
</div>



				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-success" id="update_partner_company_btn">Update</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
		    </div>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
    $(document).ready(function(){
        $(document).on('shown.bs.modal', '#add_partner_company_modal' , function (event) {
            $('#required_document_name').val('');
            $('#customer_type').val('');
            $("#errorMsg").html('');
        });

        $(document).on('shown.bs.modal', '#update_partner_company_modal' , function (event) {
            let button = $(event.relatedTarget); // Button that triggered the modal
            let id = button.attr('data-id');
            let name = button.attr('data-name');
            let type = button.attr('data-type');

            $('#update_partner_company_id').val(id);
            $('#update_partner_company_name').val(name);
            $('#update_customer_type').val(type);
            $("#update_errorMsg").html('');
        });

        $(document).on('click','#submit_partner_company_btn', function()
		{
            var type = $("#customer_type").val();
            var name = $("#partner_company_name").val();


            $.ajax({
                url: "../admin/create_partnerCompanies",
                data: {type: type, name: name },
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

		$(document).on('click', '#update_partner_company_btn', function() {
		var id = $("#update_partner_company_id").val();
		var name = $("#update_partner_company_name").val();
		var type = $("#update_customer_type").val();

		$.ajax({
			url: "../admin/update_partnerCompanies/" + id, 
			method: 'GET',
			data: { name: name, type: type },
			success: function() {
				location.reload(true);
			}
		});
	});



        $(document).on('change','#partner_company_type', function()
		{
            var type = $("#partner_company_type option:selected").text();
            if (type.toUpperCase() == 'OTHER') {
                $('#other_partner_company_type').attr('hidden', false);
                $('#other_partner_company_type').attr('disabled', false);
            } else {
                $('#other_partner_company_type').attr('hidden', true);
                $('#other_partner_company_type').attr('disabled', true);
            }

        });
    });
</script>
@endsection

