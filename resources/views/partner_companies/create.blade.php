@extends('layouts.app')

@section('content')

<div class="row">
	<div class="col-sm-8 offset-sm-2 text-center">
	
		<div class="jumbotron bg-dark text-white">
			<h1>Add a new Partner Company</h1>
		</div>
		
		<div>
		@if ($errors->any())
			<div class="alert alert-danger">
				<ul>
					@foreach ($errors->all() as $error)
					  <li>{{ $error }}</li>
					@endforeach
				</ul>
			</div>
			<br/>
		@endif
			
			{!! Form::open(['url'=>route('partner_companies.store')]) !!}
				@csrf
				<div class="form-group row">
					<div class="col-md-4 offset-md-4">
						{{Form::label('name','Company Name')}}
						{{Form::text('name','',['class' => 'form-control'])}}
					</div>
				</div>
				
				<div class="form-group row">
					<div class="col-md-4 offset-md-4">
						{{Form::label('type','Type')}}
						{{Form::select('type', array('PIATA' => 'PIATA', 'PTAA' => 'PTAA', 'Corporate' => 'Corporate'), 'Corporate', ['class' => 'form-control'])}}
					</div>
				</div>
				
				<div class="form-group row">
					<div class="col">
						<button type="submit" class="btn btn-primary">Submit</button>
					</div>
				</div>
			
			
			{!! Form::close() !!}
		</div>
	</div>


@endsection