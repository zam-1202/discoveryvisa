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
					<h1>Promo Codes</h1>
				</div>
				<div class="card-body">
					<div class="row p-1">
						<div class="col-md-12 text-right">
							<a href="{{route('admin.create_promo_code')}}" class="btn btn-success">Add Promo Code</a>
						</div>
					</div>

					<table class="table table-hover table-bordered text-center">
						<thead class="thead-dark">
							<tr>
								<th style="width:20%;">Code</th>
								<th style="width:20%;">Discount</th>
								<th style="width:20%;">Expiration Date</th>
								<th style="width:30%;">Max Quantity</th>
								<th style="width:10%;"></th>
							</tr>
						</thead>
						<tbody>
							@if($promo->count() > 0)
								@foreach($promo as $row)
									<tr>
										<td>{{$row->code}}</td>
										<td>{{$row->discount}}</td>
										<td>{{$row->expiration_date}}</td>
										<td>{{$row->max_quantity}}</td>
										<td><a class="btn btn-primary text-white" href="{{route('admin.promo_codes.edit', $row->id)}}">Update</a></td>
									</tr>
								@endforeach
							@else
								<tr>
									<td colspan="6" class="font-weight-bold">No Data Found</td>
								</tr>
							@endif
						</tbody>
					</table>

					{!! $promo->links() !!}

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
