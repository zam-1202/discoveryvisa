<?php

namespace App\Http\Controllers;


// use App\User;
use App\AccountReceivable;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\PendingApprovals;
use App\Application;
use App\VisaType;
use App\RequiredDocument;
use App\PartnerCompany;
use App\Branch;
use Excel;
use App\Exports\DailyReportExport;
use App\Other;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use PDF;
$visaTypes = VisaType::all();

class ApplicationController extends Controller
{
		protected $customer_type_array = array("Walk-In" => "Walk-In",
											"PIATA" => "PIATA",
											"PTAA" => "PTAA",
											"Corporate" => "Corporate",
											"POEA" => "POEA");

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
		public function index(Request $request)
		{
			$data = DB::table('applications')->where('branch', '=', $request->user()->branch)->orderBy('id','desc')->paginate(20);
			return view('applications.index',compact('data'));
		}

	/**
	 * AJAX function for Application Search
	 */
	public function fetch_data(Request $request)
	{
		if($request->ajax())
		{
			$searchString = $request->get('searchString');
			$start_date = $request->get('start_date');
			$end_date = $request->get('end_date');

			if($searchString != '')
			{
				$data = DB::table('applications')
				//  ->where('branch', '=', $request->user()->branch)
				 ->where(function($query) use ($searchString){
							$query->where('reference_no','LIKE','%'.$searchString.'%')
								  ->orWhere('firstname','LIKE','%'.$searchString.'%')
								  ->orWhere('middlename','LIKE','%'.$searchString.'%')
								  ->orWhere('lastname','LIKE','%'.$searchString.'%')
								  ->orWhere('group_name','LIKE','%'.$searchString.'%');
						})
				 ->orderBy('id','desc')
				 ->paginate(20);
			}
			else {
				$data = DB::table('applications')->orderBy('id','desc')->paginate(20);
				// $data = DB::table('applications')->where('branch', '=', $request->user()->branch)->orderBy('id','desc')->paginate(20);

			}
			if ($request->user()->branch !== 'MNL') {
				$query->where('branch', '=', $request->user()->branch);
			}

			return view('applications.application_list', compact('data'))->render();
		}
	}

	/**
	 * AJAX function for Application Search FOR ADMIN ONLY
	 */
	// public function fetch_data_forAdmin(Request $request)
	// {
	// 	if($request->ajax())
	// 	{
	// 		$searchString = $request->get('searchString');

	// 		if($searchString != '')
	// 		{
	// 			$data = DB::table('applications')
	// 			 ->where(function($query) use ($searchString){
	// 						$query->where('reference_no','LIKE','%'.$searchString.'%')
	// 							  ->orWhere('firstname','LIKE','%'.$searchString.'%')
	// 							  ->orWhere('middlename','LIKE','%'.$searchString.'%')
	// 							  ->orWhere('lastname','LIKE','%'.$searchString.'%')
	// 							  ->orWhere('group_name','LIKE','%'.$searchString.'%');
	// 					})
	// 			 ->orderBy('application_date','desc')
	// 			 ->paginate(20);
	// 		}
	// 		else {
	// 			$data = DB::table('applications')->orderBy('id','desc')->paginate(20);
	// 		}

	// 		return view('applications.application_list', compact('data'))->render();
	// 	}
	// }
	

	/**
	 * For Past Applications pop-up window
	 */
	public function past_applications(Request $request)
	{
		if($request->ajax())
		{
			$lastname = $request->get('lastname');
			$firstname = $request->get('firstname');

			$pastApplications = DB::table('applications')
				->select('visa_type', 'application_date', 'application_status')
				->where('lastname', 'LIKE', '%'.$lastname.'%')
				->where('firstname', 'LIKE', '%'.$firstname.'%')
				->orderBy('id', 'desc')
				->paginate(10);


			return view('applications.past_applications', compact('pastApplications', 'application_status_array'))->render();
		}
	}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
	public function create(Request $request)
	{
		$visatypes = VisaType::orderBy('id', 'asc')->get();
		$documentlist = RequiredDocument::orderBy('id', 'asc')->get();
		$customer_type_array = $this->customer_type_array;
		$customer_company = PartnerCompany::all(); // Fetch the customer company data
		$result = VisaType::with('documents')->orderBy('id', 'asc')->paginate(20);
		$docs = RequiredDocument::all();
		$docs_filipino = collect($docs)->whereIn('type', 'FILIPINO');
		$docs_japanese = collect($docs)->whereIn('type', 'JAPANESE');
		$docs_foreign = collect($docs)->whereIn('type', 'FOREIGN');
		$branch = Auth::user()->branch;
		$pickupPrice = Branch::where('code', $branch)->pluck('pickup_price')->first();
		$branches = Branch::all();
	
		return view('applications.create', compact('branches', 'pickupPrice', 'visatypes', 'documentlist', 'documents', 'result', 'selected_docs', 'docs_filipino', 'docs_japanese', 'docs_foreign', 'customer_type_array', 'customer_company'))->with('pickupPrice', $pickupPrice);
	}
	
	

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $request->validate([
			'customer_type' => 'required',
			'customer_company' => 'required',
			'group_name' => 'nullable',
			'pickupMethod' => 'required',
			'pickup_fee' => 'nullable',
			'lastname' => 'required',
			'firstname' => 'required',
			'middlename' => 'nullable',
			'birthdate' => ['required', 'date_format:Y-m-d'],
			'gender' => 'required',
			'marital_status' => 'required',
			'address' => 'required',
			'email' => 'nullable|email',
			'mobile_no' => 'nullable',
			'passport_no' => 'required',
			'passport_expiry' => 'required|date',
			'departure_date' => 'required|date',
			'visa_type' => 'required',
			'visa_price' => 'nullable',
			'handling_price' => 'required',
			'promo_code' => 'required',
			'documents_submitted' => 'required',
			'tracking_no' => 'nullable',
			'verification_no' => 'nullable',
		]);

		$pickupPrice = Branch::where('code', $request->user()->branch)->pluck('pickup_price')->first();
		$application = new Application([
			'reference_no' => $this->generateReferenceNo($request),
			'application_status' => 1,
			'customer_type' => $request->get('customer_type'),
			'customer_company'  => $request->get('customer_company'),
			'group_name' => strtoupper($request->get('group_name')),
			'pickupMethod' => $request->input('pickupMethod'),
			'pickup_fee' => ($request->pickupMethod === 'On-site') ? null : $request->input('pickup_fee'),
			'branch' => $request->user()->branch,
			'lastname' => strtoupper($request->get('lastname')),
			'firstname' => strtoupper($request->get('firstname')),
			'middlename' => strtoupper($request->get('middlename')),
			'birthdate' => $request->get('birthdate'),
			'gender' => $request->get('gender'),
			'marital_status' => $request->get('marital_status'),
			'address' => strtoupper($request->get('address')),
			'email' => $request->get('email'),
			'telephone_no' => $request->get('telephone_no'),
			'mobile_no' => $request->get('mobile_no'),
			'passport_no' => strtoupper($request->get('passport_no')),
			'passport_expiry' => $request->get('passport_expiry'),
			'departure_date' => $request->get('departure_date'),
			'remarks' => $request->get('remarks'),
			'visa_type' => $request->get('visa_type'),
			'visa_price' => $request->get('visa_price'),
			'handling_price' => $request->get('handling_price'),
			'promo_code' => $request->get('promo_code'),
			'documents_submitted' => $request->get('documents_submitted'),
			'payment_status' => 'UNPAID',
			'application_date' => Carbon::now(),
			'encoded_by' => $request->user()->username,
			'last_update_by' => $request->user()->username
		]);

		$application->save();
		return redirect('/applications')->with('success','Application saved!');
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

        $application = Application::find($id);
		// $application = Application::where('reference_no', $request->ref_no)->first();
		$visatypes = VisaType::orderBy('id', 'asc')->get();
		$documentlist = RequiredDocument::orderBy('id', 'asc')->get();
		$customer_type_array = $this->customer_type_array;
		$customer_company = PartnerCompany::all(); // Fetch the customer company data
        $submittedDocs = explode(",",$application->documents_submitted);
        $documents = RequiredDocument::whereIn('id', $submittedDocs)->get();
		$branch = Auth::user()->branch;

		
        
		
		$result = VisaType::with('documents')->orderBy('id', 'asc')->paginate(20);

		$docs = RequiredDocument::all();

        $docs_filipino = collect($docs)->whereIn('type', 'FILIPINO');
        $docs_japanese = collect($docs)->whereIn('type', 'JAPANESE');
        $docs_foreign = collect($docs)->whereIn('type', 'FOREIGN');

		return view('applications.edit', compact('submittedDocs', 'application', 'visatypes', 'documentlist', 'customer_type_array', 'documents', 'result', 'selected_docs', 'docs_filipino', 'docs_japanese', 'docs_foreign', 'customer_company', 'branch'));
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
        $request->validate([
			'reference_no' => 'required',
			'application_status' => 'required',
			'customer_type' => 'required',
			'customer_company' => 'required',
			'group_name' => 'nullable',
			'pickupMethod' => 'required',
			'pickup_fee' => 'nullable',
			'branch' => 'required',
			'lastname' => 'required',
			'firstname' => 'required',
			'middlename' => 'nullable',
			'birthdate' => 'required|date',
			'gender' => 'required',
			'marital_status' => 'required',
			'address' => 'required',
			'email' => 'nullable|email',
			'mobile_no' => 'nullable',
			'passport_no' => 'required',
			'passport_expiry' => 'required|date',
			'departure_date' => 'required|date',
			'visa_type' => 'required',
			'visa_price' => 'nullable',
			'handling_price' => 'required',
			'documents_submitted' => 'required',
			'payment_status' => 'required',
			'verification_no' => 'nullable',
			'tracking_no' => 'nullable'
			
		]);

		$application = Application::find($id);

        if ($application->application_status != $request->get('application_status'))
        {
            if ($request->get('application_status') == '1'){
                $request_type = 'Mark as New Application';
				$application->application_status = $request->get('application_status');
            } elseif ($request->get('application_status') == '2') {
				$request_type = 'Sent to Main Office';
				$application->application_status = $request->get('application_status');
			} elseif ($request->get('application_status') == '3') {
				$request_type = 'Received by Main Office';
				$application->application_status = $request->get('application_status');
			} elseif ($request->get('application_status') == '4') {
				$request_type = 'Sent to Original Branch';
				$application->application_status = $request->get('application_status');
			} elseif ($request->get('application_status') == '5') {
				$request_type = 'Received by Original Branch';
				$application->application_status = $request->get('application_status');
			} elseif ($request->get('application_status') == '6') {
				$request_type = 'Submitted to Embassy';
				$application->application_status = $request->get('application_status');
			} elseif ($request->get('application_status') == '7') {
				$request_type = 'Received from Embassy';
				$application->application_status = $request->get('application_status');
			} elseif ($request->get('application_status') == '8') {
				$request_type = 'Sent to/Claimed by Client';
				$application->application_status = $request->get('application_status');
			} else {
                $request_type = 'Mark as Incomplete';
				$application->application_status = '10';
            }

            $approval_request = new PendingApprovals([
                'application_id' => $id,
                'request_type' => $request_type,
                'requested_by' => $request->user()->username,
                'request_date' => Carbon::now()
            ]);
            $approval_request->save();

            
        }

		$application->reference_no = $request->get('reference_no');
		$application->customer_type = $request->get('customer_type');
		$application->customer_company = $request->get('customer_company');
		$application->group_name = $request->get('group_name');
		$application->pickupMethod = $request->get('pickupMethod');
		$application->pickup_fee = $request->get('pickup_fee');
		$application->branch = $request->get('branch');
		$application->lastname = strtoupper($request->get('lastname'));
		$application->firstname = strtoupper($request->get('firstname'));
		$application->middlename = strtoupper($request->get('middlename'));
		$application->birthdate = $request->get('birthdate');
		$application->gender = $request->get('gender');
		$application->marital_status = $request->get('marital_status');
		$application->address = strtoupper($request->get('address'));
		$application->email = $request->get('email');
		$application->telephone_no = $request->get('telephone_no');
		$application->mobile_no = $request->get('mobile_no');
		$application->passport_no = strtoupper($request->get('passport_no'));
		$application->passport_expiry = $request->get('passport_expiry');
		$application->departure_date = $request->get('departure_date');
		$application->remarks = $request->get('remarks');
		$application->visa_type = $request->get('visa_type');
		$application->visa_price = $request->get('visa_price');
		$application->handling_price = $request->get('handling_price');
		$application->promo_code = $request->get('promo_code');
		$application->discount_amount = $request->get('discount_amount');
		$application->documents_submitted = $request->get('documents_submitted');
		$application->payment_status = $request->get('payment_status');
		$application->or_number = $request->get('or_number');
		$application->vpr_number = $request->get('vpr_number');
		$application->tracking_no = $request->get('tracking_no');
		$application->verification_no = $request->get('verification_no');
		$application->last_update_by = $request->user()->username;

		$application->save();

		return redirect('/applications')->with('success', 'Application saved!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $application = Application::find($id);
		$application->delete();

		return redirect('/applications')->with('success', 'Application deleted!');
    }

	/**
	 * Payment Form for Cashier's use
	 */
	public function showPaymentForm(Request $request)
	{
		$referenceNo = $request->reference_no;
		return view('cashier.receive_payment', compact('referenceNo'));
	}

	public function retrievePaymentForm(Request $request)
	{
		if ($request->ajax()) {
			$userBranch = DB::table('users')->select('branch')->where('branch', '=', $request->user()->branch)->first();
	
			$searchString = $request->get('searchString');
			$application = Application::where('reference_no', '=', $searchString)->first();
			$modeOfPayment = Arr::pluck(collect(Other::where('type', 'mop')->get()), 'name', 'name');
			$modeOfPayment = Arr::prepend($modeOfPayment, '', '');
			$paymentRequest = Arr::pluck(collect(Other::where('type', 'pr')->get()), 'name', 'name');
			$paymentRequest = Arr::prepend($paymentRequest, '', '');
			$branch = Auth::user()->branch;
			$isBranchMatch = ($userBranch && $userBranch->branch == $application->branch);
			$visaType = null;
			if ($application) {
				$visaType = VisaType::where('name', $application->visa_type)->first();
			}
	
			return view('cashier.confirm_payment', compact('isBranchMatch', 'branch', 'searchString', 'application', 'modeOfPayment', 'paymentRequest', 'visaType', 'userBranch'));
		}
	}
	
	public function markCustomerAsPaid(Request $request)
	{
		$application = DB::table('applications')
						->where('reference_no', '=', $request->get('reference_no'))
						->update(['or_number' => $request->get('or_number'),
                                  'payment_mode' => $request->get('payment_mode'),
                                  'payment_request' => $request->get('payment_request'),
								  'payment_date' => Carbon::now(),
								  'payment_received_by' => $request->user()->username,
								  'payment_status' => 'PAID']);

        $data = Application::where('reference_no', $request->get('reference_no'))->first();
		$account_receivables = null;
		if ($data) {
			$account_receivables = AccountReceivable::where('batch_no', $data->batch_no)
												->where('company', $data->customer_company)
												->first();
		}
		
        // $account_receivables = AccountReceivable::where('batch_no', $data->batch_no)
        //                                 ->where('company', $data->customer_company)
        //                                 ->first();



		if ($account_receivables) {
			if ($account_receivables->total_amount <= $data->visa_price) {
				$account_receivables->delete();
			} else {
				$account_receivables->total_amount -= $data->visa_price;
				$account_receivables->save();
			}
		}
		return 'Payment for '. $request->get('reference_no') . ' received';
	}

	public function markCustomerAsUnpaid(Request $request)
{
	$application = DB::table('applications')
                ->where('reference_no', '=', $request->get('reference_no'))
                ->update(['or_number' => null,
                          'payment_mode' => null,
                          'payment_request' => null,
                          'payment_date' => null,
                          'payment_received_by' => null,
                          'payment_status' => 'UNPAID']);

    return 'Payment for ' . $request->get('reference_no') . ' marked as UNPAID';
}


	/**
	 * Generate unique Reference_No
	 *
	 * @param Request $request
	 * @return String
	 */
	protected function generateReferenceNo(Request $request)
	{
		$reference_no = $this->generateRandomCode($request);

		//ensure that $reference_no is unique
		while(Application::where('reference_no', $reference_no)->count() > 0)
		{
			$reference_no = $this->generateRandomCode($request);
		}

		return $reference_no;
	}

	/**
	 * Generate random code with the following format:
	 * [Branch code of Encoder] + [current date in 'ymd' format] + [random 3-character alphabetic string]
	 *
	 * @param Request $request
	 * @return String
	 */
	protected function generateRandomCode(Request $request)
	{
		 return $request->user()->branch . date("ymd") . chr(mt_rand(65, 90)) . chr(mt_rand(65, 90)) . chr(mt_rand(65, 90));
	}

	public function redeemPromoCode(Request $request)
	{
		if($request->ajax())
		{
			$page = $request->get('page');
			$code = $request->get('promo_code');
			$id = $request->get('id');
			$promo_code = DB::table('promo_codes')->where('code', $code)->first();
			$quantity = DB::table('applications')->where('promo_code', $code)->count();


            if ($promo_code) {
                if($quantity+1 <= $promo_code->max_quantity)
                {
                    //apply promo code
                    $application = Application::find($id);
                    $application->promo_code = $code;
                    $application->save();

                    return array($promo_code->discount, "success");
                } else {
                    return array(0,"This Promo Code has reached its max limit");
                }
            }
		}
	}

    public function downloadReport(Request $request){
        if (Auth::user()) {
            $branch = Auth::user()->branch;
            $role = Auth::user()->role;
            return Excel::download(new DailyReportExport($request->date, $branch, $role), 'sample.xlsx', null, [\Maatwebsite\Excel\Excel::XLSX]);
        }

    }

    public function downloadAcknowledgementReceipt(Request $request){
        $application = Application::where('reference_no', $request->ref_no)->first();
        $submittedDocs = explode(",",$application->documents_submitted);
        $documents = RequiredDocument::whereIn('id', $submittedDocs)->get();
        $pdf = PDF::loadView('cashier.acknowledgement_receipt', ['application' => $application, 'docs' => $documents]);
		return $pdf->download($application->reference_no . ' Acknowledgement Receipt.pdf');

    }
	
	public function showVisaType($visaID)
	{
		$result = VisaType::where('id', $visaID)->with('documents')->orderBy('id', 'asc')->first();
	
		$selected_docs = [];
		foreach ($result->documents as $key => $value) {
			array_push($selected_docs, $value->id);
		}
	
		$docs = RequiredDocument::all();
	
		$docs_filipino = $docs->where('type', 'FILIPINO');
		$docs_japanese = $docs->where('type', 'JAPANESE');
		$docs_foreign = $docs->where('type', 'FOREIGN');
	
		return view('admin.visa.index', compact('result', 'docs_filipino', 'docs_japanese', 'docs_foreign'));
	}
	

	public function checkApprovalCode(Request $request)
	{
		if($request->ajax())
		{
			$approval_code = $request->get('approval_code');
			$user_type = $request->user()->role;

			$code_is_valid = false;

			$approver;
			$approval_request;

					if($approval_code != '') {
						$approver = DB::table('users')->where('approval_code', $approval_code);
						
						if($approver->count() > 0){
							$code_is_valid = true;
							return response()->json(['status' => 'success']);
						}
					} return response()->json(['status' => 'Request has been rejected'], 400);
		}
	}

		public function showUnpaidApplicants(Request $request)
		{		
			
			$list = DB::table('applications')
					->where('payment_status', 'UNPAID')
					->whereDate('created_at', Carbon::today())
					->get();
	

		return view('cashier.unpaidList', compact ('list'));
			
		}

		public function filter(Request $request)
		{
			$start_date = $request->input('start_date');
			$end_date = $request->input('end_date');

			// dd($start_date, $end_date);
		
			$data = DB::table('applications')
				->whereDate('application_date', '>=', $start_date)
				->whereDate('application_date', '<=', $end_date)
				->orderBy('id', 'desc')
				->paginate(20);
		
			return view('applications.index', compact('data'));
		}
}