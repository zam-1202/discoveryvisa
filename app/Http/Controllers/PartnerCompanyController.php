<?php

namespace App\Http\Controllers;

use DB;
use App\PartnerCompany;
use Illuminate\Http\Request;

class PartnerCompanyController extends Controller
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
    public function index()
    {       
        
        $customer_type_array = $this->customer_type_array;
		$customer_company = PartnerCompany::all(); // Fetch the customer company data
        $result = DB::table('partner_companies')->orderBy('type', 'asc')->orderBy('name', 'asc')->paginate(20);
        $names = PartnerCompany::distinct()->get(['name']);
        $types = PartnerCompany::distinct()->get(['type']);
        $types = collect($types)->pluck('type')->push('Other');
		return view('admin.partner_companies', compact('result', 'types', 'names', 'customer_type_array', 'customer_company'));
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
        $result = DB::table('partner_companies')->orderBy('type', 'asc')->orderBy('name', 'asc')->paginate(20);
        // $types = PartnerCompany::distinct()->pluck('type')->toArray();
        // $types['Other'] = 'Other';
        $names = PartnerCompany::distinct()->get(['name']);
        $types = [
            'Walk-In' => 'Walk-In',
            'PIATA' => 'PIATA',
            'PTAA' => 'PTAA',
            'Corporate' => 'Corporate',
            'POEA' => 'POEA',
            'Other' => 'Other',
        ];
        

        return view('partner_companies.create', compact('result', 'types', 'names'));

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
        $customer_company = PartnerCompany::all(); // Fetch the customer company data
    
        return view('partner_companies.edit', compact('customer_company'));
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
	 *
	 * Add Partner Company to database
	 *
	 */
	public function createpartnerCompanies(Request $request)
	{
		if($request->ajax()){
			$type = $request->get('type');
			$name = $request->get('name');

			$partnerComp = new PartnerCompany([
				'type' => $type,
				'name' => $name
			]);

			$partnerComp->save();

			$request->session()->flash('status', 'Partner Company successfully added');
            return response()->json(['success' => true]); // Send a success response
		}
	}
    

    /**
	 *
	 * Edit Partner Company to database
	 *
	 */

     public function editPartnerCompanies(Request $request)
     {
		$partnerComp = PartnerCompany::find($id);
        $company_array = array("Walk-In" => "Walk-In", "PIATA" => "PIATA", "PTAA" => "PTAA", "Corporate" => "Corporate", "POEA" => "POEA", "Other" => "Other");
        return view('admin.partner_companies', compact('partnerComp', 'company_array'));
    
     }
 
    /**
	 *
	 * Update Partner Company to database
	 *
	 */
    
     public function updatepartnerCompanies(Request $request, $id)
     {
         $request->validate([
             'type' => 'required',
             'name' => 'required',
         ]);
     
         $partnerComp = PartnerCompany::find($id);
     
         $partnerComp->name = $request->name;
         $partnerComp->type = $request->type;
         $partnerComp->save();
     
         $request->session()->flash('status', 'Partner Company successfully updated');
     
     }
     
    
}
