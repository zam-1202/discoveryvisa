@extends('layouts.app')

@section('content')

@php
    $x = 0;
    $y = 0;
    $z = 0;
@endphp

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
								@foreach($result as $row)
                                @php $x = 0; $y = 0; $z = 0; @endphp
									<tr>
										<td>{{$row->name}}</td>
                                        <td>{{number_format($row->handling_fee, 2, '.', ',')}}</td>
                                        <td>{{number_format($row->visa_fee, 2, '.', ',')}}</td>
                                        <td>
                                            @foreach ($row->documents as $document)
                                                @if($document->type == 'FILIPINO')
                                                    @if ($x == 0)
                                                        <h6 class="text-left pl-2 font-weight-bold mb-0 mt-2">FILIPINO</h6>
                                                        @php
                                                            $x = 1;
                                                        @endphp
                                                    @endif
                                                    <li class="text-left pl-5">{{$document->name}}</li>
                                                @elseif ($document->type == 'JAPANESE')
                                                    @if ($y == 0)
                                                        <h6 class="text-left pl-2 font-weight-bold mb-0 mt-2">JAPANESE</h6>
                                                        @php
                                                            $y = 1;
                                                        @endphp
                                                    @endif
                                                    <li class="text-left pl-5">{{$document->name}}</li>
                                                @else
                                                    @if ($y == 0)
                                                        <h6 class="text-left pl-2 font-weight-bold mb-0 mt-2">FOREIGN</h6>
                                                        @php
                                                            $y = 1;
                                                        @endphp
                                                    @endif
                                                    <li class="text-left pl-5">{{$document->name}}</li>
                                                @endif
                                            @endforeach
                                        </td>

										<td><a href="" class="btn btn-primary" data-toggle="modal" data-target="#update_required_document_modal" data-id="{{ $row->id }}" data-name="{{ $row->name }}" data-type="{{ $row->type }}">Update</a></td>
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

