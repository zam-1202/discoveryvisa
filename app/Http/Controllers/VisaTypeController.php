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

        $documents = [];
        foreach ($result as $value) {
            $docs_filipino = [];
            $docs_japanese = [];
            $docs_foreign = [];
            foreach ($value->documents as $key => $document) {
                if ($document->type == 'FILIPINO') {
                    array_push($docs_filipino, $document);
                } elseif ($document->type == 'JAPANESE') {
                    array_push($docs_japanese, $document);
                } else {
                    array_push($docs_foreign, $document);
                }
            }
            array_push($documents, ['filipino' => $docs_filipino, 'japanese' => $docs_japanese, 'foreign' => $docs_foreign]);
        }

        return view('admin.visa.index', compact('result', 'documents', 'docs_filipino', 'docs_japanese', 'docs_foreign'));
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

        $request->session()->flash('status', 'Visa type successfully added');
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
        $result = VisaType::where('id', $id)->with('documents')->orderBy('id', 'asc')->first();

        $selected_docs = [];
        foreach ($result->documents as $key => $value) {
            array_push($selected_docs, $value->id);
        }

        $docs = RequiredDocument::all();

        $docs_filipino = collect($docs)->whereIn('type', 'FILIPINO');
        $docs_japanese = collect($docs)->whereIn('type', 'JAPANESE');
        $docs_foreign = collect($docs)->whereIn('type', 'FOREIGN');
        return view('admin.visa.edit', compact('result', 'selected_docs', 'docs_filipino', 'docs_japanese', 'docs_foreign'));
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
			'name' => 'required|unique:visa_types,name,' .$id,
            'handling_fee' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
            'visa_fee' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
		]);

        $visa = VisaType::findOrFail($id);
        $visa->name = $request->name;
        $visa->handling_fee = $request->handling_fee;
        $visa->visa_fee = $request->visa_fee;
        $visa->save();

        if ($request->documents_submitted) {
            $docs = explode(',', $request->documents_submitted);
            $visa->documents()->sync($docs);
        } else {
            $visa->documents()->sync([]);
        }

        $request->session()->flash('status', 'Visa type successfully updated');
        return redirect('/admin/visa_types');
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
