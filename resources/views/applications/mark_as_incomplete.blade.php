@extends('layouts.app')

@section('content')

<div class="container">
	<div class="row justify-content-center">
		<div class="card">
			<div class="card-header">
				<h4 class="text-center">Mark as Incomplete</h4>
			</div>
			<div class="card-body">
				{{Form::text('reference_no', $application->reference_no, ['class' => 'form-control'])}}
			</div>
		</div>
	</div>
</div>

@endsection