	@extends('layouts.app')
@if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
	@section('content')

	@php
		$application_status_array = array('1' => 'NEW Application',
										'2' => 'Sent to Main Office',
										'3' => 'Received by Main Office',
										'4' => 'Sent to Original Branch',
										'5' => 'Received by Original Branch',
										'6' => 'Submitted to Embassy',
										'7' => 'Received from Embassy',
										'8' => 'Sent to/Claimed by Client',
										'9' => 'Incomplete',
										'10' => 'Pending Approval',
										'11' => 'Additional Documents Required',
										'12' => 'Released by Embassy',
										'13' => 'Resubmitted to JPN',
                        							'14' => 'Passport Return from JPN Embassy');
	@endphp
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
			<form id="searchForm">
				<div class="row b-3">
					<div class="col-md-2 mb-3">
						<label for="fromdate">Date from</label>
						<input type="date" class="form-control form-control-sm" id="fromdate" name="fromdate">
					</div>
					<div class="col-md-2 mb-3">
						<label for="todate">Date to</label>
						<input type="date" class="form-control form-control-sm" id="todate" name="todate">
					</div>
					<div class="col-md-3 d-flex align-items-center">
						<div class="btn-group" style="margin-top: 15px;">
							<button type="submit" class="btn btn-info">Search</button>&nbsp;&nbsp;
							<button type="button" class="btn btn-secondary" id="clearButton">Clear</button>
						</div>
					</div>
					<div class="col-md-5 d-flex align-items-center" style="margin-top: 15px;">
					<input type="text" class="form-control text-center" id="applicantSearch" name="searchString" placeholder="Type Reference Number, Last Name, First Name, or Middle Name to search">
					</div>
				</div>

                                <div class="row">
                <div class="col-md-10 mb-3">
                    <select class="form-control" id="searchStatus" name="application_status">
                        <option value="">Choose one</option> 
                        @foreach($application_status_array as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option> 
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 align-items-center">
                    <div class="btn-group">
                        <a href="{{route('applications.create')}}" class="btn btn-success">NEW Application</a>
                    </div>
                </div>
            </div>
</form>


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
$(document).ready(function() {
  fetch_data(1);

  function fetch_data(page) {
    $.ajax({
      url: "applications/application_list?page=" + page,
      data: {
        searchString: $('#applicantSearch').val(),
        fromdate: $('#fromdate').val(),
        todate: $('#todate').val(),
		application_status: $('#searchStatus').val()
      },
      success: function(data) {
        $('#application_list').html(data);
      }
    });
  }

  $(document).on('click', '.pagination a', function(event) {
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    fetch_data(page);
  });

  $('#searchForm').submit(function(event) {
    event.preventDefault();

    var fromDate = $('#fromdate').val();
    var toDate = $('#todate').val();

    if (fromDate === '' && toDate === '') {
        $('#fromdate').val(''); // Clear the value of "Date from" input
        $('#todate').val(''); // Clear the value of "Date to" input
        return; // Exit the submit function without further processing
    }

    fetch_data(1);
});


  $('#searchStatus').change(function() {
	console.log("Search status changed");
	fetch_data(1);
    });

  $(document).on('keyup paste', '#applicantSearch', function() {
    fetch_data(1);
  });

  $('#clearButton').click(function() {
    $('#fromdate').val('');
    $('#todate').val('');
    $('#applicantSearch').val('');

    // Reset the form elements
    $('#searchForm')[0].reset();

    fetch_data(1);
});

});







</script>
@endsection

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

