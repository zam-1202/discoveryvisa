<table class="table">
	<thead class="thead-dark">
		<th class="text-right align-top" style="width:50%;">Reference No: </th>
		<th class="text-break align-top" style="width:50%;">{{ $searchString }}</th>
	</thead>
	<tbody>
	@if(!is_null($application))
		{{Form::hidden('reference_no', $application->reference_no, ['id' => 'reference_no'])}}
		<tr class="border bg-white">
			<td class="text-right">Name of Applicant: </td>
			<td>{{ $application->lastname}}, {{ $application->firstname }} {{ $application->middlename }}</td>
		</tr>
		<tr class="border bg-white">
			<td class="text-right">Visa Type: </td>
			<td>{{ $application->visa_type }}</td>
		</tr>
		<tr class="border bg-white">
			<td class="text-right">Visa Price: </td>
			<td>{{ $application->visa_price }} ({{ $application->visa_price_type }})</td>
		</tr>
		<tr class="border bg-white">
			<td class="text-right">OR Number: </td>
			<td>{{Form::text('or_number', $application->or_number, ['class' => 'form-control', 'id' => 'or_number', 'placeholder' => '(optional)', 'autocomplete' => 'off'])}}</td>
		</tr>
		<tr class="border bg-white">
			<td class="text-right">VPR Number: </td>
			<td>{{Form::text('vpr_number', $application->vpr_number, ['class' => 'form-control', 'id' => 'vpr_number', 'placeholder' => '(required)', 'autocomplete' => 'off', 'data-tooltip' => 'tooltip', 'title' => 'Required'])}}</td>
		</tr>
		@if($application->payment_status == 'UNPAID')
		<tr class="border bg-white">
			<td colspan="2" class="text-center">
				<button class="btn btn-success" id="confirm_btn">Confirm Payment</button> 
				<button class="btn btn-danger" id="close_btn">Close</button>
			</td>
		</tr>
		@else
		<tr class="border bg-white">
			<td class="text-right">Payment Status: </td>
			<td>{{ $application->payment_status }}</td>
		</tr>
		<tr class="border bg-white">
			<td colspan="2" class="text-center"><button class="btn btn-danger" id="close_btn">Close</button></td>
		</tr>
		@endif
	@else
		<tr>
			<td colspan="2" class="text-center border bg-white text-danger"><h1>No Data Found</h1></td>
		</tr>
		<tr class="border bg-white">
			<td colspan="2" class="text-center"><button class="btn btn-danger" id="close_btn">Close</button></td>
		</tr>
	@endif
	</tbody>
</table>
