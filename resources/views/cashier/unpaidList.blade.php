@extends('layouts.app')

@php
	$branches = App\Branch::all();
	$branch_lookup = array();
	foreach($branches as $branch){
		$branch_lookup[$branch->code] = $branch->description;
	}

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

@section('content')

@if(session()->get('success'))
<div class="alert alert-primary alert-dismissible fade show text-center" role="alert">
	<strong>{{ session('success') }}</strong>
	<button type="button" class="close" data-dismiss="alert" aria-label="Close">
		<span aria-hidden="true">&times;</span>
	</button>
</div>
@endif

<div class='jumbotron text-center bg-dark text-white col-md-12'>
	<h1>Unpaid Applicants</h1>
</div>

<div class="table-responsive">
	<table class="table table-striped text-center">
		<thead class="thead-dark">
			<th style="width:20%;">Reference No.</th>
			<th style="width:10%;">Customer Type</th>
			<th style="width:20%;">Name</th>
			<th style="width:15%;">Status</th>
			<th style="width:15%;">Payment Status</th>
			<th style="width:15%;">Application Date</th>
			<th style="width:15%;">Action</th>
		</thead>
		<tbody>
			@if($list->count() > 0)
				@foreach($list as $row)
				<tr>
					<td>{{$row->reference_no}}</td>
					<td>{{$row->customer_type}}</td>
					<td>{{ $row->lastname }}, {{ $row->firstname }} {{ $row->middlename }}</td>
					<td>{{ $application_status_array[$row->application_status] }}</td>
					<td>{{$row->payment_status}}</td>
					<td>{{ date('Y-m-d', strtotime($row->application_date)) }}</td>
					<td><a href="{{route('cashier.receive_payment', ['reference_no' => $row->reference_no])}}" id="receive_payment_btn_{{$row->reference_no}}" class="btn btn-primary">Receive Payment</a></td>
				</tr>
				@endforeach
			@else
				<tr>
					<td colspan='7' class="font-weight-bold"><h1>No Data Found</h1></td>
				</tr>
			@endif
		</tbody>
	</table>
	{!! $list->links() !!}
</div>

<div class="row">
	<div class="col-md-12 text-center">
		<a href="{{ route('home')}}" class="btn btn-danger">Back</a>
	</div>
</div>

@endsection



@section('scripts')
<!-- <script>

</script> -->
@endsection

