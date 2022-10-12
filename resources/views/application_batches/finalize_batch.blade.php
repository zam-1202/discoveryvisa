@extends('layouts.app')

@section('content')

<div class="container">
	<div class="row justify-content-center">
		<div class="col-md-8">
			@if (session('status'))
				<div class="alert alert-danger alert-dismissible fade show text-center" role="alert">
					<strong>{{ session('status') }}</strong>
					<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
			@endif
			<div class="card">
				<div class="card-header bg-primary text-white text-center">
					<h4 class="font-weight-bold">FINALIZE Daily Batch of Applications</h4>
				</div>
				<div class="card-body text-center">
					<span class="text-danger font-weight-bold">WARNING!!</span>
					<br>
					<span class="text-danger"> This process can only be done once per day.</span>
					<br>
					<span class="text-danger"> Use the <a href="{{route('application_batches.checklist')}}" class="font-weight-bold">GENERATE Checklist</a> function to check the applications that will be included in this final batch.</span>
					<br>
					<br>
					<a href="{{route('finalize_batch')}}" class="btn btn-danger">FINALIZE BATCH</a> <a href="{{url('/')}}" class="btn btn-primary">Back</a>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection