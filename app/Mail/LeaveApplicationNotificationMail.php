<?php

namespace App\Mail;

use App\Models\LeaveApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LeaveApplicationNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $leaveApplication;

    public function __construct(LeaveApplication $leaveApplication)
    {
        $this->leaveApplication = $leaveApplication;
    }

    public function build()
    {
        return $this->subject('New Leave Application Submitted')
                    ->view('emails.leave_notification');
    }
}
