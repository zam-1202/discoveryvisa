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
                        <a href="" data-toggle="modal" data-target="#daily_reports" class="btn btn-primary col-md-6 mt-3">Daily Report</a>
					@elseif(Auth::user()->role == 'Accounting')
						<a href="{{route('account_receivables.index')}}" class="btn btn-primary col-md-6 mt-3">Account Receivables</a>
                        <a href="" data-toggle="modal" data-target="#daily_reports" class="btn btn-primary col-md-6 mt-3">Daily Report</a>
					@elseif(Auth::user()->role == 'Admin')
						<a href="{{route('applications.index')}}" class="btn btn-primary col-md-6 mt-3">Applicant Search</a>
						<a href="{{route('admin.users')}}" class="btn btn-primary col-md-6 mt-3">User List</a>
						<a href="{{route('admin.branches')}}" class="btn btn-primary col-md-6 mt-3">Branch List</a>
                        <a href="{{route('admin.partner_companies')}}" class="btn btn-primary col-md-6 mt-3">Partner Companies</a>
						<a href="{{route('admin.approvals')}}" class="btn btn-primary col-md-6 mt-3">Pending Approvals</a>
                        <a href="{{route('admin.promo_codes')}}" class="btn btn-primary col-md-6 mt-3">Promo Codes</a>
                        <a href="{{route('admin.visa_types')}}" class="btn btn-primary col-md-6 mt-3">Visa Types</a>
                        <a href="{{route('admin.required_documents')}}" class="btn btn-primary col-md-6 mt-3">Required Document List</a>
                        <a href="{{route('admin.mode_of_payment')}}" class="btn btn-primary col-md-6 mt-3">Mode of Payment List</a>
                        <a href="{{route('admin.payment_request')}}" class="btn btn-primary col-md-6 mt-3">Payment Request List</a>
					@endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="daily_reports">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header bg-success text-white">
				<span class="modal-title">Daily Report</span>
				<button type="button" class="close" data-dismiss="modal">x</button>
			</div>
			<div class="modal-body d-flex justify-content-center">
				<div class="container">
					<div class="form-group row">
						<div class="col-md-12">
							<label><small class="text-danger" id="errorMsg">&nbsp;</small></label>
						</div>
					</div>
					<div class="form-group row">
						<div class="col-md-2 text-right"><label>Date: </label></div>
						<div class="col-md-10">{{Form::date('report_date', date('Y-m-d'), ['class' => 'form-control text-uppercase', 'id' => 'report_date'])}}</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-success" id="submit_btn" data-action="" data-id="">Download</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
		    </div>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
    $(document).ready(function(){
        $(document).on('click','#submit_btn', function()
		{
            var date = $("#report_date").val();
            if(date == ''){
				$("#errorMsg").html('Date cannot be empty.');
            } else {
                $.ajax({
                    url: "cashier/download_report",
                    data: {date:date},
                    xhrFields: {
                        responseType: 'blob',
                    },
                    success: function(result)
                    {
                        var currentDate = date.split("-").join("");
                        var filename = currentDate +'_DailyReport.xlsx';
                        var blob = new Blob([result], {
                            type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                        });
                        var link = document.createElement('a');
                        link.href = window.URL.createObjectURL(blob);
                        link.download = filename;

                        document.body.appendChild(link);

                        link.click();
                        document.body.removeChild(link);
                    }
                });
            }
        });
    });
</script>

@endsection
