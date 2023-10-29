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


<div class="table-responsive">
	<table class="table table-striped table-hover text-center" id="applicantIndex">
		<thead class="thead-dark">
			<tr>
			  <th style="width:15%;">Reference Number</th>
			  <th style="width:7.5%;">Status</th>
			  <th style="width:7.5%;">Branch</th>
			  <th style="width:30%;">Name</th>
			  <th style="width:10%;">Customer Type</th>
			  <th style="width:10%;">Payment Status</th>
			  <th style="width:10%;">Application Date</th>
			  <th style="width:10%;">Passport No.</th>
			  <th style="width:10%;">Group name</th>
			  <th style="width:10%;"></th>
			</tr>
		</thead>
		<tbody>
		@if($data->count() > 0)
		  @foreach($data as $row)
			<tr>
				<td>{{ $row->reference_no }}</td>
				<!-- <td>{{ $row->application_status}}</td> -->
				<td>{{ $application_status_array[$row->application_status] }}</td>
				<td>{{ $branch_lookup[$row->branch] }}</td>
				<td>{{ $row->lastname }}, {{ $row->firstname }} {{ $row->middlename }}</td>
				<td>{{ $row->customer_type }}</td>
				<td>{{ $row->payment_status }}</td>
				<td>{{ date('Y-m-d', strtotime($row->application_date)) }}</td>
				<td>{{ $row->passport_no }}</td>
				<td>{{ $row->group_name }}</td>
				<td><a href="{{route('applications.edit', $row->id)}}" class="btn btn-primary">Update</a></td>
			</tr>
		  @endforeach
		@else
			<tr>
				<td colspan='10'> No Data Found </td>
			</tr>
		@endif
		</tbody>
	</table>

	{!! $data->links() !!}
</div>
