<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use App\PromoCode;

class PromoCodeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $promo = DB::table('promo_codes')->paginate(20);
        return view('admin.promo_codes', compact('promo'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.create_promo_code');
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
            'code' => 'required|unique:promo_codes',
            'discount' => 'required',
            'expiration_date' => 'required|date',
            'quantity' => 'required|numeric'
        ]);

        $promo = new PromoCode([
            'code' => $request->get('code'),
            'discount' => $request->get('discount'),
            'expiration_date' => $request->get('expiration_date'),
            'max_quantity' => $request->get('quantity')
        ]);

        $promo->save();
        $request->session()->flash('status', 'Promo code successfully added');
		return redirect('/admin/promo_codes');
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
        $promo = PromoCode::find($id);
        return view('admin.update_promo_code', compact('promo'));
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
            'code' => 'required|unique:promo_codes,code,' .$id,
            'discount' => 'required',
            'expiration_date' => 'required|date',
            'quantity' => 'required|numeric'
        ]);

        $promo = PromoCode::find($id);
        $promo->discount = $request->get('discount');
        $promo->expiration_date = $request->get('expiration_date');
        $promo->max_quantity = $request->get('quantity');
        $promo->save();

        $request->session()->flash('status', 'Promo code successfully updated');
        return redirect('/admin/promo_codes');
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
}
