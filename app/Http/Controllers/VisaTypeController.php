<?php

namespace App\Http\Controllers;

use App\RequiredDocument;
use App\VisaType;
use Illuminate\Http\Request;

class VisaTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $result = VisaType::with('documents')->orderBy('id', 'asc')->paginate(20);
        return view('admin.visa.index', compact('result', 'types'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $docs = RequiredDocument::all();

        $docs_filipino = collect($docs)->whereIn('type', 'FILIPINO');
        $docs_japanese = collect($docs)->whereIn('type', 'JAPANESE');
        $docs_foreign = collect($docs)->whereIn('type', 'FOREIGN');

        return view('admin.visa.create', compact('docs_filipino', 'docs_japanese', 'docs_foreign'));
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
			'name' => 'required|unique:visa_types',
            'handling_fee' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
            'visa_fee' => 'nullable|numeric|regex:/^\d+(\.\d{1,2})?$/',
		]);

        $visa = VisaType::create(
            [
                'name' => $request->name,
                'handling_fee' => $request->handling_fee,
                'visa_fee' => $request->visa_fee
            ]);

        if ($request->documents_submitted) {
            $docs = explode(',', $request->documents_submitted);
            $visa->documents()->sync($docs);
        }

        return redirect('/admin/visa_types');
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
