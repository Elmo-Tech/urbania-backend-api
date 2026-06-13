<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Attachment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage; // Add this at the top of your file to ensure Storage is available


// class ClientTicketEmail extends Mailable
// {
//     use Queueable, SerializesModels;
    
//     public $subject;
//     public $content;
//     //public $attachments;

//     public function __construct($subject, $content, $attachments = [])
//     {
//         $this->subject = $subject;
//         $this->content = $content;
//         $this->attachment = is_array($attachments) ? $attachments : [$attachments]; // Make sure it's an array
//     }


//     /**
//      * Get the message envelope.
//      */
//     public function envelope(): Envelope
//     {
//         return new Envelope(
//             subject: 'Client Ticket Email',
//         );
//     }

//     /**
//      * Get the message content definition.
//      */
//     public function content(): Content
//     {
//         return new Content(
//             view: 'emails.sendClientEmail',
//         );
        
//         /*return new Content(
//             html: View::make('emails.sendClientEmail', ['content' => $this->content, 'subject' => $this->subject])->render()
//         );*/
        
        
//     }

//     /**
//      * Get the attachments for the message.
//      *
//      * @return array<int, \Illuminate\Mail\Mailables\Attachment>
//      */
//  public function attachments(): array
// {

//     // Loop through all the attachment files passed to the mailable
//      $attachments = [];

//     foreach ($this->attachment as $filePath) {
//         $attachments[] = Attachment::fromStorage($filePath);
//     }

//     return $attachments;

// }

    
    
// }

class ClientTicketEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $header;
    public $body;
    public $files;

    public function __construct($header, $body, $files = [])
    {
        $this->header = $header;
        $this->body = $body;
        $this->files = $files;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // Start building the email with subject and view
        $this->subject($this->header)
                      ->view('emails.sendClientEmail', [
                          'header' => $this->header,
                          'body' => $this->body
                      ]);

        // Attach files if provided
            // You can use `fromStorage` if you're using files from storage
             foreach ($this->files as $file){

                $this->attach($file);

            }

        return $this;
    }
}

