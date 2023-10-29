<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\User;
use App\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class SubmissionListGenerated extends Mailable
{
    use Queueable, SerializesModels;

    public $pdf_files;
    public $current_date;
    public $branch;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($pdf_array, $date, $branch)
    {
        $this->pdf_files = $pdf_array;
        $this->current_date = $date;
        $this->branch = $branch;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
	 
    public function build(Request $request)
    {
        $branch_code = $this->branch->code;
        $batch_no_string = Carbon::now()->format('Ymd') . $branch_code;
        $branchEmail = $this->branch->email;
    
        $mail = $this->from($request->user()->email, $request->user()->name, 'Discovery Tours')
            ->subject('Submission List from ' . $this->branch->code . ' - ' . $this->current_date)
            ->view('emails.submission_list')
            ->with('branch', $this->branch)
            ->with('batch_no_string', $batch_no_string);
			
			$ccEmails = ['junichi@discoverytour.ph', 'connie@discoverytour.ph', 'carmen@discoverytour.ph'];
            $mail->cc($ccEmails);
    
		foreach($this->pdf_files as $key => $value)
		{
			$mail->attachData($value, $key . '.pdf', ['mime' => 'application/pdf']);
		}
    
        return $mail;
    }
}

