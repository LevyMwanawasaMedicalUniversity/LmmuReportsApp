<?php

namespace App\Mail;

use App\Models\BasicInformation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */

    public $studentId;

    public function __construct($studentId)
    {
        $this->studentId = $studentId;
    }

    public function build()
    {
        $studentDetails = BasicInformation::find($this->studentId);
        
        return $this
            ->from('registration@lmmu.ac.zm')
            ->subject('2024 EXAMINATION DOCKETS')
            ->view('emails.notification', compact('studentDetails'));
    }
}
