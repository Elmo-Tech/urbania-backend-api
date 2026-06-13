<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Attachment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\View;

class TicketCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $content;
    public $attachments;

    public function __construct($subject, $content, $attachments)
    {
        $this->subject = $subject;
        $this->content = $content;
        $this->attachments = $attachments;
    }

   public function envelope()
    {
        return new Envelope(
            from: new Address('noreply@example.com', 'Your App'),
            subject: $this->subject, // Dynamic subject
        );
    }

    // Attach files from storage using Attachment::fromStorage()
    public function attachments()
    {


        return [];
    }

      public function content()
    {
        // Return the Blade view for the email content
        return new Content(
            html: View::make('emails.sendClientEmail', ['content' => $this->content, 'subject' => $this->subject])->render()
        );
    }
}
