<?php

namespace App\Mail;

use App\Models\BasicInformation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DefSupDocket extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $pdfPath;
    public $studentId;

    public function __construct($pdfPath, $studentId)
    {
        $this->pdfPath = $pdfPath;
        $this->studentId = $studentId;
    }
    public function build()
    {
        $studentDetails = BasicInformation::find($this->studentId);
        
        return $this
            ->from('registration@lmmu.ac.zm')
            ->subject('Exam Docket For Deffered and Supplementary Exams')
            ->view('emails.defandsup', compact('studentDetails'))
            ->attach($this->pdfPath, [
                'as' => 'exam_docket.pdf',
                'mime' => 'application/pdf'
            ]);
    }

}
