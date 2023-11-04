<?php

namespace App\Mail;

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

     public $pdfPath;

     public function __construct($pdfPath)
     {
         $this->pdfPath = $pdfPath;
     }
 
     public function build()
     {
         return $this
             ->from('ictlmmu@gmail.com')
             ->subject('Exam Docket For Unregistered Student')
             ->view('emails.email-view') // Replace with your email view
             ->attach($this->pdfPath, [
                 'as' => 'exam_docket.pdf', // Specify the name of the attachment
                 'mime' => 'application/pdf' // Specify the MIME type
             ]);
     }

    // public function __construct()
    // {
    //     //
    // }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Exam Docket For Unregistered Student',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.test',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
