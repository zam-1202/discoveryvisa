@extends('layouts.app')

@section('content')

<div class="container">
	<div class="row justify-content-center">
		<div class="col-md-10">
			@if (session('status'))
				<div class="alert alert-primary alert-dismissible fade show text-center" role="alert">
					<strong>{{ session('status') }}</strong>
					<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
			@endif
			<div class="card">
				<div class="card-header bg-primary text-white text-center font-weight-bold">
					<h1>Visa Types</h1>
				</div>
				<div class="card-body">
					<div class="row p-1">
						<div class="col-md-12 text-right">
                            <a href="{{route('admin.visa_types.create')}}" class="btn btn-success">Add Visa Type</a>
						</div>
					</div>

					<table class="table table-hover table-bordered text-center">
						<thead class="thead-dark">
							<tr>
								<th style="width:20%;">Category</th>
                                <th style="width:15%;">Handling Fee</th>
                                <th style="width:10%;">Visa Fee</th>
                                <th style="width:45%;">Required Documents</th>
								<th style="width:10%;"></th>
							</tr>
						</thead>
						<tbody>
							@if($result->count() > 0)
								@foreach($result as $key => $row)
									<tr>
										<td>{{$row->name}}</td>
                                        <td>{{number_format($row->handling_fee, 2, '.', ',')}}</td>
                                        <td>{{number_format($row->visa_fee, 2, '.', ',')}}</td>
                                        <td>
                                            @if ($documents[$key]['filipino'])
                                                <h6 class="text-left pl-2 font-weight-bold mb-0 mt-2">FILIPINO</h6>
                                                @foreach ($documents[$key]['filipino'] as $docs_fil)
                                                    <li class="text-left pl-5">{{$docs_fil->name}}</li>
                                                @endforeach
                                            @endif

                                            @if ($documents[$key]['japanese'])
                                                <h6 class="text-left pl-2 font-weight-bold mb-0 mt-2">JAPANESE</h6>
                                                @foreach ($documents[$key]['japanese'] as $docs_jap)
                                                    <li class="text-left pl-5">{{$docs_jap->name}}</li>
                                                @endforeach
                                            @endif

                                            @if ($documents[$key]['foreign'])
                                                <h6 class="text-left pl-2 font-weight-bold mb-0 mt-2">FOREIGN</h6>
                                                @foreach ($documents[$key]['foreign'] as $docs_foreign)
                                                    <li class="text-left pl-5">{{$docs_foreign->name}}</li>
                                                @endforeach
                                            @endif
                                        </td>

										<td><a class="btn btn-primary text-white" href="{{route('admin.visa_types.edit', $row->id)}}">Update</a></td>
									</tr>
								@endforeach
							@else
								<tr>
									<td colspan="3" class="font-weight-bold">No Data Found</td>
								</tr>
							@endif
						</tbody>
					</table>

					{!! $result->links() !!}

					<div class="row">
						<div class="col-md-12 text-center">
							<a href="{{url('/')}}" class="btn btn-danger">Back</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


@endsection

