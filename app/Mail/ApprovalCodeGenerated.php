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

class ApprovalCodeGenerated extends Mailable
{
    use Queueable, SerializesModels;

    public $current_date;
    public $otp_code;

    /**
     * Create a new message instance.
     *
     * @param string $otp_code
     * @param string $current_date
     */
    public function __construct($otp_code, $current_date)
    {
        $this->otp_code = $otp_code;
        $this->current_date = $current_date;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(Request $request)
    {
        $mail = $this->from($request->user()->email, 'Discovery Tours')
            ->subject('OTP Code: Account Verification')
            ->view('emails.approval');

        return $mail;
    }
}
