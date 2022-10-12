<div class="table-responsive">
  <table class="table table-striped text-center">
	<thead class="thead-dark">
		<th style="width:30%;">Visa Type</th>
		<th style="width:40%;">Application Date</th>
		<th style="width:30%;">Result</th>
	</thead>
	<tbody>
		@if($pastApplications->count() > 0)
			@foreach($pastApplications as $row)
				<tr>
					<td>{{$row->visa_type}}</td>
					<td>{{$row->application_date}}</td>
					<td>{{$row->application_status}}</td>
				</tr>
			@endforeach
		@else
			<tr>
				<td colspan="3">No Data Found</td>
			</tr>
		@endif
	</tbody>
  </table>
  {!! $pastApplications->links() !!}
</div>