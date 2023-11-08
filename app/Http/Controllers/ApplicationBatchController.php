<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use TCPDF;
use DB;
use Carbon\Carbon;
use PDF;
use Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\JsonResponse;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Application;
use App\ApplicationBatch;
use App\ApplicationBatchStatus;
use App\Branch;
use App\PartnerCompany;
use App\User;
use App\VisaType;
use Auth;
use App\Mail\ApprovalCodeGenerated;

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
		$userBranch = Auth::user()->branch;

        if ( Auth::user()->branch == 'MNL') {
            $application_batches = DB::table('application_batches')
                ->leftJoin('applications', 'application_batches.batch_no', '=', 'applications.batch_no')
                ->where('applications.branch', 'MNL')
				->whereIn('application_batches.status', ['2', '3', '6', '7', '8'])
                ->select('application_batches.id', 'application_batches.batch_no', 'application_batches.batch_date', 'application_batches.status', 'application_batches.branch', 'application_batches.total_applications')
                ->groupBy('application_batches.id', 'application_batches.batch_no', 'application_batches.batch_date', 'application_batches.status', 'application_batches.branch', 'application_batches.total_applications')
                ->orderBy('application_batches.batch_date', 'desc')->paginate(20);
        } else {
            $application_batches = DB::table('application_batches')
                ->leftJoin('applications', 'application_batches.batch_no', '=', 'applications.batch_no')
                ->where('applications.branch', Auth::user()->branch)
                ->select('application_batches.id', 'application_batches.batch_no', 'application_batches.batch_date', 'application_batches.status', 'application_batches.branch', 'application_batches.total_applications')
                ->groupBy('application_batches.id', 'application_batches.batch_no', 'application_batches.batch_date', 'application_batches.status', 'application_batches.branch', 'application_batches.total_applications')
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
			$user = Auth::user();
			$role = $user->role;
	
			if ($searchString != '') {
				$application_batches = DB::table('application_batches')
					->where(function ($query) use ($searchString) {
						$query->where('batch_no', 'LIKE', '%' . $searchString . '%');
					})
					->when($role === 'Encoder' && $user->branch === 'MNL', function ($query) {
						// No additional restrictions for MNL user
					})
					->when($role === 'Encoder' && $user->branch !== 'MNL', function ($query) use ($user) {
						$query->where('branch', $user->branch);
					})
					->orderBy('id', 'desc')
					->paginate(20);
			} else {
				$application_batches = DB::table('application_batches')
					->when($role === 'Encoder' && $user->branch !== 'MNL', function ($query) use ($user) {
						$query->where('branch', $user->branch);
					})
					->orderBy('id', 'desc')
					->paginate(20);
			}
	
			return view('application_batches.applicationbatch_list', compact('application_batches'))->render();
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
				'3' => 'Received by Main Office',
                '6' => 'Submitted to Embassy',
                '7' => 'Received from Embassy',
                '8' => 'Sent to/Claimed by Client',
				'11' => 'Additional Documents Required',
				'12' => 'Released from Embassy',
				'13' => 'Resubmitted to JPN',
				'14' => 'Passport Return from JPN Embassy'

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
		
	
		// Clear batch number if status is "Incomplete", "Pending Approval" or "NEW Application"
		if (in_array($request->get('status'), [1, 9, 10])) {
			$batch->batch_no = null;
		}
	
		$batch->save();
	
		DB::table('applications')->where('batch_no', $batch->batch_no)
								 ->update(['application_status' => $request->get('status')]);
	
		if ($request->get('status') == 3) {
			$dateField = 'date_received_by_main_office';
		} elseif ($request->get('status') == 7) {
			$dateField = 'date_received_from_embassy';
			// Record username if status is changed to "Received from Embassy (7)"
			$applications = Application::where('batch_no', $batch->batch_no)->get();
			foreach ($applications as $application) {
				$application->receiver_from_embassy = $request->user()->username;
				$application->save();
			}
		} elseif ($request->get('status') == 8) {
			$dateField = 'date_distributed';
			// Record username if status is changed to "Sent to/Claimed by Client (8)"
			$applications = Application::where('batch_no', $batch->batch_no)->get();
			foreach ($applications as $application) {
				$application->distributed_by = $request->user()->username;
				$application->save();
			}
		} elseif ($request->get('status') == 11) {
			$dateField = 'date_docsRequired';
			// Record username if status is changed to "Additional Documents Required (11)"
			$applications = Application::where('batch_no', $batch->batch_no)->get();
			foreach ($applications as $application) {
				$application->additional_docs = $request->user()->username;
				$application->save();
			}
		} elseif ($request->get('status') == 12) {
			$dateField = 'date_released_by_embassy';
			// Record username if status is changed to "Released by Embassy (12)"
			$applications = Application::where('batch_no', $batch->batch_no)->get();
			foreach ($applications as $application) {
				$application->released_by_embassy = $request->user()->username;
				$application->save();
			}
		} elseif ($request->get('status') == 6) {
			$dateField = 'date_submitted_to_embassy';
			// Record username if status is changed to "Submitted to Embassy (6)"
			$applications = Application::where('batch_no', $batch->batch_no)->get();
			foreach ($applications as $application) {
				$application->submitted_to_embassy = $request->user()->username;
				$application->save();
			}
		} elseif ($request->get('status') == 13) {
			$dateField = 'date_return_to_jpn';
			// Record username if status is changed to "Resubmitted to JPN (13)"
			$applications = Application::where('batch_no', $batch->batch_no)->get();
			foreach ($applications as $application) {
				$application->return_to_jpn_emb = $request->user()->username;
				$application->save();
			}
		} elseif ($request->get('status') == 14) {
			$dateField = 'date_passport_return';
			// Record username if status is changed to "Passport Return from JPN Embassy (14)"
			$applications = Application::where('batch_no', $batch->batch_no)->get();
			foreach ($applications as $application) {
				$application->passport_return = $request->user()->username;
				$application->save();
			}
		} else {
			$applications = Application::where('batch_no', $batch->batch_no)->get();
			foreach ($applications as $application) {
				$application->last_update_by = $request->user()->username;
				$application->save();
			}
		}
	
		if (isset($dateField)) {
			DB::table('applications')
				->where('batch_no', $batch->batch_no)
				->update([$dateField => Carbon::now()]);

		}
	
		return redirect()->back()->with('status', 'Batch ' . $batch->batch_no . ' has been updated');
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
											->orWhere('customer_type','=','Via Courier')
											->orWhere('customer_type','=','Courier')
											->orWhere('customer_type','=','Expo')
											->orWhere('customer_type','=','Others');
										})
								->where('branch','=',$branch)
								->where('application_status','=','1')
								->where('payment_status','=','PAID')
								->where(function ($query) {
									$query->whereNotNull('batch_no')
										->orWhereNull('batch_no');
								})
								->where(function ($query) {
									$query->where('created_at', '<', Carbon::today())
										->orWhere(function ($query) {
											$query->where('created_at', '>=', Carbon::today())
												->whereIn('application_status', [1, 2]);
										});
								})			
								->orderBy('lastname','asc')
								->paginate(10);

		$piata_applications = DB::table('applications')
								->where('customer_type','=','PIATA')
								->where('payment_status','=','PAID')
								->where('application_status','=','1')
								->where('branch','=',$branch)
								->where(function ($query) {
									$query->whereNotNull('batch_no')
										->orWhereNull('batch_no');
								})
								->where(function ($query) {
									$query->where('created_at', '<', Carbon::today())
										->orWhere(function ($query) {
											$query->where('created_at', '>=', Carbon::today())
												->whereIn('application_status', [1, 2]);
										});
								})			
								->orderBy('lastname','asc')
								->paginate(10);

		$ptaa_applications = DB::table('applications')
								->where('customer_type','=','PTAA')
								->where('payment_status','=','PAID')
								->where('application_status','=','1')
								->where('branch','=',$branch)
								->where(function ($query) {
									$query->whereNotNull('batch_no')
										->orWhereNull('batch_no');
								})
								->where(function ($query) {
									$query->where('created_at', '<', Carbon::today())
										->orWhere(function ($query) {
											$query->where('created_at', '>=', Carbon::today())
												->whereIn('application_status', [1, 2]);
										});
								})			
								->orderBy('lastname','asc')
								->paginate(10);

		$corporate_applications = DB::table('applications')
								->where('customer_type', '=', 'Corporate')
								->where('application_status', '=', '1')
								->where('branch', '=', $branch)
								->where(function ($query) {
									$query->where('payment_status', '=', 'PAID')
										->orWhere('payment_status', '=', 'UNPAID');
								})
								->where(function ($query) {
									$query->where('created_at', '<', Carbon::today())
										->orWhere(function ($query) {
											$query->where('created_at', '>=', Carbon::today())
												->whereIn('application_status', [1, 2]);
										});
								})			
								->where(function ($query) {
									$query->whereNotNull('batch_no')
										->orWhereNull('batch_no');
								})
								->orderBy('lastname', 'asc')
								->paginate(10);
							

        $poea_applications = DB::table('applications')
								->where('customer_type','=','POEA')
								->where('payment_status','=','PAID')
								->where('application_status','=','1')
								->where('branch','=',$branch)
								->where(function ($query) {
									$query->whereNotNull('batch_no')
										->orWhereNull('batch_no');
								})
								->where(function ($query) {
									$query->where('created_at', '<', Carbon::today())
										->orWhere(function ($query) {
											$query->where('created_at', '>=', Carbon::today())
												->whereIn('application_status', [1, 2]);
										});
								})			
								->orderBy('lastname','asc')
								->paginate(10);
	

	return view('application_batches.checklist', compact('branch', 'date', 'walkin_applications', 'piata_applications', 'ptaa_applications', 'corporate_applications', 'poea_applications'));
	}

	public function downloadChecklist(Request $request)
	{

		$pdf = new TCPDF();
		$pdf->AddPage();
		$pdf->resetColumns();
		$pdf->setEqualColumns(2, 30);

		$currentDate = Carbon::now()->format('Y-m-d');
		$randomToken = Str::random(10); // Generate a random token

		$qrCodeContent = 'http://192.168.1.4/discovery-visa-system/public/application_batches/checklist?date=' . $currentDate . '&token=' . $randomToken;		$qrCode = QrCode::format('png')->errorCorrection('H')->generate($qrCodeContent);
		$base64QrCode = base64_encode($qrCode);
		
		$date = Carbon::now()->toFormattedDateString();
		$current_date = $date;
		$visatypes = VisaType::orderBy('id', 'asc')->get();
		$branch = $request->user()->branch;
		
		$walkin_applications = DB::table('applications')
		->where(function ($query) use ($branch) {
			$query->where(function ($query) {
				$query->where('customer_type', '=', 'Walk-In')
					->orWhere('customer_type', '=', 'Corporate')
					->orWhere('customer_type', '=', 'POEA')
					->orWhere('customer_type', '=', 'Courier')
					->orWhere('customer_type', '=', 'Expo')
					->orWhere('customer_type', '=', 'Others');
			})
			->where('branch', '=', $branch)
			->where('application_status', '!=', '10')
			->where('application_status', '!=', '9')
			->where(function ($query) {
				$query->where('payment_status', '=', 'PAID')
					->orWhere('payment_status', '=', 'UNPAID');
			})
			->where('batch_no', '=', NULL);
		})
		->orderBy('lastname', 'asc')
		->get();
	

		$pdf->resetColumns();
		$pdf->setEqualColumns(2, 30);

		$piata_applications = DB::table('applications')
								->where('application_status','!=','10')
								->where('application_status','!=','9')
								->where('customer_type','=','PIATA')
								->where('payment_status','=','PAID')
								->where('branch','=',$branch)
								->where(function ($query) {
									$query->where('created_at', '<', Carbon::today())
										->orWhere(function ($query) {
											$query->where('created_at', '>=', Carbon::today())
												->whereIn('application_status', [1, 2]);
										});
								})								
								->where('batch_no','=',NULL)
								->orderBy('lastname','asc')
								->get();

		$ptaa_applications = DB::table('applications')
								->where('application_status','!=','10')
								->where('application_status','!=','9')
								->where('customer_type','=','PTAA')
								->where('payment_status','=','PAID')
								->where('branch','=',$branch)
								->where(function ($query) {
									$query->where('created_at', '<', Carbon::today())
										->orWhere(function ($query) {
											$query->where('created_at', '>=', Carbon::today())
												->whereIn('application_status', [1, 2]);
										});
								})								
								->where('batch_no','=',NULL)
								->orderBy('lastname','asc')
								->get();
		
				
	$pdf = PDF::loadView('application_batches.checklist_pdf', compact('qrCodeContent','base64QrCode','branch', 'date', 'walkin_applications', 'piata_applications', 'ptaa_applications'));
	return $pdf->stream($branch . ' Checklist for ' . $date . '.pdf');
	// return $pdf->download($branch . ' Checklist for ' . $date . '.pdf');

    // Save the PDF file on the server
    $pdfPath = storage_path('app/checklist_' . $branch . '_' . $date . '.pdf');
    $pdf->save($pdfPath);

    // Generate a URL for accessing the PDF file on the phone
    $pdfUrl = url('storage/checklist_' . $branch . '_' . $date . '.pdf');

    return $pdfUrl;
	// return $pdf->download($branch . ' Checklist for ' . $date . '.pdf');
	}

	public function finalPDFChecklist(Request $request)
	{
		
		$date = Carbon::now()->toFormattedDateString();
		$current_date = $date;
		$visatypes = VisaType::orderBy('id', 'asc')->get();
		$branchCode = $request->user()->branch;
		
		$walkin_applications = DB::table('applications')
		->where(function ($query) {
			$query->where('customer_type', '=', 'Corporate')
				->orWhere(function ($query) {
					$query->where('customer_type', '!=', 'Corporate')
						->where('payment_status', '=', 'PAID');
				});
			$query->orWhere(function ($query) {
				$query->where('customer_type', '=', 'Walk-In')
					->orWhere('customer_type', '=', 'Mobile Service')
					->orWhere('customer_type', '=', 'Via Courier')
					->orWhere('customer_type', '=', 'POEA')
					->orWhere('customer_type', '=', 'Courier')
					->orWhere('customer_type', '=', 'Expo')
					->orWhere('customer_type', '=', 'Others');
			});
		})
								->where('branch','=',$branch)
								// ->where('payment_status','=','PAID')
								->where('branch', '=', $request->user()->branch)
								->where(function ($query) {
									$query->where('created_at', '<', Carbon::today())
										->orWhere(function ($query) {
											$query->where('created_at', '>=', Carbon::today())
												->whereIn('application_status', [1, 2]);
										});
								})								
								->where('batch_no','=',NULL)
								->orderBy('lastname','asc')
								->get();

		$piata_applications = DB::table('applications')
								->where('customer_type','=','PIATA')
								->where('payment_status','=','PAID')
								->where('branch', '=', $request->user()->branch)
								->where(function ($query) {
									$query->where('created_at', '<', Carbon::today())
										->orWhere(function ($query) {
											$query->where('created_at', '>=', Carbon::today())
												->whereIn('application_status', [1, 2]);
										});
								})								
								->where('batch_no','=',NULL)
								->orderBy('lastname','asc')
								->get();

		$ptaa_applications = DB::table('applications')
								->where('customer_type','=','PTAA')
								->where('payment_status','=','PAID')
								->where('branch', '=', $request->user()->branch)
								->where(function ($query) {
									$query->where('created_at', '<', Carbon::today())
										->orWhere(function ($query) {
											$query->where('created_at', '>=', Carbon::today())
												->whereIn('application_status', [1, 2]);
										});
								})								
								->where('batch_no','=',NULL)
								->orderBy('lastname','asc')
								->get();

		$corporate_applications = DB::table('applications')
								->where('customer_type','=','Corporate')
								->where('branch', '=', $request->user()->branch)
								->where('branch','=',$branch)
								->where(function ($query) {
									$query->where('created_at', '<', Carbon::today())
										->orWhere(function ($query) {
											$query->where('created_at', '>=', Carbon::today())
												->whereIn('application_status', [1, 2]);
										});
								})								
								->where('batch_no','=',NULL)
								->orderBy('lastname','asc')
								->get();

        $poea_applications = DB::table('applications')
								->where('customer_type','=','POEA')
								->where('branch', '=', $request->user()->branch)
								->where('branch','=',$branch)
								->where(function ($query) {
									$query->where('created_at', '<', Carbon::today())
										->orWhere(function ($query) {
											$query->where('created_at', '>=', Carbon::today())
												->whereIn('application_status', [1, 2]);
										});
								})								
								->where('batch_no','=',NULL)
								->orderBy('lastname','asc')
								->get();
		
		$walkin_applications = $walkin_applications->concat($corporate_applications)->concat($poea_applications);
		
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
		$currentUserBranch = $request->user()->branch;
		$branch = Branch::where('code', $currentUserBranch)->first();
		$branch_code = $branch->code;
		$batch_no_string = Carbon::now()->format('Ymd') . $branch_code;
		

		// if(ApplicationBatch::where('batch_no', $batch_no_string)->exists()){
		// 	return back()->with('status', "Submission List Generated.");
		// }
		
		$status = 2; //Sent to Main Office (Batch Status)
		if($branch->id == 1) $status = 6; //if batch is for Main Office, go straight to Submitted to Embassy (Batch Status)

		$paid_applications = DB::table('applications')
		->where('branch', '=', $branch->code)
		->where(function ($query) {
			$query->where('customer_type', '=', 'Walk-In')
				->orWhere('customer_type', '=', 'PIATA')
				->orWhere('customer_type', '=', 'PTAA')
				->orWhere('customer_type', '=', 'POEA')
				->orWhere('customer_type', '=', 'Courier')
				->orWhere('customer_type', '=', 'Expo')
				->orWhere('customer_type', '=', 'Others')
				->orWhere(function ($query) {
					$query->where('customer_type', '!=', 'Corporate')
						->where('payment_status', '=', 'PAID');
				});
		})
		// ->where('application_status', '=', 1)
		->whereIn('application_status', [1, 2])
		->where('payment_status','=','PAID')
		->where('batch_no', '=', NULL)
		->where(function ($query) {
				$query->whereNotNull('branch')
					->orWhereNull('branch');
			})
		->update(['batch_no' => $batch_no_string, 'application_status' => $status]);
		

		$finalized_batch = new ApplicationBatch([
			'batch_no' => $batch_no_string,
			'batch_date' => Carbon::today(),
			'total_applications' => $paid_applications,
			'status' => $status,
			'branch' => $branch_code
		]);
		$finalized_batch->save();

		AccountReceivableController::generateAccountReceivables($batch_no_string, Carbon::today());

		// if($branch->id == 1) //if user is from head office
		if ($currentUserBranch == $branch->code) {
			DB::table('applications')
				->where('batch_no', '=', $batch_no_string)
				->update(['date_received_by_main_office' => Carbon::today()]);
	
			// Introduce a flag variable to track if the email has been sent
			$emailSent = false;
	
			if (!$branch->submission_list_generated) {
				$this->generateSubmissionList($request);
				$emailSent = true;
			}
	
			// Set the submission_list_generated flag to true
			// $branch->submission_list_generated = true;
			$branch->save();
	
			if ($emailSent) {
				return redirect('/application_batches/finalize_batch_page')->with('status', 'Submission List Generated!');
			} else {
				return redirect('/application_batches/finalize_batch_page')->with('status', 'Submission List already generated!');
			}
		}
	
		return redirect('/application_batches/finalize_batch_page')->with('status', $batch_no_string . ' has been finalized.');
	}
	
	
	public static function generateSubmissionList(Request $request)
	{
		$current_date = Carbon::now()->toFormattedDateString();
		$branch = Branch::where('code', $request->user()->branch)->first();
		// $sub_batch_no = "S" . $branch . "_" . Carbon::now()->format('Ymd');
		$branchCode = $branch->code;
		$sub_batch_no = $branchCode . Carbon::now()->format('Ymd');
		
		//for e-mail
		// $mailing_list = User::find(1); // Get the user with ID 1
		$mailing_list = User::where('role', 'ADMIN')->pluck('email');
		$mailing_list = $mailing_list->toArray();		
		$branchEmail = $branch->email;

		$partner_companies = PartnerCompany::all();
		$partner_companies_array = array();
		foreach ($partner_companies as $company) {
			$partner_companies_array[$company->id] = $company->name;
		}
	
		DB::table('applications')
			->whereIn('application_status', [1])
			->update(['submission_batch_no' => $sub_batch_no]);

			if ($branchCode === "MNL") {
				// If branch is "MNL", set application_status to 3
				DB::table('applications')
					->whereIn('application_status', [1])
					->update(['application_status' => 3]);
			} else {
				// If branch is not "MNL", set application_status to 2
				DB::table('applications')
					->whereIn('application_status', [1])
					->update(['application_status' => 2]);
			}
	
			$walkin_applications = DB::table('applications')
			->where(function ($query) {
				$query->where('customer_type', '=', 'Corporate')
					->orWhere(function ($query) {
						$query->where('customer_type', '!=', 'Corporate')
							->where('payment_status', '=', 'PAID');
					});
				$query->orWhere(function ($query) {
					$query->where('customer_type', '=', 'Walk-In')
						->orWhere('customer_type', '=', 'Mobile Service')
						->orWhere('customer_type', '=', 'Via Courier')
						->orWhere('customer_type', '=', 'POEA')
						->orWhere('customer_type', '=', 'Courier')
						->orWhere('customer_type', '=', 'Expo')
						->orWhere('customer_type', '=', 'Others');
				});
			})
			->where('submission_batch_no', '=', $sub_batch_no)
			->whereIn('application_status', [1, 2, 3])
			->where(function ($query) {
				$query->where('created_at', '<', Carbon::today())
					->orWhere(function ($query) {
						$query->where('created_at', '>=', Carbon::today())
							->whereIn('application_status', [1, 2, 3]);
					});
			})    
			->where('branch', '=', $request->user()->branch)
			->where(function ($query) use ($sub_batch_no) {
				$query->where('batch_no', '=', NULL)
					->orWhere(function ($query) use ($sub_batch_no) {
						$query->where('batch_no', 'NOT LIKE', "%$sub_batch_no%");
					});
			})
			->orderByRaw("visa_type ASC, lastname ASC")
			->get();
		
	
		$piata_applications = DB::table('applications')
			->where('customer_type', '=', 'PIATA')
			->where('submission_batch_no', '=', $sub_batch_no)
			->whereIn('application_status', [1, 2, 3])
			->where('payment_status','=','PAID')
			->where(function ($query) {
				$query->where('created_at', '<', Carbon::today())
					->orWhere(function ($query) {
						$query->where('created_at', '>=', Carbon::today())
						->whereIn('application_status', [1, 2, 3]);
					});
			})		
			->where('branch', '=', $request->user()->branch)
			->where(function ($query) use ($sub_batch_no) {
				$query->where('batch_no', '=', NULL)
					->orWhere(function ($query) use ($sub_batch_no) {
						$query->where('batch_no', 'NOT LIKE', "%$sub_batch_no%");
					});
			})
			->orderByRaw("visa_type ASC, lastname ASC")
			->get();
	
		$ptaa_applications = DB::table('applications')
			->where('customer_type', '=', 'PTAA')
			->where('submission_batch_no', '=', $sub_batch_no)
			->whereIn('application_status', [1, 2, 3])
			->where('payment_status','=','PAID')
			->where(function ($query) {
				$query->where('created_at', '<', Carbon::today())
					->orWhere(function ($query) {
						$query->where('created_at', '>=', Carbon::today())
							->whereIn('application_status', [1, 2, 3]);
					});
			})		
			->where('branch', '=', $request->user()->branch)
			->where(function ($query) use ($sub_batch_no) {
				$query->where('batch_no', '=', NULL)
					->orWhere(function ($query) use ($sub_batch_no) {
						$query->where('batch_no', 'NOT LIKE', "%$sub_batch_no%");
					});
			})
			->orderByRaw("visa_type ASC, lastname ASC")
			->get();
	
		$pdf = PDF::loadView('application_batches/pdf', compact('walkin_applications', 'piata_applications', 'ptaa_applications', 'poea_applications',
			'partner_companies_array', 'current_date'));
		$pdf_array[$sub_batch_no] = $pdf->download()->getOriginalContent();
		Storage::put('public/pdf/' . $sub_batch_no . '.pdf', $pdf_array[$sub_batch_no]);
	
		if ($branch->submission_list_generated) {
			// The submission list has already been generated and sent
			// You can choose to return an error message or handle it as per your requirement
			return redirect()->back()->with('status', 'Submission list has already been generated and sent.');
		}
		
		//send email to admins
		Mail::to($mailing_list)->send(new SubmissionListGenerated($pdf_array, $current_date, $branch, $branchEmail));
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

		public function checkApprovalCode(Request $request)
		{
			if ($request->ajax()) {
				$approval_code = $request->get('approval_code');
				$user_type = $request->user()->role;
				$code_is_valid = false;
				$approver;
				$approval_request;
		
				if ($approval_code != '') {
					$approver = DB::table('users')->where('approval_code', $approval_code);
		
					if ($approver->count() > 0) {
						$code_is_valid = true;
						return response()->json(['status' => 'success']);
					}
				}
		
				// If the code reaches this point, it means the approval code is invalid or empty.
				return response('', 200);
			}
		}

		public static function generateApprovalcode(Request $request)
{

    // Generate OTP code
    $otp_code = rand(100000, 999999);
    $current_date = Carbon::now()->toFormattedDateString();
    $branch = Branch::where('code', $request->user()->branch)->first();
    $branchEmail = $branch->email;

    // Save the OTP code to the user's session
    $request->session()->put('approval_code', $otp_code);
    $adminUser = User::where('role', 'Admin')->first();
    $adminUser->approval_code = $otp_code;
    $adminUser->save();

	// Save the OTP code to the user's session
    $request->session()->put('approval_code', $otp_code);

    // Set expiration for 24 hours
    $expiration_time = Carbon::now()->addHours(24);
    $request->session()->put('otp_expiration', $expiration_time);

    // Send email to the admin user
    $branch = Branch::where('code', $adminUser->branch)->first();
    $branchEmail = $branch->email;
    $mailing_list = User::where('role', 'ADMIN')->pluck('email')->toArray();
    Mail::to($mailing_list)->send(new ApprovalCodeGenerated($otp_code, $current_date, $branchEmail));
}


public function checkOtpCode(Request $request)
{
    $userProvidedOtp = $request->input('approval_code');

    // Retrieve the user with the role "Admin" from the database
    $adminUser = User::where('role', 'Admin')->first();

    // Retrieve the OTP code for the admin user from the database
    $storedOtp = $adminUser->approval_code;

	
    // Check if the OTP is expired
    $otpExpiration = $request->session()->get('otp_expiration');
    if (Carbon::now()->gt($otpExpiration)) {
        return response()->json(['status' => 'error', 'message' => 'OTP has expired']);
    }

    if ($userProvidedOtp == $storedOtp) {
        // The provided OTP matches the stored OTP for the admin user
        // Proceed with the necessary action (e.g., updating payment status)
        return response()->json(['status' => 'success']);
    } else {
        // The provided OTP is incorrect
        return response()->json(['status' => 'error', 'message' => 'Incorrect OTP']);
    }
}


}
