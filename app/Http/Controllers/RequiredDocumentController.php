<?php

namespace App\Http\Controllers;

use App\RequiredDocument;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class RequiredDocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $result = RequiredDocument::orderBy('id', 'asc')->paginate(20);
        return view('admin.required_documents', compact('result'));
    }

    public function addRequiredDocument(Request $request)
    {
        $request->validate([
			'name' => 'required',
            'type' => ['required', Rule::in(['FILIPINO', 'JAPANESE', 'FOREIGN'])],
		]);

        RequiredDocument::create(
            [
                'name' => $request->name,
                'type' => $request->type
            ]);

        $request->session()->flash('status', 'Required Document successfully added');

    }

    public function updateRequiredDocument(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:required_documents,id',
			'name' => 'required',
            'type' => ['required', Rule::in(['FILIPINO', 'JAPANESE', 'FOREIGN'])],
		]);

        $result = RequiredDocument::find($request->id);
        $result->name = $request->name;
        $result->type = $request->type;
        $result->save();

        $request->session()->flash('status', 'Required Document successfully updated');

    }


}
