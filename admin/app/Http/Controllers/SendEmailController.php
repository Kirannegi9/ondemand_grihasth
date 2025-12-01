<?php

namespace App\Http\Controllers;

use App\Mail\SetEmailData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Redirect;

class SendEmailController extends Controller
{
    public function __construct()
    {
    }


    function sendMail(Request $request)
    {
        try {
            $data = $request->all();

            $subject = $data['subject'];
            $message = base64_decode($data['message']);
            $recipients = $data['recipients'];

            Mail::to($recipients)->send(new SetEmailData($subject, $message));

            return response()->json(['status' => 'success', 'message' => 'Email sent successfully!']);
        } catch (\Exception $e) {
            \Log::error('Email sending failed: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to send email: ' . $e->getMessage()], 500);
        }
    }
}

?>