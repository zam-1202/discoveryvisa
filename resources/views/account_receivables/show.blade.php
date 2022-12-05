@php
    $branches = App\Branch::all();
	$branch_lookup = array();
	foreach($branches as $branch){
		$branch_lookup[$branch->code] = $branch->description;
	}

    $application_status_array = array('1' => 'NEW Application', '2' => 'Submitted to Embassy', '3' => 'Received from Embassy', '4' => 'Sent/Claimed by Client');
@endphp

@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-sm-12">
		<div class="row">
			<div class="jumbotron bg-dark text-white text-center col-md-8 offset-md-2">
				<h1>Account Receivables</h1>
			</div>
		</div>

		<br>

		<div class="table-responsive">
            <table class="table table-striped table-hover text-center" id="applicantIndex">
                <thead class="thead-dark">
                    <tr>
                      <th style="width:15%;">Reference Number</th>
                      <th style="width:7.5%;">Status</th>
                      <th style="width:7.5%;">Branch</th>
                      <th style="width:40%;">Name</th>
                      <th style="width:10%;">Amount</th>
                      <th style="width:10%;">Customer Type</th>
                      <th style="width:10%;">Payment Status</th>
                    </tr>
                </thead>
                <tbody>
                @if($data->count() > 0)
                  @foreach($data as $row)
                    <tr>
                        <td>{{ $row->reference_no }}</td>
                        <td>{{ $application_status_array[$row->application_status] }}</td>
                        <td>{{ $branch_lookup[$row->branch] }}</td>
                        <td>{{ $row->lastname }}, {{ $row->firstname }} {{ $row->middlename }}</td>
                        <td>{{ $row->visa_price }}</td>
                        <td>{{ $row->customer_type }}</td>
                        <td>{{ $row->payment_status }}</td>
                    </tr>
                  @endforeach
                @else
                    <tr>
                        <td colspan='7'> No Data Found </td>
                    </tr>
                @endif
                </tbody>
            </table>

            {!! $data->links() !!}
        </div>

		<div class="row">
			<div class="col-md-12 text-center">
				<a href="{{ route('account_receivables.index')}}" class="btn btn-danger">Back</a>
			</div>
		</div>
	</div>
</div>
@endsection
