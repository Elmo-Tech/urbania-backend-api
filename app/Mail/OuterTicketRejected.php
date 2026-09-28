<?php

namespace App\Mail;

use App\Models\ClientOuterTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OuterTicketRejected extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ClientOuterTicket $ticket)
    {
    }

    public function build()
    {
        return $this->subject('Ticket Rejected')->view('emails.rejectedOuterTicket');
    }
}
