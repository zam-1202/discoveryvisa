<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;

use App\User;
use App\Branch;
use App\PendingApprovals;

class AdminController extends Controller
{
    /**
     * Display list of users.
     *
     * @return \Illuminate\Http\Response
     */
    public function userList()
    {
		$users = DB::table('users')->paginate(20);

		$branches = Branch::all();
		$branch_list = array();
		foreach($branches as $branch) $branch_list[$branch->code] = $branch->description;

		$role_array = array("Encoder" => "Encoder", "Cashier" => "Cashier", "Accounting" => "Accounting", "Admin" => "Admin");

        return view('admin.users', compact('users', 'branch_list', 'role_array'));
    }

    public function editUser($id)
    {
        $user = User::find($id);

        $branches = Branch::all();
		$branch_list = array();
		foreach($branches as $branch) $branch_list[$branch->code] = $branch->description;

		$role_array = array("Encoder" => "Encoder", "Cashier" => "Cashier", "Accounting" => "Accounting", "Admin" => "Admin");
        return view('admin.edit', compact('branch_list', 'role_array', 'user'));
    }

    public function updateUser(Request $request, $id)
    {
        $request->validate([
			'username' => 'required|unique:users,username,' .$id,
            'name' => 'required',
            'role' => 'required',
            'branch' => 'required',
            'email' => 'required|unique:users,email,' .$id,
		]);

        $user = User::find($id);
        $user->username = $request->get('username');
        $user->name = $request->get('name');
        $user->role = $request->get('role');
        $user->branch = $request->get('branch');
        $user->email = $request->get('email');
        $user->save();
        return redirect('/admin/users');

    }


	/**
	 * Display list of branches.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function branchList()
	{
		$branches = Branch::all();

		return view('admin.branches', compact('branches'));
	}

	/**
	 *
	 * Add branch to database
	 *
	 */
	public function addBranch(Request $request)
	{
		if($request->ajax()){
			$branch_code = $request->get('branch_code');
			$branch_desc = $request->get('branch_desc');

			$branch = new Branch([
				'code' => $branch_code,
				'description' => $branch_desc
			]);

			$branch->save();

			$request->session()->flash('status', 'Branch successfully added');
		}
	}

	/**
	 * Show list of pending approvals
	 *
	 */
	public function pendingApprovals()
	{
		$approval_requests = DB::table('pending_approvals')->where('action', null)->orderby('request_date', 'asc')->get();

		return view('admin.approvals', compact('approval_requests'));
	}

}
