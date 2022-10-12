@extends('layouts.app')

@section('content')


<div class="container">
	<div class="row justify-content-center">
		<div class="col-md-8">
			@if(session()->get('status'))
			<div class="alert alert-primary alert-dismissible fade show text-center" role="alert">
				<strong>{{ session('status') }}</strong>
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			@endif

			<div class="card">
				<div class='card-header text-center bg-primary text-white'>
					<h1>Visa Application Batches</h1>
				</div>

				<div class="card-body">
					<table class="table table-bordered table-striped table-hover text-center">
						<thead class="thead-dark">
							<tr>
								<th style="width:30%"> Batch No </th>
								<th style="width:30%"> Batch Date </th>
								<th style="width:30%"> Status </th>
								<th style="width:10%"> </th>
							</tr>
						</thead>
						<tbody>
						@if($application_batches->count() >0)
							@foreach($application_batches as $batch)
							<tr>
								<td> {{ $batch->batch_no }} </td>
								<td> {{ $batch->batch_date }} </td>
								<td> {{ $batch->receive_status }} </td>
								<td> <a href="{{ route('application_batches.edit', $batch->id) }}" class="btn btn-primary"> View </a> </td>
							</tr>
							@endforeach
						@else
							<tr>
								<td colspan="4" class="text-center font-weight-bold">No Data Found</td>
							</tr>
						@endif
						</tbody>
					</table>
					
					{!! $application_batches->links() !!}
					
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

@section('scripts')

@endsection