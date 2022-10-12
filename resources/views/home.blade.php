@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
			@if (session('status'))
				<div class="alert alert-primary alert-dismissible fade show text-center" role="alert">
					<strong>{{ session('status') }}</strong>
					<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
			@endif
            <div class="card">
				<div class="card-header bg-dark text-white text-center">
					<h4 class="font-weight-bold">DASHBOARD</h4>
				</div>
                <div class="card-body text-center">
                    @if (Auth::user()->role == 'Encoder')
						<a href="{{route('applications.create')}}" class="btn btn-primary col-md-6 mt-3">NEW Visa Application</a>
						<a href="{{route('applications.index')}}" class="btn btn-success col-md-6 mt-3">SEARCH Visa Applications</a>
						<a href="{{route('application_batches.index')}}" class="btn btn-secondary col-md-6 mt-3">BATCH VIEW Visa Applications</a>
						<a href="{{route('application_batches.checklist')}}" class="btn btn-warning col-md-6 mt-3 font-weight-bold">GENERATE Checklist</a>
						<a href="{{route('show_finalize_batch_page')}}" class="btn btn-danger col-md-6 mt-3">FINALIZE Application Batch</a>
					@elseif(Auth::user()->role == 'Cashier')
						<a href="{{route('cashier.receive_payment')}}" class="btn btn-primary col-md-6 mt-3">Receive Payment</a>
					@elseif(Auth::user()->role == 'Accounting')
						<a href="{{route('account_receivables.index')}}" class="btn btn-primary col-md-6 mt-3">Account Receivables</a>
					@elseif(Auth::user()->role == 'Admin')
						<a href="{{route('admin.users')}}" class="btn btn-primary col-md-6 mt-3">User List</a>
						<a href="{{route('admin.branches')}}" class="btn btn-primary col-md-6 mt-3">Branch List</a>
						<a href="{{route('admin.approvals')}}" class="btn btn-primary col-md-6 mt-3">Pending Approvals</a>
					@endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
