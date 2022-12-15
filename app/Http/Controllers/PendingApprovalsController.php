<?php

namespace App\Http\Controllers;

use App\User;
use App\Application;
use App\PendingApprovals;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

class PendingApprovalsController extends Controller
{
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

	public function mark_as_incomplete(Request $request)
	{
		if($request->ajax())
		{
			$request_type = $request->get('request_type');
			$application_id = $request->get('application_id');
			$approval_code = $request->get('approval_code');
			$user_type = $request->user()->role;

			$code_is_valid = false;

			$approver;
			$approval_request;
			$action = "";

			if($request_type == "Mark as Incomplete")
			{
				if($user_type == "Encoder")
				{
					if($approval_code != '')
					{
						$approver = DB::table('users')->where('approval_code', $approval_code);
						if($approver->count() > 0) $code_is_valid = true;
					}

					if($code_is_valid)
					{
						$approval_request = new PendingApprovals([
							'application_id' => $application_id,
							'request_type' => $request_type,
							'requested_by' => $request->user()->username,
							'request_date' => Carbon::now(),
							'officer_in_charge' => $request->user()->username . " using approval code of " . $approver->first()->username,
							'action_date' => Carbon::now(),
							'action' => 'APPROVED'
						]);
						$approval_request->save();

						$application = Application::find($application_id);
						$application->application_status = 2;
						$application->save();

						$request->session()->flash('status', 'Application# ' . $application->reference_no . ' is now marked as incomplete.');
					}
					else
					{
                        $application = Application::find($application_id);
                        $application->application_status = 3;
						$application->save();

						$approval_request = new PendingApprovals([
							'application_id' => $application_id,
							'request_type' => $request_type,
							'requested_by' => $request->user()->username,
							'request_date' => Carbon::now()
						]);
						$approval_request->save();

						$request->session()->flash('status', 'Approval code is blank/incorrect.  Please wait for admin approval.');
					}
				}
				else if($user_type == "Admin")
				{
					if($approval_code == "Approval")
					{
						$action = "APPROVED";
					}
					else
					{
						$action = "REJECTED";
					}

					$approval_request = PendingApprovals::find($application_id);

                    $requestType = $approval_request->request_type;
					$approval_request->officer_in_charge = $request->user()->username;
					$approval_request->action_date = Carbon::now();
					$approval_request->action = $action;

					$id = $approval_request->application_id;

					$approval_request->save();

					if($approval_code == "Approval")
					{
						$application = Application::find($id);
                        if ($requestType == 'Mark as Incomplete') {
                            $application->application_status = 2;
                        } else {
                            $application->application_status = 1;
                        }
						$application ->save();

						$request->session()->flash('status', 'Request has been approved.');
					}
					else
					{
                        $application = Application::find($id);
                        if ($requestType == 'Mark as Incomplete') {
                            $application->application_status = 1;
                        } else {
                            $application->application_status = 2;
                        }
						$application ->save();

						$request->session()->flash('status', 'Request has been rejected');
					}
				}
			}
		}
	}
}
