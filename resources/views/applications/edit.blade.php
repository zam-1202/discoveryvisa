@extends('layouts.app')
@if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@php
$branches = App\Branch::all();
$branch_lookup = [];
	foreach($branches as $branch){
		$branch_lookup[$branch->code] = $branch->description;
	}

	$visatypearray = array();
	foreach($visatypes as $type)
	{
		$visatypearray[$type->id] = $type->name;
	}

	$selectedVisaType = $application->visa_type_id;

    $application_status_array = array('1' => 'NEW Application',
                                      '2' => 'Sent to Main Office',
                                      '3' => 'Received by Main Office',
                                      '4' => 'Sent to Original Branch',
                                      '5' => 'Received by Original Branch',
                                      '6' => 'Submitted to Embassy',
                                      '7' => 'Received from Embassy',
                                      '8' => 'Sent to/Claimed by Client',
									  '9' => 'Incomplete',
									  '10' => 'Pending Approval',
									  '11' => 'Additional Documents Required',
									  '12' => 'Released by Embassy',
									'13' => 'Resubmitted to JPN',
                        						'14' => 'Passport Return from JPN Embassy');

	$application_status = $application->application_status;
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
		<style>

			.scrollable-div {
			max-height: 200px; /* Adjust the height as needed */
			overflow-y: auto;
			}

			.required-field,
			.required-text {
			color: red;
			font-size: 13px;
			}

			.required-text {
			font-style: italic;
			}

        </style>
    </head>
<body>

@section('content')
<div class="row justify-content-center">
	<div class="col-sm-20 text-center">
	<div class="jumbotron bg-dark text-white" style="padding: 10px">
			<h1>Update an Application</h1>	
			<div class="col-sm-20">
			<div class="form-group row">
			<table class="text-center">
    <tr>
		<td style="padding: 10px;">
			<strong>Filer</strong><br>
			<span style="background-color: darkgray; color: #383C44; border-radius: 4px; padding: 5px; font-size: 11px;">
				{{$application->encoded_by}} &#9679; {{date('Y-m-d', strtotime($application->created_at))}}
			</span>
		</td>

		<td style="padding: 10px;">
            <strong>Last Encoded by</strong><br>
			<span style="background-color: darkgray; color: #383C44; border-radius: 4px; padding: 5px; font-size: 11px;">
            {{$application->last_update_by}} &#9679; {{date('Y-m-d', strtotime($application->updated_at))}}
        </td>

		
		<td style="padding: 10px;">
            <strong>Submitted to Embassy by</strong><br>
			<span style="background-color: darkgray; color: #383C44; border-radius: 4px; padding: 5px; font-size: 11px;">
			{{$application->submitted_to_embassy}} &#9679; 
			@if ($application->submitted_to_embassy)
				{{date('Y-m-d', strtotime($application->date_submitted_to_embassy))}}
			@endif
        </td>

		<td style="padding: 10px;">
            <strong>Submission to EMB</strong><br>
			<span style="background-color: darkgray; color: #383C44; border-radius: 4px; padding: 5px; font-size: 11px;">
            {{$application->receiver_from_embassy}} &#9679; 
			@if ($application->receiver_from_embassy)
				({{date('Y-m-d', strtotime($application->date_received_from_embassy))}})
			@endif
        </td>

		<td style="padding: 10px;">
            <strong>Released by Embassy</strong><br>
			<span style="background-color: darkgray; color: #383C44; border-radius: 4px; padding: 5px; font-size: 11px;">
			{{$application->released_by_embassy}} &#9679; 
		@if ($application->released_by_embassy)
			{{date('Y-m-d', strtotime($application->date_released_by_embassy))}}
		@endif
        </td>

    </tr>
</table>
<div class="form-group row">
			<table class="text-center">

    <tr>

	<td style="padding: 10px;">
            <strong>Sent to Client by</strong><br>
			<span style="background-color: darkgray; color: #383C44; border-radius: 4px; padding: 5px; font-size: 11px;">
			{{$application->distributed_by}} &#9679; 
			@if ($application->date_distributed)
				({{date('Y-m-d', strtotime($application->date_distributed))}})
			@endif
        </td>

	<td style="padding: 10px;">
            <strong>Additional Docs - JPN</strong><br>
			<span style="background-color: darkgray; color: #383C44; border-radius: 4px; padding: 5px; font-size: 11px;">
			{{$application->additional_docs}} &#9679; 
			@if ($application->date_docsRequired)
				({{date('Y-m-d', strtotime($application->date_docsRequired))}})
			@endif
        </td>

                <td style="padding: 10px;">
            <strong>Resubmitted to JPN</strong><br>
                        <span style="background-color: darkgray; color: #383C44; border-radius: 4px; padding: 5px; font-size: 11px;">
            {{$application->return_to_jpn_emb}} &#9679;
                        @if ($application->date_return_to_jpn)
                                ({{date('Y-m-d', strtotime($application->date_return_to_jpn))}})
                        @endif
                        </span>
        </td>
    </tr>
</table>

</div>

			</div>
		</div>
		</div>

        <form method="post" action="{{ route('applications.update', $application->id) }}">
            @method('PATCH')
            @csrf
            <div class="form-group row">
				<div class="col-md-1">
				  <label for="branch">Branch</label>
				  {{Form::text('branch', $application->branch, ['class' => 'form-control text-center', 'readonly' => 'readonly']) }}
				</div>
				<div class="col-md-2">
				  <label for="reference_no">Reference No.</label>
				  {{Form::text('reference_no', $application->reference_no, ['readonly' => 'readonly', 'class' => 'form-control text-center'])}}
				</div>

                                <div class="col-md-3">
                                <label for="application_status">Application Status</label>
                                        @if ($application->application_status == 9 && $application->branch === $user->branch)
                                                {{Form::select('application_status', [
                                                        9 => 'Incomplete',
                                                        1 => 'New Application',
                                                        4 => 'Sent to Original Branch',
                                                ], $application->application_status, ['class' => 'form-control text-center', 'id' => 'application_status_select'])}}
                                        @elseif ($application->application_status == 2 && $user->branch === 'MNL')
                                                {{Form::select('application_status', [
                                                        2 => 'Sent to Main Office',
                                                        9 => 'Incomplete',
                                                        4 => 'Sent to Original Branch',
                                                        5 => 'Received by Original Branch'
                                                ], $application->application_status, ['class' => 'form-control text-center', 'id' => 'application_status_select'])}}
                                        @elseif ($application->application_status == 4 && $application->branch === $user->branch)
                                                {{Form::select('application_status', [
                                                        4 => 'Sent to Original Branch',
                                                        5 => 'Received by Original Branch'
                                                ], $application->application_status, ['class' => 'form-control text-center', 'id' => 'application_status_select'])}}
                                        @elseif ($application->application_status == 5 && $application->branch === $user->branch)
                                                {{Form::select('application_status', [
                                                        5 => 'Received by Original Branch',
                                                        1 => 'New Application'
                                                ], $application->application_status, ['class' => 'form-control text-center', 'id' => 'application_status_select'])}}
                                        @elseif ($application->application_status == 6 && $user->branch === 'MNL')
                                                {{Form::select('application_status', [
                                                        6 => 'Submitted to Embassy',
                                                        7 => 'Received from Embassy',
                                                        8 => 'Sent to/Claimed by Client',
                                                        11 => 'Additional Documents Required'
                                                ], $application->application_status, ['class' => 'form-control text-center', 'id' => 'application_status_select'])}}
                                        @elseif ($application->application_status == 7 && $user->branch === 'MNL')
                                                {{Form::select('application_status', [
                                                        7 => 'Received from Embassy',
                                                        12 => 'Released by Embassy',
                                                        8 => 'Sent to/Claimed by Client',
                                                        11 => 'Additional Documents Required'
                                                ], $application->application_status, ['class' => 'form-control text-center', 'id' => 'application_status_select'])}}
                                        @elseif ($application->application_status == 12 && $user->branch === 'MNL')
                                                {{Form::select('application_status', [
                                                        12 => 'Released by Embassy',
                                                        8 => 'Sent to/Claimed by Client',
                                                        11 => 'Additional Documents Required'
                                                ], $application->application_status, ['class' => 'form-control text-center', 'id' => 'application_status_select'])}}
                                        @elseif ($application->application_status == 11 && $user->branch === 'MNL')
                                                {{Form::select('application_status', [
                                                        11 => 'Additional Documents Required',
                                                        13 => 'Resubmitted to JPN'
                                                ], $application->application_status, ['class' => 'form-control text-center', 'id' => 'application_status_select'])}}
                                        @elseif ($application->application_status == 13 && $user->branch === 'MNL')
                                                {{Form::select('application_status', [
                                                        13 => 'Resubmitted to JPN',
                                                        14 => 'Passport Return from JPN Embassy',
                                                        12 => 'Released by Embassy',
                                                        8 => 'Sent to/Claimed by Client',
                                                ], $application->application_status, ['class' => 'form-control text-center', 'id' => 'application_status_select'])}}
                                        @elseif ($application->application_status == 14 && $user->branch === 'MNL')
                                                {{Form::select('application_status', [
                                                        14 => 'Passport Return from JPN Embassy',
                                                        12 => 'Released by Embassy',
                                                        8 => 'Sent to/Claimed by Client',
                                                ], $application->application_status, ['class' => 'form-control text-center', 'id' => 'application_status_select'])}}
                                        @else
                                                {{Form::text('application_status', $application_status_array[$application->application_status], ['class' => 'form-control text-center', 'readonly' => 'readonly', 'style' => 'pointer-events: none; touch-action: none;', 'id' => 'application_status_select'])}}
                                                {{Form::hidden('application_status', $application->application_status)}}
                                        @endif
                                </div>



				<div class="col-md-2">
					<label for="date_received_from_embassy">Submission to EMB</label>
					@if ($application->application_status == '7')
						{{ Form::text('date_received_from_embassy', $application->date_received_from_embassy, ['class' => 'form-control text-center', 'readonly' => 'readonly', 'style' => 'pointer-events: none; touch-action: none;']) }}
					@elseif ($application->date_received_from_embassy)
						{{ Form::text('date_received_from_embassy', $application->date_received_from_embassy, ['class' => 'form-control text-center', 'readonly' => 'readonly', 'style' => 'pointer-events: none; touch-action: none;']) }}
					@else
						{{ Form::text('date_received_from_embassy', '', ['class' => 'form-control text-center', 'readonly' => 'readonly']) }}
					@endif
				</div>



				<div class="col-md-2">
				  <label for="tracking_no">Tracking No.</label>
				  {{Form::text('tracking_no', $application->tracking_no, ['class' => 'form-control text-center', 'oninput' => 'validateInput(event)']) }}
				</div>
				<div class="col-md-2">
				  <label for="verification_no">Verification No.</label>
				  @if ($application->application_status == '7')
						{{Form::text('verification_no', $application->verification_no, ['class' => 'form-control text-center', 'id' => 'verification_no', 'oninput' => 'validateInput(event)']) }}
				  @else
					{{Form::text('verification_no', $application->verification_no, ['readonly' => 'readonly', 'class' => 'form-control text-center','id' => 'verification_no','readonly' => 'readonly'])}}
					@endif
				</div>
			</div>

			<div class="form-group row">

				<div class="col-md-4"></div>

			</div>

			<div class="form-group row">
			<div class="col-md-3">
			<label for="customer_type">Customer Type<span class="required-field">*</span></label>
			@if (($application->application_status == 2 && $user->branch === 'MNL') || in_array($application->application_status, ['6', '7', '8']))
				{{ Form::select('customer_type', $customer_type_array, $application->customer_type, ['class' => 'form-control', 'id' => 'customer_type', 'readonly' => 'readonly', 'style' => 'pointer-events: none; touch-action: none;']) }}
			@else
				{{ Form::select('customer_type', $customer_type_array, $application->customer_type, ['class' => 'form-control', 'id' => 'customer_type']) }}
			@endif
			</div>


				<div class="col-md-2">
				<label for="customer_company">Client's Company<span class="required-field">*</span></label>
				@if (($application->application_status == 2 && $user->branch === 'MNL') || in_array($application->application_status, ['6', '7', '8']))
					{{ Form::select('customer_company', $customer_company, null, ['class' => 'form-control', 'id' => 'customer_company', 'readonly' => 'readonly', 'style' => 'pointer-events: none; touch-action: none;']) }}
				@else
					{{ Form::select('customer_company', $customer_company, null, ['class' => 'form-control', 'id' => 'customer_company']) }}
				@endif
				</div>
				
				<div class="col-md-2">
				<label for="pickupMethod">Pick Up Method<span class="required-field">*</span></label>
					<?php
					$pickupMethodOptions = ['On-site' => 'On-site', 'Courier' => 'Courier'];
					$defaultPickupMethod = $application->pickupMethod; // Fetch the value from the database
					?>
					@if (($application->application_status == 2 && $user->branch === 'MNL') || in_array($application->application_status, ['6', '7', '8']))
						{{ Form::select('pickupMethod', $pickupMethodOptions, $defaultPickupMethod, ['class' => 'form-control text-center', 'readonly' => 'readonly', 'style' => 'pointer-events: none; touch-action: none;']) }}
					@else
						{{ Form::select('pickupMethod', $pickupMethodOptions, $defaultPickupMethod, ['class' => 'form-control text-center']) }}
					@endif
				</div>
				
				<div class="col-md-2">
						<label for="submitter">Submitter<span class="required-field">*</span></label>
						{{ Form::textarea('submitter', $application->submitter, ['class' => 'form-control text-center text-uppercase', 'rows' => '1', 'id' => 'submitter', 'readonly' => 'readonly']) }}
					</div>

				<div class="col-md-3">
					<label for="group_name">Group Name</label>
					@if (($application->application_status == 2 && $user->branch === 'MNL') || in_array($application->application_status, ['6', '7', '8']))
						{{ Form::textarea('group_name', $application->group_name, ['class' => 'form-control text-center text-uppercase', 'rows' => '1', 'id' => 'group_name', 'readonly' => 'readonly']) }}
					@else
						{{ Form::textarea('group_name', $application->group_name, ['class' => 'form-control text-center text-uppercase', 'rows' => '1', 'id' => 'group_name']) }}
					@endif
				</div>

			</div>

			<div class="form-group row">
			<div class="col-md-4">
				<label for="visa_result">Visa Result</label>
					@if ($application->application_status == 7)
						{{ Form::textarea('visa_result', $application->visa_result, ['class' => 'form-control text-center text-uppercase', 'rows' => '1', 'id' => 'visa_result']) }}
					@else
						{{ Form::textarea('visa_result', $application->visa_result, ['class' => 'form-control text-center text-uppercase', 'rows' => '1', 'id' => 'visa_result', 'readonly' => 'readonly']) }}
					@endif
				</div>


					<div class="col-md-4">
						<label for="released_to">Released to</label>
						@if (($application->application_status == 1) || in_array($application->application_status, ['2', '3', '4', '5', '6', '7', '8', '9', '10', '11']))
							{{ Form::textarea('released_to', $application->released_to, ['class' => 'form-control text-center text-uppercase', 'rows' => '1', 'id' => 'released_to', 'readonly' => 'readonly']) }}
						@else
							{{ Form::textarea('released_to', $application->released_to, ['class' => 'form-control text-center text-uppercase', 'rows' => '1', 'id' => 'released_to']) }}
						@endif
					</div>

					<div class="col-md-4">
						<label for="courier_tracking">Courier Tracking No.</label>
						{{ Form::textarea('courier_tracking', $application->courier_tracking, ['class' => 'form-control text-center text-uppercase', 'rows' => '1', 'id' => 'courier_tracking']) }}
					</div>
				</div>
				<br>
				<div class="row">
				<div class="col-md-4"><hr style="border: 1px solid black;"></div>
				<div class="col-md-4"><h4 style="text-align:center;">PERSONAL DETAILS</h4></div>
				<div class="col-md-4"><hr style="border: 1px solid black;"></div>
				</div>
				<br>

				<div class="form-group row">
					<div class="col-md-3">
					<label for="lastname">Last Name<span class="required-field">*</span></label>
					@if (($application->application_status == 2 && $user->branch === 'MNL') || in_array($application->application_status, ['6', '7', '8']))
						{{ Form::text('lastname', $application->lastname, ['class' => 'form-control text-center text-uppercase', 'id' => 'editApplication_lastname', 'readonly' => 'readonly']) }}
					@else
						{{ Form::text('lastname', $application->lastname, ['class' => 'form-control text-center text-uppercase', 'id' => 'editApplication_lastname']) }}
					@endif
					</div>
					<div class="col-md-3">
					<label for="firstname">First Name<span class="required-field">*</span></label>
					@if (($application->application_status == 2 && $user->branch === 'MNL') || in_array($application->application_status, ['6', '7', '8']))
						{{Form::text('firstname', $application->firstname, ['class' => 'form-control text-center text-uppercase', 'id' => 'editApplication_firstname', 'readonly' => 'readonly']) }}
					@else
						{{Form::text('firstname', $application->firstname, ['class' => 'form-control text-center text-uppercase', 'id' => 'editApplication_firstname']) }}
					@endif
					</div>
					<div class="col-md-3">
						<label for="middlename">Middle Name</label>
					@if (($application->application_status == 2 && $user->branch === 'MNL') || in_array($application->application_status, ['6', '7', '8']))
						{{Form::text('middlename', $application->middlename, ['class' => 'form-control text-center text-uppercase', 'id' => 'middlename', 'readonly' => 'readonly']) }}
					@else
						{{Form::text('middlename', $application->middlename, ['class' => 'form-control text-center text-uppercase', 'id' => 'middlename']) }}
					@endif
					</div>
				</div>

				<div class="form-group row">
					<div class="col-md-3">
					<label for="birthdate">Birthday<span class="required-field">*</span></label>
						@if (($application->application_status == 2 && $user->branch === 'MNL') || in_array($application->application_status, ['6', '7', '8']))
							{{ Form::date('birthdate', $application->birthdate, ['class' => 'form-control text-center', 'id' => 'birthdate', 'max' => \Carbon\Carbon::now()->subDay()->format('Y-m-d'), 'title' => 'Please enter valid birthdate', 'required', 'readonly' => 'readonly']) }}
						@else
							{{ Form::date('birthdate', $application->birthdate, ['class' => 'form-control text-center', 'id' => 'birthdate', 'max' => \Carbon\Carbon::now()->subDay()->format('Y-m-d'), 'title' => 'Please enter valid birthdate', 'required']) }}
						@endif
							@error('birthdate')
								<span class="invalid-feedback" role="alert">
									<strong>{{ $message }}</strong>
								</span>
							@enderror
					</div>
					<div class="col-md-2">
					<label for="gender">Gender<span class="required-field">*</span></label>
						@if (($application->application_status == 2 && $user->branch === 'MNL') || in_array($application->application_status, ['6', '7', '8']))
							{{Form::select('gender', array('Female' => 'Female', 'Male' => 'Male'), $application->gender, ['class' => 'form-control', 'readonly' => 'readonly', 'style' => 'pointer-events: none; touch-action: none;'])}}
						@else
							{{Form::select('gender', array('Female' => 'Female', 'Male' => 'Male'), $application->gender, ['class' => 'form-control'])}}
						@endif
					</div>
				<div class="col-md-2">
				<label for="marital_status">Marital Status<span class="required-field">*</span></label>
				@if (($application->application_status == 2 && $user->branch === 'MNL') || in_array($application->application_status, ['6', '7', '8']))
					{{Form::select('marital_status', array('Single' => 'Single', 'Married' => 'Married', 'Divorced' => 'Divorced', 'Annulled' => 'Annulled', 'Widowed' => 'Widowed'), $application->marital_status, ['class' => 'form-control','readonly' => 'readonly', 'style' => 'pointer-events: none; touch-action: none;'])}}
				@else
					{{Form::select('marital_status', array('Single' => 'Single', 'Married' => 'Married', 'Divorced' => 'Divorced', 'Annulled' => 'Annulled', 'Widowed' => 'Widowed'), $application->marital_status, ['class' => 'form-control'])}}
				@endif
				</div>
					<div class="col-md-5">
					  <label for="email">Email:</label>
				@if (($application->application_status == 2 && $user->branch === 'MNL') || in_array($application->application_status, ['6', '7', '8']))
				  	{{Form::text('email', $application->email, ['class' => 'form-control text-center', 'readonly' => 'readonly'])}}
				@else
					{{Form::text('email', $application->email, ['class' => 'form-control text-center'])}}
				@endif
					</div>
				</div>

				<div class="form-group row">
					<div class="col-md-2">
					  <label for="telephone_no">Telephone No</label>
					@if (($application->application_status == 2 && $user->branch === 'MNL') || in_array($application->application_status, ['6', '7', '8']))
						{{Form::text('telephone_no', $application->telephone_no, ['class' => 'form-control text-center', 'id' => 'telno', 'maxlength' => '15', 'onkeypress' => 'return isNumericKey(event)', 'onpaste' => 'return validatePastedText(event)', 'oninput' => 'validateTelephoneNo(this.value)', 'readonly' => 'readonly']) }}
					@else
						{{Form::text('telephone_no', $application->telephone_no, ['class' => 'form-control text-center', 'id' => 'telno', 'maxlength' => '15', 'onkeypress' => 'return isNumericKey(event)', 'onpaste' => 'return validatePastedText(event)', 'oninput' => 'validateTelephoneNo(this.value)']) }}
					@endif
					</div>
					<div class="col-md-2">
					  <label for="mobile_no">Mobile No:</label>
					@if (($application->application_status == 2 && $user->branch === 'MNL') || in_array($application->application_status, ['6', '7', '8']))
						{{Form::text('mobile_no', $application->mobile_no, ['class' => 'form-control text-center', 'id' => 'mobno', 'maxlength' => '15', 'onkeypress' => 'return isNumericKey(event)', 'onpaste' => 'return validatePastedText(event)', 'oninput' => 'validateMobileNo(this.value)', 'readonly' => 'readonly'])}}
					@else
						{{Form::text('mobile_no', $application->mobile_no, ['class' => 'form-control text-center', 'id' => 'mobno', 'maxlength' => '15', 'onkeypress' => 'return isNumericKey(event)', 'onpaste' => 'return validatePastedText(event)', 'oninput' => 'validateMobileNo(this.value)'])}}
					@endif
					</div>
					<div class="col-md-8">
					<label for="address">Address<span class="required-field">*</span></label>
					@if (($application->application_status == 2 && $user->branch === 'MNL') || in_array($application->application_status, ['6', '7', '8']))
						{{Form::textarea('address', $application->address, ['class' => 'form-control text-center text-uppercase', 'rows' => '2', 'id' => 'address', 'maxlength' => '500','readonly' => 'readonly'])}}
					@else
						{{Form::textarea('address', $application->address, ['class' => 'form-control text-center text-uppercase', 'rows' => '2', 'id' => 'address', 'maxlength' => '500'])}}
					@endif
					</div>
				</div>

				<div class="form-group row">
					<div class="col-md-4">
					<label for="passport_no">Passport No<span class="required-field">*</span></label>
				@if (($application->application_status == 2 && $user->branch === 'MNL') || in_array($application->application_status, ['6', '7', '8']))
			  		{{ Form::text('passport_no', $application->passport_no, ['class' => 'form-control text-center', 'id' => 'passport_no', 'style' => 'text-transform: uppercase;', 'onkeyup' => 'validatePassportNo(this.value)', 'readonly' => 'readonly']) }}	
				@else
					{{ Form::text('passport_no', $application->passport_no, ['class' => 'form-control text-center', 'id' => 'passport_no', 'style' => 'text-transform: uppercase;', 'onkeyup' => 'validatePassportNo(this.value)']) }}	
				@endif

			  @error('passport_no')
				<span class="invalid-feedback" role="alert">
					<strong>{{ $message }}</strong>
				</span>
				@enderror
			</div>
			<div class="col-md-4">
				<label for="passport_expiry">Passport Expiry<span class="required-field">*</span></label>
				@if (($application->application_status == 2 && $user->branch === 'MNL') || in_array($application->application_status, ['3', '4', '5', '6', '7', '8', '9', '10', '11', '12']))
					{{Form::date('passport_expiry', $application->passport_expiry, ['class' => 'form-control text-center', 'min' => now()->addDay()->format('Y-m-d'), 'readonly' => 'readonly']) }}
				@else
					{{Form::date('passport_expiry', $application->passport_expiry, ['class' => 'form-control text-center', 'id' => 'passport_expiry']) }}
				@endif
			</div>

					<div class="col-md-4">
					<label for="departure_date">Expected Departure Date<span class="required-field">*</span></label>
					@if (($application->application_status == 2 && $user->branch === 'MNL') || in_array($application->application_status, ['3', '4', '5', '6', '7', '8', '9', '10', '11', '12']))
						{{Form::date('departure_date', $application->departure_date, ['class' => 'form-control text-center', 'min' => now()->addDay()->format('Y-m-d'),'readonly' => 'readonly']) }}
					@else
						{{Form::date('departure_date', $application->departure_date, ['class' => 'form-control text-center']) }}
					@endif
					</div>
				</div>

				<div class="form-group row">
					<div class="col-md-12">
					  <label for="remarks">Remarks:</label>
					  {{Form::textarea('remarks', $application->remarks, ['class' => 'form-control text-center', 'rows' => '3'])}}
					</div>
				</div>

				<br>
				<div class="row">
				<div class="col-md-4"><hr style="border: 1px solid black;"></div>
				<div class="col-md-4"><h4 style="text-align:center;">VISA DETAILS</h4></div>
				<div class="col-md-4"><hr style="border: 1px solid black;"></div>
				</div>
				<br>

				<div class="form-group row">
						<div class="col-md-4">
						<label for="visa_type">Visa Type:<span class="required-field">*</span></label>
						<select class="form-control" name="visa_type" id="visa_type">
								<option value="">--- Select a visa type ---</option>
									@foreach($visatypes as $visaType)
										<option value="{{ $visaType->name }}"
											data-visa-fee="{{ $visaType->visa_fee }}"
											data-handling-fee="{{ $visaType->handling_fee }}"
											data-required-docs="{{ json_encode($visaType->documents) }}"
											{{ $visaType->name === $application->visa_type ? 'selected' : '' }}>            	
											{{ $visaType->name }}
										</option>
									@endforeach
								</select>
						</div>


				<div class="col-md-3">
					<label for="visa_price">Visa Price:</label>
						{{ Form::text('visa_price', $application->visa_price, ['data-visa-fee' => $visaType->visa_fee, 'class' => 'form-control text-center', 'id' => 'visa_price', 'readonly' => 'readonly']) }}
					</div>

				<div class="col-md-3">
					<label for="handling_price">Handling Price:</label>
					  {{ Form::text('handling_price', $application->handling_price, ['data-handling-fee' => $visaType->handling_fee, 'class' => 'form-control text-center', 'id' => 'handling_price', 'readonly' => 'readonly']) }}
				</div>
				
				<div class="col-md-2">
						<label for="promo_code">Promo Code:</label>
						{{ Form::text('promo_code', $application->promo_code, ['class' => 'form-control text-center text-uppercase', 'id' => 'promo_code', 'placeholder' => '(optional)', 'readonly' => in_array($application->application_status, ['6', '7', '8']) ? 'readonly' : '']) }}
					</div>
					<div class="col-md-5">
						<a class="btn btn-success rounded text-white" id="promo_code_btn">Use Promo Code</a>
					</div>

					<div class="col-md-3">
						{{Form::hidden('discount_amount', $application->discount_amount)}}
						{{Form::hidden('discount', 0)}}
					</div>
				</div>

				<div class="form-group row">
		    <div class="col-md-12">
			{{ Form::hidden('documents_submitted', $application->documents_submitted, ['id' => 'documents_submitted']) }}
				<label for="documents_submitted">Documents Required:<span class="required-field">*</span></label>
				<div class="table-responsive">
					<table class="table table-sm table-bordered">
                                      <thead class="thead-light">
                                          <th style="width:33.33%;" class="bg-success text-white">FILIPINO DOCUMENTS</th>
                                          <th style="width:33.33%;" class="bg-info text-white">JAPANESE DOCUMENTS</th>
                                          <th style="width:33.33%;" class="bg-dark text-white">FOREIGNER DOCUMENTS</th>
                                      </thead>
                                      	<tbody>
                                          <tr>
                                            <td class="bg-success text-left">
											<div class="scrollable-div">
                                                <ul class="list-group" id="filipino_documents">	
												@foreach ($docs_filipino->sortBy('name') as $value)
													<li class='list-group-item'><input type='checkbox' name='submitted_documents' value='{{ $value->id }}' id='{{ $value->id }}'/> {{ $value->name }} </li>                                                    @endforeach
                                                </ul>
												</div>
                                            </td>
                                            <td class="bg-info text-left">
												<div class="scrollable-div">
                                                <ul class="list-group" id="japanese_documents">
												@foreach ($docs_japanese->sortBy('name') as $value)
													<li class='list-group-item'><input type='checkbox' name='submitted_documents' value='{{ $value->id }}' id='{{ $value->id }}'/> {{ $value->name }} </li>                                                    @endforeach
                                                </ul>
												</div>
                                            </td>
                                            <td class="bg-dark text-left">
												<div class="scrollable-div">
                                                <ul class="list-group" id="foreign_documents">
                                                @foreach ($docs_foreign->sortBy('name') as $value)
													<li class='list-group-item'><input type='checkbox' name='submitted_documents' value='{{ $value->id }}' id='{{ $value->id }}'/> {{ $value->name }} </li>                                                    @endforeach
                                                </ul>
												</div>
                                            </td>
                                          </tr>
                                      	</tbody>
                    </table>
				</div>
				{{Form::hidden('payment_status', $application->payment_status)}}
				<div class="row">
					<div class="col-md-2 offset-md-4">
						<button type="submit" class="btn btn-primary">Update</button>
					</div>
        </form>
					<div class="col-md-2">
						<a href="{{ url()->previous() }}" class="btn btn-danger">Back</a>
					</div>
				</div>

			</div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">


$(document).ready(function () {
        // Function to update the value of documents_submitted hidden field
        function updateDocumentsSubmitted() {
            const selectedDocuments = $('input[name="submitted_documents"]:checked')
                .map(function () {
                    return this.value;
                })
                .get()
                .join(',');
            $('#documents_submitted').val(selectedDocuments);
        }

        // Call the function initially to populate the hidden field on page load
        updateDocumentsSubmitted();

        // Bind an event handler for when any checkbox is clicked
        $('input[name="submitted_documents"]').on('change', function () {
            updateDocumentsSubmitted();
        });

		$('#application_status').on('change', function () {
        const selectedStatus = $(this).val();
        const visaResultField = $('#visa_result');

        if (selectedStatus === 'Received from Embassy') {
            visaResultField.removeAttr('readonly');
        } else {
            visaResultField.attr('readonly', 'readonly');
        }
    });

    });

$(document).ready(function() {
    var applicationStatus = "{{ $application->application_status }}";
    var userBranch = "{{ $user->branch }}";

    if ((applicationStatus === '2' && userBranch === 'MNL') || ['6', '7', '8'].includes(applicationStatus)) {
      $("#visa_type").prop("readOnly", true);
	  $("#visa_type").css({"pointer-events": "none", "touch-action": "none", "background-color": "#e8e8e8"});
    }
  });

function validateInput(event) {
  const input = event.target;
  const newValue = input.value.replace(/\D/g, ''); // Remove all non-digit characters
  input.value = newValue;
}
function checkAddressLength() {
    var address = document.getElementById("address");
    var addressLength = document.getElementById("addressLength");
    
    if (address.value.length > 200) {
        addressLength.style.color = "red";
    } else {
        addressLength.style.color = "black";
    }
    
    addressLength.textContent = address.value.length + "/200";
}

function isNumericKey(event) {
  const charCode = (event.which) ? event.which : event.keyCode;
  if ((charCode < 48 || charCode > 57) && charCode !== 45 && charCode !== 43) {
    return false;
  }
  return true;
}

function validatePastedText(event) {
  const pastedText = event.clipboardData.getData('text/plain');
  if (!/^\d+(-\d*){0,14}$/.test(pastedText) && !/^\+\d+(-\d*){0,14}$/.test(pastedText)) {
    event.preventDefault();
    return false;
  }
  return true;
}

function validateTelephoneNo(value) {
  const telnoInput = document.getElementById('telno');
  const isValid = /^\d+(-\d*){0,14}$/.test(value) || /^\+\d+(-\d*){0,14}$/.test(value);
  if (!isValid) {
    telnoInput.setCustomValidity('Invalid telephone number');
  } else {
    telnoInput.setCustomValidity('');
  }
}

function validateMobileNo(value) {
  const mobnoInput = document.getElementById('mobno');
  const isValid = /^\d+(-\d*){0,14}$/.test(value) || /^\+\d+(-\d*){0,14}$/.test(value);
  if (!isValid) {
    mobnoInput.setCustomValidity('Invalid mobile number');
  } else {
    mobnoInput.setCustomValidity('');
  }
}


function validatePassportNo(value) {
  var regex = /^[A-Za-z0-9\- ]{0,15}$/;
  var containsLettersOrNumbers = /[A-Za-z0-9]/.test(value);
  var isValid = regex.test(value) && containsLettersOrNumbers;
  var passportNoInput = document.getElementById('passport_no');

  if (!isValid) {
    passportNoInput.setCustomValidity('Please enter a valid passport number');
  } else {
    passportNoInput.setCustomValidity('');
  }
}

	$(document).ready(function(){

		$(document).ready(function() {
    restore_checkboxes();

    // Add event listener to the form submit action
    $('form').on('submit', function() {
        updateHiddenInputValue();
    });
});

function restore_checkboxes() {
    var submitted_documents = "{{$application->documents_submitted}}".split(",");
    $('input[name="submitted_documents"]').each(function(){
        if(submitted_documents.includes($(this).val()))
        {
            $(this).attr("checked", true);
        }
    });
}

function updateHiddenInputValue() {
    var selectedDocuments = [];
    $('input[name="submitted_documents"]:checked').each(function() {
        selectedDocuments.push($(this).val());
    });
    $('#documents_submitted').val(selectedDocuments.join(","));
}

		
	$(document).on('keypress', '#editApplication_lastname, #editApplication_firstname, #middlename', function(event) {
    var inputValue = this.value;
    var keyPressed = String.fromCharCode(event.charCode || event.which);
    
    if (inputValue.length === 0 && !isLetter(keyPressed)) {
        event.preventDefault();
        return false;
    }
    
    var regex = new RegExp("^[A-Za-z0-9.'\\s-]+$");
    if (!regex.test(keyPressed) && event.which !== 0 && !event.ctrlKey && !event.metaKey) {
        event.preventDefault();
        return false;
    }
});

function isLetter(char) {
    return /^[A-Za-z]+$/.test(char);
}


const customerTypeSelect = document.getElementById('customer_type');
	const customerCompanySelect = document.getElementById('customer_company');
	const partnerCompanies = {!! json_encode($customer_company) !!};

  // Function to update the Customer Company dropdown options
	function updateCustomerCompanyOptions() {
    const selectedType = customerTypeSelect.value;
    const customerNameSelect = document.getElementById('customer_company');
    customerNameSelect.innerHTML = ''; // Clear previous options

    if (selectedType === 'Walk-in' || selectedType === '') {
      customerNameSelect.disabled = true;
    } else {
      customerNameSelect.disabled = false;

      // Populate options based on selected type
      const options = partnerCompanies.filter(company => company.type === selectedType);
      options.forEach(option => {
        const optionElement = document.createElement('option');
        optionElement.value = option.name;
        optionElement.textContent = option.name;
        customerNameSelect.appendChild(optionElement);
      });
    }
  }

  // Event listener for Customer Type selection change
  customerTypeSelect.addEventListener('change', updateCustomerCompanyOptions);

  // Initial update of Customer Company options
  updateCustomerCompanyOptions();

  document.getElementById('application_status_select').addEventListener('change', function(event) {
    console.log('Change event triggered');
        const releasedToField = document.getElementById('released_to');
const visaResultField = document.getElementById('visa_result');
        const selectedStatus = event.target.value.trim();
        console.log('Selected status:', selectedStatus);


  if (selectedStatus === '8') {
    console.log('Removing readonly attribute');
    releasedToField.removeAttribute('readonly');
  } else {
    console.log('Setting readonly attribute');
    releasedToField.setAttribute('readonly', 'readonly');
 
  }
});

  document.getElementById('application_status_select').addEventListener('change', function(event) {
    console.log('Change event triggered');
const visaResultField = document.getElementById('visa_result');
        const selectedStatus = event.target.value.trim();
        console.log('Selected status:', selectedStatus);


  if (selectedStatus === '12') {
    console.log('Removing readonly attribute');
visaResultField.removeAttribute('readonly');
  } else {
    console.log('Setting readonly attribute');
    visaResultField.setAttribute('readonly', 'readonly');
  }
});

  document.getElementById('application_status_select').addEventListener('change', function(event) {
    console.log('Change event triggered');
const verificationField = document.getElementById('verification_no');
        const selectedStatus = event.target.value.trim();
        console.log('Selected status:', selectedStatus);


  if (selectedStatus === '7') {
    console.log('Removing readonly attribute');
verificationField.removeAttribute('readonly');
  } else {
    console.log('Setting readonly attribute');
    verificationField.setAttribute('readonly', 'readonly');
  }
});

let globalVisaFee;
let globalHandlingFee;
let customerType;

  document.getElementById('visa_type').addEventListener('change', function(event) {
  const selectedVisaType = event.target.value;
  const selectedOption = event.target.selectedOptions[0];
  const visaFee = selectedOption.dataset.visaFee;
  const handlingFee = selectedOption.dataset.handlingFee;
  const customerType = document.getElementById('customer_type').value;

  const visaTypeField = document.getElementById('visa_type');
  const visaPriceField = document.getElementById('visa_price');
  const visaHandlingFeeField = document.getElementById('handling_price');
  const documentsSubmittedField = document.getElementById('documents_submitted');
  const filipinoDocumentsList = document.getElementById('filipino_documents');
  const japaneseDocumentsList = document.getElementById('japanese_documents');
  const foreignDocumentsList = document.getElementById('foreign_documents');

  globalVisaFee = selectedOption.dataset.visaFee;
  globalHandlingFee = selectedOption.dataset.handlingFee;

  if (selectedVisaType) {
    visaTypeField.value = selectedVisaType;
    visaPriceField.value = visaFee;
    visaHandlingFeeField.value = handlingFee;

        if (selectedVisaType) {
    if ((customerType === 'PIATA' || customerType === 'POEA' || customerType === 'PTAA') && selectedVisaType === 'FOREIGN PASSPORT') {
      visaPriceField.value = 1200.00;
      visaHandlingFeeField.value = 500.00;
    } else {
      visaPriceField.value = globalVisaFee;
      if ((customerType === 'PIATA' || customerType === 'POEA' || customerType === 'PTAA') && selectedVisaType !== 'FOREIGN PASSPORT') {
        visaHandlingFeeField.value = 500.00;
      } else {
        visaHandlingFeeField.value = globalHandlingFee;
      }
    }
  }


        if(customerType === 'Expo') {
                visaPriceField.removeAttribute('readonly');
                visaHandlingFeeField.removeAttribute('readonly');
        } else {
            visaPriceField.setAttribute('readonly', 'readonly');
                visaHandlingFeeField.setAttribute('readonly', 'readonly');
        }

    // Clear existing document lists
    filipinoDocumentsList.innerHTML = '';
    japaneseDocumentsList.innerHTML = '';
    foreignDocumentsList.innerHTML = '';

    // Generate new document lists based on requiredDocs
    const requiredDocs = JSON.parse(selectedOption.dataset.requiredDocs);
    if (requiredDocs && requiredDocs.length > 0) {
      requiredDocs.forEach(doc => {
        const li = document.createElement('li');
        li.className = 'list-group-item';
                li.innerHTML = `<input type="checkbox" name="submitted_documents" value="${doc.id}" id="${doc.id}"/> ${doc.name}`;

        if (doc.type === 'FILIPINO') {
          filipinoDocumentsList.appendChild(li);
        } else if (doc.type === 'JAPANESE') {
          japaneseDocumentsList.appendChild(li);
        } else if (doc.type === 'FOREIGN') {
          foreignDocumentsList.appendChild(li);
        }
      });
    }
  } else {
    visaTypeField.value = '';
    visaPriceField.value = '';
    visaHandlingFeeField.value = '';
    documentsSubmittedField.value = '';
    filipinoDocumentsList.innerHTML = '';
    japaneseDocumentsList.innerHTML = '';
    foreignDocumentsList.innerHTML = '';
  }
});

document.getElementById('customer_type').addEventListener('change', function(event) {
  const selectedCustomerType = event.target.value;
  const selectedVisaType = document.getElementById('visa_type').value;
  const selectedOption = event.target.selectedOptions[0];
  const visaFee = selectedOption.dataset.visaFee;
  const handlingFee = selectedOption.dataset.handlingFee;
  const customerType = document.getElementById('customer_type').value;

  const visaTypeField = document.getElementById('visa_type');
  const visaPriceField = document.getElementById('visa_price');
  const visaHandlingFeeField = document.getElementById('handling_price');


  if (selectedVisaType) {
    if ((customerType === 'PIATA' || customerType === 'POEA' || customerType === 'PTAA') && selectedVisaType === 'FOREIGN PASSPORT') {
      visaPriceField.value = 1200.00;
      visaHandlingFeeField.value = 500.00;
    } else {
      visaPriceField.value = globalVisaFee;
      if ((customerType === 'PIATA' || customerType === 'POEA' || customerType === 'PTAA') && selectedVisaType !== 'FOREIGN PASSPORT') {
        visaHandlingFeeField.value = 500.00;
      } else {
        visaHandlingFeeField.value = globalHandlingFee;
      }
    }
  }

  if(customerType === 'Expo') {
                visaPriceField.removeAttribute('readonly');
                visaHandlingFeeField.removeAttribute('readonly');
        } else {
            visaPriceField.setAttribute('readonly', 'readonly');
                visaHandlingFeeField.setAttribute('readonly', 'readonly');
        }
});



		var visaTypeArray = {!! $visatypes->toJson() !!};

		// populate_partner_companies("{{$application->customer_type}}");
		// get_promo_code();

        var visaType = $('#visa_type').find('option:selected').val();

		$(document).ready(function () {
        // Restore the checkboxes based on the stored value in the hidden input field
        var submitted_documents = "{{$application->documents_submitted}}".split(",");
        $('input[name="submitted_documents"]').each(function () {
            if (submitted_documents.includes($(this).val())) {
                $(this).prop("checked", true);
            }
        });

        // Update the hidden input field when checkboxes are changed
        $(document).on('change', 'input[name="submitted_documents"]', function () {
            var checkboxes = $('input[name="submitted_documents"]:checked');
            var output = [];

            checkboxes.each(function () {
                output.push($(this).val());
            });

            $('input[name="documents_submitted"]').val(output.join(","));
        });
    });
	
		$(document).on('change', '#visa_type', function(){
			var visaType = $(this).find('option:selected').val();
			// on_change_visa_type(visaType,true);
		});


		// $(document).on('click', '#promo_code_btn', function(){
		// 	get_promo_code();
		// });

	});





</script>
@endsection


<!-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> -->
