@extends('layouts.app')

@section('content')
<div class="table-responsive col-md-8 offset-md-2">
	<table class="table table-bordered table-striped text-center">
		<thead class="thead-dark">
			<tr>
				<th>Name</th>
				<th>Type</th>
			</tr>
		</thead>
		<tbody>
			@if($data->count() > 0)
				@foreach($data as $row)
				<tr>
					<td>{{$row->name}}</td>
					<td>{{$row->type}}</td>
				</tr>
				@endforeach
			@else
				<tr>
					<td colspan='2'>No Data Found</td>
				</tr>
			@endif
		</tbody>
	</table>
	
	<a href="{{ route('partner_companies.create')}}" class="btn btn-primary">Add New</a>
	
	{!! $data->links() !!}
</div>

@endsection