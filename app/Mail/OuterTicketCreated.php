<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Attachment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\View;

class OuterTicketCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $ticket;


    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;

    }


        public function build()
        {
            return $this->subject('Your Reservation Details')
                        ->view('emails.createdOuterTicket')
                        ->with([
                            'ticket' => $this->ticket
                        ]);
        }


}
