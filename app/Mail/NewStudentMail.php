<?php

namespace App\Mail;

use App\Models\BasicInformation;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewStudentMail extends Mailable
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
        $studentLocalDetails = User::where('name', $this->studentId)->first();
        $studentId = $this->studentId;
        
        return $this
            ->from('registration@lmmu.ac.zm')
            ->subject('COURSE REGISTRATION ON SIS REPORTS')
            ->view('emails.newStudentMail', compact('studentDetails','studentLocalDetails','studentId'));
    }
}
