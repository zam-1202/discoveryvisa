@extends('layouts.app')

@section('content')

<div class="container">
	<div class="row justify-content-center">
		<div class="col-md-10">
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
					<h1>List of Users</h1>
				</div>
				<div class="card-body">
					<div class="row p-1">
						<div class="col-md-12 text-right">
							<a href="{{route('register')}}" class="btn btn-success">Add NEW User</a>
						</div>
					</div>
					
					<table class="table table-hover table-bordered text-center">
						<thead class="thead-dark">
							<tr>
								<th style="width:20%;">Username</th>
								<th style="width:25%;">Name</th>
								<th style="width:20%;">Email</th>
								<th style="width:10%;">Role</th>
								<th style="width:15%;">Branch</th>
								<th style="width:10%;"></th>
							</tr>
						</thead>
						<tbody>
							@if($users->count() > 0)
								@foreach($users as $row)
									<tr>
										<td>{{$row->username}}</td>
										<td>{{$row->name}}</td>
										<td>{{$row->email}}</td>
										<td>{{$row->role}}</td>
										<td>{{$branch_list[$row->branch]}}</td>
										<td><a class="btn btn-primary text-white" id="{{$row->id}}">Update</a></td>
									</tr>
								@endforeach
							@else
								<tr>
									<td colspan="6" class="font-weight-bold">No Data Found</td>
								</tr>
							@endif
						</tbody>
					</table>
					
					{!! $users->links() !!}
					
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
@endsection