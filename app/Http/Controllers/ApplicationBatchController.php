<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use PDF;
use Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\JsonResponse;
// use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Application;
use App\ApplicationBatch;
use App\ApplicationBatchStatus;
use App\Branch;
use App\PartnerCompany;
use App\User;
use App\VisaType;
use Auth;
use TCPDF;
$visaTypes = VisaType::all();

use App\Http\Controllers\AccountReceivableController;

use App\Mail\SubmissionListGenerated;

// class ChecklistController extends Controller
// {
//     public function show()
//     {
//         $branch = 'Branch Name'; // Replace with the actual branch name
//         $date = date('Y-m-d'); // Get the current date
//         $walkin_applications = WalkinApplication::all(); // Retrieve all walk-in applications
//         $piata_applications = PiataApplication::all(); // Retrieve all PIATA applications
//         $ptaa_applications = PtaaApplication::all(); // Retrieve all PTAA applications
//         $corporate_applications = CorporateApplication::all(); // Retrieve all corporate applications

//         return view('checklist', compact('branch', 'date', 'walkin_applications', 'piata_applications', 'ptaa_applications', 'corporate_applications'));
//     }
// }

class ApplicationBatchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        if ( Auth::user()->branch == 'MNL') {
            $application_batches = DB::table('application_batches')
                ->leftJoin('applications', 'application_batches.batch_no', '=', 'applications.batch_no')
                ->where('applications.branch', 'MNL')
                ->orWhere('application_batches.status', '2')
                ->orWhere('application_batches.status', '3')
                ->orWhere('application_batches.status', '6')
                ->orWhere('application_batches.status', '7')
                ->orWhere('application_batches.status', '8')
                ->select('application_batches.id', 'application_batches.batch_no', 'application_batches.batch_date', 'application_batches.status', 'application_batches.total_applications')
                ->groupBy('application_batches.id', 'application_batches.batch_no', 'application_batches.batch_date', 'application_batches.status', 'application_batches.total_applications')
                ->orderBy('application_batches.batch_date', 'desc')->paginate(20);
        } else {
            $application_batches = DB::table('application_batches')
                ->leftJoin('applications', 'application_batches.batch_no', '=', 'applications.batch_no')
                ->where('applications.branch', Auth::user()->branch)
                ->select('application_batches.id', 'application_batches.batch_no', 'application_batches.batch_date', 'application_batches.status', 'application_batches.total_applications')
                ->groupBy('application_batches.id', 'application_batches.batch_no', 'application_batches.batch_date', 'application_batches.status', 'application_batches.total_applications')
                ->orderBy('application_batches.batch_date', 'desc')->paginate(20);
        }

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

	public function searchBatchNum(Request $request)
	{
		if ($request->ajax()) {
			$searchString = $request->get('searchString');
	
			if ($searchString != '') {
				$data = DB::table('application_batches')
				->where(function($query) use ($searchString){
					$query->where('batch_no', 'LIKE', '%' . $searchString . '%');
                })
					->orderBy('id', 'desc')
					->paginate(20);
			} else {
				$data = DB::table('application_batches')->orderBy('id', 'desc')->paginate(20);
			}

			return view('application_batches.applicationBatch_list', compact('data', 'searchString'))->render();
			
		}
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
        $currentStatus = ApplicationBatchStatus::find($batch->status);
        if ( Auth::user()->branch == 'MNL') {
            $status_list = array(
                '6' => 'Submitted to Embassy',
                '7' => 'Received from Embassy',
                '8' => 'Sent to/Claimed by Client'
            );
        } else {
            $status_list = array(
                '2' => 'Sent to Main Office',
				'3' => 'Received by Main Office',
                '4' => 'Sent to Original Branch',
            );
        }

        $key = array_search($currentStatus->description, $status_list);
        if (!$key) {
            $status_list = collect($status_list)->prepend($currentStatus->description, $currentStatus->id);
        }

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

       DB::table('applications')->where('batch_no', $batch->batch_no)
                                ->update(['application_status' => $request->get('status')]);

								if ($request->get('status') == 3) {
									$dateField = 'date_received_by_main_office';
								} elseif ($request->get('status') == 7) {
									$dateField = 'date_received_from_embassy';
								}
								
								if (isset($dateField)) {
									DB::table('applications')
										->where('batch_no', $batch->batch_no)
										->update([$dateField => Carbon::today()]);
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
											$query->where('customer_type','=','Walk-In')
											->orWhere('customer_type','=','Mobile Service')
											->orWhere('customer_type','=','Via Courier');
										})
								->where('branch','=',$branch)
								->where('application_status','!=','9')
								->where('payment_status','=','PAID')
								// ->orWhere('payment_status','=','UNPAID')
								->where('batch_no','=',NULL)
								->whereDate('created_at', Carbon::today())
								// ->where('application_status','=',1) // add this line to filter by application_status
								->orderBy('lastname','asc')
								->get();

		$piata_applications = DB::table('applications')
								->where('customer_type','=','PIATA')
								->where('payment_status','=','PAID')
								->where('branch','=',$branch)
								->where('batch_no','=',NULL)
								->whereDate('created_at', Carbon::today())
								->orderBy('lastname','asc')
								->get();

		$ptaa_applications = DB::table('applications')
								->where('customer_type','=','PTAA')
								->where('payment_status','=','PAID')
								->where('branch','=',$branch)
								->where('batch_no','=',NULL)
								->whereDate('created_at', Carbon::today())
								->orderBy('lastname','asc')
								->get();

		$corporate_applications = DB::table('applications')
								->where('customer_type','=','Corporate')
								->where('payment_status','=','PAID')
								->orWhere('payment_status','=','UNPAID')
								->where('branch','=',$branch)
								->where('batch_no','=',NULL)
								->whereDate('created_at', Carbon::today())
								->orderBy('lastname','asc')
								->get();

        $poea_applications = DB::table('applications')
								->where('customer_type','=','POEA')
								->where('payment_status','=','PAID')
								->where('branch','=',$branch)
								->where('batch_no','=',NULL)
								->whereDate('created_at', Carbon::today())
								->orderBy('lastname','asc')
								->get();

			return view('application_batches.checklist', compact('branch', 'date', 'walkin_applications', 'piata_applications', 'ptaa_applications', 'corporate_applications', 'poea_applications'));
							}

	public function downloadChecklist(Request $request)
	{
		
		$date = Carbon::now()->toFormattedDateString();
		$current_date = $date;
		$visatypes = VisaType::orderBy('id', 'asc')->get();
		$branch = $request->user()->branch;
		$walkin_applications = DB::table('applications')
								->where(function($query){
											$query->where('customer_type','=','Walk-In');
											$query->orWhere('customer_type','=','Mobile Service');
											$query->orWhere('customer_type','=','Via Courier');
										})
								->where('branch','=',$branch)
								->where('application_status','!=','10')
								->where('application_status','!=','9')
								// ->where('payment_status','=','PAID')
								// ->whereDate('created_at', Carbon::today())
								->where('batch_no','=',NULL)
								->orderBy('lastname','asc')
								->get();

		$piata_applications = DB::table('applications')
								->where('application_status','!=','10')
								->where('application_status','!=','9')
								->where('customer_type','=','PIATA')
								// ->where('payment_status','=','PAID')
								->where('branch','=',$branch)
								// ->whereDate('created_at', Carbon::today())
								->where('batch_no','=',NULL)
								->orderBy('lastname','asc')
								->get();

		$ptaa_applications = DB::table('applications')
								->where('application_status','!=','10')
								->where('application_status','!=','9')
								->where('customer_type','=','PTAA')
								// ->where('payment_status','=','PAID')
								->where('branch','=',$branch)
								// ->whereDate('created_at', Carbon::today())
								->where('batch_no','=',NULL)
								->orderBy('lastname','asc')
								->get();

		$corporate_applications = DB::table('applications')
								->where('application_status','!=','10')
								->where('application_status','!=','9')
								->where('customer_type','=','Corporate')
								->where('payment_status','=','PAID')
								->orWhere('payment_status','=','UNPAID')
								->where('branch','=',$branch)
								// ->whereDate('created_at', Carbon::today())
								->where('batch_no','=',NULL)
								->orderBy('lastname','asc')
								->get();

        $poea_applications = DB::table('applications')
								->where('application_status','!=','10')
								->where('application_status','!=','9')
								->where('customer_type','=','POEA')
								// ->where('payment_status','=','PAID')
								->where('branch','=',$branch)
								// ->whereDate('created_at', Carbon::today())
								->where('batch_no','=',NULL)
								->orderBy('lastname','asc')
								->get();
								
								
	$pdf = PDF::loadView('application_batches.checklist_pdf', compact('qrCode','branch', 'date', 'walkin_applications', 'piata_applications', 'ptaa_applications', 'corporate_applications', 'poea_applications'));
	return $pdf->stream($branch . ' Checklist for ' . $date . '.pdf');
	// return $pdf->download($branch . ' Checklist for ' . $date . '.pdf');
	}

	public function finalPDFChecklist(Request $request)
	{
		
		$date = Carbon::now()->toFormattedDateString();
		$current_date = $date;
		$visatypes = VisaType::orderBy('id', 'asc')->get();
		$branch = $request->user()->branch;
		$walkin_applications = DB::table('applications')
								->where(function($query){
											$query->where('customer_type','=','Walk-In');
											$query->orWhere('customer_type','=','Mobile Service');
											$query->orWhere('customer_type','=','Via Courier');
										})
								->where('branch','=',$branch)
								->where('payment_status','=','PAID')
								->whereDate('created_at', Carbon::today())
								->where('batch_no','=',NULL)
								->orderBy('lastname','asc')
								->get();

		$piata_applications = DB::table('applications')
								->where('customer_type','=','PIATA')
								->where('payment_status','=','PAID')
								->where('branch','=',$branch)
								->whereDate('created_at', Carbon::today())
								->where('batch_no','=',NULL)
								->orderBy('lastname','asc')
								->get();

		$ptaa_applications = DB::table('applications')
								->where('customer_type','=','PTAA')
								->where('payment_status','=','PAID')
								->where('branch','=',$branch)
								->whereDate('created_at', Carbon::today())
								->where('batch_no','=',NULL)
								->orderBy('lastname','asc')
								->get();

		$corporate_applications = DB::table('applications')
								->where('customer_type','=','Corporate')
								->where('payment_status','=','PAID')
								->where('branch','=',$branch)
								->whereDate('created_at', Carbon::today())
								->where('batch_no','=',NULL)
								->orderBy('lastname','asc')
								->get();

        $poea_applications = DB::table('applications')
								->where('customer_type','=','POEA')
								->where('payment_status','=','PAID')
								->where('branch','=',$branch)
								->whereDate('created_at', Carbon::today())
								->where('batch_no','=',NULL)
								->orderBy('lastname','asc')
								->get();

								$pdf = new TCPDF();
								
								// $pdf->SetMargins(10, 10, 10); // Set the page margins
								$pdf->SetAutoPageBreak(true, 10); // Enable auto page breaks
								$pdf->AddPage(); // Add a new page
							
								$html = view('application_batches.pdf', compact('visatypes','qrCode','branch', 'date', 'walkin_applications', 'piata_applications', 'ptaa_applications', 'corporate_applications', 'poea_applications', 'current_date'))->render();
							
								$pdf->writeHTML($html, true, false, true, false, '');
							
								$pdf->Output($branch . ' Checklist for ' . $date . '.pdf', 'I');
	}

	public function showFinalizeBatchPage()
	{
		return view('application_batches.finalize_batch');
	}

	public function finalizeBatchContents(Request $request)
	{
		$branch = Branch::where('code', $request->user()->branch)->first();
		$batch_no_string = Carbon::now()->format('Ymd') . $branch->id;

		$status = 2; //Sent to Main Office (Batch Status)
		if($branch->id == 1) $status = 6; //if batch is for Main Office, go straight to Received by Main Office (Batch Status)

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
								->update(['batch_no' => $batch_no_string, 'application_status' => $status]);

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
								->update(['batch_no' => $batch_no_string, 'application_status' => $status]);

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
		  ->whereNotNull('batch_no','<>',NULL)
		  ->whereNotNull('date_received_by_main_office','<>',NULL)
		  ->whereNull('submission_batch_no','=',NULL)
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



		public function generatePDF()
		{
			$pdf = new TCPDF();

			$pdf->SetMargins(10, 10, 10); // Set the page margins
			$pdf->SetAutoPageBreak(true, 10); // Enable auto page breaks
			$pdf->AddPage(); // Add a new page

			// Generate your PDF content using TCPDF methods
			$pdf->SetFont('Helvetica', 'B', 12);
			$pdf->Cell(0, 10, 'Hello, World!', 0, 1, 'C');

			// Output the PDF as a stream
			$pdf->Output('filename.pdf', 'I');
		}



}
