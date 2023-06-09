@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-sm-13">

		@if(session()->get('success'))
		<div class="alert alert-success alert-dismissible fade show text-center" role="alert">
			<strong>{{ session('success') }}</strong>
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		@endif

		<div class="row">
			<div class="jumbotron bg-dark text-white d-flex align-items-center justify-content-center col-md-12" style="height: 10px;">
				<h1>Visa Applications</h1>
			</div>
		</div>

		<form method="POST" action="{{ route('applications.application_list') }}">
			@csrf
			<div class="row b-3">
				<div class="mb-3"><label for="date">Date from</label>
					<input type="date" class="form-control form-control-sm" id="fromdate" name="start_date">
				</div>
				<div class="mb-3"><label for="date">Date to</label>
					<input type="date" class="form-control form-control-sm" id="todate" name="end_date">
				</div>
				<div class="col-md-2">
				<div class="btn-group">
					<button type="submit" class="btn btn-info">Search</button>
					<button type="button" class="btn btn-secondary" id="clearButton">Clear</button>
				</div>
	</div>

			</div>
		</form>

		<div class="row">
			<div class="col-md-8 offset-md-2">
				<input type="text" class="form-control text-center" id="applicantSearch" name="searchString"
				       placeholder="Type Reference Number, Last Name, First Name, or Middle Name to search">
			</div>
		</div>

<br></br>
		<div id='application_list'>
			@include('applications.application_list')
		</div>

		<div class="row">
			<div class="col-md-12 text-center">
				<a href="{{url()->previous()}}" class="btn btn-danger">Back</a>
			</div>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
	$(document).ready(function()
	{
		fetch_data(1);

		function fetch_data(page)
		{
			$.ajax({
				url: "applications/application_list?page=" + page,
				data: {
	  				searchString:$('#applicantSearch').val()},
				success: function(data)
				{
					$('#application_list').html(data);
				}
			});
		}

		$(document).on('click','.pagination a', function(event)
		{
			event.preventDefault();
			var page = $(this).attr('href').split('page=')[1];
			fetch_data(page);
		});


		$(document).on('keyup paste', '#applicantSearch', function(){
			fetch_data(1);
		});

		$('#clearButton').click(function() {
			$('#fromdate').val('');
			$('#todate').val('');
			$('#applicantSearch').val('');
			fetch_data(1);
		});


	});

</script>
@endsection
