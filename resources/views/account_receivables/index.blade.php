@extends('layouts.app')

@php

	$companies = App\PartnerCompany::all();
	$company_lookup = array();
	foreach($companies as $company)
	{
		$company_lookup[$company->id] = $company->name;
	}
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

<div class='jumbotron text-center bg-dark text-white col-md-8 offset-md-2'>
	<h1>Account Receivables</h1>
</div>

<div class="table-responsive">
	<table class="table table-striped text-center">
		<thead class="thead-dark">
			<th style="width:40%;">Company</th>
			<th style="width:10%;">Batch No</th>
			<th style="width:15%;">Application Date</th>
			<th style="width:10%;">Total Amount</th>
			<th style="width:15%;">Payment Status</th>
			<th style="width:10%;"></th>
		</thead>
		<tbody>
			@if($account_receivables->count() > 0)
				@foreach($account_receivables as $row)
				<tr>
					<td>{{$company_lookup[$row->company]}}</td>
					<td>{{$row->batch_no}}</td>
					<td>{{$row->application_date}}</td>
					<td>{{$row->total_amount}}</td>
					<td>{{$row->payment_status}}</td>
					<td><a href="{{ route('account_receivables.show', $row->id) }}" class="btn btn-primary">View</a></td>
				</tr>
				@endforeach
			@else
				<tr>
					<td colspan='6' class="font-weight-bold"><h1>No Data Found</h1></td>
				</tr>
			@endif
		</tbody>
	</table>

	{!! $account_receivables->links() !!}
</div>

<div class="row">
	<div class="col-md-12 text-center">
		<a href="{{ route('home')}}" class="btn btn-danger">Back</a>
	</div>
</div>

@endsection



@section('scripts')




@endsection

