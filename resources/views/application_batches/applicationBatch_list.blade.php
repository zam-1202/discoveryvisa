
<div class="table-responsive">
	<table class="table table-striped table-hover text-center" id="applicationbatch_list">
		<thead class="thead-dark">
							<tr>
								<th style="width:30%"> Batch No </th>
								<th style="width:30%"> Batch Date </th>
								<th style="width:30%"> Status </th>
								<th style="width:30%"> Branch </th>
								<th style="width:10%"> </th>
							</tr>
						</thead>
						<tbody>
						@if($application_batches->count() >0)
							@foreach($application_batches as $batch)
							<tr>
								<td> {{ $batch->batch_no }} </td>
								<td> {{ $batch->batch_date }} </td>
								<td> {{ $batch->status }} </td>
								<td> {{ $batch->branch }} </td>

								<td> <a href="{{ route('application_batches.edit', $batch->id) }}" class="btn btn-primary"> View </a> </td>
							</tr>
							@endforeach
						@else
							<tr>
								<td colspan="4" class="text-center font-weight-bold">No Data Found</td>
							</tr>
						@endif
						</tbody>
					</table>
			{!! $application_batches->links() !!}
	</div>

