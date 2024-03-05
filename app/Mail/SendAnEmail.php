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

class SendAnEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     * 
     * 
     */

    //  public $pdfPath;
     public $studentId;

     public function __construct($studentId)
    {
        
        $this->studentId = $studentId;
    }
    public function build()
    {
        $studentDetails = BasicInformation::find($this->studentId);
        $studentLocalDetails = User::where('name', $this->studentId)->first();
        
        return $this
            ->from('registrar@lmmu.ac.zm')
            ->subject('Password Reset Notification')
            ->view('emails.test', compact('studentDetails','studentLocalDetails'));
        
    }

    
}
