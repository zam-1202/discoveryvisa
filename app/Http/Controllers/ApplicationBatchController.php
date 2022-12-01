<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use PDF;
use Storage;
use Illuminate\Support\Facades\Mail;

use App\Application;
use App\ApplicationBatch;
use App\ApplicationBatchStatus;
use App\Branch;
use App\PartnerCompany;
use App\User;

use App\Http\Controllers\AccountReceivableController;

use App\Mail\SubmissionListGenerated;

class ApplicationBatchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $application_batches = DB::table('application_batches')->orderBy('batch_date', 'desc')->paginate(20);

		$status_array = ApplicationBatchStatus::all();
		$status_list = array();
		foreach($status_array as $status) $status_list[$status->id] = $status->description;

		foreach($application_batches as $batch){
			if($batch->status == 2){
				$batch->receive_status = $status_list[$batch->status] . " (" . $batch->total_applications . "/" .$batch->total_applications . ")";
			} else {
				$batch->receive_status = $status_list[$batch->status];
			}
		}
		return view('application_batches.index', compact('application_batches'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $batch = ApplicationBatch::find($id);
		$status_list = ApplicationBatchStatus::all();

		$batch_contents = DB::table('applications')->where('batch_no', $batch->batch_no)->get();
		return view('application_batches.edit', compact('batch', 'status_list', 'batch_contents'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
       $batch = ApplicationBatch::find($id);
	   $batch->batch_no = $batch->batch_no;
	   $batch->batch_date = $batch->batch_date;
	   $batch->total_applications = $batch->total_applications;
	   $batch->status = $request->get('status');
	   $batch->tracking_no = $request->get('tracking_no');

	   $batch->save();

	   if($request->get('status') == 2)
	   {
		   //update field "date_received_by_main_office" for all applications of current batch
		   DB::table('applications')->where('batch_no', $batch->batch_no)
									->update(['date_received_by_main_office' => Carbon::today()]);
	   }


	   return redirect()->back()->with('status','Batch ' . $batch->batch_no . ' has been updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

	public function showChecklist(Request $request)
	{

		$date = Carbon::now()->toFormattedDateString();
		$branch = $request->user()->branch;

		$walkin_applications = DB::table('applications')
								->where(function($query){
											$query->where('customer_type','=','Walk-In');
											$query->orWhere('customer_type','=','Mobile Service');
											$query->orWhere('customer_type','=','Via Courier');
										})
								->where('branch','=',$branch)
								->where('application_status','=',1)
								->where('payment_status','=','PAID')
								->where('batch_no','=',NULL)
								->orderBy('lastname','asc')
								->get();

		$piata_applications = DB::table('applications')
								->where('customer_type','=','PIATA')
								->where('branch','=',$branch)
								->where('application_status','=',1)
								->where('batch_no','=',NULL)
								->orderBy('lastname','asc')
								->get();

		$ptaa_applications = DB::table('applications')
								->where('customer_type','=','PTAA')
								->where('branch','=',$branch)
								->where('application_status','=',1)
								->where('batch_no','=',NULL)
								->orderBy('lastname','asc')
								->get();

		$corporate_applications = DB::table('applications')
								->where('customer_type','=','Corporate')
								->where('branch','=',$branch)
								->where('application_status','=',1)
								->where('batch_no','=',NULL)
								->orderBy('lastname','asc')
								->get();

        $poea_applications = DB::table('applications')
								->where('customer_type','=','POEA')
								->where('branch','=',$branch)
								->where('application_status','=',1)
								->where('batch_no','=',NULL)
								->orderBy('lastname','asc')
								->get();

		return view('application_batches.checklist', compact('branch', 'date', 'walkin_applications', 'piata_applications', 'ptaa_applications', 'corporate_applications', 'poea_applications'));
	}

	public function downloadChecklist(Request $request)
	{

		$date = Carbon::now()->toFormattedDateString();
		$branch = $request->user()->branch;
		$walkin_applications = DB::table('applications')
								->where(function($query){
											$query->where('customer_type','=','Walk-In');
											$query->orWhere('customer_type','=','Mobile Service');
											$query->orWhere('customer_type','=','Via Courier');
										})
								->where('branch','=',$branch)
								->where('application_status','=',1)
								->where('payment_status','=','PAID')
								->where('batch_no','=',NULL)
								->orderBy('lastname','asc')
								->get();

		$piata_applications = DB::table('applications')
								->where('customer_type','=','PIATA')
								->where('branch','=',$branch)
								->where('application_status','=',1)
								->where('batch_no','=',NULL)
								->orderBy('lastname','asc')
								->get();

		$ptaa_applications = DB::table('applications')
								->where('customer_type','=','PTAA')
								->where('branch','=',$branch)
								->where('application_status','=',1)
								->where('batch_no','=',NULL)
								->orderBy('lastname','asc')
								->get();

		$corporate_applications = DB::table('applications')
								->where('customer_type','=','Corporate')
								->where('branch','=',$branch)
								->where('application_status','=',1)
								->where('batch_no','=',NULL)
								->orderBy('lastname','asc')
								->get();

        $poea_applications = DB::table('applications')
								->where('customer_type','=','POEA')
								->where('branch','=',$branch)
								->where('application_status','=',1)
								->where('batch_no','=',NULL)
								->orderBy('lastname','asc')
								->get();

		$pdf = PDF::loadView('application_batches.checklist_pdf', compact('branch', 'date', 'walkin_applications', 'piata_applications', 'ptaa_applications', 'corporate_applications', 'poea_applications'));
		return $pdf->download($branch . ' Checklist for ' . $date . '.pdf');
	}

	public function showFinalizeBatchPage()
	{
		return view('application_batches.finalize_batch');
	}

	public function finalizeBatchContents(Request $request)
	{
		$branch = Branch::where('code', $request->user()->branch)->first();
		$batch_no_string = Carbon::now()->format('Ymd') . $branch->id;

		$status = 1; //Sent to Main Office (Batch Status)
		if($branch->id == 1) $status = 2; //if batch is for Main Office, go straight to Received by Main Office (Batch Status)

		if(ApplicationBatch::where('batch_no', $batch_no_string)->exists()){
			return back()->with('status', "Batch for today's applications are already finalized.");
		}

		$paid_applications = DB::table('applications')
								->where('branch','=',$branch->code)
								->where(function($query){
												$query->where('customer_type','=','Walk-In');
												$query->orWhere('customer_type','=','Mobile Service');
												$query->orWhere('customer_type','=','Via Courier');
										})
								->where('payment_status','=','PAID')
								->where('application_status','=',1)
								->where('batch_no','=',NULL)
								->update(['batch_no' => $batch_no_string]);

		$unpaid_applications = DB::table('applications')
								->where('branch','=',$branch->code)
								->where(function($query){
											$query->where('customer_type','=','PIATA');
											$query->orWhere('customer_type','=','PTAA');
											$query->orWhere('customer_type','=','Corporate');
                                            $query->orWhere('customer_type','=','POEA');
										})
								->where('application_status','=',1)
								->where('batch_no','=',NULL)
								->update(['batch_no' => $batch_no_string]);

		$finalized_batch = new ApplicationBatch([
			'batch_no' => $batch_no_string,
			'batch_date' => Carbon::today(),
			'total_applications' => $paid_applications + $unpaid_applications,
			'status' => $status
		]);
		$finalized_batch->save();

		AccountReceivableController::generateAccountReceivables($batch_no_string, Carbon::today());

		if($branch->id == 1) //if user is from head office
		{
			//update field date_received_by_main_office for current batch
			DB::table('applications')
			  ->where('batch_no','=',$batch_no_string)
			  ->update(['date_received_by_main_office' => Carbon::today()]);

			$this->generateSubmissionList();
			return redirect('/application_batches/finalize_batch_page')->with('status', 'Submission List Generated!');
		}

		return redirect('/application_batches/finalize_batch_page')->with('status', $batch_no_string . ' has been finalized.');
	}

	public static function generateSubmissionList()
	{
		$sub_batch_no = "S" . Carbon::now()->format('Ymd');
		$current_date = Carbon::now()->toFormattedDateString();

		//for e-mail
		$pdf_array = array();
		$mailing_list = User::find(1);

		$partner_companies = PartnerCompany::all();
		$partner_companies_array = array();
		foreach($partner_companies as $company)
		{
			$partner_companies_array[$company->id] = $company->name;
		}

		DB::table('applications')
		  ->where('application_status','=',1)
		  ->where('batch_no','<>',NULL)
		  ->where('date_received_by_main_office','<>',NULL)
		  ->where('submission_batch_no','=',NULL)
		  ->update(['submission_batch_no' => $sub_batch_no, 'application_status' => 3]);

		$walkin_applications = DB::table('applications')
								 ->where(function($query){
												$query->where('customer_type','=','Walk-In');
												$query->orWhere('customer_type','=','Mobile Service');
												$query->orWhere('customer_type','=','Via Courier');
										})
								 ->where('submission_batch_no', '=', $sub_batch_no)
								 ->get();

		$piata_applications = DB::table('applications')
								->where('customer_type','=','PIATA')
								->where('submission_batch_no', '=', $sub_batch_no)
								->get();

		$ptaa_applications = DB::table('applications')
							   ->where('customer_type','=','PTAA')
							   ->where('submission_batch_no', '=', $sub_batch_no)
							   ->get();

		$corporate_applications = DB::table('applications')
									->where('customer_type','=','POEA')
									->where('submission_batch_no', '=', $sub_batch_no)
									->get();

        $poea_applications = DB::table('applications')
									->where('customer_type','=','Corporate')
									->where('submission_batch_no', '=', $sub_batch_no)
									->get();

		$pdf = PDF::loadView('application_batches/pdf', compact('walkin_applications', 'piata_applications', 'ptaa_applications', 'corporate_applications', 'poea_applications',
																	'partner_companies_array','current_date'));
		$pdf_array[$sub_batch_no] = $pdf->download()->getOriginalContent();
		Storage::put('public/pdf/' . $sub_batch_no . '.pdf', $pdf_array[$sub_batch_no]);

		//send email to admins
		Mail::to($mailing_list)->send(new SubmissionListGenerated($pdf_array, $current_date));
	}
}
