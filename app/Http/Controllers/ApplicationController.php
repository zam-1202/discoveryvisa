<?php

namespace App\Http\Controllers;

use App\AccountReceivable;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\PendingApprovals;
use App\Application;
use App\VisaType;
use App\RequiredDocument;
use App\Branch;
use Excel;
use App\Exports\DailyReportExport;
use App\Other;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use PDF;

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

			if($searchString != '')
			{
				$data = DB::table('applications')
				 ->where('branch', '=', $request->user()->branch)
				 ->where(function($query) use ($searchString){
							$query->where('reference_no','LIKE','%'.$searchString.'%')
								  ->orWhere('firstname','LIKE','%'.$searchString.'%')
								  ->orWhere('middlename','LIKE','%'.$searchString.'%')
								  ->orWhere('lastname','LIKE','%'.$searchString.'%');
						})
				 ->orderBy('id','desc')
				 ->paginate(20);
			}
			else {
				$data = DB::table('applications')->where('branch', '=', $request->user()->branch)->orderBy('id','desc')->paginate(20);
			}

			return view('applications.application_list', compact('data'))->render();
		}
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
        return view('applications.create', compact('visatypes', 'documentlist', 'customer_type_array'));
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
			'lastname' => 'required',
			'firstname' => 'required',
			'middlename' => 'required',
			'birthdate' => 'required|date',
			'gender' => 'required',
			'marital_status' => 'required',
			'address' => 'required',
			'email' => 'required',
			'mobile_no' => 'required',
			'passport_no' => 'required',
			'passport_expiry' => 'required|date',
			'departure_date' => 'required|date',
			'visa_type' => 'required',
			'visa_price' => 'required',
			'promo_code' => 'required',
			'documents_submitted' => 'required'
		]);

		$application = new Application([
			'reference_no' => $this->generateReferenceNo($request),
			'application_status' => 1,
			'customer_type' => $request->get('customer_type'),
			'customer_company'  => $request->get('customer_company'),
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
			'passport_no' => $request->get('passport_no'),
			'passport_expiry' => $request->get('passport_expiry'),
			'departure_date' => $request->get('departure_date'),
			'remarks' => $request->get('remarks'),
			'visa_type' => $request->get('visa_type'),
			'visa_price' => $request->get('visa_price'),
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
		$visatypes = VisaType::orderBy('id', 'asc')->get();
		$documentlist = RequiredDocument::orderBy('id', 'asc')->get();
		$customer_type_array = $this->customer_type_array;
		return view('applications.edit', compact('application','visatypes','documentlist', 'customer_type_array'));
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
			'branch' => 'required',
			'lastname' => 'required',
			'firstname' => 'required',
			'middlename' => 'required',
			'birthdate' => 'required|date',
			'address' => 'required',
			'email' => 'required',
			'mobile_no' => 'required',
			'passport_no' => 'required',
			'passport_expiry' => 'required|date',
			'departure_date' => 'required|date',
			'visa_type' => 'required',
			'visa_price' => 'required',
			'documents_submitted' => 'required',
			'payment_status' => 'required'
		]);

		$application = Application::find($id);

        if ($application->application_status != $request->get('application_status'))
        {
            if ($request->get('application_status') == '1'){
                $request_type = 'Mark as New Application';
            } else {
                $request_type = 'Mark as Incomplete';
            }

            $approval_request = new PendingApprovals([
                'application_id' => $id,
                'request_type' => $request_type,
                'requested_by' => $request->user()->username,
                'request_date' => Carbon::now()
            ]);
            $approval_request->save();

            $application->application_status = '10';
        }

		$application->reference_no = $request->get('reference_no');
		$application->customer_type = $request->get('customer_type');
		$application->customer_company = $request->get('customer_company');
		$application->branch = $request->get('branch');
		$application->lastname = strtoupper($request->get('lastname'));
		$application->firstname = strtoupper($request->get('firstname'));
		$application->middlename = strtoupper($request->get('middlename'));
		$application->birthdate = $request->get('birthdate');
		$application->address = strtoupper($request->get('address'));
		$application->email = $request->get('email');
		$application->telephone_no = $request->get('telephone_no');
		$application->mobile_no = $request->get('mobile_no');
		$application->passport_no = $request->get('passport_no');
		$application->passport_expiry = $request->get('passport_expiry');
		$application->departure_date = $request->get('departure_date');
		$application->remarks = $request->get('remarks');
		$application->visa_type = $request->get('visa_type');
		$application->visa_price = $request->get('visa_price');
		$application->promo_code = $request->get('promo_code');
		$application->discount_amount = $request->get('discount_amount');
		$application->documents_submitted = $request->get('documents_submitted');
		$application->payment_status = $request->get('payment_status');
		$application->or_number = $request->get('or_number');
		$application->vpr_number = $request->get('vpr_number');
		$application->tracking_no = $request->get('tracking_no');
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
		return view('cashier.receive_payment');
	}

	public function retrievePaymentForm(Request $request)
	{
		if($request->ajax())
		{
			$searchString = $request->get('searchString');
			$application = Application::where('reference_no','=',$searchString)->first();
            $modeOfPayment = Arr::pluck(collect(Other::where('type', 'mop')->get()), 'name', 'name');
            $modeOfPayment = Arr::prepend($modeOfPayment, '', '');
            $paymentRequest = Arr::pluck(collect(Other::where('type', 'pr')->get()), 'name', 'name');
            $paymentRequest = Arr::prepend($paymentRequest, '', '');
            $visaType = VisaType::where('name', $application->visa_type)->first();

			return view('cashier.confirm_payment', compact('searchString', 'application', 'modeOfPayment', 'paymentRequest', 'visaType'));
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
        $account_receivables = AccountReceivable::where('batch_no', $data->batch_no)
                                        ->where('company', $data->customer_company)
                                        ->first();


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
        return $pdf->download('Checklist.pdf');
    }
}
