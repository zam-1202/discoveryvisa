<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SubmissionListGenerated extends Mailable
{
    use Queueable, SerializesModels;
	
	public $pdf_files;
	
	public $current_date;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($pdf_array, $date)
    {
        $this->pdf_files = $pdf_array;
		$this->current_date = $date;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mail = $this->from('nortesam6@gmail.com', 'Discovery Visa System')
        ->subject('Submission Lists for ' . $this->current_date)
        ->view('emails.submission_list');
		
		foreach($this->pdf_files as $key => $value)
		{
			$mail->attachData($value, $key . '.pdf', ['mime' => 'application/pdf']);
		}
		
		return $mail;
    }

}
