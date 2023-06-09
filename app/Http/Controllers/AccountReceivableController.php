<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;

use App\AccountReceivable;
use App\Application;
use Carbon\Carbon;



class AccountReceivableController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		$account_receivables = DB::table('account_receivables')->paginate(20);
        return view('account_receivables.index', compact('account_receivables'));
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
        $receivables = AccountReceivable::find($id);

        $data = Application::where('batch_no', $receivables->batch_no)
                ->where('payment_status', 'UNPAID')
                ->where('customer_company', $receivables->company)
                ->orderBy('id', 'asc')
                ->paginate(20);

        return view('account_receivables.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
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

	/**
	 *  Generate Account Receivables for inputted batch
	 */
	public static function generateAccountReceivables($batchno, $date)
	{
		$receivables = DB::table('applications')
							->select(DB::raw('customer_company, sum(visa_price) as total_amount'))
							->where('batch_no', '=', $batchno)
							->where('customer_type', '<>', 'Walk-In')
                            ->where('payment_status', '=', 'UNPAID')
							->groupBy('customer_company')
							->get();

		foreach($receivables as $account_receivable)
		{

			$receivable = new AccountReceivable([
				'company' => $account_receivable->customer_company,
				'batch_no' => $batchno,
				'application_date' => $date,
				'total_amount' => $account_receivable->total_amount,
				'payment_status' => 'UNPAID'
			]);

			$receivable->save();
		}
	}
public function markCustomerAsPaid(Request $request)
{
    $application = DB::table('applications')
        ->where('reference_no', '=', $request->get('reference_no'))
        ->update([
            'or_number' => $request->get('or_number'),
            'payment_mode' => $request->get('payment_mode'),
            'payment_request' => $request->get('payment_request'),
            'payment_date' => Carbon::now(),
            'payment_received_by' => $request->user()->username,
            'payment_status' => 'PAID',
            'reference_no' => $request->get('reference_no')
        ]);

    $data = Application::where('reference_no', $request->get('reference_no'))->first();
    $account_receivables = null;
    if ($data) {
        $account_receivables = AccountReceivable::where('batch_no', $data->batch_no)
            ->where('company', $data->customer_company)
            ->first();
    }

    if ($account_receivables) {
        if ($account_receivables->total_amount <= $data->visa_price) {
            $account_receivables->delete();
        } else {
            $account_receivables->total_amount -= $data->visa_price;
            $account_receivables->save();
        }
    }
    return 'Payment for ' . $request->get('reference_no') . ' received';
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

}
