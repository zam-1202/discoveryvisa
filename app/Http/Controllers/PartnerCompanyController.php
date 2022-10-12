<?php

namespace App\Http\Controllers;

use DB;
use App\PartnerCompany;
use Illuminate\Http\Request;

class PartnerCompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = DB::table('partner_companies')->orderBy('type', 'asc')->orderBy('name', 'asc')->paginate(20);
		return view('partner_companies.index', compact('data'));
    }

	public function filterList(Request $request)
	{
		if($request->ajax())
		{
			$filterType = $request->get('filterType');

			if($filterType != '')
			{
				$data = DB::table('partner_companies')->where('type','=', $filterType)->orderBy('name', 'asc')->paginate(20);
			}
			else
			{
				$data = DB::table('partner_companies')->orderBy('type', 'asc')->orderBy('name', 'asc')->paginate(20);
			}

			return view('partner_companies.index', compact('data'))->render();
		}
	}

	public static function getPartnerCompanies(Request $request)
	{
		$filterType = $request->get('filterType');

		$data = DB::table('partner_companies')->where('type','=', $filterType)->orderBy('name', 'asc')->get();

		return $data;
	}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('partner_companies.create');
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
			'name' => 'required',
			'type' => 'required'
		]);

		$partnerCompany = new PartnerCompany([
			'name' => $request->get('name'),
			'type' => $request->get('type')
		]);

		$partnerCompany->save();
		return redirect('/partner_companies')->with('success','Application saved!');
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
}
