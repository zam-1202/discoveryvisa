<?php

namespace App\Http\Controllers;

use App\Other;
use Illuminate\Http\Request;

class OtherController extends Controller
{
    public function modeOfPaymentList()
    {
        $result = Other::where('type', 'mop')->orderBy('id', 'asc')->paginate(20);
        return view('admin.mode_of_payment', compact('result'));

    }

    public function addModeOfPayment(Request $request)
    {
        $request->validate([
			'name' => 'required',
		]);

        Other::create(
            [
                'name' => $request->name,
                'type' => 'mop'
            ]);

        $request->session()->flash('status', 'Mode of payment successfully added');

    }

    public function updateModeOfPayment(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:others,id',
			'name' => 'required',
		]);

        $result = Other::find($request->id);
        $result->name = $request->name;
        $result->save();

        $request->session()->flash('status', 'Mode of payment successfully updated');

    }

    public function paymentRequestList()
    {
        $result = Other::where('type', 'pr')->orderBy('id', 'asc')->paginate(20);
        return view('admin.payment_request', compact('result'));
    }

    public function addPaymentRequest(Request $request)
    {
        $request->validate([
			'name' => 'required',
		]);

        Other::create(
            [
                'name' => $request->name,
                'type' => 'pr'
            ]);

        $request->session()->flash('status', 'Payment request successfully added');

    }

    public function updatePaymentRequest(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:others,id',
			'name' => 'required',
		]);

        $result = Other::find($request->id);
        $result->name = $request->name;
        $result->save();

        $request->session()->flash('status', 'Payment request successfully updated');

    }








    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param  \App\Other  $other
     * @return \Illuminate\Http\Response
     */
    public function show(Other $other)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Other  $other
     * @return \Illuminate\Http\Response
     */
    public function edit(Other $other)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Other  $other
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Other $other)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Other  $other
     * @return \Illuminate\Http\Response
     */
    public function destroy(Other $other)
    {
        //
    }
}
