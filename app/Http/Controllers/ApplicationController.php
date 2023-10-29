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
                                            "Corporate" => "Corporate",
                                            "Courier" => "Courier",
                                            "Expo" => "Expo",
                                            "Others" => "Others",
                                            "PIATA" => "PIATA",
                                            "POEA" => "POEA",
                                            "PTAA" => "PTAA");

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

		$application_status_array = [
			'1' => 'NEW Application',
			'2' => 'Sent to Main Office',
			'3' => 'Received by Main Office',
			'4' => 'Sent to Original Branch',
			'5' => 'Received by Original Branch',
			'6' => 'Submitted to Embassy',
			'7' => 'Received from Embassy',
			'8' => 'Sent to/Claimed by Client',
			'9' => 'Incomplete',
			'10' => 'Pending Approval',
			'11' => 'Additional Documents Required',
 			'12' => 'Released by Embassy',
                        '13' => 'Resubmitted to JPN',
                        '14' => 'Passport Return from JPN Embassy'
		];
		

		if ($request->ajax()) {
			$searchString = $request->get('searchString');
			$start_date = $request->input('fromdate');
			$end_date = $request->input('todate');
			$application_status = $request->input('application_status');
	
			$user = $request->user();
			$role = $user->role;
			$branch = Auth::user()->branch;
	
			$query = DB::table('applications')->orderBy('id', 'desc');
			
	
			if ($role !== 'Admin') {
				if ($role === 'Encoder' && $branch === 'MNL') {
					$query->where(function ($query) {
						$query->where('application_status', 2)
							->orWhere('branch', 'MNL');
					});
				} else {
					$query->where('branch', $branch);
				}
			}
	
			if (!empty($searchString)) {
				$query->where(function ($query) use ($searchString) {
					$query->where('reference_no', 'LIKE', '%' . $searchString . '%')
						->orWhere('firstname', 'LIKE', '%' . $searchString . '%')
						->orWhere('middlename', 'LIKE', '%' . $searchString . '%')
						->orWhere('lastname', 'LIKE', '%' . $searchString . '%')
						->orWhere('group_name', 'LIKE', '%' . $searchString . '%');
				});
			}
	
			if (!empty($application_status)) {
				$statusValues = explode(',', $application_status); // Convert the selected status string to an array
				$query->whereIn('application_status', $statusValues);
			}
			


			if (!empty($start_date) && !empty($end_date)) {
				$start_datetime = $start_date . ' 00:00:00';
				$end_datetime = $end_date . ' 23:59:59';
				$query->whereBetween('created_at', [$start_datetime, $end_datetime]);
			}

	
			$data = $query->paginate(20);
			// dd($request->all());

	
			return view('applications.application_list', compact('data', 'application_status_array'));
		}
	}
	
	
	
		
	

	
	

	public function filterStatus(Request $request)
	{
		$application_status = $request->get('application_status');
	
		$application_status_array = array(
			'1' => 'NEW Application',
			'2' => 'Sent to Main Office',
			'3' => 'Received by Main Office',
			'4' => 'Sent to Original Branch',
			'5' => 'Received by Original Branch',
			'6' => 'Submitted to Embassy',
			'7' => 'Received from Embassy',
			'8' => 'Sent to/Claimed by Client',
			'9' => 'Incomplete',
			'10' => 'Pending Approval',		
			'11' => 'Additional Documents Required',
 			'12' => 'Released by Embassy',
                        '13' => 'Resubmitted to JPN',
                        '14' => 'Passport Return from JPN Embassy'
		);
	
		$application_status = (int)$request->get('application_status');

		if ($application_status === 0) {
			$data = Application::orderBy('id', 'desc')->paginate(20);
		} else {
			$data = Application::where('application_status', $application_status)->orderBy('id', 'desc')->paginate(20);
		}
		
	
		$view = view('applications.application_list')
			->with('data', $data)
			->with('application_status_array', $application_status_array)
			->render();
	
		$response = [
			'view' => $view,
			'application_status_array' => $application_status_array
		];
	
		return response()->json($response);
	}
		

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


			return view('applications.past_applications', compact('pastApplications'))->render();
		}
	}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
	public function create(Request $request)
	{
		$userBranch = auth()->user()->branch;
		$visatypes = VisaType::where('branch', $userBranch)->orderBy('id', 'asc')->get();
		$selectedVisaType = $request->input('visa_type');
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

		if ($selectedVisaType) {
			$visaType = VisaType::where('name', $selectedVisaType)->first();
			$requiredDocs = $visaType->documents_submitted;
		} else {
			$requiredDocs = null;
		}
		
	
		return view('applications.create', compact('userBranch','docs','requiredDocs','branches', 'pickupPrice', 'visatypes', 'documentlist',  'result', 'docs_filipino', 'docs_japanese', 'docs_foreign', 'customer_type_array', 'customer_company'))->with('pickupPrice', $pickupPrice);
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
 			'submitter' => 'required',
			'pickupMethod' => 'required',
			'pickup_fee' => 'nullable',
                        'lastname' => ($request->input('visa_type') === 'FOREIGN PASSPORT' || $request->input('visa_type') === 'FOREIGN PASSPORT (INDIAN)') ? 'required_without:firstname' : 'required',
                        'firstname' => ($request->input('visa_type') === 'FOREIGN PASSPORT' || $request->input('visa_type') === 'FOREIGN PASSPORT (INDIAN)') ? 'required_without:lastname' : 'required',
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
			'submitter' => strtoupper($request->input('submitter')),
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
			'last_update_by' => $request->user()->username,
			'visa_result' => strtoupper($request->get('visa_result')),
			'released_to' => strtoupper($request->get('released_to')),
			'courier_tracking' => $request->get('courier_tracking')

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
		$user = auth()->user();
		$visatypes = VisaType::where('branch', $user->branch)->orderBy('id', 'asc')->get();
		$documentlist = RequiredDocument::orderBy('id', 'asc')->get();
		$customer_type_array = $this->customer_type_array;
		$customer_company = PartnerCompany::all();
		$submittedDocs = explode(",", $application->documents_submitted);
		$documents = RequiredDocument::whereIn('id', $submittedDocs)->get();
		$branch = Auth::user()->branch;
		$result = VisaType::with('documents')->orderBy('id', 'asc')->paginate(20);
		$docs = RequiredDocument::all();
		$docs_filipino = collect($docs)->whereIn('type', 'FILIPINO');
		$docs_japanese = collect($docs)->whereIn('type', 'JAPANESE');
		$docs_foreign = collect($docs)->whereIn('type', 'FOREIGN');
	
		if (in_array($application->application_status, [1, 4, 5, 9, 10, 11])) {
			$batch_no_string = null; // or $batch_no_string = '';
		} else {
			$batch_no_string = $application->batch_no;
		}
	
		return view('applications.edit', compact('user', 'submittedDocs', 'application', 'visatypes', 'documentlist', 'customer_type_array', 'documents', 'result', 'docs_filipino', 'docs_japanese', 'docs_foreign', 'customer_company', 'branch', 'batch_no_string'));
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
 			'submitter' => 'required',
			'pickupMethod' => 'required',
			'pickup_fee' => 'nullable',
			'branch' => 'required',
			'lastname' => ($request->input('visa_type') === 'FOREIGN PASSPORT' || $request->input('visa_type') === 'FOREIGN PASSPORT (INDIAN)') ? 'required_without:firstname' : 'required',
                        'firstname' => ($request->input('visa_type') === 'FOREIGN PASSPORT' || $request->input('visa_type') === 'FOREIGN PASSPORT (INDIAN)') ? 'required_without:lastname' : 'required',
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
			'documents_submitted' => 'sometimes|nullable',
			'payment_status' => 'required',
			'verification_no' => 'nullable',
			'tracking_no' => 'nullable',
			'visa_result' => 'nullable',
			'released_to' => 'nullable',
			'courier_tracking' => 'nullable'
			
		]);

		$application = Application::find($id);

		if ($application->application_status != $request->get('application_status')) {
			if ($request->get('application_status') == '1') {
				$request_type = 'Mark as New Application';
			} else {
				$request_type = 'Mark as Incomplete';
			}
		
			if (!in_array($request->get('application_status'), ['6', '2'])) {
				$approval_request = new PendingApprovals([
					'application_id' => $id,
					'request_type' => $request_type,
					'requested_by' => $request->user()->username,
					'request_date' => Carbon::now()
				]);
				$approval_request->save();
			}
		
			$application->application_status = $request->get('application_status');

			if ($request->get('application_status') == 7) {
				$application->receiver_from_embassy = $request->user()->username;
				$dateField = 'date_received_from_embassy';
                        } elseif ($request->get('application_status') == 6) {
                                $application->submitted_to_embassy = $request->user()->username;
                                $dateField = 'date_submitted_to_embassy';
			} elseif ($request->get('application_status') == 8) {
				$application->distributed_by = $request->user()->username;
				$dateField = 'date_distributed';
			} elseif ($request->get('application_status') == 11) {
                                $application->additional_docs = $request->user()->username;
                                $dateField = 'date_docsRequired';
                        } elseif ($request->get('application_status') == 12) {
                                $application->released_by_embassy = $request->user()->username;
                                $dateField = 'date_released_by_embassy';
                        }

			if (isset($dateField)) {
				$application->$dateField = Carbon::now();
				$application->save();
			}
		}
		
		$application->reference_no = $request->get('reference_no');
		$application->application_status = $request->get('application_status');
		$application->customer_type = $request->get('customer_type');
		$application->customer_company = $request->get('customer_company');
		$application->group_name = $request->get('group_name');
		$application->submitter = strtoupper($request->get('submitter'));
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
		$application->visa_result = strtoupper($request->get('visa_result'));
		$application->released_to = strtoupper($request->get('released_to'));
		$application->courier_tracking = $request->get('courier_tracking');

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
			$branch = Auth::user()->branch;
			$branches = Branch::all();
			$searchString = $request->get('searchString');
			$application = Application::where('reference_no', '=', $searchString)
			->where('branch', $branch)
			->first();
			
			$modeOfPayment = Arr::pluck(collect(Other::where('type', 'mop')->get()), 'name', 'name');
			$modeOfPayment = Arr::prepend($modeOfPayment, '', '');
			$paymentRequest = Arr::pluck(collect(Other::where('type', 'pr')->get()), 'name', 'name');
			$paymentRequest = Arr::prepend($paymentRequest, '', '');
			$visaType = null;
			if ($application) {
				$visaType = VisaType::where('name', $application->visa_type)->first()
				->where('branch','=',$branch);
			}
	
			return view('cashier.confirm_payment', compact('branch', 'branches', 'searchString', 'application', 'modeOfPayment', 'paymentRequest', 'visaType'));
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
	
		return response($pdf->output(), 200)
			->header('Content-Type', 'application/pdf')
			->header('Content-Disposition', 'inline; filename="'.$application->reference_no.' Acknowledgement Receipt.pdf"');
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
	
	
	
public function showUnpaidApplicants(Request $request)
        {                
                $branch = Auth::user()->branch;
                $branches = Branch::all();
                $application = Application::where('branch', $branch)
                        ->first();
                $list = DB::table('applications')
                        ->where('payment_status', 'UNPAID')
                        // ->whereDate('created_at', Carbon::today())
                        ->where('branch', $branch)
                        ->orderBy('id', 'desc')
                        ->paginate(20);
        
                return view('cashier.unpaidList', compact('list'));
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
