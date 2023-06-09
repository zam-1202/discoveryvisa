<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\SubmissionListGenerated;

// use PHPMailer\PHPMailer\PHPMailer;
// use PHPMailer\PHPMailer\Exception;

class MailController extends Controller
{
    // public function sendMail(){ 
    //     $pdfArray = []; // Provide the array of PDF files
    //     $currentDate = date('Y-m-d'); // Provide the current date

    //     Mail::to('nortesam6@gmail.com')->send(new SubmissionListGenerated($pdfArray, $currentDate));
    //     return view('welcome');
    // }
}
